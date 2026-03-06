/**
 * Accounts & Transaction Management — missing functions
 * (debts.js handles debt/backup/export functions)
 */

// ── Account Management ──
async function submitAccount() {
    const name = document.getElementById('acc-name')?.value?.trim();
    const type = document.getElementById('acc-type')?.value || 'bank';
    const initial = parseFloat(document.getElementById('acc-initial')?.value || 0);

    if (!name) return toast('Nama akun wajib diisi', 'e');

    const newAcc = {
        id: 'acc_' + Date.now(),
        name, type,
        balance_cached: initial,
        initial_balance: initial,
        created_at: new Date().toISOString()
    };

    try {
        await localAPI.accounts.save(newAcc);
        accounts = await localAPI.accounts.getAll();
        window.accounts = accounts;
        closeModal('modal-add-account');
        ['acc-name', 'acc-initial'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        toast('Akun berhasil dibuat!', 's');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) {
        toast(e?.message || 'Gagal membuat akun', 'e');
    }
}

async function deleteAccount(id) {
    confirmDialog('Hapus Akun', 'Akun ini akan dihapus. Transaksi tetap ada.', 'danger', async () => {
        try {
            await localAPI.accounts.delete(String(id));
            accounts = await localAPI.accounts.getAll();
            window.accounts = accounts;
            renderAccountModal();
            if (typeof refreshUI === 'function') await refreshUI();
            toast('Akun dihapus', 's');
        } catch (e) {
            toast(e?.message || 'Gagal menghapus akun', 'e');
        }
    });
}

function renderAccountModal() {
    const list = document.getElementById('acc-modal-list');
    if (!list) return;
    if (!accounts || !accounts.length) {
        list.innerHTML = '<div class="empty"><i class="fas fa-wallet"></i>Belum ada akun. Buat akun pertamamu!</div>';
        return;
    }
    const frag = document.createDocumentFragment();
    accounts.forEach(a => {
        const el = document.createElement('div');
        el.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)';
        el.innerHTML = `
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--text)">${a.name}</div>
                <div style="font-size:11px;color:var(--text-muted)">${a.type || 'bank'}</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-weight:700;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--text)">${rp(a.balance_cached || 0)}</span>
                <button onclick="deleteAccount('${a.id}')" style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:13px;opacity:.7" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>
        `;
        frag.appendChild(el);
    });
    list.replaceChildren(frag);
}

// Override openModal to do extra setup for specific modals
const _baseOpenModal = window.openModal;
window.openModal = function (id) {
    if (id === 'modal-account') renderAccountModal();
    if (id === 'modal-transaction') {
        const today = new Date().toISOString().split('T')[0];
        const dtEl = document.getElementById('tx-date');
        if (dtEl && !dtEl.value) dtEl.value = today;
        populateTxAccountDropdowns();
        if (typeof setType === 'function') setType(window.currentTxType || 'expense');
    }
    if (id === 'modal-budget') {
        const mo = new Date().toISOString().slice(0, 7);
        const moEl = document.getElementById('bgt-month');
        if (moEl && !moEl.value) moEl.value = mo;
        buildBudgetCategoryDropdown();
    }
    if (_baseOpenModal) _baseOpenModal(id);
};

function populateTxAccountDropdowns() {
    ['tx-account-id', 'tx-from-acc', 'tx-to-acc', 'inst-account-id'].forEach(selId => {
        const sel = document.getElementById(selId);
        if (!sel) return;
        const cur = sel.value;
        sel.innerHTML = '<option value="">-- Pilih Akun --</option>' +
            (accounts || []).map(a => `<option value="${a.id}">${a.name} (${rp(a.balance_cached || 0)})</option>`).join('');
        if (cur) sel.value = cur;
    });
}

