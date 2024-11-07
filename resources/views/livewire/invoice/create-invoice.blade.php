<div class="rounded-lg bg-white p-6 shadow-lg">
  <h2 class="mb-6 text-2xl font-bold">Crear Nueva Factura</h2>

  <!-- Cliente -->
  <div class="mb-6">
    <x-input-label for="client_search" value="Buscar Cliente" />
    <x-text-input
      wire:model.live="client_search"
      type="text"
      class="w-full"
      placeholder="Buscar por nombre o número de documento" />
    @if ($client_search && !$selected_client)
      <div class="mt-2 rounded-lg bg-white shadow">
        @foreach ($clients ?? [] as $client)
          <div class="cursor-pointer p-2 hover:bg-gray-100"
            wire:click="selectClient({{ $client->id }})">
            {{ $client->name }} - {{ $client->document_number }}
          </div>
        @endforeach
      </div>
    @endif
    @if ($selected_client)
      <div class="mt-2 rounded-lg bg-gray-50 p-3">
        <p class="font-bold">Cliente seleccionado:</p>
        <p>{{ $selected_client->name }}</p>
        <p>{{ $selected_client->document_number }}</p>
      </div>
    @endif
  </div>

  <!-- Información de Factura -->
  <div class="mb-6 grid grid-cols-3 gap-4">
    <div>
      <x-input-label value="Serie" />
      <x-text-input type="text" value="{{ $invoice_series }}" disabled class="w-full bg-gray-100" />
    </div>
    <div>
      <x-input-label value="Número" />
      <x-text-input type="text" value="{{ $invoice_number }}" disabled class="w-full bg-gray-100" />
    </div>
    <div>
      <x-input-label for="reference_number" value="Número de Referencia" />
      <x-text-input wire:model="reference_number" type="text" class="w-full" />
    </div>
  </div>

  <!-- Moneda y Tipo de Cambio -->
  <div class="mb-6 grid grid-cols-2 gap-4"
    x-data="{ selectedCurrency: @entangle('selected_currency').live }">
    <div>
      <x-input-label for="selected_currency" value="Moneda de Facturación" />
      <select wire:model.live="selected_currency"
        x-model="selectedCurrency"
        class="w-full rounded-md border-gray-300">
        <option value="NIO">Córdobas (NIO)</option>
        <option value="USD">Dólares (USD)</option>
      </select>
    </div>
    <div>
      <x-input-label for="exchange_rate" value="Tipo de Cambio (1 USD = X NIO)" />
      @if ($selected_currency === 'NIO' && !$exchange_rate)
        <p class="text-red-500">El tipo de cambio es obligatorio cuando la moneda es Córdobas.</p>
      @endif
      <x-text-input
        wire:model.live="exchange_rate"
        type="number"
        step="0.01"
        class="w-full"
        x-bind:disabled="selectedCurrency === 'USD'"
        x-bind:class="{
            'opacity-50 bg-gray-100 cursor-not-allowed': selectedCurrency === 'USD',
            'bg-white': selectedCurrency !== 'USD'
        }"
        required />
    </div>
  </div>

  <!-- Productos -->
  <div class="mb-6">
    <x-input-label for="product_search" value="Agregar Productos" />
    <x-text-input
      wire:model.live="product_search"
      type="text"
      class="w-full"
      placeholder="Buscar por nombre o código"
      x-bind:disabled="selectedCurrency === 'NIO' && !exchange_rate"
      x-bind:class="{
          'opacity-50 bg-gray-100 cursor-not-allowed': selectedCurrency === 'NIO' && !exchange_rate,
          'bg-white': !(selectedCurrency === 'NIO' && !exchange_rate)
      }" />

    @if ($product_search)
      <div class="mt-2 rounded-lg bg-white shadow">
        @foreach ($products ?? [] as $product)
          <div class="cursor-pointer p-2 hover:bg-gray-100"
            wire:click="addProduct({{ $product->id }})">
            {{ $product->name }} - {{ $product->code }}
            ({{ $product->currency }} {{ number_format($product->unit_price, 2) }})
          </div>
        @endforeach
      </div>
    @endif

    <!-- Lista de productos seleccionados -->
    @if (count($selected_products))
      <table class="mt-4 w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="p-2 text-left">Producto</th>
            <th class="p-2">Cantidad</th>
            <th class="p-2">Precio ({{ $selected_currency }})</th>
            <th class="p-2">Total ({{ $selected_currency }})</th>
            <th class="p-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($selected_products as $id => $product)
            <tr>
              <td class="p-2">
                {{ $product->name }}
                @if ($product->currency !== $selected_currency)
                  <span class="text-sm text-gray-500">
                    (Original: {{ $product->currency }} {{ number_format($original_prices[$id], 2) }})
                  </span>
                @endif
              </td>
              <td class="p-2">
                <input
                  type="number"
                  wire:model.live="quantities.{{ $id }}"
                  wire:change="updateQuantity({{ $id }}, $event.target.value)"
                  class="w-20 rounded-md border-gray-300"
                  min="1" />
              </td>
              <td class="p-2">{{ number_format($prices[$id], 2) }}</td>
              <td class="p-2">{{ number_format($quantities[$id] * $prices[$id], 2) }}</td>
              <td class="p-2">
                <button wire:click="removeProduct({{ $id }})"
                  class="text-red-600 hover:text-red-800">
                  Eliminar
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Impuesto Global -->
      <div class="mt-4">
        <div class="flex items-center">
          <input type="checkbox" wire:model.live="use_global_tax" class="mr-2">
          <x-input-label value="Usar impuesto global" />
        </div>
        @if ($use_global_tax)
          <div class="mt-2">
            <x-input-label for="global_tax_rate" value="Tasa de impuesto (%)" />
            <x-text-input
              wire:model.live="global_tax_rate"
              type="number"
              step="0.01"
              class="w-full" />
          </div>
        @endif
      </div>

      <!-- Totales -->
      <div class="mt-4 text-right">
        <p>Subtotal: {{ $selected_currency }} {{ number_format($subtotal, 2) }}</p>
        <p>IVA {{ $use_global_tax ? "($global_tax_rate%)" : '' }}:
          {{ $selected_currency }} {{ number_format($tax, 2) }}</p>
        <p class="text-lg font-bold">
          Total: {{ $selected_currency }} {{ number_format($total, 2) }}
        </p>
      </div>
    @endif
  </div>

  <!-- Estado y Tipo de Pago -->
  <div class="mb-6 grid grid-cols-2 gap-4">
    <div>
      <x-input-label for="status" value="Estado" />
      <select wire:model.live="status" class="w-full rounded-md border-gray-300">
        <option value="issued">Emitida</option>
        <option value="debt">Pendiente</option>
      </select>
    </div>
    <div>
      <x-input-label for="payment_type" value="Tipo de Pago" />
      <select wire:model.live="payment_type" class="w-full rounded-md border-gray-300">
        <option value="cash">Contado</option>
        <option value="credit">Crédito</option>
      </select>
    </div>
  </div>

  @if ($payment_type === 'credit')
    <div class="mb-6 grid grid-cols-2 gap-4">
      <div>
        <x-input-label for="credit_days" value="Días de Crédito" />
        <x-text-input wire:model.live="credit_days" type="number" class="w-full" />
      </div>
      @if ($credit_days)
        <div>
          <x-input-label value="Fecha de Vencimiento" />
          <x-text-input type="date" value="{{ $due_date }}" disabled class="w-full bg-gray-100" />
        </div>
      @endif
    </div>
  @endif

  <!-- Notas -->
  <div class="mb-6">
    <x-input-label for="notes" value="Notas" />
    <textarea
      wire:model="notes"
      class="w-full rounded-md border-gray-300"
      rows="3"></textarea>
  </div>

  <!-- Mensajes de Error -->
  @if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Botón de Guardar -->
  <div class="flex justify-end">
    <button
      wire:click="save"
      class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
      Guardar Factura
    </button>
  </div>
</div>
