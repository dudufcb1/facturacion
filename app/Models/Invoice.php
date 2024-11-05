<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
