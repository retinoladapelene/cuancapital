/**
 * Transactions Tab Logic
 */

window.renderTransactionsTab = function (container) {
    window.injectHTML(container, `
        <!-- ══ LAYER 1: QUICK ADD CTA ══ -->
        <div class="tx-quick-add">
            <button class="tx-qa-main" onclick="openModal('modal-transaction');prepTx('expense')">
                <div class="tx-qa-plus"><i class="fas fa-plus"></i></div>
                <div>
                    <div style="font-size:14px;font-weight:800;">Catat Transaksi</div>
                    <div style="font-size:11px;opacity:.8;margin-top:2px;">Tap untuk input cepat</div>
                </div>
            </button>
            <div class="tx-qa-types">
                <button class="tx-qa-type-btn income" onclick="openModal('modal-transaction');prepTx('income')">
                    <i class="fas fa-arrow-down-left"></i><span>Pemasukan</span>
                </button>
                <button class="tx-qa-type-btn expense" onclick="openModal('modal-transaction');prepTx('expense')">
                    <i class="fas fa-arrow-up-right"></i><span>Pengeluaran</span>
                </button>
                <button class="tx-qa-type-btn transfer" onclick="openModal('modal-transaction');prepTx('transfer')">
                    <i class="fas fa-arrow-right-arrow-left"></i><span>Transfer</span>
                </button>
            </div>
        </div>

        <!-- ══ LAYER 2: FILTER & SEARCH ══ -->
        <div class="card tx-filter-card">
            <div class="tx-filter-row">
                <div class="tx-search-wrap">
                    <i class="fas fa-magnifying-glass tx-search-icon"></i>
                    <input type="text" class="tx-search-input" id="tx-search" placeholder="Cari transaksi, nominal, catatan..." oninput="if(!window.debouncedTxSearch) window.debouncedTxSearch = window.debounce(() => { if(typeof applyTxFilter === 'function') applyTxFilter(); else if(typeof window.applyTxFilter === 'function') window.applyTxFilter(); }, 300); window.debouncedTxSearch();">
                </div>
                <div class="tx-filter-chips">
                    <div class="sel-wrap" style="flex:0 0 auto; width:140px;">
                        <select class="fselect" id="tx-filter-period" onchange="applyTxFilter()">
                            <option value="all">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="7d">7 Hari</option>
                            <option value="this_month" selected>Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                            <option value="this_year">Tahun Ini</option>
                        </select>
                    </div>
                    <div class="sel-wrap" style="flex:0 0 auto; width:140px;">
                        <select class="fselect" id="tx-filter-type" onchange="applyTxFilter()">
                            <option value="all">Semua Tipe</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="sel-wrap" style="flex:0 0 auto; width:150px;">
                        <select class="fselect" id="tx-filter-pillar" onchange="applyTxFilter()">
                            <option value="all">Semua Pilar</option>
                            <option value="wajib">Wajib</option>
                            <option value="growth">Growth</option>
                            <option value="lifestyle">Lifestyle</option>
                            <option value="bocor">Bocor</option>
                        </select>
                    </div>
                    <button class="btn btn-ghost btn-sm" onclick="resetTxFilter()" title="Reset filter" style="flex-shrink:0;">
                        <i class="fas fa-rotate-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- ══ LAYER 3: MONTHLY SUMMARY STRIP ══ -->
        <div class="tx-summary-strip" id="tx-summary-strip">
            <div class="tx-sum-card income-sum">
                <div class="tx-sum-icon"><i class="fas fa-arrow-down-left"></i></div>
                <div>
                    <div class="tx-sum-label">Pemasukan</div>
                    <div class="tx-sum-val pos" id="tx-sum-inc">Rp 0</div>
                </div>
            </div>
            <div class="tx-sum-card expense-sum">
                <div class="tx-sum-icon"><i class="fas fa-arrow-up-right"></i></div>
                <div>
                    <div class="tx-sum-label">Pengeluaran</div>
                    <div class="tx-sum-val neg" id="tx-sum-exp">Rp 0</div>
                </div>
            </div>
            <div class="tx-sum-card net-sum">
                <div class="tx-sum-icon"><i class="fas fa-scale-balanced"></i></div>
                <div>
                    <div class="tx-sum-label">Net Cashflow</div>
                    <div class="tx-sum-val" id="tx-sum-net">Rp 0</div>
                </div>
            </div>
        </div>

        <!-- ══ LAYER 4: TRANSACTION LIST ══ -->
        <div class="card" style="overflow:hidden;">
            <div class="card-head" style="padding:16px 20px;">
                <span class="card-title">
                    <i class="fas fa-list-ul" style="color:var(--accent);margin-right:8px;"></i>
                    <span id="tx-tab-info" style="font-size:13px;font-weight:700;color:var(--text);">Histori Transaksi</span>
                </span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn btn-ghost btn-sm" id="btn-tx-loadmore" style="display:none;" onclick="loadMoreTxTab()">
                        <i class="fas fa-chevron-down"></i> Muat Lagi
                    </button>
                    <button class="btn btn-accent btn-sm" onclick="openModal('modal-transaction');prepTx('expense')">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <!-- Transaction list rendered here — grouped by day -->
            <div id="tx-tab-tbody" style="padding:0 0 8px;">
                <div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat data...</div>
            </div>
        </div>
    `);

    setTimeout(() => { if (typeof window.loadTransactionsTab === 'function') window.loadTransactionsTab(); }, 1);

    return function cleanup() {
        if (window._txObserver) {
            window._txObserver.disconnect();
            window._txObserver = null;
        }
        if (window._txVirtualList) {
            window._txVirtualList.destroy();
            window._txVirtualList = null;
        }
        window._txInitDone = false;
    };
};

