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
  public $selected_currency = 'NIO';
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
    'exchange_rate' => 'required|numeric|min:0.01',
    'reference_number' => 'nullable|string|max:50',
    'global_tax_rate' => 'nullable|numeric|min:0|max:100',
  ];

  public function mount()
  {
    $company = Company::where('default', true)->first();
    $this->invoice_series = $company->invoice_series;
    $this->invoice_number = $company->assignNextInvoiceNumber();
    $this->exchange_rate = 36.50; // Valor por defecto
  }

  public function updatedClientSearch()
  {
    if (strlen($this->client_search) > 2) {
      $this->clients = Client::where('name', 'like', "%{$this->client_search}%")
        ->orWhere('document_number', 'like', "%{$this->client_search}%")
        ->take(5)
        ->get();
    }
  }

  public function selectClient($client_id)
  {
    $this->selected_client = Client::find($client_id);
    $this->client_search = '';
  }

  public function updatedProductSearch()
  {
    if (strlen($this->product_search) > 2) {
      $this->products = Product::where('name', 'like', "%{$this->product_search}%")
        ->orWhere('code', 'like', "%{$this->product_search}%")
        ->take(5)
        ->get();
    }
  }

  public function addProduct($product_id)
  {
    $product = Product::find($product_id);
    if (!isset($this->selected_products[$product_id])) {
      $this->selected_products[$product_id] = $product;
      $this->quantities[$product_id] = 1;
      $this->original_prices[$product_id] = $product->unit_price;

      // Convertir el precio si es necesario
      $price = $product->unit_price;
      if ($product->currency !== $this->selected_currency) {
        if ($product->currency === 'USD' && $this->selected_currency === 'NIO') {
          $price = $price * $this->exchange_rate;
        } else if ($product->currency === 'NIO' && $this->selected_currency === 'USD') {
          $price = $price / $this->exchange_rate;
        }
      }
      $this->prices[$product_id] = round($price, 2);
    }
    $this->calculateTotals();
    $this->product_search = '';
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
      $price = $this->original_prices[$id];
      if ($product->currency !== $this->selected_currency) {
        if ($product->currency === 'USD' && $this->selected_currency === 'NIO') {
          $price = $price * $this->exchange_rate;
        } else if ($product->currency === 'NIO' && $this->selected_currency === 'USD') {
          $price = $price / $this->exchange_rate;
        }
      }
      $this->prices[$id] = round($price, 2);
    }
    $this->calculateTotals();
  }

  public function updatedExchangeRate()
  {
    if ($this->exchange_rate > 0) {
      $this->updatedSelectedCurrency();
    }
  }

  public function updatedUseGlobalTax()
  {
    if (!$this->use_global_tax) {
      $this->global_tax_rate = null;
    }
    $this->calculateTotals();
  }

  public function updatedGlobalTaxRate()
  {
    if ($this->use_global_tax && $this->global_tax_rate !== null) {
      $this->calculateTotals();
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

      // Usar impuesto global si está activado, sino usar el impuesto del producto
      $taxRate = $this->use_global_tax && $this->global_tax_rate !== null
        ? $this->global_tax_rate
        : $product->tax_rate;

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
