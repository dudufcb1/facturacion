<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Pagos Registrados') }}
      </h2>
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

      {{-- Filtros de búsqueda --}}
      <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap items-end gap-4">
          {{-- Filtro de fecha --}}
          <div class="w-full md:w-1/4">
            <label for="date_from" class="block text-sm font-medium text-gray-700">Desde</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="w-full md:w-1/4">
            <label for="date_to" class="block text-sm font-medium text-gray-700">Hasta</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>

          {{-- Filtro por método de pago --}}
          <div class="w-full md:w-1/4">
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
            <select name="payment_method" id="payment_method"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Todos los métodos</option>
              @foreach (App\Models\Payment::PAYMENT_METHODS as $key => $value)
                <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>
                  {{ $value }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Botones de acción --}}
          <div class="flex w-full items-end md:w-1/4">
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
              Buscar
            </button>
            <a href="{{ route('payments.index') }}"
              class="ml-2 rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
              Limpiar
            </a>
          </div>
        </form>
      </div>

      {{-- Listado de pagos --}}
      <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Factura
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Cliente
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Fecha
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Método
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Monto
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Estado
              </th>
              <th class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($payments as $payment)
              <tr>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ $payment->invoice->invoice_series }}-{{ $payment->invoice->invoice_number }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $payment->invoice->client->name }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  @if ($payment->payment_date)
                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                  @else
                    -
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ App\Models\Payment::PAYMENT_METHODS[$payment->payment_method] }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ number_format($payment->amount_paid, 2) }} {{ $payment->payment_currency }}
                  @if ($payment->payment_currency !== $payment->invoice_currency)
                    <div class="text-xs text-gray-500">
                      {{ number_format($payment->converted_amount, 2) }} {{ $payment->invoice_currency }}
                    </div>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
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
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <div class="flex space-x-2">
                    <a href="{{ route('payments.show', $payment) }}"
                      class="text-blue-600 hover:text-blue-900">Ver</a>
                    @if ($payment->status !== 'completed')
                      <form action="{{ route('payments.destroy', $payment) }}" method="POST"
                        class="inline"
                        onsubmit="return confirm('¿Estás seguro de querer eliminar este pago?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                  No hay pagos registrados
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- Paginación --}}
        @if ($payments->count())
          <div class="px-6 py-4">
            {{ $payments->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
