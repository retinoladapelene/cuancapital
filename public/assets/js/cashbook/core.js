/**
 * Core Initialization, Database setup, and Repositories for Cashbook
 */

// Global Variables
let indexedDB;
if (window.indexedDB) {
    indexedDB = window.indexedDB;
} else if (window.mozIndexedDB) {
    indexedDB = window.mozIndexedDB;
} else if (window.webkitIndexedDB) {
    indexedDB = window.webkitIndexedDB;
} else if (window.msIndexedDB) {
    indexedDB = window.msIndexedDB;
}

const DB_NAME = 'CuanCashbookDB';
const DB_VERSION = 4; // Incremented for installments support

let allTxList = [];
let lapTxVisibleCount = 20;

// ── Central State Management ──
// Single source of truth for all modules.
// Modules should READ from here instead of fetching independently.
window.cashbookState = {
    activeTab: 'overview',
    transactions: [],
    categories: [],
    accounts: [],
    debts: [],
    budgets: [],
    initialized: false,
};


// Formatting utility (re-implemented here if not in ui.js, or assume it's global)
function rp(v) {
    if (v === null || v === undefined) return 'Rp 0';
    return 'Rp ' + parseFloat(v).toLocaleString('id-ID');
}

// Local API wrapper
const localAPI = {
    db: null,
    async init() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains('transactions')) {
                    const store = db.createObjectStore('transactions', { keyPath: 'id' });
                    store.createIndex('date', 'transaction_date', { unique: false });
                    store.createIndex('type', 'type', { unique: false });
                }
                if (!db.objectStoreNames.contains('accounts')) db.createObjectStore('accounts', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('categories')) db.createObjectStore('categories', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('budgets')) db.createObjectStore('budgets', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('debts')) db.createObjectStore('debts', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('installments')) db.createObjectStore('installments', { keyPath: 'id' });
                if (!db.objectStoreNames.contains('meta')) db.createObjectStore('meta', { keyPath: 'key' });
            };
            req.onsuccess = (e) => { this.db = e.target.result; resolve(this.db); };
            req.onerror = (e) => reject(e.target.error);
        });
    },
    async _runOp(storeName, mode, callback) {
        if (!this.db) await this.init();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(storeName, mode);
            const store = tx.objectStore(storeName);
            let result;
            try {
                const req = callback(store);
                if (req) {
                    req.onsuccess = () => { result = req.result; };
                    req.onerror = () => reject(req.error);
                }
            } catch (e) { reject(e); }
            tx.oncomplete = () => resolve(result);
            tx.onerror = () => reject(tx.error);
        });
    },
    meta: {
        get: (key) => localAPI._runOp('meta', 'readonly', s => s.get(key)).then(r => r ? r.value : null),
        set: (key, value) => localAPI._runOp('meta', 'readwrite', s => s.put({ key, value })),
        getAll: () => localAPI._runOp('meta', 'readonly', s => s.getAll())
    },
    store(name) {
        return {
            getAll: () => localAPI._runOp(name, 'readonly', s => s.getAll()),
            save: (item) => localAPI._runOp(name, 'readwrite', s => s.put(item)),
            delete: (id) => localAPI._runOp(name, 'readwrite', s => s.delete(id))
        };
    }
};

localAPI.transactions = localAPI.store('transactions');
localAPI.accounts = localAPI.store('accounts');
localAPI.categories = localAPI.store('categories');
localAPI.budgets = localAPI.store('budgets');
localAPI.debts = localAPI.store('debts');
localAPI.installments = localAPI.store('installments');

function generateId() { return Date.now().toString(36) + Math.random().toString(36).substr(2, 5); }

