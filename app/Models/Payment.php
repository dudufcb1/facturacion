<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
  use SoftDeletes;

  // Enum para Currency
  const CURRENCY = [
    'USD' => 'Dólares',
    'NIO' => 'Córdobas'
  ];

  const CURRENCY_SYMBOLS = [
    'USD' => '$',
    'NIO' => 'C$'
  ];

  // Enum para PaymentMethod
  const PAYMENT_METHODS = [
    'cash' => 'Efectivo',
    'bank_transfer' => 'Transferencia Bancaria',
    'check' => 'Cheque',
    'credit_card' => 'Tarjeta de Crédito',
    'debit_card' => 'Tarjeta de Débito',
    'other' => 'Otro'
  ];

  protected $fillable = [
    'invoice_id',
    'user_id',
    'amount_paid',
    'payment_currency',
    'exchange_rate',
    'converted_amount',
    'invoice_currency',
    'payment_method',
    'reference_number',
    'reference_document',
    'notes',
    'status',
    'payment_date',
    'bank_name',
    'bank_account'
  ];

  protected $casts = [
    'amount_paid' => 'decimal:2',
    'exchange_rate' => 'decimal:4',
    'converted_amount' => 'decimal:2',
    'payment_date' => 'datetime',
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($payment) {
      $payment->partial_token = Str::uuid();

      // Obtener la moneda de la factura
      $invoice = Invoice::findOrFail($payment->invoice_id);
      $payment->invoice_currency = $invoice->currency;

      // Calcular el monto convertido
      if ($payment->payment_currency === $payment->invoice_currency) {
        $payment->converted_amount = $payment->amount_paid;
        $payment->exchange_rate = 1;
      } else {
        // Si el pago es en córdobas y la factura en dólares
        if ($payment->payment_currency === 'NIO' && $payment->invoice_currency === 'USD') {
          $payment->converted_amount = $payment->amount_paid / $payment->exchange_rate;
        }
        // Si el pago es en dólares y la factura en córdobas
        else if ($payment->payment_currency === 'USD' && $payment->invoice_currency === 'NIO') {
          $payment->converted_amount = $payment->amount_paid * $payment->exchange_rate;
        }
      }
    });

    static::created(function ($payment) {
      $payment->checkInvoicePaymentStatus();
    });
  }

  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function checkInvoicePaymentStatus()
  {
    $invoice = $this->invoice;
    $totalPaidConverted = $invoice->payments()->sum('converted_amount');

    if ($totalPaidConverted >= $invoice->total) {
      $invoice->status = 'paid';
      $invoice->save();

      $this->final_token = Str::uuid();
      $this->save();
    }
  }

  public function getFormattedAmountPaidAttribute()
  {
    return self::CURRENCY_SYMBOLS[$this->payment_currency] . ' ' . number_format($this->amount_paid, 2);
  }

  public function getFormattedConvertedAmountAttribute()
  {
    return self::CURRENCY_SYMBOLS[$this->invoice_currency] . ' ' . number_format($this->converted_amount, 2);
  }

  public function getConversionDetailsAttribute()
  {
    if ($this->payment_currency === $this->invoice_currency) {
      return 'Sin conversión necesaria';
    }

    return sprintf(
      '%s %s = %s %s (TC: %s)',
      self::CURRENCY_SYMBOLS[$this->payment_currency],
      number_format($this->amount_paid, 2),
      self::CURRENCY_SYMBOLS[$this->invoice_currency],
      number_format($this->converted_amount, 2),
      number_format($this->exchange_rate, 4)
    );
  }

  // Scopes
  public function scopeDateBetween($query, $from, $to)
  {
    return $query->whereBetween('payment_date', [$from, $to]);
  }

  public function scopeByPaymentMethod($query, $method)
  {
    return $query->where('payment_method', $method);
  }

  public function scopeByStatus($query, $status)
  {
    return $query->where('status', $status);
  }

  public function scopeByCurrency($query, $currency)
  {
    return $query->where('payment_currency', $currency);
  }

  public function scopeByInvoice($query, $invoiceId)
  {
    return $query->where('invoice_id', $invoiceId);
  }
}
