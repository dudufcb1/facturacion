<?php

namespace App\Http\Controllers;

use Livewire\Livewire;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function index()
  {
    $invoices = Invoice::with(['client', 'company'])
      ->orderBy('created_at', 'desc')
      ->paginate(10);

    return view('invoices.index', compact('invoices'));
  }

  public function create()
  {
    // Método 2: Renderizar directamente el componente
    return view('invoices.create');
  }
}
