# Modern Mobile-First Responsive System v2.0

Sistem desain responsif yang telah direnovasi untuk memberikan pengalaman mobile yang lebih baik dengan integrasi Tailwind CSS yang lebih dalam.

## 🎯 Fitur Utama

- **Mobile-First Approach**: Semua komponen dirancang untuk mobile terlebih dahulu
- **Tailwind Integration**: Menggunakan `@apply` directive untuk konsistensi
- **Enhanced Accessibility**: Dukungan focus-visible dan reduced motion
- **Safe Area Support**: Kompatibel dengan device modern (iPhone X+)
- **Dark Mode Ready**: Otomatis mendukung tema gelap
- **Performance Optimized**: CSS yang lebih efisien dan ringan

## 📱 Container System

```html
<!-- Standard container -->
<div class="m-container">Content</div>

<!-- Compact container -->
<div class="m-container-compact">Content</div>

<!-- Full-width container -->
<div class="m-container-full">Content</div>
```

## 🎨 Card System

```html
<!-- Basic card -->
<div class="m-card">
  <div class="m-card-header">Header</div>
  <div class="m-card-body">Content</div>
  <div class="m-card-footer">Footer</div>
</div>

<!-- Variants -->
<div class="m-card m-card-compact">Compact</div>
<div class="m-card m-card-large">Large</div>
<div class="m-card m-card-glass">Glass effect</div>
<div class="m-card m-card-interactive">Hover effects</div>
```

## 📐 Grid System

```html
<!-- Auto-responsive grid -->
<div class="m-grid m-grid-auto">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>

<!-- Manual grids -->
<div class="m-grid m-grid-2">2 columns</div>
<div class="m-grid m-grid-3">3 columns</div>

<!-- Sidebar layout -->
<div class="m-grid m-grid-sidebar">
  <main class="m-grid-sidebar-main">Main content</main>
  <aside class="m-grid-sidebar-aside">Sidebar</aside>
</div>
```

## 🔤 Typography

```html
<h1 class="m-heading-1">Large Heading</h1>
<h2 class="m-heading-2">Medium Heading</h2>
<h3 class="m-heading-3">Small Heading</h3>
<p class="m-body">Body text</p>
<span class="m-caption">Caption text</span>
```

## 🎛️ Buttons

```html
<button class="m-btn m-btn-primary">Primary</button>
<button class="m-btn m-btn-secondary">Secondary</button>
<button class="m-btn m-btn-ghost">Ghost</button>
```

## 📱 Navigation

```html
<!-- Mobile navigation -->
<nav class="m-mobile-nav">
  <a href="#" class="m-nav-item active">
    <i class="icon">🏠</i>
    <span>Home</span>
  </a>
  <!-- More items -->
</nav>

<!-- Sticky CTA -->
<div class="m-sticky-cta">
  <button class="m-btn m-btn-primary">Call to Action</button>
</div>
```

## 🪗 Accordion

```html
<div class="m-accordion">
  <button class="m-accordion-trigger" aria-expanded="false">
    <span>Question</span>
    <i class="fas fa-chevron-down"></i>
  </button>
  <div class="m-accordion-content" data-state="closed">
    <div class="px-4 py-2">Answer content</div>
  </div>
</div>
```

## 🛠️ Utility Classes

```html
<!-- Mobile visibility -->
<div class="m-hide-mobile">Hidden on mobile</div>
<div class="m-show-mobile">Shown on mobile</div>

<!-- Stacking order -->
<div class="m-stack-1">First on mobile</div>
<div class="m-stack-2">Second on mobile</div>

<!-- Horizontal scroll -->
<div class="m-scroll-x">
  <div>Scrollable item</div>
  <!-- More items -->
</div>

<!-- Safe areas -->
<div class="m-safe-top">Safe top area</div>
<div class="m-safe-bottom">Safe bottom area</div>

<!-- Aspect ratios -->
<div class="m-aspect-square">Square</div>
<div class="m-aspect-video">Video</div>
<div class="m-aspect-card">Card ratio</div>
```

## 🎨 Breakpoints

- `sm:` 640px+
- `md:` 768px+
- `lg:` 1024px+
- `xl:` 1280px+

## 🌙 Dark Mode

Semua komponen secara otomatis mendukung dark mode dengan class `dark:` dari Tailwind.

## ♿ Accessibility

- Minimum touch targets: 44px
- Focus management dengan `focus-visible`
- ARIA attributes yang tepat
- Reduced motion support
- High contrast support

## 📄 Print Styles

Otomatis menyembunyikan elemen mobile saat print.

## 🔄 Migration Guide

### Dari v1.0 ke v2.0

| Old Class | New Class | Notes |
|-----------|-----------|-------|
| `m-card-sm` | `m-card-compact` | Updated naming |
| `m-acc-btn` | `m-accordion-trigger` | New structure |
| `m-acc-body` | `m-accordion-content` | Data attributes |
| `m-bottom-nav` | `m-mobile-nav` | Simplified |
| `m-priority-*` | `m-stack-*` | Better naming |
| `m-hide-on-mobile` | `m-hide-mobile` | Shorter |
| `m-show-on-mobile` | `m-show-mobile` | Shorter |

### Komponen Updates

- **Card Component**: Update `variant="sm"` ke `variant="compact"`
- **Accordion Component**: Struktur HTML baru dengan data-state

## 🚀 Performance

- CSS yang lebih kecil dengan Tailwind utilities
- Reduced specificity conflicts
- Better caching dengan utility-first approach
- Optimized for modern browsers

## 📚 Best Practices

1. **Gunakan semantic HTML** dengan class utility
2. **Test pada device nyata** untuk touch interactions
3. **Gunakan container classes** untuk consistent spacing
4. **Leverage Tailwind utilities** sebelum custom CSS
5. **Test accessibility** dengan keyboard navigation

---

*Built with ❤️ for modern web experiences*</content>
<parameter name="filePath">c:\Asset Jualan\Web Cashflow Engine\cuan-workflow\docs\mobile-system-v2.md