// The unified repository & service (handles real server sync if needed later, right now pure offline)
const CashbookService = {
    async addTransaction(data) {
        data.id = data.id || generateId();

        let txForDb;
        if (data.type === 'transfer') {
            txForDb = { ...data, category_id: null, category: { name: 'Transfer' }, updated_at: new Date().toISOString() };
        } else {
            const categories = await localAPI.categories.getAll();
            const cat = categories.find(c => String(c.id) === String(data.category_id));
            if (!cat) throw new Error("Kategori tidak valid");
            data.category = cat;
            txForDb = { ...data, updated_at: new Date().toISOString() };
        }

        // Adjust balance
        const accs = await localAPI.accounts.getAll();

        if (data.type === 'transfer') {
            const fromAcc = accs.find(a => String(a.id) === String(data.account_id));
            const toAcc = accs.find(a => String(a.id) === String(data.target_account_id));
            if (!fromAcc || !toAcc) throw new Error("Akun sumber/tujuan tidak valid");

            fromAcc.balance_cached = parseFloat(fromAcc.balance_cached || 0) - parseFloat(data.amount);
            toAcc.balance_cached = parseFloat(toAcc.balance_cached || 0) + parseFloat(data.amount);

            await localAPI.accounts.save(fromAcc);
            await localAPI.accounts.save(toAcc);
        } else {
            const acc = accs.find(a => String(a.id) === String(data.account_id));
            if (!acc) throw new Error("Akun tidak valid");

            const amt = parseFloat(data.amount);
            if (data.type === 'income') acc.balance_cached = parseFloat(acc.balance_cached || 0) + amt;
            else if (data.type === 'expense') acc.balance_cached = parseFloat(acc.balance_cached || 0) - amt;

            await localAPI.accounts.save(acc);
        }

        await localAPI.transactions.save(txForDb);
        return txForDb;
    },

    async saveBudget(data) {
        if (!data.id) data.id = generateId();
        data.updated_at = new Date().toISOString();
        await localAPI.budgets.save(data);
        return data;
    },
    async deleteBudget(id) { await localAPI.budgets.delete(id); },

    async addDebt(data) {
        data.id = data.id || generateId();
        data.status = 'active';
        data.created_at = new Date().toISOString();
        data.updated_at = new Date().toISOString();
        const debts = await localAPI.debts.getAll();
        debts.push(data);
        await localAPI.debts.save(data);
        return data;
    },

    async payDebt(debtId, amount, accountId, date, notes) {
        const debts = await localAPI.debts.getAll();
        const accs = await localAPI.accounts.getAll();

        const debt = debts.find(d => String(d.id) === String(debtId));
        if (!debt) throw new Error("Utang tidak ditemukan");

        const acc = accs.find(a => String(a.id) === String(accountId));
        if (!acc) throw new Error("Akun tidak ditemukan");

        // Validate account balance if paying a debt
        if (debt.debt_type === 'payable') {
            const currentBal = parseFloat(acc.balance_cached || 0);
            if (currentBal < amount && amount > 0) {
                throw new Error(`Saldo tidak mencukupi. Saldo akun: ${rp(currentBal)}`);
            }
        }

        // Generate Installment Record
        const instId = generateId();
        const instData = {
            id: instId,
            debt_id: debt.id,
            account_id: accountId,
            amount: amount,
            payment_date: date,
            notes: notes,
            created_at: new Date().toISOString()
        };
        await localAPI.installments.save(instData);

        // Update Debt
        debt.paid_amount = parseFloat(debt.paid_amount || 0) + amount;
        if (debt.paid_amount >= parseFloat(debt.total_amount || 0)) {
            debt.status = 'paid_off';
        }
        debt.updated_at = new Date().toISOString();
        await localAPI.debts.save(debt);

        // Update Account Balance
        if (debt.debt_type === 'payable') {
            acc.balance_cached = parseFloat(acc.balance_cached || 0) - amount; // paying debt -> money out
        } else {
            acc.balance_cached = parseFloat(acc.balance_cached || 0) + amount; // receiving receivable -> money in
        }
        await localAPI.accounts.save(acc);

        // Record as Transaction for ledger
        const isPayable = debt.debt_type === 'payable';
        const txName = isPayable ? `Bayar Cicilan: ${debt.name}` : `Terima Piutang: ${debt.name}`;
        await CashbookService.addTransaction({
            account_id: accountId,
            category_id: null,
            type: isPayable ? 'expense' : 'income',
            amount: amount,
            transaction_date: date + 'T00:00:00', // Ensure datetime format
            note: notes ? `${txName} - ${notes}` : txName
        });

        return { debt, installment: instData, account: acc };
    },

    async deleteDebt(debtId) {
        // Needs reversal logic if you want true accounting, but for now just delete the entity
        await localAPI.debts.delete(debtId);
    }
};