// ── Transaction Submit ──
async function submitTransaction() {
    const modal = document.getElementById('modal-transaction');
    const editId = modal?.dataset?.editId || '';
    const type = window.currentTxType || 'expense';

    let accountId, toAccId;
    if (type === 'transfer') {
        accountId = document.getElementById('tx-from-acc')?.value;
        toAccId = document.getElementById('tx-to-acc')?.value;
        if (!accountId || !toAccId) return toast('Pilih akun sumber dan tujuan', 'e');
        if (accountId === toAccId) return toast('Akun sumber dan tujuan harus berbeda', 'e');
    } else {
        accountId = document.getElementById('tx-account-id')?.value;
        if (!accountId) return toast('Pilih akun terlebih dahulu', 'e');
    }

    const amount = parseFloat(document.getElementById('tx-amount')?.value || 0);
    if (!amount || amount <= 0) return toast('Jumlah harus lebih dari 0', 'e');

    const dateVal = document.getElementById('tx-date')?.value;
    if (!dateVal) return toast('Tanggal wajib diisi', 'e');

    const note = document.getElementById('tx-note')?.value?.trim() || '';
    const categoryId = document.getElementById('tx-category-id')?.value || null;

    const txData = {
        id: editId || ('tx_' + Date.now()),
        type, account_id: accountId, amount,
        transaction_date: dateVal + (dateVal.length === 10 ? 'T00:00:00' : ''),
        note, category_id: categoryId || null,
        to_account_id: toAccId || null,
        created_at: new Date().toISOString()
    };

    if (categoryId) {
        txData.category = (categories || []).find(c => String(c.id) === String(categoryId)) || null;
    }

    try {
        await localAPI.transactions.save(txData);

        // Handle transfer: create reverse entry
        if (type === 'transfer' && toAccId) {
            const txIn = {
                id: 'tx_' + Date.now() + '_in',
                type: 'transfer',
                account_id: toAccId,
                amount,
                transaction_date: txData.transaction_date,
                note: (note || 'Transfer') + ' (masuk)',
                linked_tx_id: txData.id,
                created_at: new Date().toISOString()
            };
            await localAPI.transactions.save(txIn);
        }

        // Update account balance cache
        await recalcAccountBalance(accountId);
        if (toAccId) await recalcAccountBalance(toAccId);

        // Reload global state
        allTxList = await localAPI.transactions.getAll();
        allTxList.sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date));
        window.allTxList = allTxList;
        accounts = await localAPI.accounts.getAll();
        window.accounts = accounts;

        closeModal('modal-transaction');
        if (modal) modal.dataset.editId = '';
        toast(editId ? 'Transaksi diperbarui!' : 'Transaksi dicatat!', 's');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) {
        toast(e?.message || 'Gagal menyimpan transaksi', 'e');
        console.error(e);
    }
}

async function recalcAccountBalance(accountId) {
    const acc = (accounts || []).find(a => String(a.id) === String(accountId));
    if (!acc) return;
    const txs = await localAPI.transactions.getAll();
    let bal = parseFloat(acc.initial_balance || 0);
    txs.forEach(tx => {
        if (String(tx.account_id) !== String(accountId)) return;
        if (tx.type === 'income') bal += parseFloat(tx.amount || 0);
        else if (tx.type === 'expense') bal -= parseFloat(tx.amount || 0);
        else if (tx.type === 'transfer') {
            if (tx.linked_tx_id) bal += parseFloat(tx.amount || 0); // incoming transfer
            else bal -= parseFloat(tx.amount || 0); // outgoing transfer
        }
    });
    acc.balance_cached = bal;
    await localAPI.accounts.save(acc);
}

// ── Budget Management ──
function buildBudgetCategoryDropdown() {
    const menu = document.getElementById('cdd-budget-menu');
    if (!menu) return;
    const expCats = (categories || []).filter(c => c.pillar !== 'income');
    if (!expCats.length) {
        menu.innerHTML = '<div class="cdd-item" style="color:var(--text-muted)">Belum ada kategori pengeluaran</div>';
        return;
    }
    menu.innerHTML = expCats.map(c => `
        <div class="cdd-item" onclick="selectBudgetCat('${c.id}', '${(c.name || '').replace(/'/g, "\\'")}')">
            <div class="cdd-item-icon"><i class="fas ${c.icon || 'fa-tag'}" style="color:${c.color || 'var(--accent)'}"></i></div>
            <span class="cdd-item-lbl">${c.name}</span>
        </div>
    `).join('');
}

