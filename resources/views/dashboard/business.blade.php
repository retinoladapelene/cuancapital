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
    <link rel="stylesheet" href="{{ asset('assets/css/business-core.css') }}?v=1.9">
    <link rel="stylesheet" href="{{ asset('assets/css/business-modules.css') }}?v=1.9">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a0e1a">
</head>
<body>

<!-- ══════════════════════════════════════════════════════════════
     APP SHELL
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-shell">

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
      <button class="biz-sidebar-link active" data-tab="dashboard" onclick="bizSwitchTab('dashboard',this)">
        <i class="fas fa-house"></i> Dashboard
      </button>
      <button class="biz-sidebar-link" data-tab="sales" onclick="bizSwitchTab('sales',this)">
        <i class="fas fa-receipt"></i> Penjualan
      </button>
      <button class="biz-sidebar-link" data-tab="products" onclick="bizSwitchTab('products',this)">
        <i class="fas fa-box"></i> Produk
      </button>
      <button class="biz-sidebar-link" data-tab="inventory" onclick="bizSwitchTab('inventory',this)">
        <i class="fas fa-warehouse"></i> Inventaris
      </button>
      <button class="biz-sidebar-link" data-tab="finance" onclick="bizSwitchTab('finance',this)">
        <i class="fas fa-wallet"></i> Keuangan
      </button>
      <button class="biz-sidebar-link" data-tab="reports" onclick="bizSwitchTab('reports',this)">
        <i class="fas fa-chart-pie"></i> Analytics
      </button>
    </div>

    <!-- Footer actions -->
    <div class="biz-sidebar-footer">
      <button class="biz-sidebar-link" onclick="bizOpenModal('biz-modal-backup')">
        <i class="fas fa-cloud-arrow-down"></i> Backup Data
      </button>
      <button class="biz-sidebar-link" onclick="bizToggleTheme()">
        <i class="fas fa-circle-half-stroke"></i> Tema
      </button>
      <a href="/cashbook" class="biz-sidebar-link">
        <i class="fas fa-arrow-left"></i> Cashbook Pribadi
      </a>
    </div>

  </nav><!-- /sidebar -->

  <!-- ── Main Content Area ─────────────────────────────────────── -->
  <main class="biz-main">

    <!-- Top Nav Bar (Desktop) -->
    <header class="biz-topnav">
      <div class="biz-topnav-left">
        <h1 class="biz-page-title" id="biz-page-title">Dashboard</h1>
      </div>
      <div class="biz-nav-right">
        <button class="biz-icon-btn" title="Tema" onclick="bizToggleTheme()">
          <i class="fas fa-circle-half-stroke"></i>
        </button>
        <button class="biz-icon-btn" title="Menu" onclick="bizToggleNavMenu('biz-topnav-menu')">
          <i class="fas fa-ellipsis"></i>
        </button>
        <div class="biz-nav-dropdown" id="biz-topnav-menu">
          <button class="biz-nav-dd-btn" onclick="bizOpenModal('biz-modal-backup');bizCloseNavMenus()">
            <i class="fas fa-cloud-arrow-down"></i> Backup Data
          </button>
          <button class="biz-nav-dd-btn" onclick="bizOpenModal('biz-modal-business');bizCloseNavMenus()">
            <i class="fas fa-store"></i> Info Bisnis
          </button>
          <a href="/cashbook" class="biz-nav-dd-btn" style="text-decoration:none">
            <i class="fas fa-arrow-left"></i> Personal Cashbook
          </a>
        </div>
      </div>
    </header>

    <!-- Page Container (module content injected here) -->
    <div id="biz-app-container" class="biz-page">
      <div class="biz-loading">
        <i class="fas fa-spinner fa-spin"></i> Memuat Business Manager...
      </div>
    </div>

  </main><!-- /main -->

</div><!-- /shell -->

<!-- ══════════════════════════════════════════════════════════════
     MOBILE BOTTOM NAVIGATION (5 Tabs)
     ══════════════════════════════════════════════════════════════ -->
