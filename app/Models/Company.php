<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
  use SoftDeletes; // Añadido porque la migración incluye softDeletes

  protected $fillable = [
    'name',                  // Nombre del taller/empresa
    'default',               // Default company flag
    'owner_name',            // Nombre del propietario/ingeniero
    'phones',                // Teléfonos
    'address',               // Dirección completa
    'ruc',                   // RUC de la empresa
    'services',              // Descripción de servicios que ofrece
    'logo_path',             // Ruta del logo de la empresa
    'invoice_series',        // Serie de facturas/proformas
    'last_invoice_number',   // Último número de factura
  ];

  // Campos que deben ser convertidos a tipos nativos
  protected $casts = [
    'default' => 'boolean',
    'last_invoice_number' => 'integer',
  ];

  // Método para incrementar el número de factura

  public function updateNextInvoiceNumber()
  {
    $this->last_invoice_number++; // Corrección: Faltaba el operador de incremento
    $this->save();
    return $this->last_invoice_number;
  }

  // Método para asignarle un numero a la factura
  public function assignNextInvoiceNumber()
  {
    return $this->last_invoice_number + 1;
  }

  // Método para formatear los teléfonos
  public function getFormattedPhonesAttribute()
  {
    return str_replace('/', ' / ', $this->phones);
  }

  // Relaciones que podrías necesitar
  // public function invoices()
  // {
  //     return $this->hasMany(Invoice::class);
  // }

  // public function clients()
  // {
  //     return $this->hasMany(Client::class);
  // }
}
