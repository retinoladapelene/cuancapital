/**
 * Business Manager — sales.js
 * POS Cart (multi-item) + Quick Sale modal (single-item fast mode)
 */

// ── Quick Sale State ─────────────────────────────────────────────────────────
let _qsProduct = null;
let _qsPayMethod = 'cash';

function qsOnSearch(val) {
    const dd = document.getElementById('qs-dropdown');
    if (!dd) return;
    if (!val.trim()) { dd.style.display = 'none'; return; }

    const results = bizSearchProducts(val);
    if (!results.length) { dd.innerHTML = '<div class="biz-product-option"><div class="biz-product-option-name">Tidak ditemukan</div></div>'; dd.style.display = 'block'; return; }

    dd.innerHTML = results.map(p => {
        const isOut = p.type === 'physical' && (p.stock || 0) <= 0;
        return `<div class="biz-product-option ${isOut ? 'disabled' : ''}" onclick="qsSelectProduct('${p.id}')">
            <div>
                <div class="biz-product-option-name">${_esc(p.name)}${isOut ? ' <span style="color:var(--biz-danger)">(Habis)</span>' : ''}</div>
                <div class="biz-product-option-meta">${p.type === 'physical' ? `Stok: ${p.stock || 0}` : p.type}</div>
            </div>
            <div class="biz-product-option-price">${bizRp(p.price_sell)}</div>
        </div>`;
    }).join('');
    dd.style.display = 'block';
}

function qsSelectProduct(productId) {
    const p = window.bizState.productCache.find(pr => pr.id === productId);
    if (!p) return;
    _qsProduct = p;

    document.getElementById('qs-search').value = '';
    document.getElementById('qs-dropdown').style.display = 'none';
    document.getElementById('qs-prod-name').textContent = p.name;
    document.getElementById('qs-prod-price').textContent = bizRpFull(p.price_sell) + ' / unit';
    document.getElementById('qs-qty').value = 1;
    document.getElementById('qs-selected').style.display = 'block';
    document.getElementById('qs-pay-wrap').style.display = 'block';
    document.getElementById('qs-save-btn').style.display = 'block';
    qsUpdateTotal();
}
window.qsSelectProduct = qsSelectProduct;

function qsOnSearch_init() {
    // Render top-5 quick chips
    const el = document.getElementById('qs-quick-chips');
    if (!el) return;
    const chips = (window.bizState.quickProducts || window.bizState.productCache.slice(0, 5));
    el.innerHTML = chips.map(p =>
        `<span class="biz-quick-product-chip" onclick="qsSelectProduct('${p.id}')">${_esc(p.name)}</span>`
    ).join('');

    // Check repeat last
    const last = localStorage.getItem('biz_last_sale');
    const rw = document.getElementById('qs-repeat-wrap');
    if (last && rw) rw.style.display = 'block';
}

