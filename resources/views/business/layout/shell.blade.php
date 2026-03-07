<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Business Manager — kelola bisnis UMKM kamu langsung di perangkat, tanpa server.">
    <title>Business Manager — Cuan Capital</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Business CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/business-core.css') }}?v=2.0">
    <link rel="stylesheet" href="{{ asset('assets/css/business-modules.css') }}?v=1.9">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0e1a">
</head>
<body>

<!-- ══════════════════════════════════════════════════════════════
     APP SHELL (12-Column Grid Desktop)
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-shell">

  @include('business.layout.sidebar')

  <!-- Mobile Top Nav -->
  <div class="biz-mob-topnav">
    <div class="biz-mob-top-brand">
      <i class="fas fa-chart-line" style="color:var(--biz-primary)"></i> Cuan Capital
    </div>
    <button class="biz-mob-top-btn" onclick="bizOpenModal('biz-modal-more')" aria-label="Menu Lainnya">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <main class="biz-main">
    @include('business.layout.topnav')
    
    <div class="biz-content-area" id="biz-content-area">
        @yield('content')
    </div>
  </main>

</div>

@include('business.layout.bottomnav')

<!-- Global FAB -->
<div class="biz-global-fab" id="biz-global-fab">
  <div class="biz-fab-menu" id="biz-fab-menu">
    <button class="biz-fab-item" onclick="bizToggleFabMenu(); bizOpenModal('biz-modal-product'); setTimeout(()=>document.getElementById('prod-name').focus(),200)">
      <span>Produk Baru</span>
      <div class="biz-fab-icon"><i class="fas fa-box"></i></div>
    </button>
    <button class="biz-fab-item" onclick="bizToggleFabMenu(); bizOpenModal('biz-modal-expense')">
      <span>Catat Biaya</span>
      <div class="biz-fab-icon" style="color:var(--biz-danger)"><i class="fas fa-arrow-trend-down"></i></div>
    </button>
    <button class="biz-fab-item" onclick="bizToggleFabMenu(); bizOpenModal('biz-modal-quick-sale')">
      <span>Quick Sale</span>
      <div class="biz-fab-icon" style="color:var(--biz-warning)"><i class="fas fa-bolt"></i></div>
    </button>
  </div>
  <button class="biz-fab-main" onclick="bizToggleFabMenu()">
    <i class="fas fa-plus"></i>
  </button>
</div>

<!-- Toast -->
<div id="biz-toast" role="status" aria-live="polite"></div>

@include('business.components.modals')
@include('business.components.templates')

<!-- Theme init before paint to avoid flash -->
<script>
  (function(){
    const t = localStorage.getItem('biz_theme') || localStorage.getItem('cb_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
  })();
</script>

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Core modules -->
<script src="{{ asset('assets/js/business/db.js') }}?v=1.9"          defer></script>
<script src="{{ asset('assets/js/business/ui.js') }}?v=2.0"          defer></script>
<script src="{{ asset('assets/js/business/backup.js') }}?v=1.9"      defer></script>
<script src="{{ asset('assets/js/business/dashboard.js') }}?v=2.0"   defer></script>
<script src="{{ asset('assets/js/business/sales.js') }}?v=2.0"       defer></script>
<script src="{{ asset('assets/js/business/products.js') }}?v=2.0"    defer></script>
<script src="{{ asset('assets/js/business/inventory.js') }}?v=2.0"   defer></script>
<script src="{{ asset('assets/js/business/finance.js') }}?v=2.0"     defer></script>
<script src="{{ asset('assets/js/business/reports.js') }}?v=2.0"     defer></script>
<script src="{{ asset('assets/js/business/intelligence.js') }}?v=1.9" defer></script>

<!-- Bootstrap -->
<script defer>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.requestIdleCallback) {
      requestIdleCallback(() => bizBootstrap(), { timeout: 1500 });
    } else {
      setTimeout(() => bizBootstrap(), 50);
    }
  });
</script>

<!-- Service Worker Registration -->
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  }
</script>

</body>
</html>