function getDateRange(period) {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    if (period === 'today') return { start: today, end: new Date(today.getTime() + 86399999) };
    if (period === '7d') return { start: new Date(today.getTime() - 6 * 86400000), end: new Date(today.getTime() + 86399999) };
    if (period === 'this_month') return { start: new Date(now.getFullYear(), now.getMonth(), 1), end: new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59) };
    if (period === 'last_month') return { start: new Date(now.getFullYear(), now.getMonth() - 1, 1), end: new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59) };
    if (period === 'this_year') return { start: new Date(now.getFullYear(), 0, 1), end: new Date(now.getFullYear(), 11, 31, 23, 59, 59) };
    return null;
}

function renderTxTab() {
    const tbody = document.getElementById('tx-tab-tbody');
    if (!tbody) return;

    // Fallback search and filters
    const searchEl = document.getElementById('tx-search');
    const q = (searchEl?.value || '').toLowerCase();

    const typeEl = document.getElementById('tx-filter-type');
    const t = typeEl ? typeEl.value : 'all';

    const pillarEl = document.getElementById('tx-filter-pillar');
    const pillar = pillarEl ? pillarEl.value : 'all';

    const periodEl = document.getElementById('tx-filter-period');
    const period = periodEl ? periodEl.value : 'all';

    const range = getDateRange(period);

    let filtered = (allTxList || []).filter(tx => {
        if (t !== 'all' && tx.type !== t) return false;
        if (pillar !== 'all') {
            const p = tx.category?.pillar || (tx.type === 'income' ? 'income' : '');
            if (p !== pillar) return false;
        }
        if (range) {
            const d = new Date(tx.transaction_date);
            if (d < range.start || d > range.end) return false;
        }
        if (q) {
            const s = ((tx.note || '') + ' ' + (tx.category?.name || '') + ' ' + tx.amount).toLowerCase();
            if (!s.includes(q)) return false;
        }
        return true;
    });

    let sumInc = 0, sumExp = 0;
    filtered.forEach(tx => {
        if (tx.type === 'income') sumInc += parseFloat(tx.amount || 0);
        if (tx.type === 'expense') sumExp += parseFloat(tx.amount || 0);
    });

    const net = sumInc - sumExp;
    const stripEl = document.getElementById('tx-summary-strip');
    if (stripEl) {
        stripEl.style.display = filtered.length ? 'grid' : 'none';
        const incEl = document.getElementById('tx-sum-inc');
        if (incEl) incEl.textContent = rp(sumInc);

        const expEl = document.getElementById('tx-sum-exp');
        if (expEl) expEl.textContent = rp(sumExp);

        const netEl = document.getElementById('tx-sum-net');
        if (netEl) {
            netEl.textContent = rp(net);
            netEl.style.color = net >= 0 ? 'var(--accent)' : 'var(--danger)';
        }
    }

    const loadMoreBtn = document.getElementById('btn-tx-loadmore');
    const infoEl = document.getElementById('tx-tab-info');

    if (!filtered.length) {
        tbody.innerHTML = '<div class="empty" style="margin-top:24px"><i class="fas fa-folder-open" style="font-size:32px;opacity:0.2;display:block;margin-bottom:10px;"></i><div style="font-size:14px;font-weight:700">Tidak ada transaksi</div><div style="font-size:12px;opacity:0.7">Coba ubah filter pencarian</div></div>';
        if (infoEl) infoEl.textContent = 'Menampilkan 0 transaksi';
        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        return;
    }

    // Pre-calculate daily aggregates O(N) instead of O(N^2)
    const dailyNets = {};
    filtered.forEach(tx => {
        const dKey = tx.transaction_date.slice(0, 10);
        if (tx.type === 'income') dailyNets[dKey] = (dailyNets[dKey] || 0) + parseFloat(tx.amount || 0);
        else if (tx.type === 'expense') dailyNets[dKey] = (dailyNets[dKey] || 0) - parseFloat(tx.amount || 0);
    });

    let flatItems = [];
    let lastDateKey = null;

    filtered.forEach(tx => {
        const dObj = new Date(tx.transaction_date);
        const dKey = tx.transaction_date.slice(0, 10);

        if (dKey !== lastDateKey) {
            lastDateKey = dKey;
            flatItems.push({
                type: 'header',
                dateStr: dKey,
                dateObj: dObj,
                dayNet: dailyNets[dKey] || 0
            });
        }
        flatItems.push({ type: 'row', tx: tx });
    });

    if (window._txVirtualList) {
        window._txVirtualList.destroy();
        window._txVirtualList = null;
    }

    tbody.style.position = 'relative';

    window._txVirtualList = new VirtualList(tbody, {
        items: flatItems,
        getItemHeight: (item) => item.type === 'header' ? 36 : 64,
        renderItem: (item) => {
            if (item.type === 'header') {
                const dLabel = item.dateObj.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'short' });
                const netColor = item.dayNet > 0 ? 'var(--accent)' : item.dayNet < 0 ? 'var(--danger)' : 'var(--text-muted)';
                return `
                <div class="tx-day-header">
                    <span>${dLabel}</span>
                    <span class="tx-day-net" style="color:${netColor}">${item.dayNet >= 0 ? '+' : ''}${rp(item.dayNet)}</span>
                </div>`;
            } else {
                const tx = item.tx;
                const isInc = tx.type === 'income', isExp = tx.type === 'expense', isTransfer = tx.type === 'transfer';
                const sign = isInc ? '+' : isExp ? '-' : String.fromCharCode(8644);
                const catName = tx.category?.name || (isTransfer ? 'Transfer' : tx.type);
                const accName = (window.accounts || []).find(a => a.id === tx.account_id)?.name || (isTransfer ? 'Antar Akun' : '-');

                const iconBg = isInc ? 'rgba(16,185,129,.15)' : isTransfer ? 'rgba(59,130,246,.15)' : 'rgba(239,68,68,.15)';
                const iconColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';
                const icon = tx.category?.icon ? tx.category.icon : (isInc ? 'fa-arrow-down-left' : isTransfer ? 'fa-arrow-right-arrow-left' : 'fa-arrow-up-right');
                const amtColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';

                return `
                <div class="tx-ledger-row">
                    <div class="tx-row-icon" style="background:${iconBg};color:${iconColor};"><i class="fas ${icon}"></i></div>
                    <div class="tx-row-body">
                        <div class="tx-row-name">${tx.note || '<em style="opacity:.5">Tanpa Catatan</em>'}</div>
                        <div class="tx-row-meta">${catName}</div>
                    </div>
                    <div class="tx-row-right">
                        <div class="tx-row-amt" style="color:${amtColor}">${sign}${rp(tx.amount)}</div>
                        <div class="tx-row-acct"><i class="fas fa-wallet" style="font-size:9px;margin-right:3px;opacity:.7"></i>${accName}</div>
                    </div>
                    <div class="tx-row-actions">
                        <button onclick="editTx('${tx.id}')" title="Edit"><i class="fas fa-pen"></i></button>
                        <button onclick="dupTx('${tx.id}')" title="Duplikat"><i class="fas fa-copy"></i></button>
                        <button onclick="delTx('${tx.id}')" class="del" title="Hapus"><i class="fas fa-trash"></i></button>
                    </div>
                </div>`;
            }
        }
    });

    if (infoEl) infoEl.textContent = 'Menampilkan ' + filtered.length + ' transaksi';
    if (loadMoreBtn) loadMoreBtn.style.display = 'none';
}

