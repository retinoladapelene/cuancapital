/**
 * Business Manager — dashboard.js (Phase 13: Global Command Dashboard)
 * Mission Control: 4-Zone Responsive BI Dashboard
 */

async function bizLoadDashboard() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const bizId = window.bizState.businessId;

    // Phase 13 Global Command Structure (Mobile-First 12-Column Grid)
    container.innerHTML = `
    <div class="biz-page" id="biz-dash-page">
        <!-- Dashboard Header -->
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Business Command</h2>
                <div style="font-size:13px;color:var(--biz-text-muted);font-weight:600" id="dash-date">${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</div>
            </div>
            <div class="biz-quick-actions" style="display:flex; gap:8px;">
                <button class="biz-btn biz-btn-primary" style="padding:6px 12px; font-size:12px" onclick="bizOpenModal('biz-modal-quick-sale')"><i class="fas fa-plus"></i> Sale</button>
            </div>
        </div>

        <!-- 12-Column Main Grid Wrapper -->
        <div style="display:grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap:16px;">
            
            <!-- ZONE 1: Strategic Business Pulse (KPI Cards) -->
            <!-- Mobile: span 12 (1 col within), Tablet: span 12 (2 col within), Desktop: spans 3 per card manually or flex -->
            <div style="grid-column: span 12; display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 140px), 1fr)); gap:12px;" id="dash-zone1-kpi">
                ${_dashKpiSkeleton()}
            </div>

            <!-- ZONE 2 & 3 CONTAINER: Radar + Pulse -->
            <div style="grid-column: span 12; display:grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap:16px;" class="dash-mid-grid">
                
                <!-- ZONE 3: Profit Radar (Signature Feature) -->
                <!-- Desktop: 5 cols. Tablet/Mobile: 12 cols -->
                <div class="dash-radar-col" style="grid-column: span 12;">
                    <div class="biz-card" style="height:100%; border-top: 3px solid var(--biz-primary)">
                        <div class="biz-card-header" style="padding-bottom:0">
                            <div class="biz-card-title"><i class="fas fa-satellite-dish" style="color:var(--biz-primary)"></i> Profit Radar</div>
                            <div id="dash-radar-score" style="font-size:12px; font-weight:700; background:var(--biz-surface-2); padding:2px 8px; border-radius:12px">—</div>
                        </div>
                        <div style="padding:16px; display:flex; justify-content:center; align-items:center; min-height:220px;">
                            <canvas id="dashRadarChart" style="max-height:240px; width:100%"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ZONE 4 & 2: AI CFO Insight + Live Pulse -->
                <!-- Desktop: 7 cols. Tablet/Mobile: 12 cols -->
                <div class="dash-ai-col" style="grid-column: span 12; display:flex; flex-direction:column; gap:16px;">
                    
                    <!-- ZONE 2: Live Business Pulse -->
                    <div class="biz-card" style="background:var(--biz-surface-2)">
                        <div style="padding:12px 16px; display:flex; justify-content:space-between; align-items:center">
                            <div style="display:flex; align-items:center; gap:8px">
                                <div style="width:8px; height:8px; border-radius:50%; background:var(--biz-danger); box-shadow:0 0 8px var(--biz-danger); animation:pulse 1.5s infinite"></div>
                                <div style="font-size:12px; font-weight:700; color:var(--biz-text-dim); text-transform:uppercase">Live: Last 60 Mins</div>
                            </div>
                            <div id="dash-zone2-live" style="display:flex; gap:16px; font-weight:700; font-size:14px">
                                <div><i class="fas fa-shopping-bag" style="color:var(--biz-text-muted)"></i> <span id="dash-live-qty">-</span></div>
                                <div><i class="fas fa-sack-dollar" style="color:var(--biz-success)"></i> <span id="dash-live-rev">-</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- ZONE 4: AI CFO Panel -->
                    <div class="biz-card" style="flex:1; border:1px solid var(--biz-border-strong); border-bottom:3px solid var(--biz-success)">
                        <div class="biz-card-header" style="margin-bottom:8px">
                            <div class="biz-card-title"><i class="fas fa-robot" style="color:var(--biz-success)"></i> AI CFO Insights</div>
                        </div>
                        <div id="dash-zone4-insights" style="display:flex; flex-direction:column; gap:8px; padding:0 16px 16px 16px;">
                            <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Analyzing Business Health...</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ZONE 5: Historical Trends (Bottom) -->
            <div style="grid-column: span 12; margin-top:8px;">
                <div class="biz-card">
                    <div class="biz-card-header">
                        <div class="biz-card-title">Penjualan Terbaru</div>
                        <button class="biz-card-action" onclick="bizSwitchTab('sales')">Semua →</button>
                    </div>
                    <div id="dash-recent-sales"><div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
                </div>
            </div>
            
        </div>
    </div>`;

    // Simple inline style to handle desktop breakpoints without muddying business-core.css
    if (!document.getElementById('dash-grid-styles')) {
        const style = document.createElement('style');
        style.id = 'dash-grid-styles';
        style.innerHTML = `
            @media (min-width: 1024px) {
                .dash-radar-col { grid-column: span 5 !important; }
                .dash-ai-col { grid-column: span 7 !important; }
            }
            @keyframes pulse { 0% { opacity:0.5; transform:scale(0.8) } 50% { opacity:1; transform:scale(1.2) } 100% { opacity:0.5; transform:scale(0.8) } }
        `;
        document.head.appendChild(style);
    }

    // Load data in parallel using the Intelligence engines
    const [salesIntel, healthRadar, cfoInsights, sales] = await Promise.all([
        typeof bizSalesIntelligence === 'function' ? bizSalesIntelligence(bizId) : null,
        typeof bizHealthScoreAnalytics === 'function' ? bizHealthScoreAnalytics(bizId) : null,
        typeof bizGenerateGlobalInsights === 'function' ? bizGenerateGlobalInsights(bizId) : null,
        BizDB.sales.getAll()
    ]);

    // ZONE 1: Strategic Pulse
    if (salesIntel) {
        // Find cash safely
        const snaps = await BizDB.finSnapshots.getAll();
        const todaySnap = snaps.find(s => s.snapshot_date === _fmtDate(new Date()) && s.business_id === bizId) || {};

        // Use sales intel for highly accurate 30day rolling window, mapped to UI
        const rev30 = salesIntel.overview.rev30 || 0;
        const prof30 = salesIntel.overview.profit30 || 0; // Approximated from sales if needed, but snapshots are better for net
        const ords = salesIntel.overview.orders || 0;
        const cashBal = todaySnap.cash_balance || 0;

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

    // ZONE 3: Profit Radar
    if (healthRadar) {
        const sEl = document.getElementById('dash-radar-score');
        sEl.textContent = `${healthRadar.score} — ${healthRadar.status}`;
        if (healthRadar.score >= 80) { sEl.style.color = 'var(--biz-primary)'; sEl.style.background = 'var(--biz-primary-light)'; }
        else if (healthRadar.score < 50) { sEl.style.color = 'var(--biz-danger)'; sEl.style.background = 'rgba(239, 68, 68, 0.1)'; }

        _dashRenderRadarChart(healthRadar.axes);
    } else {
        document.getElementById('dashRadarChart').parentElement.innerHTML = '<div class="biz-empty" style="padding:20px; text-align:center"><i class="fas fa-chart-pie"></i><br>Belum cukup data untuk Radar</div>';
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

// Ensure the chart is painted after the canvas is physically inside the DOM flow
function _dashRenderRadarChart(axes) {
    if (typeof Chart === 'undefined') return;
    const ctx = document.getElementById('dashRadarChart');
    if (!ctx) return;

    const dataArr = [axes.revenue, axes.profit, axes.inventory, axes.cashflow, axes.customer];
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const brandColor = '#3b82f6'; // primary

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Rev Growth', 'Margin', 'Inv Health', 'Cashflow', 'Loyalty'],
            datasets: [{
                label: 'Business Vitals',
                data: dataArr,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: brandColor,
                pointBackgroundColor: brandColor,
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: brandColor,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: gridColor },
                    grid: { color: gridColor },
                    pointLabels: {
                        color: textColor,
                        font: { size: 10, weight: 'bold', family: 'Inter' }
                    },
                    ticks: {
                        display: false, // hide the internal numbers (0..100) 
                        min: 0,
                        max: 100,
                        stepSize: 25
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? 'rgba(30,41,59,0.9)' : 'rgba(255,255,255,0.9)',
                    titleColor: isDark ? '#fff' : '#000',
                    bodyColor: isDark ? '#cbd5e1' : '#334155',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: { label: (ctx) => `Score: ${ctx.raw}/100` }
                }
            }
        }
    });
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
