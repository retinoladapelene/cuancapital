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
        <div id="fin-kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 140px), 1fr)); gap:12px; margin-bottom:24px">
            <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Loading KPIs...</div>
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
                    ${e.notes && e.notes.trim() !== '' ? `<div class="biz-list-sub" style="font-size:12px;color:var(--biz-text-dim)">${_esc(e.notes)}</div>` : ''}
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
