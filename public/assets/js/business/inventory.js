/**
 * Business Manager — inventory.js
 * 5-Layer SaaS Inventory Intelligence System
 */

async function bizLoadInventory() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i><br><br>Memuat Inventory Intelligence...</div></div>`;

    const bizId = window.bizState.businessId;
    const invData = typeof bizInventoryHealth === 'function' ? await bizInventoryHealth(bizId) : null;

    // Store globally for pagination & lazy rendering
    window._invSysData = invData;

    if (!invData) {
        container.innerHTML = `<div class="biz-empty"><i class="fas fa-warehouse"></i><br>Data inventori tidak tersedia.</div>`;
        return;
    }

    container.innerHTML = `<div class="biz-page" style="padding-bottom:100px;">
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px;">
            <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Inventory Control Center</h2>
            <div style="font-size:13px;color:var(--biz-text-dim);font-weight:600;margin-top:2px">SaaS Level Stock Intelligence</div>
        </div>

        <!-- Layer 1: Inventory Overview (KPIs) -->
        <div id="inv-layer-1" style="margin-bottom:24px;"></div>

        <!-- Layer 2: Predictive Alerts -->
        <div id="inv-layer-2" style="margin-bottom:24px;"></div>

        <!-- Layer 3: AI Insight Layer -->
        <div id="inv-layer-3" style="margin-bottom:24px;"></div>

        <!-- Layer 4: Inventory Analytics -->
        <div id="inv-layer-4" class="inv-chart-lazy" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 5: Product Database (Virtualized Table) -->
        <div class="biz-card" style="margin-bottom:24px;overflow:hidden">
            <div class="biz-card-header" style="padding:16px;background:var(--biz-surface-2);border-bottom:1px solid var(--biz-border);display:flex;justify-content:space-between;align-items:center">
                <div class="biz-card-title"><i class="fas fa-database" style="color:var(--biz-primary)"></i> Product Database</div>
                <button class="biz-btn biz-btn-primary biz-btn-sm" onclick="bizOpenProductModal()"><i class="fas fa-plus"></i> Produk</button>
            </div>
            <div style="padding:12px 16px;border-bottom:1px solid var(--biz-border)">
                <div class="biz-search-bar" style="margin:0">
                    <i class="fas fa-search"></i>
                    <input type="text" id="inv-smart-search" placeholder="Cari SKU, Nama, atau Status (Cth: low)..." oninput="invRenderChunk(0, this.value)">
                </div>
            </div>
            
            <div style="overflow-x:auto;">
                <table style="width:100%;text-align:left;border-collapse:collapse;min-width:700px">
                    <thead style="background:var(--biz-surface-2);font-size:11px;font-weight:700;color:var(--biz-text-dim);text-transform:uppercase;letter-spacing:0.5px;position:sticky;top:0;z-index:10;box-shadow:0 1px 0 var(--biz-border)">
                        <tr>
                            <th style="padding:12px 16px">Produk & Kategori</th>
                            <th style="padding:12px 16px;text-align:right">Stok Aktif</th>
                            <th style="padding:12px 16px;text-align:center">Kecepatan Jual</th>
                            <th style="padding:12px 16px;text-align:right">Nilai & Margin</th>
                            <th style="padding:12px 16px;text-align:center">Status</th>
                            <th style="padding:12px 16px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="inv-table-body">
                        <!-- Virtualized rows here -->
                    </tbody>
                </table>
            </div>
            <div id="inv-table-footer" style="padding:12px;text-align:center;font-size:12px;color:var(--biz-text-dim);background:var(--biz-surface-2)"></div>
        </div>
    </div>`;

    setTimeout(() => {
        _invRenderLayer1();
        _invRenderLayer2();
        _invRenderLayer3();
        invRenderChunk(0, '');
        invSetupLazyRendering();
    }, 50);
}

// ── LAYER 1: KPI Overview ────────────────────────────────────────────────
function _invRenderLayer1() {
    const { score, counts, burnRates, totalValue } = window._invSysData;
    const totalQty = burnRates.reduce((sum, b) => sum + b.stock, 0);
    const totalItems = burnRates.length;

    let scoreCol = score >= 80 ? 'var(--biz-success)' : score >= 50 ? 'var(--biz-warning)' : 'var(--biz-danger)';
    let scoreTxt = score >= 80 ? 'HEALTHY' : score >= 50 ? 'NEEDS ATTENTION' : 'CRITICAL';

    const html = `
    <div style="display:flex;gap:16px;align-items:stretch;margin-bottom:12px;flex-wrap:wrap">
        <!-- Gamified Health Score -->
        <div class="biz-card" style="padding:24px;text-align:center;flex:1;min-width:200px;background:var(--biz-surface-2);display:flex;flex-direction:column;justify-content:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-dim);margin-bottom:8px">STOCK HEALTH SCORE</div>
            <div style="font-size:42px;font-weight:900;color:${scoreCol};line-height:1">${score}</div>
            <div style="font-size:12px;font-weight:700;color:${scoreCol};margin-top:6px;letter-spacing:1px">${scoreTxt}</div>
            <div style="font-size:11px;color:var(--biz-text-muted);font-weight:600;margin-top:8px">
                <span style="color:var(--biz-danger)">${counts.low + counts.out} Low</span> · 
                <span style="color:var(--biz-primary)">${counts.over} Over</span> · 
                <span style="color:var(--biz-warning)">${counts.dead} Dead</span>
            </div>
        </div>

        <!-- 6 Micro KPIs -->
        <div style="flex:2;min-width:min(100%, 300px);display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%, 120px),1fr));gap:12px">
            <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOTAL PRODUK</div><div style="font-size:20px;font-weight:800;margin-top:4px">${totalItems}</div></div>
            <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOTAL UNIT</div><div style="font-size:20px;font-weight:800;margin-top:4px">${totalQty.toLocaleString('id-ID')}</div></div>
            <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">NILAI STOK</div><div style="font-size:18px;font-weight:800;color:var(--biz-primary);margin-top:4px">${bizRp(totalValue)}</div></div>
            
            <div class="biz-card" style="padding:16px;border:1px solid rgba(239,68,68,0.2)"><div style="font-size:11px;color:var(--biz-danger);font-weight:700">LOW STOCK</div><div style="font-size:20px;font-weight:800;color:var(--biz-danger);margin-top:4px">${counts.low}</div></div>
            <div class="biz-card" style="padding:16px;border:1px solid rgba(59,130,246,0.2)"><div style="font-size:11px;color:var(--biz-primary);font-weight:700">OVERSTOCK</div><div style="font-size:20px;font-weight:800;color:var(--biz-primary);margin-top:4px">${counts.over}</div></div>
            <div class="biz-card" style="padding:16px;border:1px solid rgba(245,158,11,0.2)"><div style="font-size:11px;color:var(--biz-warning);font-weight:700">SLOW MOVING</div><div style="font-size:20px;font-weight:800;color:var(--biz-warning);margin-top:4px">${counts.slow}</div></div>
        </div>
    </div>`;
    document.getElementById('inv-layer-1').innerHTML = html;
}

// ── LAYER 2: Predictive Alerts ──────────────────────────────────────────
function _invRenderLayer2() {
    const { alerts } = window._invSysData;
    let html = '';
    if (alerts && alerts.length > 0) {
        html = `<div class="biz-card" style="border:1px solid rgba(239,68,68,0.3);background:var(--biz-danger-bg)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-danger);letter-spacing:0.5px;border-bottom:1px solid rgba(239,68,68,0.1)">
                <i class="fas fa-bell"></i> PREDICTIVE ALERTS
            </div>
            <div style="padding:12px 16px">
                ${alerts.map(a => {
            const col = a.type === 'danger' ? 'var(--biz-danger)' : 'var(--biz-warning)';
            return `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                        <i class="fas ${a.icon}" style="color:${col};margin-top:3px;font-size:14px"></i>
                        <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${a.text}</span>
                    </div>`;
        }).join('')}
            </div>
        </div>`;
    }
    document.getElementById('inv-layer-2').innerHTML = html;
}

// ── LAYER 3: AI Insights ────────────────────────────────────────────────
function _invRenderLayer3() {
    const { insights } = window._invSysData;
    let html = '';
    if (insights && insights.length > 0) {
        html = `<div class="biz-card" style="border:1px solid var(--biz-border-strong);border-left:4px solid var(--biz-primary)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-primary);letter-spacing:0.5px;border-bottom:1px solid var(--biz-border)">
                <i class="fas fa-sparkles"></i> AI INVENTORY INSIGHTS
            </div>
            <div style="padding:12px 16px">
                ${insights.map(i => `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                    <i class="fas fa-lightbulb" style="color:var(--biz-success);margin-top:3px;font-size:14px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${i}</span>
                </div>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('inv-layer-3').innerHTML = html;
}

// ── LAYER 4: Lazy Analytics Layout ──────────────────────────────────────
function invSetupLazyRendering() {
    const el = document.getElementById('inv-layer-4');
    if (!el) return;

    if (!window.IntersectionObserver) { _invRenderLayer4(); return; }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                _invRenderLayer4();
                obs.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px 0px' });
    observer.observe(el);
}

function _invRenderLayer4() {
    const container = document.getElementById('inv-layer-4');
    if (typeof Chart === 'undefined') {
        setTimeout(_invRenderLayer4, 500);
        return;
    }

    const { counts, burnRates } = window._invSysData;
    const deadAgeText = counts.dead > 0 ? `<span style="color:var(--biz-danger)">${counts.dead} Produk Dead</span>` : `<span style="color:var(--biz-success)">Aman</span>`;

    container.innerHTML = `<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:20px;">
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-primary)"></i> Stock Distribution</div>
                <div style="font-size:11px; font-weight:700; color:var(--biz-success)">${counts.fast} Fast Moving</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="invDistChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-skull" style="color:var(--biz-danger)"></i> Dead Stock Age</div>
                <div style="font-size:11px; font-weight:700;">${deadAgeText}</div>
            </div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="invDeadAgeChart"></canvas></div>
        </div>
    </div>`;

    // Dist Chart
    const ctxDist = document.getElementById('invDistChart');
    if (ctxDist) {
        if (window.bizInvDistChartInst) window.bizInvDistChartInst.destroy();
        window.bizInvDistChartInst = new Chart(ctxDist, {
            type: 'doughnut',
            data: { labels: ['Fast', 'Normal', 'Slow', 'Dead'], datasets: [{ data: [counts.fast, counts.normal, counts.slow, counts.dead], backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { family: "'Inter',sans-serif", size: 11 } } } } }
        });
    }

    // Dead Age Chart
    const ctxAge = document.getElementById('invDeadAgeChart');
    if (ctxAge) {
        let age0_7 = 0, age7_30 = 0, age30_60 = 0, age60p = 0;
        burnRates.forEach(b => {
            if (b.velocityLabel === 'DEAD' && b.stock > 0) {
                if (b.daysSinceSold <= 7) age0_7++;
                else if (b.daysSinceSold <= 30) age7_30++;
                else if (b.daysSinceSold <= 60) age30_60++;
                else age60p++;
            }
        });

        if (window.bizInvAgeChartInst) window.bizInvAgeChartInst.destroy();
        window.bizInvAgeChartInst = new Chart(ctxAge, {
            type: 'bar',
            data: { labels: ['0-7 Hari', '7-30 Hari', '30-60 Hari', '> 60 Hari'], datasets: [{ label: 'Jumlah Produk', data: [age0_7, age7_30, age30_60, age60p], backgroundColor: 'rgba(239,68,68,0.8)', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }
}

// ── LAYER 5: Virtualized Product Database ─────────────────────────────────
let _invChunkPos = 0;
const _invChunkSize = 30;

function invRenderChunk(startIdx, query) {
    const { burnRates } = window._invSysData;
    const tbody = document.getElementById('inv-table-body');
    const footer = document.getElementById('inv-table-footer');
    if (!tbody || !burnRates) return;

    if (startIdx === 0) {
        _invChunkPos = 0;
        tbody.innerHTML = '';
        window.scrollTo(0, 0); // optional, to stay focused
    }

    let filtered = burnRates;
    if (query) {
        const q = query.toLowerCase();
        filtered = burnRates.filter(b =>
            b.name.toLowerCase().includes(q) ||
            (b.sku && b.sku.toLowerCase().includes(q)) ||
            b.masterStatus.toLowerCase().includes(q)
        );
    }

    // Default sort by critical status
    filtered.sort((a, b) => {
        const w = s => s === 'OUT' ? 5 : s === 'LOW' ? 4 : s === 'DEAD' ? 3 : s === 'OVER' ? 2 : s === 'EXPIRING' ? 1 : 0;
        return w(b.masterStatus) - w(a.masterStatus);
    });

    const chunk = filtered.slice(startIdx, startIdx + _invChunkSize);

    if (chunk.length === 0 && startIdx === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding:30px;text-align:center;color:var(--biz-text-muted)"><i class="fas fa-box-open fa-2x"></i><br>Tidak ditemukan produk yang cocok.</td></tr>';
        footer.innerHTML = '';
        return;
    }

    // Generate HTML for chunk
    const html = chunk.map(p => {
        let badgeCol = p.masterStatus === 'FAST' ? 'background:var(--biz-success-bg);color:var(--biz-success)' :
            p.masterStatus === 'OUT' || p.masterStatus === 'LOW' || p.masterStatus === 'DEAD' || p.masterStatus === 'EXPIRING' ? 'background:var(--biz-danger-bg);color:var(--biz-danger)' :
                p.masterStatus === 'SLOW' ? 'background:var(--biz-warning-bg);color:var(--biz-warning)' :
                    p.masterStatus === 'OVER' ? 'background:#e0e7ff;color:#4f46e5' :
                        'background:var(--biz-surface-2);color:var(--biz-text-dim)';

        let dateTx = p.lastSold ? new Date(p.lastSold).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }) : 'N/A';
        let actCol = p.stock <= 0 ? 'var(--biz-danger)' : 'var(--biz-primary)';

        return `<tr style="border-bottom:1px solid var(--biz-border);transition:all 0.2s">
            <td style="padding:12px 16px">
                <div style="font-weight:700;font-size:13px;color:var(--biz-text)">${_esc(p.name)}</div>
                <div style="font-size:11px;color:var(--biz-text-muted);margin-top:2px">${_esc(p.category || 'General')} · ${_esc(p.sku)}</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <span style="font-size:14px;font-weight:900">${p.stock}</span>
                <div style="font-size:10px;color:var(--biz-text-dim);margin-top:2px">Unit Tersedia</div>
            </td>
            <td style="padding:12px 16px;text-align:center">
                <div style="font-weight:700;font-size:13px">${p.avgDaily} <span style="font-size:10px;color:var(--biz-text-dim)">/hr</span></div>
                <div style="font-size:10px;color:var(--biz-text-muted);margin-top:2px"><i class="fas fa-clock"></i> L terjual: ${dateTx}</div>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <div style="font-weight:700;font-size:13px">${bizRp(p.stockValue)}</div>
                <div style="font-size:10px;${p.marginPct >= 0 ? 'color:var(--biz-success)' : 'color:var(--biz-danger)'};font-weight:700;margin-top:2px">${p.marginPct}% Margin Profit</div>
            </td>
            <td style="padding:12px 16px;text-align:center">
                <span style="display:inline-block;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:800;${badgeCol}">${p.masterStatus}</span>
            </td>
            <td style="padding:12px 16px;text-align:right">
                <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="bizOpenRestock('${p.id}','${_esc(p.name)}','${p.stock}')" title="Restock/Adjust" style="color:${actCol}"><i class="fas fa-boxes-packing"></i></button>
            </td>
        </tr>`;
    }).join('');

    tbody.insertAdjacentHTML('beforeend', html);
    _invChunkPos = startIdx + _invChunkSize;

    if (_invChunkPos < filtered.length) {
        footer.innerHTML = `<button class="biz-btn biz-btn-ghost" style="width:100%" onclick="invRenderChunk(${_invChunkPos}, document.getElementById('inv-smart-search').value)">Load More (${filtered.length - _invChunkPos} produk tersisa)</button>`;
    } else {
        footer.innerHTML = `Menampilkan seluruh ${filtered.length} produk.`;
    }
}

// ── Modals & Actions ────────────────────────────────────────────────────
function bizOpenRestock(productId, name, currentStock) {
    document.getElementById('restock-product-id').value = productId;
    document.getElementById('restock-product-name').textContent = `${name} — Stok saat ini: ${currentStock}`;
    document.getElementById('restock-qty').value = '';
    document.getElementById('restock-notes').value = '';
    bizOpenModal('biz-modal-restock');
    setTimeout(() => document.getElementById('restock-qty')?.focus(), 150);
}

async function bizSaveRestock() {
    const productId = document.getElementById('restock-product-id').value;
    const qty = parseInt(document.getElementById('restock-qty').value) || 0;
    const notes = document.getElementById('restock-notes').value.trim();
    if (qty === 0) { bizToast('Qty tidak boleh 0', 'w'); return; }

    const products = await BizDB.products.getAll();
    const p = products.find(pr => pr.id === productId);
    if (!p) { bizToast('Produk tidak ditemukan', 'e'); return; }

    p.stock = (p.stock || 0) + qty;
    p.updated_at = new Date().toISOString();
    await BizDB.products.save(p);

    await BizDB.invMovements.save({
        id: bizUUID(),
        business_id: window.bizState.businessId,
        product_id: productId,
        type: qty > 0 ? 'in' : 'out',
        quantity: Math.abs(qty),
        stock_after: p.stock,
        reference_type: 'adjustment',
        reference_id: null,
        notes: notes || 'Stock Adjustment manual',
        created_at: new Date().toISOString(),
    });

    await bizPreloadProducts();
    bizToast(`✅ Stok ${p.name} disesuaikan → ${p.stock}`, 's');
    bizCloseModal('biz-modal-restock');

    // Clear cache & reload
    _iCacheSet('inv_health_' + window.bizState.businessId, null, 0);
    await bizLoadInventory();
}

window.bizLoadInventory = bizLoadInventory;
window.invRenderChunk = invRenderChunk;
window.bizOpenRestock = bizOpenRestock;
window.bizSaveRestock = bizSaveRestock;

