/**
 * Business Manager — products.js
 * Product list, Add/Edit form, HPP Calculator
 */

// ── HPP Calculator State ─────────────────────────────────────────────────────
let _hppRows = [];   // [{ name, cost, qty, unit }]
let _hppOpen = false;

function prodToggleHPP() {
    _hppOpen = !_hppOpen;
    document.getElementById('hpp-calc-panel').classList.toggle('open', _hppOpen);
    document.getElementById('hpp-toggle-lbl').textContent = _hppOpen ? 'Tutup HPP Calculator' : 'Buka HPP Calculator';
    if (_hppOpen && !_hppRows.length) hppAddRow();
}
function hppAddRow() {
    _hppRows.push({ name: '', cost: 0, qty: 1, unit: '' });
    hppRenderRows();
}
function hppRemoveRow(i) { _hppRows.splice(i, 1); hppRenderRows(); }
function hppUpdateRow(i, field, val) {
    if (_hppRows[i]) { _hppRows[i][field] = field === 'name' || field === 'unit' ? val : parseFloat(val) || 0; }
    hppCalcTotal();
}
function hppRenderRows() {
    const el = document.getElementById('hpp-ingredients-list');
    if (!el) return;
    el.innerHTML = _hppRows.map((r, i) => `
        <div class="biz-hpp-row">
            <input class="biz-input" style="font-size:12px;padding:6px 10px" placeholder="Nama bahan" value="${_esc(r.name)}"
                   oninput="hppUpdateRow(${i},'name',this.value)">
            <input class="biz-input" style="font-size:12px;padding:6px 10px;width:90px" type="number" min="0"
                   placeholder="Biaya" value="${r.cost || ''}" oninput="hppUpdateRow(${i},'cost',this.value)">
            <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppRemoveRow(${i})" style="padding:4px 8px">
                <i class="fas fa-xmark"></i>
            </button>
        </div>`).join('');
    hppCalcTotal();
}
function hppCalcTotal() {
    const total = _hppRows.reduce((s, r) => s + (parseFloat(r.cost) || 0), 0);
    const el = document.getElementById('hpp-calc-total');
    if (el) el.textContent = bizRpFull(total);
    return total;
}
function hppApplyToProduct() {
    const total = hppCalcTotal();
    const inp = document.getElementById('prod-hpp');
    if (inp) { inp.value = total; prodUpdateMarginPreview(); }
    bizToast('HPP diterapkan ke produk', 'i');
}
function prodUpdateMarginPreview() {
    const price = parseFloat(document.getElementById('prod-price')?.value) || 0;
    const hpp = parseFloat(document.getElementById('prod-hpp')?.value) || 0;
    const prev = document.getElementById('prod-margin-preview');
    const pct = document.getElementById('prod-margin-pct');
    const pro = document.getElementById('prod-margin-profit');
    if (!prev) return;
    if (price > 0) {
        const margin = ((price - hpp) / price * 100);
        const profitUnit = price - hpp;
        prev.style.display = 'block';
        pct.textContent = margin.toFixed(1) + '%';
        pro.textContent = bizRpFull(profitUnit) + ' / unit';
        pct.style.color = margin >= 20 ? 'var(--biz-success)' : margin >= 10 ? 'var(--biz-warning)' : 'var(--biz-danger)';
    } else {
        prev.style.display = 'none';
    }
}
function prodTypeChange() {
    const type = document.getElementById('prod-type')?.value;
    const wrap = document.getElementById('prod-stock-wrap');
    if (wrap) wrap.style.display = type === 'physical' ? 'flex' : 'none';
}

window.prodToggleHPP = prodToggleHPP; window.hppAddRow = hppAddRow;
window.hppRemoveRow = hppRemoveRow; window.hppUpdateRow = hppUpdateRow;
window.hppApplyToProduct = hppApplyToProduct; window.hppCalcTotal = hppCalcTotal;
window.prodUpdateMarginPreview = prodUpdateMarginPreview; window.prodTypeChange = prodTypeChange;

