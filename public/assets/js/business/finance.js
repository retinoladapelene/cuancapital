/**
 * Business Manager — finance.js
 * Expense CRUD, expense list by month, fin_snapshots update
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

// ── Finance Page ─────────────────────────────────────────────────────────────
async function bizLoadFinance() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const now = new Date();
    const today = bizToday();
    const monthKey = bizMonthKey();

    const [expenses, snapshots] = await Promise.all([BizDB.expenses.getAll(), BizDB.finSnapshots.getAll()]);
    const monthExp = expenses.filter(e => e.expense_date && e.expense_date.startsWith(monthKey));
    const monthSnap = snapshots.filter(s => s.snapshot_date && s.snapshot_date.startsWith(monthKey));
    const revenue = monthSnap.reduce((s, r) => s + (r.revenue || 0), 0);
    const expTotal = monthSnap.reduce((s, r) => s + (r.expenses || 0), 0);
    const profit = monthSnap.reduce((s, r) => s + (r.profit || 0), 0);

    container.innerHTML = `<div class="biz-page">

        <!-- Summary strip -->
        <div class="biz-summary-strip" style="margin-bottom:14px">
            <div class="biz-strip-item"><div class="biz-strip-label">Revenue</div><div class="biz-strip-value">${bizRp(revenue)}</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Pengeluaran</div><div class="biz-strip-value" style="color:var(--biz-danger)">${bizRp(expTotal)}</div></div>
            <div class="biz-strip-item"><div class="biz-strip-label">Profit Bersih</div><div class="biz-strip-value" style="color:var(--biz-success)">${bizRp(profit)}</div></div>
        </div>

        <div style="display:flex;gap:10px;margin-bottom:14px">
            <button class="biz-btn biz-btn-danger" onclick="bizOpenAddExpense()">
                <i class="fas fa-plus"></i> Catat Pengeluaran
            </button>
            <select class="biz-input" id="fin-month-sel" style="width:auto" onchange="finLoadMonth(this.value)">
                ${_generateMonthOptions()}
            </select>
        </div>

        <div id="fin-expense-list"></div>
    </div>`;

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
    const filtered = all.filter(e => e.expense_date && e.expense_date.startsWith(monthKey));
    window._finExpenses = filtered;
    finRenderExpenses(filtered);
}

function finRenderExpenses(list) {
    const el = document.getElementById('fin-expense-list');
    if (!el) return;
    const sorted = (list || []).sort((a, b) => new Date(b.expense_date) - new Date(a.expense_date));
    if (!sorted.length) { el.innerHTML = '<div class="biz-empty"><i class="fas fa-wallet"></i><br>Belum ada pengeluaran</div>'; return; }

    // Group by date
    const groups = {};
    sorted.forEach(e => { if (!groups[e.expense_date]) groups[e.expense_date] = []; groups[e.expense_date].push(e); });

    el.innerHTML = Object.entries(groups).map(([date, exps]) => {
        const dayTotal = exps.reduce((s, e) => s + (e.amount || 0), 0);
        return `<div class="biz-date-header">${_fmtDate ? _fmtDate(date) : date} · ${bizRp(dayTotal)}</div>` +
            exps.map(e => `<div class="biz-expense-item">
                <div class="biz-expense-cat-icon"><i class="fas fa-receipt"></i></div>
                <div class="biz-list-body">
                    <div class="biz-list-name">${_esc(e.category_name || 'Lainnya')}</div>
                    <div class="biz-list-sub">${_esc(e.notes) || '—'}</div>
                </div>
                <div class="biz-list-right">
                    <div class="biz-expense-amount">-${bizRp(e.amount)}</div>
                </div>
                <button class="biz-icon-btn" onclick="bizDeleteExpense('${e.id}')" style="margin-left:6px" title="Hapus">
                    <i class="fas fa-trash-can" style="font-size:12px;color:var(--biz-danger)"></i>
                </button>
            </div>`).join('');
    }).join('');
}

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

window.bizLoadFinance = bizLoadFinance;
window.finLoadMonth = finLoadMonth;
window.finRenderExpenses = finRenderExpenses;
window.bizDeleteExpense = bizDeleteExpense;
