<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
  /**
   * Muestra el formulario para crear una nueva compañía
   */
  public function create()
  {
    return view('companies.create');
  }

  /**
   * Almacena una nueva compañía en la base de datos
   */
  public function store(Request $request)
  {
    // dd($request);
    $messages = [
      'required' => 'El campo :attribute es obligatorio.',
      'string' => 'El campo :attribute debe ser texto.',
      'max' => 'El campo :attribute no debe ser mayor a :max caracteres.',
      'unique' => 'Este :attribute ya está registrado.',
      'boolean' => 'El campo :attribute debe ser verdadero o falso.',
      'integer' => 'El campo :attribute debe ser un número entero.',
      'mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
      'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    ];

    $attributes = [
      'name' => 'nombre',
      'default' => 'empresa por defecto',
      'owner_name' => 'nombre del propietario',
      'phones' => 'teléfonos',
      'address' => 'dirección',
      'ruc' => 'RUC',
      'services' => 'servicios',
      'logo_path' => 'logo',
      'invoice_series' => 'serie de facturas',
      'last_invoice_number' => 'último número de factura',
    ];

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'default' => 'boolean',
      'owner_name' => 'nullable|string|max:255',
      'phones' => 'nullable|string|max:255',
      'address' => 'required|string|max:255',
      'ruc' => 'required|string|unique:companies,ruc|max:20',
      'services' => 'nullable|string',
      'logo_path' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
      'invoice_series' => 'nullable|string|max:20',
      'last_invoice_number' => 'nullable|integer',
    ], $messages, $attributes);

    try {
      // Manejo del archivo de logo
      if ($request->hasFile('logo_path')) {
        $logo = $request->file('logo_path');
        $logoPath = $logo->store('company-logos', 'public');
        $validated['logo_path'] = $logoPath;
      }

      // Si es la primera compañía o se marca como default, desactivar otros defaults
      if ($validated['default'] ?? false) {
        Company::where('default', true)->update(['default' => false]);
      }
      // Si es la primera compañía, hacerla default automáticamente
      if (Company::count() === 0) {
        $validated['default'] = true;
      }

      $company = Company::create($validated);

      return redirect()
        ->route('companies.index')
        ->with('success', 'Empresa creada exitosamente');
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Error al crear la empresa: ' . $e->getMessage());
    }
  }

  /**
   * Muestra el listado de compañías
   */
  public function index()
  {
    $companies = Company::paginate(10); // Adjust the number as needed
    return view('companies.index', compact('companies'));
  }

  /**
   * Muestra una compañía específica
   */
  public function show(Company $company)
  {
    return view('companies.show', compact('company'));
  }

  /**
   * Muestra el formulario para editar una compañía
   */
  public function edit(Company $company)
  {
    return view('companies.edit', compact('company'));
  }

  /**
   * Actualiza una compañía en la base de datos
   */
  public function update(Request $request, Company $company)
  {
    $messages = [
      'required' => 'El campo :attribute es obligatorio.',
      'string' => 'El campo :attribute debe ser texto.',
      'max' => 'El campo :attribute no debe ser mayor a :max caracteres.',
      'unique' => 'Este :attribute ya está registrado.',
      'boolean' => 'El campo :attribute debe ser verdadero o falso.',
      'integer' => 'El campo :attribute debe ser un número entero.',
      'mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
      'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    ];

    $attributes = [
      'name' => 'nombre',
      'default' => 'empresa por defecto',
      'owner_name' => 'nombre del propietario',
      'phones' => 'teléfonos',
      'address' => 'dirección',
      'ruc' => 'RUC',
      'services' => 'servicios',
      'logo_path' => 'logo',
      'invoice_series' => 'serie de facturas',
      'last_invoice_number' => 'último número de factura',
    ];

    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'default' => 'boolean',
      'owner_name' => 'nullable|string|max:255',
      'phones' => 'nullable|string|max:255',
      'address' => 'required|string|max:255',
      'ruc' => 'required|string|max:20|unique:companies,ruc,' . $company->id,
      'services' => 'nullable|string',
      'logo_path' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
      'invoice_series' => 'nullable|string|max:20',
      'last_invoice_number' => 'nullable|integer',
    ], $messages, $attributes);

    try {
      if ($request->hasFile('logo_path')) {
        // Eliminar logo anterior si existe
        if ($company->logo_path) {
          Storage::disk('public')->delete($company->logo_path);
        }
        $logo = $request->file('logo_path');
        $logoPath = $logo->store('company-logos', 'public');
        $validated['logo_path'] = $logoPath;
      }

      if ($validated['default'] ?? false) {
        Company::where('id', '!=', $company->id)
          ->where('default', true)
          ->update(['default' => false]);
      }

      $company->update($validated);

      return redirect()
        ->route('companies.index')
        ->with('success', 'Empresa actualizada exitosamente');
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Error al actualizar la empresa: ' . $e->getMessage());
    }
  }

  /**
   * Elimina una compañía de la base de datos
   */
  public function destroy(Company $company)
  {
    try {
      if ($company->default) {
        return redirect()
          ->back()
          ->with('error', 'No se puede eliminar la empresa por defecto');
      }

      if ($company->logo_path) {
        Storage::disk('public')->delete($company->logo_path);
      }

      $company->delete();

      return redirect()
        ->route('companies.index')
        ->with('success', 'Empresa eliminada exitosamente');
    } catch (\Exception $e) {
      return redirect()
        ->back()
        ->with('error', 'Error al eliminar la empresa: ' . $e->getMessage());
    }
  }
}