// ── Open Add Product Form ────────────────────────────────────────────────────
function bizOpenAddProduct() {
    document.getElementById('prod-id').value = '';
    document.getElementById('prod-name').value = '';
    document.getElementById('prod-price').value = '';
    document.getElementById('prod-hpp').value = '';
    document.getElementById('prod-stock').value = '0';
    document.getElementById('prod-low-alert').value = '5';
    document.getElementById('prod-type').value = 'physical';
    document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-box"></i> Tambah Produk';
    _hppRows = []; hppRenderRows();
    _hppOpen = false;
    document.getElementById('hpp-calc-panel').classList.remove('open');
    document.getElementById('hpp-toggle-lbl').textContent = 'Buka HPP Calculator';
    document.getElementById('prod-margin-preview').style.display = 'none';
    prodTypeChange();
    bizOpenModal('biz-modal-product');
    setTimeout(() => document.getElementById('prod-name')?.focus(), 150);
}
function bizOpenEditProduct(product) {
    document.getElementById('prod-id').value = product.id;
    document.getElementById('prod-name').value = product.name;
    document.getElementById('prod-price').value = product.price_sell;
    document.getElementById('prod-hpp').value = product.hpp || 0;
    document.getElementById('prod-stock').value = product.stock || 0;
    document.getElementById('prod-low-alert').value = product.low_stock_alert || 5;
    document.getElementById('prod-type').value = product.type || 'physical';
    document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-pencil"></i> Edit Produk';
    prodTypeChange(); prodUpdateMarginPreview();
    // Load ingredients
    BizDB.ingredients.getAll().then(ings => {
        _hppRows = ings.filter(r => r.product_id === product.id).map(r => ({ name: r.name, cost: r.cost, qty: r.quantity || 1, unit: r.unit || '' }));
        hppRenderRows();
    });
    bizOpenModal('biz-modal-product');
}
window.bizOpenAddProduct = bizOpenAddProduct;
window.bizOpenEditProduct = bizOpenEditProduct;

// ── Save Product ─────────────────────────────────────────────────────────────
async function bizSaveProduct() {
    const id = document.getElementById('prod-id').value.trim();
    const name = document.getElementById('prod-name').value.trim();
    const price = parseFloat(document.getElementById('prod-price').value) || 0;
    const hpp = parseFloat(document.getElementById('prod-hpp').value) || 0;
    const type = document.getElementById('prod-type').value;
    if (!name) { bizToast('Nama produk wajib diisi', 'w'); return; }
    if (price <= 0) { bizToast('Harga jual harus > 0', 'w'); return; }

    const bizId = window.bizState.businessId;
    const now = new Date().toISOString();
    const prodId = id || bizUUID();

    const prodDoc = {
        id: prodId,
        business_id: bizId,
        name,
        price_sell: price,
        hpp,
        type,
        stock: type === 'physical' ? (parseInt(document.getElementById('prod-stock').value) || 0) : null,
        low_stock_alert: parseInt(document.getElementById('prod-low-alert').value) || 5,
        is_active: true,
        created_at: id ? undefined : now,
        updated_at: now,
    };
    if (!id) prodDoc.created_at = now;

    await BizDB.products.save(prodDoc);

    // Save ingredients
    if (_hppRows.length) {
        // Remove old ingredients for this product
        const existing = await BizDB.ingredients.getAll();
        for (const old of existing.filter(r => r.product_id === prodId)) await BizDB.ingredients.delete(old.id);
        for (const row of _hppRows.filter(r => r.name)) {
            await BizDB.ingredients.save({ id: bizUUID(), product_id: prodId, name: row.name, cost: row.cost, quantity: row.qty || 1, unit: row.unit || '', created_at: now });
        }
    }

    await bizPreloadProducts();
    bizToast(`✅ Produk "${name}" disimpan`, 's');
    bizCloseModal('biz-modal-product');
    if (window.bizState.activeTab === 'products') await bizLoadProducts();
}
window.bizSaveProduct = bizSaveProduct;

