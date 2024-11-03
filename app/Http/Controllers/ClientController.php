<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
  /**
   * Display a listing of the clients.
   */
  public function index(Request $request)
  {
    $query = Client::query();

    // Aplicar filtro de búsqueda
    if ($request->has('search')) {
      $searchTerm = $request->search;
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'LIKE', "%{$searchTerm}%")
          ->orWhere('business_name', 'LIKE', "%{$searchTerm}%")
          ->orWhere('document_number', 'LIKE', "%{$searchTerm}%")
          ->orWhere('email', 'LIKE', "%{$searchTerm}%");
      });
    }

    // Aplicar filtro de estado
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    // Paginar los resultados (por ejemplo, 10 por página)
    $clients = $query->paginate(10)->withQueryString();

    return view('clients.index', compact('clients'));
  }

  /**
   * Show the form for creating a new client.
   */
  public function create()
  {
    return view('clients.create');
  }

  /**
   * Store a newly created client in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'document_type' => 'required|in:DNI,RUC',
      'document_number' => 'required|unique:clients,document_number',
      'name' => 'nullable|string|max:255',
      'business_name' => 'nullable|string|max:255',
      'address' => 'nullable|string|max:500',
      'phone' => 'nullable|string|max:20',
      'email' => 'nullable|email|unique:clients,email',
      'status' => 'required|in:active,inactive',
      'notes' => 'nullable|string',
      'customer_type' => 'required|in:regular,premium,vip',
      'custom_field_1' => 'nullable|string|max:255',
      'custom_field_2' => 'nullable|string|max:255',
    ]);

    Client::create($validated);

    return redirect()->route('clients.index')
      ->with('success', 'Cliente creado exitosamente.');
  }

  /**
   * Display the specified client.
   */
  public function show(Client $client)
  {
    return view('clients.show', compact('client'));
  }

  /**
   * Show the form for editing the specified client.
   */
  public function edit(Client $client)
  {
    return view('clients.edit', compact('client'));
  }

  /**
   * Update the specified client in storage.
   */
  public function update(Request $request, Client $client)
  {
    $validated = $request->validate([
      'document_type' => 'required|in:DNI,RUC',
      'document_number' => 'required|unique:clients,document_number,' . $client->id,
      'name' => 'nullable|string|max:255',
      'business_name' => 'nullable|string|max:255',
      'address' => 'nullable|string|max:500',
      'phone' => 'nullable|string|max:20',
      'email' => 'nullable|email|unique:clients,email,' . $client->id,
      'status' => 'required|in:active,inactive',
      'notes' => 'nullable|string',
      'customer_type' => 'required|in:regular,premium,vip',
      'custom_field_1' => 'nullable|string|max:255',
      'custom_field_2' => 'nullable|string|max:255',
    ]);

    $client->update($validated);

    return redirect()->route('clients.index')
      ->with('success', 'Cliente actualizado exitosamente.');
  }

  /**
   * Remove the specified client from storage.
   */
  public function destroy(Client $client)
  {
    $client->delete();
    return redirect()->route('clients.index')
      ->with('success', 'Cliente eliminado exitosamente.');
  }
}
