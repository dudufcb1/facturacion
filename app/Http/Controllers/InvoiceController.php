<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $dateFilter = $request->input('date_filter', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $clientId = $request->input('client_id');
        $status = $request->input('status');

        $query = Invoice::with(['client', 'company'])->orderBy('created_at', 'desc');

        $this->applyDateFilters($query, $dateFilter, $dateFrom, $dateTo);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status) {
            if ($status === 'pending') {
                $query->where('status', 'debt');  // Cambia "pending" por "debt"
            } else {
                $query->where('status', $status);
            }
        }

        $invoices = $query->paginate(10)->withQueryString();
        $filteredInvoices = $query->get();

        $totals = $filteredInvoices->groupBy('currency')->map(function ($group) {
            return [
                'pending' => $group->where('status', 'debt')->sum('total'),
                'paid' => $group->where('status', 'paid')->sum('total'),
            ];
        });

        $clients = Client::orderBy('name')->get();

        return view('invoices.index',
            compact('invoices', 'clients', 'dateFilter', 'dateFrom', 'dateTo', 'clientId', 'status', 'totals'));
    }

// Controller Method
    private function applyDateFilters($query, $dateFilter, $dateFrom = null, $dateTo = null)
    {
        if ($dateFilter === 'custom' && $dateFrom && $dateTo) {
            $query->whereBetween('issued_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay()
            ]);
        } else {
            if ($dateFilter === 'all' || $dateFilter === null) {
                return;
            }

            if ($dateFilter === 'this_week') {
                $query->whereBetween('issued_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'this_month') {
                $query->whereMonth('issued_at', now()->month)
                    ->whereYear('issued_at', now()->year);
            } elseif ($dateFilter === 'last_month') {
                $query->whereMonth('issued_at', now()->subMonth()->month)
                    ->whereYear('issued_at', now()->subMonth()->year);
            }
        }
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

    public function generatePDF(Invoice $invoice)
    {
        // Preparar las imágenes
        $logoImage = null;
        if ($invoice->company->logo_path) {
            $logoImage = $this->getBase64Image($invoice->company->logo_path);
        }

        $statusImage = $this->getBase64Image(
            'public/company-logos/'.
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

    private function getBase64Image($path)
    {
        if (Storage::exists($path)) {
            $imageData = Storage::get($path);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/'.$type.';base64,'.base64_encode($imageData);
        }
        return null;
    }

    /**
     * Anular una factura
     */
    public function void(Request $request, Invoice $invoice)
    {
        try {
            // Validar la razón de anulación
            $request->validate([
                'void_reason' => 'required|string|min:10|max:500'
            ], [
                'void_reason.required' => 'La razón de anulación es obligatoria.',
                'void_reason.min' => 'La razón debe tener al menos 10 caracteres.',
                'void_reason.max' => 'La razón no puede exceder 500 caracteres.'
            ]);

            // Verificar que la factura puede ser anulada
            if (!$invoice->canBeVoided()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Esta factura no puede ser anulada. Solo se pueden anular facturas emitidas o pendientes.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Esta factura no puede ser anulada. Solo se pueden anular facturas emitidas o pendientes.');
            }

            // Verificar si tiene pagos asociados
            $hasPayments = $invoice->payments()->where('status', 'completed')->exists();

            // Anular la factura
            $invoice->voidInvoice($request->void_reason, Auth::id());

            // Enviar notificaciones a administradores
            $this->notifyAdministrators($invoice, Auth::user(), $request->void_reason);

            $message = $hasPayments
                ? 'Factura anulada exitosamente. Los pagos asociados también fueron anulados.'
                : 'Factura anulada exitosamente.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'had_payments' => $hasPayments
                ]);
            }

            return redirect()->route('invoices.show', $invoice)->with('success', $message);

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Error al anular la factura: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al anular la factura: ' . $e->getMessage());
        }
    }

    /**
     * Notificar a administradores sobre la anulación
     */
    private function notifyAdministrators(Invoice $invoice, $voidedBy, string $reason)
    {
        // Obtener todos los administradores
        $administrators = User::whereIn('role', ['administrador', 'propietario'])
            ->where('id', '!=', $voidedBy->id) // Excluir al usuario que anuló
            ->get();

        // Enviar notificación a cada administrador
        foreach ($administrators as $admin) {
            $admin->notify(new \App\Notifications\VoidInvoiceNotification($invoice, $voidedBy, $reason));
        }
    }
}
