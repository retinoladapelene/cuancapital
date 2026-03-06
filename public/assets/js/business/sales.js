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
    document.getElementById('qs-footer').style.display = 'block';
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
    document.getElementById('qs-footer').style.display = 'none';
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
    const ids = ['qs-selected', 'qs-pay-wrap', 'qs-footer', 'qs-repeat-wrap'];
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
    const fEl = document.getElementById('pos-footer');
    if (!cEl) return;

    document.getElementById('pos-quick-chips').innerHTML =
        (window.bizState.quickProducts || []).slice(0, 5).map(p =>
            `<span class="biz-quick-product-chip" onclick="posAddProduct('${p.id}')">${_esc(p.name)}</span>`).join('');

    if (!_posCart.length) {
        cEl.innerHTML = ''; eEl.style.display = 'block'; tEl.style.display = 'none';
        if (fEl) fEl.style.display = 'none';
        return;
    }
    eEl.style.display = 'none'; tEl.style.display = 'block';
    if (fEl) fEl.style.display = 'block';

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

// ── Sales Intelligence List (8-Layer Command Center) ───────────────────────
async function bizLoadSales() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i><br><br>Memuat Sales Command Center...</div></div>`;

    const bizId = window.bizState.businessId;
    const salesData = typeof bizSalesIntelligence === 'function' ? await bizSalesIntelligence(bizId) : null;

    window._salesSysData = salesData;

    if (!salesData) {
        container.innerHTML = `<div class="biz-empty"><i class="fas fa-receipt"></i><br>Data penjualan tidak tersedia.</div>`;
        return;
    }

    container.innerHTML = `<div class="biz-page" style="padding-bottom:100px;">
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Sales Command Center</h2>
                <div style="font-size:13px;color:var(--biz-text-dim);font-weight:600;margin-top:2px">Operational Analytics & Revenue Tracking</div>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="bizOpenModal('biz-modal-pos-cart');posRenderCart()"><i class="fas fa-cart-shopping"></i> POS</button>
                <button class="biz-btn biz-btn-primary biz-btn-sm" onclick="bizOpenModal('biz-modal-quick-sale')"><i class="fas fa-bolt"></i> Quick Sale</button>
            </div>
        </div>

        <!-- Layer 1: Overview KPIs -->
        <div id="sales-layer-1" style="margin-bottom:24px;"></div>

        <!-- Layer 2 & 3: Pulse & Alerts (Grid side by side on desktop) -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:16px; margin-bottom:24px;">
            <div id="sales-layer-2"></div>
            <div id="sales-layer-3"></div>
        </div>

        <!-- Layer 4, 5, 6, 7: Charts (Lazy loaded container) -->
        <div id="sales-chart-container" class="sales-chart-lazy" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi Penjualan...</div>
        </div>

        <!-- Layer 8: Smart Sales Database (Virtualized Table) -->
        <div class="biz-card" style="margin-bottom:24px;overflow:hidden">
            <div class="biz-card-header" style="padding:16px;background:var(--biz-surface-2);border-bottom:1px solid var(--biz-border);display:flex;justify-content:space-between;align-items:center">
                <div class="biz-card-title"><i class="fas fa-database" style="color:var(--biz-primary)"></i> Transaction Database (Last 100)</div>
            </div>
            <div style="padding:12px 16px;border-bottom:1px solid var(--biz-border)">
                <div class="biz-search-bar" style="margin:0">
                    <i class="fas fa-search"></i>
                    <input type="text" id="sales-smart-search" placeholder="Cari No TRX, Produk, atau Metode Bayar..." oninput="salesRenderChunk(0, this.value)">
                </div>
            </div>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;text-align:left;border-collapse:collapse;min-width:1000px">
                    <thead style="background:var(--biz-surface-2);font-size:11px;font-weight:700;color:var(--biz-text-dim);text-transform:uppercase;letter-spacing:0.5px">
                        <tr>
                            <th style="padding:12px 16px">Transaksi</th>
                            <th style="padding:12px 16px">Produk (Qty)</th>
                            <th style="padding:12px 16px;text-align:right">Revenue</th>
                            <th style="padding:12px 16px;text-align:right">Profit / Margin</th>
                            <th style="padding:12px 16px;text-align:right">Metode</th>
                        </tr>
                    </thead>
                    <tbody id="sales-table-body">
                        <!-- Virtualized rows here -->
                    </tbody>
                </table>
            </div>
            <div id="sales-table-footer" style="padding:12px;text-align:center;font-size:12px;color:var(--biz-text-dim);background:var(--biz-surface-2)"></div>
        </div>
    </div>`;

    setTimeout(() => {
        _salesRenderLayer1();
        _salesRenderLayer2();
        _salesRenderLayer3();
        salesRenderChunk(0, '');
        _salesSetupLazyRendering();
    }, 50);
}

function _salesRenderLayer1() {
    const { overview } = window._salesSysData;
    const growthColor = overview.revGrowth > 0 ? 'var(--biz-success)' : overview.revGrowth < 0 ? 'var(--biz-danger)' : 'var(--biz-text-dim)';
    const growthIcon = overview.revGrowth > 0 ? 'fa-arrow-up' : overview.revGrowth < 0 ? 'fa-arrow-down' : 'fa-minus';
    const refundColor = overview.refundRate > 5 ? 'var(--biz-danger)' : 'var(--biz-text)';

    document.getElementById('sales-layer-1').innerHTML = `
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:12px">
        <div class="biz-card" style="padding:16px;border:1px solid rgba(16,185,129,0.3)">
            <div style="font-size:11px;color:var(--biz-success);font-weight:800">REVENUE 30D</div>
            <div style="font-size:20px;font-weight:800;color:var(--biz-success);margin-top:4px">${bizRpFull(overview.rev30)}</div>
            <div style="font-size:11px;color:${growthColor};font-weight:700;margin-top:6px"><i class="fas ${growthIcon}"></i> ${Math.abs(overview.revGrowth).toFixed(1)}% vs Prev 30D</div>
        </div>
        <div class="biz-card" style="padding:16px">
            <div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">ORDERS</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">${overview.ord30}</div>
        </div>
        <div class="biz-card" style="padding:16px">
            <div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">UNIT TERJUAL</div>
            <div style="font-size:20px;font-weight:800;margin-top:4px">${overview.units30}</div>
        </div>
        <div class="biz-card" style="padding:16px">
            <div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">AOV (Rata Order)</div>
            <div style="font-size:18px;font-weight:800;margin-top:4px">${bizRpFull(overview.aov)}</div>
        </div>
        <div class="biz-card" style="padding:16px">
            <div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">REPEAT CUSTOMER</div>
            <div style="font-size:20px;font-weight:800;color:var(--biz-primary);margin-top:4px">${overview.repeatRate.toFixed(1)}%</div>
        </div>
        <div class="biz-card" style="padding:16px">
            <div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">CANCEL / REFUND</div>
            <div style="font-size:20px;font-weight:800;color:${refundColor};margin-top:4px">${overview.refundRate.toFixed(1)}%</div>
        </div>
        <div class="biz-card" style="padding:16px;background:var(--biz-surface-2)">
            <div style="font-size:11px;color:var(--biz-purple);font-weight:800"><i class="fas fa-wand-magic-sparkles"></i> AI PROJECTED</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-purple);margin-top:4px">${bizRpFull(overview.projectedRev)}</div>
            <div style="font-size:10px;color:var(--biz-text-muted);font-weight:600;margin-top:6px">Estimasi bulan ini</div>
        </div>
    </div>`;
}

function _salesRenderLayer2() {
    const { realtime } = window._salesSysData;
    const isLive = realtime.ord60m > 0;
    const pulseHtml = isLive
        ? `<div style="display:flex;align-items:center;gap:8px;color:var(--biz-success);font-weight:700;font-size:13px"><span class="biz-pulse" style="width:8px;height:8px;background:var(--biz-success);border-radius:50%;display:inline-block"></span> ${realtime.ord60m} orders baru saja masuk.</div>`
        : `<div style="color:var(--biz-text-muted);font-size:13px;font-weight:600"><i class="fas fa-sleep"></i> Belum ada penjualan direkam di jam ini.</div>`;

    document.getElementById('sales-layer-2').innerHTML = `<div class="biz-card" style="height:100%;border:1px solid var(--biz-border-strong)">
        <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-text);letter-spacing:0.5px;border-bottom:1px solid var(--biz-border);display:flex;justify-content:space-between">
            <span><i class="fas fa-clock"></i> LAST 60 MINS PULSE</span>
            <span style="color:var(--biz-danger);animation:fade 1.5s infinite">LIVE</span>
        </div>
        <div style="padding:16px">
            <div style="font-size:24px;font-weight:800;color:var(--biz-text);margin-bottom:8px">${bizRpFull(realtime.rev60m)}</div>
            ${pulseHtml}
        </div>
    </div>`;
}

function _salesRenderLayer3() {
    const { alerts } = window._salesSysData;
    let listHtml = alerts.length > 0
        ? alerts.map(a => `<div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:8px"><i class="fas ${a.icon}" style="color:var(--biz-${a.type});margin-top:2px;font-size:13px"></i><span style="font-size:13px;font-weight:600">${a.text}</span></div>`).join('')
        : `<div style="color:var(--biz-text-muted);font-size:13px;font-weight:600"><i class="fas fa-check-circle"></i> Tidak ada anomali atau peringatan terbaca.</div>`;

    document.getElementById('sales-layer-3').innerHTML = `<div class="biz-card" style="height:100%;border-left:4px solid var(--biz-warning)">
        <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-warning);letter-spacing:0.5px;border-bottom:1px solid var(--biz-border)">
            <i class="fas fa-robot"></i> SMART SALES MONITOR
        </div>
        <div style="padding:16px">
            ${listHtml}
        </div>
    </div>`;
}

