/**
 * Business Manager — backup.js
 * Export/Import JSON backup, schema versioning, 7-day backup reminder
 */

const BIZ_BACKUP_SCHEMA_VERSION = 1;
const BIZ_BACKUP_APP = 'CuanCapital-Business';

// ── Export ────────────────────────────────────────────────────────────────────
async function bizExportData() {
    const btn = document.querySelector('[onclick="bizExportData()"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...'; }

    try {
        const [businesses, productCats, products, ingredients, customers,
            sales, saleItems, expenseCats, expenses, invMovements, finSnapshots, meta]
            = await Promise.all([
                BizDB.businesses.getAll(),
                BizDB.productCats.getAll(),
                BizDB.products.getAll(),
                BizDB.ingredients.getAll(),
                BizDB.customers.getAll(),
                BizDB.sales.getAll(),
                BizDB.saleItems.getAll(),
                BizDB.expenseCats.getAll(),
                BizDB.expenses.getAll(),
                BizDB.invMovements.getAll(),
                BizDB.finSnapshots.getAll(),
                BizDB.meta.getAll(),
            ]);

        const backup = {
            version: BIZ_BACKUP_SCHEMA_VERSION,
            app: BIZ_BACKUP_APP,
            exported_at: new Date().toISOString(),
            data: {
                businesses, product_categories: productCats, products,
                product_ingredients: ingredients, customers, sales,
                sale_items: saleItems, expense_categories: expenseCats,
                expenses, inv_movements: invMovements, fin_snapshots: finSnapshots,
            },
        };

        const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        const today = new Date().toISOString().slice(0, 10);
        a.href = url;
        a.download = `cuancapital-business-backup-${today}.json`;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        URL.revokeObjectURL(url);
        document.body.removeChild(a);

        // Mark last backup
        await BizDB.meta.set('last_backup_at', new Date().toISOString());
        localStorage.setItem('biz_last_backup', Date.now().toString());

        bizToast('✅ Backup berhasil diunduh', 's');
        _updateBackupInfo();

        bizClearIntelligenceCache?.();
    } catch (e) {
        console.error('[Backup] Export error:', e);
        bizToast('Gagal export: ' + e.message, 'e');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-cloud-arrow-down"></i> Export Backup (JSON)'; }
    }
}
window.bizExportData = bizExportData;

// ── Import ────────────────────────────────────────────────────────────────────
async function bizImportData(input) {
    const file = input?.files?.[0];
    if (!file) return;
    input.value = '';   // reset so same file can be re-imported

    bizConfirm(
        'Restore Data Bisnis',
        'Ini akan MENGGANTIKAN semua data bisnis yang ada. Pastikan kamu sudah backup dulu. Yakin lanjutkan?',
        async () => {
            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    const parsed = JSON.parse(e.target.result);
                    _validateBackup(parsed);

                    // Clear all stores first
                    const stores = ['businesses', 'product_categories', 'products', 'product_ingredients',
                        'customers', 'sales', 'sale_items', 'expense_categories',
                        'expenses', 'inv_movements', 'fin_snapshots'];
                    for (const s of stores) await BizDB.store(s).clear();

                    // Write all records
                    const d = parsed.data;
                    for (const biz of (d.businesses || [])) await BizDB.businesses.save(biz);
                    for (const c of (d.product_categories || [])) await BizDB.productCats.save(c);
                    for (const p of (d.products || [])) await BizDB.products.save(p);
                    for (const ing of (d.product_ingredients || [])) await BizDB.ingredients.save(ing);
                    for (const cu of (d.customers || [])) await BizDB.customers.save(cu);
                    for (const sa of (d.sales || [])) await BizDB.sales.save(sa);
                    for (const si of (d.sale_items || [])) await BizDB.saleItems.save(si);
                    for (const ec of (d.expense_categories || [])) await BizDB.expenseCats.save(ec);
                    for (const ex of (d.expenses || [])) await BizDB.expenses.save(ex);
                    for (const im of (d.inv_movements || [])) await BizDB.invMovements.save(im);
                    for (const fs of (d.fin_snapshots || [])) await BizDB.finSnapshots.save(fs);

                    // Restore active business
                    if (d.businesses?.[0]) {
                        await BizDB.meta.set('active_business_id', d.businesses[0].id);
                        BizSession._bizId = d.businesses[0].id;
                        window.bizState.businessId = d.businesses[0].id;
                    }

                    await bizPreloadProducts?.();
                    bizClearIntelligenceCache?.();
                    bizToast('✅ Data berhasil di-restore', 's');
                    bizCloseModal('biz-modal-backup');

                    // Reload current module
                    setTimeout(() => {
                        BIZ_MODULES[window.bizState.activeTab].loaded = false;
                        bizSwitchTab(window.bizState.activeTab);
                    }, 500);

                } catch (err) {
                    console.error('[Backup] Import error:', err);
                    bizToast('Gagal import: ' + err.message, 'e');
                }
            };
            reader.readAsText(file);
        },
        'danger'
    );
}
window.bizImportData = bizImportData;

