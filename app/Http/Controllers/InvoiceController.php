<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Livewire\Livewire;
use App\Models\Company;
use App\Models\Invoice;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
  public function index(Request $request)
  {
    // Obtener los parámetros de filtro
    $dateFilter = $request->input('date_filter', 'all');
    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');
    $clientId = $request->input('client_id');
    $status = $request->input('status');

    // Construir la consulta base
    $query = Invoice::with(['client', 'company'])->orderBy('created_at', 'desc');

    // Aplicar filtros de fecha
    if ($dateFilter === 'this_week') {
      $query->dueThisWeek();
    } elseif ($dateFilter === 'this_month') {
      $query->whereMonth('issued_at', now()->month)
        ->whereYear('issued_at', now()->year);
    } elseif ($dateFilter === 'last_month') {
      $query->whereMonth('issued_at', now()->subMonth()->month)
        ->whereYear('issued_at', now()->subMonth()->year);
    } elseif ($dateFilter === 'custom' && $dateFrom && $dateTo) {
      $query->dateBetween($dateFrom, $dateTo);
    }

    // Filtrar por cliente
    if ($clientId) {
      $query->client($clientId);
    }

    // Filtrar por estado
    if ($status) {
      $query->status($status);
    }

    // Obtener las facturas paginadas
    $invoices = $query->paginate(10)->withQueryString();

    // Obtener lista de clientes para el filtro
    $clients = Client::orderBy('name')->get();

    return view('invoices.index', compact('invoices', 'clients', 'dateFilter', 'dateFrom', 'dateTo', 'clientId', 'status'));
  }


  public function show(Invoice $invoice)
  {
    // Cargar las relaciones necesarias
    $invoice->load(['client', 'company', 'products']);

    return view('invoices.show', compact('invoice'));
  }


  public function create()
  {
    // Método 2: Renderizar directamente el componente
    return view('invoices.create');
  }

  // En tu controlador, antes de generar el PDF, añade estas funciones:
  private function getBase64Image($path)
  {
    if (Storage::exists($path)) {
      $imageData = Storage::get($path);
      $type = pathinfo($path, PATHINFO_EXTENSION);
      return 'data:image/' . $type . ';base64,' . base64_encode($imageData);
    }
    return null;
  }


  public function generatePDF(Invoice $invoice)
  {
    // Preparar las imágenes
    $logoImage = null;
    if ($invoice->company->logo_path) {
      $logoImage = $this->getBase64Image($invoice->company->logo_path);
    }

    $statusImage = $this->getBase64Image(
      'public/company-logos/' .
        ($invoice->status === 'paid' ? 'Pagado.png' : 'Pendiente.png')
    );

    // Pasar las imágenes a la vista
    $data = [
      'invoice' => $invoice,
      'logoImage' => $logoImage,
      'statusImage' => $statusImage
    ];

    $pdf = app('dompdf.wrapper');
    $pdf->loadView('invoices.pdf', $data);
    return $pdf->stream();
  }
}
