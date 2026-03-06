/**
 * Business Manager — finance.js
 * Expense CRUD, Command Center (5-Layer), expense list by month
 */

// ── Open Expense Modal ───────────────────────────────────────────────────────
async function bizOpenAddExpense() {
    document.getElementById('exp-id').value = '';
    document.getElementById('exp-amount').value = '';
    document.getElementById('exp-notes').value = '';
    document.getElementById('exp-date').value = bizToday();
    document.getElementById('expense-modal-title').innerHTML = '<i class="fas fa-arrow-trend-down" style="color:var(--biz-danger)"></i> Tambah Pengeluaran';

    // Load expense categories
    const cats = await BizDB.expenseCats.getAll();
    const sel = document.getElementById('exp-cat');
    if (sel) {
        sel.innerHTML = cats.map(c => `<option value="${c.id}" data-name="${_esc(c.name)}">${_esc(c.name)}</option>`).join('');
    }

    bizOpenModal('biz-modal-expense');
    setTimeout(() => document.getElementById('exp-amount')?.focus(), 150);
}
window.bizOpenAddExpense = bizOpenAddExpense;

// ── Save Expense ─────────────────────────────────────────────────────────────
async function bizSaveExpense() {
    const id = document.getElementById('exp-id').value.trim();
    const amount = parseFloat(document.getElementById('exp-amount').value) || 0;
    const date = document.getElementById('exp-date').value || bizToday();
    const notes = document.getElementById('exp-notes').value.trim();
    const catSel = document.getElementById('exp-cat');
    const catId = catSel?.value || null;
    const catName = catSel?.selectedOptions[0]?.dataset.name || 'Lainnya';

    if (amount <= 0) { bizToast('Jumlah harus > 0', 'w'); return; }

    const bizId = window.bizState.businessId;

    if (id) {
        // Edit existing — need to reverse old snapshot first
        const old = await BizDB.expenses.getById(id);
        if (old) {
            // Reverse old amount from snapshot
            await bizUpsertSnapshot(bizId, old.expense_date, { revenue: 0, expenses: -(old.amount), profit: old.amount, orders_count: 0 });
        }
        await BizDB.expenses.save({ ...old, id, category_id: catId, category_name: catName, amount, expense_date: date, notes, updated_at: new Date().toISOString() });
    } else {
        await bizCreateExpense({ businessId: bizId, categoryId: catId, categoryName: catName, amount, expenseDate: date, notes });
    }

    bizToast('✅ Pengeluaran disimpan', 's');
    bizCloseModal('biz-modal-expense');
    if (window.bizState.activeTab === 'finance') await bizLoadFinance();
    if (window.bizState.activeTab === 'dashboard') await bizLoadDashboard();
}
window.bizSaveExpense = bizSaveExpense;

async function bizDeleteExpense(id) {
    bizConfirm(
        'Hapus Pengeluaran',
        'Yakin hapus pengeluaran ini?',
        async () => {
            const old = await BizDB.expenses.getById(id);
            if (old) {
                await bizUpsertSnapshot(window.bizState.businessId, old.expense_date, {
                    revenue: 0, expenses: -(old.amount), profit: old.amount, orders_count: 0
                });
            }
            await BizDB.expenses.delete(id);
            bizToast('Pengeluaran dihapus', 's');
            await bizLoadFinance();
        }
    );
}
window.bizDeleteExpense = bizDeleteExpense;


// ── Finance Page (Command Center) ────────────────────────────────────────────

