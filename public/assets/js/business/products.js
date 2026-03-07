/**
 * Business Manager — products.js
 * Product list, Add/Edit form, HPP Calculator
 */

// ── HPP Calculator State ─────────────────────────────────────────────────────
let _hppRows = [];   // [{ name, cost, qty, unit }]
let _hppOpen = false;

function prodToggleHPP() {
    _hppOpen = !_hppOpen;
    document.getElementById('hpp-calc-panel').classList.toggle('open', _hppOpen);
    document.getElementById('hpp-toggle-lbl').textContent = _hppOpen ? 'Tutup HPP Calculator' : 'Buka HPP Calculator';
    if (_hppOpen && !_hppRows.length) hppAddRow();
}
function hppAddRow() {
    _hppRows.push({ name: '', cost: 0, qty: 1, unit: '' });
    hppRenderRows();
}
function hppRemoveRow(i) { _hppRows.splice(i, 1); hppRenderRows(); }
function hppUpdateRow(i, field, val) {
    if (_hppRows[i]) { _hppRows[i][field] = field === 'name' || field === 'unit' ? val : parseFloat(val) || 0; }
    hppCalcTotal();
}
function hppRenderRows() {
    const el = document.getElementById('hpp-ingredients-list');
    if (!el) return;
    el.innerHTML = _hppRows.map((r, i) => `
        <div class="biz-hpp-row">
            <input class="biz-input" style="font-size:12px;padding:6px 10px" placeholder="Nama bahan" value="${_esc(r.name)}"
                   oninput="hppUpdateRow(${i},'name',this.value)">
            <input class="biz-input" style="font-size:12px;padding:6px 10px;width:90px" type="number" min="0"
                   placeholder="Biaya" value="${r.cost || ''}" oninput="hppUpdateRow(${i},'cost',this.value)">
            <button class="biz-btn biz-btn-ghost biz-btn-sm" onclick="hppRemoveRow(${i})" style="padding:4px 8px">
                <i class="fas fa-xmark"></i>
            </button>
        </div>`).join('');
    hppCalcTotal();
}
function hppCalcTotal() {
    // 1. Calculate Raw Materials
    const totalMaterials = _hppRows.reduce((s, r) => s + (parseFloat(r.cost) || 0), 0);

    // 2. Read Yield & Overhead
    const yieldEl = document.getElementById('hpp-yield');
    const yieldQty = Math.max(1, parseInt(yieldEl ? yieldEl.value : 1) || 1);

    const overheadEl = document.getElementById('hpp-overhead');
    const overheadCost = parseFloat(overheadEl ? overheadEl.value : 0) || 0;

    // 3. Math Time
    const hppBahanPerPorsi = totalMaterials / yieldQty;
    const finalHpp = hppBahanPerPorsi + overheadCost;

    // 4. Update Displays
    const sb = document.getElementById('hpp-sub-bahan');
    if (sb) sb.textContent = `Rp ${bizRp(totalMaterials)} / ${yieldQty} = Rp ${bizRp(hppBahanPerPorsi)}`;

    const so = document.getElementById('hpp-sub-overhead');
    if (so) so.textContent = `Rp ${bizRp(overheadCost)}`;

    const el = document.getElementById('hpp-calc-total');
    if (el) el.textContent = bizRpFull(finalHpp);

    return finalHpp;
}
function hppApplyToProduct() {
    const total = hppCalcTotal();
    const inp = document.getElementById('prod-hpp');
    if (inp) { inp.value = total; prodUpdateMarginPreview(); }
    bizToast('HPP diterapkan ke produk', 'i');
}
function prodUpdateMarginPreview() {
    const price = parseFloat(document.getElementById('prod-price')?.value) || 0;
    const hpp = parseFloat(document.getElementById('prod-hpp')?.value) || 0;
    const prev = document.getElementById('prod-margin-preview');
    const pct = document.getElementById('prod-margin-pct');
    const pro = document.getElementById('prod-margin-profit');
    if (!prev) return;
    if (price > 0) {
        const margin = ((price - hpp) / price * 100);
        const profitUnit = price - hpp;
        prev.style.display = 'block';
        pct.textContent = margin.toFixed(1) + '%';
        pro.textContent = bizRpFull(profitUnit) + ' / unit';
        pct.style.color = margin >= 20 ? 'var(--biz-success)' : margin >= 10 ? 'var(--biz-warning)' : 'var(--biz-danger)';
    } else {
        prev.style.display = 'none';
    }
}
function prodTypeChange() {
    const type = document.getElementById('prod-type')?.value;
    const wrap = document.getElementById('prod-stock-wrap');
    if (wrap) wrap.style.display = type === 'physical' ? 'flex' : 'none';
}

