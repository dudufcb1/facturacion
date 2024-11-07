@php
  use App\Models\Payment;
@endphp


<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Recibo de Pago #{{ $payment->id }}
      </h2>
      <a href="{{ route('invoices.show', $payment->invoice_id) }}"
        class="rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
        Volver a Factura
      </a>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <div class="p-6">
          <!-- Información de la Factura -->
          <div class="mb-6 border-b pb-4">
            <h3 class="mb-4 text-lg font-semibold">Información de la Factura</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Número de Factura:</p>
                <p class="font-medium">{{ $payment->invoice->invoice_series }}-{{ $payment->invoice->invoice_number }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Cliente:</p>
                <p class="font-medium">{{ $payment->invoice->client->name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Monto Total de Factura:</p>
                <p class="font-medium">{{ number_format($payment->invoice->total, 2) }} {{ $payment->invoice_currency }}
                </p>
              </div>
            </div>
          </div>

          <!-- Detalles del Pago -->
          <div class="mb-6 border-b pb-4">
            <h3 class="mb-4 text-lg font-semibold">Detalles del Pago</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Número de Pago:</p>
                <p class="font-medium">
                  {{ $payment->invoice->payments()->where('created_at', '<=', $payment->created_at)->count() }}
                  de {{ $payment->invoice->payments()->count() }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Fecha de Pago:</p>
                @if ($payment->payment_date)
                  {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                @else
                  -
                @endif

              </div>
              <div>
                <p class="text-sm text-gray-600">Método de Pago:</p>
                <p class="font-medium">{{ Payment::PAYMENT_METHODS[$payment->payment_method] }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Estado:</p>
                @if ($payment->status === 'completed')
                  <span
                    class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                    Completado
                  </span>
                @elseif ($payment->status === 'pending')
                  <span
                    class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                    Pendiente
                  </span>
                @else
                  <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                    Cancelado
                  </span>
                @endif
              </div>
            </div>
          </div>

          <!-- Información Monetaria -->
          <div class="mb-6 border-b pb-4">
            <h3 class="mb-4 text-lg font-semibold">Información Monetaria</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-600">Monto Pagado:</p>
                <p class="font-medium">{{ number_format($payment->amount_paid, 2) }} {{ $payment->payment_currency }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Tasa de Cambio:</p>
                <p class="font-medium">{{ number_format($payment->exchange_rate, 4) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Monto Convertido:</p>
                <p class="font-medium">{{ number_format($payment->converted_amount, 2) }}
                  {{ $payment->invoice_currency }}</p>
              </div>
              @if ($payment->bank_name)
                <div>
                  <p class="text-sm text-gray-600">Banco:</p>
                  <p class="font-medium">{{ $payment->bank_name }}</p>
                </div>
              @endif
              @if ($payment->reference_number)
                <div>
                  <p class="text-sm text-gray-600">Referencia:</p>
                  <p class="font-medium">{{ $payment->reference_number }}</p>
                </div>
              @endif
            </div>
          </div>

          @if ($payment->notes)
            <div class="mb-6 border-b pb-4">
              <h3 class="mb-4 text-lg font-semibold">Notas</h3>
              <p class="text-gray-700">{{ $payment->notes }}</p>
            </div>
          @endif

          <!-- Tokens -->
          <div class="mt-8 text-xs text-gray-500">
            <p>Token Parcial: {{ $payment->partial_token }}</p>
            @if ($payment->final_token)
              <p>Token Final: {{ $payment->final_token }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