async function bizLoadFinance() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const bizId = window.bizState.businessId;

    container.innerHTML = `<div class="biz-page" id="biz-fin-page">
        <!-- Header -->
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center">
            <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Financial Command Center</h2>
            <div style="font-size:13px;color:var(--biz-text-muted);font-weight:600">${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</div>
        </div>

        <!-- LAYER 1: Financial Snapshot (KPI Cards) -->
        <div id="fin-kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-bottom:24px">
            <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Loading KPIs...</div>
        </div>

        <!-- LAYER 2: Visual Analytics (Charts) -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px; margin-bottom:24px">
            <!-- Revenue vs Expense Line Chart -->
            <div class="biz-card" style="flex:2;min-width:320px">
                <div class="biz-card-header" style="margin-bottom:12px">
                    <div class="biz-card-title"><i class="fas fa-chart-area" style="color:var(--biz-primary)"></i> Trend Keuangan (14 Hari)</div>
                </div>
                <div style="height:220px; width:100%; position:relative">
                    <canvas id="finMainChart"></canvas>
                </div>
                <div id="fin-main-chart-labels" style="margin-top:10px;display:flex;justify-content:center;gap:15px;font-size:12px;color:var(--biz-text-dim)"></div>
            </div>

            <!-- Cashflow Timeline (Chart + List) -->
            <div class="biz-card" style="flex:1;min-width:300px">
                <div class="biz-card-header" style="margin-bottom:12px">
                    <div class="biz-card-title"><i class="fas fa-water" style="color:var(--biz-primary)"></i> Cashflow Timeline</div>
                </div>
                <div style="height:100px; width:100%; position:relative; margin-bottom:12px">
                    <canvas id="finCashflowChart"></canvas>
                </div>
                <div id="fin-cashflow-timeline" style="display:flex;flex-direction:column;gap:12px">
                    <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
            </div>
        </div>

        <!-- LAYER 3: Smart Insights -->
        <div id="fin-smart-insights" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:24px"></div>

        <!-- LAYER 4: Expense Breakdown & Stock Intelligence -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:20px; margin-bottom:24px">
            <!-- Expense Breakdown Donut -->
            <div class="biz-card">
                <div class="biz-card-header" style="margin-bottom:12px">
                    <div class="biz-card-title"><i class="fas fa-chart-pie" style="color:var(--biz-danger)"></i> Expense Breakdown</div>
                </div>
                <div style="height:180px; width:100%; position:relative">
                    <canvas id="finExpenseDonutChart"></canvas>
                </div>
                <div id="fin-expense-donut-labels" style="margin-top:16px;display:flex;flex-direction:column;gap:8px"></div>
            </div>

            <!-- Stock Intelligence Panel -->
            <div class="biz-card">
                <div class="biz-card-header" style="margin-bottom:12px">
                    <div class="biz-card-title"><i class="fas fa-boxes-packing" style="color:var(--biz-primary)"></i> Stock Intelligence</div>
                </div>
                <div id="fin-stock-intel" style="display:flex;flex-direction:column;gap:14px">
                    <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
            </div>
        </div>

        <div id="fin-profit-leak-container"></div>
        
        <div class="biz-divider" style="margin:32px 0 20px 0"></div>

        <!-- LAYER 5: Expense History -->
        <div class="biz-card" style="margin-bottom:80px">
            <div class="biz-card-header" style="margin-bottom:16px">
                <div class="biz-card-title">Histori Pengeluaran</div>
                <div style="display:flex; gap:8px">
                    <button class="biz-btn biz-btn-sm biz-btn-danger" onclick="bizOpenAddExpense()">
                        <i class="fas fa-plus"></i>
                    </button>
                    <select class="biz-input" id="fin-month-sel" style="width:auto; height:32px; padding:0 8px; font-size:13px" onchange="finLoadMonth(this.value)">
                        ${_generateMonthOptions()}
                    </select>
                </div>
            </div>
            <div id="fin-expense-list"><div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
        </div>
    </div>`;

    // Update page title
    const pt = document.getElementById('biz-page-title');
    if (pt) pt.textContent = 'Command Center';

    // Load Data
    const today = bizToday();
    const monthKey = bizMonthKey();
    const [snapshots, sales, saleItems, expenses] = await Promise.all([
        BizDB.finSnapshots.getAll(),
        BizDB.sales.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.expenses.getAll(),
    ]);

    const todaySnap = snapshots.find(s => s.snapshot_date === today) || {};
    const revenueToday = todaySnap.revenue || 0;
    const profitToday = todaySnap.profit || 0;
    const expToday = todaySnap.expenses || 0;
    const marginToday = revenueToday > 0 ? ((profitToday / revenueToday) * 100).toFixed(0) : 0;

    let totalCash = 0;
    sales.forEach(s => { if (s.business_id === bizId) totalCash += s.total_amount; });
    expenses.forEach(e => { if (e.business_id === bizId) totalCash -= e.amount; });

    document.getElementById('fin-kpi-grid').innerHTML = `
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Revenue (Hari Ini)</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-text);letter-spacing:-0.5px">Rp <span class="biz-count-up" data-val="${revenueToday}">0</span></div>
        </div>
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Expense (Hari Ini)</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-danger);letter-spacing:-0.5px">Rp <span class="biz-count-up" data-val="${expToday}">0</span></div>
        </div>
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Profit (Hari Ini)</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-success);letter-spacing:-0.5px">Rp <span class="biz-count-up" data-val="${profitToday}">0</span></div>
        </div>
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center; background:var(--biz-surface-2)">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-dim);margin-bottom:4px;text-transform:uppercase">Cash Balance</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-primary);letter-spacing:-0.5px">Rp <span class="biz-count-up" data-val="${totalCash}">0</span></div>
        </div>
        <div class="biz-card" style="flex:1; padding:16px 12px; text-align:center">
            <div style="font-size:11px;font-weight:700;color:var(--biz-text-muted);margin-bottom:4px;text-transform:uppercase">Margin (Hari Ini)</div>
            <div style="font-size:18px;font-weight:800;color:var(--biz-warning);letter-spacing:-0.5px"><span class="biz-count-up" data-val="${marginToday}">0</span>%</div>
        </div>
    `;

    setTimeout(() => {
        document.querySelectorAll('.biz-count-up').forEach(el => {
            const target = parseInt(el.getAttribute('data-val')) || 0;
            if (typeof _animateValue === 'function') _animateValue(el, 0, target, 1000);
            else el.textContent = target;
        });
    }, 100);

    // Render Charts and Intelligence
    _finRenderMainFinanceChart(snapshots, bizId);
    _finRenderExpenseDonut(expenses, bizId);
    _finRenderCashflowTimeline(snapshots, sales, expenses, bizId);
    _finRenderStockIntelligence(bizId);
    _finRenderProfitLeak(bizId, monthKey);

    // Smart Insights
    if (typeof bizGenerateInsights === 'function') {
        const insights = await bizGenerateInsights(bizId);
        const ifeed = document.getElementById('fin-smart-insights');
        if (ifeed) {
            const topInsights = insights.slice(0, 3);
            ifeed.innerHTML = topInsights.map(ins => {
                let textCol = 'var(--biz-primary)';
                let bgCol = 'var(--biz-primary-light)';
                let iconStr = '<i class="fas fa-lightbulb"></i>';

                if (ins.type === 'danger') {
                    textCol = 'var(--biz-danger)'; bgCol = 'rgba(239, 68, 68, 0.08)'; iconStr = '<i class="fas fa-triangle-exclamation"></i>';
                } else if (ins.type === 'warning') {
                    textCol = 'var(--biz-warning)'; bgCol = 'rgba(245, 158, 11, 0.08)'; iconStr = '<i class="fas fa-chart-line-down"></i>';
                } else if (ins.type === 'success') {
                    textCol = 'var(--biz-success)'; bgCol = 'rgba(34, 197, 94, 0.08)'; iconStr = '<i class="fas fa-arrow-trend-up"></i>';
                }

                const parts = ins.text.split('. ');
                let title = parts[0].replace(/<[^>]*>?/gm, '');

                return `
                <div style="background:${bgCol}; border-radius:12px; padding:16px; display:flex; align-items:flex-start; gap:12px; border:1px solid rgba(0,0,0,0.02)">
                    <div style="color:${textCol}; font-size:16px; margin-top:2px">${iconStr}</div>
                    <p style="font-size:14px; font-weight:600; color:${textCol}; line-height:1.4; margin:0">${title}</p>
                </div>`;
            }).join('');
        }
    }

    // Render Expense List
    const monthExp = expenses.filter(e => e.expense_date && e.expense_date.startsWith(monthKey) && e.business_id === bizId);
    window._finExpenses = monthExp;
    finRenderExpenses(monthExp);
}

