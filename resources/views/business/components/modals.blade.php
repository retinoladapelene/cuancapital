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

    <!-- Payment method & Customer -->
    <div id="qs-pay-wrap" style="display:none">
      <div class="biz-input-group" style="margin-bottom:16px;">
        <label class="biz-label" for="qs-customer-name">Nama Pelanggan (Opsional)</label>
        <input type="text" id="qs-customer-name" class="biz-input" placeholder="e.g. Budi / Guest">
      </div>

      <div class="biz-input-group">
        <label class="biz-label">Metode Pembayaran</label>
        <div class="biz-payment-chips">
          <span class="biz-pay-chip active" data-pay="cash" onclick="qsSetPay(this,'cash')"><i class="fas fa-money-bill-wave"></i> Cash</span>
          <span class="biz-pay-chip" data-pay="transfer" onclick="qsSetPay(this,'transfer')"><i class="fas fa-building-columns"></i> Transfer</span>
          <span class="biz-pay-chip" data-pay="qris" onclick="qsSetPay(this,'qris')"><i class="fas fa-qrcode"></i> QRIS</span>
        </div>
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

      <!-- Customer & Payment method -->
      <div class="biz-input-group" style="margin-bottom:16px;">
        <label class="biz-label" for="pos-customer-name">Nama Pelanggan (Opsional)</label>
        <input type="text" id="pos-customer-name" class="biz-input" placeholder="e.g. Siti / Guest">
      </div>

      <div class="biz-input-group">
        <label class="biz-label">Metode Pembayaran</label>
        <div class="biz-payment-chips">
          <span class="biz-pay-chip active" data-pay="cash" onclick="posSetPay(this,'cash')"><i class="fas fa-money-bill-wave"></i> Cash</span>
          <span class="biz-pay-chip" data-pay="transfer" onclick="posSetPay(this,'transfer')"><i class="fas fa-building-columns"></i> Transfer</span>
          <span class="biz-pay-chip" data-pay="qris" onclick="posSetPay(this,'qris')"><i class="fas fa-qrcode"></i> QRIS</span>
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

    <!-- Image Upload -->
    <div class="biz-input-group" style="display:flex; gap:16px; align-items:center;">
        <div id="prod-image-preview" style="width:60px;height:60px;border-radius:12px;background:var(--biz-surface-2);border:1px dashed var(--biz-border);display:flex;align-items:center;justify-content:center;color:var(--biz-text-muted);overflow:hidden;flex-shrink:0;background-size:cover;background-position:center;">
            <i class="fas fa-camera"></i>
        </div>
        <div style="flex:1">
            <label class="biz-label" for="prod-image">Foto Produk (Opsional)</label>
            <input id="prod-image" type="file" accept="image/*" class="biz-input" style="padding:8px" onchange="prodHandleImageUpload(this)">
            <input type="hidden" id="prod-image-base64">
        </div>
    </div>

    <div class="biz-input-group">
      <label class="biz-label" for="prod-category">Kategori</label>
      <select id="prod-category" class="biz-input">
        <option value="">-- Pilih Kategori --</option>
        <option value="Makanan">🍔 Makanan</option>
        <option value="Minuman">🥤 Minuman</option>
        <option value="Fashion">👗 Fashion</option>
        <option value="Elektronik">💻 Elektronik</option>
        <option value="Kecantikan">💄 Kecantikan</option>
        <option value="Kesehatan">💊 Kesehatan</option>
        <option value="Digital">📱 Digital / Online</option>
        <option value="Jasa">🛠️ Jasa / Servis</option>
        <option value="Lainnya">📦 Lainnya</option>
      </select>
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
      <!-- Section 1: Yield -->
      <div style="margin-bottom:16px;">
        <label class="biz-label" for="hpp-yield">Akan Menghasilkan Berapa Porsi/Unit? (Yield)</label>
        <div style="display:flex;gap:8px;align-items:center;">
            <input id="hpp-yield" type="number" class="biz-input" value="1" min="1" oninput="hppCalcTotal()" style="flex:1">
            <span style="font-size:12px;color:var(--biz-text-muted);width:60px">porsi</span>
        </div>
      </div>
      
      <!-- Section 2: Bahan Baku -->
      <div style="font-size:12px;font-weight:700;color:var(--biz-text-muted);margin-bottom:10px;border-top:1px dashed var(--biz-border);padding-top:10px">Komponen Bahan Baku</div>
      <div id="hpp-ingredients-list"></div>
      <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppAddRow()" style="margin-top:8px">
        <i class="fas fa-plus"></i> Tambah Bahan
      </button>

      <!-- Section 3: Overhead -->
      <div style="margin-top:16px;border-top:1px dashed var(--biz-border);padding-top:10px">
        <label class="biz-label" for="hpp-overhead">Biaya Overhead / Non-Bahan (Per Porsi/Unit)</label>
        <div style="display:flex;gap:8px;align-items:center;">
            <span style="font-size:12px;color:var(--biz-text-muted)">Rp</span>
            <input id="hpp-overhead" type="number" class="biz-input" placeholder="Misal: Biaya Kemasan/Staf" oninput="hppCalcTotal()" style="flex:1">
        </div>
      </div>

      <!-- Section 4: Summary -->
      <div class="biz-hpp-total-wrapper" style="margin-top:20px;padding:12px;background:var(--biz-surface-2);border-radius:8px">
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--biz-text-muted);margin-bottom:6px">
          <span>HPP Bahan (Total / Porsi)</span>
          <span id="hpp-sub-bahan">Rp 0</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--biz-text-muted);margin-bottom:10px">
          <span>HPP Overhead (Per Porsi)</span>
          <span id="hpp-sub-overhead">Rp 0</span>
        </div>
        <div class="biz-hpp-total-row" style="border-top:1px solid var(--biz-border);padding-top:8px;font-size:14px">
          <span style="color:var(--biz-text)">Total Modal HPP / Porsi</span>
          <span id="hpp-calc-total" style="color:var(--biz-warning);font-weight:800">Rp 0</span>
        </div>
      </div>
      
      <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppApplyToProduct()" style="width:100%;margin-top:12px">
        <i class="fas fa-arrow-up"></i> Gunakan HPP Ini
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
