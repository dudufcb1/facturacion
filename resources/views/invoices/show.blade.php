{{-- resources/views/invoices/show.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <x-menu-component title="Factura" routeIndex="invoices.index" routeCreate="invoices.create" />
  </x-slot>
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
            <h2 class="text-xl font-bold text-gray-800">FACTURA</h2>
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
            <span class="font-semibold">{{ $invoice->currency === 'USD' ? '$' : 'C$' }}
              {{ number_format($invoice->subtotal, 2) }}</span>
          </div>
          @if ($invoice->tax > 0)
            <div class="mt-2 flex items-center justify-between">
              <span class="text-gray-600">IVA:</span>
              <span class="font-semibold">{{ $invoice->currency === 'USD' ? '$' : 'C$' }}
                {{ number_format($invoice->tax, 2) }}</span>
            </div>
          @endif
          <div class="mt-2 flex items-center justify-between border-t pt-2">
            <span class="text-lg font-bold">Total:</span>
            <span class="text-lg font-bold text-blue-600">{{ $invoice->currency === 'USD' ? '$' : 'C$' }}
              {{ number_format($invoice->total, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Pagos Realizados --}}
    @if ($invoice->payments->count() > 0)
      <div class="mt-6">
        <h3 class="mb-3 text-lg font-semibold text-gray-700">Pagos Realizados</h3>
        <div class="space-y-2">
          @foreach ($invoice->payments as $payment)
            <a href="{{ route('payments.show', $payment) }}"
              class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-3 transition-colors hover:bg-gray-50">
              <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p class="font-medium">Recibo #{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</p>
                  <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                  </p>
                </div>
              </div>
              <span class="font-semibold">
                {{ $payment->payment_currency === 'USD' ? '$' : 'C$' }}
                {{ number_format($payment->amount_paid, 2) }}
              </span>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Notas y Firmas --}}
    <div class="mt-8">
      <p class="text-sm text-gray-600">
        Elaborar Ck. a nombre de {{ $invoice->company->owner_name }}
      </p>
      <div class="mt-8 grid grid-cols-2 gap-8">
        <div class="text-center">
          <div class="border-t border-gray-300 pt-2">
            <p class="text-sm text-gray-600">Elaborado Por</p>
            <p class="mt-1 font-semibold">{{ auth()->user()->name }}</p>
          </div>
        </div>
        <div class="text-center">
          <div class="border-t border-gray-300 pt-2">
            <p class="text-sm text-gray-600">Cliente</p>
            <p class="mt-1 font-semibold">{{ $invoice->client->name }}</p>
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

      {{-- Botón de Anulación - Solo visible para administradores y propietarios --}}
      @if(auth()->user()->canVoidInvoices() && $invoice->canBeVoided())
        <button onclick="openVoidModal()"
          class="flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-red-600 transition-colors hover:bg-red-100">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Anular Factura
        </button>
      @endif

      {{-- Mostrar estado anulada si aplica --}}
      @if($invoice->isVoided())
        <div class="flex items-center gap-2 rounded-lg bg-red-100 px-4 py-2 text-red-800">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
          <span class="font-bold">FACTURA ANULADA</span>
        </div>
      @endif
    </div>
  </div>

  {{-- Modal de Anulación --}}
  @if(auth()->user()->canVoidInvoices() && $invoice->canBeVoided())
    <div id="voidModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
      <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900">Anular Factura</h3>
        </div>

        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-3">
            ⚠️ <strong>Operación Delicada:</strong> Esta acción anulará permanentemente la factura.
          </p>

          @if($invoice->payments()->where('status', 'completed')->exists())
            <div class="mb-3 rounded-lg bg-yellow-50 p-3 border border-yellow-200">
              <p class="text-sm text-yellow-800">
                <strong>⚠️ Atención:</strong> Esta factura tiene pagos asociados que también serán anulados.
              </p>
            </div>
          @endif

          <label for="voidReason" class="block text-sm font-medium text-gray-700 mb-2">
            Razón de anulación (obligatorio):
          </label>
          <textarea id="voidReason" rows="3"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
            placeholder="Explique detalladamente la razón de la anulación..."></textarea>
          <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres, máximo 500.</p>
        </div>

        <div class="flex gap-3">
          <button onclick="closeVoidModal()"
            class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
          </button>
          <button onclick="confirmVoid()"
            class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
            Anular Factura
          </button>
        </div>
      </div>
    </div>

    <script>
      function openVoidModal() {
        document.getElementById('voidModal').classList.remove('hidden');
        document.getElementById('voidModal').classList.add('flex');
        document.getElementById('voidReason').focus();
      }

      function closeVoidModal() {
        document.getElementById('voidModal').classList.add('hidden');
        document.getElementById('voidModal').classList.remove('flex');
        document.getElementById('voidReason').value = '';
      }

      function confirmVoid() {
        const reason = document.getElementById('voidReason').value.trim();

        if (reason.length < 10) {
          alert('La razón de anulación debe tener al menos 10 caracteres.');
          return;
        }

        if (reason.length > 500) {
          alert('La razón de anulación no puede exceder 500 caracteres.');
          return;
        }

        // Confirmar la acción
        if (!confirm('¿Está seguro de que desea anular esta factura? Esta acción no se puede deshacer.')) {
          return;
        }

        // Enviar solicitud AJAX
        fetch('{{ route("invoices.void", $invoice) }}', {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            void_reason: reason
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Error: ' + (data.error || 'No se pudo anular la factura'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error de conexión. Por favor, intente nuevamente.');
        });
      }

      // Cerrar modal con ESC
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeVoidModal();
        }
      });
    </script>
  @endif
</x-app-layout>