function _generateMonthOptions() {
    const months = [];
    const now = new Date();
    for (let i = 0; i < 6; i++) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const key = d.toISOString().slice(0, 7);
        const lbl = d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        months.push(`<option value="${key}">${lbl}</option>`);
    }
    return months.join('');
}

async function finLoadMonth(monthKey) {
    const all = await BizDB.expenses.getAll();
    const filtered = all.filter(e => e.expense_date && e.expense_date.startsWith(monthKey) && e.business_id === window.bizState.businessId);
    window._finExpenses = filtered;
    finRenderExpenses(filtered);
}

function finRenderExpenses(list) {
    const el = document.getElementById('fin-expense-list');
    if (!el) return;
    const sorted = (list || []).sort((a, b) => new Date(b.expense_date) - new Date(a.expense_date));
    if (!sorted.length) { el.innerHTML = '<div class="biz-empty"><i class="fas fa-wallet"></i><br>Belum ada pengeluaran</div>'; return; }

    const groups = {};
    sorted.forEach(e => { if (!groups[e.expense_date]) groups[e.expense_date] = []; groups[e.expense_date].push(e); });

    el.innerHTML = Object.entries(groups).map(([date, exps]) => {
        const dayTotal = exps.reduce((s, e) => s + (e.amount || 0), 0);
        return `<div class="biz-date-header" style="margin-top:10px">${new Date(date).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' })} · Rp${bizRp(dayTotal)}</div>` +
            exps.map(e => `<div class="biz-expense-item biz-list-item" style="padding:10px 14px;border-bottom:1px solid var(--biz-border);display:flex;align-items:center">
                <div class="biz-expense-cat-icon" style="background:rgba(244,63,94,0.1);color:var(--biz-danger);width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-receipt"></i></div>
                <div class="biz-list-body" style="margin-left:12px;flex:1">
                    <div class="biz-list-name" style="font-weight:600;font-size:14px;color:var(--biz-text)">${_esc(e.category_name || 'Lainnya')}</div>
                    <div class="biz-list-sub" style="font-size:12px;color:var(--biz-text-dim)">${_esc(e.notes) || '—'}</div>
                </div>
                <div class="biz-list-right" style="text-align:right">
                    <div class="biz-expense-amount" style="font-weight:700;color:var(--biz-danger)">-${bizRp(e.amount)}</div>
                </div>
                <button class="biz-icon-btn" onclick="bizDeleteExpense('${e.id}')" style="margin-left:12px;background:none;border:none;padding:5px;cursor:pointer;flex-shrink:0" title="Hapus">
                    <i class="fas fa-trash-can" style="font-size:13px;color:var(--biz-danger)"></i>
                </button>
            </div>`).join('');
    }).join('');
}

