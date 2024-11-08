<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Cliente" routeIndex="clients.index" routeCreate="clients.create" />
  </x-slot>


  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="rounded-lg bg-white p-6 shadow">
        <form action="{{ route('clients.update', $client) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
              <label for="document_type" class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
              <select name="document_type" id="document_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="DNI" {{ old('document_type', $client->document_type) == 'DNI' ? 'selected' : '' }}>DNI
                </option>
                <option value="RUC" {{ old('document_type', $client->document_type) == 'RUC' ? 'selected' : '' }}>RUC
                </option>
              </select>
              @error('document_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="document_number" class="block text-sm font-medium text-gray-700">Número de Documento</label>
              <input type="text" name="document_number" id="document_number"
                value="{{ old('document_number', $client->document_number) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('document_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
              <input type="text" name="name" id="name"
                value="{{ old('name', $client->name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="business_name" class="block text-sm font-medium text-gray-700">Razón Social</label>
              <input type="text" name="business_name" id="business_name"
                value="{{ old('business_name', $client->business_name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('business_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
              <input type="email" name="email" id="email"
                value="{{ old('email', $client->email) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
              <input type="text" name="phone" id="phone"
                value="{{ old('phone', $client->phone) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div class="col-span-2">
              <label for="address" class="block text-sm font-medium text-gray-700">Dirección</label>
              <input type="text" name="address" id="address"
                value="{{ old('address', $client->address) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
              <select name="status" id="status"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Activo
                </option>
                <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactivo
                </option>
              </select>
              @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div>
              <label for="customer_type" class="block text-sm font-medium text-gray-700">Tipo de Cliente</label>
              <select name="customer_type" id="customer_type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="regular"
                  {{ old('customer_type', $client->customer_type) == 'regular' ? 'selected' : '' }}>Regular</option>
                <option value="premium"
                  {{ old('customer_type', $client->customer_type) == 'premium' ? 'selected' : '' }}>Premium</option>
                <option value="vip" {{ old('customer_type', $client->customer_type) == 'vip' ? 'selected' : '' }}>
                  VIP</option>
              </select>
              @error('customer_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div class="col-span-2">
              <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
              <textarea name="notes" id="notes" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $client->notes) }}</textarea>
              @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('clients.index') }}"
              class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
              Cancelar
            </a>
            <button type="submit"
              class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
              Actualizar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
