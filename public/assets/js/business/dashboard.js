/**
 * Business Manager — dashboard.js
 * KPI Cards, Sparkline, Top Products, Quick Actions, Intelligence widgets
 */

async function bizLoadDashboard() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const bizId = window.bizState.businessId;

    container.innerHTML = `<div class="biz-page" id="biz-dash-page">
        <!-- Quick Actions -->
        <div class="biz-quick-actions" id="dash-quick-actions">
            <button class="biz-quick-btn" onclick="bizOpenModal('biz-modal-quick-sale')">
                <i class="fas fa-bolt" style="color:var(--biz-warning)"></i>Quick Sale
            </button>
            <button class="biz-quick-btn" onclick="bizSwitchTab('sales')">
                <i class="fas fa-receipt" style="color:var(--biz-primary)"></i>Semua Sales
            </button>
            <button class="biz-quick-btn" onclick="bizOpenModal('biz-modal-expense')">
                <i class="fas fa-arrow-trend-down" style="color:var(--biz-danger)"></i>Catat Biaya
            </button>
        </div>

        <!-- KPI Cards -->
        <div class="biz-kpi-grid" id="dash-kpi-grid">
            ${_dashKpiSkeleton()}
        </div>

        <!-- Backup reminder (shown if needed) -->
        <div id="dash-backup-reminder" style="display:none"></div>

        <!-- Insight Box + Health Score row -->
        <div class="biz-row" style="flex-wrap:wrap;gap:12px;margin-bottom:14px">
            <div id="dash-health-card" class="biz-card" style="flex:1;min-width:140px;text-align:center">
                <div class="biz-card-title" style="justify-content:center;margin-bottom:12px"><i class="fas fa-heart-pulse" style="color:var(--biz-danger)"></i> Health</div>
                <div class="biz-health-score-num" id="dash-health-num">—</div>
                <div class="biz-health-status" id="dash-health-status" style="color:var(--biz-text-muted)">Menghitung...</div>
            </div>
            <div id="dash-forecast-card" class="biz-card" style="flex:1;min-width:140px">
                <div class="biz-card-title" style="margin-bottom:10px"><i class="fas fa-droplet" style="color:var(--biz-info)"></i> Cash Forecast</div>
                <div class="biz-forecast-runway" id="dash-forecast-runway">—</div>
                <div style="font-size:11px;color:var(--biz-text-muted);margin-top:4px" id="dash-forecast-label">Menghitung...</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="biz-chart-wrap" id="dash-chart-section">
            <div class="biz-card-header">
                <div class="biz-card-title"><i class="fas fa-chart-area" style="color:var(--biz-primary)"></i> Revenue 14 Hari</div>
            </div>
            <div id="dash-chart-container"><svg class="biz-sparkline-svg" style="opacity:.3"><text x="50%" y="50%" text-anchor="middle" fill="var(--biz-text-muted)" font-size="12">Memuat data...</text></svg></div>
            <div class="biz-chart-labels" id="dash-chart-labels"></div>
        </div>

        <!-- Top Products -->
        <div class="biz-card">
            <div class="biz-card-header">
                <div class="biz-card-title"><i class="fas fa-trophy" style="color:var(--biz-warning)"></i> Top Produk Bulan Ini</div>
                <button class="biz-card-action" onclick="bizSwitchTab('reports')">Lihat →</button>
            </div>
            <div id="dash-top-products"><div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
        </div>

        <!-- Recent Sales -->
        <div class="biz-card" style="margin-top:14px">
            <div class="biz-card-header">
                <div class="biz-card-title"><i class="fas fa-clock" style="color:var(--biz-primary)"></i> Penjualan Terbaru</div>
                <button class="biz-card-action" onclick="bizSwitchTab('sales')">Semua →</button>
            </div>
            <div id="dash-recent-sales"><div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
        </div>
    </div>`;

    // Update page title
    const pt = document.getElementById('biz-page-title');
    if (pt) pt.textContent = 'Dashboard';

    // Load data in parallel
    const today = bizToday();
    const [snapshots, sales, saleItems, biz] = await Promise.all([
        BizDB.finSnapshots.getAll(),
        BizDB.sales.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.businesses.getAll(),
    ]);

    // Business name in sidebar brand
    if (biz[0]) {
        const brandSub = document.querySelector('.biz-sidebar-brand-sub');
        if (brandSub) brandSub.textContent = biz[0].name;
    }

    // Filter today's + month's snapshots
    const monthKey = bizMonthKey();
    const todaySnap = snapshots.find(s => s.snapshot_date === today) || {};
    const monthSnaps = snapshots.filter(s => s.snapshot_date && s.snapshot_date.startsWith(monthKey));

    const revenueToday = todaySnap.revenue || 0;
    const profitToday = todaySnap.profit || 0;
    const ordersToday = todaySnap.orders_count || 0;
    const expToday = todaySnap.expenses || 0;

    // KPI
    document.getElementById('dash-kpi-grid').innerHTML = `
        <div class="biz-kpi-card revenue">
            <div class="biz-kpi-label"><i class="fas fa-arrow-trend-up"></i> Revenue Hari Ini</div>
            <div class="biz-kpi-value">${bizRp(revenueToday)}</div>
        </div>
        <div class="biz-kpi-card profit">
            <div class="biz-kpi-label"><i class="fas fa-sack-dollar"></i> Profit</div>
            <div class="biz-kpi-value" style="color:var(--biz-success)">${bizRp(profitToday)}</div>
        </div>
        <div class="biz-kpi-card orders">
            <div class="biz-kpi-label"><i class="fas fa-receipt"></i> Pesanan</div>
            <div class="biz-kpi-value">${ordersToday}</div>
        </div>
        <div class="biz-kpi-card expense">
            <div class="biz-kpi-label"><i class="fas fa-arrow-trend-down"></i> Pengeluaran</div>
            <div class="biz-kpi-value" style="color:var(--biz-danger)">${bizRp(expToday)}</div>
        </div>`;

    // Intelligence — Health Score
    if (typeof bizHealthScore === 'function') {
        const { score, status, color } = bizHealthScore(snapshots, bizId);
        document.getElementById('dash-health-num').textContent = score;
        document.getElementById('dash-health-status').textContent = status;
        document.getElementById('dash-health-status').style.color = color;
    }

    // Intelligence — Cashflow Forecast
    if (typeof bizCashForecast === 'function') {
        const forecast = bizCashForecast(snapshots);
        const fEl = document.getElementById('dash-forecast-runway');
        const lEl = document.getElementById('dash-forecast-label');
        if (forecast.netDaily >= 0) {
            fEl.textContent = '📈 Positif';
            fEl.className = 'biz-forecast-runway good';
            lEl.textContent = `Cashflow +${bizRp(forecast.netDaily)}/hari`;
        } else {
            const days = forecast.runway;
            fEl.textContent = days !== null ? `${days} hari` : '—';
            fEl.className = `biz-forecast-runway ${days === null ? '' : days > 30 ? 'good' : days > 14 ? 'caution' : 'danger'}`;
            lEl.textContent = days !== null ? 'Estimasi kas tahan' : 'Belum ada data';
        }
    }

    // Chart — 14 day sparkline
    _dashRenderChart(snapshots);

    // Top Products
    _dashRenderTopProducts(saleItems, monthKey);

    // Recent Sales
    _dashRenderRecentSales(sales);

    // Backup reminder
    if (typeof bizCheckBackupReminder === 'function') bizCheckBackupReminder('dash-backup-reminder');
}

