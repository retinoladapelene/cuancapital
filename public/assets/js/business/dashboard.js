/**
 * Business Manager — dashboard.js
 * KPI Cards, Sparkline, Top Products, Quick Actions, Intelligence widgets
 */

async function bizLoadDashboard() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const bizId = window.bizState.businessId;

    container.innerHTML = `<div class="biz-page" id="biz-dash-page">
        <!-- Dashboard Header -->
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px">
            <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Hari Ini</h2>
            <div style="font-size:13px;color:var(--biz-text-muted);font-weight:600" id="dash-date">${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</div>
        </div>

        <!-- Big Numbers (Today) -->
        <div class="biz-row" id="dash-kpi-grid" style="margin-bottom:16px;gap:8px">
            <!-- Injected via JS -->
            <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div>
        </div>

        <!-- Top Product & Health -->
        <div class="biz-row" style="flex-wrap:nowrap;gap:8px;margin-bottom:20px">
            <div id="dash-health-card" class="biz-card" style="flex:1;min-width:130px;padding:14px;text-align:center">
                <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:6px">Health Score</div>
                <div class="biz-health-score-num" style="font-size:30px" id="dash-health-num">—</div>
                <div id="dash-health-status" style="font-size:11px;font-weight:700;margin-top:2px">—</div>
            </div>

            <div class="biz-card" style="flex:1.5;min-width:160px;padding:14px;display:flex;flex-direction:column;justify-content:center">
                <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:6px"><i class="fas fa-crown" style="color:#f59e0b"></i> Top Product</div>
                <div id="dash-top-product-highlight">
                    <div style="font-size:14px;font-weight:700;color:var(--biz-text)">—</div>
                    <div style="font-size:11px;color:var(--biz-text-muted)">Sold: 0</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="biz-quick-actions" id="dash-quick-actions" style="margin-bottom:20px">
            <button class="biz-quick-btn" onclick="bizOpenModal('biz-modal-quick-sale')">
                <i class="fas fa-bolt" style="color:var(--biz-warning)"></i> + Sale
            </button>
            <button class="biz-quick-btn" onclick="bizOpenModal('biz-modal-expense')">
                <i class="fas fa-arrow-trend-down" style="color:var(--biz-danger)"></i> + Expense
            </button>
            <button class="biz-quick-btn" onclick="bizOpenModal('biz-modal-product'); setTimeout(()=>document.getElementById('prod-name').focus(),200)">
                <i class="fas fa-box" style="color:var(--biz-primary)"></i> + Product
            </button>
        </div>

        <!-- Smart AI Insights (Insight Feed) -->
        <div class="biz-card" style="margin-bottom:20px;border:1px solid var(--biz-border-strong);border-bottom:3px solid var(--biz-success)">
            <div class="biz-card-header" style="margin-bottom:12px">
                <div class="biz-card-title"><i class="fas fa-wand-magic-sparkles" style="color:var(--biz-success)"></i> Business Insight</div>
            </div>
            <div id="dash-insight-feed" style="display:flex;flex-direction:column;gap:10px;">
                <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Analisa berjalan...</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="biz-chart-wrap" id="dash-chart-section">
            <div class="biz-card-header" style="margin-bottom:0">
                <div class="biz-card-title"><i class="fas fa-chart-area" style="color:var(--biz-primary)"></i> Revenue 7 Hari</div>
            </div>
            <div id="dash-chart-container">
                <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div>
            </div>
            <div id="dash-chart-labels"></div>
        </div>
        
        <!-- Recent Sales -->
        <div class="biz-card" style="margin-top:16px">
            <div class="biz-card-header">
                <div class="biz-card-title">Penjualan Terbaru</div>
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
    const marginToday = revenueToday > 0 ? ((profitToday / revenueToday) * 100).toFixed(0) : 0;

    // KPI Big Numbers (Today) - 3 Columns
    document.getElementById('dash-kpi-grid').innerHTML = `
        <div class="biz-card" style="flex:1.5; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Revenue</div>
            <div style="font-size:20px;font-weight:800;color:var(--biz-text);letter-spacing:-0.5px;max-width:100%">${bizRp(revenueToday)}</div>
        </div>
        <div class="biz-card" style="flex:1.5; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Profit</div>
            <div style="font-size:20px;font-weight:800;color:var(--biz-success);letter-spacing:-0.5px">${bizRp(profitToday)}</div>
        </div>
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center; background:var(--biz-surface-2)">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Margin</div>
            <div style="font-size:20px;font-weight:800;color:var(--biz-primary)">${marginToday}%</div>
        </div>`;

    // Intelligence — Health Score
    if (typeof bizHealthScore === 'function') {
        const { score, status, color } = bizHealthScore(snapshots, bizId);
        document.getElementById('dash-health-num').textContent = score;
        document.getElementById('dash-health-status').textContent = status;
        document.getElementById('dash-health-status').style.color = color;
    }


    // Chart — 14 day sparkline
    _dashRenderChart(snapshots, bizId);

    // Top Products
    _dashRenderTopProducts(saleItems, monthKey);

    // Recent Sales
    _dashRenderRecentSales(sales);

    // Backup reminder
    if (typeof bizCheckBackupReminder === 'function') bizCheckBackupReminder('dash-backup-reminder');

    // Load AI Advisor Insights
    if (typeof bizGenerateInsights === 'function') {
        const insights = await bizGenerateInsights(bizId);
        const ifeed = document.getElementById('dash-insight-feed');
        if (ifeed) {
            ifeed.innerHTML = insights.map(ins => `
                <div style="display:flex;gap:12px;align-items:flex-start;padding:12px 14px;background:var(--biz-${ins.type}-bg, rgba(0,0,0,0.03));border:1px solid rgba(0,0,0,0.05);border-radius:12px">
                    <div style="color:var(--biz-${ins.type});margin-top:2px"><i class="fas ${ins.icon}"></i></div>
                    <div style="flex:1;font-size:13px;line-height:1.45;color:var(--biz-text)">${ins.text}</div>
                </div>
            `).join('');
        }
    }
}

function _dashKpiSkeleton() {
    return ['revenue', 'profit', 'orders', 'expense'].map(k =>
        `<div class="biz-kpi-card ${k}">
            <div class="biz-kpi-label">—</div>
            <div class="biz-kpi-value" style="color:var(--biz-border)">Rp 0</div>
        </div>`).join('');
}

function _dashRenderChart(snapshots, bizId) {
    // Get last 7 days
    const days = Array.from({ length: 7 }, (_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - (6 - i));
        return d.toISOString().split('T')[0];
    });
    const values = days.map(d => {
        const snap = snapshots.find(s => s.snapshot_date === d && s.business_id === bizId);
        return snap ? (snap.revenue || 0) : 0;
    });
    const max = Math.max(...values, 1);

    const formatNum = (v) => {
        if (v === 0) return '';
        if (v >= 1000000) return (v / 1000000).toFixed(1).replace('.0', '') + 'M';
        if (v >= 1000) return (v / 1000).toFixed(0) + 'K';
        return v;
    };

    const formatDay = (dStr) => {
        const d = new Date(dStr);
        return d.toLocaleDateString('id-ID', { weekday: 'short' });
    };

    document.getElementById('dash-chart-container').innerHTML = `
        <div style="display:flex;align-items:flex-end;justify-content:space-between;height:100px;padding-top:15px;gap:6px">
            ${values.map((v, i) => {
        const h = Math.max((v / max) * 100, 4); // min 4%
        return `
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%">
                    <div style="font-size:10px;font-weight:700;color:var(--biz-text-muted);margin-bottom:6px;letter-spacing:-0.5px">${formatNum(v)}</div>
                    <div style="width:100%;max-width:32px;background:var(--biz-primary);border-radius:6px 6px 0 0;height:${h}%;opacity:${i === 6 ? 1 : 0.6};transition:all 0.3s ease"></div>
                </div>`;
    }).join('')}
        </div>
    `;

    document.getElementById('dash-chart-labels').innerHTML = `
        <div style="display:flex;justify-content:space-between;margin-top:8px">
            ${days.map(d => `<div style="flex:1;text-align:center;font-size:10px;color:var(--biz-text-dim);font-weight:600">${formatDay(d)}</div>`).join('')}
        </div>
    `;
}

async function _dashRenderTopProducts(saleItems, monthKey) {
    const el = document.getElementById('dash-top-product-highlight');
    if (!el) return;

    const monthItems = saleItems.filter(si => si.created_at && si.created_at.startsWith(monthKey));
    const freq = {};
    monthItems.forEach(si => {
        if (!freq[si.product_id]) freq[si.product_id] = { name: si.product_name, qty: 0 };
        freq[si.product_id].qty += si.quantity;
    });

    const sorted = Object.values(freq).sort((a, b) => b.qty - a.qty);
    if (!sorted.length) {
        el.innerHTML = `<div style="font-size:16px;font-weight:700;color:var(--biz-text)">—</div>
                        <div style="font-size:12px;color:var(--biz-text-muted)">Belum ada data</div>`;
        return;
    }

    const top = sorted[0];
    el.innerHTML = `<div style="font-size:16px;font-weight:700;color:var(--biz-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${_esc(top.name)}">${_esc(top.name)}</div>
                    <div style="font-size:12px;color:var(--biz-text-muted);font-weight:600">Sold: <span style="color:var(--biz-primary)">${top.qty}</span></div>`;
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