// ── Validation ────────────────────────────────────────────────────────────────
function _validateBackup(parsed) {
    if (!parsed || typeof parsed !== 'object') throw new Error('Format file tidak valid (bukan JSON)');
    if (!parsed.app || !parsed.data) throw new Error('Bukan file backup Business Manager');
    if (parsed.app !== BIZ_BACKUP_APP) throw new Error('File ini bukan backup Cuan Capital Business Manager');
    if (!parsed.version || parsed.version > BIZ_BACKUP_SCHEMA_VERSION)
        throw new Error(`File dari versi app lebih baru (v${parsed.version}). Update app dulu.`);
    if (!Array.isArray(parsed.data.businesses)) throw new Error('Data bisnis tidak valid di file backup');
    return true;
}

// ── Backup Reminder ───────────────────────────────────────────────────────────
function bizCheckBackupReminder(containerId) {
    const lastBackup = localStorage.getItem('biz_last_backup');
    if (!lastBackup) return;   // never backed up — don't pressure on first use

    const daysSince = (Date.now() - parseInt(lastBackup)) / (1000 * 60 * 60 * 24);
    if (daysSince < 7) return;

    const el = containerId ? document.getElementById(containerId) : null;
    const msg = `Sudah ${Math.floor(daysSince)} hari sejak backup terakhir.`;

    if (el) {
        el.style.display = 'block';
        el.innerHTML = `<div class="biz-backup-reminder">
            <i class="fas fa-shield-halved" style="font-size:18px"></i>
            <div class="biz-backup-reminder-text">${msg} Backup sekarang agar data aman.</div>
            <div style="display:flex;gap:6px">
                <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="bizOpenModal('biz-modal-backup')">Backup</button>
                <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="this.closest('.biz-backup-reminder').parentElement.style.display='none'">Nanti</button>
            </div>
        </div>`;
    }
}
window.bizCheckBackupReminder = bizCheckBackupReminder;

// ── Business Info Save ────────────────────────────────────────────────────────
async function bizSaveBusinessInfo() {
    const name = document.getElementById('biz-info-name')?.value.trim();
    const type = document.getElementById('biz-info-type')?.value;
    if (!name) { bizToast('Nama bisnis wajib diisi', 'w'); return; }

    const bizId = window.bizState.businessId;
    const all = await BizDB.businesses.getAll();
    const biz = all.find(b => b.id === bizId) || { id: bizId, created_at: new Date().toISOString() };
    biz.name = name;
    biz.business_type = type || 'retail';
    biz.updated_at = new Date().toISOString();
    await BizDB.businesses.save(biz);

    // Update sidebar brand
    const brand = document.querySelector('.biz-sidebar-brand-sub');
    if (brand) brand.textContent = name;

    bizToast('✅ Info bisnis disimpan', 's');
    bizCloseModal('biz-modal-business');
}
window.bizSaveBusinessInfo = bizSaveBusinessInfo;

// ── Update backup info in modal ───────────────────────────────────────────────
function _updateBackupInfo() {
    const el = document.getElementById('backup-last');
    if (!el) return;
    const last = localStorage.getItem('biz_last_backup');
    if (!last) { el.textContent = 'Belum pernah backup'; return; }
    const d = new Date(parseInt(last));
    el.textContent = `Backup terakhir: ${d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`;
}
// Run on modal open
document.addEventListener('click', e => {
    if (e.target.closest('[onclick*="biz-modal-backup"]')) setTimeout(_updateBackupInfo, 100);
}, { passive: true });
