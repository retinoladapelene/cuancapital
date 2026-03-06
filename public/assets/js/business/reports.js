/**
 * Business Manager — reports.js
 * Revenue/profit bar chart, expense breakdown, product performance table
 */

async function bizLoadReports() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const [snapshots, saleItems] = await Promise.all([BizDB.finSnapshots.getAll(), BizDB.saleItems.getAll()]);

    // Build 6-month data
    const months = Array.from({ length: 6 }, (_, i) => {
        const d = new Date();
        d.setMonth(d.getMonth() - (5 - i));
        return d.toISOString().slice(0, 7);
    });

    const monthData = months.map(mk => {
        const snaps = snapshots.filter(s => s.snapshot_date && s.snapshot_date.startsWith(mk));
        return {
            month: mk,
            label: new Date(mk + '-01').toLocaleDateString('id-ID', { month: 'short' }),
            revenue: snaps.reduce((s, r) => s + (r.revenue || 0), 0),
            profit: snaps.reduce((s, r) => s + (r.profit || 0), 0),
            expenses: snaps.reduce((s, r) => s + (r.expenses || 0), 0),
        };
    });

    const maxRev = Math.max(...monthData.map(m => m.revenue), 1);

    // Product performance (current month)
    const thisMonth = bizMonthKey();
    const monthItems = saleItems.filter(si => si.created_at && si.created_at.startsWith(thisMonth));
    const prodPerf = {};
    monthItems.forEach(si => {
        if (!prodPerf[si.product_id]) prodPerf[si.product_id] = { name: si.product_name, qty: 0, revenue: 0, profit: 0 };
        prodPerf[si.product_id].qty += si.quantity;
        prodPerf[si.product_id].revenue += si.subtotal;
        prodPerf[si.product_id].profit += si.profit;
    });
    const prodSorted = Object.values(prodPerf).sort((a, b) => b.revenue - a.revenue);

    // Expense breakdown
    const monthSnaps = snapshots.filter(s => s.snapshot_date && s.snapshot_date.startsWith(thisMonth));
    const totalRevM = monthSnaps.reduce((s, r) => s + (r.revenue || 0), 0);
    const totalProfM = monthSnaps.reduce((s, r) => s + (r.profit || 0), 0);
    const totalExpM = monthSnaps.reduce((s, r) => s + (r.expenses || 0), 0);
    const totalSalesM = monthSnaps.reduce((s, r) => s + (r.orders_count || 0), 0);
    const margin = totalRevM > 0 ? ((totalProfM / totalRevM) * 100).toFixed(1) : 0;

    container.innerHTML = `<div class="biz-page">

        <!-- Month summary -->
        <div class="biz-summary-strip" style="margin-bottom:14px">
            <div class="biz-strip-item"><div class="biz-strip-label">Revenue</div><div class="biz-strip-value">${bizRp(totalRevM)}</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Profit</div><div class="biz-strip-value" style="color:var(--biz-success)">${bizRp(totalProfM)}</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Margin</div><div class="biz-strip-value" style="color:var(--biz-primary)">${margin}%</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Transaksi</div><div class="biz-strip-value">${totalSalesM}</div></div>
        </div>

        <!-- 6-month bar chart -->
        <div class="biz-chart-wrap">
            <div class="biz-card-header">
                <div class="biz-card-title"><i class="fas fa-chart-bar" style="color:var(--biz-primary)"></i> Revenue 6 Bulan</div>
            </div>
            <div class="biz-report-bar-wrap">
                ${monthData.map(m => {
        const h = maxRev > 0 ? Math.max(4, (m.revenue / maxRev) * 110) : 4;
        return `<div class="biz-report-bar-col">
                        <div style="font-size:9px;color:var(--biz-text-muted);font-weight:700">${bizRp(m.revenue)}</div>
                        <div class="biz-report-bar revenue" style="height:${h}px" title="${m.label}: ${bizRpFull(m.revenue)}"></div>
                        <div class="biz-report-bar-label">${m.label}</div>
                    </div>`;
    }).join('')}
            </div>
        </div>

        <!-- Revenue vs Expense legend -->
        <div class="biz-card" style="margin-bottom:14px">
            <div class="biz-card-title" style="margin-bottom:14px"><i class="fas fa-scale-balanced"></i> Revenue vs Pengeluaran</div>
            ${monthData.map(m => {
        const maxV = Math.max(m.revenue, m.expenses, 1);
        const rPct = Math.round((m.revenue / maxV) * 100);
        const ePct = Math.round((m.expenses / maxV) * 100);
        return `<div style="margin-bottom:12px">
                    <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:4px">
                        <span style="color:var(--biz-text-muted);font-weight:700">${m.label}</span>
                        <span style="color:var(--biz-success);font-weight:700">+${bizRp(m.profit)}</span>
                    </div>
                    <div class="biz-breakdown-bar-track" style="height:6px;margin-bottom:3px">
                        <div class="biz-breakdown-bar-fill" style="width:${rPct}%;background:var(--biz-primary)"></div>
                    </div>
                    <div class="biz-breakdown-bar-track" style="height:6px">
                        <div class="biz-breakdown-bar-fill" style="width:${ePct}%;background:var(--biz-danger)"></div>
                    </div>
                </div>`;
    }).join('')}
            <div style="display:flex;gap:16px;font-size:11px;font-weight:700;margin-top:8px">
                <span style="color:var(--biz-primary)">█ Revenue</span>
                <span style="color:var(--biz-danger)">█ Pengeluaran</span>
            </div>
        </div>

        <!-- Product Performance -->
        <div class="biz-card">
            <div class="biz-card-title" style="margin-bottom:14px">
                <i class="fas fa-box" style="color:var(--biz-primary)"></i> Performa Produk Bulan Ini
            </div>
            ${prodSorted.length ? `
            <div style="display:grid;grid-template-columns:1fr 60px 80px 80px;gap:8px;font-size:10px;font-weight:700;color:var(--biz-text-muted);margin-bottom:6px;padding-bottom:6px;border-bottom:1px solid var(--biz-border);text-transform:uppercase">
                <span>Produk</span><span style="text-align:right">Qty</span><span style="text-align:right">Revenue</span><span style="text-align:right">Profit</span>
            </div>
            ${prodSorted.map((p, i) => `
            <div style="display:grid;grid-template-columns:1fr 60px 80px 80px;gap:8px;padding:8px 0;border-bottom:1px solid var(--biz-border);font-size:12px">
                <span style="font-weight:600">${_esc(p.name)}</span>
                <span style="text-align:right;color:var(--biz-text-muted)">${p.qty}</span>
                <span style="text-align:right;font-weight:700">${bizRp(p.revenue)}</span>
                <span style="text-align:right;font-weight:700;color:var(--biz-success)">${bizRp(p.profit)}</span>
            </div>`).join('')}` :
            '<div class="biz-empty" style="padding:24px"><i class="fas fa-box-open"></i><br>Belum ada penjualan bulan ini</div>'}
        </div>

    </div>`;
}

window.bizLoadReports = bizLoadReports;
