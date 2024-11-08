@php $defaultCompany = \App\Models\Company::where('default', true)->first(); @endphp
<nav class="border-b border-gray-100 bg-white">
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
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"> {{ __('Dashboard') }} </x-nav-link>

          <x-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')"> {{ __('Compañías') }} </x-nav-link>

          <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')"> {{ __('Categorías') }} </x-nav-link>

          <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"> {{ __('Clientes') }} </x-nav-link>

          <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')"> {{ __('Productos') }} </x-nav-link>

          <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')"> {{ __('Facturas') }} </x-nav-link>

          <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')"> {{ __('Pagos') }} </x-nav-link>
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
            <x-dropdown-link :href="route('profile.edit')"> {{ __('Profile') }} </x-dropdown-link>
            <!-- Autenticación -->
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Log Out') }} </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>
    </div>
  </div>
</nav>
