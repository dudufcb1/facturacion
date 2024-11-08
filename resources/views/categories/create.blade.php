<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Categoría" routeIndex="categories.index" routeCreate="categories.create" />
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="rounded-lg bg-white p-6 shadow">
        <form action="{{ route('categories.store') }}" method="POST">
          @csrf

          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea name="description" id="description" rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            @error('description')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="status" id="status"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
              <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex justify-end gap-2">
            <a href="{{ route('categories.index') }}"
              class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
              Cancelar
            </a>
            <button type="submit"
              class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
