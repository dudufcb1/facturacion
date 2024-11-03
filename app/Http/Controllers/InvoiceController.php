<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function create()
  {
    // Get the default company
    $defaultCompany = Company::where('default', true)->first();
    // Get all companies for the dropdown
    $companies = Company::all();

    return view('invoices.create', compact('defaultCompany', 'companies'));
  }

  public function store(Request $request)
  {
    // Validate the request
    $request->validate([
      'company_id' => 'required|exists:companies,id',
      // Other validation rules...
    ]);

    // Create the invoice with the selected company
    // $invoice = new Invoice();
    // $invoice->company_id = $request->input('company_id');
    // // Set other invoice fields...
    // $invoice->save();

    return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
  }
}