function selectBudgetCat(id, name) {
    const val = document.getElementById('bgt-cat-val');
    const lbl = document.getElementById('cdd-bgt-lbl');
    const menu = document.getElementById('cdd-budget-menu');
    if (val) val.value = id;
    if (lbl) { lbl.textContent = name; lbl.className = 'cdd-trigger-lbl'; }
    if (menu) menu.classList.remove('open');
}

async function submitBudget() {
    const catId = document.getElementById('bgt-cat-val')?.value;
    const month = document.getElementById('bgt-month')?.value;
    const limit = parseFloat(document.getElementById('bgt-limit')?.value || 0);

    if (!catId) return toast('Pilih kategori', 'e');
    if (!month) return toast('Pilih bulan', 'e');
    if (!limit || limit <= 0) return toast('Limit harus lebih dari 0', 'e');

    const budget = {
        id: 'bgt_' + catId + '_' + month,
        category_id: catId, month,
        limit_amount: limit, created_at: new Date().toISOString()
    };

    try {
        await localAPI.budgets.save(budget);
        closeModal('modal-budget');
        toast('Budget disimpan!', 's');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) {
        toast(e?.message || 'Gagal menyimpan budget', 'e');
    }
}

async function submitEditBudget() {
    const id = document.getElementById('edit-bgt-id')?.value;
    const month = document.getElementById('edit-bgt-month')?.value;
    const limit = parseFloat(document.getElementById('edit-bgt-limit')?.value || 0);

    if (!id) return toast('ID budget tidak ditemukan', 'e');
    if (!limit || limit <= 0) return toast('Limit harus lebih dari 0', 'e');

    try {
        const budgets = await localAPI.budgets.getAll();
        const bgt = budgets.find(b => String(b.id) === String(id));
        if (!bgt) return toast('Budget tidak ditemukan', 'e');
        bgt.limit_amount = limit;
        if (month) bgt.month = month;
        await localAPI.budgets.save(bgt);
        closeModal('modal-edit-budget');
        toast('Budget diperbarui!', 's');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) {
        toast(e?.message || 'Gagal memperbarui budget', 'e');
    }
}

async function copyLastMonthBudget() {
    const now = new Date();
    const thisMonth = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 7);
    const lastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().slice(0, 7);

    try {
        const allBudgets = await localAPI.budgets.getAll();
        const lastMonthBudgets = allBudgets.filter(b => b.month === lastMonth);
        if (!lastMonthBudgets.length) return toast('Tidak ada budget bulan lalu untuk disalin', 'w');

        for (const b of lastMonthBudgets) {
            const copied = { ...b, id: 'bgt_' + b.category_id + '_' + thisMonth, month: thisMonth };
            await localAPI.budgets.save(copied);
        }
        toast(`${lastMonthBudgets.length} budget berhasil disalin!`, 's');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) {
        toast(e?.message || 'Gagal menyalin budget', 'e');
    }
}

// ── Expose ──
window.submitAccount = submitAccount;
window.deleteAccount = deleteAccount;
window.renderAccountModal = renderAccountModal;
window.submitTransaction = submitTransaction;
window.submitBudget = submitBudget;
window.submitEditBudget = submitEditBudget;
window.copyLastMonthBudget = copyLastMonthBudget;
window.populateTxAccountDropdowns = populateTxAccountDropdowns;
window.buildBudgetCategoryDropdown = buildBudgetCategoryDropdown;
window.selectBudgetCat = selectBudgetCat;
window.recalcAccountBalance = recalcAccountBalance;
window.recalcAccountBalance = recalcAccountBalance;
