<!-- ── Sidebar (Desktop) ──────────────────────────────────────── -->
<nav class="biz-sidebar" id="biz-sidebar" role="navigation" aria-label="Business Manager Navigation">

  <!-- Brand -->
  <a href="/" class="biz-sidebar-brand" aria-label="Kembali ke Cuan Capital">
    <div class="biz-sidebar-brand-icon">
      <i class="fas fa-chart-line"></i>
    </div>
    <div>
      <div class="biz-sidebar-brand-name">Cuan Capital</div>
      <div class="biz-sidebar-brand-sub">Business Manager</div>
    </div>
  </a>

  <!-- Nav -->
  <div class="biz-sidebar-nav">
    <!-- Group: Dashboard -->
    <div class="biz-nav-group">Workspace</div>
    <button class="biz-sidebar-link active" data-tab="dashboard" onclick="bizSwitchTab('dashboard',this)">
      <i class="fas fa-house"></i> Dashboard
    </button>

    <!-- Group: Operations -->
    <div class="biz-nav-group">Operations</div>
    <button class="biz-sidebar-link" data-tab="sales" onclick="bizSwitchTab('sales',this)">
      <i class="fas fa-receipt"></i> Penjualan
    </button>
    <button class="biz-sidebar-link" data-tab="products" onclick="bizSwitchTab('products',this)">
      <i class="fas fa-box"></i> Produk
    </button>
    <button class="biz-sidebar-link" data-tab="inventory" onclick="bizSwitchTab('inventory',this)">
      <i class="fas fa-warehouse"></i> Inventaris
    </button>

    <!-- Group: Finance -->
    <div class="biz-nav-group">Finance</div>
    <button class="biz-sidebar-link" data-tab="finance" onclick="bizSwitchTab('finance',this)">
      <i class="fas fa-wallet"></i> Keuangan
    </button>

    <!-- Group: Analytics -->
    <div class="biz-nav-group">Analytics</div>
    <button class="biz-sidebar-link" data-tab="reports" onclick="bizSwitchTab('reports',this)">
      <i class="fas fa-chart-pie"></i> Laporan
    </button>

  </div>

</nav>