let accounts = [], categories = [];

const CAT_ICONS = {
    'Makan & Minuman': ['fa-utensils', '#10b981'],
    'Transportasi': ['fa-car', '#3b82f6'],
    'Listrik & Air': ['fa-bolt', '#f59e0b'],
    'Sewa / KPR': ['fa-house', '#8b5cf6'],
    'Kesehatan': ['fa-heart-pulse', '#ef4444'],
    'Tabungan / Investasi': ['fa-piggy-bank', '#10b981'],
    'Pendidikan': ['fa-graduation-cap', '#6366f1'],
    'Bisnis / Modal': ['fa-briefcase', '#0ea5e9'],
    'Hiburan': ['fa-gamepad', '#a855f7'],
    'Belanja Pribadi': ['fa-bag-shopping', '#f97316'],
    'Langganan Digital': ['fa-tv', '#06b6d4'],
    'Liburan': ['fa-plane', '#84cc16'],
    'Jajan / Impulsif': ['fa-ice-cream', '#f43f5e'],
    'Denda & Charge': ['fa-triangle-exclamation', '#ef4444'],
    'Tak Terduga': ['fa-circle-exclamation', '#dc2626'],
    'Gaji / Penghasilan': ['fa-money-bill-wave', '#10b981'],
};
const INCOME_NAMES = ['Gaji / Penghasilan', 'Tabungan / Investasi', 'Bisnis / Modal'];
const DEFAULT_ICON = ['fa-tag', '#94a3b8'];

const DEFAULT_CATEGORIES = [
    { name: 'Makan & Minuman', pillar: 'wajib', type: 'expense' },
    { name: 'Transportasi', pillar: 'wajib', type: 'expense' },
    { name: 'Listrik & Air', pillar: 'wajib', type: 'expense' },
    { name: 'Sewa / KPR', pillar: 'wajib', type: 'expense' },
    { name: 'Kesehatan', pillar: 'wajib', type: 'expense' },
    { name: 'Pendidikan', pillar: 'growth', type: 'expense' },
    { name: 'Hiburan', pillar: 'lifestyle', type: 'expense' },
    { name: 'Belanja Pribadi', pillar: 'lifestyle', type: 'expense' },
    { name: 'Langganan Digital', pillar: 'lifestyle', type: 'expense' },
    { name: 'Liburan', pillar: 'lifestyle', type: 'expense' },
    { name: 'Jajan / Impulsif', pillar: 'bocor', type: 'expense' },
    { name: 'Denda & Charge', pillar: 'bocor', type: 'expense' },
    { name: 'Gaji / Penghasilan', pillar: 'income', type: 'income' },
    { name: 'Tabungan / Investasi', pillar: 'income', type: 'income' },
    { name: 'Bisnis / Modal', pillar: 'income', type: 'income' }
];

