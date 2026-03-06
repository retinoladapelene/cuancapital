/**
 * Business Manager — ui.js
 * App Shell Router: tab switching, toast, modals, theme, mode switcher
 */

// ── State ────────────────────────────────────────────────────────────────────
window.bizState = {
    activeTab: 'dashboard',
    businessId: null,
    productCache: [],   // preloaded products for fast search
    initialized: false,
};

// ── Theme ────────────────────────────────────────────────────────────────────
(function () {
    const saved = localStorage.getItem('biz_theme') || localStorage.getItem('cb_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
})();

function bizToggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('biz_theme', next);
}
window.bizToggleTheme = bizToggleTheme;

// ── Toast ─────────────────────────────────────────────────────────────────────
let _toastTimer = null;
function bizToast(msg, type = 'i', duration = 2800) {
    const el = document.getElementById('biz-toast');
    if (!el) return;
    const colors = { s: 'var(--biz-success)', e: 'var(--biz-danger)', i: 'var(--biz-primary)', w: 'var(--biz-warning)' };
    const icons = { s: 'fa-circle-check', e: 'fa-circle-xmark', i: 'fa-circle-info', w: 'fa-triangle-exclamation' };
    el.innerHTML = `<i class="fas ${icons[type] || icons.i}" style="color:${colors[type] || colors.i}"></i> ${msg}`;
    el.classList.add('show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => el.classList.remove('show'), duration);
}
window.bizToast = bizToast;

// ── Modal ─────────────────────────────────────────────────────────────────────
function bizOpenModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function bizCloseModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('open');
    document.body.style.overflow = '';
}
function bizCloseOut(bgEl) {
    if (bgEl && bgEl.id) bizCloseModal(bgEl.id);
}
window.bizOpenModal = bizOpenModal;
window.bizCloseModal = bizCloseModal;
window.bizCloseOut = bizCloseOut;

// ── Global FAB Menu ───────────────────────────────────────────────────────────
function bizToggleFabMenu() {
    const menu = document.getElementById('biz-fab-menu');
    const main = document.querySelector('.biz-fab-main');
    if (!menu || !main) return;

    if (menu.classList.contains('open')) {
        menu.classList.remove('open');
        main.classList.remove('active');
    } else {
        menu.classList.add('open');
        main.classList.add('active');
    }
}
window.bizToggleFabMenu = bizToggleFabMenu;

// ── Tab Manager (App Shell Router) ────────────────────────────────────────────
const BIZ_MODULES = {
    dashboard: { loaded: false, init: () => typeof bizLoadDashboard === 'function' && bizLoadDashboard() },
    sales: { loaded: false, init: () => typeof bizLoadSales === 'function' && bizLoadSales() },
    products: { loaded: false, init: () => typeof bizLoadProducts === 'function' && bizLoadProducts() },
    inventory: { loaded: false, init: () => typeof bizLoadInventory === 'function' && bizLoadInventory() },
    finance: { loaded: false, init: () => typeof bizLoadFinance === 'function' && bizLoadFinance() },
    reports: { loaded: false, init: () => typeof bizLoadReports === 'function' && bizLoadReports() },
};

async function bizSwitchTab(name, fromBtn) {
    if (bizState.activeTab === name && BIZ_MODULES[name]?.loaded) return;

    // Update nav button states (mobile)
    document.querySelectorAll('.biz-nav-btn').forEach(b => b.classList.remove('active'));
    if (fromBtn) fromBtn.classList.add('active');
    else {
        const btn = document.getElementById('biz-mob-' + name);
        if (btn) btn.classList.add('active');
    }

    // Update sidebar (desktop)
    document.querySelectorAll('.biz-sidebar-link').forEach(l => l.classList.remove('active'));
    const sLink = document.querySelector(`.biz-sidebar-link[data-tab="${name}"]`);
    if (sLink) sLink.classList.add('active');

    // Show loading skeleton
    const container = document.getElementById('biz-app-container');
    if (container) {
        container.innerHTML = '<div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';
    }

    bizState.activeTab = name;

    // Dispatch tab init
    const mod = BIZ_MODULES[name];
    if (mod) {
        try {
            await mod.init();
            mod.loaded = true;
        } catch (e) {
            console.error('[BizUI] Tab load error:', e);
            if (container) container.innerHTML = `<div class="biz-empty">Gagal memuat: ${e.message}</div>`;
        }
    }
}
window.bizSwitchTab = bizSwitchTab;

// ── Confirm Dialog ─────────────────────────────────────────────────────────────
function bizConfirm(title, msg, onConfirm, type = 'danger') {
    document.getElementById('biz-confirm-title').textContent = title;
    document.getElementById('biz-confirm-msg').textContent = msg;
    const btn = document.getElementById('biz-confirm-ok');
    btn.className = `biz-btn ${type === 'danger' ? 'biz-btn-danger' : 'biz-btn-primary'}`;
    btn.onclick = () => { bizCloseModal('biz-modal-confirm'); onConfirm(); };
    bizOpenModal('biz-modal-confirm');
}
window.bizConfirm = bizConfirm;

