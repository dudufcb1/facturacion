<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\Invoice;

class CreateInvoice extends Component
{
  public $client_search = '';
  public $selected_client = null;
  public $clients = [];
  public $products = [];
  public $status = 'issued';
  public $payment_type = 'cash';
  public $notes;
  public $credit_days;
  public $due_date;
  public $selected_currency = null;
  public $exchange_rate;
  public $product_search = '';
  public $selected_products = [];
  public $quantities = [];
  public $prices = [];
  public $original_prices = [];
  public $subtotal = 0;
  public $tax = 0;
  public $total = 0;
  public $invoice_series;
  public $invoice_number;
  public $reference_number;
  public $use_global_tax = false;
  public $global_tax_rate = null;

  protected $rules = [
    'selected_client' => 'required',
    'selected_products' => 'required|array|min:1',
    'status' => 'required|in:issued,debt',
    'payment_type' => 'required|in:cash,credit',
    'credit_days' => 'required_if:payment_type,credit|nullable|integer',
    'due_date' => 'required_if:payment_type,credit|nullable|date',
    'selected_currency' => 'required|in:NIO,USD',
    'exchange_rate' => 'required_if:selected_currency,USD|numeric|min:0.01',
    'reference_number' => 'nullable|string|max:50',
    'global_tax_rate' => 'nullable|numeric|min:0|max:100',
  ];

  protected $messages = [
    'selected_client.required' => 'El campo cliente es obligatorio.',
    'selected_products.required' => 'Debes seleccionar al menos un producto.',
    'selected_products.array' => 'La selección de productos debe ser una lista.',
    'selected_products.min' => 'Debes seleccionar al menos :min producto.',
    'status.required' => 'El campo estado es obligatorio.',
    'status.in' => 'El estado solo puede ser "emitido" o "deuda".',
    'payment_type.required' => 'El tipo de pago es requerido.',
    'payment_type.in' => 'El tipo de pago solo puede ser "efectivo" o "crédito".',
    'credit_days.required_if' => 'Los días de crédito son obligatorios si el pago es a crédito.',
    'credit_days.integer' => 'Los días de crédito deben ser un número entero.',
    'due_date.required_if' => 'La fecha de vencimiento es obligatoria si el pago es a crédito.',
    'due_date.date' => 'La fecha de vencimiento debe ser una fecha válida.',
    'selected_currency.required' => 'El campo de moneda seleccionada es obligatorio.',
    'selected_currency.in' => 'La moneda solo puede ser NIO o USD.',
    'exchange_rate.required_if' => 'El tipo de cambio es obligatorio si la moneda es USD.',
    'exchange_rate.numeric' => 'El tipo de cambio debe ser un número.',
    'exchange_rate.min' => 'El tipo de cambio debe ser al menos :min.',
    'reference_number.max' => 'El número de referencia no debe exceder :max caracteres.',
    'global_tax_rate.numeric' => 'La tasa de impuesto global debe ser un número.',
    'global_tax_rate.min' => 'La tasa de impuesto global debe ser al menos :min.',
    'global_tax_rate.max' => 'La tasa de impuesto global no debe ser mayor que :max.',
  ];

  public function mount()
  {
    $company = Company::where('default', true)->first();
    $this->invoice_series = $company->invoice_series;
    $this->invoice_number = $company->assignNextInvoiceNumber();
    $this->exchange_rate = 36.50;
  }

  public function isCurrencySelectionRequired()
  {
    $currencies = Product::whereIn('id', array_keys($this->selected_products))->pluck('currency')->unique();
    return $currencies->count() > 1;
  }

  public function updatedClientSearch()
  {
    $this->clients = Client::where('name', 'like', "%{$this->client_search}%")
      ->orWhere('document_number', 'like', "%{$this->client_search}%")
      ->take(5)
      ->get();
  }

  public function selectClient($client_id)
  {
    $this->selected_client = Client::find($client_id);
    $this->client_search = '';
  }

  public function updatedProductSearch()
  {
    $this->products = Product::where('name', 'like', "%{$this->product_search}%")
      ->orWhere('code', 'like', "%{$this->product_search}%")
      ->take(5)
      ->get();
  }