function _dashKpiSkeleton() {
    return ['revenue', 'profit', 'orders', 'expense'].map(k =>
        `<div class="biz-kpi-card ${k}">
            <div class="biz-kpi-label">—</div>
            <div class="biz-kpi-value" style="color:var(--biz-border)">Rp 0</div>
        </div>`).join('');
}

function _dashRenderChart(snapshots) {
    // Get last 14 days
    const days = Array.from({ length: 14 }, (_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - (13 - i));
        return d.toISOString().split('T')[0];
    });
    const values = days.map(d => {
        const snap = snapshots.find(s => s.snapshot_date === d);
        return snap ? (snap.revenue || 0) : 0;
    });
    const max = Math.max(...values, 1);

    const w = 300, h = 70, pad = 4;
    const dx = (w - pad * 2) / (values.length - 1);
    const pts = values.map((v, i) => `${pad + i * dx},${h - pad - ((v / max) * (h - pad * 2))}`);

    document.getElementById('dash-chart-container').innerHTML = `
        <svg viewBox="0 0 ${w} ${h}" class="biz-sparkline-svg" style="height:70px">
            <defs>
                <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3"/>
                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                </linearGradient>
            </defs>
            <path d="M${pts.join(' L')} L${pad + (values.length - 1) * dx},${h} L${pad},${h} Z" fill="url(#chartGrad)"/>
            <polyline points="${pts.join(' ')}" fill="none" stroke="#6366f1" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
            <circle cx="${pts[pts.length - 1].split(',')[0]}" cy="${pts[pts.length - 1].split(',')[1]}" r="4" fill="#6366f1"/>
        </svg>`;

    document.getElementById('dash-chart-labels').innerHTML =
        `<span>${days[0].slice(5)}</span><span>${days[6].slice(5)}</span><span>${days[13].slice(5)}</span>`;
}

