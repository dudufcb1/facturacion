<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($this->category)],
      'description' => 'nullable|string',
      'status' => 'required|in:active,inactive',
    ];
  }
}
