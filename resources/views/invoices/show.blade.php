{{-- resources/views/invoices/show.blade.php --}}
<x-app-layout>
  <div class="mx-auto my-8 max-w-4xl rounded-lg bg-white p-4 shadow-lg">
    {{-- Encabezado --}}
    <div class="border-b pb-6">
      <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
        {{-- Logo y Datos de la Empresa --}}
        <div class="w-full md:w-2/3">
          <h1 class="text-2xl font-bold text-gray-800 md:text-3xl">
            {{ $invoice->company->name }}
          </h1>
          <p class="font-semibold text-gray-600">{{ $invoice->company->owner_name }}</p>
          <div class="mt-2 text-sm text-gray-600">
            <p class="flex items-center gap-2">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              {{ $invoice->company->getFormattedPhonesAttribute() }}
            </p>
            <p class="flex items-center gap-2">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ $invoice->company->address }}
            </p>
            <p class="flex items-center gap-2">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              RUC: {{ $invoice->company->ruc }}
            </p>
          </div>
        </div>

        {{-- Información de la Proforma --}}
        <div class="w-full text-right md:w-1/3">
          <div class="rounded-lg bg-gray-100 p-4">
            <h2 class="text-xl font-bold text-gray-800">PROFORMA</h2>
            <p class="text-2xl font-bold text-blue-600">
              N° {{ str_pad($invoice->invoice_number, 4, '0', STR_PAD_LEFT) }}
            </p>
            <div class="mt-2 text-sm">
              <p class="text-gray-600">Fecha de Emisión:</p>
              <p class="font-semibold">{{ $invoice->issued_at->format('d/m/Y') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Información del Cliente --}}
    <div class="mt-6 rounded-lg bg-gray-50 p-4">
      <h3 class="mb-3 text-lg font-semibold text-gray-700">Información del Cliente</h3>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <p class="text-sm text-gray-600">Cliente:</p>
          <p class="font-semibold">{{ $invoice->client->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">RUC:</p>
          <p class="font-semibold">{{ $invoice->client->document_number }}</p>
        </div>
        <div class="md:col-span-2">
          <p class="text-sm text-gray-600">Dirección:</p>
          <p class="font-semibold">{{ $invoice->client->address }}</p>
        </div>
      </div>
    </div>

    {{-- Tabla de Productos --}}
    <div class="mt-6 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr class="bg-gray-50">
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cant.</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Descripción</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">P.Unit.</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach ($invoice->products as $product)
            <tr>
              <td class="px-4 py-3 text-sm">{{ $product->pivot->quantity }}</td>
              <td class="px-4 py-3 text-sm">{{ $product->name }}</td>
              <td class="px-4 py-3 text-right text-sm">{{ number_format($product->pivot->price, 2) }}</td>
              <td class="px-4 py-3 text-right text-sm">{{ number_format($product->pivot->total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Totales --}}
    <div class="mt-6 flex justify-end">
      <div class="w-full md:w-1/3">
        <div class="rounded-lg bg-gray-50 p-4">
          <div class="flex items-center justify-between">
            <span class="text-gray-600">Subtotal:</span>
            <span class="font-semibold">C$ {{ number_format($invoice->subtotal, 2) }}</span>
          </div>
          @if ($invoice->tax > 0)
            <div class="mt-2 flex items-center justify-between">
              <span class="text-gray-600">IVA:</span>
              <span class="font-semibold">C$ {{ number_format($invoice->tax, 2) }}</span>
            </div>
          @endif
          <div class="mt-2 flex items-center justify-between border-t pt-2">
            <span class="text-lg font-bold">Total:</span>
            <span class="text-lg font-bold text-blue-600">C$ {{ number_format($invoice->total, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Notas y Firmas --}}
    <div class="mt-8">
      <p class="text-sm text-gray-600">
        Elaborar Ck. a nombre de {{ $invoice->company->owner_name }}
      </p>
      <div class="mt-8 grid grid-cols-2 gap-8">
        <div class="text-center">
          <div class="border-t border-gray-300 pt-2">
            <p class="text-sm text-gray-600">Elaborado Por</p>
          </div>
        </div>
        <div class="text-center">
          <div class="border-t border-gray-300 pt-2">
            <p class="text-sm text-gray-600">Cliente</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Botones de Acción --}}
    <div class="mt-8 flex justify-end gap-4">
      <a href="{{ route('invoices.index') }}"
        class="flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-gray-600 transition-colors hover:bg-gray-200">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Regresar
      </a>

      <a href="{{ route('invoices.pdf', $invoice) }}"
        class="flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-red-600 transition-colors hover:bg-red-100">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        PDF
      </a>

      <button onclick="window.print()"
        class="flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-blue-600 transition-colors hover:bg-blue-100">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Imprimir
      </button>
    </div>
  </div>
</x-app-layout>
