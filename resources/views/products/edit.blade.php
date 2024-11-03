<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Editar Producto') }}
      </h2>
      {{-- Botón para eliminar producto --}}
      <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
        onsubmit="return confirm('¿Está seguro de que desea eliminar este producto?');">
        @csrf
        @method('DELETE')
        <button type="submit"
          class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
          {{ __('Eliminar Producto') }}
        </button>
      </form>
    </div>
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

        <form method="post" action="{{ route('products.update', $product) }}" class="max-w-3xl p-10">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            {{-- Columna 1 --}}
            <div class="space-y-6">
              <div>
                <x-input-label for="code" :value="__('Código (SKU)')" />
                <x-text-input id="code" name="code" class="mt-1 w-full" :value="old('code', $product->code)" required />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="name" :value="__('Nombre')" />
                <x-text-input id="name" name="name" class="mt-1 w-full" :value="old('name', $product->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="unit_price" :value="__('Precio Unitario')" />
                <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" class="mt-1 w-full"
                  :value="old('unit_price', $product->unit_price)" required />
                <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="currency" :value="__('Moneda')" />
                <select id="currency" name="currency"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                  <option value="NIO" {{ old('currency', $product->currency) == 'NIO' ? 'selected' : '' }}>NIO
                    (Córdoba)</option>
                  <option value="USD" {{ old('currency', $product->currency) == 'USD' ? 'selected' : '' }}>USD
                    (Dólar)</option>
                </select>
                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="stock" :value="__('Stock')" />
                <x-text-input id="stock" name="stock" type="number" class="mt-1 w-full" :value="old('stock', $product->stock)"
                  required />
                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="tax_rate" :value="__('Tasa de Impuesto (%)')" />
                <x-text-input id="tax_rate" name="tax_rate" type="number" step="0.01" class="mt-1 w-full"
                  :value="old('tax_rate', $product->tax_rate)" required />
                <x-input-error :messages="$errors->get('tax_rate')" class="mt-2" />
              </div>
            </div>

            {{-- Columna 2 --}}
            <div class="space-y-6">
              <div>
                <x-input-label for="status" :value="__('Estado')" />
                <select id="status" name="status"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                  <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Activo
                  </option>
                  <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                    Inactivo</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="category_id" :value="__('Categoría')" />
                <select id="category_id" name="category_id"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                  <option value="">Seleccione una categoría</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                      {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="unit_of_measure" :value="__('Unidad de Medida')" />
                <x-text-input id="unit_of_measure" name="unit_of_measure" class="mt-1 w-full" :value="old('unit_of_measure', $product->unit_of_measure)"
                  required />
                <x-input-error :messages="$errors->get('unit_of_measure')" class="mt-2" />
              </div>

              <div>
                <x-input-label for="description" :value="__('Descripción')" />
                <textarea id="description" name="description"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  rows="4">{{ old('description', $product->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="notes" :value="__('Notas')" />
                <textarea id="notes" name="notes"
                  class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  rows="4">{{ old('notes', $product->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
              </div>

            </div>
          </div>

          {{-- Botones de acción --}}
          <div class="mt-8 flex items-center justify-end space-x-4">
            <a href="{{ route('products.index') }}"
              class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
              {{ __('Cancelar') }}
            </a>
            <x-primary-button>
              {{ __('Actualizar Producto') }}
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