// ── Chart Generators ────────────────────────────────────────────────────────

function _finRenderMainFinanceChart(snapshots, bizId) {
    const canvas = document.getElementById('finMainChart');
    if (!canvas) return;

    const days = Array.from({ length: 14 }, (_, i) => {
        const d = new Date(); d.setDate(d.getDate() - (13 - i));
        return d.toISOString().split('T')[0];
    });

    const revenues = [];
    const expenses = [];
    const profits = [];

    days.forEach(d => {
        const snap = snapshots.find(s => s.snapshot_date === d && s.business_id === bizId);
        revenues.push(snap ? (snap.revenue || 0) : 0);
        expenses.push(snap ? (snap.expenses || 0) : 0);
        profits.push(snap ? (snap.profit || 0) : 0);
    });

    const labels = days.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));

    if (window.bizFinMainChartInstance) window.bizFinMainChartInstance.destroy();

    const maxVal = Math.max(...revenues, ...expenses);
    if (maxVal === 0) {
        canvas.parentElement.innerHTML = '<div class="biz-empty" style="color:var(--biz-text-dim);height:100%;display:flex;align-items:center;justify-content:center"><i class="fas fa-chart-line" style="margin-right:8px"></i> Belum ada data transaksi kas.</div>';
        return;
    }

    if (typeof Chart === 'undefined') return;

    window.bizFinMainChartInstance = new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue', data: revenues, borderColor: '#6366f1', backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#6366f1', pointRadius: 4, fill: true, tension: 0.4
                },
                {
                    label: 'Expense', data: expenses, borderColor: '#ef4444', backgroundColor: 'transparent',
                    borderWidth: 2, borderDash: [5, 5], pointBackgroundColor: '#fff', pointBorderColor: '#ef4444', pointRadius: 3, fill: false, tension: 0.4
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)', titleFont: { family: "'Inter', sans-serif" }, bodyFont: { family: "'Inter', sans-serif", size: 13 },
                    callbacks: { label: function (ctx) { return ctx.dataset.label + ": Rp " + ctx.raw.toString().replace(/B(?=(d{3})+(?!d))/g, "."); } }
                }
            },
            scales: { x: { grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif", size: 10 } } }, y: { display: false, min: 0 } }
        }
    });

    const totRev = revenues.reduce((a, b) => a + b, 0);
    const totExp = expenses.reduce((a, b) => a + b, 0);
    const totProf = profits.reduce((a, b) => a + b, 0);

    document.getElementById('fin-main-chart-labels').innerHTML = `
        <div style="display:flex;gap:6px;align-items:center"><div style="width:10px;height:10px;border-radius:2px;background:#6366f1"></div><span style="font-weight:600">Terima Rp${bizRp(totRev)}</span></div>
        <div style="display:flex;gap:6px;align-items:center"><div style="width:10px;height:10px;border-radius:2px;border:2px dashed #ef4444"></div><span style="font-weight:600">Keluar Rp${bizRp(totExp)}</span></div>
        <div style="display:flex;gap:6px;align-items:center;background:var(--biz-surface-2);padding:2px 8px;border-radius:12px"><i class="fas fa-arrow-up" style="color:var(--biz-success);font-size:10px"></i><span style="font-weight:700;color:var(--biz-success)">Profit Rp${bizRp(totProf)}</span></div>
    `;
}