  public function addProduct($product_id)
  {
    $product = Product::find($product_id);

    // Verifica si el producto ya está en la lista de seleccionados. Si no, lo agregamos.
    if (!isset($this->selected_products[$product_id])) {
      $this->selected_products[$product_id] = $product;
      $this->quantities[$product_id] = 1;  // Cantidad inicial de 1
      $this->original_prices[$product_id] = $product->unit_price; // Precio original
      $this->recalculatePrice($product_id); // Recalcula el precio en base a la moneda seleccionada
    }

    // Limpia la búsqueda de productos después de agregar uno
    $this->product_search = '';

    // Recalcula los totales después de agregar el producto
    $this->calculateTotals();
  }



  private function recalculatePrice($product_id)
  {
    $product = $this->selected_products[$product_id];
    $price = $this->original_prices[$product_id];
    if ($product->currency !== $this->selected_currency) {
      if ($product->currency === 'USD' && $this->selected_currency === 'NIO') {
        $price = $price * $this->exchange_rate;
      } elseif ($product->currency === 'NIO' && $this->selected_currency === 'USD') {
        $price = $price / $this->exchange_rate;
      }
    }
    $this->prices[$product_id] = round($price, 2);
  }

  public function removeProduct($product_id)
  {
    unset($this->selected_products[$product_id]);
    unset($this->quantities[$product_id]);
    unset($this->prices[$product_id]);
    unset($this->original_prices[$product_id]);
    $this->calculateTotals();
  }

  public function updateQuantity($product_id, $quantity)
  {
    $this->quantities[$product_id] = $quantity;
    $this->calculateTotals();
  }

  public function updatedPaymentType()
  {
    if ($this->payment_type === 'credit' && $this->credit_days) {
      $this->calculateDueDate();
    } else {
      $this->due_date = null;
      $this->credit_days = null;
    }
  }

  public function updatedCreditDays()
  {
    if ($this->credit_days) {
      $this->calculateDueDate();
    }
  }

  public function calculateDueDate()
  {
    if ($this->credit_days) {
      $this->due_date = now()->addDays(intval($this->credit_days))->format('Y-m-d');
    }
  }

  public function updatedSelectedCurrency()
  {
    foreach ($this->selected_products as $id => $product) {
      $this->recalculatePrice($id);
    }
    $this->calculateTotals();
  }

  public function updatedExchangeRate()
  {
    if ($this->exchange_rate > 0) {
      $this->updatedSelectedCurrency();
    }
  }

  public function calculateTotals()
  {
    $this->subtotal = 0;
    $this->tax = 0;
    $this->total = 0;

    foreach ($this->selected_products as $id => $product) {
      $lineTotal = $this->quantities[$id] * $this->prices[$id];
      $this->subtotal += $lineTotal;
      $taxRate = $this->use_global_tax && $this->global_tax_rate !== null ? $this->global_tax_rate : $product->tax_rate;
      $this->tax += $lineTotal * ($taxRate / 100);
    }

    $this->subtotal = round($this->subtotal, 2);
    $this->tax = round($this->tax, 2);
    $this->total = round($this->subtotal + $this->tax, 2);
  }

  public function save()
  {
    $this->validate();

    $company = Company::where('default', true)->first();
    $invoice = Invoice::create([
      'company_id' => $company->id,
      'client_id' => $this->selected_client->id,
      'invoice_series' => $this->invoice_series,
      'invoice_number' => $this->invoice_number,
      'status' => $this->status,
      'payment_type' => $this->payment_type,
      'credit_days' => $this->credit_days,
      'due_date' => $this->due_date,
      'notes' => $this->notes,
      'subtotal' => $this->subtotal,
      'tax' => $this->tax,
      'total' => $this->total,
      'currency' => $this->selected_currency,
      'exchange_rate' => $this->exchange_rate,
      'reference_number' => $this->reference_number,
      'issued_at' => now(),
    ]);

    foreach ($this->selected_products as $id => $product) {
      $invoice->products()->attach($id, [
        'quantity' => $this->quantities[$id],
        'price' => $this->prices[$id],
        'total' => $this->quantities[$id] * $this->prices[$id],
        'tax_rate' => $this->use_global_tax ? $this->global_tax_rate : $product->tax_rate,
      ]);
    }

    $company->updateNextInvoiceNumber();
    session()->flash('message', 'Factura creada exitosamente.');
    return redirect()->route('invoices.index');
  }

  public function render()
  {
    return view('livewire.invoice.create-invoice');
  }
}