// ── Products List Page ────────────────────────────────────────────────────────
async function bizLoadProducts() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;
    document.getElementById('biz-page-title')?.setAttribute('textContent', 'Produk');

    const products = await BizDB.products.getAll();
    const active = products.filter(p => p.is_active !== false);

    container.innerHTML = `<div class="biz-page">
        <div style="display:flex;gap:10px;margin-bottom:14px;align-items:center">
            <div class="biz-search-bar" style="flex:1;margin:0">
                <i class="fas fa-search"></i>
                <input type="text" id="prod-search" placeholder="Cari produk..." oninput="prodFilter(this.value)">
            </div>
            <button class="biz-btn biz-btn-primary biz-btn-sm" onclick="bizOpenAddProduct()">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>

        <div class="biz-chips" style="margin-bottom:14px">
            <span class="biz-chip active" onclick="prodSetFilter(this,'all')">Semua</span>
            <span class="biz-chip" onclick="prodSetFilter(this,'physical')">Fisik</span>
            <span class="biz-chip" onclick="prodSetFilter(this,'digital')">Digital</span>
            <span class="biz-chip" onclick="prodSetFilter(this,'service')">Jasa</span>
        </div>

        <div id="prod-grid" class="biz-product-grid"></div>
    </div>`;

    window._productsData = active;
    window._prodTypeFilter = 'all';
    prodFilter('');
}

function prodSetFilter(el, type) {
    document.querySelectorAll('#biz-app-container .biz-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    window._prodTypeFilter = type;
    prodFilter(document.getElementById('prod-search')?.value || '');
}
function prodFilter(q) {
    const grid = document.getElementById('prod-grid');
    if (!grid) return;
    let list = window._productsData || [];
    if (window._prodTypeFilter && window._prodTypeFilter !== 'all') list = list.filter(p => p.type === window._prodTypeFilter);
    if (q) list = list.filter(p => p.name.toLowerCase().includes(q.toLowerCase()));
    if (!list.length) { grid.innerHTML = '<div class="biz-empty" style="grid-column:1/-1"><i class="fas fa-box-open"></i><br>Belum ada produk</div>'; return; }

    grid.innerHTML = list.map(p => {
        const margin = p.price_sell > 0 ? ((p.price_sell - (p.hpp || 0)) / p.price_sell * 100).toFixed(0) : 0;
        let stockBadge = '';
        if (p.type === 'physical') {
            const cls = p.stock <= 0 ? 'out' : p.stock <= (p.low_stock_alert || 5) ? 'low' : 'ok';
            const ico = cls === 'out' ? 'fa-circle-xmark' : cls === 'low' ? 'fa-circle-exclamation' : 'fa-circle-check';
            stockBadge = `<div class="biz-stock-badge ${cls}"><i class="fas ${ico}"></i> Stok: ${p.stock}</div>`;
        }
        return `<div class="biz-product-card" onclick="bizOpenEditProduct(${JSON.stringify(p).replace(/"/g, '&quot;')})">
            <div class="biz-product-icon"><i class="fas ${p.type === 'service' ? 'fa-handshake' : p.type === 'digital' ? 'fa-file-code' : 'fa-box'}"></i></div>
            <div class="biz-product-name">${_esc(p.name)}</div>
            <div class="biz-product-price">${bizRp(p.price_sell)}</div>
            <div class="biz-product-meta">HPP: ${bizRp(p.hpp || 0)} · Margin: ${margin}%</div>
            ${stockBadge}
        </div>`;
    }).join('');
}

window.bizLoadProducts = bizLoadProducts;
window.prodFilter = prodFilter;
window.prodSetFilter = prodSetFilter;
