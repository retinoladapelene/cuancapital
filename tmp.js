>(function(){if(!localStorage.getItem('auth_token'))window.location.href='/?auth_action=login&redirect=cashbook';var t=localStorage.getItem('cb_theme')||'dark';document.documentElement.setAttribute('data-theme',t);})(); src="https://cdn.jsdelivr.net/npm/chart.js">>
    // ─── IndexedDB Configuration ───
    const DB_NAME = 'cuancapital_cashbook_db';
    const DB_VERSION = 1;

    let db;
    const initDB = () => new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = (event) => {
            const tempDb = event.target.result;
            if (!tempDb.objectStoreNames.contains('transactions')) tempDb.createObjectStore('transactions', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('categories')) tempDb.createObjectStore('categories', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('accounts')) tempDb.createObjectStore('accounts', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('budgets')) tempDb.createObjectStore('budgets', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('debts')) tempDb.createObjectStore('debts', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('installments')) tempDb.createObjectStore('installments', { keyPath: 'id' });
            if (!tempDb.objectStoreNames.contains('meta')) tempDb.createObjectStore('meta', { keyPath: 'key' });
        };
        request.onsuccess = (event) => {
            db = event.target.result;
            // Seed default meta if not exists
            localAPI.meta.get('schema_version').then(res => {
                if (!res) {
                    localAPI.meta.save('schema_version', 1);
                    localAPI.meta.save('auto_backup_enabled', false);
                    localAPI.meta.save('backup_interval_days', 7);
                    localAPI.meta.save('last_backup_at', null);
                }
            });
            resolve(db);
        };
        request.onerror = (event) => reject(event.target.error);
    });

    const generateId = () => Date.now().toString() + Math.random().toString(36).substr(2, 5);

    // ─── Local API Wrapper ───
    const localAPI = {
        _runOp: (storeName, mode, callback) => new Promise((resolve, reject) => {
            if (!db) return reject('Database not initialized');
            const tx = db.transaction(storeName, mode);
            const store = tx.objectStore(storeName);
            let req;
            try { req = callback(store); } catch(e) { return reject(e); }
            if (req && req.onsuccess !== undefined) {
                req.onsuccess = () => resolve(req.result);
                req.onerror = () => reject(req.error);
            } else {
                tx.oncomplete = () => resolve(true);
                tx.onerror = () => reject(tx.error);
            }
        }),

        transactions: {
            getAll: async () => {
                const txs = await localAPI._runOp('transactions', 'readonly', s => s.getAll());
                return txs.filter(t => !t.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('transactions', 'readwrite', s => s.put(record));
                return record;
            },
            delete: async (id) => {
                const record = await localAPI._runOp('transactions', 'readonly', s => s.get(id));
                if (record) {
                    record.deleted_at = new Date().toISOString();
                    await localAPI._runOp('transactions', 'readwrite', s => s.put(record));
                }
            }
        },

        categories: {
            getAll: async () => {
                const cats = await localAPI._runOp('categories', 'readonly', s => s.getAll());
                return cats.filter(c => !c.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('categories', 'readwrite', s => s.put(record));
                return record;
            }
        },
        
        accounts: {
            getAll: async () => {
                const accs = await localAPI._runOp('accounts', 'readonly', s => s.getAll());
                return accs.filter(a => !a.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('accounts', 'readwrite', s => s.put(record));
                return record;
            }
        },

        budgets: {
            getAll: async () => {
                const bgts = await localAPI._runOp('budgets', 'readonly', s => s.getAll());
                return bgts.filter(b => !b.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('budgets', 'readwrite', s => s.put(record));
                return record;
            },
            delete: async (id) => {
                const record = await localAPI._runOp('budgets', 'readonly', s => s.get(id));
                if (record) {
                    record.deleted_at = new Date().toISOString();
                    await localAPI._runOp('budgets', 'readwrite', s => s.put(record));
                }
            }
        },

        debts: {
            getAll: async () => {
                const debts = await localAPI._runOp('debts', 'readonly', s => s.getAll());
                return debts.filter(d => !d.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('debts', 'readwrite', s => s.put(record));
                return record;
            },
            delete: async (id) => {
                const record = await localAPI._runOp('debts', 'readonly', s => s.get(id));
                if (record) {
                    record.deleted_at = new Date().toISOString();
                    await localAPI._runOp('debts', 'readwrite', s => s.put(record));
                }
            }
        },

        installments: {
            getAll: async () => {
                const insts = await localAPI._runOp('installments', 'readonly', s => s.getAll());
                return insts.filter(i => !i.deleted_at);
            },
            save: async (data) => {
                const id = data.id || generateId();
                const now = new Date().toISOString();
                const record = { ...data, id, updated_at: now, created_at: data.created_at || now, deleted_at: null };
                await localAPI._runOp('installments', 'readwrite', s => s.put(record));
                return record;
            }
        },

        meta: {
            getAll: async () => {
                return await localAPI._runOp('meta', 'readonly', s => s.getAll());
            },
            get: async (key) => {
                const entry = await localAPI._runOp('meta', 'readonly', s => s.get(key));
                return entry ? entry.value : null;
            },
            set: async (key, value) => {
                await localAPI._runOp('meta', 'readwrite', s => s.put({ key, value }));
            },
            save: async (key, value) => {
                await localAPI._runOp('meta', 'readwrite', s => s.put({ key, value }));
            }
        }
    };

    // ============================================================
    // CASHBOOK REPOSITORY — Storage layer. Hard rules:
    //   1. NEVER returns deleted records (filter in Repository)
    //   2. Named per-store methods (Service doesn't know store names)
    //   3. String() ID normalization always applied
    // ============================================================
    const CashbookRepository = {
        // Internal helpers
        _save: async (store, data) => {
            const now = new Date().toISOString();
            const record = {
                ...data,
                id: data.id || generateId(),
                created_at: data.created_at || now,
                updated_at: now,
                deleted_at: data.deleted_at || null
            };
            await localAPI._runOp(store, 'readwrite', s => s.put(record));
            return record;
        },
        _getById: async (store, id) => {
            if (!id) throw new Error(`ID required for ${store}`);
            return localAPI._runOp(store, 'readonly', s => s.get(String(id)));
        },
        _softDelete: async (store, id) => {
            const record = await CashbookRepository._getById(store, id);
            if (!record) throw new Error('Record tidak ditemukan');
            record.deleted_at = new Date().toISOString();
            return CashbookRepository._save(store, record);
        },

        // Named per-store read methods (always filter deleted_at)
        async getTransactions() {
            const rows = await localAPI._runOp('transactions', 'readonly', s => s.getAll());
            return rows.filter(r => !r.deleted_at);
        },
        async getAccounts() {
            const rows = await localAPI._runOp('accounts', 'readonly', s => s.getAll());
            return rows.filter(r => !r.deleted_at);
        },
        async getCategories() {
            const rows = await localAPI._runOp('categories', 'readonly', s => s.getAll());
            return rows.filter(r => !r.deleted_at);
        },
        async getBudgets() {
            const rows = await localAPI._runOp('budgets', 'readonly', s => s.getAll());
            return rows.filter(r => !r.deleted_at);
        },
        async getDebts() {
            const rows = await localAPI._runOp('debts', 'readonly', s => s.getAll());
            return rows.filter(r => !r.deleted_at);
        },

        // Generic by-id / save / softDelete (store name accepted here only)
        getById: (store, id) => CashbookRepository._getById(store, id),
        save:    (store, data) => CashbookRepository._save(store, data),
        softDelete: (store, id) => CashbookRepository._softDelete(store, id),
    };

    // ============================================================
    // CASHBOOK SERVICE — Business logic layer. Hard rules:
    //   1. All validation here (throw Error — UI only try/catch)
    //   2. No store name strings (only CashbookRepository methods)
    //   3. Complex ops atomic: rollback if second step fails
    // ============================================================
    const CashbookService = {

        // ── Transactions ─────────────────────────────────────
        async addTransaction(data) {
            if (!data.amount || Number(data.amount) <= 0) throw new Error('Jumlah transaksi harus lebih dari 0');
            if (!data.account_id) throw new Error('Pilih akun terlebih dahulu');
            if (!data.transaction_date) throw new Error('Tanggal transaksi diperlukan');
            if (!data.type || !['income','expense','transfer'].includes(data.type)) throw new Error('Tipe transaksi tidak valid');
            return CashbookRepository.save('transactions', {
                ...data,
                amount: Number(data.amount),
                account_id: String(data.account_id)
            });
        },

        async editTransaction(id, data) {
            if (!id) throw new Error('ID transaksi tidak valid');
            const existing = await CashbookRepository.getById('transactions', id);
            if (!existing) throw new Error('Transaksi tidak ditemukan');
            if (data.amount !== undefined && Number(data.amount) <= 0) throw new Error('Jumlah harus lebih dari 0');
            return CashbookRepository.save('transactions', {
                ...existing, ...data,
                id: String(id),
                amount: Number(data.amount || existing.amount),
                account_id: String(data.account_id || existing.account_id)
            });
        },

        async deleteTransaction(id) {
            if (!id) throw new Error('ID transaksi tidak valid');
            return CashbookRepository.softDelete('transactions', id);
        },

        async getLedger() {
            return CashbookRepository.getTransactions();
        },

        async getAccountBalance(accountId) {
            if (!accountId) throw new Error('Account ID diperlukan');
            const ledger = await CashbookRepository.getTransactions();
            const initial = (await CashbookRepository.getAccounts())
                .find(a => String(a.id) === String(accountId))?.initial_balance || 0;
            return ledger
                .filter(t => String(t.account_id) === String(accountId))
                .reduce((acc, t) => {
                    if (t.type === 'income')   return acc + Number(t.amount);
                    if (t.type === 'expense')  return acc - Number(t.amount);
                    if (t.type === 'transfer') {
                        if (String(t.account_id) === String(accountId)) return acc - Number(t.amount);
                        if (String(t.to_account_id) === String(accountId)) return acc + Number(t.amount);
                    }
                    return acc;
                }, Number(initial));
        },

        // ── Debts ─────────────────────────────────────────────
        async addDebt(data) {
            if (!data.name || !data.name.trim()) throw new Error('Nama hutang diperlukan');
            if (!data.total_amount || Number(data.total_amount) <= 0) throw new Error('Total hutang harus lebih dari 0');
            return CashbookRepository.save('debts', {
                ...data,
                total_amount: Number(data.total_amount),
                paid_amount: 0
            });
        },

        async payDebt(debtId, amount, accountId, date, note) {
            if (!debtId) throw new Error('ID hutang tidak valid');
            if (!amount || Number(amount) <= 0) throw new Error('Jumlah bayar harus lebih dari 0');
            if (!accountId) throw new Error('Pilih akun untuk pembayaran');
            if (!date) throw new Error('Tanggal pembayaran diperlukan');

            const debt = await CashbookRepository.getById('debts', debtId);
            if (!debt) throw new Error('Data hutang tidak ditemukan');

            const remaining = Number(debt.total_amount) - Number(debt.paid_amount || 0);
            if (Number(amount) > remaining) throw new Error(`Jumlah melebihi sisa hutang (${remaining.toLocaleString('id-ID')})`);

            if (debt.type === 'payable') {
                const balance = await CashbookService.getAccountBalance(accountId);
                if (balance < Number(amount)) throw new Error('Saldo akun tidak mencukupi');
            }

            // Atomic: update debt first, rollback if tx insert fails
            const prevPaid = Number(debt.paid_amount || 0);
            const updatedDebt = {
                ...debt,
                paid_amount: prevPaid + Number(amount)
            };
            await CashbookRepository.save('debts', updatedDebt);

            try {
                await CashbookRepository.save('transactions', {
                    type: debt.type === 'payable' ? 'expense' : 'income',
                    amount: Number(amount),
                    account_id: String(accountId),
                    transaction_date: date,
                    note: note || `Pembayaran: ${debt.name}`,
                    source: 'debt_payment',
                    debt_id: String(debtId)
                });
            } catch (txErr) {
                // Rollback debt update
                await CashbookRepository.save('debts', { ...debt, paid_amount: prevPaid });
                throw new Error('Gagal mencatat transaksi pembayaran: ' + txErr.message);
            }
            return true;
        },

        async deleteDebt(id) {
            if (!id) throw new Error('ID hutang tidak valid');
            return CashbookRepository.softDelete('debts', id);
        },

        // ── Budgets ───────────────────────────────────────────
        async saveBudget(data) {
            if (!data.category_id) throw new Error('Pilih kategori anggaran');
            if (!data.limit_amount || Number(data.limit_amount) <= 0) throw new Error('Limit anggaran harus lebih dari 0');
            if (!data.month) throw new Error('Bulan anggaran diperlukan');
            return CashbookRepository.save('budgets', {
                ...data,
                limit_amount: Number(data.limit_amount),
                category_id: String(data.category_id)
            });
        },

        async deleteBudget(id) {
            if (!id) throw new Error('ID anggaran tidak valid');
            return CashbookRepository.softDelete('budgets', id);
        },

        // ── Accounts ──────────────────────────────────────────
        async saveAccount(data) {
            if (!data.name || !data.name.trim()) throw new Error('Nama akun diperlukan');
            return CashbookRepository.save('accounts', {
                ...data,
                name: data.name.trim(),
                initial_balance: Number(data.initial_balance || 0)
            });
        },

        async deleteAccount(id) {
            if (!id) throw new Error('ID akun tidak valid');
            return CashbookRepository.softDelete('accounts', id);
        },
    };

    // ── Unified Refresh Pipeline (stateless — always refresh all) ──
    async function refreshUI() {
        await loadMaster();
        loadDashboard();
        if (typeof loadAnggaran === 'function') await loadAnggaran();
        if (typeof loadDebts === 'function') await loadDebts();
        if (typeof applyTxFilter === 'function') applyTxFilter();
    }

    const rp = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n || 0);

    // ─── Theme ───
    function toggleTheme() {
        const cur = document.documentElement.getAttribute('data-theme');
        const next = cur === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('cb_theme', next);
        document.getElementById('theme-btn').className = `fas ${next === 'dark' ? 'fa-moon' : 'fa-sun'}`;
    }
    (function () {
        const t = localStorage.getItem('cb_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
        document.getElementById('theme-btn').className = `fas ${t === 'dark' ? 'fa-moon' : 'fa-sun'}`;
    })();

    // ─── Toast ───
    const TOAST_ICONS = { s: 'fa-circle-check', e: 'fa-circle-xmark', i: 'fa-circle-info', w: 'fa-triangle-exclamation' };
    let _toastTimer;
    function toast(msg, type = 's') {
        const el = document.getElementById('toast');
        const ico = TOAST_ICONS[type] || TOAST_ICONS.s;
        el.innerHTML = `<img src="/assets/icon/aksa_notif.png" class="toast-aksa" alt=""><i class="fas ${ico} toast-icon"></i><span class="toast-msg">${msg}</span>`;
        el.className = `toast show ${type}`;
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => { el.className = 'toast'; }, type === 'e' ? 4000 : 3200);
    }

    // --- FAB ---
    let fabOpen = false;
    function spawnCoins() {
        const wrap = document.getElementById('fab-wrap');
        const coins = ['💰','💵','💸','🪙','✨'];
        for (let i = 0; i < 7; i++) {
            const el = document.createElement('span');
            el.textContent = coins[Math.floor(Math.random() * coins.length)];
            el.style.cssText = `
                position:absolute;bottom:60px;left:${20 + Math.random()*80}px;
                font-size:${16+Math.floor(Math.random()*14)}px;
                animation:coinFloat ${0.6+Math.random()*0.6}s ease-out forwards;
                animation-delay:${Math.random()*0.25}s;
                pointer-events:none;z-index:9999;
            `;
            wrap.appendChild(el);
            setTimeout(() => el.remove(), 1200);
        }
    }
    function toggleFab() {
        fabOpen = !fabOpen;
        document.getElementById('fab-menu').classList.toggle('open', fabOpen);
        const img = document.getElementById('fab-icon');
        if (fabOpen) {
            img.style.transform = 'scale(1.1) rotate(-8deg)';
            setTimeout(() => { img.src = '/assets/icon/aksa_dompet2.png'; spawnCoins(); }, 80);
        } else {
            img.src = '/assets/icon/aksa_dompet1.png';
            img.style.transform = 'scale(1) rotate(0deg)';
        }
    }
    function closeFab() {
        fabOpen = false;
        document.getElementById('fab-menu').classList.remove('open');
        const img = document.getElementById('fab-icon');
        img.src = '/assets/icon/aksa_dompet1.png';
        img.style.transform = 'scale(1) rotate(0deg)';
    }

    // --- Nav Settings Dropdown ---
    function toggleNavSettings(e) {
        if (e) e.stopPropagation();
        const m = document.getElementById('nav-settings-menu');
        m.style.display = m.style.display === 'none' ? 'block' : 'none';
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('#nav-settings-wrap')) {
            const m = document.getElementById('nav-settings-menu');
            if (m) m.style.display = 'none';
        }
    });

    // ─── Modals ───
    function openModal(id) { document.getElementById(id).classList.add('open'); if (id === 'modal-transaction') prepTx(); if (id === 'modal-budget') prepBgt(); if (id === 'modal-account') loadAccModal(); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    function closeOut(el) { el.classList.remove('open'); }
    function openInfoModal(title, desc) {
        document.getElementById('info-modal-title').textContent = title;
        document.getElementById('info-modal-desc').textContent = desc;
        openModal('modal-info');
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.modal-bg.open').forEach(m => m.classList.remove('open')); });

    // ─── Custom Confirm Dialog ───
    function confirmDialog(title, message, type = 'danger', onConfirm) {
        const iconMap = {
            danger: { icon: 'fa-trash-can', color: 'var(--danger)', bg: 'rgba(239,68,68,0.12)', btnClass: 'btn-accent', btnStyle: 'background:var(--danger);', label: 'Ya, Hapus' },
            info:   { icon: 'fa-file-export', color: 'var(--info)', bg: 'rgba(59,130,246,0.12)', btnClass: 'btn-accent', btnStyle: 'background:var(--info);', label: 'Ya, Lanjutkan' },
            warning:{ icon: 'fa-triangle-exclamation', color: 'var(--warning)', bg: 'rgba(245,158,11,0.12)', btnClass: 'btn-accent', btnStyle: 'background:var(--warning);', label: 'Ya, Lanjutkan' },
        };
        const m = iconMap[type] || iconMap.danger;
        const wrap = document.getElementById('confirm-icon-wrap');
        const ico  = document.getElementById('confirm-icon');
        const okBtn = document.getElementById('confirm-ok');
        wrap.style.background = m.bg;
        ico.className = `fas ${m.icon}`;
        ico.style.color = m.color;
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        okBtn.className = `btn ${m.btnClass}`;
        okBtn.style.cssText = m.btnStyle;
        okBtn.textContent = m.label;
        // Wire up callback
        const newBtn = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newBtn, okBtn);
        newBtn.addEventListener('click', () => { closeModal('modal-confirm'); onConfirm(); });
        openModal('modal-confirm');
    }

    // ─── Insight banner collapse ───
    function collapseInsight() { document.getElementById('insight-banner').style.display = 'none'; }

    // ─── Chart tab switching (sidebar) ───
    function switchChart(pane, btn) {
        document.querySelectorAll('#pane-trend, #pane-pillar').forEach(p => p.style.display = 'none');
        document.getElementById('pane-' + pane).style.display = 'block';
        btn.closest('.chart-tabs').querySelectorAll('.ctab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    // ─── Main Tab Switching ───
    function switchMainTab(tabId, btn) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        const tabEl = document.getElementById('tab-' + tabId);
        if (tabEl) tabEl.classList.add('active');
        document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
        // btn is optional (may not exist when called programmatically)
        if (btn) btn.classList.add('active');
        else {
            const found = document.querySelector(`.nav-tab[onclick*="'${tabId}'"]`);
            if (found) found.classList.add('active');
        }

        if(tabId === 'transaksi') {
            if (typeof loadTransactionsTab === 'function') loadTransactionsTab();
        } else if(tabId === 'laporan') {
            if (typeof populateLapAccountFilter === 'function') populateLapAccountFilter();
            if (typeof generateReport === 'function') generateReport();
        } else if(tabId === 'anggaran') {
            if (typeof loadAnggaran === 'function') loadAnggaran();
        } else if(tabId === 'debts') {
            if (typeof loadDebts === 'function') loadDebts();
        }
    }

    // ─── Projection tab ───
    let projDatasets = [];
    let projChart = null;
    function switchProjTab(mode, btn) {
        btn.closest('.chart-tabs').querySelectorAll('.ctab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        if (!projChart) return;
        projChart.data.datasets.forEach((ds, i) => {
            if (mode === 'baseline') ds.hidden = i > 0;
            else ds.hidden = false;
        });
        projChart.update();
    }

    // â"€â"€â"€ Category icon map â"€â"€â"€
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

    // â"€â"€â"€ Custom Dropdown â"€â"€â"€
    function toggleCdd(id) {
        const menu = document.getElementById(id + '-menu');
        const trigger = document.querySelector(`#${id} .cdd-trigger`);
        const isOpen = menu.classList.contains('open');
        document.querySelectorAll('.cdd-menu.open').forEach(el => el.classList.remove('open'));
        document.querySelectorAll('.cdd-trigger.open').forEach(el => el.classList.remove('open'));
        if (!isOpen) { menu.classList.add('open'); trigger.classList.add('open'); }
    }
    document.addEventListener('click', e => { if (!e.target.closest('.cdd')) { document.querySelectorAll('.cdd-menu.open').forEach(el => el.classList.remove('open')); document.querySelectorAll('.cdd-trigger.open').forEach(el => el.classList.remove('open')); } });

    function buildCatDropdown(filteredCats, selectedId = null) {
        const menu = document.getElementById('cdd-category-menu'); menu.innerHTML = '';
        const none = document.createElement('div'); none.className = 'cdd-item' + (selectedId === '' ? ' selected' : '');
        none.innerHTML = `<div class="cdd-item-icon" style="background:rgba(148,163,184,.1)"><i class="fas fa-ban" style="color:#64748b"></i></div><span class="cdd-item-lbl">Tanpa kategori</span>`;
        none.onclick = () => { document.getElementById('tx-category-id').value = ''; setCddDisplay('cdd-cat-icon', 'cdd-cat-lbl', 'Tanpa kategori', 'fas fa-tag', '#94a3b8', true); closeCdd('cdd-category'); };
        menu.appendChild(none);
        const pillars = [...new Set(filteredCats.map(c => c.pillar))];
        const pillarLabel = { 'wajib': '<i class="fas fa-circle" style="color:var(--info);margin-right:4px;font-size:10px;"></i> Wajib', 'growth': '<i class="fas fa-circle" style="color:var(--accent);margin-right:4px;font-size:10px;"></i> Growth', 'lifestyle': '<i class="fas fa-circle" style="color:var(--warning);margin-right:4px;font-size:10px;"></i> Lifestyle', 'bocor': '<i class="fas fa-circle" style="color:var(--danger);margin-right:4px;font-size:10px;"></i> Bocor', 'income': '<i class="fas fa-circle" style="color:var(--accent2);margin-right:4px;font-size:10px;"></i> Pemasukan' };
        pillars.forEach(p => {
            const sec = document.createElement('div'); sec.className = 'cdd-section';
            sec.innerHTML = `<div class="cdd-section-label">${pillarLabel[p] || p}</div>`;
            filteredCats.filter(c => c.pillar === p).forEach(c => {
                const [ico, col] = CAT_ICONS[c.name] || DEFAULT_ICON;
                const item = document.createElement('div'); item.className = 'cdd-item' + (c.id == selectedId ? ' selected' : '');
                item.innerHTML = `<div class="cdd-item-icon" style="background:${col}20"><i class="fas ${ico}" style="color:${col}"></i></div><span class="cdd-item-lbl">${c.name}</span><span class="cdd-item-pill pill-${p}">${p}</span>`;
                item.onclick = () => { document.getElementById('tx-category-id').value = c.id; setCddDisplay('cdd-cat-icon', 'cdd-cat-lbl', c.name, ico, col, false); closeCdd('cdd-category'); };
                sec.appendChild(item);
            });
            menu.appendChild(sec);
        });
    }

    function buildBgtDropdown(cats, selectedId = null) {
        const menu = document.getElementById('cdd-budget-menu'); if (!menu) return; menu.innerHTML = '';
        const expCats = cats.filter(c => !INCOME_NAMES.includes(c.name));
        const pillars = [...new Set(expCats.map(c => c.pillar))];
        const pillarLabel = { 'wajib': '<i class="fas fa-circle" style="color:var(--info);margin-right:4px;font-size:10px;"></i> Wajib', 'growth': '<i class="fas fa-circle" style="color:var(--accent);margin-right:4px;font-size:10px;"></i> Growth', 'lifestyle': '<i class="fas fa-circle" style="color:var(--warning);margin-right:4px;font-size:10px;"></i> Lifestyle', 'bocor': '<i class="fas fa-circle" style="color:var(--danger);margin-right:4px;font-size:10px;"></i> Bocor' };
        pillars.forEach(p => {
            const sec = document.createElement('div'); sec.className = 'cdd-section';
            sec.innerHTML = `<div class="cdd-section-label">${pillarLabel[p] || p}</div>`;
            expCats.filter(c => c.pillar === p).forEach(c => {
                const [ico, col] = CAT_ICONS[c.name] || DEFAULT_ICON;
                const item = document.createElement('div'); item.className = 'cdd-item' + (c.id == selectedId ? ' selected' : '');
                item.innerHTML = `<div class="cdd-item-icon" style="background:${col}20"><i class="fas ${ico}" style="color:${col}"></i></div><span class="cdd-item-lbl">${c.name}</span><span class="cdd-item-pill pill-${p}">${p}</span>`;
                item.onclick = () => { document.getElementById('bgt-cat-val').value = c.id; setCddDisplay('cdd-bgt-icon', 'cdd-bgt-lbl', c.name, ico, col, false); closeCdd('cdd-budget'); };
                sec.appendChild(item);
            });
            menu.appendChild(sec);
        });
    }

    function setCddDisplay(iconId, lblId, text, ico, col, isPlaceholder) {
        document.getElementById(iconId).innerHTML = `<i class="fas ${ico}" style="color:${col}"></i>`;
        const lbl = document.getElementById(lblId); lbl.textContent = text;
        lbl.className = 'cdd-trigger-lbl' + (isPlaceholder ? ' placeholder' : '');
    }
    function closeCdd(id) { document.getElementById(id + '-menu').classList.remove('open'); document.querySelector(`#${id} .cdd-trigger`).classList.remove('open'); }

    // â"€â"€â"€ Tx type â"€â"€â"€
    let txType = 'income';
    function setType(t) {
        txType = t;
        ['income', 'expense', 'transfer'].forEach(x => { document.getElementById('tab-' + x).className = 'ttab'; });
        document.getElementById('tab-' + t).className = 'ttab ' + t[0];
        const isTrans = t === 'transfer';
        document.getElementById('tx-single-acc').style.display = isTrans ? 'none' : 'block';
        document.getElementById('tx-transfer-box').style.display = isTrans ? 'block' : 'none';
        document.getElementById('tx-cat-wrap').style.display = isTrans ? 'none' : 'block';
        if (!isTrans) {
            document.getElementById('cat-label').textContent = t === 'income' ? 'Kategori Pemasukan' : 'Kategori Pengeluaran';
            buildCatDropdown(t === 'income' ? categories.filter(c => INCOME_NAMES.includes(c.name)) : categories.filter(c => !INCOME_NAMES.includes(c.name)));
            document.getElementById('tx-category-id').value = '';
            setCddDisplay('cdd-cat-icon', 'cdd-cat-lbl', 'Tanpa kategori', 'fa-tag', '#94a3b8', true);
        }
    }

    // ─── Master data ───
    let accounts = [], categories = [];
    
    // Default Categories to Seed
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
        if (!db) await initDB();
        
        // Fetch from localDB
        accounts = await localAPI.accounts.getAll();
        const rawCats = await localAPI.categories.getAll();
        // Deduplicate by name (guard against double-seeding in previous sessions)
        const seenCatNames = new Set();
        categories = rawCats.filter(c => {
            if (seenCatNames.has(c.name)) return false;
            seenCatNames.add(c.name);
            return true;
        });
        
        // Seed default categories only if DB has never been seeded (check ALL incl. deleted)
        const allCatRaw = await localAPI._runOp('categories', 'readonly', s => s.getAll());
        if (allCatRaw.length === 0) {
            for (const cat of DEFAULT_CATEGORIES) {
                const saved = await localAPI.categories.save(cat);
                categories.push(saved);
            }
        }
        
        // Re-calculate local balances for accounts based on transactions
        const allTxs = await localAPI.transactions.getAll();
        accounts.forEach(acc => {
            acc.balance_cached = parseFloat(acc.initial_balance || 0);
            allTxs.forEach(tx => {
                if (tx.type === 'income' && tx.account_id === acc.id) acc.balance_cached += parseFloat(tx.amount);
                if (tx.type === 'expense' && tx.account_id === acc.id) acc.balance_cached -= parseFloat(tx.amount);
                if (tx.type === 'transfer' && tx.from_account_id === acc.id) acc.balance_cached -= parseFloat(tx.amount);
                if (tx.type === 'transfer' && tx.to_account_id === acc.id) acc.balance_cached += parseFloat(tx.amount);
            });
        });

        renderAccBar(); 
        fillSelects();
    }

    function renderAccBar() {
        const bar = document.getElementById('accounts-bar');
        const icons = { 'bank': 'fa-building-columns', 'cash': 'fa-money-bill', 'ewallet': 'fa-mobile-screen' };
        bar.innerHTML = `<div class="acc-add-chip" onclick="openModal('modal-add-account')"><i class="fas fa-plus" style="font-size:12px"></i> Tambah Akun</div>`;
        accounts.forEach(a => {
            const chip = document.createElement('div');
            chip.className = 'acc-chip';
            chip.style.cssText = 'position:relative;';
            const cleanName = a.name.replace(/'/g, "\\'");
            chip.innerHTML = `
      <div class="acc-chip-icon"><i class="fas ${icons[a.type] || 'fa-wallet'}"></i></div>
      <div style="flex:1"><div class="acc-chip-name">${a.name}</div><div class="acc-chip-type">${a.type}</div></div>
      <div class="acc-chip-bal">${rp(a.balance_cached)}</div>
      <div class="acc-chip-acts" style="display:none;position:absolute;top:6px;right:6px;display:none;gap:4px;align-items:center;">
        <button onclick="event.stopPropagation();editAccount('${a.id}')" style="background:var(--surface3);border:1px solid var(--border);color:var(--info);cursor:pointer;width:26px;height:26px;border-radius:7px;font-size:10px;" title="Edit"><i class="fas fa-pen"></i></button>
        <button onclick="event.stopPropagation();deleteAccount('${a.id}','${cleanName}')" style="background:var(--surface3);border:1px solid var(--border);color:var(--danger);cursor:pointer;width:26px;height:26px;border-radius:7px;font-size:10px;" title="Hapus"><i class="fas fa-trash"></i></button>
      </div>`;
            chip.addEventListener('mouseenter', () => { chip.querySelector('.acc-chip-acts').style.display = 'flex'; });
            chip.addEventListener('mouseleave', () => { chip.querySelector('.acc-chip-acts').style.display = 'none'; });
            bar.appendChild(chip);
        });
    }

    async function editAccount(id) {
        const acc = accounts.find(a => String(a.id) === String(id));
        if (!acc) return toast('Akun tidak ditemukan', 'e');
        openModal('modal-add-account');
        setTimeout(() => {
            document.getElementById('acc-name').value    = acc.name || '';
            document.getElementById('acc-type').value    = acc.type || 'bank';
            document.getElementById('acc-balance').value = acc.initial_balance || 0;
            document.getElementById('modal-add-account').dataset.editId = String(id);
        }, 80);
    }

    async function deleteAccount(id, name) {
        confirmDialog(
            'Hapus Akun',
            `Akun "${name}" akan dihapus. Transaksi yang sudah tercatat tetap ada. Lanjutkan?`,
            'danger',
            async () => {
                try {
                    await CashbookService.deleteAccount(String(id));
                    toast('Akun berhasil dihapus');
                    await refreshUI();
                } catch(e) { toast(e.message || 'Gagal menghapus akun', 'e'); }
            }
        );
    }


    function fillSelects() {
        const accOpts = accounts.map(a => `<option value="${a.id}">${a.name} (${rp(a.balance_cached)})</option>`).join('');
        ['tx-from-acc', 'tx-to-acc'].forEach(id => { document.getElementById(id).innerHTML = '<option value="">Pilih akun...</option>' + accOpts; });
        document.getElementById('tx-account-id').innerHTML = '<option value="">Pilih akun...</option>' + accOpts;
        buildCatDropdown(categories.filter(c => INCOME_NAMES.includes(c.name)));
        buildBgtDropdown(categories);
    }

    function prepTx() {
        document.getElementById('tx-date').value = new Date().toISOString().split('T')[0];
        document.getElementById('tx-category-id').value = '';
        setCddDisplay('cdd-cat-icon', 'cdd-cat-lbl', 'Tanpa kategori', 'fa-tag', '#94a3b8', true);
        setType('income');
        if (!accounts.length) loadMaster(); else fillSelects();
    }
    function prepBgt() {
        const n = new Date(); document.getElementById('bgt-month').value = `${n.getFullYear()}-${String(n.getMonth() + 1).padStart(2, '0')}`;
        if (!categories.length) loadMaster(); else buildBgtDropdown(categories);
    }

    // ─── Submit Transaction ───
    async function submitTransaction() {
        const btn = document.getElementById('btn-save-tx');
        const amount = parseFloat(document.getElementById('tx-amount').value);
        if (!amount || amount <= 0) { toast('Masukkan jumlah yang valid', 'e'); return; }
        const date = document.getElementById('tx-date').value; if (!date) { toast('Pilih tanggal', 'e'); return; }
        
        let body = { type: txType, amount, transaction_date: date, note: document.getElementById('tx-note').value };
        const isEdit = document.getElementById('modal-transaction').dataset.editId;
        if (isEdit) body.id = isEdit;

        if (txType === 'transfer') {
            body.from_account_id = document.getElementById('tx-from-acc').value;
            body.to_account_id = document.getElementById('tx-to-acc').value;
            if (!body.from_account_id || !body.to_account_id) { toast('Pilih akun asal dan tujuan', 'e'); return; }
        } else {
            body.account_id = document.getElementById('tx-account-id').value;
            if (!body.account_id) { toast('Pilih akun', 'e'); return; }
            const cat = document.getElementById('tx-category-id').value;
            if (cat && cat !== '') body.category_id = cat;
        }
        
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        try {
            if (body.id) {
                await CashbookService.editTransaction(body.id, body);
            } else {
                await CashbookService.addTransaction(body);
            }
            toast('Transaksi berhasil disimpan!'); 
            closeModal('modal-transaction');
            delete document.getElementById('modal-transaction').dataset.editId;
            document.getElementById('tx-amount').value = ''; 
            document.getElementById('tx-note').value = '';
            await refreshUI();
        } catch (e) { 
            toast(e.message || 'Gagal menyimpan transaksi', 'e'); 
        }
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Simpan';
    }

    // --- Submit Account (create + edit) ---
    async function submitAccount() {
        const name    = document.getElementById('acc-name').value.trim();
        const type    = document.getElementById('acc-type').value;
        const initial = parseFloat(document.getElementById('acc-initial')?.value || document.getElementById('acc-balance')?.value || 0) || 0;
        const modal   = document.getElementById('modal-add-account');
        const editId  = modal.dataset.editId;
        try {
            if (editId) {
                const existing = await CashbookRepository.getById('accounts', editId);
                await CashbookService.saveAccount({ ...(existing || {}), id: editId, name, type, initial_balance: initial });
                toast(`Akun "${name}" diperbarui!`);
                delete modal.dataset.editId;
            } else {
                await CashbookService.saveAccount({ name, type, initial_balance: initial });
                toast(`Akun "${name}" dibuat!`);
            }
            closeModal('modal-add-account');
            document.getElementById('acc-name').value = '';
            const balInput = document.getElementById('acc-initial') || document.getElementById('acc-balance');
            if (balInput) balInput.value = '';
            await refreshUI();
        } catch (e) { toast(e.message || 'Gagal menyimpan akun', 'e'); }
    }


    async function loadAccModal() {
        const el = document.getElementById('acc-modal-list'); 
        el.innerHTML = '<p style="color:var(--text-muted);font-size:13px">Memuat...</p>';
        const list = await localAPI.accounts.getAll();
        
        // recalculate balance for display
        const allTxs = await localAPI.transactions.getAll();
        list.forEach(acc => {
            acc.balance_cached = parseFloat(acc.initial_balance || 0);
            allTxs.forEach(tx => {
                if (tx.type === 'income' && tx.account_id === acc.id) acc.balance_cached += parseFloat(tx.amount);
                if (tx.type === 'expense' && tx.account_id === acc.id) acc.balance_cached -= parseFloat(tx.amount);
                if (tx.type === 'transfer' && tx.from_account_id === acc.id) acc.balance_cached -= parseFloat(tx.amount);
                if (tx.type === 'transfer' && tx.to_account_id === acc.id) acc.balance_cached += parseFloat(tx.amount);
            });
        });

        if (!list.length) { el.innerHTML = '<div class="empty"><i class="fas fa-wallet"></i>Belum ada akun</div>'; return; }
        el.innerHTML = '';
        list.forEach(a => { el.innerHTML += `<div class="acc-list-item"><div><div class="acc-list-name">${a.name}</div><div class="acc-list-type">${a.type}</div></div><div class="acc-list-bal">${rp(a.balance_cached)}</div></div>`; });
    }

    // ─── Submit Budget ───
    async function submitBudget() {
        const cat = document.getElementById('bgt-cat-val').value;
        const month = document.getElementById('bgt-month').value;
        const limit = parseFloat(document.getElementById('bgt-limit').value);
        try {
            await CashbookService.saveBudget({ category_id: cat, month, limit_amount: limit });
            toast('Budget disimpan!'); 
            closeModal('modal-budget');
            await refreshUI();
        } catch (e) { toast(e.message || 'Gagal menyimpan budget', 'e'); }
    }

    // ─── Load Transactions ───
    let allTxList = [];
    let txTabVisibleCount = 20;

    async function loadTransactions() {
        const rawTxs = await localAPI.transactions.getAll();
        
        // Populate category relation for UI compatibility with old structure
        allTxList = rawTxs.map(tx => {
            const cat = categories.find(c => c.id === tx.category_id) || null;
            return { ...tx, category: cat };
        });
        
        // Sort by date descending
        allTxList.sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date));
        
        // Render Overview Tab
        const tb = document.getElementById('tx-tbody');
        if (!allTxList.length) { tb.innerHTML = '<tr><td colspan="5"><div class="empty"><i class="fas fa-receipt"></i>Belum ada transaksi</div></td></tr>'; }
        else {
            tb.innerHTML = '';
            allTxList.slice(0, 15).forEach(tx => {
                const isInc = tx.type === 'income', isExp = tx.type === 'expense';
                const amtCls = isInc ? 'pos' : isExp ? 'neg' : 'neutral';
                const sign = isInc ? '+' : isExp ? '-' : '↔';
                const pillar = tx.category?.pillar || '';
                const catName = tx.category?.name || (tx.type === 'transfer' ? 'Transfer' : tx.type);
                tb.innerHTML += `<tr>
          <td style="color:var(--text-muted);font-family:'JetBrains Mono',monospace;font-size:12px;white-space:nowrap">${tx.transaction_date}</td>
          <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-sub)">${tx.note || '--'}</td>
          <td><span class="tag ${pillar || 'income'}">${catName}</span></td>
          <td><span class="tx-amount ${amtCls}">${sign}${rp(tx.amount)}</span></td>
          <td style="text-align:center">
            <button onclick="delTx('${tx.id}')" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:4px 6px;border-radius:6px;transition:.2s" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'" title="Hapus">
              <i class="fas fa-trash-alt" style="font-size:11px"></i>
            </button>
          </td>
        </tr>`;
            });
        }
        
        // Refresh Transaksi tab if active
        if(document.getElementById('tab-transaksi').classList.contains('active')) {
            renderTxTab();
        }
        if(document.getElementById('tab-laporan').classList.contains('active')) {
            generateReport();
        }
    }

    // --- TRANSAKSI TAB LOGIC ---
    function getDateRange(period) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        if (period === 'today')      return { start: today, end: new Date(today.getTime() + 86399999) };
        if (period === '7d')         return { start: new Date(today.getTime() - 6*86400000), end: new Date(today.getTime() + 86399999) };
        if (period === 'this_month') return { start: new Date(now.getFullYear(), now.getMonth(), 1), end: new Date(now.getFullYear(), now.getMonth()+1, 0, 23, 59, 59) };
        if (period === 'last_month') return { start: new Date(now.getFullYear(), now.getMonth()-1, 1), end: new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59) };
        if (period === 'this_year')  return { start: new Date(now.getFullYear(), 0, 1), end: new Date(now.getFullYear(), 11, 31, 23, 59, 59) };
        return null;
    }

    function renderTxTab() {
        const tbody = document.getElementById('tx-tab-tbody');
        if (!tbody) return;
        const q      = (document.getElementById('tx-search')?.value || '').toLowerCase();
        const t      = document.getElementById('tx-filter-type')?.value || 'all';
        const pillar = document.getElementById('tx-filter-pillar')?.value || 'all';
        const period = document.getElementById('tx-filter-period')?.value || 'all';
        const range  = getDateRange(period);

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
                const s = ((tx.note||'') + ' ' + (tx.category?.name||'') + ' ' + tx.amount).toLowerCase();
                if (!s.includes(q)) return false;
            }
            return true;
        });

        let sumInc = 0, sumExp = 0;
        filtered.forEach(tx => {
            if (tx.type === 'income')  sumInc += parseFloat(tx.amount || 0);
            if (tx.type === 'expense') sumExp += parseFloat(tx.amount || 0);
        });
        const net = sumInc - sumExp;
        const stripEl = document.getElementById('tx-summary-strip');
        if (stripEl) {
            stripEl.style.display = filtered.length ? 'grid' : 'none';
            document.getElementById('tx-sum-inc').textContent = rp(sumInc);
            document.getElementById('tx-sum-exp').textContent = rp(sumExp);
            const netEl = document.getElementById('tx-sum-net');
            netEl.textContent = rp(net);
            netEl.style.color = net >= 0 ? 'var(--accent)' : 'var(--danger)';
        }

        if (!filtered.length) {
            tbody.innerHTML = '<div class="empty" style="margin-top:24px"><i class="fas fa-folder-open" style="font-size:32px;opacity:0.2;display:block;margin-bottom:10px;"></i><div style="font-size:14px;font-weight:700">Tidak ada transaksi</div><div style="font-size:12px;opacity:0.7">Coba ubah filter pencarian</div></div>';
            document.getElementById('tx-tab-info').textContent = 'Menampilkan 0 transaksi';
            document.getElementById('btn-tx-loadmore').style.display = 'none';
            return;
        }

        tbody.innerHTML = '';
        const toShow = filtered.slice(0, txTabVisibleCount);
        let lastDateKey = null;

        toShow.forEach(tx => {
            const isInc = tx.type === 'income', isExp = tx.type === 'expense', isTransfer = tx.type === 'transfer';
            const sign      = isInc ? '+' : isExp ? '-' : String.fromCharCode(8644);
            const amtCls    = isInc ? 'pos' : isExp ? 'neg' : 'neutral';
            
            const catName   = tx.category?.name || (isTransfer ? 'Transfer' : tx.type);
            const accName   = (accounts || []).find(a => a.id === tx.account_id)?.name || (isTransfer ? 'Antar Akun' : '-');
            
            const iconBg = isInc ? 'rgba(16,185,129,.15)' : isTransfer ? 'rgba(59,130,246,.15)' : 'rgba(239,68,68,.15)';
            const iconColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';
            const icon = tx.category?.icon ? tx.category.icon : (isInc ? 'fa-arrow-down-left' : isTransfer ? 'fa-arrow-right-arrow-left' : 'fa-arrow-up-right');
            const amtColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';

            const dObj   = new Date(tx.transaction_date);
            const dKey   = tx.transaction_date.slice(0, 10);
            const dLabel = dObj.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'short' });
            
            if (dKey !== lastDateKey) {
                lastDateKey = dKey;
                let dayNet = 0;
                filtered.forEach(t => {
                    if (t.transaction_date.startsWith(dKey)) {
                        if (t.type === 'income') dayNet += parseFloat(t.amount||0);
                        if (t.type === 'expense') dayNet -= parseFloat(t.amount||0);
                    }
                });
                const netColor = dayNet > 0 ? 'var(--accent)' : dayNet < 0 ? 'var(--danger)' : 'var(--text-muted)';
                tbody.innerHTML += `
                    <div class="tx-day-header">
                        <span>${dLabel}</span>
                        <span class="tx-day-net" style="color:${netColor}">${dayNet >= 0 ? '+' : ''}${rp(dayNet)}</span>
                    </div>`;
            }

            tbody.innerHTML += `
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
        });

        document.getElementById('tx-tab-info').textContent = 'Menampilkan ' + toShow.length + ' dari ' + filtered.length + ' transaksi';
        document.getElementById('btn-tx-loadmore').style.display = txTabVisibleCount < filtered.length ? 'inline-block' : 'none';
    }

    function resetTxFilter() {
        document.getElementById('tx-search').value = '';
        document.getElementById('tx-filter-period').value = 'this_month';
        document.getElementById('tx-filter-type').value = 'all';
        document.getElementById('tx-filter-pillar').value = 'all';
        applyTxFilter();
    }

    function dupTx(id) {
        const tx = (allTxList || []).find(t => String(t.id) === String(id));
        if (!tx) return toast('Transaksi tidak ditemukan', 'e');
        openModal('modal-transaction');
        setTimeout(() => {
            setType(tx.type);
            document.getElementById('tx-amount').value = tx.amount;
            document.getElementById('tx-note').value   = (tx.note || '') + ' (Copy)';
            document.getElementById('tx-date').value   = new Date().toISOString().slice(0, 16);
            if (tx.category_id && document.getElementById('tx-category')) document.getElementById('tx-category').value = tx.category_id;
            if (tx.account_id && document.getElementById('tx-account')) document.getElementById('tx-account').value = tx.account_id;
            document.getElementById('modal-transaction').dataset.editId = ''; // Clear edit ID so it creates new
        }, 150);
    }

    function applyTxFilter() { txTabVisibleCount = 20; renderTxTab(); }
    function loadMoreTxTab() { txTabVisibleCount += 20; renderTxTab(); }
    function loadTransactionsTab() { renderTxTab(); }

    function editTx(id) {
        const tx = (allTxList || []).find(t => String(t.id) === String(id));
        if (!tx) return toast('Transaksi tidak ditemukan', 'e');
        openModal('modal-transaction');
        setTimeout(() => {
            setType(tx.type);
            document.getElementById('tx-amount').value = tx.amount;
            document.getElementById('tx-note').value   = tx.note || '';
            document.getElementById('tx-date').value   = tx.transaction_date;
            document.getElementById('tx-account').value = tx.account_id;
            if (tx.category_id) document.getElementById('tx-category').value = tx.category_id;
            document.getElementById('modal-transaction').dataset.editId = String(id);
        }, 80);
    }

    async function delTx(id) {
        confirmDialog(
            'Hapus Transaksi',
            'Transaksi ini akan dihapus dan tidak bisa dikembalikan. Lanjutkan?',
            'danger',
            async () => {
                try {
                    await CashbookService.deleteTransaction(String(id));
                    toast('Transaksi dihapus');
                    await refreshUI();
                } catch (e) {
                    toast(e.message || 'Gagal menghapus', 'e');
                }
            }
        );
    }

    // Add missing localAPI.meta methods
    if (typeof localAPI !== 'undefined' && !localAPI.meta) {
        localAPI.meta = {
            _data: {}, // Simple in-memory store for demonstration
            async getAll() {
                return Object.values(this._data);
            },
            async set(key, value) {
                this._data[key] = value;
                return value;
            }
        };
    }

    // ─── Dashboard ───
    let trendChart = null, pillarChart = null;
    async function loadDashboard() {
        try {
            const now = new Date();
            const yr = now.getFullYear(), mo = now.getMonth() + 1;

            // ── Layer 1: Hero Metrics ──
            const totalBalance = (accounts || []).reduce((s, a) => s + parseFloat(a.balance_cached || 0), 0);
            document.getElementById('ov-total-balance').textContent = rp(totalBalance);
            document.getElementById('ov-balance-sub').textContent = `${(accounts||[]).length} akun aktif`;

            let incThisMonth = 0, expThisMonth = 0;
            let todayIn = 0, todayOut = 0;
            const todayStr = new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
            const catSpend = {}; // category_id → amount
            const monthlyByDay = {}; // 'YYYY-MM-DD' → {inc, exp}

            allTxList.forEach(tx => {
                const txD = new Date(tx.transaction_date);
                const txY = txD.getFullYear(), txM = txD.getMonth() + 1;
                const dKey = tx.transaction_date?.slice(0, 10);
                if (dKey) {
                    if (!monthlyByDay[dKey]) monthlyByDay[dKey] = { inc: 0, exp: 0 };
                    if (tx.type === 'income') {
                        monthlyByDay[dKey].inc += parseFloat(tx.amount || 0);
                        if (dKey === todayStr) todayIn += parseFloat(tx.amount || 0);
                    }
                    if (tx.type === 'expense') {
                        monthlyByDay[dKey].exp += parseFloat(tx.amount || 0);
                        if (dKey === todayStr) todayOut += parseFloat(tx.amount || 0);
                    }
                }
                if (txY === yr && txM === mo) {
                    const amt = parseFloat(tx.amount || 0);
                    if (tx.type === 'income')  incThisMonth += amt;
                    if (tx.type === 'expense') {
                        expThisMonth += amt;
                        const cid = tx.category_id || 'other';
                        catSpend[cid] = (catSpend[cid] || 0) + amt;
                    }
                }
            });

            const nc = incThisMonth - expThisMonth;
            const savingRate = incThisMonth > 0 ? Math.max(0, (nc / incThisMonth) * 100) : 0;
            const runway = expThisMonth > 0 ? Math.floor(totalBalance / expThisMonth) : (totalBalance > 0 ? 99 : 0);

            document.getElementById('ov-income').textContent = rp(incThisMonth);
            document.getElementById('ov-expense').textContent = rp(expThisMonth);
            const srEl = document.getElementById('ov-saving-rate');
            srEl.textContent = `${savingRate.toFixed(1)}%`;
            srEl.className = 'ov-hmetric-val' + (savingRate >= 20 ? ' pos' : savingRate >= 10 ? '' : ' neg');
            document.getElementById('ov-runway').textContent = `${runway > 99 ? '99+' : runway} bln`;

            // saving rate progress bar
            const srPct = Math.min(100, savingRate / 20 * 100);
            document.getElementById('ov-sr-bar').style.width = srPct + '%';
            document.getElementById('ov-sr-bar').style.background = savingRate >= 20 ? 'var(--accent)' : savingRate >= 10 ? 'var(--warning)' : 'var(--danger)';
            document.getElementById('ov-sr-pct-label').textContent = `${savingRate.toFixed(1)}% (Target 20%)`;

            // Today Check
            document.getElementById('ov-today-in').textContent = rp(todayIn);
            document.getElementById('ov-today-out').textContent = rp(todayOut);

            // Health badge gamification UI
            let hs = 50;
            if (savingRate >= 20) hs += 20; else if (savingRate > 0) hs += 10;
            if (runway >= 6) hs += 20; else if (runway >= 3) hs += 10;
            if (nc > 0) hs += 10;
            hs = Math.min(100, Math.max(0, hs));
            const healthColor = hs >= 80 ? 'var(--accent)' : hs >= 60 ? 'var(--info)' : hs >= 40 ? 'var(--warning)' : 'var(--danger)';
            const badge = document.getElementById('ov-health-badge');
            
            badge.style.background = `linear-gradient(135deg, ${healthColor.replace('var(', 'rgba(').replace(')', ', .15)')} 0%, ${healthColor.replace('var(', 'rgba(').replace(')', ', .02)')} 100%)`;
            badge.style.borderColor = healthColor.replace('var(', 'rgba(').replace(')', ', .3)');
            
            badge.firstElementChild.style.color = healthColor;
            const scoreEl = document.getElementById('ov-health-score');
            scoreEl.textContent = hs;
            scoreEl.style.color = healthColor;
            scoreEl.nextElementSibling.style.color = healthColor.replace('var(', 'rgba(').replace(')', ', .6)');

            // ── Layer 2: 30-day cashflow area chart ──
            const days30 = [];
            for (let i = 29; i >= 0; i--) {
                const d = new Date(now); d.setDate(d.getDate() - i);
                days30.push(d.toISOString().slice(0, 10));
            }
            const incData = days30.map(d => (monthlyByDay[d]?.inc || 0));
            const expData = days30.map(d => (monthlyByDay[d]?.exp || 0));
            const hasAnyData = incData.some(v => v > 0) || expData.some(v => v > 0);
            document.getElementById('ov-chart-empty').style.display = hasAnyData ? 'none' : 'flex';

            const ctxOv = document.getElementById('ov-cashflow-chart').getContext('2d');
            if (window._ovCashflowChart) window._ovCashflowChart.destroy();
            if (hasAnyData) {
                const gInc = ctxOv.createLinearGradient(0, 0, 0, 160);
                gInc.addColorStop(0, 'rgba(16,185,129,.25)'); gInc.addColorStop(1, 'rgba(16,185,129,0)');
                const gExp = ctxOv.createLinearGradient(0, 0, 0, 160);
                gExp.addColorStop(0, 'rgba(239,68,68,.18)'); gExp.addColorStop(1, 'rgba(239,68,68,0)');
                window._ovCashflowChart = new Chart(ctxOv, {
                    type: 'line',
                    data: {
                        labels: days30.map((d, i) => i % 7 === 0 ? new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'short' }) : ''),
                        datasets: [
                            { label: 'Pemasukan', data: incData, borderColor: '#10b981', backgroundColor: gInc, fill: true, tension: .35, pointRadius: 0, borderWidth: 2 },
                            { label: 'Pengeluaran', data: expData, borderColor: '#f59e0b', backgroundColor: gExp, fill: true, tension: .35, pointRadius: 0, borderWidth: 2 }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: true, position: 'top', labels: { color: '#64748b', usePointStyle: true, pointStyleWidth: 12, font: { size: 11 } } }, tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(15,23,42,.95)', callbacks: { label: c => ' ' + c.dataset.label + ': ' + rp(c.raw) } } },
                        scales: { y: { grid: { color: 'rgba(100,116,139,.06)' }, ticks: { color: '#64748b', font: { size: 10 }, callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v }, beginAtZero: true }, x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } } }
                    }
                });
                document.getElementById('ov-trend-net').textContent = `Net: ${nc >= 0 ? '+' : ''}${rp(nc)}`;
            }

            // ── Layer 3: Spending Distribution ──
            const distEl = document.getElementById('ov-spending-dist');
            const colors = ['#10b981','#6366f1','#f59e0b','#ef4444','#8b5cf6'];
            const catEntries = Object.entries(catSpend).sort((a, b) => b[1] - a[1]).slice(0, 5);
            if (catEntries.length === 0) {
                distEl.innerHTML = '<div class="empty"><i class="fas fa-chart-pie"></i>Belum ada pengeluaran bulan ini</div>';
            } else {
                const maxAmt = catEntries[0][1];
                distEl.innerHTML = catEntries.map(([cid, amt], i) => {
                    const cat = categories.find(c => c.id == cid) || {};
                    const pct = maxAmt > 0 ? (amt / maxAmt * 100) : 0;
                    return `<div class="ov-spend-row">
                        <span class="ov-spend-label">${cat.name || 'Lainnya'}</span>
                        <div class="ov-spend-bar-wrap"><div class="ov-spend-bar" style="width:${pct}%;background:${colors[i]};"></div></div>
                        <span class="ov-spend-amt">${rp(amt)}</span>
                    </div>`;
                }).join('');
            }

            // ── Layer 4: Smart Insights ──
            const insEl = document.getElementById('ov-insights');
            let insights = [];

            // 1. 7-Day Trailing Habit Loop
            let weekInc = 0, weekExp = 0;
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            const wStr = weekAgo.toISOString().slice(0, 10);
            for (let dKey in monthlyByDay) {
                if (dKey >= wStr && dKey <= todayStr) {
                    weekInc += monthlyByDay[dKey].inc;
                    weekExp += monthlyByDay[dKey].exp;
                }
            }
            const weekNet = weekInc - weekExp;

            if (weekNet > 0 && weekInc > 0) {
                insights.push({ cls: 'good', icon: 'fa-check-circle', text: `💡 Hebat! Kamu berhasil menghemat <strong>${rp(weekNet)}</strong> dalam 7 hari terakhir. Pertahankan hijau ini!` });
            } else if (weekExp > 0 && weekInc > 0 && weekExp > weekInc) {
                insights.push({ cls: 'warn', icon: 'fa-lightbulb', text: `💡 Pengeluaran 7 hari terakhir sedikit lebih tinggi dari pemasukan. Yuk coba rem sedikit pengeluaran besok.` });
            } else if (todayOut === 0 && todayIn === 0) {
                insights.push({ cls: 'info', icon: 'fa-sparkles', text: `💡 Belum ada transaksi hari ini. Catat satu pengeluaran atau pemasukan untuk update skormu!` });
            }

            // 2. Health & Saving Rate
            if (savingRate >= 20) {
                insights.push({ cls: 'good', icon: 'fa-arrow-trend-up', text: `📊 Saving rate kamu <strong>${savingRate.toFixed(1)}%</strong>! Kamu sudah masuk kategori finansial yang sangat sehat bulan ini.` });
            } else if (savingRate > 0 && savingRate < 20) {
                insights.push({ cls: 'warn', icon: 'fa-bullseye', text: `📊 Saving rate saat ini <strong>${savingRate.toFixed(1)}%</strong>. Sedikit lagi menuju target ideal 20%.` });
            } else if (nc < 0 && savingRate === 0) {
                insights.push({ cls: 'warn', icon: 'fa-scale-unbalanced', text: `⚠ Pemasukan dan pengeluaran bulan ini sedang minus. Coba cek kategori mana yang bisa ditekan.` });
            }

            // 3. Urgent Debts Check
            try {
                const debts = await localAPI.debts.getAll();
                let uDebts = 0;
                debts.forEach(d => {
                    if (d.due_date && d.status !== 'paid') {
                        const diff = (new Date(d.due_date) - now) / 86400000;
                        if (diff >= 0 && diff <= 7) uDebts++;
                    }
                });
                if (uDebts > 0) {
                    insights.unshift({ cls: 'warn', icon: 'fa-calendar-exclamation', text: `⏰ Ada <strong>${uDebts} utang/cicilan</strong> yang jatuh tempo dalam minggu ini. Jangan lupa cek tab Utang ya.` });
                }
            } catch (e) {
                // debt fetch error, neglect
            }

            // Fallback
            if (insights.length === 0) {
                insights.push({ cls: 'info', icon: 'fa-chart-pie', text: `💡 Insight akan muncul setelah kamu mencatat lebih banyak transaksi di bulan ini.` });
            }

            insEl.innerHTML = insights.slice(0, 3).map(ins =>
                `<div class="ov-insight-card ${ins.cls}"><i class="fas ${ins.icon} ov-ic-icon"></i><span>${ins.text}</span></div>`
            ).join('');

            // ── Recent Transactions (last 5) ──
            const rcEl = document.getElementById('ov-recent-tx');
            const recent = [...allTxList].sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date)).slice(0, 5);
            if (!recent.length) {
                rcEl.innerHTML = '<div class="empty"><i class="fas fa-receipt"></i>Belum ada transaksi</div>';
            } else {
                rcEl.innerHTML = `<div style="padding:0 16px;">` + recent.map(tx => {
                    const amt = parseFloat(tx.amount || 0);
                    const isInc = tx.type === 'income';
                    const isTransfer = tx.type === 'transfer';
                    const iconBg = isInc ? 'rgba(16,185,129,.15)' : isTransfer ? 'rgba(59,130,246,.15)' : 'rgba(239,68,68,.15)';
                    const iconColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';
                    const icon = isInc ? 'fa-arrow-down-left' : isTransfer ? 'fa-arrow-right-arrow-left' : 'fa-arrow-up-right';
                    const amtColor = isInc ? 'var(--accent)' : isTransfer ? 'var(--info)' : 'var(--danger)';
                    const cat = categories.find(c => c.id == tx.category_id);
                    const dateStr = new Date(tx.transaction_date).toLocaleDateString('id-ID', { day:'2-digit', month:'short' });
                    return `<div class="ov-tx-row">
                        <div class="ov-tx-icon" style="background:${iconBg};color:${iconColor};"><i class="fas ${icon}"></i></div>
                        <div class="ov-tx-info">
                            <div class="ov-tx-name">${tx.notes || tx.description || 'Transaksi'}</div>
                            <div class="ov-tx-cat">${cat?.name || tx.type}</div>
                        </div>
                        <span class="ov-tx-date">${dateStr}</span>
                        <span class="ov-tx-amt" style="color:${amtColor}">${isInc ? '+' : '−'}${rp(amt)}</span>
                    </div>`;
                }).join('') + `</div>`;
            }

        } catch (e) { console.error('loadDashboard error', e); }
    }

    function saveReflection() {
        toast('Catatan disimpan!');
    }

    // === LAPORAN TAB LOGIC ===
    let lapCashflowChart = null, lapPillarChart = null, lapNetWorthChart = null;
    let lapCurrentData = { inc: 0, exp: 0, net: 0, filteredTx: [] };
    let lapTxVisibleCount = 50;
    let lapCatData = [];   // { name, amount, pct, pillar }
    let lapChartType = 'bar';

    // Feature 9: populate account filter
    function populateLapAccountFilter() {
        const sel = document.getElementById('lap-filter-account');
        if (!sel) return;
        const current = sel.value;
        sel.innerHTML = '<option value="all">Semua Akun</option>';
        (accounts || []).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id; opt.textContent = a.name;
            sel.appendChild(opt);
        });
        sel.value = current || 'all';
    }

    // Feature 1: custom date range toggle
    function onLapPeriodChange() {
        const val = document.getElementById('lap-filter-period').value;
        const customDiv = document.getElementById('lap-custom-range');
        if (customDiv) customDiv.classList.toggle('visible', val === 'custom');
        if (val !== 'custom') generateReport();
    }

    function getLapDateRange() {
        const period = document.getElementById('lap-filter-period').value;
        const now = new Date();
        if (period === 'custom') {
            const from = document.getElementById('lap-date-from').value;
            const to   = document.getElementById('lap-date-to').value;
            if (!from || !to) return null;
            return { start: new Date(from), end: new Date(to + 'T23:59:59') };
        }
        if (period === 'this_month')  return { start: new Date(now.getFullYear(), now.getMonth(), 1), end: new Date(now.getFullYear(), now.getMonth()+1, 0, 23, 59, 59) };
        if (period === 'last_month') { const s = new Date(now.getFullYear(), now.getMonth()-1, 1); return { start: s, end: new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59) }; }
        if (period === 'this_year')   return { start: new Date(now.getFullYear(), 0, 1), end: new Date(now.getFullYear(), 11, 31, 23, 59, 59) };
        return null; // all_time
    }

    // Feature 6: compute MoM delta
    function computeMoM(current, period) {
        if (period === 'custom' || period === 'all_time' || period === 'this_year') return null;
        const now = new Date();
        let prevStart, prevEnd;
        if (period === 'this_month') {
            prevStart = new Date(now.getFullYear(), now.getMonth()-1, 1);
            prevEnd   = new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59);
        } else if (period === 'last_month') {
            prevStart = new Date(now.getFullYear(), now.getMonth()-2, 1);
            prevEnd   = new Date(now.getFullYear(), now.getMonth()-1, 0, 23, 59, 59);
        } else return null;
        const prevTx = (allTxList || []).filter(tx => {
            const d = new Date(tx.transaction_date);
            return d >= prevStart && d <= prevEnd && tx.type !== 'transfer';
        });
        let prevInc = 0, prevExp = 0;
        prevTx.forEach(tx => {
            if (tx.type === 'income')  prevInc += parseFloat(tx.amount || 0);
            if (tx.type === 'expense') prevExp += parseFloat(tx.amount || 0);
        });
        return { prevInc, prevExp, prevNet: prevInc - prevExp };
    }

    function momBadgeHtml(current, prev) {
        if (prev === null || prev === undefined || isNaN(prev) || prev === 0) return '<span class="mom-badge mom-flat">&mdash; -</span>';
        const pct = ((current - prev) / Math.abs(prev)) * 100;
        const sign = pct >= 0 ? '+' : '';
        const cls  = pct > 0 ? 'mom-up' : pct < 0 ? 'mom-down' : 'mom-flat';
        const icon = pct > 0 ? 'fa-arrow-up' : pct < 0 ? 'fa-arrow-down' : 'fa-minus';
        return `<span class="mom-badge ${cls}"><i class="fas ${icon}" style="font-size:8px;"></i>${sign}${pct.toFixed(1)}% vs lalu</span>`;
    }

    // Feature 10: Net Worth Chart
    function renderNetWorthChart(txList) {
        const ctx = document.getElementById('lapNetWorthChart');
        if (!ctx) return;
        const now = new Date();
        // Build monthly net worth: use accounts total and subtract month-by-month
        const totalBalance = (accounts || []).reduce((s, a) => s + parseFloat(a.balance_cached || 0), 0);
        document.getElementById('lap-nw-current').textContent = rp(totalBalance);
        // Build 12-month back-series
        const months = [];
        for (let m = 11; m >= 0; m--) {
            const d = new Date(now.getFullYear(), now.getMonth() - m, 1);
            months.push({ yr: d.getFullYear(), mo: d.getMonth()+1, label: d.toLocaleString('id-ID', { month: 'short' }) + ' ' + d.getFullYear() });
        }
        // Cumulative net cashflow per month (simplified: balance = current - future net flows)
        let running = totalBalance;
        const futureNet = [];
        for (let m = 11; m >= 0; m--) {
            const { yr, mo } = months[11 - m] || {};
            if (!yr) continue;
            const mTx = txList.filter(tx => {
                const d = new Date(tx.transaction_date);
                return d.getFullYear() === yr && (d.getMonth()+1) === mo && tx.type !== 'transfer';
            });
            let net = 0;
            mTx.forEach(tx => { net += tx.type === 'income' ? parseFloat(tx.amount||0) : -parseFloat(tx.amount||0); });
            futureNet.push(net);
        }
        // Reconstruct balance going backwards
        const balances = [];
        let bal = totalBalance;
        for (let i = 11; i >= 0; i--) {
            balances.unshift(bal);
            bal -= futureNet[i] || 0;
        }
        const allPos = balances.every(b => b >= 0);
        const lineColor = allPos ? '#10b981' : '#ef4444';
        const gradColor = allPos ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.1)';

        if (lapNetWorthChart) lapNetWorthChart.destroy();
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 160);
        gradient.addColorStop(0, gradColor);
        gradient.addColorStop(1, 'rgba(0,0,0,0)');
        lapNetWorthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months.map(m => m.label),
                datasets: [{
                    label: 'Kekayaan Bersih', data: balances,
                    borderColor: lineColor, backgroundColor: gradient,
                    fill: true, tension: .4, pointRadius: 3, pointBackgroundColor: lineColor, borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,.95)', callbacks: { label: c => ' ' + rp(c.raw) } } }, scales: { y: { grid: { color: 'rgba(100,116,139,.07)' }, ticks: { color: '#64748b', callback: v => rp(v), maxTicksLimit: 5 } }, x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 }, maxTicksLimit: 6 } } } }
        });
    }

    // Feature 5: income sources breakdown
    function renderIncomeSourcesPanel(filteredTx) {
        const el = document.getElementById('lap-income-sources');
        if (!el) return;
        const srcMap = {};
        filteredTx.filter(tx => tx.type === 'income').forEach(tx => {
            const cat = tx.category?.name || 'Lainnya';
            srcMap[cat] = (srcMap[cat] || 0) + parseFloat(tx.amount || 0);
        });
        const total = Object.values(srcMap).reduce((a,b)=>a+b,0);
        const sorted = Object.entries(srcMap).sort((a,b)=>b[1]-a[1]);
        if (!sorted.length) {
            el.innerHTML = '<div class="empty" style="padding:20px 0;"><i class="fas fa-money-bill-wave"></i>Belum ada pemasukan</div>';
            return;
        }
        el.innerHTML = sorted.map(([cat, amt]) => {
            const pct = total > 0 ? (amt/total*100) : 0;
            const [ico, col] = CAT_ICONS[cat] || DEFAULT_ICON;
            return `<div class="income-src-item">
                <div style="width:28px;height:28px;border-radius:8px;background:${col}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas ${ico}" style="color:${col};font-size:11px;"></i></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${cat}</div>
                    <div class="income-src-bar"><div class="income-src-fill" style="width:${pct.toFixed(1)}%;background:${col};"></div></div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:12px;font-weight:700;color:var(--accent);">${rp(amt)}</div>
                    <div style="font-size:10px;color:var(--text-muted);">${pct.toFixed(1)}%</div>
                </div>
            </div>`;
        }).join('');
    }

    // Switch expense / income distribution pane
    function switchDistribusi(pane, btn) {
        document.getElementById('pane-dist-expense').style.display = pane === 'expense' ? 'block' : 'none';
        document.getElementById('pane-dist-income').style.display  = pane === 'income'? 'block' : 'none';
        btn.closest('.chart-tabs').querySelectorAll('.ctab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    // Feature 7: render all categories table
    function renderLapCatTable() {
        const wrap = document.getElementById('lap-cat-wrap');
        if (!wrap || !lapCatData.length) {
            if (wrap) wrap.innerHTML = '<div class="empty"><i class="fas fa-receipt"></i>Belum ada data pengeluaran</div>';
            return;
        }
        const sortBy = document.getElementById('lap-cat-sort')?.value || 'amount';
        const sorted = [...lapCatData].sort((a,b) => {
            if (sortBy === 'name')   return a.name.localeCompare(b.name);
            if (sortBy === 'pct')    return b.pct - a.pct;
            return b.amount - a.amount;
        });
        const totalExp = sorted.reduce((s,c)=>s+c.amount,0);
        wrap.innerHTML = `<table class="cat-table">
            <thead><tr><th>Kategori</th><th style="text-align:right;">Jumlah</th><th style="text-align:right;">% Total</th><th>Pilar</th></tr></thead>
            <tbody>${sorted.map(c => {
                const [ico, col] = CAT_ICONS[c.name] || DEFAULT_ICON;
                return `<tr>
                    <td><div style="display:flex;align-items:center;gap:7px;">
                        <div style="width:22px;height:22px;border-radius:6px;background:${col}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas ${ico}" style="color:${col};font-size:10px;"></i></div>
                        <span>${c.name}</span>
                    </div>
                    <div class="cat-bar-mini"><div class="cat-bar-fill" style="width:${c.pct.toFixed(1)}%;background:${col};"></div></div></td>
                    <td style="text-align:right;font-weight:700;color:var(--danger);font-family:'JetBrains Mono',monospace;font-size:12px;">${rp(c.amount)}</td>
                    <td style="text-align:right;font-size:12px;color:var(--text-muted);">${c.pct.toFixed(1)}%</td>
                    <td><span class="tag ${c.pillar || 'bocor'}" style="padding:2px 8px;">${c.pillar || '-'}</span></td>
                </tr>`;
            }).join('')}</tbody>
        </table>`;
    }

    // Feature 8: Budget vs Aktual
    function renderBudgetVsAktual(filteredTx, budgetMap) {
        const el = document.getElementById('lap-bva-body');
        if (!el) return;
        if (!budgetMap) { el.innerHTML = '<div class="empty"><i class="fas fa-sliders"></i>Set budget di tab Anggaran terlebih dahulu</div>'; return; }

        // Build expense map: catId → { name, amt, pillar }
        const catExp = {};
        filteredTx.filter(tx => tx.type === 'expense').forEach(tx => {
            const cid = String(tx.category?.id || 'x');
            if (!catExp[cid]) catExp[cid] = { name: tx.category?.name || 'Lainnya', amt: 0, pillar: tx.category?.pillar || '' };
            catExp[cid].amt += parseFloat(tx.amount || 0);
        });

        // Merge: show all categories that have budget OR expenses
        const allCatIds = new Set([...Object.keys(catExp), ...Object.keys(budgetMap)]);
        const rows = [...allCatIds].map(cid => {
            const exp  = catExp[cid]  || { name: '--', amt: 0, pillar: '' };
            const bgt  = budgetMap[cid] || 0;
            // Try to get name from categories array if not in expense
            if (exp.name === '--') {
                const cat = (categories || []).find(c => String(c.id) === cid);
                if (cat) exp.name = cat.name;
            }
            const diff = bgt - exp.amt;
            const pct  = bgt > 0 ? (exp.amt / bgt * 100) : (exp.amt > 0 ? 100 : 0);
            const status = bgt === 0
                ? (exp.amt > 0 ? '<span class="bva-status over"><i class="fas fa-exclamation-circle" style="font-size:9px;"></i>No Budget</span>' : '')
                : pct >= 100
                    ? '<span class="bva-status over"><i class="fas fa-fire" style="font-size:9px;"></i>OVER</span>'
                    : pct > 80
                        ? '<span class="bva-status warn"><i class="fas fa-triangle-exclamation" style="font-size:9px;"></i>WASPADA</span>'
                        : '<span class="bva-status ok"><i class="fas fa-check" style="font-size:9px;"></i>OK</span>';
            return `<tr>
                <td style="font-weight:600;font-size:12px;">${exp.name}</td>
                <td style="text-align:right;font-family:'JetBrains Mono',monospace;color:var(--danger);font-size:12px;">${exp.amt > 0 ? rp(exp.amt) : '<span style="color:var(--text-muted)">Rp 0</span>'}</td>
                <td style="text-align:right;font-family:'JetBrains Mono',monospace;color:var(--text-muted);font-size:12px;">${bgt > 0 ? rp(bgt) : '&mdash;'}</td>
                <td style="text-align:right;font-family:'JetBrains Mono',monospace;font-size:12px;${diff >= 0 ? 'color:var(--accent)' : 'color:var(--danger)'};">${bgt > 0 ? (diff >= 0 ? '+' : '') + rp(diff) : '&mdash;'}</td>
                <td>${status}</td>
            </tr>`;
        }).join('');

        el.innerHTML = rows.length
            ? `<table class="bva-table"><thead><tr><th>Kategori</th><th style="text-align:right;">Realisasi</th><th style="text-align:right;">Budget</th><th style="text-align:right;">Selisih</th><th>Status</th></tr></thead><tbody>${rows}</tbody></table>`
            : '<div class="empty"><i class="fas fa-check-circle" style="color:var(--accent);"></i>Belum ada pengeluaran periode ini</div>';
    }

    // Feature 2: transaction table
    let lapTxDataBuffer = [];
    // ── Detail Table Rendering ──
    function renderLapTxTable(forceData = null) {
        if (forceData) lapTxDataBuffer = forceData;
        const tbody = document.getElementById('lap-det-tbody');
        if (!tbody) return;

        let dataToRender = lapTxDataBuffer || [];
        const searchQ = document.getElementById('lap-tx-search')?.value.toLowerCase() || '';
        
        if (searchQ) {
            dataToRender = dataToRender.filter(tx => {
                const note = (tx.note || '').toLowerCase();
                const cat = (tx.category?.name || '').toLowerCase();
                return note.includes(searchQ) || cat.includes(searchQ);
            });
        }

        const countEl = document.getElementById('lap-det-info');
        if (countEl) countEl.textContent = `${dataToRender.length} transaksi`;

        const toShow = dataToRender.slice(0, lapTxVisibleCount);
        if (!toShow.length) { 
            tbody.innerHTML = '<tr><td colspan="5"><div class="empty" style="padding:30px 0;"><i class="fas fa-search"></i>Tidak ada transaksi ditemukan</div></td></tr>'; 
            document.getElementById('lap-det-more').style.display = 'none';
            return; 
        }

        tbody.innerHTML = toShow.map(tx => {
            const isInc = tx.type === 'income';
            const amt = parseFloat(tx.amount || 0);
            
            const incCol = isInc ? `<span style="color:#22c55e;font-family:'JetBrains Mono',monospace;font-weight:700;">+${rp(amt)}</span>` : '-';
            const expCol = !isInc ? `<span style="color:#ef4444;font-family:'JetBrains Mono',monospace;font-weight:700;">-${rp(amt)}</span>` : '-';
            
            const dp = tx.transaction_date.split('T')[0].split('-');
            const dateStr = `${dp[2]}/${dp[1]}/${dp[0].slice(2)}`;
            
            const catName = tx.category?.name || (tx.type === 'transfer' ? 'Transfer' : 'Lainnya');
            const note = tx.note || '<em style="color:var(--text-muted);font-size:11px;">Tanpa catatan</em>';

            return `<tr>
                <td style="color:var(--text-muted);font-family:'JetBrains Mono',monospace;">${dateStr}</td>
                <td><span class="tag" style="background:var(--surface2);color:var(--text);padding:3px 8px;">${catName}</span></td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${note}</td>
                <td style="text-align:right;">${incCol}</td>
                <td style="text-align:right;">${expCol}</td>
            </tr>`;
        }).join('');

        const moreBtn = document.getElementById('lap-det-more');
        if (moreBtn) moreBtn.style.display = lapTxVisibleCount < dataToRender.length ? 'inline-block' : 'none';
    }

    // Export CSV hook
    function exportCSV() {
        const data = lapTxDataBuffer || [];
        if (!data.length) return toast('Tidak ada data untuk diekspor', 'e');
        
        const rows = [['Tanggal', 'Kategori', 'Deskripsi', 'Tipe', 'Jumlah']];
        data.forEach(tx => {
            rows.push([
                tx.transaction_date.split('T')[0],
                (tx.category?.name || '').replace(/,/g, ''),
                (tx.note || '').replace(/,/g, ' '),
                tx.type,
                tx.amount
            ]);
        });
        
        const csv = rows.map(r => r.join(',')).join('\n');
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `CuanCapital_Laporan_${new Date().toISOString().slice(0,10)}.csv`;
        a.click();
        toast('CSV berhasil diunduh!', 's');
    }

    function printLaporan() {
        window.print();
    }

    // Switch cashflow chart type
    function switchLapChart(type, btn) {
        lapChartType = type;
        document.getElementById('lap-btn-bar').style.background = type === 'bar' ? 'var(--surface2)' : '';
        document.getElementById('lap-btn-line').style.background = type === 'line' ? 'var(--surface2)' : '';
        generateReport();
    }

    // --- MAIN generateReport ---
    // === REPORT / Analisa Tab Logic for Fintech UI ===
    let lapCurrentPeriod = 'this_month';
    function setLapPeriod(period, btnEl) {
        lapCurrentPeriod = period;
        document.querySelectorAll('.lap-pill').forEach(btn => btn.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');
        
        const customDate = document.getElementById('lap-custom-date');
        if (period === 'custom') {
            customDate.style.display = 'flex';
        } else {
            customDate.style.display = 'none';
            generateReport();
        }
    }

    // Helper for date range from pills
    function getLapActiveDateRange() {
        const now = new Date();
        const y = now.getFullYear();
        const m = now.getMonth();
        let start, end;
        
        if (lapCurrentPeriod === 'this_month') {
            start = new Date(y, m, 1);
            end = new Date(y, m + 1, 0, 23, 59, 59);
        } else if (lapCurrentPeriod === 'last_month') {
            start = new Date(y, m - 1, 1);
            end = new Date(y, m, 0, 23, 59, 59);
        } else if (lapCurrentPeriod === 'last_3_months') {
            start = new Date(y, m - 2, 1);
            end = new Date(y, m + 1, 0, 23, 59, 59);
        } else if (lapCurrentPeriod === 'this_year') {
            start = new Date(y, 0, 1);
            end = new Date(y, 11, 31, 23, 59, 59);
        } else if (lapCurrentPeriod === 'custom') {
            const f = document.getElementById('lap-date-from').value;
            const t = document.getElementById('lap-date-to').value;
            if (f && t) {
                start = new Date(f + 'T00:00:00');
                end = new Date(t + 'T23:59:59');
            } else { return null; }
        }
        return { start, end };
    }

    async function generateReport() {
        populateLapAccountFilter();
        
        if (!allTxList || !allTxList.length) {
            document.getElementById('lap-donut-empty').style.display = 'flex';
            document.getElementById('lap-det-tbody').innerHTML = '<tr><td colspan="5"><div class="empty"><i class="fas fa-receipt"></i>Belum ada data transaksi</div></td></tr>';
            document.getElementById('lap-smart-insight').innerHTML = 'Mulai catat transaksi untuk mendapatkan insight keuangan otomatis.';
            return;
        }

        const range = getLapActiveDateRange();
        const accFilter = document.getElementById('lap-filter-account')?.value || 'all';
        lapTxVisibleCount = 20;

        const filteredTx = allTxList.filter(tx => {
            if (tx.type === 'transfer') return false;
            if (accFilter !== 'all' && String(tx.account_id) !== String(accFilter)) return false;
            if (range) {
                const d = new Date(tx.transaction_date);
                if (d < range.start || d > range.end) return false;
            }
            return true;
        });

        let totalInc = 0, totalExp = 0;
        const catExpMap = {};
        const tsData = {}; // time series data for line chart
        
        filteredTx.forEach(tx => {
            const amt = parseFloat(tx.amount || 0);
            const isInc = tx.type === 'income';
            
            if (isInc) { totalInc += amt; } 
            else {
                totalExp += amt;
                const cName = tx.category?.name || 'Lain-lain';
                catExpMap[cName] = (catExpMap[cName] || 0) + amt;
            }

            // Daily aggregation for line chart
            const dStr = tx.transaction_date.split('T')[0]; 
            if (!tsData[dStr]) tsData[dStr] = { inc: 0, exp: 0 };
            if (isInc) tsData[dStr].inc += amt; else tsData[dStr].exp += amt;
        });

        const net = totalInc - totalExp;
        const savingRate = totalInc > 0 ? (net / totalInc) * 100 : 0;

        // ── 0. Month-over-Month (MoM) Logic & Financial Score ──
        let prevInc = 0, prevExp = 0, prevNet = 0, prevSav = 0;
        if (range && allTxList && allTxList.length) {
            const rangeDuration = range.end.getTime() - range.start.getTime();
            const prevStart = new Date(range.start.getTime() - rangeDuration);
            const prevEnd = new Date(range.end.getTime() - rangeDuration);
            
            allTxList.forEach(tx => {
                if (tx.type === 'transfer') return;
                const d = new Date(tx.transaction_date);
                if (d >= prevStart && d <= prevEnd) {
                    const amt = parseFloat(tx.amount || 0);
                    if (tx.type === 'income') prevInc += amt;
                    else prevExp += amt;
                }
            });
            prevNet = prevInc - prevExp;
            prevSav = prevInc > 0 ? (prevNet / prevInc) * 100 : 0;
        }

        function updateMom(elId, curr, prev, invert = false) {
            const el = document.getElementById(elId);
            if (!el) return;
            if (prev === 0 && curr === 0) {
                el.innerHTML = '--'; el.className = 'lap-mom-badge neutral'; return;
            }
            if (prev === 0) {
                el.innerHTML = `<i class="fas fa-arrow-up"></i> 100%`;
                el.className = `lap-mom-badge ${invert ? 'negative' : 'positive'}`;
                return;
            }
            const diffPct = ((curr - prev) / prev) * 100;
            const isPos = diffPct >= 0;
            if (diffPct === 0) { el.innerHTML = '--'; el.className = 'lap-mom-badge neutral'; return; }
            
            const isGood = invert ? !isPos : isPos; // For expenses, down is good (positive class)
            el.innerHTML = `<i class="fas fa-arrow-${isPos ? 'up' : 'down'}"></i> ${Math.abs(diffPct).toFixed(1)}%`;
            el.className = `lap-mom-badge ${isGood ? 'positive' : 'negative'}`;
        }

        updateMom('lap-mom-income', totalInc, prevInc);
        updateMom('lap-mom-expense', totalExp, prevExp, true);
        updateMom('lap-mom-net', net, prevNet);
        updateMom('lap-mom-saving', savingRate, prevSav);

        // Calculate FinHealth Score
        // Base: 40 pts if saving rate > 10%, scale up to 60 for > 20%
        // Expense ratio: up to 40 pts if expenses < 50% of income 
        let score = 0;
        if (totalInc > 0) {
            if (savingRate > 20) score += 60;
            else if (savingRate > 10) score += 40;
            else if (savingRate > 0) score += 20;

            const expRatio = totalExp / totalInc;
            if (expRatio <= 0.4) score += 40;
            else if (expRatio <= 0.6) score += 30;
            else if (expRatio <= 0.8) score += 15;
            else if (expRatio < 1) score += 5;
        } else if (totalExp === 0 && totalInc === 0) {
            score = 0;
        } else {
            score = 10; // Only spending, no income
        }
        
        // Ensure no unrealistic zeroes if active
        if (score === 0 && (totalInc > 0 || totalExp > 0)) score = 50; 
        
        const scoreEl = document.getElementById('lap-val-score');
        if (scoreEl) {
            scoreEl.innerHTML = `${Math.round(score)} <span style="font-size:12px;color:var(--text-muted);font-weight:600;">/ 100</span>`;
            if (score >= 80) scoreEl.style.color = 'var(--accent)';
            else if (score >= 50) scoreEl.style.color = 'var(--text)';
            else scoreEl.style.color = '#ef4444';
        }

        // ── 1. Update Metrics ──
        document.getElementById('lap-val-income').textContent = rp(totalInc);
        document.getElementById('lap-val-expense').textContent = rp(totalExp);
        document.getElementById('lap-val-net').textContent = rp(net);
        document.getElementById('lap-val-saving').textContent = Math.max(0, savingRate).toFixed(1) + '%';
        
        if (net < 0) document.getElementById('lap-val-net').style.color = '#ef4444'; 
        else document.getElementById('lap-val-net').style.color = '#3b82f6';

        // ── 2. Line Chart (Cashflow Trend) ──
        const labels = Object.keys(tsData).sort();
        const chartLabels = labels.map(l => {
            const d = new Date(l);
            return `${d.getDate()} ${d.toLocaleString('id-ID', {month:'short'})}`;
        });
        const incData = labels.map(k => tsData[k].inc);
        const expData = labels.map(k => tsData[k].exp);

        const ctxTrend = document.getElementById('lapTrendChart').getContext('2d');
        if (lapCashflowChart) lapCashflowChart.destroy();
        
        const gInc = ctxTrend.createLinearGradient(0,0,0,240); 
        gInc.addColorStop(0,'rgba(34,197,94,0.3)'); gInc.addColorStop(1,'rgba(34,197,94,0.02)');
        const gExp = ctxTrend.createLinearGradient(0,0,0,240); 
        gExp.addColorStop(0,'rgba(239,68,68,0.3)'); gExp.addColorStop(1,'rgba(239,68,68,0.02)');
        
        lapCashflowChart = new Chart(ctxTrend, { 
            type: 'line', 
            data: { 
                labels: chartLabels, 
                datasets: [
                    { label:'Pemasukan', data:incData, borderColor:'#22c55e', backgroundColor:gInc, fill:true, tension:0.4, borderWidth:2, pointRadius:2, pointHoverRadius:4 }, 
                    { label:'Pengeluaran', data:expData, borderColor:'#ef4444', backgroundColor:gExp, fill:true, tension:0.4, borderWidth:2, pointRadius:2, pointHoverRadius:4 }
                ] 
            }, 
            options: { 
                responsive:true, maintainAspectRatio:false, 
                interaction: { mode: 'index', intersect: false },
                plugins: { 
                    legend: { display:true, position:'top', align:'end', labels: { boxWidth:10, usePointStyle:true, font:{size:11, family:"'Inter', sans-serif"} } },
                    tooltip: { backgroundColor:'rgba(15,23,42,0.9)', padding:10, cornerRadius:8, callbacks:{ label: c => ' ' + c.dataset.label + ': ' + rp(c.raw) } } 
                },
                scales: { 
                    y: { border:{display:false}, grid:{color:'rgba(0,0,0,0.04)'}, ticks:{color:'#64748b', font:{size:10}, callback:v=>rp(v)} }, 
                    x: { border:{display:false}, grid:{display:false}, ticks:{color:'#64748b', font:{size:10}, maxTicksLimit:10} } 
                } 
            } 
        });

        // ── 3. Donut Chart (Category Breakdown) ──
        const catSorted = Object.entries(catExpMap).sort((a,b) => b[1]-a[1]);
        const topCats = catSorted.slice(0, 5);
        if (catSorted.length > 5) {
            const othersAmt = catSorted.slice(5).reduce((acc, curr) => acc + curr[1], 0);
            topCats.push(['Lainnya', othersAmt]);
        }

        const dLabels = topCats.map(c => c[0]);
        const dData   = topCats.map(c => c[1]);
        const dColors = ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444', '#94a3b8'];
        
        document.getElementById('lap-donut-empty').style.display = totalExp > 0 ? 'none' : 'flex';
        
        const ctxDonut = document.getElementById('lapDonutChart').getContext('2d');
        if (lapPillarChart) lapPillarChart.destroy();
        
        if (totalExp > 0) {
            lapPillarChart = new Chart(ctxDonut, { 
                type: 'doughnut', 
                data: { labels: dLabels, datasets: [{ data: dData, backgroundColor: dColors, borderWidth: 0, hoverOffset: 4 }] }, 
                options: { 
                    responsive: true, maintainAspectRatio: false, cutout: '75%', 
                    plugins: { legend: { display:false }, tooltip: { backgroundColor:'rgba(15,23,42,0.9)', callbacks:{ label: c => ' '+rp(c.raw) } } } 
                } 
            });
            
            // Build custom legend
            document.getElementById('lap-donut-legend').innerHTML = topCats.map((c, i) => {
                const pct = ((c[1]/totalExp)*100).toFixed(1);
                return `<div class="lap-legend-item">
                    <div class="lap-legend-left">
                        <div class="lap-legend-color" style="background:${dColors[i]}"></div>
                        <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px;" title="${c[0]}">${c[0]}</span>
                    </div>
                    <div class="lap-legend-right">
                        <span class="lap-legend-val">${rp(c[1])}</span>
                        <span class="lap-legend-pct">${pct}%</span>
                    </div>
                </div>`;
            }).join('');

            // Build Top Expenses Progress Bars
            const maxExpAmt = topCats.length > 0 ? topCats[0][1] : 1;
            document.getElementById('lap-top-expenses').innerHTML = topCats.map((c, i) => {
                const fillPct = (c[1] / maxExpAmt) * 100;
                return `
                <div class="lap-exp-row">
                    <div class="lap-exp-header">
                        <span style="color:var(--text);">${c[0]}</span>
                        <span class="lap-exp-val">${rp(c[1])}</span>
                    </div>
                    <div class="lap-exp-track">
                        <div class="lap-exp-fill" style="width:${fillPct}%; background:${dColors[i]}"></div>
                    </div>
                </div>`;
            }).join('');
            
        } else {
            document.getElementById('lap-donut-legend').innerHTML = '';
            document.getElementById('lap-top-expenses').innerHTML = '<div class="empty"><i class="fas fa-check-circle" style="color:var(--accent);"></i>Belum ada pengeluaran</div>';
        }

        // ── 4. Smart Insight ──
        let insights = [];
        if (totalExp === 0 && totalInc === 0) {
            insights.push('Belum ada aktivitas transaksi di periode ini.');
        } else {
            if (savingRate >= 20) {
                insights.push(`🔥 <strong>Sehat:</strong> Saving rate kamu berada di angka aman (${savingRate.toFixed(1)}%). Pertahankan!`);
            } else if (savingRate > 0) {
                insights.push(`💡 <strong>Saran:</strong> Kamu masih mencatat surplus, cobalah alokasikan sedikit pengeluaran konsumtif ke tabungan untuk menaikkan Financial Score-mu.`);
            } else if (totalExp > 0) {
                const deficit = totalExp - totalInc;
                insights.push(`⚠️ <strong>Perhatian:</strong> Kamu mengalami defisit sebesar ${rp(deficit)}. Pantau ketat arus kasmu minggu ini.`);
            }

            if (topCats.length > 0) {
                insights.push(`📈 <strong>Terkendali:</strong> <strong>${topCats[0][0]}</strong> adalah area pengeluaran paling aktif, mengambil porsi ${((topCats[0][1]/totalExp)*100).toFixed(0)}% dari total pengeluaran.`);
            }

            if (prevExp > 0) {
                if (totalExp < prevExp) {
                    insights.push(`⭐ <strong>Tren Bagus:</strong> Pengeluaranmu lebih rendah ${( (prevExp - totalExp) / prevExp * 100 ).toFixed(1)}% dibanding periode sebelumnya.`);
                }
            }
        }
        
        document.getElementById('lap-smart-insight').innerHTML = `<ul>${insights.map(i => `<li>${i}</li>`).join('')}</ul>`;

        // ── 5. Detailed Table ──
        renderLapTxTable(filteredTx);
    }

    // === ANGGARAN TAB LOGIC ===
    const PILLAR_META = {
        wajib:     { label: 'Wajib',     color: 'var(--info)',    bg: 'rgba(59,130,246,0.08)',  border: 'rgba(59,130,246,0.25)' },
        growth:    { label: 'Growth',    color: 'var(--accent)',  bg: 'rgba(16,185,129,0.08)', border: 'rgba(16,185,129,0.25)' },
        lifestyle: { label: 'Lifestyle', color: 'var(--warning)', bg: 'rgba(245,158,11,0.08)', border: 'rgba(245,158,11,0.25)' },
        bocor:     { label: 'Bocor',     color: 'var(--danger)',  bg: 'rgba(239,68,68,0.08)',  border: 'rgba(239,68,68,0.25)' },
    };

    // Feature 8: Rollover toggle
    let rolloverEnabled = JSON.parse(localStorage.getItem('bgt_rollover') || 'false');
    function toggleRollover(el) {
        rolloverEnabled = el.checked;
        localStorage.setItem('bgt_rollover', JSON.stringify(rolloverEnabled));
        const label = document.getElementById('ang-rollover-label');
        const val = document.getElementById('ang-val-rollover');
        if (rolloverEnabled) {
            label.textContent = 'Aktif';
            label.style.color = 'var(--accent)';
            val.textContent = 'On';
            val.style.color = 'var(--accent)';
            toast('Rollover aktif: sisa budget akan diteruskan ke bulan depan', 's');
        } else {
            label.textContent = 'Nonaktif';
            label.style.color = 'var(--text-muted)';
            val.textContent = 'Off';
            val.style.color = 'var(--text-muted)';
        }
        loadAnggaran();
    }

    // Feature 7: Budget Recommendation
    function refreshRecommendations() {
        const el = document.getElementById('ang-recommendations');
        if (!el) return;
        el.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Menganalisa...</div>';
        setTimeout(() => computeRecommendations(), 400);
    }
    function computeRecommendations() {
        const el = document.getElementById('ang-recommendations');
        if (!el) return;
        if (!allTxList || !allTxList.length) {
            el.innerHTML = '<div class="empty"><i class="fas fa-database"></i>Tambah transaksi untuk mendapat rekomendasi</div>';
            return;
        }
        const picker = document.getElementById('ang-filter-month');
        const selMonth = picker ? picker.value : '';
        const now = new Date();
        // Analyze last 3 months
        const catAvg = {};
        const catCount = {};
        for (let m = 1; m <= 3; m++) {
            const d = new Date(now.getFullYear(), now.getMonth() - m, 1);
            const yr = d.getFullYear(), mo = d.getMonth() + 1;
            const monthTx = allTxList.filter(tx => {
                const td = new Date(tx.transaction_date);
                return td.getFullYear() === yr && (td.getMonth() + 1) === mo && tx.type === 'expense';
            });
            monthTx.forEach(tx => {
                const cat = tx.category?.name || 'Tak Berkategori';
                catAvg[cat] = (catAvg[cat] || 0) + parseFloat(tx.amount || 0);
                catCount[cat] = (catCount[cat] || 0) + 1;
            });
        }
        // Average per category
        const recs = Object.entries(catAvg)
            .map(([cat, total]) => ({ cat, avg: Math.round(total / 3), pillar: allTxList.find(tx => tx.category?.name === cat)?.category?.pillar || 'lainnya' }))
            .filter(r => r.avg > 0)
            .sort((a, b) => b.avg - a.avg)
            .slice(0, 5);

        if (!recs.length) {
            el.innerHTML = '<div class="empty"><i class="fas fa-chart-bar"></i>Belum cukup data (min. 1 bulan)</div>';
            return;
        }
        const [ico, col] = ['fa-lightbulb', '#f59e0b'];
        el.innerHTML = `
            <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px;"><i class="fas fa-info-circle" style="margin-right:4px;"></i>Berdasarkan rata-rata 3 bulan lalu</div>
            ${recs.map(r => `
            <div class="rec-item">
                <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0;">
                    <div class="rec-icon"><i class="fas ${(CAT_ICONS[r.cat] || DEFAULT_ICON)[0]}" style="color:${(CAT_ICONS[r.cat] || DEFAULT_ICON)[1]}"></i></div>
                    <div style="min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.cat}</div>
                        <div style="font-size:10px;color:var(--text-muted);margin-top:1px;">Rata-rata: ${rp(r.avg)}/bln</div>
                    </div>
                </div>
                <span class="rec-badge" onclick="openBudgetWithSuggestion(${JSON.stringify(r.cat)}, ${r.avg})" title="Pakai saran ini">Pakai ${rp(Math.round(r.avg * 1.1))}</span>
            </div>`).join('')}
        `;
    }

    function openBudgetWithSuggestion(catName, suggestedAmt) {
        openModal('modal-budget');
        setTimeout(() => {
            document.getElementById('bgt-limit').value = Math.round(suggestedAmt * 1.1);
            const cat = categories.find(c => c.name === catName);
            if (cat) {
                document.getElementById('bgt-cat-val').value = cat.id;
                const [ico, col] = CAT_ICONS[cat.name] || DEFAULT_ICON;
                setCddDisplay('cdd-bgt-icon', 'cdd-bgt-lbl', cat.name, ico, col, false);
            }
        }, 200);
    }

    // Feature 4: Trend Chart (6 months)
    function renderBgtTrendChart(budgetsByMonth) {
        const el = document.getElementById('ang-trend-chart');
        if (!el) return;
        if (!budgetsByMonth || !budgetsByMonth.length) {
            el.innerHTML = '<div class="empty"><i class="fas fa-chart-line"></i>Belum ada data historis</div>';
            return;
        }
        const maxPct = Math.max(...budgetsByMonth.map(m => m.pct), 20);
        el.innerHTML = `
        <div style="display:flex;gap:20px;align-items:flex-end;">
            <div style="flex:1;">
                <div style="display:grid;grid-template-columns:repeat(${budgetsByMonth.length},1fr);gap:8px;align-items:flex-end;height:72px;margin-bottom:6px;">
                    ${budgetsByMonth.map(m => {
                        const h = Math.max(6, (m.pct / maxPct) * 72);
                        const col = m.pct >= 100 ? 'var(--danger)' : m.pct > 80 ? 'var(--warning)' : 'var(--accent)';
                        return `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;justify-content:flex-end;height:72px;">
                            <div style="font-size:10px;font-weight:700;color:${col};">${m.pct.toFixed(0)}%</div>
                            <div class="bgt-trend-bar" data-tip="${m.label}: ${rp(m.used)} / ${rp(m.budget)}" style="height:${h}px;background:${col};width:100%;border-radius:3px 3px 0 0;"></div>
                        </div>`;
                    }).join('')}
                </div>
                <div style="display:grid;grid-template-columns:repeat(${budgetsByMonth.length},1fr);gap:8px;border-top:1px solid var(--border);padding-top:6px;">
                    ${budgetsByMonth.map(m => `<div style="font-size:10px;color:var(--text-muted);text-align:center;">${m.label}</div>`).join('')}
                </div>
            </div>
            <div style="width:120px;flex-shrink:0;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);margin-bottom:8px;">Ringkasan</div>
                <div style="font-size:11px;color:var(--text-sub);margin-bottom:4px;">Rata-rata pemakaian:</div>
                <div style="font-size:15px;font-weight:800;color:var(--text);font-family:'JetBrains Mono',monospace;">${(budgetsByMonth.reduce((a,m)=>a+m.pct,0)/budgetsByMonth.length).toFixed(0)}%</div>
                <div style="font-size:10px;color:var(--text-muted);margin-top:8px;">Bulan over budget:</div>
                <div style="font-size:15px;font-weight:800;color:${budgetsByMonth.filter(m=>m.pct>=100).length > 0 ? 'var(--danger)' : 'var(--accent)'};font-family:'JetBrains Mono',monospace;">${budgetsByMonth.filter(m=>m.pct>=100).length}x</div>
            </div>
        </div>`;
    }

    // Feature 1: Bar Chart Budget vs Aktual
    function renderBgtBarChart(budgets, timePct) {
        const el = document.getElementById('ang-bar-chart');
        if (!el) return;
        if (!budgets.length) {
            el.innerHTML = '<div class="empty"><i class="fas fa-chart-bar"></i>Belum ada budget</div>';
            return;
        }
        const sorted = [...budgets].sort((a,b) => b.pct - a.pct);
        el.innerHTML = '<div class="bgt-chart-wrap">' + sorted.map(b => {
            const barColor = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
            const warnPill = b.pct >= 100
                ? '<span class="bgt-warn-pill bgt-warn-over"><i class="fas fa-fire" style="font-size:9px;"></i>OVER</span>'
                : b.pct > 80
                ? '<span class="bgt-warn-pill bgt-warn-warn"><i class="fas fa-triangle-exclamation" style="font-size:9px;"></i>WASPADA</span>'
                : '<span class="bgt-warn-pill bgt-warn-ok"><i class="fas fa-check" style="font-size:9px;"></i>OK</span>';
            return `<div class="bgt-bar-row">
                <div class="bgt-bar-label" title="${b.catName}">${b.catName}</div>
                <div>
                    <div class="bgt-bar-track">
                        <div class="bgt-bar-fill" style="width:${Math.min(b.pct,100).toFixed(1)}%;background:${barColor};"></div>
                        <div class="bgt-bar-time" style="left:${timePct.toFixed(1)}%;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:3px;">
                        <span style="font-size:10px;color:var(--text-muted);">Pakai: ${rp(b.usage)}</span>
                        <span style="font-size:10px;color:var(--text-muted);">Limit: ${rp(b.limit)}</span>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
                    <span class="bgt-bar-val" style="color:${barColor};">${b.pct.toFixed(0)}%</span>
                    ${warnPill}
                </div>
            </div>`;
        }).join('') + '</div>';
    }

    // Feature 2: Edit Budget
    function editBudget(budgetId, catName, limit, month) {
        document.getElementById('edit-bgt-id').value = budgetId;
        document.getElementById('edit-bgt-cat-name').textContent = catName;
        document.getElementById('edit-bgt-month').value = month;
        document.getElementById('edit-bgt-limit').value = limit;
        openModal('modal-edit-budget');
    }
    async function submitEditBudget() {
        const id = document.getElementById('edit-bgt-id').value;
        const month = document.getElementById('edit-bgt-month').value;
        const limit = parseFloat(document.getElementById('edit-bgt-limit').value);
        try {
            await CashbookService.saveBudget({ id, month, limit_amount: limit });
            toast('Budget berhasil diperbarui!');
            closeModal('modal-edit-budget');
            await refreshUI();
        } catch (e) { toast(e.message || 'Gagal memperbarui budget', 'e'); }
    }

    // Feature 2: Delete Budget
    async function deleteBudget(budgetId, catName) {
        confirmDialog(
            'Hapus Budget',
            `Budget untuk "${catName}" akan dihapus. Lanjutkan?`,
            'danger',
            async () => {
                try {
                    await CashbookService.deleteBudget(String(budgetId));
                    toast('Budget dihapus');
                    await refreshUI();
                } catch(e) { toast(e.message || 'Gagal menghapus budget', 'e'); }
            }
        );
    }

    // Feature 5: Empty State Wizard
    function renderBgtWizard() {
        const wrap = document.getElementById('ang-pillars-wrap');
        const pillarPresets = [
            { label: 'Kebutuhan Pokok', icon: 'fa-house', catPillar: 'wajib', hint: 'Sewa, Listrik, Makan' },
            { label: 'Investasi', icon: 'fa-seedling', catPillar: 'growth', hint: 'Tabungan, Saham' },
            { label: 'Gaya Hidup', icon: 'fa-star', catPillar: 'lifestyle', hint: 'Hiburan, Belanja' },
            { label: 'Lainnya', icon: 'fa-tag', catPillar: 'bocor', hint: 'Pengeluaran tak terduga' },
        ];
        wrap.innerHTML = `
        <div class="bgt-wizard">
            <div style="width:52px;height:52px;border-radius:14px;background:var(--accent-glow);border:2px solid var(--border-accent);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:20px;color:var(--accent);">
                <i class="fas fa-sliders"></i>
            </div>
            <div style="font-size:17px;font-weight:800;color:var(--text);margin-bottom:8px;">Atur Anggaran Pertamamu</div>
            <div style="font-size:13px;color:var(--text-sub);margin-bottom:20px;line-height:1.6;">Tetapkan limit pengeluaran per kategori untuk mengontrol keuangan dengan lebih disiplin.</div>
            <div class="wizard-steps">
                ${pillarPresets.map(p => `
                <div class="wizard-step" onclick="openModal('modal-budget')" title="${p.hint}">
                    <div class="wizard-step-icon"><i class="fas ${p.icon}"></i></div>
                    <div class="wizard-step-label">${p.label}</div>
                    <div style="font-size:9px;color:var(--text-muted);margin-top:1px;">${p.hint}</div>
                </div>`).join('')}
            </div>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                <button class="btn btn-accent" onclick="openModal('modal-budget')"><i class="fas fa-plus"></i> Set Budget Baru</button>
                <button class="btn btn-ghost btn-sm" onclick="copyLastMonthBudget()"><i class="fas fa-copy"></i> Copy Bulan Lalu</button>
            </div>
        </div>`;
    }

    // === DATA & BACKUP (JSON EXPORT/IMPORT) ===

    // Track the currently selected backup interval in the UI
    let _selectedBackupInterval = 7;

    async function loadBackupSettings() {
        try {
            const enabled = (await localAPI.meta.get('auto_backup_enabled')) === 'true' || (await localAPI.meta.get('auto_backup_enabled')) === true;
            const interval = parseInt(await localAPI.meta.get('backup_interval_days') || '7');
            const lastStr  = await localAPI.meta.get('last_backup_at');

            // Toggle state
            const toggle = document.getElementById('ab-enabled-toggle');
            if (toggle) toggle.checked = enabled;
            _selectedBackupInterval = interval || 7;
            _updateToggleUI(enabled);

            // Card selection
            selectBackupInterval(_selectedBackupInterval, false);

            // Last / next backup info
            const lastEl = document.getElementById('ab-last-backup');
            const nextEl = document.getElementById('ab-next-backup');
            if (lastStr && lastStr !== 'null') {
                const last = new Date(lastStr);
                lastEl.textContent = last.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
                if (enabled) {
                    const next = new Date(last.getTime() + _selectedBackupInterval * 86400000);
                    nextEl.textContent = next.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
                } else {
                    nextEl.textContent = 'Nonaktif';
                }
            } else {
                if (lastEl) lastEl.textContent = 'Belum pernah';
                if (nextEl) nextEl.textContent = enabled ? 'Saat app dibuka berikutnya' : 'Nonaktif';
            }
        } catch(e) { console.warn('loadBackupSettings error', e); }
    }

    function _updateToggleUI(enabled) {
        const track  = document.getElementById('ab-track');
        const thumb  = document.getElementById('ab-thumb');
        const label  = document.getElementById('ab-toggle-label');
        const opts   = document.getElementById('ab-options');
        const hint   = document.getElementById('ab-hint');
        if (!track) return;
        if (enabled) {
            track.style.background = 'var(--accent)';
            thumb.style.left = '22px';
            thumb.style.background = '#fff';
            label.textContent = 'Aktif';
            label.style.color = 'var(--accent)';
            if (opts) opts.style.display = 'block';
            if (hint) hint.style.display = 'none';
        } else {
            track.style.background = 'var(--surface3)';
            thumb.style.left = '2px';
            thumb.style.background = 'var(--text-muted)';
            label.textContent = 'Nonaktif';
            label.style.color = 'var(--text-muted)';
            if (opts) opts.style.display = 'none';
            if (hint) hint.style.display = 'block';
        }
    }

    function onAutoBackupToggle(checkbox) {
        _updateToggleUI(checkbox.checked);
    }

    function selectBackupInterval(days, autoSave = false) {
        _selectedBackupInterval = days;
        [1, 7, 30].forEach(d => {
            const card = document.getElementById(`ab-opt-${d}`);
            if (card) card.className = 'ab-opt-card' + (d === days ? ' selected' : '');
        });
        if (autoSave) saveBackupSettings();
    }

    async function saveBackupSettings() {
        const enabled = document.getElementById('ab-enabled-toggle')?.checked || false;
        try {
            await localAPI.meta.set('auto_backup_enabled', String(enabled));
            await localAPI.meta.set('backup_interval_days', String(_selectedBackupInterval));
            toast(enabled ? `Backup otomatis aktif setiap ${_selectedBackupInterval} hari!` : 'Backup otomatis dinonaktifkan', 's');
            await loadBackupSettings(); // refresh display
        } catch(e) { toast('Gagal menyimpan pengaturan backup', 'e'); }
    }

    async function exportData() {
        toast('Sedang menyiapkan file backup...', 'i');
        try {
            // Some users may not have the 'installments' store if their DB was created before it was added
            let installments = [];
            try { installments = await localAPI.installments.getAll(); } catch(_) {}

            const backupData = {
                exported_at: new Date().toISOString(),
                version: 1,
                data: {
                    meta: await localAPI.meta.getAll(),
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
            a.download = `cuan_cashbook_backup_${new Date().toISOString().slice(0,10)}.json`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            toast('Backup berhasil diunduh!', 's');
            closeModal('modal-data');
        } catch (e) {
            toast('Gagal melakukan export data: ' + e.message, 'e');
        }
    }

    async function handleImportFile(input) {
        if (!input.files || input.files.length === 0) return;
        const file = input.files[0];
        
        if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
            toast('File harus berformat .json', 'e');
            input.value = '';
            return;
        }

        toast('Sedang memulihkan data...', 'i');
        try {
            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    const parsed = JSON.parse(e.target.result);
                    if (!parsed.data || typeof parsed.data !== 'object') throw new Error("Format JSON tidak valid");
                    
                    const d = parsed.data;
                    
                    // Clear existing data (except meta, we just update it)
                    if (d.categories) { await clearStore('categories'); for(const item of d.categories) await localAPI.categories.save(item); }
                    if (d.accounts) { await clearStore('accounts'); for(const item of d.accounts) await localAPI.accounts.save(item); }
                    if (d.transactions) { await clearStore('transactions'); for(const item of d.transactions) await localAPI.transactions.save(item); }
                    if (d.budgets) { await clearStore('budgets'); for(const item of d.budgets) await localAPI.budgets.save(item); }
                    if (d.debts) { await clearStore('debts'); for(const item of d.debts) await localAPI.debts.save(item); }
                    // installments store may not exist in older DBs — skip gracefully
                    if (d.installments && d.installments.length) {
                        try {
                            await clearStore('installments');
                            for(const item of d.installments) await localAPI.installments.save(item);
                        } catch(_) {}
                    }
                    if (d.meta) { for(const item of d.meta) await localAPI.meta.set(item.key, item.value); }
                    
                    toast('Restore Sukses! Memuat ulang Cashbook...', 's');
                    closeModal('modal-data');
                    setTimeout(() => window.location.reload(), 1500);
                } catch(err) {
                    toast('Gagal membaca JSON: ' + err.message, 'e');
                }
            };
            reader.readAsText(file);
        } catch (e) {
            toast('Terjadi kesalahan sistem saat me-restore data', 'e');
        } finally {
            input.value = ''; // reset input
        }
    }
    
    // Helper function to clear a store — returns the IDBRequest so _runOp can resolve it
    function clearStore(storeName) {
        return localAPI._runOp(storeName, 'readwrite', store => store.clear());
    }

    // Main loadAnggaran for Premium Fintech UI
    async function loadAnggaran() {
        const picker = document.getElementById('ang-filter-month');
        if (!picker.value) {
            const n = new Date();
            picker.value = `${n.getFullYear()}-${String(n.getMonth()+1).padStart(2,'0')}`;
        }
        const selMonth = picker.value;
        const [yr, mo] = selMonth.split('-').map(Number);

        let budgets = [];
        try {
            const allBgts = await localAPI.budgets.getAll();
            budgets = allBgts.filter(b => !b.month || b.month === selMonth || (b.month && b.month.startsWith(selMonth)));
        } catch(e) {}

        const monthTx = (allTxList || []).filter(tx => {
            const d = new Date(tx.transaction_date);
            return d.getFullYear() === yr && (d.getMonth()+1) === mo && tx.type === 'expense';
        });

        const catSpent = {};
        monthTx.forEach(tx => {
            const key = String(tx.category_id || 'uncategorized');
            catSpent[key] = (catSpent[key] || 0) + parseFloat(tx.amount || 0);
        });

        let totalBudget = 0, totalUsed = 0;
        const budgetItems = [];
        
        budgets.forEach(b => {
            const limit = parseFloat(b.limit || b.limit_amount || 0);
            const usage = parseFloat(b.usage || catSpent[String(b.category_id)] || 0);
            const pct = limit > 0 ? (usage / limit) * 100 : 0;
            const catInfo = (categories || []).find(c => c.id == b.category_id) || {};
            const catName = b.category_name || catInfo.name || 'Lainnya';
            totalBudget += limit;
            totalUsed += usage;
            budgetItems.push({ ...b, limit, usage, pct, catName, id: b.id });
        });

        const totalRemaining = totalBudget - totalUsed;
        const spendPct = totalBudget > 0 ? (totalUsed / totalBudget) * 100 : 0;

        // ── 1. Header Summary ──
        document.getElementById('ang-sum-used').textContent = rp(totalUsed);
        document.getElementById('ang-sum-total').textContent = rp(totalBudget);
        document.getElementById('ang-sum-left').textContent = rp(Math.max(0, totalRemaining));
        document.getElementById('ang-sum-pct').textContent = `${spendPct.toFixed(0)}%`;
        
        const sumBar = document.getElementById('ang-sum-bar');
        sumBar.style.width = `${Math.min(100, spendPct)}%`;
        sumBar.style.background = spendPct >= 100 ? 'var(--danger)' : spendPct > 80 ? 'var(--warning)' : 'var(--accent)';

        const progList = document.getElementById('ang-progress-list');
        const catList = document.getElementById('ang-category-list');
        
        if (!budgetItems.length) {
            progList.innerHTML = '<div class="empty"><i class="fas fa-folder-open"></i>Belum ada data progress</div>';
            catList.innerHTML = '<div class="empty" style="padding:32px 20px;"><i class="fas fa-folder-open"></i>Belum ada anggaran dibuat.<br><span style="font-size:12px;opacity:0.7;margin-top:6px;display:block;">Anggaran membantu kamu mengontrol pengeluaran.</span></div>';
            document.getElementById('ang-insight-text').innerHTML = 'Buat anggaran pertamamu untuk mendapatkan AI insight seputar pengeluaranmu.';
            return;
        }

        budgetItems.sort((a,b) => b.pct - a.pct); // Highest % first

        // ── 2. Progress Visuals ──
        progList.innerHTML = budgetItems.slice(0, 5).map(b => {
            const color = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
            return `
            <div class="ang-prog-item" onclick="openModal('modal-budget'); setTimeout(()=>{document.getElementById('modal-budget').dataset.editId='${b.id}';document.getElementById('bg-category').value='${b.category_id}';document.getElementById('bg-limit').value='${b.limit}';},100)">
                <div class="ang-prog-header">
                    <span class="ang-prog-name">${b.catName}</span>
                    <span class="ang-prog-pct" style="color:${color}">${b.pct.toFixed(0)}%</span>
                    <div class="ang-prog-acts">
                        <button class="edit" title="Edit" onclick="event.stopPropagation(); editBudget(${b.id || 0}, '${b.catName.replace(/'/g,"\\'")}', ${b.limit}, '${selMonth}')"><i class="fas fa-pen"></i></button>
                        <button class="del" title="Hapus" onclick="event.stopPropagation(); deleteBudget(${b.id || 0}, '${b.catName.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="ang-prog-track">
                    <div class="ang-prog-fill" style="width:${Math.min(100, b.pct)}%; background:${color};"></div>
                </div>
                <div class="ang-prog-meta">
                    <span>${rp(b.usage)}</span>
                    <span>dari ${rp(b.limit)}</span>
                </div>
            </div>`;
        }).join('');

        // ── 3. Category List ──
        catList.innerHTML = budgetItems.map(b => {
            const color = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
            return `
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">${b.catName}</div>
                    <div style="font-size:12px;color:var(--text-muted);"><span style="color:${color};font-weight:700;">${rp(b.usage)}</span> / ${rp(b.limit)}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">Sisa: ${rp(Math.max(0, b.limit - b.usage))}</div>
                    <div style="display:flex;gap:4px;justify-content:flex-end;">
                        <button class="btn btn-ghost btn-sm" style="padding:4px 8px;" onclick="editBudget(${b.id || 0}, '${b.catName.replace(/'/g,"\\'")}', ${b.limit}, '${selMonth}')"><i class="fas fa-pen"></i></button>
                        <button class="btn btn-ghost btn-sm" style="padding:4px 8px;color:var(--danger);" onclick="deleteBudget(${b.id || 0}, '${b.catName.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>`;
        }).join('');

        // ── 4. Smart Insight ──
        const today = new Date();
        const daysInMonth = new Date(yr, mo, 0).getDate();
        const dayOfMonth = (today.getFullYear() === yr && (today.getMonth()+1) === mo) ? today.getDate() : daysInMonth;
        const timePct = (dayOfMonth / daysInMonth) * 100;

        let insightText = '';
        if (spendPct >= 100) {
            insightText = `<strong>Anggaran habis!</strong> Kamu sudah memakai 100% anggaran. Tahan pengeluaran yang tidak penting sampai bulan depan.`;
        } else if (spendPct > timePct + 15) {
            insightText = `<strong>Pengereman diperlukan.</strong> Pemakaian anggaran (${spendPct.toFixed(0)}%) lebih cepat dari waktu berjalan (${timePct.toFixed(0)}%).`;
        } else if (budgetItems.length > 0 && budgetItems[0].pct >= 90) {
            insightText = `Kategori <strong>${budgetItems[0].catName}</strong> hampir mencapai limit (${budgetItems[0].pct.toFixed(0)}%). Hati-hati bocor di kategori ini.`;
        } else {
            insightText = `<strong>Good job!</strong> Keuanganmu bulan ini berjalan sesuai rencana. Sisa anggaranmu masih ${rp(totalRemaining)}.`;
        }
        document.getElementById('ang-insight-text').innerHTML = insightText;
    }

    async function copyLastMonthBudget() {
        const picker = document.getElementById('ang-filter-month');
        if (!picker.value) return toast('Pilih bulan terlebih dahulu', 'e');
        const [yr, mo] = picker.value.split('-').map(Number);
        const lastMo = mo === 1 ? 12 : mo - 1;
        const lastYr = mo === 1 ? yr - 1 : yr;
        const lastMonth = `${lastYr}-${String(lastMo).padStart(2,'0')}`;
        try {
            const allBgts = await localAPI.budgets.getAll();
            const lastBudgets = allBgts.filter(b => b.month && b.month.startsWith(lastMonth));
            if (!lastBudgets.length) return toast(`Tidak ada budget di ${lastMonth}`, 'e');
            
            let copied = 0;
            for (const b of lastBudgets) {
                // Check if already exists this month
                const exist = allBgts.find(cb => cb.category_id == b.category_id && cb.month === picker.value);
                if (!exist) {
                    await localAPI.budgets.save({ category_id: b.category_id, month: picker.value, limit_amount: parseFloat(b.limit || b.limit_amount || 0) });
                    copied++;
                }
            }
            if (copied > 0) {
                toast(`${copied} budget disalin dari bulan lalu!`, 's'); 
                loadAnggaran();
            } else {
                toast('Semua budget bulan lalu sudah ada di bulan ini', 'i');
            }
        } catch(e) { toast('Gagal menyalin budget: ' + e.message, 'e'); }
    }






    // === DEBT & INSTALLMENT TAB ===
    let allDebts = [];

    async function loadDebts() {
        const uPayable = document.getElementById('utang-list-payable');
        const uReceivable = document.getElementById('utang-list-receivable');
        if (uPayable) uPayable.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat...</div>';
        if (uReceivable) uReceivable.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat...</div>';
        
        try {
            allDebts = await localAPI.debts.getAll();
        } catch(e) {
            console.error('Failed to load debts:', e);
            return;
        }
        renderDebtGrid();
    }

    function renderDebtGrid() {
        // Summary metrics
        let totalPayable = 0, countPayable = 0;
        let totalReceivable = 0, countReceivable = 0;
        let countPaidOff = 0;
        
        const payableList = [];
        const receivableList = [];

        allDebts.forEach(d => {
            const total = parseFloat(d.total_amount || 0);
            const paid = parseFloat(d.paid_amount || 0);
            const remaining = Math.max(0, total - paid);
            
            if (d.status === 'paid_off') { 
                countPaidOff++; 
            } else {
                if (d.debt_type === 'payable') { 
                    totalPayable += remaining; countPayable++; 
                    payableList.push({ ...d, total, paid, remaining });
                } else { 
                    totalReceivable += remaining; countReceivable++; 
                    receivableList.push({ ...d, total, paid, remaining });
                }
            }
        });

        // 1. Update Overview Card
        const setEl = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
        setEl('debt-val-total-payable', rp(totalPayable));
        setEl('debt-count-payable', `${countPayable} hutang aktif`);
        setEl('debt-val-total-receivable', rp(totalReceivable));
        setEl('debt-count-receivable', `${countReceivable} piutang aktif`);
        setEl('debt-val-paid-off', `${countPaidOff}`);
        setEl('debt-count-paid-off', 'pembayaran lunas');

        // 2. Render Lists
        setEl('dt-count-payable', `${countPayable} items`);
        setEl('dt-count-receivable', `${countReceivable} items`);

        const renderItem = (d, type) => {
            const pct = d.total > 0 ? Math.min(100, (d.paid / d.total) * 100) : 0;
            const tClass = type === 'payable' ? 'payable' : 'receivable';
            const bgFill = type === 'payable' ? '#f59e0b' : '#10b981';
            const btnLabel = type === 'payable' ? 'Bayar Cicilan' : 'Catat Penerimaan';
            const icon = type === 'payable' ? 'fa-money-bill-wave' : 'fa-hand-holding-dollar';
            
            const cleanName = d.debt_name.replace(/&amp;/g, '&').replace(/'/g, "\\'");
            const safeType = d.debt_type;

            return `
            <div class="utang-item">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div style="font-size:15px; font-weight:700; color:var(--text);">${d.debt_name}</div>
                    <span class="utang-badge ${tClass}">${pct.toFixed(0)}% LUNAS</span>
                </div>
                
                <div class="utang-prog-bg">
                    <div class="utang-prog-fill" style="width:${pct}%; background:${bgFill};"></div>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:6px; margin-top:4px;">
                    <div class="utang-meta-row">
                        <span>Total ${type === 'payable' ? 'Hutang' : 'Piutang'}</span>
                        <span class="utang-meta-val">${rp(d.total)}</span>
                    </div>
                    <div class="utang-meta-row">
                        <span>Sudah Dibayar</span>
                        <span class="utang-meta-val" style="color:${bgFill};">${rp(d.paid)}</span>
                    </div>
                    <div class="utang-meta-row" style="border-top:1px solid var(--border); padding-top:6px; margin-top:2px;">
                        <span style="font-weight:600; color:var(--text);">Sisa</span>
                        <span class="utang-meta-val" style="font-size:14px; font-weight:800; color:var(--text);">${rp(d.remaining)}</span>
                    </div>
                </div>
                
                <div class="utang-actions">
                    <button class="utang-btn primary" onclick="openPayInstallment('${d.id}', '${cleanName}', ${d.remaining}, '${safeType}')">
                        <i class="fas ${icon}"></i> ${btnLabel}
                    </button>
                    <button class="utang-btn secondary" onclick="deleteDebt(${d.id}, '${cleanName}')" style="max-width:40px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;
        };

        const uPay = document.getElementById('utang-list-payable');
        if (uPay) {
            if (payableList.length > 0) {
                uPay.innerHTML = payableList.map(d => renderItem(d, 'payable')).join('');
            } else {
                uPay.innerHTML = '<div class="empty" style="padding:40px 0;"><i class="fas fa-check-circle" style="color:#10b981;font-size:32px;margin-bottom:12px;"></i><div style="color:var(--text);">Semua hutang sudah lunas!</div></div>';
            }
        }

        const uRec = document.getElementById('utang-list-receivable');
        if (uRec) {
            if (receivableList.length > 0) {
                uRec.innerHTML = receivableList.map(d => renderItem(d, 'receivable')).join('');
            } else {
                uRec.innerHTML = '<div class="empty" style="padding:40px 0;"><i class="fas fa-box-open" style="font-size:28px;margin-bottom:12px;"></i><div>Tidak ada piutang aktif.</div></div>';
            }
        }

        // 3. Update Insight
        const insightBox = document.getElementById('utang-smart-insight');
        if (insightBox) {
            if (totalPayable === 0 && totalReceivable === 0) {
                insightBox.innerHTML = "Selamat! Kamu tidak memiliki hutang maupun piutang aktif saat ini. Keuanganmu sangat sehat.";
            } else if (totalPayable > 0 && totalReceivable === 0) {
                const biggest = payableList.sort((a,b) => b.remaining - a.remaining)[0];
                insightBox.innerHTML = `Fokus lunasi <strong>${biggest.debt_name}</strong> terlebih dahulu karena memiliki sisa terbesar (${rp(biggest.remaining)}). Tetap konsisten menyisihkan budget setiap bulan!`;
            } else if (totalReceivable > totalPayable) {
                insightBox.innerHTML = `Kondisi sangat baik! Total piutangmu (${rp(totalReceivable)}) lebih besar dari hutang (${rp(totalPayable)}). Pastikan kamu mem-follow up piutang yang sudah mendekati jatuh tempo.`;
            } else {
                insightBox.innerHTML = `Kamu memiliki kewajiban hutang sebesar <strong>${rp(totalPayable)}</strong>. Coba alokasikan sebagian dari penagihan piutangmu (${rp(totalReceivable)}) untuk mempercepat pelunasan.`;
            }
        }
    }

    async function submitDebt() {
        const name  = document.getElementById('debt-name').value.trim();
        const type  = document.getElementById('debt-type').value;
        const total = parseFloat(document.getElementById('debt-total').value);
        const due   = document.getElementById('debt-due').value;
        const notes = document.getElementById('debt-notes').value.trim();
        try {
            await CashbookService.addDebt({ debt_name: name, debt_type: type, total_amount: total, paid_amount: 0, due_date: due || null, notes: notes || null });
            toast('Utang berhasil ditambahkan!', 's');
            closeModal('modal-add-debt');
            document.getElementById('debt-name').value = '';
            document.getElementById('debt-total').value = '';
            document.getElementById('debt-due').value = '';
            document.getElementById('debt-notes').value = '';
            await refreshUI();
        } catch(e) { toast(e.message || 'Gagal menyimpan', 'e'); }
    }

    function openPayInstallment(debtId, debtName, remaining, debtType) {
        const isPayable = debtType === 'payable';
        document.getElementById('inst-debt-id').value = debtId;

        // Adapt modal title and label based on debt type
        const titleEl = document.querySelector('#modal-pay-installment .modal-title');
        if (titleEl) titleEl.innerHTML = isPayable
            ? '<i class="fas fa-money-bill-wave" style="color:var(--accent);margin-right:8px"></i>Bayar Cicilan'
            : '<i class="fas fa-arrow-down-to-line" style="color:var(--info);margin-right:8px"></i>Catat Penerimaan Piutang';

        const accLabelEl = document.querySelector('label[for="inst-account-id"], #modal-pay-installment .flabel');
        if (accLabelEl) accLabelEl.textContent = isPayable ? 'Bayar dari Akun' : 'Masuk ke Akun';

        document.getElementById('inst-debt-name-display').textContent =
            (isPayable ? 'Membayar cicilan untuk: ' : 'Mencatat penerimaan piutang: ') + debtName;
        document.getElementById('inst-debt-remaining-display').textContent =
            (isPayable ? 'Sisa utang: ' : 'Sisa piutang: ') + rp(remaining);

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('inst-date').value = today;
        document.getElementById('inst-amount').value = '';
        document.getElementById('inst-notes').value = '';
        // Store debt type for submitInstallment
        document.getElementById('inst-debt-id').dataset.debtType = debtType;

        // Populate account dropdown from global accounts array
        const sel = document.getElementById('inst-account-id');
        const balEl = document.getElementById('inst-account-balance');
        sel.innerHTML = '<option value="">-- Pilih akun --</option>';
        (accounts || []).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.name + ' (' + rp(a.balance_cached || 0) + ')';
            sel.appendChild(opt);
        });

        // Show balance hint on change + real-time validation (only relevant for payable)
        const amountInput = document.getElementById('inst-amount');
        const payBtn = document.querySelector('#modal-pay-installment .btn-accent');

        function checkBalance() {
            const acc = (accounts || []).find(a => String(a.id) === String(sel.value));
            const bal = acc ? parseFloat(acc.balance_cached || 0) : null;
            const amt = parseFloat(amountInput.value) || 0;
            if (acc) {
                if (isPayable && bal < amt && amt > 0) {
                    balEl.innerHTML = `<span style="color:var(--danger);"><i class="fas fa-exclamation-circle"></i> Saldo tidak cukup! Saldo: ${rp(bal)}</span>`;
                    if (payBtn) payBtn.disabled = true;
                } else {
                    const hint = isPayable ? 'Saldo tersedia: ' : 'Saldo saat ini: ';
                    balEl.innerHTML = `<span style="color:var(--text-muted);">${hint}${rp(bal)}</span>`;
                    if (payBtn) payBtn.disabled = false;
                }
            } else {
                balEl.textContent = '';
                if (payBtn) payBtn.disabled = false;
            }
        }

        sel.onchange = checkBalance;
        amountInput.oninput = checkBalance;

        // Pre-select first active account and run check
        if (sel.options.length > 1) { sel.selectedIndex = 1; checkBalance(); }

        openModal('modal-pay-installment');
    }

    async function submitInstallment() {
        const debtIdEl = document.getElementById('inst-debt-id');
        const debtId   = debtIdEl.value;
        const accountId = document.getElementById('inst-account-id').value;
        const amount   = parseFloat(document.getElementById('inst-amount').value);
        const date     = document.getElementById('inst-date').value;
        const notes    = document.getElementById('inst-notes').value.trim();
        try {
            await CashbookService.payDebt(debtId, amount, accountId, date, notes);
            const debtType = debtIdEl.dataset.debtType || 'payable';
            toast(debtType === 'payable' ? 'Cicilan berhasil dibayar!' : 'Penerimaan piutang dicatat!', 's');
            closeModal('modal-pay-installment');
            await refreshUI();
        } catch(e) { toast(e.message || 'Gagal menyimpan cicilan', 'e'); }
    }

    async function deleteDebt(debtId, debtName) {
        confirmDialog(
            'Hapus Utang',
            `Data utang "${debtName}" beserta riwayat cicilannya akan dihapus. Lanjutkan?`,
            'danger',
            async () => {
                try {
                    await CashbookService.deleteDebt(String(debtId));
                    toast('Utang berhasil dihapus');
                    await refreshUI();
                } catch(e) { toast(e.message || 'Gagal menghapus utang', 'e'); }
            }
        );
    }


    // === App Init ===
    document.addEventListener('DOMContentLoaded', async () => {
        // Init theme
        const t = localStorage.getItem('cb_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);

        // Load master data (accounts + categories + tx + dashboard) using local DB
        await loadMaster();
        
        allTxList = await localAPI.transactions.getAll();
        
        // Populate category relation for UI compatibility with old structure
        allTxList = allTxList.map(tx => {
            const cat = categories.find(c => c.id === tx.category_id) || null;
            return { ...tx, category: cat };
        });
        
        // Sort by date descending
        allTxList.sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date));
        
        await loadDashboard();

        // Set month defaults
        const now = new Date();
        const monthStr = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}`;
        const angPicker = document.getElementById('ang-filter-month');
        if (angPicker && !angPicker.value) angPicker.value = monthStr;

        // Rollover state
        const rolloverEl = document.getElementById('ang-rollover-toggle');
        if (rolloverEl) {
            rolloverEl.checked = rolloverEnabled;
            if (rolloverEnabled) {
                const label = document.getElementById('ang-rollover-label');
                const val   = document.getElementById('ang-val-rollover');
                if (label) { label.textContent = 'Aktif'; label.style.color = 'var(--accent)'; }
                if (val)   { val.textContent = 'On'; val.style.color = 'var(--accent)'; }
            }
        }

        // Show overview tab
        switchMainTab('overview');

        // ==== Auto Backup Logic ====
        try {
            const metaSettings = await localAPI.meta.getAll();
            const autoEnabled = metaSettings.find(m => m.key === 'auto_backup_enabled')?.value === 'true';
            const intervalDays = parseInt(metaSettings.find(m => m.key === 'backup_interval_days')?.value || '7');
            const lastBackupStr = metaSettings.find(m => m.key === 'last_backup_at')?.value;
            
            if (autoEnabled) {
                const now = new Date();
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
                    // Gracefully handle stores that may not exist yet
                    let installments = [];
                    try { installments = await localAPI.installments.getAll(); } catch(_) {}

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
                    a.download = `cuan_cashbook_autobackup_${now.toISOString().slice(0,10)}.json`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    
                    // Update last backup time
                    await localAPI.meta.set('last_backup_at', now.toISOString());
                    console.log('Auto backup completed successfully');
                }
            }
        } catch(e) {
            console.error('Failed to run auto backup', e);
        }
    });