<nav class="biz-mob-nav" id="biz-mob-nav" role="navigation" aria-label="Mobile Navigation">
  <button id="biz-mob-dashboard" class="biz-nav-btn active" onclick="bizSwitchTab('dashboard',this)" aria-label="Dashboard">
    <i class="fas fa-house"></i><span>Home</span>
  </button>
  <button id="biz-mob-sales" class="biz-nav-btn" onclick="bizSwitchTab('sales',this)">
    <i class="fas fa-receipt"></i><span>Sales</span>
  </button>
  <button id="biz-mob-products" class="biz-nav-btn" onclick="bizSwitchTab('products',this)">
    <i class="fas fa-box"></i><span>Products</span>
  </button>
  <button id="biz-mob-reports" class="biz-nav-btn" onclick="bizSwitchTab('reports',this)">
    <i class="fas fa-chart-pie"></i><span>Analytics</span>
  </button>
  <button id="biz-mob-more" class="biz-nav-btn" onclick="bizOpenModal('biz-modal-more')">
    <i class="fas fa-bars"></i><span>More</span>
  </button>
</nav>

<!-- ══════════════════════════════════════════════════════════════
     GLOBAL FLOATING ACTION BUTTON (FAB)
     ══════════════════════════════════════════════════════════════ -->
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

<!-- ══════════════════════════════════════════════════════════════
     TOAST
     ══════════════════════════════════════════════════════════════ -->