function _finRenderExpenseDonut(expenses, bizId) {
    const canvas = document.getElementById('finExpenseDonutChart');
    if (!canvas) return;

    const cutoff = new Date(); cutoff.setDate(cutoff.getDate() - 30);
    const recentExp = expenses.filter(e => e.business_id === bizId && new Date(e.created_at || Math.max(new Date(e.expense_date), new Date() - 86400000)) >= cutoff);

    if (recentExp.length === 0) {
        canvas.parentElement.innerHTML = '<div class="biz-empty" style="color:var(--biz-text-dim);height:100%;display:flex;align-items:center;justify-content:center"><i class="fas fa-chart-pie" style="margin-right:8px"></i> Belum ada pengeluaran 30 hari.</div>';
        return;
    }

    const catMap = {}; let total = 0;
    recentExp.forEach(e => { catMap[e.category] = (catMap[e.category] || 0) + e.amount; total += e.amount; });

    const sortedCats = Object.entries(catMap).sort((a, b) => b[1] - a[1]);
    const top4 = sortedCats.slice(0, 4);
    const others = sortedCats.slice(4).reduce((sum, item) => sum + item[1], 0);
    if (others > 0) top4.push(['Lainnya', others]);

    const labels = top4.map(c => c[0]);
    const data = top4.map(c => c[1]);
    const colors = ['#f43f5e', '#f59e0b', '#8b5cf6', '#3b82f6', '#94a3b8'];

    if (window.bizFinExpenseDonutInstance) window.bizFinExpenseDonutInstance.destroy();

    if (typeof Chart === 'undefined') return;

    window.bizFinExpenseDonutInstance = new Chart(canvas, {
        type: 'doughnut',
        data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 0, hoverOffset: 4 }] },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (ctx) { return " Rp " + ctx.raw.toString().replace(/B(?=(d{3})+(?!d))/g, "."); } } } }
        }
    });

    document.getElementById('fin-expense-donut-labels').innerHTML = top4.map((c, i) => {
        const pct = Math.round((c[1] / total) * 100);
        return `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid var(--biz-border)">
            <div style="display:flex;align-items:center;gap:8px">
                <div style="width:10px;height:10px;border-radius:50%;background:${colors[i]}"></div>
                <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${_esc(c[0])}</span>
            </div>
            <div style="display:flex;align-items:right;gap:8px;font-size:12px;font-weight:600">
                <span style="color:var(--biz-text-dim)">${pct}%</span>
                <span style="color:var(--biz-text);width:70px;text-align:right">Rp${bizRp(c[1])}</span>
            </div>
        </div>`;
    }).join('');
}

