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

    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i><br><br>Memuat Product Intelligence...</div></div>`;

    const bizId = window.bizState.businessId;
    const prodData = typeof bizProductIntelligence === 'function' ? await bizProductIntelligence(bizId) : null;

    window._prodSysData = prodData;

    if (!prodData) {
        container.innerHTML = `<div class="biz-empty"><i class="fas fa-box-open"></i><br>Data produk tidak tersedia.</div>`;
        return;
    }

    container.innerHTML = `<div class="biz-page" style="padding-bottom:100px;">
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Product Intelligence</h2>
                <div style="font-size:13px;color:var(--biz-text-dim);font-weight:600;margin-top:2px">SaaS Level Commercial Analytics</div>
            </div>
            <button class="biz-btn biz-btn-primary" onclick="bizOpenAddProduct()"><i class="fas fa-plus"></i> Tambah</button>
        </div>

        <!-- Layer 1: Product Overview (KPIs) -->
        <div id="prod-layer-1" style="margin-bottom:24px;"></div>

        <!-- Layer 2: Product Alerts -->
        <div id="prod-layer-2" style="margin-bottom:24px;"></div>

        <!-- Layer 3: AI Product Insights -->
        <div id="prod-layer-3" style="margin-bottom:24px;"></div>

        <!-- Layer 4: Product Analytics -->
        <div id="prod-layer-4" class="prod-chart-lazy" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 5: Product Database (Virtualized Table) -->
        <div class="biz-card" style="margin-bottom:24px;overflow:hidden">
            <div class="biz-card-header" style="padding:16px;background:var(--biz-surface-2);border-bottom:1px solid var(--biz-border);display:flex;justify-content:space-between;align-items:center">
                <div class="biz-card-title"><i class="fas fa-database" style="color:var(--biz-primary)"></i> Product Database</div>
            </div>
            <div style="padding:12px 16px;border-bottom:1px solid var(--biz-border)">
                <div class="biz-search-bar" style="margin:0">
                    <i class="fas fa-search"></i>
                    <input type="text" id="prod-smart-search" placeholder="Cari SKU, Nama, atau Status LifeCycle..." oninput="prodRenderChunk(0, this.value)">
                </div>
            </div>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;text-align:left;border-collapse:collapse;min-width:900px">
                    <thead style="background:var(--biz-surface-2);font-size:11px;font-weight:700;color:var(--biz-text-dim);text-transform:uppercase;letter-spacing:0.5px;position:sticky;top:0;z-index:10;box-shadow:0 1px 0 var(--biz-border)">
                        <tr>
                            <th style="padding:12px 16px">Produk & Kategori</th>
                            <th style="padding:12px 16px;text-align:right">Harga & Margin</th>
                            <th style="padding:12px 16px;text-align:right">Unit Terjual (30D)</th>
                            <th style="padding:12px 16px;text-align:right">Revenue & Profit</th>
                            <th style="padding:12px 16px;text-align:center">Lifecycle</th>
                            <th style="padding:12px 16px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="prod-table-body">
                        <!-- Virtualized rows here -->
                    </tbody>
                </table>
            </div>
            <div id="prod-table-footer" style="padding:12px;text-align:center;font-size:12px;color:var(--biz-text-dim);background:var(--biz-surface-2)"></div>
        </div>
    </div>`;

    setTimeout(() => {
        _prodRenderLayer1();
        _prodRenderLayer2();
        _prodRenderLayer3();
        prodRenderChunk(0, '');
        prodSetupLazyRendering();
    }, 50);
}

function _prodRenderLayer1() {
    const { stats } = window._prodSysData;
    const html = `
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%, 140px),1fr));gap:12px">
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOTAL PRODUK</div><div style="font-size:20px;font-weight:800;margin-top:4px">${stats.totalProducts}</div></div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">REVENUE 30D</div><div style="font-size:20px;font-weight:800;color:var(--biz-primary);margin-top:4px">${bizRpFull(stats.totalRev30d)}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(16,185,129,0.2)"><div style="font-size:11px;color:var(--biz-success);font-weight:700">MARGIN TINGGI</div><div style="font-size:20px;font-weight:800;color:var(--biz-success);margin-top:4px">${stats.highMargin}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(239,68,68,0.2)"><div style="font-size:11px;color:var(--biz-danger);font-weight:700">MARGIN TIPIS (<10%)</div><div style="font-size:20px;font-weight:800;color:var(--biz-danger);margin-top:4px">${stats.lowMargin}</div></div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOP SELLER</div><div style="font-size:14px;font-weight:800;color:var(--biz-warning);margin-top:8px">${_esc(stats.topSellerName)}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(139,92,246,0.2)"><div style="font-size:11px;color:var(--biz-purple);font-weight:700">PRODUK STAGNAN</div><div style="font-size:20px;font-weight:800;color:var(--biz-purple);margin-top:4px">${stats.stagnant}</div></div>
    </div>`;
    document.getElementById('prod-layer-1').innerHTML = html;
}

function _prodRenderLayer2() {
    const { alerts } = window._prodSysData;
    let html = '';
    if (alerts && alerts.length > 0) {
        html = `<div class="biz-card" style="border:1px solid rgba(239,68,68,0.3);background:var(--biz-danger-bg)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-danger);letter-spacing:0.5px;border-bottom:1px solid rgba(239,68,68,0.1)">
                <i class="fas fa-bell"></i> SYSTEM ALERTS
            </div>
            <div style="padding:12px 16px">
                ${alerts.map(a => `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                    <i class="fas ${a.icon}" style="color:var(--biz-${a.type});margin-top:3px;font-size:14px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${a.text}</span>
                </div>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('prod-layer-2').innerHTML = html;
}

function _prodRenderLayer3() {
    const { insights } = window._prodSysData;
    let html = '';
    if (insights && insights.length > 0) {
        html = `<div class="biz-card" style="border:1px solid var(--biz-border-strong);border-left:4px solid var(--biz-primary)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-primary);letter-spacing:0.5px;border-bottom:1px solid var(--biz-border)">
                <i class="fas fa-sparkles"></i> AI PRODUCT INSIGHTS
            </div>
            <div style="padding:12px 16px">
                ${insights.map(i => `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                    <i class="fas fa-lightbulb" style="color:var(--biz-success);margin-top:3px;font-size:14px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${i}</span>
                </div>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('prod-layer-3').innerHTML = html;
}

function prodSetupLazyRendering() {
    const el = document.getElementById('prod-layer-4');
    if (!el) return;
    if (!window.IntersectionObserver) { _prodRenderLayer4(); return; }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                _prodRenderLayer4();
                obs.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px 0px' });
    observer.observe(el);
}

function _prodRenderLayer4() {
    const container = document.getElementById('prod-layer-4');
    if (typeof Chart === 'undefined') { setTimeout(_prodRenderLayer4, 500); return; }

    const rd = window._prodSysData;

    // Insights Logic
    const topRevName = rd.top10Rev.labels[0] || 'N/A';
    const topProfName = rd.top10Prof.labels[0] || 'N/A';
    const marginUnder10 = rd.distribution.under10;
    const marginLabel = marginUnder10 > 5 ? `<span style="color:var(--biz-danger)">${marginUnder10} Produk Margin Rendah</span>` : `<span style="color:var(--biz-success)">Margin Sehat</span>`;

    container.innerHTML = `<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap:20px;">
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-money-bill-wave" style="color:var(--biz-success)"></i> Top 10 Revenue</div>
                <div style="font-size:11px; font-weight:700; color:var(--biz-success)">#1: ${_esc(topRevName)}</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="prodRevChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-hand-holding-dollar" style="color:var(--biz-primary)"></i> Top 10 Profit</div>
                <div style="font-size:11px; font-weight:700; color:var(--biz-primary)">#1: ${_esc(topProfName)}</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="prodProfChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-warning)"></i> Revenue Concentration</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="prodConcChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-tags" style="color:var(--biz-purple)"></i> Margin Distribution</div>
                <div style="font-size:11px; font-weight:700;">${marginLabel}</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="prodMarginChart"></canvas></div>
        </div>
    </div>`;

    const ctxRev = document.getElementById('prodRevChart');
    const ctxProf = document.getElementById('prodProfChart');
    const ctxConc = document.getElementById('prodConcChart');
    const ctxMargin = document.getElementById('prodMarginChart');

    if (window.bizProdRevChartInst) window.bizProdRevChartInst.destroy();
    if (window.bizProdProfChartInst) window.bizProdProfChartInst.destroy();
    if (window.bizProdConcChartInst) window.bizProdConcChartInst.destroy();
    if (window.bizProdMarginChartInst) window.bizProdMarginChartInst.destroy();

    // rd already declared above

    window.bizProdRevChartInst = new Chart(ctxRev, {
        type: 'bar',
        data: { labels: rd.top10Rev.labels, datasets: [{ label: 'Revenue 30D', data: rd.top10Rev.data, backgroundColor: '#10b981', borderRadius: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y', scales: { x: { display: false } } }
    });

    window.bizProdProfChartInst = new Chart(ctxProf, {
        type: 'bar',
        data: { labels: rd.top10Prof.labels, datasets: [{ label: 'Profit 30D', data: rd.top10Prof.data, backgroundColor: '#3b82f6', borderRadius: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y', scales: { x: { display: false } } }
    });

    window.bizProdConcChartInst = new Chart(ctxConc, {
        type: 'doughnut',
        data: { labels: ['Top 5 Products', 'Lainnya'], datasets: [{ data: [rd.concentration.top5, rd.concentration.rest], backgroundColor: ['#f59e0b', '#e5e7eb'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { family: "'Inter',sans-serif", size: 11 } } } } }
    });

    window.bizProdMarginChartInst = new Chart(ctxMargin, {
        type: 'doughnut',
        data: { labels: ['<10%', '10-20%', '20-30%', '>30%'], datasets: [{ data: [rd.distribution.under10, rd.distribution.tenTo20, rd.distribution.twentyTo30, rd.distribution.over30], backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'], borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { family: "'Inter',sans-serif", size: 11 } } } } }
    });
}

let _prodChunkPos = 0;
const _prodChunkSize = 30;

function prodRenderChunk(startIdx, query) {
    const { products } = window._prodSysData;
    const tbody = document.getElementById('prod-table-body');
    const footer = document.getElementById('prod-table-footer');
    if (!tbody || !products) return;

    if (startIdx === 0) {
        _prodChunkPos = 0;
        tbody.innerHTML = '';
        // scroll removed to avoid jump
    }

    let filtered = products;
    if (query) {
        const q = query.toLowerCase();
        filtered = products.filter(b =>
            b.name.toLowerCase().includes(q) ||
            b.sku.toLowerCase().includes(q) ||
            b.lifecycle.toLowerCase().includes(q)
        );
    }

    const chunk = filtered.slice(startIdx, startIdx + _prodChunkSize);

    if (chunk.length === 0 && startIdx === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding:30px;text-align:center;color:var(--biz-text-muted)"><i class="fas fa-box-open fa-2x"></i><br>Tidak ditemukan produk yang cocok.</td></tr>';
        footer.innerHTML = '';
        return;
    }

    const html = chunk.map(p => {
        let lCol = p.lifecycle === 'GROWING' ? 'background:var(--biz-info-bg);color:var(--biz-info)' :
            p.lifecycle === 'NEW' ? 'background:var(--biz-success-bg);color:var(--biz-success)' :
                p.lifecycle === 'DECLINING' ? 'background:var(--biz-danger-bg);color:var(--biz-danger)' :
                    'background:var(--biz-surface-2);color:var(--biz-text-dim)';

        let lIcon = p.lifecycle === 'GROWING' ? 'fa-fire' :
            p.lifecycle === 'NEW' ? 'fa-wand-magic-sparkles' :
                p.lifecycle === 'DECLINING' ? 'fa-arrow-trend-down' : 'fa-balance-scale';

        let mTxt = p.marginPct < 10 ? 'var(--biz-danger)' : p.marginPct >= 30 ? 'var(--biz-success)' : 'var(--biz-text)';

        return `<tr style="border-bottom:1px solid var(--biz-border);transition:all 0.2s">
            <td style="padding:12px 16px">
                <div style="font-weight:700;font-size:13px;color:var(--biz-text)">${_esc(p.name)}</div>
                <div style="font-size:11px;color:var(--biz-text-muted);margin-top:2px">${_esc(p.category || 'General')} · ${_esc(p.sku)}</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <span style="font-size:13px;font-weight:700">${bizRp(p.price)}</span>
                <div style="font-size:10px;font-weight:800;color:${mTxt};margin-top:2px">${p.marginPct}% Margin</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <div style="font-weight:700;font-size:13px">${p.sold30d} <span style="font-size:10px;color:var(--biz-text-dim)">pcs</span></div>
                <div style="font-size:10px;color:var(--biz-text-muted);margin-top:2px"><i class="fas fa-clock"></i> ${p.avgDaily} / hr</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <div style="font-weight:700;font-size:13px;color:var(--biz-success)">${bizRpFull(p.rev30d)} Rev</div>
                <div style="font-weight:700;font-size:10px;color:var(--biz-primary);margin-top:2px">${bizRpFull(p.prof30d)} Prof</div>
            </td>
            <td style="padding:12px 16px;text-align:center">
                <span style="display:inline-block;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:800;${lCol}">
                    <i class="fas ${lIcon}" style="margin-right:2px"></i> ${p.lifecycle}
                </span>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="bizOpenEditAction('${p.id}')" title="Edit Produk"><i class="fas fa-pencil"></i></button>
            </td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('beforeend', html);
    _prodChunkPos = startIdx + _prodChunkSize;

    if (_prodChunkPos < filtered.length) {
        footer.innerHTML = `<button class="biz-btn biz-btn-ghost" style="width:100%" onclick="prodRenderChunk(${_prodChunkPos}, document.getElementById('prod-smart-search').value)">Load More (${filtered.length - _prodChunkPos} produk tersisa)</button>`;
    } else {
        footer.innerHTML = `Menampilkan seluruh ${filtered.length} produk.`;
    }
}

async function bizOpenEditAction(id) {
    const products = await BizDB.products.getAll();
    const p = products.find(prod => prod.id === id);
    if (p) bizOpenEditProduct(p);
}

window.bizLoadProducts = bizLoadProducts;
window.prodRenderChunk = prodRenderChunk;
window.bizOpenEditAction = bizOpenEditAction;