<div id="biz-toast" role="status" aria-live="polite"></div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: QUICK SALE (Bottom Sheet)
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-quick-sale" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="quick-sale-title">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="quick-sale-title"><i class="fas fa-bolt" style="color:var(--biz-warning)"></i> Quick Sale</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-quick-sale')" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
    </div>

    <!-- Top-5 quick product chips (populated by JS) -->
    <div id="qs-quick-chips" class="biz-quick-products"></div>

    <!-- Product search -->
    <div class="biz-input-group biz-pos-search-wrap">
      <i class="fas fa-search biz-pos-search-icon"></i>
      <input type="text" id="qs-search" class="biz-input biz-pos-search" placeholder="Cari produk..." autocomplete="off"
             oninput="qsOnSearch(this.value)">
      <div id="qs-dropdown" class="biz-product-dropdown" style="display:none"></div>
    </div>

    <!-- Selected product + qty -->
    <div id="qs-selected" style="display:none">
      <div class="biz-card biz-card-sm" style="margin-bottom:14px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
          <div>
            <div class="biz-list-name" id="qs-prod-name">—</div>
            <div class="biz-list-sub" id="qs-prod-price">Rp 0</div>
          </div>
          <button class="biz-cart-item-remove" onclick="qsClear()"><i class="fas fa-xmark"></i></button>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
          <label class="biz-label" style="margin:0">Qty</label>
          <div class="biz-stepper">
            <button class="biz-stepper-btn" onclick="qsChangeQty(-1)">−</button>
            <input type="number" id="qs-qty" class="biz-stepper-val" value="1" min="1" oninput="qsUpdateTotal()">
            <button class="biz-stepper-btn" onclick="qsChangeQty(1)">+</button>
          </div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:12px;font-size:13px">
          <span style="color:var(--biz-text-muted);font-weight:600">Total</span>
          <span id="qs-total" style="font-size:17px;font-weight:800;color:var(--biz-primary)">Rp 0</span>
        </div>
      </div>
    </div>

    <!-- Payment method -->
    <div class="biz-input-group" id="qs-pay-wrap" style="display:none">
      <label class="biz-label">Metode Pembayaran</label>
      <div class="biz-payment-chips">
        <span class="biz-pay-chip active" data-pay="cash" onclick="qsSetPay(this,'cash')">💵 Cash</span>
        <span class="biz-pay-chip" data-pay="transfer" onclick="qsSetPay(this,'transfer')">🏦 Transfer</span>
        <span class="biz-pay-chip" data-pay="qris" onclick="qsSetPay(this,'qris')">📱 QRIS</span>
      </div>
    </div>

    <!-- Repeat last sale shortcut -->
    <div id="qs-repeat-wrap" style="display:none;margin-top:12px;text-align:center">
      <button class="biz-detail-toggle" style="margin:0 auto" onclick="qsRepeatLast()">
        <i class="fas fa-rotate-right"></i> Ulangi penjualan terakhir
      </button>
    </div>

    <!-- Floating Footer -->
    <div class="biz-modal-footer" id="qs-footer" style="display:none">
      <button id="qs-save-btn" class="biz-btn biz-btn-primary biz-btn-block" onclick="qsSave()" style="font-size:15px;padding:14px">
        <i class="fas fa-check"></i> Simpan Penjualan
      </button>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: POS CART (Full multi-item)
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-pos-cart" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="pos-cart-title" style="max-width:520px">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="pos-cart-title"><i class="fas fa-cart-shopping" style="color:var(--biz-primary)"></i> Tambah Penjualan</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-pos-cart')" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
    </div>

    <!-- Quick product chips -->
    <div id="pos-quick-chips" class="biz-quick-products"></div>

    <!-- Search -->
    <div class="biz-input-group biz-pos-search-wrap">
      <i class="fas fa-search biz-pos-search-icon"></i>
      <input type="text" id="pos-search" class="biz-input biz-pos-search" placeholder="Cari & tambah produk..."
             autocomplete="off" oninput="posOnSearch(this.value)">
      <div id="pos-dropdown" class="biz-product-dropdown" style="display:none"></div>
    </div>

    <!-- Cart -->
    <div id="pos-cart-items" class="biz-cart-items"></div>
    <div id="pos-empty-cart" class="biz-empty" style="padding:24px">
      <i class="fas fa-cart-shopping"></i> Tap produk untuk tambahkan
    </div>

    <!-- Totals -->
    <div id="pos-totals" style="display:none">
      <div class="biz-cart-total">
        <div class="biz-cart-total-row">
          <span style="color:var(--biz-text-muted)">Subtotal</span>
          <span id="pos-subtotal" class="biz-cart-total-val">Rp 0</span>
        </div>
        <div class="biz-cart-total-row profit-row">
          <span style="color:var(--biz-text-muted)">Est. Profit</span>
          <span id="pos-profit" class="biz-cart-total-val">Rp 0</span>
        </div>
        <div class="biz-cart-total-row total-row">
          <span>Total</span>
          <span id="pos-total"></span>
        </div>
      </div>

      <!-- Payment method -->
      <div class="biz-input-group">
        <label class="biz-label">Metode Pembayaran</label>
        <div class="biz-payment-chips">
          <span class="biz-pay-chip active" data-pay="cash" onclick="posSetPay(this,'cash')">💵 Cash</span>
          <span class="biz-pay-chip" data-pay="transfer" onclick="posSetPay(this,'transfer')">🏦 Transfer</span>
          <span class="biz-pay-chip" data-pay="qris" onclick="posSetPay(this,'qris')">📱 QRIS</span>
        </div>
      </div>
    </div> <!-- /pos-totals -->

    <!-- Floating Footer -->
    <div class="biz-modal-footer" id="pos-footer" style="display:none">
      <button class="biz-btn biz-btn-success biz-btn-block" onclick="posSave()" id="pos-save-btn" style="font-size:15px;padding:14px">
        <i class="fas fa-check-circle"></i> Selesaikan Penjualan
      </button>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: ADD/EDIT PRODUCT
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-product" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="product-modal-title"><i class="fas fa-box"></i> Tambah Produk</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-product')"><i class="fas fa-xmark"></i></button>
    </div>

    <input type="hidden" id="prod-id">

    <div class="biz-input-group">
      <label class="biz-label" for="prod-name">Nama Produk *</label>
      <input id="prod-name" type="text" class="biz-input" placeholder="e.g. Bakso Frozen">
    </div>

    <div class="biz-row">
      <div class="biz-input-group">
        <label class="biz-label" for="prod-price">Harga Jual *</label>
        <input id="prod-price" type="number" class="biz-input" placeholder="0" min="0" oninput="prodUpdateMarginPreview()">
      </div>
      <div class="biz-input-group">
        <label class="biz-label" for="prod-hpp">HPP</label>
        <input id="prod-hpp" type="number" class="biz-input" placeholder="0" min="0" oninput="prodUpdateMarginPreview()">
      </div>
    </div>

    <div class="biz-input-group">
      <label class="biz-label" for="prod-type">Tipe Produk</label>
      <select id="prod-type" class="biz-input" onchange="prodTypeChange()">
        <option value="physical">Fisik (ada stok)</option>
        <option value="digital">Digital (tanpa stok)</option>
        <option value="service">Jasa (tanpa stok)</option>
      </select>
    </div>

    <div id="prod-stock-wrap" class="biz-row">
      <div class="biz-input-group">
        <label class="biz-label" for="prod-stock">Stok Awal</label>
        <input id="prod-stock" type="number" class="biz-input" placeholder="0" min="0">
      </div>
      <div class="biz-input-group">
        <label class="biz-label" for="prod-low-alert">Alert Stok Minimum</label>
        <input id="prod-low-alert" type="number" class="biz-input" value="5" min="0">
      </div>
    </div>

    <!-- Margin preview -->
    <div id="prod-margin-preview" class="biz-margin-display" style="display:none">
      <div style="font-size:11px;color:var(--biz-success);font-weight:700;margin-bottom:3px">Profit Margin</div>
      <div class="biz-margin-pct" id="prod-margin-pct">0%</div>
      <div style="font-size:11px;color:var(--biz-text-muted);margin-top:2px" id="prod-margin-profit">Rp 0 / unit</div>
    </div>

    <!-- HPP Calculator toggle -->
    <button class="biz-detail-toggle" id="hpp-calc-toggle" onclick="prodToggleHPP()">
      <i class="fas fa-calculator"></i> <span id="hpp-toggle-lbl">Buka HPP Calculator</span>
    </button>

    <div id="hpp-calc-panel" class="biz-detail-panel">
      <div style="font-size:12px;font-weight:700;color:var(--biz-text-muted);margin-bottom:10px">Komponen HPP</div>
      <div id="hpp-ingredients-list"></div>
      <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppAddRow()" style="margin-top:8px">
        <i class="fas fa-plus"></i> Tambah Komponen
      </button>
      <div class="biz-hpp-total-row">
        <span>Total HPP</span>
        <span id="hpp-calc-total" style="color:var(--biz-warning)">Rp 0</span>
      </div>
      <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppApplyToProduct()" style="width:100%;margin-top:8px">
        <i class="fas fa-arrow-up"></i> Terapkan ke HPP Produk
      </button>
    </div>

    <div style="height:14px"></div>
    <button class="biz-btn biz-btn-primary biz-btn-block" onclick="bizSaveProduct()">
      <i class="fas fa-check"></i> Simpan Produk
    </button>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: ADD EXPENSE
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-expense" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="expense-modal-title">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="expense-modal-title"><i class="fas fa-arrow-trend-down" style="color:var(--biz-danger)"></i> Tambah Pengeluaran</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-expense')"><i class="fas fa-xmark"></i></button>
    </div>

    <input type="hidden" id="exp-id">

    <div class="biz-input-group">
      <label class="biz-label" for="exp-amount">Jumlah *</label>
      <input id="exp-amount" type="number" class="biz-input" placeholder="0" min="0" style="font-size:20px;font-weight:700">
    </div>

    <div class="biz-input-group">
      <label class="biz-label" for="exp-cat">Kategori</label>
      <select id="exp-cat" class="biz-input"></select>
    </div>

    <div class="biz-row">
      <div class="biz-input-group">
        <label class="biz-label" for="exp-date">Tanggal</label>
        <input id="exp-date" type="date" class="biz-input">
      </div>
    </div>

    <div class="biz-input-group">
      <label class="biz-label" for="exp-notes">Keterangan</label>
      <input id="exp-notes" type="text" class="biz-input" placeholder="Opsional">
    </div>

    <button class="biz-btn biz-btn-danger biz-btn-block" onclick="bizSaveExpense()">
      <i class="fas fa-check"></i> Simpan Pengeluaran
    </button>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: BACKUP / RESTORE
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-backup" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="backup-modal-title">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="backup-modal-title"><i class="fas fa-shield-halved" style="color:var(--biz-success)"></i> Backup & Restore</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-backup')"><i class="fas fa-xmark"></i></button>
    </div>

    <div class="biz-insight-box" style="margin-bottom:20px">
      <div class="biz-insight-header"><i class="fas fa-circle-info"></i> Data Kamu 100% Aman di Perangkat</div>
      <ul class="biz-insight-list">
        <li>Semua data bisnis disimpan hanya di browser ini.</li>
        <li>Kami tidak menyimpan data ke server.</li>
        <li>Backup secara berkala agar data tidak hilang.</li>
      </ul>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px">
      <button class="biz-btn biz-btn-primary biz-btn-block" onclick="bizExportData()">
        <i class="fas fa-cloud-arrow-down"></i> Export Backup (JSON)
      </button>
      <label class="biz-btn biz-btn-ghost biz-btn-block" style="cursor:pointer;text-align:center">
        <i class="fas fa-cloud-arrow-up"></i> Import Backup
        <input type="file" accept=".json" id="biz-import-file" style="display:none" onchange="bizImportData(this)">
      </label>
    </div>

    <div id="backup-last" style="margin-top:16px;font-size:11px;color:var(--biz-text-muted);text-align:center"></div>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: BUSINESS INFO
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-business" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="biz-info-title">

    <div class="biz-modal-header">
      <span class="biz-modal-title" id="biz-info-title"><i class="fas fa-store"></i> Info Bisnis</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-business')"><i class="fas fa-xmark"></i></button>
    </div>

    <div class="biz-input-group">
      <label class="biz-label" for="biz-info-name">Nama Bisnis</label>
      <input id="biz-info-name" type="text" class="biz-input" placeholder="e.g. Toko Bakso Pak Budi">
    </div>
    <div class="biz-input-group">
      <label class="biz-label" for="biz-info-type">Jenis Bisnis</label>
      <select id="biz-info-type" class="biz-input">
        <option value="retail">Retail / Toko</option>
        <option value="food">Makanan & Minuman</option>
        <option value="service">Jasa</option>
        <option value="digital">Produk Digital</option>
        <option value="reseller">Reseller / Dropship</option>
        <option value="online">Online Seller</option>
        <option value="other">Lainnya</option>
      </select>
    </div>
    <button class="biz-btn biz-btn-primary biz-btn-block" onclick="bizSaveBusinessInfo()">
      <i class="fas fa-check"></i> Simpan
    </button>

  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: MORE MENU (Mobile Only)
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-more" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" role="dialog" aria-modal="true" aria-labelledby="more-title">
    <div class="biz-modal-header">
      <span class="biz-modal-title" id="more-title"><i class="fas fa-bars"></i> Menu Lainnya</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-more')"><i class="fas fa-xmark"></i></button>
    </div>
    
    <div style="display:flex;flex-direction:column;gap:10px">
      <button class="biz-btn biz-btn-ghost biz-btn-block" style="justify-content:flex-start" onclick="bizCloseModal('biz-modal-more');bizOpenModal('biz-modal-business')">
        <i class="fas fa-store" style="width:24px"></i> Info Bisnis
      </button>
      <button class="biz-btn biz-btn-ghost biz-btn-block" style="justify-content:flex-start" onclick="bizCloseModal('biz-modal-more');bizOpenModal('biz-modal-backup')">
        <i class="fas fa-shield-halved" style="width:24px;color:var(--biz-success)"></i> Backup Data (JSON)
      </button>
      <button class="biz-btn biz-btn-ghost biz-btn-block" style="justify-content:flex-start" onclick="bizToggleTheme()">
        <i class="fas fa-circle-half-stroke" style="width:24px"></i> Ganti Tema
      </button>
      <div class="biz-divider"></div>
      <a href="/cashbook" class="biz-btn biz-btn-ghost biz-btn-block" style="justify-content:flex-start;text-decoration:none">
        <i class="fas fa-arrow-left" style="width:24px;color:var(--biz-primary)"></i> Kembali ke Cashbook
      </a>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: CONFIRM
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-confirm">
  <div class="biz-modal" role="alertdialog" aria-modal="true" style="max-width:380px">
    <div class="biz-modal-header">
      <span class="biz-modal-title" id="biz-confirm-title">Konfirmasi</span>
    </div>
    <p id="biz-confirm-msg" style="font-size:14px;color:var(--biz-text-muted);margin-bottom:20px;line-height:1.5"></p>
    <div class="biz-row">
      <button class="biz-btn biz-btn-ghost" onclick="bizCloseModal('biz-modal-confirm')">Batal</button>
      <button id="biz-confirm-ok" class="biz-btn biz-btn-danger">Ya, Lanjutkan</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     MODAL: RESTOCK
     ══════════════════════════════════════════════════════════════ -->
