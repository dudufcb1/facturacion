<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Categoría" routeIndex="categories.index" routeCreate="categories.create" />
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700">
          {{ session('success') }}
        </div>
      @endif

      <!-- Filtros -->
      <div class="mb-6 rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-4">
          <div class="flex-1">
            <input type="text" name="search" placeholder="Buscar categorías..."
              value="{{ request('search') }}"
              class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="w-48">
            <select name="status"
              class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Todos los estados</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
          </div>
          <button type="submit"
            class="rounded-md bg-gray-800 px-4 py-2 text-white hover:bg-gray-700">
            Filtrar
          </button>
        </form>
      </div>

      <!-- Tabla -->
      <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descripción
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
              <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @foreach ($categories as $category)
              <tr>
                <td class="whitespace-nowrap px-6 py-4">{{ $category->name }}</td>
                <td class="px-6 py-4">{{ $category->description }}</td>
                <td class="whitespace-nowrap px-6 py-4">
                  <span
                    class="{{ $category->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full px-2 py-1 text-xs font-semibold">
                    {{ $category->status === 'active' ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <a href="{{ route('categories.edit', $category) }}"
                    class="text-indigo-600 hover:text-indigo-900">Editar</a>
                  <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ml-2 text-red-600 hover:text-red-900"
                      onclick="return confirm('¿Está seguro que desea eliminar esta categoría?')">Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="mt-4">
        {{ $categories->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
