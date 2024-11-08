<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Factura" routeIndex="invoices.index" routeCreate="invoices.create" />
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

      {{-- Filtros de búsqueda --}}
      <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap items-end gap-4">
          {{-- Filtro de fecha --}}
          <div class="w-full md:w-1/3">
            <label for="date_filter" class="block text-sm font-medium text-gray-700">Filtro de Fecha</label>
            <select name="date_filter" id="date_filter"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Todas las fechas</option>
              <option value="this_week" {{ $dateFilter === 'this_week' ? 'selected' : '' }}>Esta semana</option>
              <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>Este mes</option>
              <option value="last_month" {{ $dateFilter === 'last_month' ? 'selected' : '' }}>Mes pasado</option>
              <option value="custom" {{ $dateFilter === 'custom' ? 'selected' : '' }}>Rango personalizado</option>
            </select>
          </div>

          {{-- Filtros de rango personalizado --}}
          <div class="w-full md:w-1/3">
            <label for="date_from" class="block text-sm font-medium text-gray-700">Desde</label>
            <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div class="w-full md:w-1/3">
            <label for="date_to" class="block text-sm font-medium text-gray-700">Hasta</label>
            <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          </div>

          {{-- Filtro por cliente --}}
          <div class="w-full md:w-1/3">
            <label for="client_id" class="block text-sm font-medium text-gray-700">Cliente</label>
            <select name="client_id" id="client_id"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Todos los clientes</option>
              @foreach ($clients as $client)
                <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                  {{ $client->name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Filtro por estado --}}
          <div class="w-full md:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
            <select name="status" id="status"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Todos los estados</option>
              <option value="issued" {{ $status === 'issued' ? 'selected' : '' }}>Emitidas</option>
              <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pendientes</option>
              <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Pagadas</option>
            </select>
          </div>

          {{-- Botones de acción --}}
          <div class="flex w-full items-end md:w-1/3">
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
              Buscar
            </button>
            <a href="{{ route('invoices.index') }}"
              class="ml-2 rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700">
              Limpiar
            </a>
          </div>
        </form>
      </div>

      {{-- Dashboard: Facturas por Vencer esta Semana --}}
      <div class="mb-6 rounded-lg bg-white p-4 shadow">
        <h3 class="mb-4 text-lg font-semibold text-gray-800">Facturas por Vencer esta Semana</h3>
        @php
          $dueThisWeek = App\Models\Invoice::dueThisWeek()
              ->with(['client', 'company'])
              ->where('status', '!=', 'paid')
              ->get();
        @endphp
        @if ($dueThisWeek->count())
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Serie/Número
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vencimiento
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              @foreach ($dueThisWeek as $invoice)
                <tr>
                  <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                    {{ $invoice->invoice_series }}-{{ $invoice->invoice_number }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    {{ $invoice->client->name }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    {{ $invoice->due_date->format('d/m/Y') }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                    {{ number_format($invoice->total, 2) }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                    @if ($invoice->payment_status !== 'paid')
                      <a href="{{ route('payments.create', ['invoice' => $invoice->id]) }}"
                        class="text-indigo-600 hover:text-indigo-900">
                        Registrar Pago
                      </a>
                    @endif
                  </td>
                  <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                    <div class="flex space-x-2">
                      <a href="{{ route('invoices.show', $invoice) }}"
                        class="text-blue-600 hover:text-blue-900">Ver</a>
                      <a href="{{ route('invoices.edit', $invoice) }}"
                        class="text-indigo-600 hover:text-indigo-900">Editar</a>
                      <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline"
                        onsubmit="return confirm('¿Estás seguro de querer eliminar esta factura?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p class="text-gray-500">No hay facturas por vencer esta semana.</p>
        @endif
      </div>

      {{-- Listado de facturas con filtros aplicados --}}
      <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Serie/Número</th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Cliente</th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Estado
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tipo
                de Pago</th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Vencimiento</th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total
              </th>
              <th scope="col"
                class="bg-gray-50 px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                Moneda</th>
              <th scope="col" class="relative bg-gray-50 px-6 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($invoices as $invoice)
              <tr>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ $invoice->invoice_series }}-{{ $invoice->invoice_number }}
                  @if ($invoice->reference_number)
                    <div class="text-xs text-gray-500">Ref: {{ $invoice->reference_number }}</div>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $invoice->client->name }}
                  <div class="text-xs">{{ $invoice->client->document_number }}</div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  @if ($invoice->status === 'issued')
                    <span
                      class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Emitida</span>
                  @elseif ($invoice->status === 'paid')
                    <span
                      class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Pagada</span>
                  @else
                    <span
                      class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Pendiente</span>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  @if ($invoice->payment_type === 'cash')
                    <span
                      class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Contado</span>
                  @else
                    <span
                      class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800">Crédito
                      ({{ $invoice->credit_days }} días)
                    </span>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  @if ($invoice->due_date)
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                  @else
                    -
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                  {{ number_format($invoice->total, 2) }}
                  <div class="text-xs text-gray-500">
                    Sub: {{ number_format($invoice->subtotal, 2) }}
                    @if ($invoice->remaining_balance > 0)
                      <br>
                      <span class="font-semibold text-red-600">
                        Deuda: {{ number_format($invoice->remaining_balance, 2) }}
                      </span>
                    @else
                      <br>
                      <span class="font-semibold text-green-600">Pagada</span>
                    @endif
                  </div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                  {{ $invoice->currency }}
                  @if ($invoice->exchange_rate)
                    <div class="text-xs">TC: {{ number_format($invoice->exchange_rate, 2) }}</div>
                  @endif
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <a href="{{ route('invoices.show', $invoice) }}"
                      class="text-blue-600 hover:text-blue-900">Ver</a>
                    {{-- <a href="{{ route('invoices.edit', $invoice) }}"
                      class="text-indigo-600 hover:text-indigo-900">Editar</a> --}}
                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline"
                      onsubmit="return confirm('¿Estás seguro de querer eliminar esta factura?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </form>
                    @if ($invoice->payment_status !== 'paid')
                      <a href="{{ route('payments.create', ['invoice' => $invoice->id]) }}"
                        class="text-indigo-600 hover:text-indigo-900">
                        $ Pagos
                      </a>
                    @endif
                  </div>


                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay facturas registradas</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        {{-- Paginación --}}
        @if ($invoices->count())
          <div class="px-6 py-4">
            {{ $invoices->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
