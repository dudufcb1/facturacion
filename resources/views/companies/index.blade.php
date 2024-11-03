<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Compañías') }}
      </h2>
      <a href="{{ route('companies.create') }}"
        class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
        {{ __('Nueva Compañía') }}
      </a>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      {{-- Mensajes flash --}}
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

      {{-- Listado de compañías --}}
      <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Logo
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Nombre
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                RUC
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Teléfonos
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Dirección
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Por defecto
              </th>
              <th scope="col" class="relative bg-gray-50 px-6 py-3">
                <span class="sr-only">Acciones</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($companies as $company)
              <tr>
                <td class="whitespace-nowrap px-6 py-4">
                  @if ($company->logo_path)
                    <img src="{{ Storage::url($company->logo_path) }}"
                      alt="Logo {{ $company->name }}"
                      class="h-10 w-10 object-contain">
                  @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200">
                      <span class="text-xs text-gray-500">Sin logo</span>
                    </div>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ $company->name }}
                  @if ($company->owner_name)
                    <div class="text-xs text-gray-500">{{ $company->owner_name }}</div>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $company->ruc }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $company->phones }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $company->address }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  @if ($company->default)
                    <span
                      class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                      Sí
                    </span>
                  @else
                    <span
                      class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                      No
                    </span>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('companies.edit', $company) }}"
                      class="text-indigo-600 hover:text-indigo-900">
                      Editar
                    </a>
                    <form action="{{ route('companies.destroy', $company) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('¿Estás seguro de querer eliminar esta compañía?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900">
                        Eliminar
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                  No hay compañías registradas
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if ($companies->count())
          <div class="px-6 py-4">
            {{ $companies->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
