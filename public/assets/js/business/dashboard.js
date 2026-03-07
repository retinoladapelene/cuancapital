/**
 * Business Manager — dashboard.js (Phase 13: Global Command Dashboard)
 * Mission Control: 4-Zone Responsive BI Dashboard
 */

async function bizLoadDashboard() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const bizId = window.bizState.businessId;

    // Phase 13 Global Command Structure using Template Cloning
    const tpl = document.getElementById('tpl-dashboard-shell');
    if (tpl) {
        container.innerHTML = tpl.innerHTML;
    } else {
        container.innerHTML = '<div class="biz-empty">Template Dashboard tidak ditemukan.</div>';
        return;
    }

    // Simple inline style to handle desktop breakpoints without muddying business-core.css
    if (!document.getElementById('dash-grid-styles')) {
        const style = document.createElement('style');
        style.id = 'dash-grid-styles';
        style.innerHTML = `
            @media (min-width: 1024px) {
                .dash-ai-col { grid-column: span 12 !important; }
            }
            @keyframes pulse { 0% { opacity:0.5; transform:scale(0.8) } 50% { opacity:1; transform:scale(1.2) } 100% { opacity:0.5; transform:scale(0.8) } }
        `;
        document.head.appendChild(style);
    }

    // Load data in parallel using the Intelligence engines
    const [salesIntel, healthRadar, cfoInsights, sales, expenses] = await Promise.all([
        typeof bizSalesIntelligence === 'function' ? bizSalesIntelligence(bizId) : null,
        typeof bizHealthScoreAnalytics === 'function' ? bizHealthScoreAnalytics(bizId) : null,
        typeof bizGenerateGlobalInsights === 'function' ? bizGenerateGlobalInsights(bizId) : null,
        BizDB.sales.getAll(),
        BizDB.expenses ? BizDB.expenses.getAll() : Promise.resolve([])
    ]);

    // ZONE 1: Strategic Pulse
    if (salesIntel) {
        // Find cash safely by iterating all sales vs expenses for exact precision
        let totalCashBal = 0;
        sales.forEach(s => { if (s.business_id === bizId && s.status !== 'cancelled') totalCashBal += (s.final_total || s.total_amount || 0); });
        expenses.forEach(e => { if (e.business_id === bizId) totalCashBal -= (e.amount || 0); });

        // Use sales intel for highly accurate 30day rolling window, mapped to UI
        const rev30 = salesIntel.overview.rev30 || 0;
        const prof30 = salesIntel.overview.profit30 || 0;
        const ords = salesIntel.overview.ord30 || 0;
        const cashBal = totalCashBal;

        // Determine profit safely
        let netProfit30 = prof30;
        if (typeof bizCalculateProfitMargin === 'function') {
            const pm = await bizCalculateProfitMargin(bizId);
            netProfit30 = pm.netProfit || prof30;
        }

        document.getElementById('dash-zone1-kpi').innerHTML = `
            <div class="biz-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center">
                <div style="font-size:11px; font-weight:700; color:var(--biz-text-muted); text-transform:uppercase; margin-bottom:4px">Revenue (30D)</div>
                <div style="font-size:22px; font-weight:800; color:var(--biz-text); letter-spacing:-0.5px" id="dash-kpi-rev">0</div>
                <div style="font-size:11px; font-weight:600; color:${salesIntel.overview.revGrowth >= 0 ? 'var(--biz-success)' : 'var(--biz-danger)'}; margin-top:4px">
                    <i class="fas ${salesIntel.overview.revGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'}"></i> ${Math.abs(salesIntel.overview.revGrowth || 0).toFixed(1)}% vs Last Mo
                </div>
            </div>
            <div class="biz-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center">
                <div style="font-size:11px; font-weight:700; color:var(--biz-text-muted); text-transform:uppercase; margin-bottom:4px">Profit (30D)</div>
                <div style="font-size:22px; font-weight:800; color:var(--biz-success); letter-spacing:-0.5px" id="dash-kpi-prof">0</div>
            </div>
            <div class="biz-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center">
                <div style="font-size:11px; font-weight:700; color:var(--biz-text-muted); text-transform:uppercase; margin-bottom:4px">Total Orders</div>
                <div style="font-size:22px; font-weight:800; color:var(--biz-text); letter-spacing:-0.5px" id="dash-kpi-ord">0</div>
            </div>
            <div class="biz-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center">
                <div style="font-size:11px; font-weight:700; color:var(--biz-text-muted); text-transform:uppercase; margin-bottom:4px">Cash Balance</div>
                <div style="font-size:22px; font-weight:800; color:var(--biz-text); letter-spacing:-0.5px" id="dash-kpi-cash">0</div>
            </div>
        `;

        // Execute Micro-Interaction Counting Animation
        if (typeof bizAnimateValue === 'function') {
            bizAnimateValue(document.getElementById('dash-kpi-rev'), 0, rev30, 800, true);
            bizAnimateValue(document.getElementById('dash-kpi-prof'), 0, netProfit30, 800, true);
            bizAnimateValue(document.getElementById('dash-kpi-ord'), 0, ords, 800, false);
            bizAnimateValue(document.getElementById('dash-kpi-cash'), 0, cashBal, 800, true);
        } else {
            document.getElementById('dash-kpi-rev').textContent = bizRp(rev30);
            document.getElementById('dash-kpi-prof').textContent = bizRp(netProfit30);
            document.getElementById('dash-kpi-ord').textContent = ords;
            document.getElementById('dash-kpi-cash').textContent = bizRp(cashBal);
        }

        // ZONE 2: Live Pulse
        const liveQty = document.getElementById('dash-live-qty');
        const liveRev = document.getElementById('dash-live-rev');
        if (typeof bizAnimateValue === 'function') {
            bizAnimateValue(liveQty, 0, salesIntel.realtime.ord60m, 600, false);
            bizAnimateValue(liveRev, 0, salesIntel.realtime.rev60m, 600, true);
        } else {
            liveQty.textContent = salesIntel.realtime.ord60m;
            liveRev.textContent = bizRpFull(salesIntel.realtime.rev60m);
        }
    }



    // ZONE 4: AI CFO Insights
    const ifeed = document.getElementById('dash-zone4-insights');
    if (ifeed && cfoInsights) {
        if (cfoInsights.length === 0) {
            ifeed.innerHTML = '<div class="biz-empty" style="font-size:12px; margin-top:10px">Belum ada insight bisnis dari AI. Catat penjualan untuk mulai!</div>';
        } else {
            ifeed.innerHTML = cfoInsights.map(ins => `
                <div style="display:flex; gap:12px; align-items:flex-start; padding:12px 14px; background:var(--biz-surface-2); border:1px solid rgba(0,0,0,0.05); border-radius:12px">
                    <div style="font-size:16px; margin-top:0px">${ins.icon}</div>
                    <div style="flex:1; font-size:12px; line-height:1.45; color:var(--biz-text); font-weight:500">${ins.text}</div>
                </div>
            `).join('');
        }
    }

    // ZONE 5: Recent Sales
    _dashRenderRecentSales(sales);
}



function _dashKpiSkeleton() {
    return Array(4).fill().map(() => `
        <div class="biz-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center; opacity:0.6">
            <div style="height:12px; width:50%; background:var(--biz-border); border-radius:4px; margin-bottom:8px"></div>
            <div style="height:24px; width:80%; background:var(--biz-border); border-radius:4px"></div>
        </div>
    `).join('');
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
function _fmtDate(d) { return d.toISOString().split('T')[0]; }

window.bizLoadDashboard = bizLoadDashboard;
window._timeAgo = _timeAgo;
window._payLabel = _payLabel;