function _finRenderCashflowTimeline(snapshots, sales, expenses, bizId) {
    const canvas = document.getElementById('finCashflowChart');
    if (!canvas) return;

    const days = Array.from({ length: 7 }, (_, i) => {
        const d = new Date(); d.setDate(d.getDate() - (6 - i));
        return d.toISOString().split('T')[0];
    });

    const mySales = sales.filter(s => s.business_id === bizId);
    const myExp = expenses.filter(e => e.business_id === bizId);

    const values = days.map(d => {
        const dEnd = new Date(d); dEnd.setHours(23, 59, 59, 999);
        let inTotal = 0; mySales.forEach(s => { if (new Date(s.created_at) <= dEnd) inTotal += s.total_amount; });
        let outTotal = 0; myExp.forEach(e => { if (new Date(e.created_at || Math.max(new Date(e.expense_date), new Date() - 86400000)) <= dEnd) outTotal += e.amount; });
        return inTotal - outTotal;
    });

    const labels = days.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));

    if (window.bizFinCashflowChartInstance) window.bizFinCashflowChartInstance.destroy();

    if (typeof Chart === 'undefined') return;

    window.bizFinCashflowChartInstance = new Chart(canvas, {
        type: 'line',
        data: { labels: labels, datasets: [{ label: 'Saldo Kas', data: values, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', borderWidth: 3, pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', titleFont: { family: "'Inter', sans-serif" }, bodyFont: { family: "'Inter', sans-serif", size: 13 }, callbacks: { label: function (ctx) { return "Rp " + ctx.raw.toString().replace(/B(?=(d{3})+(?!d))/g, "."); } } } },
            scales: { x: { grid: { display: false }, ticks: { font: { family: "'Inter', sans-serif", size: 10 } } }, y: { display: false } }
        }
    });

    const tlContainer = document.getElementById('fin-cashflow-timeline');
    if (!tlContainer) return;

    const allEvents = [
        ...mySales.map(s => ({ type: 'sale', amount: s.total_amount, date: new Date(s.created_at), desc: s.payment_method === 'cash' ? 'Penjualan Toko' : 'Penjualan Online' })),
        ...myExp.map(e => ({ type: 'expense', amount: e.amount, date: new Date(e.created_at || Math.max(new Date(e.expense_date), new Date() - 86400000)), desc: e.notes || e.category }))
    ];

    allEvents.sort((a, b) => b.date - a.date);
    const recentEvents = allEvents.slice(0, 5);

    if (recentEvents.length === 0) {
        tlContainer.innerHTML = `<div class="biz-empty" style="color:var(--biz-text-dim)">Belum ada riwayat transaksi kas.</div>`;
        return;
    }

    tlContainer.innerHTML = recentEvents.map(ev => {
        const isSale = ev.type === 'sale';
        const iconCol = isSale ? 'var(--biz-success)' : 'var(--biz-danger)';
        const iconDir = isSale ? 'fa-arrow-up' : 'fa-arrow-down';
        const sign = isSale ? '+' : '-';
        return `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:4px 0">
            <div style="display:flex;align-items:center;gap:12px">
                <i class="fas ${iconDir}" style="color:${iconCol};font-size:12px;width:12px"></i>
                <div style="display:flex;flex-direction:column">
                    <span style="font-weight:700;font-size:14px;color:${iconCol};letter-spacing:-0.2px">${sign}${bizRp(ev.amount)}</span>
                    <span style="font-size:12px;color:var(--biz-text-dim);margin-top:2px">${_esc(ev.desc)}</span>
                </div>
            </div>
            <div style="font-size:11px;font-weight:600;color:var(--biz-text-muted)">
                ${ev.date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
            </div>
        </div>`;
    }).join('');
}

