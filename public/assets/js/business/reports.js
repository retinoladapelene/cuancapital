/**
 * Business Manager — reports.js
 * Revenue/profit bar chart, expense breakdown, product performance table
 */

async function bizLoadReports() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    // Show loading skeleton
    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i></div></div>`;

    const businessId = window.UserBusiness?.id || 'TEST_BIZ_1';
    const monthKey = bizMonthKey();

    // Fetch all insights in parallel
    const [snapshots, profitData, momentumData, timeData, leakData, invData] = await Promise.all([
        BizDB.finSnapshots.getAll(),
        bizProfitAnalyzer(businessId, monthKey),
        bizProductMomentum(businessId),
        bizBestSalesTime(businessId),
        bizExpenseLeakDetector(businessId),
        bizInventoryHealth(businessId)
    ]);

    const health = bizHealthScore(snapshots, businessId);

    // Build Profit Radar UI
    let profitHtml = '';
    let lossHtml = '';
    if (profitData && profitData.products) {
        const topProfitable = profitData.products.filter(p => p.margin >= 10).slice(0, 3);
        const lossProducts = profitData.products.filter(p => p.margin < 0).slice(0, 3);

        if (topProfitable.length) {
            profitHtml = topProfitable.map((p, i) => `
                <div class="biz-list-item">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div class="biz-rank ${i === 0 ? 'gold' : i === 1 ? 'silver' : 'bronze'}">${i + 1}</div>
                        <span style="font-weight:600;font-size:13px">${_esc(p.name)}</span>
                    </div>
                    <div style="text-align:right">
                        <div style="color:var(--biz-success);font-weight:800;font-size:13px">+${bizRp(p.profit)}</div>
                    </div>
                </div>
            `).join('');
        } else {
            profitHtml = `<div class="biz-empty" style="padding:16px"><i class="fas fa-box-open"></i><br>Belum ada data profit</div>`;
        }

        if (lossProducts.length) {
            lossHtml = `
            <div class="biz-card" style="margin-bottom:14px;border:1px solid rgba(239, 68, 68, 0.3)">
                <div class="biz-card-title" style="margin-bottom:12px;color:var(--biz-danger)"><i class="fas fa-triangle-exclamation"></i> Loss Product (Rugi)</div>
                ${lossProducts.map(p => `
                    <div class="biz-list-item">
                        <span style="font-weight:600;font-size:13px">${_esc(p.name)}</span>
                        <div style="color:var(--biz-danger);font-weight:800;font-size:13px">${bizRp(p.profit)}</div>
                    </div>
                `).join('')}
            </div>`;
        }
    }

    // Build Momentum UI
    let momentumHtml = '';
    if (momentumData && momentumData.trending.length) {
        momentumHtml = `
        <div class="biz-card" style="margin-bottom:14px">
            <div class="biz-card-title" style="margin-bottom:12px"><i class="fas fa-arrow-trend-up" style="color:var(--biz-primary)"></i> Trending Products</div>
            ${momentumData.trending.slice(0, 3).map(p => `
                <div class="biz-list-item">
                    <span style="font-weight:600;font-size:13px">${_esc(p.name)}</span>
                    <div style="color:var(--biz-success);font-weight:700;font-size:12px">Naik ${p.growth.toFixed(0)}%</div>
                </div>
            `).join('')}
        </div>`;
    }

    // Build Peak Hour UI
    let timeHtml = '';
    if (timeData && timeData.maxSales > 0) {
        timeHtml = `
        <div class="biz-card" style="margin-bottom:14px;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div style="font-size:12px;color:var(--biz-text-muted);font-weight:600;margin-bottom:4px">Best Sales Time</div>
                <div style="font-size:18px;font-weight:800;color:var(--biz-primary)">${timeData.timeLabel}</div>
            </div>
            <i class="fas fa-clock" style="font-size:24px;color:var(--biz-border-strong)"></i>
        </div>`;
    }

    container.innerHTML = `<div class="biz-page">

        <!-- Business Health Score -->
        <div class="biz-card biz-health-card" style="margin-bottom:14px">
            <div style="font-size:12px;font-weight:700;color:var(--biz-text-muted);margin-bottom:8px">Business Health Score</div>
            <div class="biz-health-score-num">${health.score} <span style="font-size:16px;color:var(--biz-text-muted)">/ 100</span></div>
            <div class="biz-health-status" style="color:${health.color}">${health.status}</div>
        </div>

        <!-- Profit Radar -->
        <div class="biz-card" style="margin-bottom:14px">
            <div class="biz-card-title" style="margin-bottom:12px"><i class="fas fa-radar" style="color:var(--biz-success)"></i> Most Profitable Product</div>
            <div style="display:flex;flex-direction:column">
                ${profitHtml}
            </div>
        </div>

        ${lossHtml}
        ${momentumHtml}
        ${timeHtml}

        <!-- Inventory Health Score -->
        <div class="biz-card" style="margin-bottom:14px;text-align:center">
            <div style="font-size:12px;font-weight:700;color:var(--biz-text-muted);margin-bottom:8px">Inventory Health</div>
            <div style="font-size:32px;font-weight:800;color:${invData.score >= 80 ? 'var(--biz-success)' : invData.score >= 50 ? 'var(--biz-warning)' : 'var(--biz-danger)'}">${invData.score} <span style="font-size:14px;color:var(--biz-text-muted)">/ 100</span></div>
            <div style="font-size:11px;color:var(--biz-text-muted);margin-top:6px">${invData.outStockCount} Kosong · ${invData.lowStockCount} Menipis · ${invData.overStockCount} Overstock</div>
        </div>

    </div>`;
}

window.bizLoadReports = bizLoadReports;