async function loadMaster() {
    if (!localAPI.db) await localAPI.init();

    accounts = await localAPI.accounts.getAll();
    const rawCats = await localAPI.categories.getAll();
    const seenCatNames = new Set();
    categories = rawCats.filter(c => {
        if (seenCatNames.has(c.name)) return false;
        seenCatNames.add(c.name);
        return true;
    });

    if (categories.length === 0) {
        for (const cat of DEFAULT_CATEGORIES) {
            cat.id = generateId();
            // Map icon & color from CAT_ICONS if undefined
            if (!cat.icon || !cat.color) {
                const icData = CAT_ICONS[cat.name] || DEFAULT_ICON;
                cat.icon = icData[0];
                cat.color = icData[1];
            }
            const saved = await localAPI.categories.save(cat);
            categories.push({ ...cat });
        }
    } else {
        // Auto-patch existing DB categories missing icons
        let dirty = false;
        for (const cat of categories) {
            if (!cat.icon || !cat.color) {
                const icData = CAT_ICONS[cat.name] || DEFAULT_ICON;
                cat.icon = icData[0];
                cat.color = icData[1];
                await localAPI.categories.save(cat);
                dirty = true;
            }
        }
    }

    const allTxs = await localAPI.transactions.getAll();
    allTxList = allTxs.map(tx => {
        const cat = categories.find(c => String(c.id) === String(tx.category_id)) || null;
        return { ...tx, category: cat };
    }).sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date));

    accounts.forEach(acc => {
        acc.balance_cached = parseFloat(acc.initial_balance || 0);
        allTxs.forEach(tx => {
            if (tx.type === 'income' && String(tx.account_id) === String(acc.id)) acc.balance_cached += parseFloat(tx.amount);
            if (tx.type === 'expense' && String(tx.account_id) === String(acc.id)) acc.balance_cached -= parseFloat(tx.amount);
            if (tx.type === 'transfer' && String(tx.from_account_id) === String(acc.id)) acc.balance_cached -= parseFloat(tx.amount);
            if (tx.type === 'transfer' && String(tx.to_account_id) === String(acc.id)) acc.balance_cached += parseFloat(tx.amount);
        });
    });

    window.accounts = accounts;
    window.categories = categories;
    window.allTxList = allTxList;

    if (typeof window.renderAccBar === 'function') window.renderAccBar();
    if (typeof window.fillSelects === 'function') window.fillSelects();
}

async function refreshUI(forceAll = false) {
    await loadMaster();

    // Use window.activeTab (set by the router in ui.js) as source of truth.
    // In Single Active View architecture, there are no longer separate #view-X DOM IDs.
    const activeTabId = (window.cashbookState && window.cashbookState.activeTab)
        || window.activeTab
        || 'overview';

    const runIdle = window.requestIdleCallback || ((cb) => setTimeout(cb, 1));

    // Smart partial refresh: update only what's visible, don't re-render the whole view
    if (activeTabId === 'overview' && typeof window.loadDashboard === 'function') {
        runIdle(() => window.loadDashboard());
    } else if (activeTabId === 'transaksi' && typeof window.renderTxTab === 'function') {
        runIdle(() => window.renderTxTab());
    } else if (activeTabId === 'laporan' && typeof window.generateReport === 'function') {
        runIdle(() => window.generateReport());
    } else if (activeTabId === 'anggaran' && typeof window.loadAnggaran === 'function') {
        runIdle(() => window.loadAnggaran());
    } else if ((activeTabId === 'utang' || activeTabId === 'debts') && typeof window.loadDebts === 'function') {
        runIdle(() => window.loadDebts());
    }
}

window.localAPI = localAPI;
window.CashbookService = CashbookService;
window.DB_VERSION = DB_VERSION;
window.CAT_ICONS = CAT_ICONS;
window.INCOME_NAMES = INCOME_NAMES;
window.DEFAULT_ICON = DEFAULT_ICON;
window.DEFAULT_CATEGORIES = DEFAULT_CATEGORIES;
window.loadMaster = loadMaster;
window.refreshUI = refreshUI;
window.generateId = generateId;
window.accounts = accounts;
window.categories = categories;