window.prodToggleHPP = prodToggleHPP; window.hppAddRow = hppAddRow;
window.hppRemoveRow = hppRemoveRow; window.hppUpdateRow = hppUpdateRow;
window.hppApplyToProduct = hppApplyToProduct; window.hppCalcTotal = hppCalcTotal;
window.prodUpdateMarginPreview = prodUpdateMarginPreview; window.prodTypeChange = prodTypeChange;

// ── Image Upload Base64 Compression ──────────────────────────────────────────
function prodHandleImageUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    // File type validation
    if (!file.type.startsWith('image/')) {
        bizToast('Harap pilih file gambar (JPG/PNG)', 'w');
        input.value = '';
        return;
    }

    // Strict 2MB File Size Limit
    const maxSizeMB = 2;
    if (file.size > maxSizeMB * 1024 * 1024) {
        bizToast(`Ukuran file terlalu besar! Maksimal ${maxSizeMB}MB`, 'e');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;
            const MAX_SIZE = 500;

            if (width > height) {
                if (width > MAX_SIZE) { height *= MAX_SIZE / width; width = MAX_SIZE; }
            } else {
                if (height > MAX_SIZE) { width *= MAX_SIZE / height; height = MAX_SIZE; }
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.85); // 85% quality
            const base64Input = document.getElementById('prod-image-base64');
            const preview = document.getElementById('prod-image-preview');

            if (base64Input) base64Input.value = dataUrl;
            if (preview) {
                preview.style.backgroundImage = `url(${dataUrl})`;
                preview.innerHTML = '';
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}
window.prodHandleImageUpload = prodHandleImageUpload;

// ── Open Add Product Form ────────────────────────────────────────────────────
function bizOpenAddProduct() {
    document.getElementById('prod-id').value = '';
    document.getElementById('prod-name').value = '';
    document.getElementById('prod-category').value = '';
    document.getElementById('prod-price').value = '';
    document.getElementById('prod-hpp').value = '';
    document.getElementById('prod-stock').value = '0';
    document.getElementById('prod-low-alert').value = '5';
    document.getElementById('prod-type').value = 'physical';

    // Reset image
    const imgInput = document.getElementById('prod-image');
    if (imgInput) imgInput.value = '';
    const imgBase64 = document.getElementById('prod-image-base64');
    if (imgBase64) imgBase64.value = '';
    const imgPrev = document.getElementById('prod-image-preview');
    if (imgPrev) {
        imgPrev.style.backgroundImage = 'none';
        imgPrev.innerHTML = '<i class="fas fa-camera"></i>';
    }

    // Reset HPP state
    const ye = document.getElementById('hpp-yield'); if (ye) ye.value = 1;
    const oe = document.getElementById('hpp-overhead'); if (oe) oe.value = '';

    document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-box"></i> Tambah Produk';
    _hppRows = []; hppRenderRows();
    _hppOpen = false;
    document.getElementById('hpp-calc-panel').classList.remove('open');
    document.getElementById('hpp-toggle-lbl').textContent = 'Buka HPP Calculator';
    document.getElementById('prod-margin-preview').style.display = 'none';
    prodTypeChange();
    bizOpenModal('biz-modal-product');
    setTimeout(() => document.getElementById('prod-name')?.focus(), 150);
}
function bizOpenEditProduct(product) {
    document.getElementById('prod-id').value = product.id;
    document.getElementById('prod-name').value = product.name;
    document.getElementById('prod-category').value = product.category || '';
    document.getElementById('prod-price').value = product.price_sell;
    document.getElementById('prod-hpp').value = product.hpp || 0;
    document.getElementById('prod-stock').value = product.stock || 0;
    document.getElementById('prod-low-alert').value = product.low_stock_alert || 5;
    document.getElementById('prod-type').value = product.type || 'physical';
    document.getElementById('product-modal-title').innerHTML = '<i class="fas fa-pencil"></i> Edit Produk';
    prodTypeChange(); prodUpdateMarginPreview();

    // Load ingredients & config
    BizDB.ingredients.getAll().then(ings => {
        const productIngs = ings.filter(r => r.product_id === product.id);

        // Extract config rows if exist
        const yieldRow = productIngs.find(r => r.name === '__HPP_YIELD__');
        const yieldQty = yieldRow ? (yieldRow.quantity || 1) : 1;
        const ye = document.getElementById('hpp-yield'); if (ye) ye.value = yieldQty;

        const overheadRow = productIngs.find(r => r.name === '__HPP_OVERHEAD__');
        const overheadCost = overheadRow ? (overheadRow.cost || 0) : 0;
        const oe = document.getElementById('hpp-overhead'); if (oe) oe.value = overheadCost || '';

        // Load image
        const imgInput = document.getElementById('prod-image');
        if (imgInput) imgInput.value = ''; // Reset file input
        const imgBase64 = document.getElementById('prod-image-base64');
        const imgPrev = document.getElementById('prod-image-preview');

        if (product.image_data) {
            if (imgBase64) imgBase64.value = product.image_data;
            if (imgPrev) {
                imgPrev.style.backgroundImage = `url(${product.image_data})`;
                imgPrev.innerHTML = '';
            }
        } else {
            if (imgBase64) imgBase64.value = '';
            if (imgPrev) {
                imgPrev.style.backgroundImage = 'none';
                imgPrev.innerHTML = '<i class="fas fa-camera"></i>';
            }
        }

        // Normal rows
        _hppRows = productIngs.filter(r => !r.name.startsWith('__HPP')).map(r => ({ name: r.name, cost: r.cost, qty: r.quantity || 1, unit: r.unit || '' }));
        hppRenderRows(); // also triggers calc total
    });
    bizOpenModal('biz-modal-product');
}
window.bizOpenAddProduct = bizOpenAddProduct;
window.bizOpenEditProduct = bizOpenEditProduct;

// ── Save Product ─────────────────────────────────────────────────────────────
async function bizSaveProduct() {
    const id = document.getElementById('prod-id').value.trim();
    const name = document.getElementById('prod-name').value.trim();
    const category = document.getElementById('prod-category').value.trim() || 'Uncategorized';
    const price = parseFloat(document.getElementById('prod-price').value) || 0;
    const hpp = parseFloat(document.getElementById('prod-hpp').value) || 0;
    const type = document.getElementById('prod-type').value;
    if (!name) { bizToast('Nama produk wajib diisi', 'w'); return; }
    if (price <= 0) { bizToast('Harga jual harus > 0', 'w'); return; }

    const bizId = window.bizState.businessId;
    const now = new Date().toISOString();
    const prodId = id || bizUUID();
    const image_data = document.getElementById('prod-image-base64')?.value || null;

    const prodDoc = {
        id: prodId,
        business_id: bizId,
        name,
        category: document.getElementById('prod-category').value || 'Lainnya',
        price_sell: price,
        hpp,
        type,
        stock: type === 'physical' ? (parseInt(document.getElementById('prod-stock').value) || 0) : null,
        low_stock_alert: parseInt(document.getElementById('prod-low-alert').value) || 5,
        image_data,
        is_active: true,
        created_at: id ? undefined : now,
        updated_at: now,
    };
    if (!id) prodDoc.created_at = now;

    await BizDB.products.save(prodDoc);

    // Save ingredients & config
    const ye = document.getElementById('hpp-yield');
    const yieldQty = ye ? Math.max(1, parseInt(ye.value) || 1) : 1;

    const oe = document.getElementById('hpp-overhead');
    const overheadCost = oe ? (parseFloat(oe.value) || 0) : 0;

    // Remove old ingredients/config for this product
    const existing = await BizDB.ingredients.getAll();
    for (const old of existing.filter(r => r.product_id === prodId)) await BizDB.ingredients.delete(old.id);

    // Save normal ingredients
    for (const row of _hppRows.filter(r => r.name)) {
        await BizDB.ingredients.save({ id: bizUUID(), product_id: prodId, name: row.name, cost: row.cost, quantity: row.qty || 1, unit: row.unit || '', created_at: now });
    }

    // Save config as special ingredients if we have an HPP calculation
    if (_hppRows.length > 0) {
        if (yieldQty !== 1) await BizDB.ingredients.save({ id: bizUUID(), product_id: prodId, name: '__HPP_YIELD__', cost: 0, quantity: yieldQty, unit: 'porsi', created_at: now });
        if (overheadCost > 0) await BizDB.ingredients.save({ id: bizUUID(), product_id: prodId, name: '__HPP_OVERHEAD__', cost: overheadCost, quantity: 1, unit: '', created_at: now });
    }

    await bizPreloadProducts();
    bizToast(`✅ Produk "${name}" disimpan`, 's');
    bizCloseModal('biz-modal-product');
    if (window.bizState.activeTab === 'products') await bizLoadProducts();
}
window.bizSaveProduct = bizSaveProduct;

// ── Products List Page ────────────────────────────────────────────────────────
async function bizLoadProducts() {
    const container = document.getElementById('biz-app-container');
    if (!container) return;

    container.innerHTML = `<div class="biz-page"><div class="biz-loading" style="padding:40px;text-align:center"><i class="fas fa-spinner fa-spin fa-2x" style="color:var(--biz-primary)"></i><br><br>Memuat Product Intelligence...</div></div>`;

    const bizId = window.bizState.businessId;
    const prodData = typeof bizProductIntelligence === 'function' ? await bizProductIntelligence(bizId) : null;

    window._prodSysData = prodData;

    if (!prodData) {
        container.innerHTML = `<div class="biz-empty"><i class="fas fa-box-open"></i><br>Data produk tidak tersedia.</div>`;
        return;
    }

    container.innerHTML = `<div class="biz-page" style="padding-bottom:100px;">
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Product Intelligence</h2>
                <div style="font-size:13px;color:var(--biz-text-dim);font-weight:600;margin-top:2px">SaaS Level Commercial Analytics</div>
            </div>
            <button class="biz-btn biz-btn-primary" onclick="bizOpenAddProduct()"><i class="fas fa-plus"></i> Tambah</button>
        </div>

        <!-- Layer 1: Product Overview (KPIs) -->
        <div id="prod-layer-1" style="margin-bottom:24px;"></div>

        <!-- Layer 2: Product Alerts -->
        <div id="prod-layer-2" style="margin-bottom:24px;"></div>

        <!-- Layer 3: AI Product Insights -->
        <div id="prod-layer-3" style="margin-bottom:24px;"></div>

        <!-- Layer 5: Product Database (Virtualized Table) -->
        <div class="biz-card" style="margin-bottom:24px;overflow:hidden">
            <div class="biz-card-header" style="padding:16px;background:var(--biz-surface-2);border-bottom:1px solid var(--biz-border);display:flex;justify-content:space-between;align-items:center">
                <div class="biz-card-title"><i class="fas fa-database" style="color:var(--biz-primary)"></i> Product Database</div>
            </div>
            <div style="padding:12px 16px;border-bottom:1px solid var(--biz-border)">
                <div class="biz-search-bar" style="margin:0">
                    <i class="fas fa-search"></i>
                    <input type="text" id="prod-smart-search" placeholder="Cari SKU, Nama, atau Status LifeCycle..." oninput="prodRenderChunk(0, this.value)">
                </div>
            </div>
            
            <div id="prod-table-body" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(min(100%, 320px), 1fr));justify-content:center;gap:16px;padding:16px;background:var(--biz-surface);">
                <!-- Virtualized grid cards here -->
            </div>
            <div id="prod-table-footer" style="padding:12px;text-align:center;font-size:12px;color:var(--biz-text-dim);background:var(--biz-surface-2)"></div>
        </div>
    </div>`;

    setTimeout(() => {
        _prodRenderLayer1();
        _prodRenderLayer2();
        _prodRenderLayer3();
        prodRenderChunk(0, '');
    }, 50);
}

function _prodRenderLayer1() {
    const { stats } = window._prodSysData;
    const html = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOTAL PRODUK</div><div style="font-size:20px;font-weight:800;margin-top:4px">${stats.totalProducts}</div></div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">REVENUE 30D</div><div style="font-size:20px;font-weight:800;color:var(--biz-primary);margin-top:4px">${bizRpFull(stats.totalRev30d)}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(16,185,129,0.2)"><div style="font-size:11px;color:var(--biz-success);font-weight:700">MARGIN TINGGI</div><div style="font-size:20px;font-weight:800;color:var(--biz-success);margin-top:4px">${stats.highMargin}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(239,68,68,0.2)"><div style="font-size:11px;color:var(--biz-danger);font-weight:700">MARGIN TIPIS (<10%)</div><div style="font-size:20px;font-weight:800;color:var(--biz-danger);margin-top:4px">${stats.lowMargin}</div></div>
        <div class="biz-card" style="padding:16px"><div style="font-size:11px;color:var(--biz-text-dim);font-weight:700">TOP SELLER</div><div style="font-size:14px;font-weight:800;color:var(--biz-warning);margin-top:8px">${_esc(stats.topSellerName)}</div></div>
        <div class="biz-card" style="padding:16px;border:1px solid rgba(139,92,246,0.2)"><div style="font-size:11px;color:var(--biz-purple);font-weight:700">PRODUK STAGNAN</div><div style="font-size:20px;font-weight:800;color:var(--biz-purple);margin-top:4px">${stats.stagnant}</div></div>
    </div>`;
    document.getElementById('prod-layer-1').innerHTML = html;
}

function _prodRenderLayer2() {
    const { alerts } = window._prodSysData;
    let html = '';
    if (alerts && alerts.length > 0) {
        html = `<div class="biz-card" style="border:1px solid rgba(239,68,68,0.3);background:var(--biz-danger-bg)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-danger);letter-spacing:0.5px;border-bottom:1px solid rgba(239,68,68,0.1)">
                <i class="fas fa-bell"></i> SYSTEM ALERTS
            </div>
            <div style="padding:12px 16px">
                ${alerts.map(a => `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                    <i class="fas ${a.icon}" style="color:var(--biz-${a.type});margin-top:3px;font-size:14px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${a.text}</span>
                </div>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('prod-layer-2').innerHTML = html;
}

function _prodRenderLayer3() {
    const { insights } = window._prodSysData;
    let html = '';
    if (insights && insights.length > 0) {
        html = `<div class="biz-card" style="border:1px solid var(--biz-border-strong);border-left:4px solid var(--biz-primary)">
            <div style="padding:12px 16px;font-size:11px;font-weight:800;color:var(--biz-primary);letter-spacing:0.5px;border-bottom:1px solid var(--biz-border)">
                <i class="fas fa-sparkles"></i> AI PRODUCT INSIGHTS
            </div>
            <div style="padding:12px 16px">
                ${insights.map(i => `<div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:8px">
                    <i class="fas fa-lightbulb" style="color:var(--biz-success);margin-top:3px;font-size:14px"></i>
                    <span style="font-size:13px;font-weight:600;color:var(--biz-text)">${i}</span>
                </div>`).join('')}
            </div>
        </div>`;
    }
    document.getElementById('prod-layer-3').innerHTML = html;
}



let _prodChunkPos = 0;
const _prodChunkSize = 30;

function prodRenderChunk(startIdx, query) {
    const { products } = window._prodSysData;
    const tbody = document.getElementById('prod-table-body');
    const footer = document.getElementById('prod-table-footer');
    if (!tbody || !products) return;

    if (startIdx === 0) {
        _prodChunkPos = 0;
        tbody.innerHTML = '';
        // scroll removed to avoid jump
    }

    let filtered = products;
    if (query) {
        const q = query.toLowerCase();
        filtered = products.filter(b =>
            b.name.toLowerCase().includes(q) ||
            b.sku.toLowerCase().includes(q) ||
            b.lifecycle.toLowerCase().includes(q)
        );
    }

    const chunk = filtered.slice(startIdx, startIdx + _prodChunkSize);

    if (chunk.length === 0 && startIdx === 0) {
        tbody.style.display = 'block';
        tbody.innerHTML = '<div style="padding:40px;text-align:center;color:var(--biz-text-muted)"><i class="fas fa-box-open fa-3x" style="margin-bottom:16px;opacity:0.5"></i><br><span style="font-size:15px;font-weight:600">Tidak ditemukan produk yang cocok.</span></div>';
        footer.innerHTML = '';
        return;
    }

    tbody.style.display = 'grid';

    const html = chunk.map(p => {
        let lCol = p.lifecycle === 'GROWING' ? 'background:var(--biz-info-bg);color:var(--biz-info)' :
            p.lifecycle === 'NEW' ? 'background:var(--biz-success-bg);color:var(--biz-success)' :
                p.lifecycle === 'DECLINING' ? 'background:var(--biz-danger-bg);color:var(--biz-danger)' :
                    'background:var(--biz-surface-2);color:var(--biz-text-dim)';

        let lIcon = p.lifecycle === 'GROWING' ? 'fa-fire' :
            p.lifecycle === 'NEW' ? 'fa-wand-magic-sparkles' :
                p.lifecycle === 'DECLINING' ? 'fa-arrow-trend-down' : 'fa-balance-scale';

        let mTxt = p.marginPct < 10 ? 'var(--biz-danger)' : p.marginPct >= 30 ? 'var(--biz-success)' : 'var(--biz-text)';
        let stockTxt = p.type === 'physical' ? p.stock + ' <span style="font-size:10px;color:var(--biz-text-dim)">pcs</span>' : '- <span style="font-size:10px;color:var(--biz-text-dim)">' + p.type + '</span>';

        let imgStyle = p.image_data
            ? `background-image:url('${p.image_data}');background-size:cover;background-position:center;`
            : `background:var(--biz-surface-2);display:flex;align-items:center;justify-content:center;color:var(--biz-text-dim);font-size:24px;`;

        return `<div class="biz-card" style="display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--biz-border);transition:transform 0.2s, box-shadow 0.2s;cursor:pointer" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'">
            <!-- Headline & Image -->
            <div style="display:flex;gap:16px;padding:16px;border-bottom:1px dashed var(--biz-border)">
                <div style="width:70px;height:70px;border-radius:12px;flex-shrink:0;${imgStyle}">
                    ${p.image_data ? '' : '<i class="fas fa-box"></i>'}
                </div>
                <div style="flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center">
                    <div style="font-weight:800;font-size:15px;color:var(--biz-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="${_esc(p.name)}">${_esc(p.name)}</div>
                    <div style="font-size:12px;color:var(--biz-text-muted);margin-top:4px"><i class="fas fa-tag"></i> ${_esc(p.category || 'General')}</div>
                    <div style="font-size:11px;color:var(--biz-text-dim);margin-top:2px">SKU: ${_esc(p.sku)}</div>
                </div>
                <div style="text-align:right;display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between">
                    <span style="display:inline-block;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:800;${lCol}">
                        <i class="fas ${lIcon}" style="margin-right:2px"></i> ${p.lifecycle}
                    </span>
                    <div style="display:flex;gap:4px">
                        <button class="biz-btn biz-btn-ghost biz-btn-sm" style="padding:4px 8px" onclick="bizOpenEditAction('${p.id}')"><i class="fas fa-pencil"></i></button>
                        <button class="biz-btn biz-btn-ghost biz-btn-sm" style="padding:4px 8px;color:var(--biz-danger)" onclick="bizDeleteProductConfirm('${p.id}', '${_esc(p.name)}')"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            <!-- Analytics Body -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:16px;background:var(--biz-surface-2)">
                <div>
                    <div style="font-size:10px;color:var(--biz-text-dim);font-weight:700">HARGA & MARGIN</div>
                    <div style="font-size:14px;font-weight:800;margin-top:2px">${bizRp(p.price)}</div>
                    <div style="font-size:11px;font-weight:700;color:${mTxt};margin-top:2px">${p.marginPct}% Margin</div>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--biz-text-dim);font-weight:700">TERJUAL (30D)</div>
                    <div style="font-size:14px;font-weight:800;margin-top:2px">${p.sold30d} <span style="font-size:10px;font-weight:600;color:var(--biz-text-muted)">pcs</span></div>
                    <div style="font-size:11px;color:var(--biz-text-muted);margin-top:2px"><i class="fas fa-clock"></i> ${p.avgDaily}/hr</div>
                </div>
                <div style="grid-column: span 2;border-top:1px dashed var(--biz-border);padding-top:12px;margin-top:4px">
                    <div style="font-size:10px;color:var(--biz-text-dim);font-weight:700;display:flex;justify-content:space-between">
                        <span>EST. REVENUE</span>
                        <span>EST. PROFIT</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <span style="font-size:14px;font-weight:800;color:var(--biz-success)">${bizRpFull(p.rev30d)}</span>
                        <span style="font-size:14px;font-weight:800;color:var(--biz-primary)">${bizRpFull(p.prof30d)}</span>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    tbody.insertAdjacentHTML('beforeend', html);
    _prodChunkPos = startIdx + _prodChunkSize;

    if (_prodChunkPos < filtered.length) {
        footer.innerHTML = `<button class="biz-btn biz-btn-ghost" style="width:100%" onclick="prodRenderChunk(${_prodChunkPos}, document.getElementById('prod-smart-search').value)">Load More (${filtered.length - _prodChunkPos} produk tersisa)</button>`;
    } else {
        footer.innerHTML = `Menampilkan seluruh ${filtered.length} produk.`;
    }
}

async function bizOpenEditAction(id) {
    const products = await BizDB.products.getAll();
    const p = products.find(prod => prod.id === id);
    if (p) bizOpenEditProduct(p);
}

function bizDeleteProductConfirm(id, name) {
    if (typeof bizConfirm === 'function') {
        bizConfirm(
            'Hapus Produk',
            `Apakah Anda yakin ingin menghapus produk <b>${name}</b>?<br>Data penjualan terkait tetap utuh (aman), tapi produk ini akan dikeranjangkan / hilang dari katalog inventaris.`,
            async () => {
                const products = await BizDB.products.getAll();
                const p = products.find(prod => prod.id === id);
                if (p) {
                    p.is_active = false;
                    p.updated_at = new Date().toISOString();
                    await BizDB.products.save(p);
                    bizToast('Produk berhasil dihapus', 's');
                    bizClearIntelligenceCache();
                    await bizPreloadProducts();
                    if (window.bizState.activeTab === 'products') await bizLoadProducts();
                }
            },
            'danger'
        );
    }
}

window.bizLoadProducts = bizLoadProducts;
window.prodRenderChunk = prodRenderChunk;
window.bizOpenEditAction = bizOpenEditAction;
window.bizDeleteProductConfirm = bizDeleteProductConfirm;