async function _dashRenderTopProducts(saleItems, monthKey) {
    const el = document.getElementById('dash-top-products');
    if (!el) return;

    const monthItems = saleItems.filter(si => {
        // sale_items have sale_id — need to check via sale; approximate by created_at
        return si.created_at && si.created_at.startsWith(monthKey);
    });

    const freq = {};
    monthItems.forEach(si => {
        if (!freq[si.product_id]) freq[si.product_id] = { name: si.product_name, qty: 0, profit: 0 };
        freq[si.product_id].qty += si.quantity;
        freq[si.product_id].profit += si.profit || 0;
    });

    const sorted = Object.values(freq).sort((a, b) => b.profit - a.profit).slice(0, 5);

    if (!sorted.length) {
        el.innerHTML = '<div class="biz-empty" style="padding:24px"><i class="fas fa-box-open"></i> Belum ada penjualan bulan ini</div>';
        return;
    }

    const rankClass = ['gold', 'silver', 'bronze'];
    el.innerHTML = sorted.map((p, i) => `
        <div class="biz-top-product-item">
            <div class="biz-rank ${rankClass[i] || ''}">${i + 1}</div>
            <div style="flex:1">
                <div class="biz-list-name">${_esc(p.name)}</div>
                <div class="biz-list-sub">${p.qty} terjual</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:13px;font-weight:700;color:var(--biz-success)">${bizRp(p.profit)}</div>
                <div class="biz-list-sub">profit</div>
            </div>
        </div>`).join('');
}

function _dashRenderRecentSales(sales) {
    const el = document.getElementById('dash-recent-sales');
    if (!el) return;
    const recent = sales.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5);
    if (!recent.length) {
        el.innerHTML = '<div class="biz-empty" style="padding:24px"><i class="fas fa-receipt"></i> Belum ada penjualan</div>';
        return;
    }
    el.innerHTML = recent.map(s => {
        const ago = _timeAgo(s.created_at);
        return `<div class="biz-list-item">
            <div class="biz-list-icon" style="background:var(--biz-primary-light)"><i class="fas fa-receipt"></i></div>
            <div class="biz-list-body">
                <div class="biz-list-name">#${s.id.slice(-6).toUpperCase()}</div>
                <div class="biz-list-sub">${ago} · ${_payLabel(s.payment_method)}</div>
            </div>
            <div class="biz-list-right">
                <div class="biz-list-amount">${bizRp(s.total_amount)}</div>
                <div class="biz-sale-profit">+${bizRp(s.total_profit)}</div>
            </div>
        </div>`;
    }).join('');
}

window.bizLoadDashboard = bizLoadDashboard;

// Helpers
function _esc(s) { return (s || '').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
function _payLabel(m) { return { cash: 'Cash', transfer: 'Transfer', qris: 'QRIS', other: 'Lainnya' }[m] || m || 'Cash'; }
function _timeAgo(iso) {
    const diff = Date.now() - new Date(iso);
    const m = Math.floor(diff / 60000);
    if (m < 1) return 'Baru saja';
    if (m < 60) return `${m} menit lalu`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h} jam lalu`;
    return `${Math.floor(h / 24)} hari lalu`;
}
window._esc = _esc;
window._payLabel = _payLabel;
window._timeAgo = _timeAgo;