document.addEventListener('DOMContentLoaded', () => {
    if (window.Chart) {
        Chart.defaults.animation = false;
        Chart.defaults.animations = { colors: false, x: false };
        Chart.defaults.transitions = { active: { animation: { duration: 0 } } };
    }

    const runIdleInitialization = async () => {
        if (window.__cashbookInit) return; // Prevent double execution
        window.__cashbookInit = true;

        try {
            await loadMaster();

            // Boot the initial tab from ?tab= URL param, or default to 'overview'
            // In Single Active View, we don't query #view-X DOM IDs anymore
            const urlParams = new URLSearchParams(window.location.search);
            const selTab = urlParams.get('tab') || 'overview';

            if (typeof window.switchMainTab === 'function') {
                window.switchMainTab(selTab);
            }

            try {
                const metaSettings = await localAPI.meta.getAll();
                const autoEnabled = metaSettings.find(m => m.key === 'auto_backup_enabled')?.value === 'true';
                const intervalDays = parseInt(metaSettings.find(m => m.key === 'backup_interval_days')?.value || '7');
                const lastBackupStr = metaSettings.find(m => m.key === 'last_backup_at')?.value;
                const now = new Date();

                if (autoEnabled) {
                    let shouldBackup = false;

                    if (!lastBackupStr) {
                        shouldBackup = true;
                    } else {
                        const lastBackup = new Date(lastBackupStr);
                        const diffDays = (now - lastBackup) / (1000 * 60 * 60 * 24);
                        if (diffDays >= intervalDays) {
                            shouldBackup = true;
                        }
                    }

                    if (shouldBackup) {
                        toast('Auto Backup berjalan...', 'i');
                        let installments = [];
                        try { installments = await localAPI.installments.getAll(); } catch (_) { }

                        const backupData = {
                            exported_at: now.toISOString(),
                            version: 1,
                            type: 'auto_backup',
                            data: {
                                meta: metaSettings,
                                categories: await localAPI.categories.getAll(),
                                accounts: await localAPI.accounts.getAll(),
                                transactions: await localAPI.transactions.getAll(),
                                budgets: await localAPI.budgets.getAll(),
                                debts: await localAPI.debts.getAll(),
                                installments
                            }
                        };

                        const blob = new Blob([JSON.stringify(backupData, null, 2)], { type: 'application/json' });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = `cuan_cashbook_autobackup_${now.toISOString().slice(0, 10)}.json`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);

                        await localAPI.meta.set('last_backup_at', now.toISOString());
                        console.log('Auto backup completed successfully');
                    }
                }
            } catch (e) {
                console.error('Failed to run auto backup', e);
            }

        } catch (e) {
            console.error('Initialization error:', e);
        }
    };

    if (window.requestIdleCallback) {
        requestIdleCallback(runIdleInitialization, { timeout: 2000 });
    } else {
        setTimeout(runIdleInitialization, 50);
    }
});

// ── setType: switch transaction type in the modal ──
let currentTxType = 'expense';
window.currentTxType = 'expense'; // ensure initial state is exported
function setType(type) {
    currentTxType = type;
    window.currentTxType = type; // CRITICAL: update the window obj so accounts.js sees it

    // Update tab highlight
    ['income', 'expense', 'transfer'].forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (!btn) return;
        btn.className = 'ttab' + (t === 'income' ? ' i' : t === 'expense' ? ' e' : ' t');
        if (t === type) {
            btn.classList.add('active');
        }
    });

    // Toggle transfer vs single account UI
    const singleAcc = document.getElementById('tx-single-acc');
    const transferBox = document.getElementById('tx-transfer-box');
    const catWrap = document.getElementById('tx-cat-wrap');
    const catLabel = document.getElementById('cat-label');
    const saveBtn = document.getElementById('btn-save-tx');

    if (type === 'transfer') {
        if (singleAcc) singleAcc.style.display = 'none';
        if (transferBox) transferBox.style.display = 'block';
        if (catWrap) catWrap.style.display = 'none';
        if (saveBtn) saveBtn.style.background = 'var(--info)';
    } else {
        if (singleAcc) singleAcc.style.display = 'block';
        if (transferBox) transferBox.style.display = 'none';
        if (catWrap) catWrap.style.display = 'block';
        if (catLabel) catLabel.textContent = type === 'income' ? 'Sumber' : 'Kategori';
        if (saveBtn) saveBtn.style.background = type === 'income' ? 'var(--accent)' : 'var(--danger)';
    }

    // Reload category dropdown for this type
    if (typeof buildCategoryDropdown === 'function') buildCategoryDropdown(type);
    else loadCategoriesForModal(type);
}

