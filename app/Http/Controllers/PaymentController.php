<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
  public function index(Request $request)
  {
    $query = Payment::with(['invoice.client'])
      ->orderBy('payment_date', 'desc');

    // Aplicar filtros
    if ($request->date_from) {
      $query->whereDate('payment_date', '>=', $request->date_from);
    }

    if ($request->date_to) {
      $query->whereDate('payment_date', '<=', $request->date_to);
    }

    if ($request->payment_method) {
      $query->where('payment_method', $request->payment_method);
    }

    $payments = $query->paginate(10)->withQueryString();

    return view('payments.index', compact('payments'));
  }

  public function create(Request $request)
  {
    $invoice = null;
    if ($request->has('invoice')) {
      $invoice = Invoice::findOrFail($request->invoice);
    }

    return view('payments.create', [
      'invoice' => $invoice,
      'currencies' => Payment::CURRENCY,
      'paymentMethods' => Payment::PAYMENT_METHODS,
    ]);
  }

  public function store(Request $request)
  {
    // Primero obtenemos la factura para saber su moneda
    $invoice = Invoice::findOrFail($request->invoice_id);

    $validated = $request->validate([
      'invoice_id' => 'required|exists:invoices,id',
      'amount_paid' => 'required|numeric|min:0',
      'payment_currency' => 'required|in:USD,NIO',
      'exchange_rate' => [
        'numeric',
        'min:0',
        function ($attribute, $value, $fail) use ($request, $invoice) {
          // Solo requerimos exchange_rate si la moneda de pago es diferente a la moneda de la factura
          if ($request->payment_currency !== $invoice->currency) {
            if (empty($value)) {
              $fail('El tipo de cambio es requerido cuando la moneda de pago es diferente a la moneda de la factura.');
            }
          }
        },
      ],
      'payment_method' => 'required|in:' . implode(',', array_keys(Payment::PAYMENT_METHODS)),
      'payment_date' => 'required|date',
      'reference_number' => 'nullable|string|max:255',
      'reference_document' => 'nullable|string|max:255',
      'notes' => 'nullable|string',
      'bank_name' => 'nullable|string|max:255',
      'bank_account' => 'nullable|string|max:255',
    ]);

    try {
      DB::beginTransaction();

      // Si las monedas son iguales, forzamos el tipo de cambio a 1
      if ($invoice->currency === $request->payment_currency) {
        $validated['exchange_rate'] = 1;
      }

      $payment = new Payment($validated);
      $payment->user_id = auth()->id();
      $payment->save();

      DB::commit();

      return redirect()
        ->route('payments.show', $payment)
        ->with('success', 'Pago registrado correctamente.');
    } catch (\Exception $e) {
      DB::rollBack();
      return back()
        ->withInput()
        ->with('error', 'Error al registrar el pago: ' . $e->getMessage());
    }
  }
  public function show(Payment $payment)
  {
    $payment->load(['invoice.client', 'invoice.payments']);
    return view('payments.show', compact('payment'));
  }
}
