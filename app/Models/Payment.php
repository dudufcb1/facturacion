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
  protected $casts = [
    'payment_date' => 'datetime',
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

  // Agregar esta nueva constante
  const STATUS_TRANSLATIONS = [
    'pending' => 'Pendiente',
    'completed' => 'Completado',
    'cancelled' => 'Cancelado',
    'rejected' => 'Rechazado'
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
  public function invoice()
  {
    return $this->belongsTo(Invoice::class);
  }

  // Agregar este método
  public function getStatusSpanishAttribute()
  {
    return self::STATUS_TRANSLATIONS[$this->status] ?? $this->status;
  }
}