function loadCategoriesForModal(type) {
    const menu = document.getElementById('cdd-category-menu');
    const hiddenInput = document.getElementById('tx-category-id');
    const lbl = document.getElementById('cdd-cat-lbl');
    const ico = document.getElementById('cdd-cat-icon');

    if (lbl) { lbl.textContent = 'Tanpa kategori'; lbl.className = 'cdd-trigger-lbl placeholder'; }
    if (hiddenInput) hiddenInput.value = '';
    if (ico) ico.innerHTML = '<i class="fas fa-tag" style="color:var(--text-muted)"></i>';

    if (!menu) return;

    const cats = (categories || []).filter(c => {
        if (type === 'income') return c.pillar === 'income' || !c.pillar;
        return c.pillar !== 'income';
    });

    const PILLAR_LABELS = { wajib: 'Wajib', growth: 'Growth', lifestyle: 'Lifestyle', bocor: 'Bocor', income: 'Pemasukan' };
    const grouped = {};
    cats.forEach(c => {
        const p = c.pillar || 'lainnya';
        if (!grouped[p]) grouped[p] = [];
        grouped[p].push(c);
    });

    // Use data-attributes to avoid inline onclick quoting issues with raw string IDs
    const sections = Object.entries(grouped).map(([pillar, items]) => {
        const itemsHtml = items.map(c => {
            const safeId = String(c.id).replace(/"/g, '');
            const safeName = (c.name || '').replace(/"/g, '&quot;');
            const safeIcon = (c.icon || 'fa-tag').replace(/"/g, '');
            const safeColor = (c.color || '').replace(/"/g, '');
            const bg = (c.color || '#94a3b8') + '20';
            const col = c.color || '#94a3b8';
            return `<div class="cdd-item" data-cat-id="${safeId}" data-cat-name="${safeName}" data-cat-icon="${safeIcon}" data-cat-color="${safeColor}">` +
                `<div class="cdd-item-icon" style="background:${bg};color:${col}"><i class="fas ${c.icon || 'fa-tag'}"></i></div>` +
                `<span class="cdd-item-lbl">${c.name}</span></div>`;
        }).join('');
        return `<div class="cdd-section"><div class="cdd-section-label">${PILLAR_LABELS[pillar] || pillar}</div>${itemsHtml}</div>`;
    }).join('');

    menu.innerHTML = sections || '<div class="cdd-item" style="color:var(--text-muted)">Belum ada kategori</div>';

    // Event delegation — avoids ALL inline onclick quoting issues
    menu.onclick = (e) => {
        const item = e.target.closest('[data-cat-id]');
        if (!item) return;
        selectCategory(item.dataset.catId, item.dataset.catName, item.dataset.catIcon, item.dataset.catColor);
    };
}

function selectCategory(id, name, icon, color) {
    const hiddenInput = document.getElementById('tx-category-id');
    if (hiddenInput) hiddenInput.value = id;
    const lbl = document.getElementById('cdd-cat-lbl');
    if (lbl) { lbl.textContent = name; lbl.className = 'cdd-trigger-lbl'; }
    const ico = document.getElementById('cdd-cat-icon');
    if (ico) ico.innerHTML = `<i class="fas ${icon || 'fa-tag'}" style="color:${color || 'var(--accent)'}"></i>`;
    // Close dropdown
    const menu = document.getElementById('cdd-category-menu');
    if (menu) menu.classList.remove('open');
}

window.setType = setType;
window.selectCategory = selectCategory;
window.loadCategoriesForModal = loadCategoriesForModal;
window.currentTxType = currentTxType;
