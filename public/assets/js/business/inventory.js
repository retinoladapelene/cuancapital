/**
 * Business Manager — inventory.js
 * Stock list, low stock alerts, restock modal, movement history
 */

async function bizLoadInventory() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    const products = await BizDB.products.getAll();
    const physical = products.filter(p => p.type === 'physical' && p.is_active !== false);
    const low = physical.filter(p => p.stock <= (p.low_stock_alert || 5) && p.stock > 0);
    const out = physical.filter(p => (p.stock || 0) <= 0);

    container.innerHTML = `<div class="biz-page">
        <div class="biz-card-header" style="margin-bottom:14px">
            <div style="font-size:15px;font-weight:800">Inventaris Stok</div>
        </div>

        ${out.length ? `<div class="biz-low-stock-alert" style="background:var(--biz-danger-bg);border-color:rgba(239,68,68,0.3);color:var(--biz-danger)">
            <i class="fas fa-circle-xmark"></i> ${out.length} produk HABIS: ${out.map(p => _esc(p.name)).join(', ')}
        </div>` : ''}

        ${low.length ? `<div class="biz-low-stock-alert">
            <i class="fas fa-triangle-exclamation"></i> ${low.length} produk stok rendah: ${low.map(p => _esc(p.name)).join(', ')}
        </div>` : ''}

        <div class="biz-search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="inv-search" placeholder="Cari produk..." oninput="invFilter(this.value)">
        </div>

        <div id="inv-list"></div>
    </div>`;

    window._invData = physical.sort((a, b) => (a.stock || 0) - (b.stock || 0));
    invFilter('');
}

function invFilter(q) {
    const el = document.getElementById('inv-list');
    if (!el) return;
    const list = (window._invData || []).filter(p => !q || p.name.toLowerCase().includes(q.toLowerCase()));

    if (!list.length) { el.innerHTML = '<div class="biz-empty"><i class="fas fa-warehouse"></i><br>Tidak ada produk fisik</div>'; return; }

    el.innerHTML = list.map(p => {
        const cls = (p.stock || 0) <= 0 ? 'out' : (p.stock || 0) <= (p.low_stock_alert || 5) ? 'low' : 'ok';
        const ico = cls === 'out' ? 'fa-circle-xmark' : cls === 'low' ? 'fa-triangle-exclamation' : 'fa-circle-check';
        return `<div class="biz-list-item">
            <div class="biz-list-icon" style="background:${cls === 'ok' ? 'var(--biz-success-bg)' : cls === 'low' ? 'var(--biz-warning-bg)' : 'var(--biz-danger-bg)'}">
                <i class="fas ${ico}" style="color:var(--biz-${cls === 'ok' ? 'success' : cls === 'low' ? 'warning' : 'danger'})"></i>
            </div>
            <div class="biz-list-body">
                <div class="biz-list-name">${_esc(p.name)}</div>
                <div class="biz-list-sub">Alert: stok ≤ ${p.low_stock_alert || 5}</div>
            </div>
            <div class="biz-list-right">
                <div style="font-size:18px;font-weight:900;color:var(--biz-${cls === 'ok' ? 'success' : cls === 'low' ? 'warning' : 'danger'})">${p.stock || 0}</div>
                <div class="biz-list-date">unit</div>
            </div>
            <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="bizOpenRestock('${p.id}','${_esc(p.name)}','${p.stock || 0}')" style="margin-left:8px">
                <i class="fas fa-plus"></i>
            </button>
        </div>`;
    }).join('');
}

function bizOpenRestock(productId, name, currentStock) {
    document.getElementById('restock-product-id').value = productId;
    document.getElementById('restock-product-name').textContent = `${name} — Stok saat ini: ${currentStock}`;
    document.getElementById('restock-qty').value = '';
    document.getElementById('restock-notes').value = '';
    bizOpenModal('biz-modal-restock');
    setTimeout(() => document.getElementById('restock-qty')?.focus(), 150);
}

async function bizSaveRestock() {
    const productId = document.getElementById('restock-product-id').value;
    const qty = parseInt(document.getElementById('restock-qty').value) || 0;
    const notes = document.getElementById('restock-notes').value.trim();
    if (qty <= 0) { bizToast('Qty harus > 0', 'w'); return; }

    const products = await BizDB.products.getAll();
    const p = products.find(pr => pr.id === productId);
    if (!p) { bizToast('Produk tidak ditemukan', 'e'); return; }

    p.stock = (p.stock || 0) + qty;
    p.updated_at = new Date().toISOString();
    await BizDB.products.save(p);

    await BizDB.invMovements.save({
        id: bizUUID(),
        business_id: window.bizState.businessId,
        product_id: productId,
        type: 'in',
        quantity: qty,
        stock_after: p.stock,
        reference_type: 'restock',
        reference_id: null,
        notes: notes || 'Restock manual',
        created_at: new Date().toISOString(),
    });

    await bizPreloadProducts();
    bizToast(`✅ Stok ${p.name} +${qty} → ${p.stock}`, 's');
    bizCloseModal('biz-modal-restock');
    await bizLoadInventory();
}

window.bizLoadInventory = bizLoadInventory;
window.invFilter = invFilter;
window.bizOpenRestock = bizOpenRestock;
window.bizSaveRestock = bizSaveRestock;