function resetTxFilter() {
    ['tx-search', 'tx-filter-period', 'tx-filter-type', 'tx-filter-pillar'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = el.tagName === 'SELECT' ? (id === 'tx-filter-period' ? 'this_month' : 'all') : '';
    });
    applyTxFilter();
}

function dupTx(id) {
    const tx = (allTxList || []).find(t => String(t.id) === String(id));
    if (!tx) return toast('Transaksi tidak ditemukan', 'e');
    openModal('modal-transaction');
    setTimeout(() => {
        if (typeof setType === 'function') setType(tx.type);
        document.getElementById('tx-amount').value = tx.amount;
        document.getElementById('tx-note').value = (tx.note || '') + ' (Copy)';
        document.getElementById('tx-date').value = new Date().toISOString().slice(0, 16);
        if (tx.category_id && document.getElementById('tx-category')) document.getElementById('tx-category').value = tx.category_id;
        if (tx.account_id && document.getElementById('tx-account')) document.getElementById('tx-account').value = tx.account_id;
        document.getElementById('modal-transaction').dataset.editId = ''; // Create new
    }, 150);
}

function applyTxFilter() { renderTxTab(); }
function loadMoreTxTab() { /* Obsoleted by VirtualList */ }

let _txInitDone = false;
function loadTransactionsTab() {
    if (!_txInitDone) {
        _txInitDone = true;
    }
    renderTxTab();
}


