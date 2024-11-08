<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Cliente" routeIndex="clients.index" routeCreate="clients.create" />
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 text-xl font-semibold leading-tight text-gray-800">Detalles del Cliente</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
            <p class="mt-1 text-gray-900">{{ $client->document_type }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Número de Documento</label>
            <p class="mt-1 text-gray-900">{{ $client->document_number }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <p class="mt-1 text-gray-900">{{ $client->name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Razón Social</label>
            <p class="mt-1 text-gray-900">{{ $client->business_name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
            <p class="mt-1 text-gray-900">{{ $client->email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <p class="mt-1 text-gray-900">{{ $client->phone }}</p>
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Dirección</label>
            <p class="mt-1 text-gray-900">{{ $client->address }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Estado</label>
            <p class="mt-1 text-gray-900">{{ ucfirst($client->status) }}</p> <!-- Capitaliza la primera letra -->
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Cliente</label>
            <p class="mt-1 text-gray-900">{{ ucfirst($client->customer_type) }}</p> <!-- Capitaliza la primera letra -->
          </div>

          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700">Notas</label>
            <textarea readonly rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $client->notes }}</textarea>
          </div>
        </div>

        <!-- Botones para editar y volver -->
        <div class="mt-6 flex justify-end gap-2">
          <a href="{{ route('clients.index') }}"
            class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
            Volver
          </a>
          <a href="{{ route('clients.edit', $client) }}"
            class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            Editar
          </a>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
