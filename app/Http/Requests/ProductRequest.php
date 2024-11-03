<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'code' => ['required', Rule::unique('products')->ignore($this->product)],
      'name' => 'required|string|max:255',
      'description' => 'nullable|string',
      'notes' => 'nullable|string',
      'unit_price' => 'required|numeric|min:0',
      'stock' => 'required|integer|min:0',
      'status' => 'required|in:active,inactive',
      'currency' => 'required|in:NIO,USD',
      'category_id' => 'required|exists:categories,id', // Cambiamos la validación
      'tax_rate' => 'required|numeric|min:0',
      'unit_of_measure' => 'required|string|max:255',
    ];
  }
}
