<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Registrar Pago') }}
      </h2>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <div class="p-6">
          @if ($invoice)
            <div class="mb-6 rounded-lg bg-gray-100 p-4">
              <h3 class="mb-2 text-lg font-semibold">Detalles de la Factura</h3>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                  <p class="text-sm font-medium text-gray-600">Número de Factura</p>
                  <p class="text-lg">{{ $invoice->invoice_series }}-{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Cliente</p>
                  <p class="text-lg">{{ $invoice->client->name }}</p>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-600">Total a Pagar</p>
                  <p class="text-lg" id="invoice-amount"
                    data-currency="{{ $invoice->currency }}"
                    data-amount="{{ $invoice->remaining_balance }}">
                    {{ $invoice->currency }} {{ number_format($invoice->remaining_balance, 2) }}
                  </p>
                </div>
              </div>
            </div>
          @endif

          <form action="{{ route('payments.store') }}" method="POST" class="space-y-6">
            @csrf

            @if ($invoice)
              <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            @else
              <div>
                <label for="invoice_id" class="block text-sm font-medium text-gray-700">Factura</label>
                <select name="invoice_id" id="invoice_id" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  <option value="">Seleccione una factura</option>
                  @foreach (App\Models\Invoice::where('status', '!=', 'paid')->get() as $inv)
                    <option value="{{ $inv->id }}"
                      data-currency="{{ $inv->currency }}"
                      data-amount="{{ $inv->remaining_balance }}"
                      {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                      {{ $inv->invoice_series }}-{{ $inv->invoice_number }} -
                      {{ $inv->client->name }}
                      ({{ $inv->currency }} {{ number_format($inv->remaining_balance, 2) }})
                    </option>
                  @endforeach
                </select>
                @error('invoice_id')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <label for="amount_paid" class="block text-sm font-medium text-gray-700">Monto a Pagar</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                  <input type="number" step="0.01" name="amount_paid" id="amount_paid" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    value="{{ old('amount_paid') }}">
                </div>
                @error('amount_paid')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="payment_currency" class="block text-sm font-medium text-gray-700">Moneda del Pago</label>
                <select name="payment_currency" id="payment_currency" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  @foreach ($currencies as $code => $name)
                    <option value="{{ $code }}" {{ old('payment_currency') == $code ? 'selected' : '' }}>
                      {{ $name }}
                    </option>
                  @endforeach
                </select>
                @error('payment_currency')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="exchange_rate" class="block text-sm font-medium text-gray-700">
                  Tipo de Cambio
                  <span id="exchange-rate-helper" class="ml-1 text-sm text-gray-500"></span>
                </label>
                <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  value="{{ old('exchange_rate', 1) }}">
                @error('exchange_rate')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700">Método de Pago</label>
                <select name="payment_method" id="payment_method" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                  @foreach ($paymentMethods as $code => $name)
                    <option value="{{ $code }}" {{ old('payment_method') == $code ? 'selected' : '' }}>
                      {{ $name }}
                    </option>
                  @endforeach
                </select>
                @error('payment_method')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="payment_date" class="block text-sm font-medium text-gray-700">Fecha de Pago</label>
                <input type="datetime-local" name="payment_date" id="payment_date" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  value="{{ old('payment_date', now()->format('Y-m-d\TH:i')) }}">
                @error('payment_date')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="reference_number" class="block text-sm font-medium text-gray-700">Número de
                  Referencia</label>
                <input type="text" name="reference_number" id="reference_number"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  value="{{ old('reference_number') }}">
                @error('reference_number')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="col-span-2">
                <div class="rounded-lg bg-blue-50 p-4">
                  <h4 class="font-medium text-blue-900">Resumen de Conversión</h4>
                  <div id="conversion-summary" class="mt-2 space-y-2 text-blue-800">
                    <!-- El resumen se llenará con JavaScript -->
                  </div>
                </div>
              </div>

              <div class="col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notes" id="notes" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes')
                  <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="flex justify-end space-x-3">
              <a href="{{ route('invoices.index') }}"
                class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cancelar
              </a>
              <button type="submit"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Registrar Pago
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const amountInput = document.querySelector('input[name="amount_paid"]');
      const currencySelect = document.querySelector('select[name="payment_currency"]');
      const exchangeRateInput = document.querySelector('input[name="exchange_rate"]');
      const conversionSummary = document.querySelector('#conversion-summary');

      // Obtener la moneda y monto de la factura
      const invoiceElement = document.querySelector('[data-currency]');
      const invoiceCurrency = invoiceElement?.dataset.currency;
      const invoiceAmount = parseFloat(invoiceElement?.dataset.amount || 0);

      function updateExchangeRateVisibility() {
        const paymentCurrency = currencySelect.value;

        if (invoiceCurrency === paymentCurrency) {
          // Si las monedas son iguales, deshabilitar y establecer valor a 1
          exchangeRateInput.value = '1';
          exchangeRateInput.disabled = true;
          exchangeRateInput.classList.add('bg-gray-100');
        } else if ((invoiceCurrency === 'USD' && paymentCurrency === 'NIO') ||
          (invoiceCurrency === 'NIO' && paymentCurrency === 'USD')) {
          // Habilitar para conversión entre USD y NIO
          exchangeRateInput.disabled = false;
          exchangeRateInput.classList.remove('bg-gray-100');
          // Mantener el valor actual si existe, si no, establecer valor predeterminado
          if (!exchangeRateInput.value || exchangeRateInput.value === '1') {
            exchangeRateInput.value = '36.50';
          }
        }

        updateConversionSummary();
      }

      function updateConversionSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const rate = parseFloat(exchangeRateInput.value) || 1;
        const paymentCurrency = currencySelect.value;
        let convertedAmount = 0;
        let summaryHTML = '';

        if (invoiceCurrency !== paymentCurrency) {
          if (invoiceCurrency === 'USD' && paymentCurrency === 'NIO') {
            convertedAmount = amount / rate;
            summaryHTML = `
                    <p>Monto ingresado: C$ ${amount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                    <p>Tipo de cambio: C$ ${rate.toLocaleString('es-NI', {minimumFractionDigits: 4, maximumFractionDigits: 4})} = $1 USD</p>
                    <p>Equivalente en USD: $ ${convertedAmount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                `;
          } else if (invoiceCurrency === 'NIO' && paymentCurrency === 'USD') {
            convertedAmount = amount * rate;
            summaryHTML = `
                    <p>Monto ingresado: $ ${amount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                    <p>Tipo de cambio: C$ ${rate.toLocaleString('es-NI', {minimumFractionDigits: 4, maximumFractionDigits: 4})} = $1 USD</p>
                    <p>Equivalente en NIO: C$ ${convertedAmount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                `;
          }
        } else {
          convertedAmount = amount;
          const currencySymbol = paymentCurrency === 'USD' ? '$' : 'C$';
          summaryHTML = `
                <p>Monto a pagar: ${currencySymbol} ${amount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
            `;
        }

        const percentage = (convertedAmount / invoiceAmount) * 100;
        summaryHTML += `
            <p class="mt-2 font-semibold ${percentage >= 100 ? 'text-green-600' : ''}">
                Este pago representa el ${percentage.toFixed(2)}% del saldo pendiente 
                (${invoiceCurrency === 'USD' ? '$' : 'C$'}${invoiceAmount.toLocaleString('es-NI', {minimumFractionDigits: 2, maximumFractionDigits: 2})} ${invoiceCurrency})
            </p>
        `;

        const summaryElement = document.querySelector('#conversion-summary');
        if (summaryElement) {
          summaryElement.innerHTML = summaryHTML;
        }
      }

      // Event listeners
      if (amountInput) {
        amountInput.addEventListener('input', updateConversionSummary);
      }

      if (currencySelect) {
        currencySelect.addEventListener('change', updateExchangeRateVisibility);
      }

      if (exchangeRateInput) {
        exchangeRateInput.addEventListener('input', updateConversionSummary);
      }

      // Inicializar el estado
      updateExchangeRateVisibility();
    });
  </script>

</x-app-layout>
