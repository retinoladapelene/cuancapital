{{--
    Accordion Component
    Usage:
      <x-accordion title="Pertanyaan Satu">
          Jawaban di sini...
      </x-accordion>

      <x-accordion title="Pertanyaan Dua" :open="true">
          Terbuka secara default.
      </x-accordion>

    Props:
      title  : string  (required) — button label
      open   : bool    (default: false) — expanded by default
      icon   : string  (default: 'fas fa-chevron-down') — custom icon class
--}}
@props([
    'title' => 'Section',
    'open'  => false,
    'icon'  => 'fas fa-chevron-down',
])

<div class="m-accordion">
<div class="m-accordion">
    <button type="button"
            class="m-accordion-trigger"
            onclick="
                const content = this.nextElementSibling;
                const isOpen = content.dataset.state === 'open';
                content.dataset.state = isOpen ? 'closed' : 'open';
                this.setAttribute('aria-expanded', !isOpen);
            "
            aria-expanded="{{ $open ? 'true' : 'false' }}">
        <span>{{ $title }}</span>
        <i class="{{ $icon }}"></i>
    </button>
    <div class="m-accordion-content" data-state="{{ $open ? 'open' : 'closed' }}" role="region">
        <div class="px-4 py-2">
            {{ $slot }}
        </div>
    </div>
</div>
</div>
