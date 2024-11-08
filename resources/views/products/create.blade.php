<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Producto" routeIndex="products.index" routeCreate="products.create" />
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
          <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
          </div>
        @endif
        @if (session('error'))
          <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
            {{ session('error') }}
          </div>
        @endif

        <form method="post" action="{{ route('products.store') }}" class="max-w-3xl p-10">
          @csrf
          {{-- Información básica del producto --}}
          <div class="mt-4" x-data="{
              sku: '{{ old('code') }}' || '',
              generateSKU() {
                  const categorySelect = document.getElementById('category_id');
                  if (!categorySelect.value) {
                      alert('Por favor, seleccione una categoría antes de generar el SKU');
                      return;
                  }
          
                  // Obtener el nombre de la categoría y tomar las primeras 3 letras
                  const categoryText = categorySelect.options[categorySelect.selectedIndex].text;
                  const categoryCode = categoryText.substring(0, 3).toUpperCase();
          
                  const prefix = 'FSM';
                  const date = new Date();
                  const month = String(date.getMonth() + 1).padStart(2, '0');
                  const year = date.getFullYear().toString().substr(-2);
                  const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
          
                  this.sku = `${prefix}-${categoryCode}-${month}/${year}-${random}`;
              }
          }">
            <div class="flex items-center space-x-2">
              <div class="flex-1">
                <x-input-label for="code" :value="__('Código (SKU)')" />
                <x-text-input
                  id="code"
                  name="code"
                  class="w-full"
                  x-model="sku"
                  required
                  readonly />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
              </div>
              <div class="mt-7">
                <button
                  type="button"
                  class="rounded-md bg-gray-500 px-3 py-2 text-white hover:bg-gray-600"
                  x-on:click="generateSKU()">
                  Generar SKU
                </button>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" class="w-full" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="description" :value="__('Descripción')" />
            <textarea id="description" name="description"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
          </div>

          {{-- Información de precio y moneda --}}
          <div class="grid grid-cols-2 gap-4">
            <div class="mt-4">
              <x-input-label for="unit_price" :value="__('Precio Unitario')" />
              <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" class="w-full"
                :value="old('unit_price')" required />
              <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
            </div>
            <div class="mt-4">
              <x-input-label for="currency" :value="__('Moneda')" />
              <select id="currency" name="currency"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="NIO" {{ old('currency') == 'NIO' ? 'selected' : '' }}>NIO (Córdoba)</option>
                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (Dólar)</option>
              </select>
              <x-input-error :messages="$errors->get('currency')" class="mt-2" />
            </div>
          </div>

          <div class="mt-4">
            <x-input-label for="stock" :value="__('Stock')" />
            <x-text-input id="stock" name="stock" type="number" class="w-full" :value="old('stock')" required />
            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
          </div>

          {{-- Estado y categoría --}}
          <div class="mt-4">
            <x-input-label for="status" :value="__('Estado')" />
            <select id="status" name="status"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
              <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="category_id" :value="__('Categoría')" />
            <select id="category_id" name="category_id"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              <option value="">Seleccione una categoría</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
          </div>

          {{-- Información adicional --}}
          <div class="mt-4">
            <x-input-label for="tax_rate" :value="__('Tasa de Impuesto (%)')" />
            <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" class="w-full" :value="old('tax_rate')"
              required />
            <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="unit_of_measure" :value="__('Unidad de Medida')" />
            <x-text-input id="unit_of_measure" name="unit_of_measure" class="w-full" :value="old('unit_of_measure')" required />
            <x-input-error :messages="$errors->get('unit_of_measure')" class="mt-2" />
          </div>
          <div class="mt-4">
            <x-input-label for="notes" :value="__('Notas adicionales')" />
            <textarea id="notes" name="notes"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
          </div>

          {{-- Botón de envío --}}
          <div class="mt-6">
            <x-primary-button>
              {{ __('Guardar Producto') }}
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