async function _finRenderStockIntelligence(bizId) {
    const el = document.getElementById('fin-stock-intel');
    if (!el) return;
    if (typeof bizInventoryHealth !== 'function') return;

    const invData = await bizInventoryHealth(bizId);
    if (!invData || !invData.burnRates || invData.burnRates.length === 0) {
        el.innerHTML = `<div class="biz-empty" style="color:var(--biz-text-dim)"><i class="fas fa-boxes-packing"></i><br>Belum ada data inventaris.</div>`;
        return;
    }

    const burnRates = invData.burnRates;
    const fastSelling = burnRates.filter(p => p.daysLeft !== '90+' && p.daysLeft < 5 && p.stock > 0).sort((a, b) => a.daysLeft - b.daysLeft);
    const lowStock = burnRates.filter(p => p.stock > 0 && p.stock <= 5).sort((a, b) => a.stock - b.stock);
    const slowMoving = burnRates.filter(p => p.daysLeft === '90+' && p.stock > 10).sort((a, b) => b.stock - a.stock);

    let html = '';
    if (fastSelling.length > 0) {
        const p = fastSelling[0];
        html += `<div style="display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--biz-text-muted);font-weight:600"><i class="fas fa-fire" style="color:#f59e0b;width:16px"></i> Fast Selling</span>
            <div style="text-align:right"><span class="biz-badge biz-badge-success" style="font-size:12px">${_esc(p.name)}</span><div style="font-size:11px;color:var(--biz-text-dim);margin-top:4px">Habis dlm ${p.daysLeft} hari</div></div>
        </div><div class="biz-divider" style="margin:10px 0"></div>`;
    }
    if (lowStock.length > 0) {
        const p = lowStock[0];
        html += `<div style="display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--biz-text-muted);font-weight:600"><i class="fas fa-triangle-exclamation" style="color:var(--biz-warning);width:16px"></i> Low Stock</span>
            <div style="text-align:right"><span class="biz-badge biz-badge-warning" style="font-size:12px">${_esc(p.name)}</span><div style="font-size:11px;color:var(--biz-text-dim);margin-top:4px">Sisa ${p.stock} unit</div></div>
        </div><div class="biz-divider" style="margin:10px 0"></div>`;
    }
    if (slowMoving.length > 0) {
        const p = slowMoving[0];
        html += `<div style="display:flex;align-items:center;justify-content:space-between">
            <span style="font-size:13px;color:var(--biz-text-muted);font-weight:600"><i class="fas fa-turtle" style="color:var(--biz-primary);width:16px"></i> Slow Moving</span>
            <div style="text-align:right"><span class="biz-badge biz-badge-primary" style="font-size:12px">${_esc(p.name)}</span><div style="font-size:11px;color:var(--biz-text-dim);margin-top:4px">Overstok (${p.stock})</div></div>
        </div>`;
    }

    if (html === '') html = `<div class="biz-empty" style="padding:10px 0;color:var(--biz-success)"><i class="fas fa-check-circle"></i> Stok inventaris aman.</div>`;

    if (lowStock.length > 0) {
        const p = lowStock[0]; const sugQty = Math.max(10, Math.ceil((p.avgDaily || 2) * 14));
        html += `<div style="margin-top:16px;padding:12px;background:var(--biz-primary-light);border-radius:8px;border:1px solid rgba(99, 102, 241, 0.2)">
            <div style="font-size:11px;font-weight:700;color:var(--biz-primary);margin-bottom:4px;letter-spacing:0.5px">AI SUGGESTION</div>
            <div style="font-size:13px;color:var(--biz-text);font-weight:600;margin-bottom:8px">Restock ${_esc(p.name)} minimal ${sugQty} unit.</div>
            <button class="biz-btn biz-btn-sm biz-btn-primary" style="width:100%" onclick="bizOpenModal('biz-modal-restock'); document.getElementById('restock-product-id').value='${p.id}'; document.getElementById('restock-product-name').textContent='Restock: ${_esc(p.name)}';">Restock Sekarang</button>
        </div>`;
    }
    if (html.endsWith('<div class="biz-divider" style="margin:10px 0"></div>')) html = html.slice(0, -55);
    el.innerHTML = html;
}

