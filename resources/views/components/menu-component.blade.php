<div class="mb-6">
  <h2 class="my-4 text-xl font-semibold leading-tight text-gray-800">
    {{ $title }}
  </h2>
  <div class="flex space-x-4">
    <a href="{{ route($routeIndex) }}" class="rounded-md bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">
      {{ __('Listar') }}
    </a>
    <a href="{{ route($routeCreate) }}"
      class="flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
      <!-- SVG Icon -->
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
        class="mr-2 fill-current text-white">
        <path d="M24 10h-10v-10h-4v10h-10v4h10v10h4v-10h10z" />
      </svg>
      {{ __('' . $title) }}
    </a>

  </div>
</div>