function _salesSetupLazyRendering() {
    const el = document.getElementById('sales-chart-container');
    if (!el) return;
    if (!window.IntersectionObserver) { _salesRenderCharts(); return; }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                _salesRenderCharts();
                obs.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px 0px' });
    observer.observe(el);
}

function _salesRenderCharts() {
    const container = document.getElementById('sales-chart-container');
    if (typeof Chart === 'undefined') { setTimeout(_salesRenderCharts, 500); return; }

    container.innerHTML = `
    <!-- Tren & Komposisi -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap:20px; margin-bottom:24px;">
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-chart-line" style="color:var(--biz-primary)"></i> Revenue vs Volume Trend (30D)</div></div>
            <div style="height:260px;position:relative;padding:10px"><canvas id="salesTrendChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-warning)"></i> Revenue Composition</div></div>
            <div style="display:flex;padding:10px;height:260px">
                <div style="flex:1;position:relative"><div style="text-align:center;font-size:11px;font-weight:700;color:var(--biz-text-dim)">KATEGORI</div><canvas id="salesCatChart"></canvas></div>
                <div style="flex:1;position:relative"><div style="text-align:center;font-size:11px;font-weight:700;color:var(--biz-text-dim)">CHANNEL</div><canvas id="salesChanChart"></canvas></div>
            </div>
        </div>
    </div>
    
    <!-- Produk & Customer -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap:20px;">
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-boxes-stacked" style="color:var(--biz-success)"></i> Product Performance</div></div>
            <div style="height:240px;position:relative;padding:10px"><canvas id="salesProdDualChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-users" style="color:var(--biz-purple)"></i> Customer Loyalty Frequency</div></div>
            <div style="height:240px;position:relative;padding:10px"><canvas id="salesFreqChart"></canvas></div>
        </div>
    </div>`;

    const sd = window._salesSysData;
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];

    // 1. Dual Line Trend Chart
    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: sd.trends.labels,
            datasets: [
                { label: 'Revenue', data: sd.trends.rev, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', yAxisID: 'y', tension: 0.3, fill: true },
                { label: 'Units Sold', data: sd.trends.vol, borderColor: '#3b82f6', borderDash: [5, 5], yAxisID: 'y1', tension: 0.3 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { ticks: { maxTicksLimit: 7 }, grid: { display: false } },
                y: { type: 'linear', display: true, position: 'left', grid: { color: 'rgba(255,255,255,0.05)' } },
                y1: { type: 'linear', display: true, position: 'right', grid: { display: false } }
            },
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { family: "'Inter',sans-serif", size: 11 } } } }
        }
    });

    // 2. Category Pie
    new Chart(document.getElementById('salesCatChart'), {
        type: 'doughnut',
        data: { labels: sd.composition.category.labels, datasets: [{ data: sd.composition.category.data, backgroundColor: colors, borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    // 3. Channel Pie
    const chanLabels = sd.composition.channel.map(c => c.channel);
    const chanData = sd.composition.channel.map(c => c.revenue);
    new Chart(document.getElementById('salesChanChart'), {
        type: 'doughnut',
        data: { labels: chanLabels, datasets: [{ data: chanData, backgroundColor: ['#8b5cf6', '#ec4899', '#f59e0b', '#3b82f6'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    // 4. Products Dual Bar (Rev vs Vol) using double y-axis
    new Chart(document.getElementById('salesProdDualChart'), {
        type: 'bar',
        data: {
            labels: sd.product.topRev.map(p => p.name.length > 12 ? p.name.substring(0, 10) + '...' : p.name),
            datasets: [
                { label: 'Revenue 30D', data: sd.product.topRev.map(p => p.rev), backgroundColor: '#10b981', yAxisID: 'y', borderRadius: 4 },
                // Map the volumes corresponding to topRev to keep labels aligned
                { label: 'Units', data: sd.product.topRev.map(p => { const o = sd.product.topVol.find(v => v.name === p.name); return o ? o.vol : 0; }), backgroundColor: '#3b82f6', yAxisID: 'y1', borderRadius: 4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { type: 'linear', display: true, position: 'left' },
                y1: { type: 'linear', display: true, position: 'right', grid: { display: false } }
            },
            plugins: { legend: { position: 'top' } }
        }
    });

    // 5. Customer Frequency Polar/Pie
    new Chart(document.getElementById('salesFreqChart'), {
        type: 'pie',
        data: { labels: ['1 Order', '2 Orders', '3+ Orders'], datasets: [{ data: sd.customer.ordersCounts, backgroundColor: ['#64748b', '#3b82f6', '#8b5cf6'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });
}

let _salesChunkPos = 0;
const _salesChunkSize = 25;

function salesRenderChunk(startIdx, query) {
    const { database } = window._salesSysData;
    const tbody = document.getElementById('sales-table-body');
    const footer = document.getElementById('sales-table-footer');
    if (!tbody || !database) return;

    if (startIdx === 0) {
        _salesChunkPos = 0;
        tbody.innerHTML = '';
    }

    let filtered = database;
    if (query) {
        const q = query.toLowerCase();
        filtered = database.filter(s =>
            s.trx.toLowerCase().includes(q) ||
            s.products.toLowerCase().includes(q) ||
            s.channel.toLowerCase().includes(q) ||
            s.customer.toLowerCase().includes(q)
        );
    }

    const chunk = filtered.slice(startIdx, startIdx + _salesChunkSize);

    if (chunk.length === 0 && startIdx === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="padding:30px;text-align:center;color:var(--biz-text-muted)"><i class="fas fa-receipt fa-2x"></i><br>Tidak ditemukan transaksi.</td></tr>';
        footer.innerHTML = '';
        return;
    }

    const html = chunk.map(s => {
        const dStr = new Date(s.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        const margTxt = s.margin >= 20 ? 'var(--biz-success)' : s.margin < 10 && s.margin > 0 ? 'var(--biz-warning)' : s.margin <= 0 ? 'var(--biz-danger)' : 'var(--biz-text)';
        const statIco = s.status === 'completed' ? '<i class="fas fa-circle-check" style="color:var(--biz-success)"></i>' : '<i class="fas fa-circle-xmark" style="color:var(--biz-danger)"></i>';

        return `<tr style="border-bottom:1px solid var(--biz-border);">
            <td style="padding:12px 16px">
                <div style="font-weight:700;font-size:13px;color:var(--biz-primary)">#${s.id.slice(-6).toUpperCase()} ${statIco}</div>
                <div style="font-size:11px;color:var(--biz-text-muted);margin-top:2px">${dStr}</div>
                <div style="font-size:11px;color:var(--biz-text-dim);margin-top:2px"><i class="fas fa-user"></i> ${_esc(s.customer)}</div>
            </td>
            <td style="padding:12px 16px">
                <div style="font-size:12px;color:var(--biz-text);line-height:1.4;max-width:250px">${_esc(s.products)}</div>
                <div style="font-weight:700;font-size:11px;color:var(--biz-text-dim);margin-top:4px">${s.qty} unit</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <div style="font-size:13px;font-weight:800;color:var(--biz-success)">${bizRp(s.price)}</div>
                ${s.discount > 0 ? `<div style="font-size:10px;color:var(--biz-danger);margin-top:2px">Disc: -${bizRp(s.discount)}</div>` : ''}
            </td>
            <td style="padding:12px 16px;text-align:right">
                <div style="font-weight:700;font-size:12px;color:var(--biz-primary)">+${bizRp(s.profit)}</div>
                <div style="font-weight:800;font-size:11px;color:${margTxt};margin-top:2px">${s.margin.toFixed(1)}%</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <span style="display:inline-block;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:800;background:var(--biz-surface-2);color:var(--biz-text-dim)">
                    ${s.channel.toUpperCase()}
                </span>
            </td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('beforeend', html);
    _salesChunkPos = startIdx + _salesChunkSize;

    if (_salesChunkPos < filtered.length) {
        footer.innerHTML = `<button class="biz-btn biz-btn-ghost" style="width:100%" onclick="salesRenderChunk(${_salesChunkPos}, document.getElementById('sales-smart-search').value)">Load More (${filtered.length - _salesChunkPos} trx tersisa)</button>`;
    } else {
        footer.innerHTML = `Menampilkan terakhir ${filtered.length} transaksi.`;
    }
}

window.bizLoadSales = bizLoadSales;
window.salesRenderChunk = salesRenderChunk;