function qsChangeQty(delta) {
    const inp = document.getElementById('qs-qty');
    if (!inp) return;
    inp.value = Math.max(1, (parseInt(inp.value) || 1) + delta);
    qsUpdateTotal();
}
function qsUpdateTotal() {
    if (!_qsProduct) return;
    const qty = parseInt(document.getElementById('qs-qty')?.value) || 1;
    const total = (_qsProduct.price_sell || 0) * qty;
    const el = document.getElementById('qs-total');
    if (el) el.textContent = bizRpFull(total);
}
function qsSetPay(el, method) {
    _qsPayMethod = method;
    document.querySelectorAll('#biz-modal-quick-sale .biz-pay-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
}
function qsClear() {
    _qsProduct = null;
    document.getElementById('qs-selected').style.display = 'none';
    document.getElementById('qs-pay-wrap').style.display = 'none';
    document.getElementById('qs-save-btn').style.display = 'none';
    document.getElementById('qs-search').value = '';
}
async function qsSave() {
    if (!_qsProduct) return;
    const qty = parseInt(document.getElementById('qs-qty')?.value) || 1;
    const btn = document.getElementById('qs-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    try {
        await bizCreateSale({
            businessId: window.bizState.businessId,
            cartItems: [{ product_id: _qsProduct.id, product_name: _qsProduct.name, qty, price: _qsProduct.price_sell, hpp: _qsProduct.hpp || 0 }],
            paymentMethod: _qsPayMethod,
        });
        bizToast(`✅ Terjual: ${_qsProduct.name} ×${qty}`, 's');
        bizCloseModal('biz-modal-quick-sale');
        _qsReset();
        // Refresh cache & dashboard if visible
        await bizPreloadProducts();
        if (window.bizState.activeTab === 'dashboard') await bizLoadDashboard();
        else if (window.bizState.activeTab === 'sales') await bizLoadSales();
    } catch (e) {
        bizToast(e.message || 'Gagal menyimpan', 'e');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Simpan Penjualan';
    }
}
function _qsReset() {
    _qsProduct = null;
    const ids = ['qs-selected', 'qs-pay-wrap', 'qs-save-btn'];
    ids.forEach(id => { const el = document.getElementById(id); if (el) el.style.display = 'none'; });
    const si = document.getElementById('qs-search');
    if (si) si.value = '';
}
async function qsRepeatLast() {
    const last = JSON.parse(localStorage.getItem('biz_last_sale') || 'null');
    if (!last || !last.items?.length) return;
    const btn = document.getElementById('qs-save-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
    try {
        await bizCreateSale({ businessId: window.bizState.businessId, cartItems: last.items, paymentMethod: last.payment_method || 'cash' });
        bizToast('✅ Penjualan terakhir diulang', 's');
        bizCloseModal('biz-modal-quick-sale');
        if (window.bizState.activeTab === 'dashboard') await bizLoadDashboard();
    } catch (e) {
        bizToast(e.message || 'Gagal', 'e');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Simpan Penjualan'; }
    }
}

window.qsOnSearch = qsOnSearch; window.qsChangeQty = qsChangeQty;
window.qsUpdateTotal = qsUpdateTotal; window.qsSetPay = qsSetPay;
window.qsClear = qsClear; window.qsSave = qsSave; window.qsRepeatLast = qsRepeatLast;

// Hook modal open to init chips
document.addEventListener('click', e => {
    if (e.target.closest('#biz-modal-quick-sale') || e.target.classList.contains('fab')) {
        setTimeout(qsOnSearch_init, 80);
    }
}, { passive: true });

// ── POS Cart State ───────────────────────────────────────────────────────────
let _posCart = [];
let _posPayMethod = 'cash';

function posOnSearch(val) {
    const dd = document.getElementById('pos-dropdown');
    if (!dd) return;
    if (!val.trim()) { dd.style.display = 'none'; return; }
    const results = bizSearchProducts(val);
    if (!results.length) { dd.innerHTML = '<div class="biz-product-option"><div class="biz-product-option-name">Tidak ditemukan</div></div>'; dd.style.display = 'block'; return; }
    dd.innerHTML = results.map(p => `
        <div class="biz-product-option" onclick="posAddProduct('${p.id}')">
            <div>
                <div class="biz-product-option-name">${_esc(p.name)}</div>
                <div class="biz-product-option-meta">${p.type === 'physical' ? `Stok: ${p.stock || 0}` : p.type}</div>
            </div>
            <div class="biz-product-option-price">${bizRp(p.price_sell)}</div>
        </div>`).join('');
    dd.style.display = 'block';
}
function posAddProduct(id) {
    const p = window.bizState.productCache.find(pr => pr.id === id);
    if (!p) return;
    document.getElementById('pos-search').value = '';
    document.getElementById('pos-dropdown').style.display = 'none';
    const existing = _posCart.find(ci => ci.product_id === id);
    if (existing) { existing.qty++; }
    else { _posCart.push({ product_id: p.id, product_name: p.name, qty: 1, price: p.price_sell, hpp: p.hpp || 0 }); }
    posRenderCart();
}
function posChangeQty(idx, delta) {
    if (!_posCart[idx]) return;
    _posCart[idx].qty = Math.max(1, _posCart[idx].qty + delta);
    posRenderCart();
}
function posRemoveItem(idx) { _posCart.splice(idx, 1); posRenderCart(); }
function posSetPay(el, method) {
    _posPayMethod = method;
    document.querySelectorAll('#biz-modal-pos-cart .biz-pay-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
}
function posRenderCart() {
    const cEl = document.getElementById('pos-cart-items');
    const eEl = document.getElementById('pos-empty-cart');
    const tEl = document.getElementById('pos-totals');
    if (!cEl) return;

    document.getElementById('pos-quick-chips').innerHTML =
        (window.bizState.quickProducts || []).slice(0, 5).map(p =>
            `<span class="biz-quick-product-chip" onclick="posAddProduct('${p.id}')">${_esc(p.name)}</span>`).join('');

    if (!_posCart.length) {
        cEl.innerHTML = ''; eEl.style.display = 'block'; tEl.style.display = 'none'; return;
    }
    eEl.style.display = 'none'; tEl.style.display = 'block';

    let total = 0, profit = 0;
    cEl.innerHTML = _posCart.map((item, i) => {
        const sub = item.price * item.qty;
        const pr = (item.price - item.hpp) * item.qty;
        total += sub; profit += pr;
        return `<div class="biz-cart-item">
            <div style="flex:1">
                <div class="biz-cart-item-name">${_esc(item.product_name)}</div>
                <div style="font-size:11px;color:var(--biz-text-muted)">${bizRp(item.price)} × ${item.qty} = <strong>${bizRp(sub)}</strong></div>
            </div>
            <div class="biz-stepper">
                <button class="biz-stepper-btn" onclick="posChangeQty(${i},-1)">−</button>
                <span class="biz-stepper-val">${item.qty}</span>
                <button class="biz-stepper-btn" onclick="posChangeQty(${i},1)">+</button>
            </div>
            <button class="biz-cart-item-remove" onclick="posRemoveItem(${i})"><i class="fas fa-xmark"></i></button>
        </div>`;
    }).join('');

    document.getElementById('pos-subtotal').textContent = bizRpFull(total);
    document.getElementById('pos-profit').textContent = bizRpFull(profit);
    document.getElementById('pos-total').textContent = bizRpFull(total);
}
async function posSave() {
    if (!_posCart.length) { bizToast('Keranjang kosong', 'w'); return; }
    const btn = document.getElementById('pos-save-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    try {
        const sale = await bizCreateSale({ businessId: window.bizState.businessId, cartItems: _posCart, paymentMethod: _posPayMethod });
        bizToast(`✅ ${_posCart.length} produk terjual — ${bizRp(sale.total_amount)}`, 's');
        bizCloseModal('biz-modal-pos-cart');
        _posCart = []; posRenderCart();
        await bizPreloadProducts();
        if (window.bizState.activeTab === 'dashboard') await bizLoadDashboard();
        else await bizLoadSales();
    } catch (e) {
        bizToast(e.message || 'Gagal', 'e');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Selesaikan Penjualan';
    }
}
window.posOnSearch = posOnSearch; window.posAddProduct = posAddProduct;
window.posChangeQty = posChangeQty; window.posRemoveItem = posRemoveItem;
window.posSetPay = posSetPay; window.posSave = posSave;

// ── Sales List ───────────────────────────────────────────────────────────────
async function bizLoadSales() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;
    document.getElementById('biz-page-title')?.setAttribute('textContent', 'Penjualan');

    const [sales, items] = await Promise.all([BizDB.sales.getAll(), BizDB.saleItems.getAll()]);
    const sorted = sales.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    container.innerHTML = `<div class="biz-page">
        <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
            <button class="biz-btn biz-btn-primary" onclick="bizOpenModal('biz-modal-pos-cart');posRenderCart()">
                <i class="fas fa-cart-shopping"></i> POS Cart
            </button>
            <button class="biz-btn biz-btn-ghost" onclick="bizOpenModal('biz-modal-quick-sale')">
                <i class="fas fa-bolt"></i> Quick Sale
            </button>
        </div>

        <div class="biz-search-bar" style="margin-bottom:10px">
            <i class="fas fa-search"></i>
            <input type="text" id="sales-search" placeholder="Cari penjualan..." oninput="salesFilter(this.value)">
        </div>

        <div class="biz-summary-strip" id="sales-strip">
            <div class="biz-strip-item"><div class="biz-strip-label">Total</div><div class="biz-strip-value" id="s-total-rev">Rp 0</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Profit</div><div class="biz-strip-value" id="s-total-profit" style="color:var(--biz-success)">Rp 0</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Transaksi</div><div class="biz-strip-value" id="s-count">0</div></div>
        </div>

        <div id="sales-list"></div>
    </div>`;

    // Attach sales data for filtering
    window._salesData = sorted;
    salesFilter('');
}

function salesFilter(q) {
    const list = document.getElementById('sales-list');
    if (!list) return;
    const all = window._salesData || [];
    const filt = q ? all.filter(s => s.id.toLowerCase().includes(q.toLowerCase())) : all;
    const totalRev = filt.reduce((s, r) => s + (r.total_amount || 0), 0);
    const totalPro = filt.reduce((s, r) => s + (r.total_profit || 0), 0);

    const tr = document.getElementById('s-total-rev');
    const tp = document.getElementById('s-total-profit');
    const sc = document.getElementById('s-count');
    if (tr) tr.textContent = bizRp(totalRev);
    if (tp) tp.textContent = bizRp(totalPro);
    if (sc) sc.textContent = filt.length;

    if (!filt.length) { list.innerHTML = '<div class="biz-empty"><i class="fas fa-receipt"></i><br>Belum ada penjualan</div>'; return; }

    // Group by date
    const groups = {};
    filt.forEach(s => { if (!groups[s.sale_date]) groups[s.sale_date] = []; groups[s.sale_date].push(s); });

    list.innerHTML = Object.entries(groups).map(([date, sls]) => {
        const dayRev = sls.reduce((a, s) => a + (s.total_amount || 0), 0);
        return `<div class="biz-date-header">${_fmtDate(date)} · ${bizRp(dayRev)}</div>` +
            sls.map(s => `<div class="biz-sale-item">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <div style="font-size:12px;font-weight:700;color:var(--biz-primary)">#${s.id.slice(-6).toUpperCase()}</div>
                        <div class="biz-sale-meta">${_timeAgo(s.created_at)} · ${_payLabel(s.payment_method)}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="biz-sale-amount">${bizRp(s.total_amount)}</div>
                        <div class="biz-sale-profit">+${bizRp(s.total_profit)}</div>
                    </div>
                </div>
            </div>`).join('');
    }).join('');
}

function _fmtDate(d) {
    if (!d) return '—';
    const dt = new Date(d + 'T00:00:00');
    return dt.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}

window.bizLoadSales = bizLoadSales;
window.salesFilter = salesFilter;
