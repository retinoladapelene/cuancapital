/**
 * Business Manager — reports.js
 * 6-Layer Business Intelligence Analytics Dashboard
 */
window.bizState = window.bizState || {};
if (!window.bizState.analyticsRange) window.bizState.analyticsRange = '30d';

async function bizLoadReports() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i><br><br>Memuat Intelligence Engine...</div></div>`;

    const bizId = window.bizState.businessId;

    const [snapshots, sales, saleItems, expenses, inventoryData, profitData] = await Promise.all([
        BizDB.finSnapshots.getAll(),
        BizDB.sales.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.expenses.getAll(),
        typeof bizInventoryHealth === 'function' ? bizInventoryHealth(bizId) : null,
        typeof bizProfitAnalyzer === 'function' ? bizProfitAnalyzer(bizId, bizMonthKey()) : null
    ]);

    // Store globally for lazy renderer
    window._repData = { bizId, snapshots, sales, saleItems, expenses, inventoryData, profitData };

    container.innerHTML = `<div class="biz-page" style="padding-bottom:100px;">
        <!-- Global Time Engine -->
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:flex-end;">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Analytics</h2>
                <div style="font-size:13px;color:var(--biz-text-dim);font-weight:600;margin-top:2px">Business Intelligence Center</div>
            </div>
            
            <div class="biz-time-filters" style="display:flex; gap:6px; background:var(--biz-surface-2); padding:4px; border-radius:8px;">
                <button onclick="repSetFilter('7d')" style="padding:4px 10px; border-radius:6px; border:none; background:${window.bizState.analyticsRange === '7d' ? 'var(--biz-surface)' : 'transparent'}; box-shadow:${window.bizState.analyticsRange === '7d' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none'}; font-size:12px; font-weight:600; color:var(--biz-text); cursor:pointer;">7D</button>
                <button onclick="repSetFilter('30d')" style="padding:4px 10px; border-radius:6px; border:none; background:${window.bizState.analyticsRange === '30d' ? 'var(--biz-surface)' : 'transparent'}; box-shadow:${window.bizState.analyticsRange === '30d' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none'}; font-size:12px; font-weight:600; color:var(--biz-text); cursor:pointer;">30D</button>
                <button onclick="repSetFilter('90d')" style="padding:4px 10px; border-radius:6px; border:none; background:${window.bizState.analyticsRange === '90d' ? 'var(--biz-surface)' : 'transparent'}; box-shadow:${window.bizState.analyticsRange === '90d' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none'}; font-size:12px; font-weight:600; color:var(--biz-text); cursor:pointer;">90D</button>
                <button onclick="repSetFilter('1y')" style="padding:4px 10px; border-radius:6px; border:none; background:${window.bizState.analyticsRange === '1y' ? 'var(--biz-surface)' : 'transparent'}; box-shadow:${window.bizState.analyticsRange === '1y' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none'}; font-size:12px; font-weight:600; color:var(--biz-text); cursor:pointer;">1Y</button>
            </div>
        </div>

        <!-- AI Insight Engine (Top Banner) -->
        <div id="rep-ai-insights"></div>

        <!-- Layer 1: Business Health Overview -->
        <div id="rep-layer-1" style="margin-bottom:24px;"></div>

        <!-- Layer 2: Sales Intelligence -->
        <div id="rep-layer-2" class="biz-chart-lazy" data-layer="2" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 3: Profit Intelligence -->
        <div id="rep-layer-3" class="biz-chart-lazy" data-layer="3" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 4: Customer Intelligence -->
        <div id="rep-layer-4" class="biz-chart-lazy" data-layer="4" style="min-height:100px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 5: Inventory Intelligence -->
        <div id="rep-layer-5" class="biz-chart-lazy" data-layer="5" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

        <!-- Layer 6: Cashflow Intelligence -->
        <div id="rep-layer-6" class="biz-chart-lazy" data-layer="6" style="min-height:300px; margin-bottom:24px;">
             <div class="biz-loading" style="padding:20px"><i class="fas fa-spinner fa-spin"></i> Memuat Visualisasi...</div>
        </div>

    </div>`;

    setTimeout(() => {
        repRenderAIAndLayer1();
        repSetupLazyRendering();
    }, 50);
}

function repSetFilter(range) {
    window.bizState.analyticsRange = range;
    bizLoadReports();
}

function repGetDateRange() {
    const range = window.bizState.analyticsRange || '30d';
    const end = new Date();
    const start = new Date();
    if (range === '7d') start.setDate(end.getDate() - 7);
    else if (range === '30d') start.setDate(end.getDate() - 30);
    else if (range === '90d') start.setDate(end.getDate() - 90);
    else if (range === '1y') start.setFullYear(end.getFullYear() - 1);

    // Previous period for benchmarking
    const prevEnd = new Date(start);
    const prevStart = new Date(start);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    prevStart.setDate(prevEnd.getDate() - diffDays);

    return { start, end, prevStart, prevEnd, diffDays };
}

// Helpers
function _repFilterByBizAndDate(items, bizId, start, end, dateField) {
    return items.filter(item => {
        if (item.business_id !== bizId) return false;
        const dtStr = item[dateField] || item.created_at;
        if (!dtStr) return false;
        const d = new Date(dtStr);
        return d >= start && d <= end;
    });
}

function _repCalcDeltaHTML(currentVal, prevVal, isReverse = false) {
    if (currentVal === 0 && prevVal === 0) return `<span style="color:var(--biz-text-dim);font-size:11px;font-weight:600">—</span>`;
    if (prevVal === 0 && currentVal > 0) {
        const col = !isReverse ? 'var(--biz-success)' : 'var(--biz-danger)';
        return `<span style="color:${col};font-size:11px;font-weight:700" title="Pertumbuhan awal"><i class="fas fa-arrow-up"></i> 100%</span>`;
    }

    const delta = ((currentVal - prevVal) / prevVal) * 100;
    if (delta === 0) return `<span style="color:var(--biz-text-dim);font-size:11px;font-weight:600">—</span>`;

    let isGood = delta > 0;
    if (isReverse) isGood = !isGood; // Example: expenses going down is good

    const col = isGood ? 'var(--biz-success)' : 'var(--biz-danger)';
    const dir = delta > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
    return `<span style="color:${col};font-size:11px;font-weight:700" title="vs previous period"><i class="fas ${dir}"></i> ${Math.abs(delta).toFixed(1)}%</span>`;
}

// ── LAYER 1: Core Health & Insights ───────────────
async function repRenderAIAndLayer1() {
    const { bizId, snapshots, sales, expenses } = window._repData;
    const { start, end, prevStart, prevEnd } = repGetDateRange();

    // Data Aggregation
    const curSales = _repFilterByBizAndDate(sales, bizId, start, end, 'created_at');
    const prevSales = _repFilterByBizAndDate(sales, bizId, prevStart, prevEnd, 'created_at');

    const curExp = _repFilterByBizAndDate(expenses, bizId, start, end, 'expense_date');
    const prevExp = _repFilterByBizAndDate(expenses, bizId, prevStart, prevEnd, 'expense_date');

    const curRev = curSales.reduce((sum, s) => sum + s.total_amount, 0);
    const prevRev = prevSales.reduce((sum, s) => sum + s.total_amount, 0);

    const curExpSum = curExp.reduce((sum, e) => sum + e.amount, 0);
    const prevExpSum = prevExp.reduce((sum, e) => sum + e.amount, 0);

    const curProf = curRev - curExpSum;
    const prevProf = prevRev - prevExpSum;

    // AI Insight Engine (Top Banner)
    let aiHtml = '';
    const insights = [];
    const revGrowth = prevRev > 0 ? ((curRev - prevRev) / prevRev * 100).toFixed(0) : '100';
    if (curRev > prevRev * 1.05) insights.push({ t: 'success', msg: `Revenue naik ${revGrowth}% dibanding periode sebelumnya.` });
    else if (curRev < prevRev * 0.95 && prevRev > 0) insights.push({ t: 'danger', msg: `Revenue turun ${((prevRev - curRev) / prevRev * 100).toFixed(0)}% dibanding periode lalu.` });

    const expGrowth = prevExpSum > 0 ? ((curExpSum - prevExpSum) / prevExpSum * 100).toFixed(0) : '100';
    if (curExpSum > prevExpSum * 1.1) insights.push({ t: 'warning', msg: `Biaya pengeluaran melonjak ${expGrowth}%.` });

    if (insights.length > 0) {
        aiHtml = `<div class="biz-card" style="margin-bottom:20px;border:1px solid var(--biz-border-strong);border-left:4px solid var(--biz-primary)">
            <div style="font-size:12px;font-weight:700;color:var(--biz-primary);margin-bottom:8px;letter-spacing:0.5px"><i class="fas fa-sparkles"></i> AI BUSINESS INSIGHT</div>
            ${insights.map(ins => {
            let col = ins.t === 'success' ? 'var(--biz-success)' : ins.t === 'danger' ? 'var(--biz-danger)' : 'var(--biz-warning)';
            let icn = ins.t === 'success' ? 'fa-arrow-trend-up' : ins.t === 'danger' ? 'fa-triangle-exclamation' : 'fa-lightbulb';
            return `<div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:6px">
                    <i class="fas ${icn}" style="color:${col};margin-top:2px;font-size:13px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${ins.msg}</span>
                </div>`;
        }).join('')}
        </div>`;
    }

    const hlth = typeof bizHealthScore === 'function' ? bizHealthScore(snapshots, bizId) : { score: 0, status: 'Unknown', color: 'gray' };

    let layer1Html = `
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 130px), 1fr)); gap:12px; margin-bottom:12px">
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700;margin-bottom:4px">REVENUE</div><div style="font-size:18px;font-weight:800;margin-bottom:6px">${bizRp(curRev)}</div>${_repCalcDeltaHTML(curRev, prevRev)}</div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700;margin-bottom:4px">PROFIT</div><div style="font-size:18px;font-weight:800;color:var(--biz-success);margin-bottom:6px">${bizRp(curProf)}</div>${_repCalcDeltaHTML(curProf, prevProf)}</div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700;margin-bottom:4px">EXPENSE</div><div style="font-size:18px;font-weight:800;color:var(--biz-danger);margin-bottom:6px">${bizRp(curExpSum)}</div>${_repCalcDeltaHTML(curExpSum, prevExpSum, true)}</div>
        <div class="biz-card" style="padding:16px;background:var(--biz-surface-2)"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700;margin-bottom:4px">HEALTH SCORE</div><div style="font-size:24px;font-weight:800;color:${hlth.color};line-height:1">${hlth.score}</div><div style="font-size:11px;font-weight:700;color:${hlth.color};margin-top:4px">${hlth.status}</div></div>
    </div>
    
    <div class="biz-card" style="padding:20px">
        <div class="biz-card-header" style="margin-bottom:12px"><div class="biz-card-title"><i class="fas fa-radar" style="color:var(--biz-primary)"></i> Profit Radar</div></div>
        <div style="height:220px;width:100%;position:relative"><canvas id="repProfitRadar"></canvas></div>
    </div>
    `;

    document.getElementById('rep-ai-insights').innerHTML = aiHtml;
    document.getElementById('rep-layer-1').innerHTML = layer1Html;

    // Render Radar Layer 1
    _repRenderProfitRadar(snapshots, bizId);
}

function _repRenderProfitRadar(snapshots, bizId) {
    const canvas = document.getElementById('repProfitRadar');
    if (!canvas || typeof Chart === 'undefined') return;

    if (window.bizRepRadarInst) window.bizRepRadarInst.destroy();

    const { profitData } = window._repData;
    if (!profitData || !profitData.products || profitData.products.length === 0) {
        canvas.parentElement.innerHTML = '<div class="biz-empty"><i class="fas fa-radar"></i><br>Belum ada data profit / produk untuk dianalisa.</div>';
        return;
    }

    const topProducts = profitData.products.slice(0, 5);
    const labels = topProducts.map(p => _esc(p.name.length > 12 ? p.name.substring(0, 10) + '...' : p.name));
    const margins = topProducts.map(p => p.margin);

    window.bizRepRadarInst = new Chart(canvas, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Profit Margin (%)',
                data: margins,
                backgroundColor: 'rgba(99, 102, 241, 0.15)', // Tailwind Indigo 500 light
                borderColor: '#6366f1', // Tailwind Indigo 500
                pointBackgroundColor: '#10b981', // Emerald for active points
                pointBorderColor: '#ffffff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                r: { angleLines: { color: 'rgba(15, 23, 42, 0.05)' }, grid: { color: 'rgba(15, 23, 42, 0.05)' }, pointLabels: { font: { family: "'Inter', sans-serif", size: 10, weight: '600' }, color: '#64748b' }, ticks: { display: false, min: 0 } }
            },
            plugins: { legend: { display: false } }
        }
    });
}

// ── Lazy Renderer Logic ──────────────────────────────────────────────────
function repSetupLazyRendering() {
    if (!window.IntersectionObserver) {
        // Fallback for older browsers
        document.querySelectorAll('.biz-chart-lazy').forEach(el => repRenderSpecificLayer(el.getAttribute('data-layer'), el));
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const layerId = entry.target.getAttribute('data-layer');
                repRenderSpecificLayer(layerId, entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, { rootMargin: '200px 0px' });

    document.querySelectorAll('.biz-chart-lazy').forEach(el => observer.observe(el));
}

function repRenderSpecificLayer(layerId, container) {
    if (typeof Chart === 'undefined') {
        container.innerHTML = `<div class="biz-loading">Waiting for Chart.js...</div>`;
        setTimeout(() => repRenderSpecificLayer(layerId, container), 500);
        return;
    }

    if (layerId === '2') _repRenderSalesLayer(container);
    else if (layerId === '3') _repRenderProfitLayer(container);
    else if (layerId === '4') _repRenderCustomerLayer(container);
    else if (layerId === '5') _repRenderInventoryLayer(container);
    else if (layerId === '6') _repRenderCashflowLayer(container);
}

// Layer 2: Sales Intelligence
function _repRenderSalesLayer(container) {
    const { bizId, snapshots, sales, saleItems } = window._repData;
    const { start, end, diffDays } = repGetDateRange();

    // Reconstruct data
    const days = [];
    for (let i = diffDays; i >= 0; i--) {
        const d = new Date(end); d.setDate(d.getDate() - i);
        days.push(d.toISOString().split('T')[0]);
    }
    const revenues = [];
    const volumes = [];
    days.forEach(d => {
        const snap = snapshots.find(s => s.snapshot_date === d && s.business_id === bizId);
        revenues.push(snap ? (snap.revenue || 0) : 0);
        volumes.push(snap ? (snap.orders_count || 0) : 0);
    });
    const lbls = days.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));

    // Categ & Channel Aggregation
    const curSales = _repFilterByBizAndDate(sales, bizId, start, end, 'created_at');
    const chanMap = {};
    curSales.forEach(s => { chanMap[s.payment_method] = (chanMap[s.payment_method] || 0) + s.total_amount; });

    const curSaleIds = new Set(curSales.filter(s => s.status !== 'cancelled').map(s => s.id));
    const curItems = saleItems.filter(si => curSaleIds.has(si.sale_id));

    // Build product ID → category map from product cache
    const prodCatMap = {};
    (window.bizState.productCache || []).forEach(p => { prodCatMap[p.id] = p.category || 'Lainnya'; });

    const catMap = {};
    curItems.forEach(i => {
        const cat = prodCatMap[i.product_id] || 'Lainnya';
        catMap[cat] = (catMap[cat] || 0) + (i.subtotal || i.subtotal_price || (i.price * i.quantity) || 0);
    });

    container.innerHTML = `
    <!-- Tren & Komposisi -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 380px), 1fr)); gap:20px; margin-bottom:24px;">
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-chart-line" style="color:var(--biz-primary)"></i> Revenue Trend (${window.bizState.analyticsRange.toUpperCase()})</div>
            </div>
            <div style="height:260px;position:relative;padding:10px"><canvas id="repSalesTrendChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-warning)"></i> Revenue Composition</div></div>
            <div style="display:flex;flex-wrap:wrap;padding:10px;height:auto;min-height:260px;gap:20px;align-items:center;">
                <div style="flex:1;min-width:140px;position:relative;height:240px"><div style="text-align:center;font-size:11px;font-weight:700;color:var(--biz-text-dim)">KATEGORI</div><canvas id="repSalesCatChart"></canvas></div>
                <div style="flex:1;min-width:140px;position:relative;height:240px"><div style="text-align:center;font-size:11px;font-weight:700;color:var(--biz-text-dim)">CHANNEL</div><canvas id="repSalesChanChart"></canvas></div>
            </div>
        </div>
    </div>`;

    if (window.bizRepSalesChartInst) window.bizRepSalesChartInst.destroy();
    window.bizRepSalesChartInst = new Chart(document.getElementById('repSalesTrendChart'), {
        type: 'line',
        data: {
            labels: lbls, datasets: [
                { label: 'Revenue', data: revenues, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', yAxisID: 'y', tension: 0.3, fill: true },
                { label: 'Units Sold', data: volumes, borderColor: '#3b82f6', borderDash: [5, 5], yAxisID: 'y1', tension: 0.3 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { x: { grid: { display: false } }, y: { type: 'linear', display: true, position: 'left' }, y1: { type: 'linear', display: true, position: 'right', grid: { display: false } } }, plugins: { legend: { position: 'top' } } }
    });

    const cColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
    if (window.bizRepCatChartInst) window.bizRepCatChartInst.destroy();
    window.bizRepCatChartInst = new Chart(document.getElementById('repSalesCatChart'), {
        type: 'doughnut', data: { labels: Object.keys(catMap), datasets: [{ data: Object.values(catMap), backgroundColor: cColors, borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });

    if (window.bizRepChanChartInst) window.bizRepChanChartInst.destroy();
    window.bizRepChanChartInst = new Chart(document.getElementById('repSalesChanChart'), {
        type: 'doughnut', data: { labels: Object.keys(chanMap), datasets: [{ data: Object.values(chanMap), backgroundColor: ['#8b5cf6', '#ec4899', '#f59e0b', '#3b82f6'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } } }
    });
}

// Layer 3: Profit & Products Intelligence
function _repRenderProfitLayer(container) {
    const { bizId, expenses, profitData } = window._repData;
    const { start, end } = repGetDateRange();
    const curExp = _repFilterByBizAndDate(expenses, bizId, start, end, 'expense_date');

    const topRevNames = []; const topRevData = [];
    const topProfNames = []; const topProfData = [];
    if (profitData && profitData.products) {
        const byRev = [...profitData.products].sort((a, b) => b.revenue - a.revenue).slice(0, 10);
        byRev.forEach(p => { topRevNames.push(p.name.length > 12 ? p.name.substring(0, 10) + '...' : p.name); topRevData.push(p.revenue); });

        const byProf = [...profitData.products].sort((a, b) => b.profit - a.profit).slice(0, 10);
        byProf.forEach(p => { topProfNames.push(p.name.length > 12 ? p.name.substring(0, 10) + '...' : p.name); topProfData.push(p.profit); });
    }

    container.innerHTML = `
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap:20px; margin-bottom:20px">
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-money-bill-wave" style="color:var(--biz-success)"></i> Top 10 Revenue</div></div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="repTopRevChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-hand-holding-dollar" style="color:var(--biz-primary)"></i> Top 10 Profit</div></div>
            <div style="height:220px;position:relative;padding:10px"><canvas id="repTopProfChart"></canvas></div>
        </div>
    </div>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr)); gap:20px;">
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-danger)"></i> Operational Breakdown</div></div>
            <div style="height:180px;position:relative;padding:10px"><canvas id="repExpDonutChart"></canvas></div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-tags" style="color:var(--biz-purple)"></i> Margin Distribution</div>
            </div>
            <div style="height:180px;position:relative;padding:10px"><canvas id="repMarginDistChart"></canvas></div>
        </div>
    </div>`;
    const catMap = {};
    curExp.forEach(e => {
        const cat = e.category_name || 'Lainnya';
        catMap[cat] = (catMap[cat] || 0) + e.amount;
    });
    const topCats = Object.entries(catMap).sort((a, b) => b[1] - a[1]).slice(0, 4);

    if (window.bizRepTopRevInst) window.bizRepTopRevInst.destroy();
    window.bizRepTopRevInst = new Chart(document.getElementById('repTopRevChart'), { type: 'bar', data: { labels: topRevNames, datasets: [{ label: 'Revenue', data: topRevData, backgroundColor: '#10b981', borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y', scales: { x: { display: false } } } });

    if (window.bizRepTopProfInst) window.bizRepTopProfInst.destroy();
    window.bizRepTopProfInst = new Chart(document.getElementById('repTopProfChart'), { type: 'bar', data: { labels: topProfNames, datasets: [{ label: 'Profit', data: topProfData, backgroundColor: '#3b82f6', borderRadius: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y', scales: { x: { display: false } } } });

    if (window.bizRepDonutChartInst) window.bizRepDonutChartInst.destroy();
    if (topCats.length > 0) {
        window.bizRepDonutChartInst = new Chart(document.getElementById('repExpDonutChart'), {
            type: 'doughnut', data: { labels: topCats.map(c => c[0]), datasets: [{ data: topCats.map(c => c[1]), backgroundColor: ['#f43f5e', '#f59e0b', '#8b5cf6', '#3b82f6'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    }

    if (window.bizRepMarginChartInst) window.bizRepMarginChartInst.destroy();
    let mU = 0, mT = 0, mTw = 0, mO = 0;
    if (profitData && profitData.products) {
        profitData.products.forEach(p => { if (p.margin < 10) mU++; else if (p.margin < 20) mT++; else if (p.margin < 30) mTw++; else mO++; });
    }
    window.bizRepMarginChartInst = new Chart(document.getElementById('repMarginDistChart'), { type: 'doughnut', data: { labels: ['<10%', '10-20%', '20-30%', '>30%'], datasets: [{ data: [mU, mT, mTw, mO], backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { family: "'Inter',sans-serif", size: 11 } } } } } });
}

// Layer 4: Customers
function _repRenderCustomerLayer(container) {
    const { bizId, sales } = window._repData;
    const { start, end } = repGetDateRange();
    const curSales = _repFilterByBizAndDate(sales, bizId, start, end, 'created_at');

    let c = 0, q = 0, t = 0;
    curSales.forEach(s => {
        if (s.payment_method === 'cash') c++;
        else if (s.payment_method === 'qris') q++;
        else t++;
    });

    container.innerHTML = `
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 380px), 1fr)); gap:20px;">
        <div class="biz-card">
            <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-users" style="color:var(--biz-primary)"></i> Customer Intelligence</div></div>
            <div style="display:flex;gap:8px;margin-top:12px;padding:0 10px 10px 10px;flex-wrap:wrap">
                <div style="flex:1;min-width:90px;background:var(--biz-surface-2);border-radius:12px;padding:10px;text-align:center">
                    <div style="font-size:10px;font-weight:800;color:var(--biz-text-dim);margin-bottom:4px">CASH</div>
                    <div style="font-size:20px;font-weight:800">${c} <span style="font-size:11px;font-weight:700;color:var(--biz-text-dim)">Transaksi</span></div>
                </div>
                <div style="flex:1;min-width:90px;background:var(--biz-surface-2);border-radius:12px;padding:10px;text-align:center">
                    <div style="font-size:10px;font-weight:800;color:var(--biz-text-dim);margin-bottom:4px">QRIS</div>
                    <div style="font-size:20px;font-weight:800;color:var(--biz-primary)">${q} <span style="font-size:11px;font-weight:700;color:var(--biz-text-dim)">Transaksi</span></div>
                </div>
                <div style="flex:1;min-width:90px;background:var(--biz-surface-2);border-radius:12px;padding:10px;text-align:center">
                    <div style="font-size:10px;font-weight:800;color:var(--biz-text-dim);margin-bottom:4px">TRANSFER</div>
                    <div style="font-size:20px;font-weight:800;color:var(--biz-success)">${t} <span style="font-size:11px;font-weight:700;color:var(--biz-text-dim)">Transaksi</span></div>
                </div>
            </div>
        </div>
        <div class="biz-card">
            <div class="biz-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                <div class="biz-card-title"><i class="fas fa-heart" style="color:var(--biz-danger)"></i> Customer Loyalty Frequency</div>
            </div>
            <div style="height:120px;position:relative;padding:10px"><canvas id="repLoyaltyChart"></canvas></div>
        </div>
    </div>`;

    // Calculate customer frequency
    const custMap = {}; curSales.forEach(s => { if (s.customer_name && s.customer_name !== 'Umum') custMap[s.customer_name] = (custMap[s.customer_name] || 0) + 1; });
    let freq1 = 0, freq2 = 0, freq3 = 0;
    Object.values(custMap).forEach(v => { if (v === 1) freq1++; else if (v === 2) freq2++; else freq3++; });
    if (freq1 === 0 && freq2 === 0 && freq3 === 0) freq1 = c + q + t; // fallback if no names

    if (window.bizRepFreqChartInst) window.bizRepFreqChartInst.destroy();
    window.bizRepFreqChartInst = new Chart(document.getElementById('repLoyaltyChart'), {
        type: 'pie', data: { labels: ['1 Order', '2 Orders', '3+ Orders'], datasets: [{ data: [freq1, freq2, freq3], backgroundColor: ['#64748b', '#3b82f6', '#8b5cf6'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });
}

// Layer 5: Inventory
function _repRenderInventoryLayer(container) {
    const { inventoryData } = window._repData;
    if (!inventoryData || !inventoryData.burnRates) {
        container.innerHTML = '<div class="biz-card"><div class="biz-empty">Inventori data tidak tersedia.</div></div>';
        return;
    }

    const { burnRates } = inventoryData;
    const fast = burnRates.filter(p => typeof p.daysLeft === 'number' && p.daysLeft < 5 && p.stock > 0).length;
    const dead = burnRates.filter(p => p.daysLeft === '90+' && p.stock > 0).length;

    container.innerHTML = `<div class="biz-card">
        <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-boxes-packing" style="color:var(--biz-primary)"></i> Inventory Turnover</div></div>
        <div style="margin-top:12px">
             <div style="display:flex;justify-content:space-between;padding:12px;border:1px solid rgba(16,185,129,0.2);border-radius:8px;background:rgba(16,185,129,0.05);margin-bottom:8px">
                 <span style="font-weight:700;color:var(--biz-success)"><i class="fas fa-fire"></i> Fast Moving</span>
                 <span style="font-weight:800">${fast} Produk</span>
             </div>
             <div style="display:flex;justify-content:space-between;padding:12px;border:1px solid rgba(239,68,68,0.2);border-radius:8px;background:rgba(239,68,68,0.05)">
                 <span style="font-weight:700;color:var(--biz-danger)"><i class="fas fa-skull"></i> Dead Stock</span>
                 <span style="font-weight:800">${dead} Produk</span>
             </div>
        </div>
    </div>`;
}

// Layer 6: Cashflow
function _repRenderCashflowLayer(container) {
    const { bizId, sales, expenses } = window._repData;
    const { start, end } = repGetDateRange();

    const curSales = _repFilterByBizAndDate(sales, bizId, start, end, 'created_at');
    const curExp = _repFilterByBizAndDate(expenses, bizId, start, end, 'expense_date');

    let inFlow = curSales.reduce((a, b) => a + b.total_amount, 0);
    let outFlow = curExp.reduce((a, b) => a + b.amount, 0);

    container.innerHTML = `<div class="biz-card">
        <div class="biz-card-header"><div class="biz-card-title"><i class="fas fa-water" style="color:var(--biz-primary)"></i> Cashflow Balance (${window.bizState.analyticsRange.toUpperCase()})</div></div>
        <div style="height:120px;position:relative"><canvas id="repCashflowChart"></canvas></div>
    </div>`;

    const canvas = document.getElementById('repCashflowChart');
    if (!canvas) return;

    if (window.bizRepCashflowChartInst) window.bizRepCashflowChartInst.destroy();
    window.bizRepCashflowChartInst = new Chart(canvas, {
        type: 'bar',
        data: { labels: ['Inflow (Masuk)', 'Outflow (Keluar)'], datasets: [{ data: [inFlow, outFlow], backgroundColor: ['#10b981', '#ef4444'], borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, indexAxis: 'y' }
    });
}

window.bizLoadReports = bizLoadReports;
window.repSetFilter = repSetFilter;
