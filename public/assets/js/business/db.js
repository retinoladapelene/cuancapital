/**
 * Business Manager — db.js
 * IndexedDB Core: 12 stores, UUID keygen, versioned migrations
 * Local-First: semua data bisnis disimpan di browser, bukan server
 */

const BIZ_DB_NAME = 'CuanBusinessDB';
const BIZ_DB_VERSION = 1;

// ── UUID Generator ──────────────────────────────────────────────────────────
function bizUUID() {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) return crypto.randomUUID();
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0;
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
}
window.bizUUID = bizUUID;

// ── Format Currency ──────────────────────────────────────────────────────────
function bizRp(v) {
    if (v === null || v === undefined) return 'Rp 0';
    const num = parseFloat(v) || 0;
    if (num >= 1_000_000_000) return 'Rp ' + (num / 1_000_000_000).toFixed(1).replace('.0', '') + 'M';
    if (num >= 1_000_000) return 'Rp ' + (num / 1_000_000).toFixed(1).replace('.0', '') + 'jt';
    if (num >= 1_000) return 'Rp ' + (num / 1_000).toFixed(0) + 'rb';
    return 'Rp ' + num.toLocaleString('id-ID');
}
function bizRpFull(v) {
    if (v === null || v === undefined) return 'Rp 0';
    return 'Rp ' + parseFloat(v).toLocaleString('id-ID');
}
window.bizRp = bizRp;
window.bizRpFull = bizRpFull;

// ── Today / date helpers ─────────────────────────────────────────────────────
function bizToday() { return new Date().toISOString().split('T')[0]; }
function bizMonthKey() { return new Date().toISOString().slice(0, 7); }
window.bizToday = bizToday;
window.bizMonthKey = bizMonthKey;