// ── Global Click Outside to Close Modals & FAB ───────────────────────────────
document.addEventListener('click', e => {
    if (e.target.classList.contains('biz-modal-bg')) bizCloseOut(e.target);

    // Auto-close FAB menu if clicking outside
    if (!e.target.closest('.biz-global-fab')) {
        document.getElementById('biz-fab-menu')?.classList.remove('open');
        document.querySelector('.biz-fab-main')?.classList.remove('active');
    }
}, { passive: true });

// ── Back Button Intercept ─────────────────────────────────────────────────────
window.addEventListener('load', () => { history.pushState(null, '', location.href); });
window.addEventListener('popstate', function onPopState() {
    bizConfirm(
        'Keluar Business Manager',
        'Kembali ke halaman utama?',
        () => { window.removeEventListener('popstate', onPopState); window.location.href = '/'; },
        'warning'
    );
    history.pushState(null, '', location.href);
});

// ── Preload Product Cache (for fast search) ────────────────────────────────────
async function bizPreloadProducts() {
    try {
        const all = await BizDB.products.getAll();
        window.bizState.productCache = all.filter(p => p.is_active !== false);
        // Precompute quick products (for Quick Sale chips) — top 5 by recent sale frequency
        await bizComputeQuickProducts();
    } catch (e) { console.warn('[BizUI] Product preload failed:', e); }
}

async function bizComputeQuickProducts() {
    try {
        // Get all sale_items, count by product_id
        const items = await BizDB.saleItems.getAll();
        const freq = {};
        items.forEach(si => { freq[si.product_id] = (freq[si.product_id] || 0) + 1; });
        const sorted = window.bizState.productCache
            .filter(p => p.type !== 'service')
            .sort((a, b) => (freq[b.id] || 0) - (freq[a.id] || 0))
            .slice(0, 5);
        window.bizState.quickProducts = sorted;
    } catch (e) { window.bizState.quickProducts = window.bizState.productCache.slice(0, 5); }
}
window.bizPreloadProducts = bizPreloadProducts;

// ── App Bootstrap ─────────────────────────────────────────────────────────────
async function bizBootstrap() {
    if (window.__bizInit) return;
    window.__bizInit = true;

    try {
        // Init IndexedDB
        await BizDB.init();

        // Ensure active business exists
        const bizId = await BizSession.ensureBusiness();
        window.bizState.businessId = bizId;
        window.bizState.initialized = true;

        // Preload products in background
        bizPreloadProducts();

        // Check backup reminder
        if (typeof bizCheckBackupReminder === 'function') bizCheckBackupReminder();

        // Load default tab
        const urlParams = new URLSearchParams(location.search);
        const tab = urlParams.get('tab') || 'dashboard';
        bizSwitchTab(tab);

    } catch (e) {
        console.error('[BizUI] Bootstrap error:', e);
        const c = document.getElementById('biz-app-container');
        if (c) c.innerHTML = `<div class="biz-empty" style="color:var(--biz-danger)">
            <i class="fas fa-circle-exclamation"></i><br>Gagal memulai aplikasi.<br>
            <small>${e.message}</small></div>`;
    }
}
window.bizBootstrap = bizBootstrap;

// ── Number Animator (Fintech Micro-Interaction) ──────────────────────────────
function bizAnimateValue(el, start, end, duration = 800, isCurrency = false) {
    if (!el) return;
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        // Ease out cubic
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(easeOut * (end - start) + start);

        // Assume bizRpFull exists globally, fallback if not
        if (isCurrency && typeof bizRpFull === 'function') {
            el.textContent = bizRpFull(current);
        } else {
            el.textContent = current.toLocaleString('id-ID');
        }

        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            if (isCurrency && typeof bizRpFull === 'function') {
                el.textContent = bizRpFull(end);
            } else {
                el.textContent = end.toLocaleString('id-ID');
            }
        }
    };
    window.requestAnimationFrame(step);
}
window.bizAnimateValue = bizAnimateValue;

// ── Product Search Helper (used by Sales & Products) ─────────────────────────
function bizSearchProducts(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) return window.bizState.productCache.slice(0, 8);
    return window.bizState.productCache
        .filter(p => p.name.toLowerCase().includes(q))
        .slice(0, 8);
}
window.bizSearchProducts = bizSearchProducts;

// ── Sidebar toggle (desktop) ──────────────────────────────────────────────────
function bizToggleSidebar() {
    document.getElementById('biz-sidebar')?.classList.toggle('collapsed');
}
window.bizToggleSidebar = bizToggleSidebar;

// ── Nav dropdown menu (mobile gear menu) ─────────────────────────────────────
function bizToggleNavMenu(id) {
    const menu = document.getElementById(id);
    const isOpen = menu?.style.display === 'flex';
    document.querySelectorAll('.biz-nav-dropdown').forEach(m => m.style.display = 'none');
    if (menu && !isOpen) menu.style.display = 'flex';
}
function bizCloseNavMenus() {
    document.querySelectorAll('.biz-nav-dropdown').forEach(m => m.style.display = 'none');
}
window.bizToggleNavMenu = bizToggleNavMenu;
window.bizCloseNavMenus = bizCloseNavMenus;

document.addEventListener('click', e => {
    if (!e.target.closest('.biz-nav-right')) bizCloseNavMenus();
}, { passive: true });
document.addEventListener('touchstart', () => { }, { passive: true });
