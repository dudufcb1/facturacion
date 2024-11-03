<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      {{ __('Crear Compañia') }}
    </h2>
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

        <form method="post" action="{{ route('companies.store') }}" class="max-w-3xl p-10" enctype="multipart/form-data">
          @csrf

          {{-- Datos básicos de la empresa --}}
          <div class="mt-10">
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" class="w-full" :value="old('name')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
          </div>

          <div class="mt-4 flex items-center justify-items-center gap-4">
            <x-input-label for="default" :value="__('¿Empresa por defecto?')" />
            <input type="hidden" name="default" value="0">
            <input type="checkbox"
              id="default"
              name="default"
              value="1"
              class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              {{ old('default') ? 'checked' : '' }} />
            <x-input-error :messages="$errors->get('default')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="owner_name" :value="__('Nombre del Propietario')" />
            <x-text-input id="owner_name" name="owner_name" class="w-full" :value="old('owner_name')" />
            <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
          </div>

          {{-- Información de contacto --}}
          <div class="mt-4">
            <x-input-label for="phones" :value="__('Teléfonos')" />
            <x-text-input id="phones" name="phones" class="w-full" :value="old('phones')" />
            <x-input-error :messages="$errors->get('phones')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="address" :value="__('Dirección')" />
            <x-text-input id="address" name="address" class="w-full" :value="old('address')" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
          </div>

          {{-- Información legal --}}
          <div class="mt-4">
            <x-input-label for="ruc" :value="__('RUC')" />
            <x-text-input id="ruc" name="ruc" class="w-full" :value="old('ruc')" />
            <x-input-error :messages="$errors->get('ruc')" class="mt-2" />
          </div>

          {{-- Información adicional --}}
          <div class="mt-4">
            <x-input-label for="services" :value="__('Servicios')" />
            <textarea id="services" name="services"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('services') }}</textarea>
            <x-input-error :messages="$errors->get('services')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="logo_path" :value="__('Logo')" />
            <input type="file" id="logo_path" name="logo_path" class="w-full" />
            <x-input-error :messages="$errors->get('logo_path')" class="mt-2" />
          </div>

          {{-- Información de facturación --}}
          <div class="mt-4">
            <x-input-label for="invoice_series" :value="__('Serie de Facturas')" />
            <x-text-input id="invoice_series" name="invoice_series" class="w-full" :value="old('invoice_series')" />
            <x-input-error :messages="$errors->get('invoice_series')" class="mt-2" />
          </div>

          <div class="mt-4">
            <x-input-label for="last_invoice_number" :value="__('Último Número de Factura')" />
            <x-text-input id="last_invoice_number" name="last_invoice_number" type="number" class="w-full"
              :value="old('last_invoice_number')" />
            <x-input-error :messages="$errors->get('last_invoice_number')" class="mt-2" />
          </div>

          {{-- Botón de envío --}}
          <div class="mt-6">
            <x-primary-button>
              {{ __('Guardar') }}
            </x-primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
