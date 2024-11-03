<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
  use HasFactory;

  // Specify the table name (optional if it follows Laravel's naming conventions)
  protected $table = 'clients';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'document_type',
    'document_number',
    'name',
    'business_name',
    'address',
    'phone',
    'email',
    'status',
    'notes',
    'customer_type',
    'custom_field_1',
    'custom_field_2',
  ];

  /**
   * Define any relationships here if applicable.
   */
  // Example: A client can have many orders
  // public function orders()
  // {
  //     return $this->hasMany(Order::class);
  // }
}
