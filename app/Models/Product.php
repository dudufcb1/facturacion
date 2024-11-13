<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'notes',
        'unit_price',
        'currency',
        'stock',
        'status',
        'category_id', // Cambiamos category por category_id
        'tax_rate',
        'unit_of_measure'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
