@php
  $defaultCompany = \App\Models\Company::where('default', true)->first();
@endphp
<nav x-data="{
    open: false,
    companiesOpen: false,
    categoriesOpen: false,
    clientsOpen: false,
    productsOpen: false,
    invoicesOpen: false,
    paymentsOpen: false
}" class="border-b border-gray-100 bg-white">
  <!-- Primary Navigation Menu -->
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex h-16 justify-between">
      <div class="flex">
        <!-- Logo -->
        <div class="flex shrink-0 items-center">
          <a href="{{ route('dashboard') }}">
            @if ($defaultCompany && $defaultCompany->logo_path)
              <img src="{{ asset('storage/' . $defaultCompany->logo_path) }}" alt="{{ $defaultCompany->name }}"
                class="block h-9 w-auto">
            @else
              <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            @endif
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden items-center space-x-8 sm:-my-px sm:ms-10 sm:flex">
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
          </x-nav-link>

          <!-- Dropdown de Compañía -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('companies.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Compañía') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('companies.index')" class="w-full">
                {{ __('Listar Compañías') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('companies.create')" class="w-full">
                {{ __('Crear Compañía') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>

          <!-- Dropdown de Categorías -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('categories.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Categorías') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('categories.index')" class="w-full">
                {{ __('Listar Categorías') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('categories.create')" class="w-full">
                {{ __('Crear Categoría') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>

          <!-- Dropdown de Clientes -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('clients.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Clientes') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('clients.index')" class="w-full">
                {{ __('Listar Clientes') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('clients.create')" class="w-full">
                {{ __('Crear Cliente') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>

          <!-- Dropdown de Productos -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('products.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Productos') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('products.index')" class="w-full">
                {{ __('Listar Productos') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('products.create')" class="w-full">
                {{ __('Crear Producto') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>

          <!-- Dropdown de Facturas -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('invoices.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Facturas') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('invoices.index')" class="w-full">
                {{ __('Listar Facturas') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('invoices.create')" class="w-full">
                {{ __('Crear Factura') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>

          <!-- Dropdown de Pagos -->
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button
                class="{{ request()->routeIs('payments.*') ? 'text-gray-900 border-indigo-400' : 'text-gray-500 hover:text-gray-700' }} inline-flex items-center rounded-md border border-transparent px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out">
                <div>{{ __('Pagos') }}</div>
                <div class="ms-1">
                  <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('payments.index')" class="w-full">
                {{ __('Listar Pagos') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('payments.create')" class="w-full">
                {{ __('Crear Pago') }}
              </x-dropdown-link>
            </x-slot>
          </x-dropdown>
        </div>

      </div>

      <!-- Settings Dropdown -->
      <div class="hidden sm:ms-6 sm:flex sm:items-center">
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button
              class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
              <div>{{ Auth::user()->name }}</div>

              <div class="ms-1">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </div>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link :href="route('profile.edit')">
              {{ __('Profile') }}
            </x-dropdown-link>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
              @csrf

              <x-dropdown-link :href="route('logout')"
                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                {{ __('Log Out') }}
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>

      <!-- Hamburger -->
      <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open"
          class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Responsive Navigation Menu -->
  <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
    <div class="space-y-1 pb-3 pt-2">
      <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
      </x-responsive-nav-link>

      <!-- Responsive Companies Links -->
      <div class="relative">
        <button @click="companiesOpen = !companiesOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Compañías') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': companiesOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="companiesOpen" class="pl-4">
          <x-responsive-nav-link :href="route('companies.index')">{{ __('Listar Compañías') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('companies.create')">{{ __('Crear Compañía') }}</x-responsive-nav-link>
        </div>
      </div>

      <!-- Responsive Categories Links -->
      <div class="relative">
        <button @click="categoriesOpen = !categoriesOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Categorías') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': categoriesOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="categoriesOpen" class="pl-4">
          <x-responsive-nav-link :href="route('categories.index')">{{ __('Listar Categorías') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('categories.create')">{{ __('Crear Categoría') }}</x-responsive-nav-link>
        </div>
      </div>

      <!-- Responsive Clients Links -->
      <div class="relative">
        <button @click="clientsOpen = !clientsOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Clientes') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': clientsOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="clientsOpen" class="pl-4">
          <x-responsive-nav-link :href="route('clients.index')">{{ __('Listar Clientes') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('clients.create')">{{ __('Crear Cliente') }}</x-responsive-nav-link>
        </div>
      </div>

      <!-- Responsive Products Links -->
      <div class="relative">
        <button @click="productsOpen = !productsOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Productos') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': productsOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="productsOpen" class="pl-4">
          <x-responsive-nav-link :href="route('products.index')">{{ __('Listar Productos') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('products.create')">{{ __('Crear Producto') }}</x-responsive-nav-link>
        </div>
      </div>

      <!-- Responsive Invoices Links -->
      <div class="relative">
        <button @click="invoicesOpen = !invoicesOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Facturas') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': invoicesOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="invoicesOpen" class="pl-4">
          <x-responsive-nav-link :href="route('invoices.index')">{{ __('Listar Facturas') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('invoices.create')">{{ __('Crear Factura') }}</x-responsive-nav-link>
        </div>
      </div>

      <!-- Responsive Payments Links -->
      <div class="relative">
        <button @click="paymentsOpen = !paymentsOpen"
          class="flex w-full items-center justify-between px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-800">
          <span>{{ __('Pagos') }}</span>
          <svg class="h-5 w-5 transform" :class="{ 'rotate-180': paymentsOpen }" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="paymentsOpen" class="pl-4">
          <x-responsive-nav-link :href="route('payments.index')">{{ __('Listar Pagos') }}</x-responsive-nav-link>
          <x-responsive-nav-link :href="route('payments.create')">{{ __('Crear Pago') }}</x-responsive-nav-link>
        </div>
      </div>
    </div>

    <!-- Responsive Settings Options -->
    <div class="border-t border-gray-200 pb-1 pt-4">
      <div class="px-4">
        <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
        <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
      </div>

      <div class="mt-3 space-y-1">
        <x-responsive-nav-link :href="route('profile.edit')">
          {{ __('Profile') }}
        </x-responsive-nav-link>

        <!-- Authentication -->
        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <x-responsive-nav-link :href="route('logout')"
            onclick="event.preventDefault();
                                        this.closest('form').submit();">
            {{ __('Log Out') }}
          </x-responsive-nav-link>
        </form>
      </div>
    </div>
  </div>
</nav>