async function _finRenderProfitLeak(bizId, monthKey) {
    const el = document.getElementById('fin-profit-leak-container');
    if (!el) return;
    if (typeof bizProfitAnalyzer !== 'function') return;

    const profitData = await bizProfitAnalyzer(bizId, monthKey);
    if (!profitData || !profitData.products || profitData.products.length === 0) return;

    const leaks = profitData.products.filter(p => p.margin > 0 && p.margin < 15).sort((a, b) => a.margin - b.margin);
    if (leaks.length === 0) { el.innerHTML = ''; return; }

    const leak = leaks[0];
    el.innerHTML = `
    <div class="biz-card" style="margin-bottom:20px;border:1px solid rgba(244, 63, 94, 0.3);background:rgba(244, 63, 94, 0.015)">
        <div class="biz-card-header" style="margin-bottom:12px"><div class="biz-card-title"><span class="biz-badge biz-badge-danger" style="margin-right:6px"><i class="fas fa-search-dollar"></i> Profit Leak Alert</span></div></div>
        <div style="font-size:14px;color:var(--biz-text);font-weight:600;margin-bottom:4px;line-height:1.4">Margin ${_esc(leak.name)} sangat tipis (${leak.margin.toFixed(1)}%).</div>
        <div style="font-size:12px;color:var(--biz-text-dim);margin-bottom:16px;line-height:1.5">Profit hanya Rp${bizRp(leak.profit)} dari omset Rp${bizRp(leak.revenue)}. Pertimbangkan untuk menaikkan harga atau menekan HPP bahan baku secepatnya.</div>
        <button class="biz-btn biz-btn-sm" style="color:var(--biz-danger);background:rgba(244, 63, 94, 0.1);padding:6px 12px;border:none;border-radius:6px;font-weight:600" onclick="bizSwitchTab('products')">Review Pricing & HPP →</button>
    </div>`;
}

window.bizLoadFinance = bizLoadFinance;
window.finLoadMonth = finLoadMonth;
window.finRenderExpenses = finRenderExpenses;