// ── BizDB — IndexedDB Wrapper ────────────────────────────────────────────────
const BizDB = {
    db: null,

    async init() {
        if (this.db) return this.db;
        return new Promise((resolve, reject) => {
            const req = (window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB).open(BIZ_DB_NAME, BIZ_DB_VERSION);

            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                const old = e.oldVersion;

                /* ── Version 1: Create all 12 stores ── */
                if (old < 1) {
                    // meta — app settings (lastBackup, activeBusinessId, …)
                    if (!db.objectStoreNames.contains('meta'))
                        db.createObjectStore('meta', { keyPath: 'key' });

                    // businesses — user can have 1+ businesses
                    if (!db.objectStoreNames.contains('businesses'))
                        db.createObjectStore('businesses', { keyPath: 'id' });

                    // product_categories
                    if (!db.objectStoreNames.contains('product_categories')) {
                        const s = db.createObjectStore('product_categories', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                    }

                    // products
                    if (!db.objectStoreNames.contains('products')) {
                        const s = db.createObjectStore('products', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                        s.createIndex('type', 'type', { unique: false });
                        s.createIndex('is_active', 'is_active', { unique: false });
                    }

                    // product_ingredients (for HPP breakdown)
                    if (!db.objectStoreNames.contains('product_ingredients')) {
                        const s = db.createObjectStore('product_ingredients', { keyPath: 'id' });
                        s.createIndex('product_id', 'product_id', { unique: false });
                    }

                    // customers
                    if (!db.objectStoreNames.contains('customers')) {
                        const s = db.createObjectStore('customers', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                    }

                    // sales (header)
                    if (!db.objectStoreNames.contains('sales')) {
                        const s = db.createObjectStore('sales', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                        s.createIndex('sale_date', 'sale_date', { unique: false });
                    }

                    // sale_items
                    if (!db.objectStoreNames.contains('sale_items')) {
                        const s = db.createObjectStore('sale_items', { keyPath: 'id' });
                        s.createIndex('sale_id', 'sale_id', { unique: false });
                        s.createIndex('product_id', 'product_id', { unique: false });
                    }

                    // expense_categories
                    if (!db.objectStoreNames.contains('expense_categories')) {
                        const s = db.createObjectStore('expense_categories', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                    }

                    // expenses
                    if (!db.objectStoreNames.contains('expenses')) {
                        const s = db.createObjectStore('expenses', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                        s.createIndex('expense_date', 'expense_date', { unique: false });
                    }

                    // inv_movements (inventory movement log)
                    if (!db.objectStoreNames.contains('inv_movements')) {
                        const s = db.createObjectStore('inv_movements', { keyPath: 'id' });
                        s.createIndex('product_id', 'product_id', { unique: false });
                        s.createIndex('business_id', 'business_id', { unique: false });
                    }

                    // fin_snapshots (pre-aggregated daily metrics — used by dashboard)
                    if (!db.objectStoreNames.contains('fin_snapshots')) {
                        const s = db.createObjectStore('fin_snapshots', { keyPath: 'id' });
                        s.createIndex('business_id', 'business_id', { unique: false });
                        s.createIndex('snapshot_date', 'snapshot_date', { unique: false });
                        s.createIndex('biz_date', 'biz_date', { unique: true }); // composite
                    }
                }
                /* ── Future migrations: if (old < 2) { ... } ── */
            };

            req.onsuccess = (e) => { this.db = e.target.result; resolve(this.db); };
            req.onerror = (e) => reject(e.target.error);
            req.onblocked = () => console.warn('[BizDB] IndexedDB upgrade blocked by open tab');
        });
    },

    // ── Generic op runner ───────────────────────────────────────────────────
    async _op(storeName, mode, callback) {
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
            tx.onabort = () => reject(new Error('Transaction aborted'));
        });
    },

    // ── Multi-op in one transaction ─────────────────────────────────────────
    async _multiOp(storeNames, mode, callback) {
        if (!this.db) await this.init();
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(storeNames, mode);
            const stores = {};
            storeNames.forEach(n => { stores[n] = tx.objectStore(n); });
            let result;
            try { result = callback(stores); } catch (e) { reject(e); }
            tx.oncomplete = () => resolve(result);
            tx.onerror = () => reject(tx.error);
            tx.onabort = () => reject(new Error('Multi-transaction aborted'));
        });
    },

    // ── Store factory ───────────────────────────────────────────────────────
    store(name) {
        return {
            getAll: () => BizDB._op(name, 'readonly', s => s.getAll()),
            getById: (id) => BizDB._op(name, 'readonly', s => s.get(id)),
            save: (obj) => BizDB._op(name, 'readwrite', s => s.put(obj)),
            delete: (id) => BizDB._op(name, 'readwrite', s => s.delete(id)),
            clear: () => BizDB._op(name, 'readwrite', s => s.clear()),
            getByIndex: (idx, val) => BizDB._op(name, 'readonly', s => {
                const results = [];
                const range = IDBKeyRange.only(val);
                const req = s.index(idx).openCursor(range);
                req.onsuccess = e => {
                    const c = e.target.result;
                    if (c) { results.push(c.value); c.continue(); }
                    else { req._results = results; }
                };
                return {
                    get result() { return req._results || results; },
                    set onsuccess(fn) { const orig = req.onsuccess; req.onsuccess = e => { orig(e); if (!e.target.result) fn({ target: { result: results } }); }; },
                    onerror: null
                };
            }),
        };
    },

    // ── Meta helper ─────────────────────────────────────────────────────────
    meta: {
        get: (key) => BizDB._op('meta', 'readonly', s => s.get(key)).then(r => r ? r.value : null),
        set: (key, val) => BizDB._op('meta', 'readwrite', s => s.put({ key, value: val })),
        getAll: () => BizDB._op('meta', 'readonly', s => s.getAll()),
    },
};

// ── Convenience stores ───────────────────────────────────────────────────────
BizDB.businesses = BizDB.store('businesses');
BizDB.productCats = BizDB.store('product_categories');
BizDB.products = BizDB.store('products');
BizDB.ingredients = BizDB.store('product_ingredients');
BizDB.customers = BizDB.store('customers');
BizDB.sales = BizDB.store('sales');
BizDB.saleItems = BizDB.store('sale_items');
BizDB.expenseCats = BizDB.store('expense_categories');
BizDB.expenses = BizDB.store('expenses');
BizDB.invMovements = BizDB.store('inv_movements');
BizDB.finSnapshots = BizDB.store('fin_snapshots');

window.BizDB = BizDB;

// ── Active Business Helper ───────────────────────────────────────────────────
const BizSession = {
    _bizId: null,

    async getBusinessId() {
        if (this._bizId) return this._bizId;
        const saved = await BizDB.meta.get('active_business_id');
        if (saved) { this._bizId = saved; return saved; }
        return null;
    },

    async ensureBusiness() {
        let bizId = await this.getBusinessId();
        if (bizId) return bizId;

        // First run — create default business
        bizId = bizUUID();
        const biz = {
            id: bizId,
            name: 'Bisnis Saya',
            business_type: 'retail',
            currency: 'IDR',
            is_active: true,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };
        await BizDB.businesses.save(biz);
        await BizDB.meta.set('active_business_id', bizId);
        this._bizId = bizId;

        // Seed default expense categories
        const defaultExpCats = ['Marketing', 'Operasional', 'Gaji', 'Tools', 'Lainnya'];
        for (const name of defaultExpCats) {
            await BizDB.expenseCats.save({ id: bizUUID(), business_id: bizId, name, created_at: new Date().toISOString() });
        }

        return bizId;
    },

    clear() { this._bizId = null; },
};
window.BizSession = BizSession;

// ── Financial Snapshot Upsert ────────────────────────────────────────────────
async function bizUpsertSnapshot(businessId, date, delta) {
    // delta: { revenue: +N, expenses: +N, profit: +N, orders_count: +1 }
    const snapId = businessId + '_' + date;
    const all = await BizDB.finSnapshots.getAll();
    let snap = all.find(s => s.biz_date === snapId);

    if (!snap) {
        snap = {
            id: bizUUID(), business_id: businessId, snapshot_date: date,
            biz_date: snapId, revenue: 0, expenses: 0, profit: 0, orders_count: 0,
            updated_at: new Date().toISOString()
        };
    }

    snap.revenue = (snap.revenue || 0) + (delta.revenue || 0);
    snap.expenses = (snap.expenses || 0) + (delta.expenses || 0);
    snap.profit = (snap.profit || 0) + (delta.profit || 0);
    snap.orders_count = (snap.orders_count || 0) + (delta.orders_count || 0);
    snap.updated_at = new Date().toISOString();

    await BizDB.finSnapshots.save(snap);
    return snap;
}
window.bizUpsertSnapshot = bizUpsertSnapshot;

// ── Snapshot Rebuild Engine (used during JSON Import) ─────────────────────────
async function bizRebuildSnapshots(businessId) {
    if (!businessId) return;

    // 1. Fetch raw transaction facts
    const allSales = await BizDB.sales.getAll();
    const allExp = await BizDB.expenses.getAll();

    // Filter by business
    const sales = allSales.filter(s => s.business_id === businessId);
    const exps = allExp.filter(e => e.business_id === businessId);

    // 2. Clear old snapshots to start fresh
    const allSnaps = await BizDB.finSnapshots.getAll();
    const mySnaps = allSnaps.filter(s => s.business_id === businessId);
    for (const snap of mySnaps) {
        await BizDB.finSnapshots.delete(snap.id);
    }

    // 3. Aggregate Data processing
    const dailyData = {};

    sales.forEach(s => {
        const date = (s.sale_date || s.created_at).split('T')[0];
        if (!dailyData[date]) dailyData[date] = { r: 0, p: 0, o: 0, e: 0 };
        dailyData[date].r += (s.total_amount || 0);
        dailyData[date].p += (s.total_profit || 0);
        dailyData[date].o += 1;
    });

    exps.forEach(e => {
        const date = (e.expense_date || e.created_at).split('T')[0];
        if (!dailyData[date]) dailyData[date] = { r: 0, p: 0, o: 0, e: 0 };
        dailyData[date].e += (e.amount || 0);
    });

    // 4. Re-insert exact snapshots
    const newSnaps = Object.keys(dailyData).map(date => ({
        id: bizUUID(),
        business_id: businessId,
        snapshot_date: date,
        biz_date: businessId + '_' + date,
        revenue: dailyData[date].r,
        profit: dailyData[date].p,
        expenses: dailyData[date].e,
        orders_count: dailyData[date].o,
        updated_at: new Date().toISOString()
    }));

    for (const snap of newSnaps) {
        await BizDB.finSnapshots.save(snap);
    }

    console.log(`[Rebuild Engine] Recreated ${newSnaps.length} exact daily snapshots for business ${businessId}`);
}
window.bizRebuildSnapshots = bizRebuildSnapshots;

// ── CreateSale Action ─────────────────────────────────────────────────────────
// Orchestrates: sale + sale_items + stock update + inv_movements + fin_snapshot
async function bizCreateSale({ businessId, cartItems, paymentMethod, notes, customerId, customerName, date }) {
    if (!cartItems || !cartItems.length) throw new Error('Keranjang kosong');

    const saleDate = date || bizToday();
    const saleId = bizUUID();
    let totalAmount = 0;
    let totalHpp = 0;
    let totalProfit = 0;

    // Pre-validate stock
    const products = await BizDB.products.getAll();
    for (const item of cartItems) {
        const p = products.find(pr => pr.id === item.product_id);
        if (!p) throw new Error(`Produk ${item.product_name || item.product_id} tidak ditemukan`);
        if (p.type === 'physical' && (p.stock || 0) < item.qty) {
            throw new Error(`Stok ${p.name} tidak cukup (tersisa ${p.stock})`);
        }
    }

    // Build sale_items
    const saleItemDocs = cartItems.map(item => {
        const subtotal = item.price * item.qty;
        const profit = (item.price - item.hpp) * item.qty;
        totalAmount += subtotal;
        totalHpp += item.hpp * item.qty;
        totalProfit += profit;
        return {
            id: bizUUID(),
            sale_id: saleId,
            product_id: item.product_id,
            product_name: item.product_name,  // snapshot
            price: item.price,          // snapshot
            hpp: item.hpp,            // snapshot
            quantity: item.qty,
            subtotal,
            profit,
            created_at: new Date().toISOString(),
        };
    });

    // Sale header
    const saleDOC = {
        id: saleId,
        business_id: businessId,
        customer_id: customerId || null,
        customer_name: customerName || null,
        sale_date: saleDate,
        total_amount: totalAmount,
        total_cost: totalHpp,
        total_profit: totalProfit,
        payment_method: paymentMethod || 'cash',
        status: 'completed',
        notes: notes || '',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
    };

    // Save sale & items
    await BizDB.sales.save(saleDOC);
    for (const si of saleItemDocs) await BizDB.saleItems.save(si);

    // Update stock + log inventory movements
    for (const item of cartItems) {
        const p = products.find(pr => pr.id === item.product_id);
        if (p && p.type === 'physical') {
            p.stock = Math.max(0, (p.stock || 0) - item.qty);
            p.updated_at = new Date().toISOString();
            await BizDB.products.save(p);

            await BizDB.invMovements.save({
                id: bizUUID(),
                business_id: businessId,
                product_id: item.product_id,
                type: 'out',
                quantity: -item.qty,
                stock_after: p.stock,
                reference_type: 'sale',
                reference_id: saleId,
                notes: `Sale #${saleId.slice(-6)}`,
                created_at: new Date().toISOString(),
            });
        }
    }

    // Update financial snapshot
    await bizUpsertSnapshot(businessId, saleDate, {
        revenue: totalAmount,
        expenses: 0,
        profit: totalProfit,
        orders_count: 1,
    });

    // Remember last sale for "Repeat Last" feature
    localStorage.setItem('biz_last_sale', JSON.stringify({
        items: cartItems.map(i => ({ product_id: i.product_id, product_name: i.product_name, qty: i.qty, price: i.price, hpp: i.hpp })),
        payment_method: paymentMethod || 'cash',
        date: saleDate,
    }));

    if (typeof bizClearIntelligenceCache === 'function') {
        bizClearIntelligenceCache();
    }

    return saleDOC;
}
window.bizCreateSale = bizCreateSale;

// ── CreateExpense Action ──────────────────────────────────────────────────────
async function bizCreateExpense({ businessId, categoryId, categoryName, amount, expenseDate, notes }) {
    const expenseDoc = {
        id: bizUUID(),
        business_id: businessId,
        category_id: categoryId || null,
        category_name: categoryName || 'Lainnya',
        amount: parseFloat(amount),
        expense_date: expenseDate || bizToday(),
        notes: notes || '',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
    };
    await BizDB.expenses.save(expenseDoc);

    // Update snapshot
    await bizUpsertSnapshot(businessId, expenseDoc.expense_date, {
        revenue: 0,
        expenses: expenseDoc.amount,
        profit: -expenseDoc.amount,
        orders_count: 0,
    });

    return expenseDoc;
}
window.bizCreateExpense = bizCreateExpense;

// ── Snapshot Rebuild Engine ───────────────────────────────────────────────────
// Recalculates all daily snapshots from raw Sales and Expenses for a given business
async function bizRebuildSnapshots(businessId) {
    if (!businessId) return;

    // 1. Delete existing snapshots for this business
    const existing = await BizDB.finSnapshots.getAll();
    const toDelete = existing.filter(s => s.business_id === businessId);
    for (const snap of toDelete) {
        await BizDB.finSnapshots.delete(snap.id);
    }

    // 2. Fetch raw data
    const [allSales, allExp] = await Promise.all([
        BizDB.sales.getAll(),
        BizDB.expenses.getAll()
    ]);
    const bizSales = allSales.filter(s => s.business_id === businessId && s.status === 'completed');
    const bizExp = allExp.filter(e => e.business_id === businessId);

    const dailyMap = {}; // { 'YYYY-MM-DD': { revenue, expenses, profit, orders } }

    const ensureDay = (d) => {
        if (!dailyMap[d]) dailyMap[d] = { revenue: 0, expenses: 0, profit: 0, orders: 0 };
    };

    // 3. Aggregate Sales
    bizSales.forEach(s => {
        const d = s.sale_date;
        if (!d) return;
        ensureDay(d);
        dailyMap[d].revenue += (s.total_amount || 0);
        dailyMap[d].profit += (s.total_profit || 0);
        dailyMap[d].orders += 1;
    });

    // 4. Aggregate Expenses
    bizExp.forEach(e => {
        const d = e.expense_date;
        if (!d) return;
        ensureDay(d);
        dailyMap[d].expenses += (e.amount || 0);
        dailyMap[d].profit -= (e.amount || 0); // expense reduces net profit
    });

    // 5. Save back to IndexedDB
    for (const dateKey of Object.keys(dailyMap)) {
        const day = dailyMap[dateKey];
        const snap = {
            id: bizUUID(),
            business_id: businessId,
            snapshot_date: dateKey,
            biz_date: businessId + '_' + dateKey,
            revenue: day.revenue,
            expenses: day.expenses,
            profit: day.profit,
            orders_count: day.orders,
            updated_at: new Date().toISOString()
        };
        await BizDB.finSnapshots.save(snap);
    }

    // Clear caches to force UI re-render with fresh data
    if (typeof bizClearIntelligenceCache === 'function') {
        bizClearIntelligenceCache();
    }
}
window.bizRebuildSnapshots = bizRebuildSnapshots;
