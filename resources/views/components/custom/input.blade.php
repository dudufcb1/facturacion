@props([
    'name' => 'username',
    'id' => 'username',
    'placeholder' => 'Escribe...',
    'domain' => '',
    'label' => 'Username',
    'required' => false,
    'maxlength' => 255,
    'minlength' => 3,
])

<div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
  <div class="sm:col-span-4">
    <label for="{{ $id }}" class="block text-sm/6 font-medium text-gray-900">
      {{ $label }}
      @if ($required)
        <span class="text-red-500">*</span>
      @endif
    </label>

    <div class="mt-2">
      <div
        class="{{ $errors->has($name) ? 'ring-red-500' : 'ring-gray-300' }} flex rounded-md shadow-sm ring-1 ring-inset focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
        <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">{{ $domain }}</span>
        <input
          type="text"
          name="{{ $name }}"
          id="{{ $id }}"
          autocomplete="username"
          class="{{ $errors->has($name) ? 'text-red-900' : '' }} block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm/6"
          placeholder="{{ $placeholder }}"
          value="{{ old($name) }}"
          @if ($required) required @endif
          maxlength="{{ $maxlength }}"
          minlength="{{ $minlength }}"
          {{ $attributes }}>
      </div>

      @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
      @enderror

      @if ($maxlength)
        <p class="mt-1 text-sm text-gray-500">
          <span class="js-char-count-{{ $id }}">{{ strlen(old($name, '')) }}</span>/{{ $maxlength }}
          caracteres
        </p>
      @endif
    </div>

    @if ($attributes->has('help'))
      <p class="mt-2 text-sm text-gray-500">{{ $attributes->get('help') }}</p>
    @endif
  </div>
</div>

@once
  @push('scripts')
    <script>
      document.getElementById('{{ $id }}').addEventListener('input', function(e) {
        const charCount = e.target.value.length;
        document.querySelector('.js-char-count-{{ $id }}').textContent = charCount;
      });
    </script>
  @endpush
@endonce
