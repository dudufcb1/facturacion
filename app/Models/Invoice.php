<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Invoice extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'company_id',
    'client_id',
    'invoice_series',
    'invoice_number',
    'status',
    'due_date',
    'payment_type',
    'credit_days',
    'notes',
    'subtotal',
    'tax',
    'total',
    'currency',
    'exchange_rate',
    'reference_number',
    'issued_at'
  ];

  protected $casts = [
    'due_date' => 'date',
    'issued_at' => 'datetime',
    'subtotal' => 'decimal:2',
    'tax' => 'decimal:2',
    'total' => 'decimal:2',
    'exchange_rate' => 'decimal:4',
  ];

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function client()
  {
    return $this->belongsTo(Client::class);
  }

  public function products()
  {
    return $this->belongsToMany(Product::class, 'invoice_products')
      ->withPivot(['quantity', 'price', 'discount', 'total'])
      ->withTimestamps();
  }

  /**
   * Scope para filtrar por rango de fechas.
   */
  public function scopeDateBetween($query, $from, $to)
  {
    return $query->whereBetween('issued_at', [
      Carbon::parse($from)->startOfDay(),
      Carbon::parse($to)->endOfDay()
    ]);
  }

  /**
   * Scope para filtrar por cliente.
   */
  public function scopeClient($query, $clientId)
  {
    return $query->where('client_id', $clientId);
  }

  /**
   * Scope para filtrar por estado.
   */
  public function scopeStatus($query, $status)
  {
    return $query->where('status', $status);
  }

  /**
   * Scope para filtrar facturas próximas a vencer esta semana.
   */
  public function scopeDueThisWeek($query)
  {
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek = Carbon::now()->endOfWeek();

    return $query->whereBetween('due_date', [$startOfWeek, $endOfWeek])
      ->where('status', '!=', 'paid');
  }

  // En el modelo Invoice

  public function getTotalPaidAttribute()
  {
    return $this->payments()
      ->where('status', 'completed')
      ->sum('converted_amount');
  }

  public function getRemainingBalanceAttribute()
  {
    return $this->total - $this->total_paid;
  }

  public function getIsFullyPaidAttribute()
  {
    return $this->remaining_balance <= 0;
  }

  public function getPaymentStatusAttribute()
  {
    if ($this->remaining_balance <= 0) {
      return 'paid';
    } elseif ($this->total_paid > 0) {
      return 'partial';
    }
    return 'unpaid';
  }
  public function payments()
  {
    return $this->hasMany(Payment::class);
  }
}