<div class="biz-modal-bg" id="biz-modal-restock" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-modal" style="max-width:380px" role="dialog" aria-modal="true">
    <div class="biz-modal-header">
      <span class="biz-modal-title"><i class="fas fa-boxes-stacking"></i> Tambah Stok</span>
      <button class="biz-modal-close" onclick="bizCloseModal('biz-modal-restock')"><i class="fas fa-xmark"></i></button>
    </div>
    <input type="hidden" id="restock-product-id">
    <div style="font-size:13px;color:var(--biz-text-muted);margin-bottom:16px" id="restock-product-name"></div>
    <div class="biz-input-group">
      <label class="biz-label">Jumlah Tambah Stok</label>
      <input id="restock-qty" type="number" class="biz-input" placeholder="0" min="1" style="font-size:20px;font-weight:700">
    </div>
    <div class="biz-input-group">
      <label class="biz-label" for="restock-notes">Keterangan</label>
      <input id="restock-notes" type="text" class="biz-input" placeholder="e.g. Restock dari supplier">
    </div>
    <button class="biz-btn biz-btn-success biz-btn-block" onclick="bizSaveRestock()">
      <i class="fas fa-plus"></i> Tambah Stok
    </button>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════════
     SCRIPTS
     ══════════════════════════════════════════════════════════════ -->
<script>
  // Theme init before paint to avoid flash
  (function(){
    const t = localStorage.getItem('biz_theme') || localStorage.getItem('cb_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
  })();
</script>

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Core modules (loaded in dependency order) -->
<script src="{{ asset('assets/js/business/db.js') }}?v=1.9"          defer></script>
<script src="{{ asset('assets/js/business/ui.js') }}?v=1.9"          defer></script>
<script src="{{ asset('assets/js/business/backup.js') }}?v=1.9"      defer></script>
<script src="{{ asset('assets/js/business/dashboard.js') }}?v=1.9"   defer></script>
<script src="{{ asset('assets/js/business/sales.js') }}?v=1.9"       defer></script>
<script src="{{ asset('assets/js/business/products.js') }}?v=1.9"    defer></script>
<script src="{{ asset('assets/js/business/inventory.js') }}?v=1.9"   defer></script>
<script src="{{ asset('assets/js/business/finance.js') }}?v=1.9"     defer></script>
<script src="{{ asset('assets/js/business/reports.js') }}?v=1.9"     defer></script>
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
