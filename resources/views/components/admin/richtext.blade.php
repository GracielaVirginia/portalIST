@props([
  'name' => 'contenido_html',
  'label' => 'Contenido',
  'value' => '',
  'placeholder' => 'Describe la promoción…',
  'height' => '260px',
  'toolbar' => 'basic',   // basic | full
])
<div class="mb-5">
  <label class="block font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $label }}</label>

  <div data-richtext
       data-input="textarea"
       data-placeholder="{{ $placeholder }}"
       data-height="{{ $height }}"
       data-toolbar="{{ $toolbar }}"
       class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
    <div data-editor class="px-3 py-2"></div>
    {{-- Campo real (HTML) que viaja en el form --}}
    <textarea name="{{ $name }}" class="hidden">{{ old($name, $value) }}</textarea>
  </div>
</div>
