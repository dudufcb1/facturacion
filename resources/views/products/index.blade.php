<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ __('Productos') }}
      </h2>
      <a href="{{ route('products.create') }}"
        class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
        {{ __('Nuevo Producto') }}
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

      {{-- Buscador --}}
      <div class="mb-6 rounded-lg bg-white p-4 shadow-sm">
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center space-x-4">
          <div class="flex-1">
            <input type="text" name="search"
              value="{{ request('search') }}"
              placeholder="Buscar por nombre, código o categoría..."
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
          </div>
          <div>
            <select name="status"
              class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
              <option value="">Todos los estados</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
          </div>
          <button type="submit"
            class="rounded-md bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">
            {{ __('Buscar') }}
          </button>
          @if (request()->hasAny(['search', 'status']))
            <a href="{{ route('products.index') }}"
              class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
              {{ __('Limpiar') }}
            </a>
          @endif
        </form>
      </div>

      {{-- Tabla de productos --}}
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Código') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Nombre') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Precio') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Stock') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Estado') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Categoría') }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('QR') }}
                </th>

                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                  {{ __('Acciones') }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              @forelse ($products as $product)
                <tr>
                  <td class="whitespace-nowrap px-6 py-4">
                    {{ $product->code }}
                  </td>
                  <td class="px-6 py-4">
                    {{ $product->name }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    {{ $product->currency }} {{ number_format($product->unit_price, 2) }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    {{ $product->stock }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <span
                      class="{{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                      {{ $product->status === 'active' ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    {{ $product->category->name ?? 'Sin categoría' }}
                  </td>
                  <td class="whitespace-nowrap px-6 py-4">
                    <button type="button"
                      onclick="showQR('{{ $product->id }}')"
                      class="text-indigo-600 hover:text-indigo-900">
                      Ver QR
                    </button>
                  </td>

                  <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                    <a href="{{ route('products.edit', $product) }}"
                      class="text-indigo-600 hover:text-indigo-900">
                      {{ __('Editar') }}
                    </a>
                    <form action="{{ route('products.destroy', $product) }}"
                      method="POST"
                      class="ml-2 inline"
                      onsubmit="return confirm('¿Está seguro de que desea eliminar este producto?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900">
                        {{ __('Eliminar') }}
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    {{ __('No se encontraron productos') }}
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginación --}}
        @if ($products->hasPages())
          <div class="border-t border-gray-200 px-4 py-3">
            {{ $products->links() }}
          </div>
        @endif
      </div>
    </div>
    {{-- Modal QR --}}
    <div id="qrModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50">
      <div class="relative top-20 mx-auto w-96 rounded-md border bg-white p-5 shadow-lg">
        <div class="mt-3 text-center">
          <h3 class="text-lg font-medium leading-6 text-gray-900">Código QR del Producto</h3>
          <div class="mt-2 px-7 py-3">
            <div id="qrCode" class="flex justify-center"></div>
            <div id="productInfo" class="mt-3 text-sm text-gray-500"></div>
          </div>
          <div class="items-center space-x-3 px-4 py-3">
            <button type="button" onclick="printQR()"
              class="rounded-md bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
              Imprimir QR
            </button>
            <button type="button" id="closeModal"
              class="rounded-md bg-gray-500 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
  <script>
    // Función para mostrar el modal del QR
    function showQR(productId) {
      fetch(`/products/${productId}/qr`, {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          const svg = atob(data.qr);
          document.getElementById('qrCode').innerHTML = svg;
          document.getElementById('productInfo').innerHTML = data.info;
          document.getElementById('qrModal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al generar el código QR');
        });
    }

    // Función para imprimir el QR
    function printQR() {
      const printWindow = window.open('', '', 'width=600,height=600');
      const qrCode = document.getElementById('qrCode').innerHTML;
      const productInfo = document.getElementById('productInfo').innerHTML;

      printWindow.document.write(`
            <html>
                <head>
                    <title>Imprimir QR</title>
                    <style>
                        body {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            height: 100vh;
                            margin: 0;
                            padding: 20px;
                            box-sizing: border-box;
                        }
                        .qr-container {
                            text-align: center;
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        ${qrCode}
                        ${productInfo}
                    </div>
                </body>
            </html>
        `);

      printWindow.document.close();
      printWindow.focus();

      // Esperar a que el contenido se cargue antes de imprimir
      setTimeout(() => {
        printWindow.print();
        printWindow.close();
      }, 250);
    }

    // Función para cerrar el modal
    function closeQRModal() {
      document.getElementById('qrModal').classList.add('hidden');
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const closeButton = document.getElementById('closeModal');
      if (closeButton) {
        closeButton.addEventListener('click', closeQRModal);
      }

      const modal = document.getElementById('qrModal');
      if (modal) {
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            closeQRModal();
          }
        });
      }
    });
  </script>
</x-app-layout>