function editTx(id) {
    const tx = (allTxList || []).find(t => String(t.id) === String(id));
    if (!tx) return toast('Transaksi tidak ditemukan', 'e');
    openModal('modal-transaction');
    setTimeout(() => {
        if (typeof setType === 'function') setType(tx.type);
        document.getElementById('tx-amount').value = tx.amount;
        document.getElementById('tx-note').value = tx.note || '';
        document.getElementById('tx-date').value = tx.transaction_date.slice(0, 16); // format datetime-local
        const accSelect = document.getElementById('tx-account');
        if (accSelect) accSelect.value = tx.account_id;
        const catSelect = document.getElementById('tx-category');
        if (tx.category_id && catSelect) catSelect.value = tx.category_id;
        document.getElementById('modal-transaction').dataset.editId = String(id);

        // Update custom dropdowns visuals if they exist
        if (typeof setCddDisplay === 'function') {
            const acc = (accounts || []).find(a => a.id === tx.account_id);
            if (acc) setCddDisplay('cdd-account-icon', 'cdd-account-lbl', acc.name, 'fa-wallet', null, false);

            if (tx.category_id) {
                const cat = (categories || []).find(c => c.id === tx.category_id);
                if (cat) {
                    const [ico, col] = CAT_ICONS[cat.name] || DEFAULT_ICON;
                    setCddDisplay('cdd-category-icon', 'cdd-category-lbl', cat.name, ico, col, false);
                }
            }
        }
    }, 80);
}

async function delTx(id) {
    confirmDialog(
        'Hapus Transaksi',
        'Transaksi ini akan dihapus dan tidak bisa dikembalikan. Lanjutkan?',
        'danger',
        async () => {
            try {
                await localAPI.transactions.delete(String(id));
                toast('Transaksi dihapus');
                if (typeof refreshUI === 'function') await refreshUI();
            } catch (e) {
                toast(e.message || 'Gagal menghapus', 'e');
            }
        }
    );
}

window.getDateRange = getDateRange;
window.renderTxTab = renderTxTab;
window.resetTxFilter = resetTxFilter;
window.dupTx = dupTx;
window.applyTxFilter = applyTxFilter;
window.loadMoreTxTab = loadMoreTxTab;
window.loadTransactionsTab = loadTransactionsTab;
window.editTx = editTx;
window.delTx = delTx;
