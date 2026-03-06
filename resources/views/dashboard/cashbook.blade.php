<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,viewport-fit=cover">
<title>Discipline OS - Financial Control</title>
<!-- ─── Fonts: Hanya 400 + 600 ─── Hemat ~120KB ─── -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<!-- ─── Font Awesome: async non-blocking load ─── -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>
<link rel="stylesheet" href="{{ asset('assets/css/cashbook-core.css') }}?v={{ time() }}">
<style>
/* ── Mobile Bottom Nav (added on top of original CSS) ── */
:root { --nav-h: 62px; }
.mob-nav {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    height: var(--nav-h);
    background: rgba(15,23,42,0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex;
    z-index: 500;
    padding-bottom: env(safe-area-inset-bottom);
    box-shadow: 0 -2px 8px rgba(0,0,0,.2);
    transform: translateZ(0); /* GPU layer */
    will-change: transform;
}
/* ── MOBILE PERFORMANCE: Disable expensive GPU effects ── */
@media (max-width: 768px) {
    .mob-nav {
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        background: rgba(15,23,42,0.98); /* solid bg instead of blur */
    }
    [data-theme="light"] .mob-nav {
        background: rgba(248,250,252,0.99);
    }
}
[data-theme="light"] .mob-nav {
    background: rgba(248,250,252,0.95);
    border-top-color: rgba(0,0,0,0.08);
}
.mob-nav-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-muted);
    font-size: 10px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    letter-spacing: .3px;
    transition: color .2s;
    padding: 8px 4px;
    -webkit-tap-highlight-color: transparent;
}
.mob-nav-btn i { font-size: 18px; transition: transform .2s; }
.mob-nav-btn.active { color: var(--accent); }
.mob-nav-btn.active i { transform: translateY(-1px); }

/* ── View Tabs ── */
.view-content { display: none; }
.view-content.active { display: block; }

/* ── Mobile: hide desktop nav tabs, show mobile nav ── */
@media (max-width: 768px) {
    .nav-center { display: none !important; }
    .mob-nav { display: flex !important; }
    body { padding-bottom: var(--nav-h); }
    .page { padding: 16px 12px calc(var(--nav-h) + 8px); }
    .fab-wrap { bottom: calc(var(--nav-h) + 12px); }
    .toast { bottom: calc(var(--nav-h) + 12px); right: 12px; }
}
/* ── Desktop: show nav tabs, hide mobile nav ── */
@media (min-width: 769px) {
    .mob-nav { display: none !important; }
    .nav-center { display: flex !important; }
    .view-content.active { display: block; }
}

/* ── Modal overrides for bottom-sheet on mobile ── */
@media (max-width: 768px) {
    .modal-bg {
        align-items: flex-end;
        padding: 0;
    }
    .modal {
        border-radius: 20px 20px 0 0;
        max-width: 100%;
        transform: translateY(40px) scale(1);
        padding-bottom: calc(28px + env(safe-area-inset-bottom));
    }
    .modal-bg.open .modal {
        transform: translateY(0) scale(1);
    }
}

/* ── Tab pane override to work with view-content ── */
.tab-pane { display: block; }
.tab-pane.active { display: block; animation: fadeUp .3s ease; }

/* ── cb-toast ── */
#cb-toast {
    position: fixed; top: 24px; left: 50%;
    padding: 12px 18px 12px 24px; border-radius: 12px;
    font-size: 13px; font-weight: 600; z-index: 9999;
    transform: translate(-50%, -20px); opacity: 0; transition: all .3s cubic-bezier(0.16,1,0.3,1);
    background: var(--surface); border: 1px solid var(--border);
    box-shadow: 0 12px 32px rgba(0,0,0,.2); color: var(--text);
    width: max-content; max-width: 90%; overflow: hidden;
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
}
#cb-toast.show { transform: translate(-50%, 0); opacity: 1; }
</style>
</head>
<body>

<!-- ── SVG Defs (for chart gradients) ── -->
<svg width="0" height="0" style="position:absolute">
  <defs>
    <linearGradient id="gr1" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#10b981"/>
      <stop offset="100%" stop-color="#34d399"/>
    </linearGradient>
    <linearGradient id="gr2" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#10b981"/>
      <stop offset="100%" stop-color="#34d399"/>
    </linearGradient>
  </defs>
</svg>

<!-- ── Desktop Top Nav ── -->
<nav class="nav">
    <a href="/" onclick="event.preventDefault(); if(typeof confirmDialog === 'function') confirmDialog('Keluar Aplikasi', 'Apakah Anda yakin ingin keluar dari halaman Cashbook dan kembali ke menu utama?', 'warning', () => window.location.href='/'); else window.location.href='/';" class="brand" style="text-decoration:none; display:flex; align-items:center;">
        <img src="{{ asset('assets/icon/logo.svg') }}" class="brand-logo light-logo" alt="CuanCapital" style="height:32px;">
        <img src="{{ asset('assets/icon/logo-darkmode.svg') }}" class="brand-logo dark-logo" alt="CuanCapital" style="height:32px;">
        <style>
            html[data-theme="dark"] .brand-logo.light-logo { display: none; }
            html[data-theme="light"] .brand-logo.dark-logo { display: none; }
        </style>
    </a>
    <div class="nav-center">
        <button class="nav-tab active" id="ntab-overview" onclick="switchMainTab('overview', null, this)">Overview</button>
        <button class="nav-tab" id="ntab-transaksi" onclick="switchMainTab('transaksi', null, this)">Transaksi</button>
        <button class="nav-tab" id="ntab-laporan" onclick="switchMainTab('laporan', null, this)">Laporan</button>
        <button class="nav-tab" id="ntab-utang" onclick="switchMainTab('utang', null, this)">Utang</button>
        <button class="nav-tab" id="ntab-anggaran" onclick="switchMainTab('anggaran', null, this)">Anggaran</button>
    </div>
    <div class="nav-right" style="display:flex; gap:8px; align-items:center;">
        <div style="position:relative;">
            <button class="btn btn-ghost btn-sm icon-btn" onclick="toggleNavMenu('nav-add-menu')" title="Catat Transaksi">
                <i class="fas fa-plus"></i>
            </button>
            <div class="nav-dropdown" id="nav-add-menu">
                <button class="nav-dd-btn" onclick="openModal('modal-transaction');prepTx('income');closeNavMenus()"><i class="fas fa-arrow-trend-up" style="color:var(--accent);"></i> Pemasukan</button>
                <button class="nav-dd-btn" onclick="openModal('modal-transaction');prepTx('expense');closeNavMenus()"><i class="fas fa-arrow-trend-down" style="color:var(--danger);"></i> Pengeluaran</button>
                <button class="nav-dd-btn" onclick="openModal('modal-transaction');prepTx('transfer');closeNavMenus()"><i class="fas fa-arrow-right-arrow-left" style="color:var(--info);"></i> Transfer</button>
            </div>
        </div>

        <div style="position:relative;">
            <button class="btn btn-ghost btn-sm icon-btn" onclick="toggleNavMenu('nav-gear-menu')" title="Pengaturan">
                <i class="fas fa-gear"></i>
            </button>
            <div class="nav-dropdown" id="nav-gear-menu">
                <button class="nav-dd-btn" onclick="toggleTheme();closeNavMenus()"><i class="fas fa-moon"></i> Mode Gelap / Terang</button>
                <button class="nav-dd-btn" onclick="openModal('modal-account');closeNavMenus()"><i class="fas fa-wallet"></i> Kelola Akun</button>
                <button class="nav-dd-btn" onclick="openModal('modal-data');closeNavMenus()"><i class="fas fa-database"></i> Backup & Restore JSON</button>
                <div style="border-top:1px solid var(--border); margin:4px 0;"></div>
                <button class="nav-dd-btn" onclick="window.location.href='/guide/cashbook';closeNavMenus()"><i class="fas fa-book"></i> Panduan Cashbook</button>
            </div>
        </div>
    </div>
</nav>

<!-- ── Mobile Bottom Nav ── -->
<nav class="mob-nav" id="mob-nav">
    <button class="mob-nav-btn active" id="mob-btn-overview" onclick="switchMainTab('overview',null,this)">
        <i class="fas fa-house"></i><span>Overview</span>
    </button>
    <button class="mob-nav-btn" id="mob-btn-transaksi" onclick="switchMainTab('transaksi',null,this)">
        <i class="fas fa-receipt"></i><span>Transaksi</span>
    </button>
    <button class="mob-nav-btn" id="mob-btn-laporan" onclick="switchMainTab('laporan',null,this)">
        <i class="fas fa-chart-bar"></i><span>Laporan</span>
    </button>
    <button class="mob-nav-btn" id="mob-btn-utang" onclick="switchMainTab('utang',null,this)">
        <i class="fas fa-credit-card"></i><span>Utang</span>
    </button>
    <button class="mob-nav-btn" id="mob-btn-anggaran" onclick="switchMainTab('anggaran',null,this)">
        <i class="fas fa-sliders"></i><span>Anggaran</span>
    </button>
</nav>

<!-- ── Page ── -->
<div class="page">
    <!-- ╔══════════════════════════════════════╗ -->
    <!-- ║  SINGLE ACTIVE VIEW CONTAINER        ║ -->
    <!-- ╚══════════════════════════════════════╝ -->
    <!-- Only ONE tab's DOM lives here at a time. -->
    <!-- Content is mounted/unmounted by ui.js router -->
    <div id="cashbook-app-container" class="view-content active">
        <div class="empty" style="padding:64px;"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>
    </div>
</div>


<!-- ── FAB dihilangkan, Quick Add dipindah ke Top Navbar ── -->

<!-- ── Toast ── -->
<div id="cb-toast"></div>

<!-- ── Modals ── -->
@include('dashboard.cashbook.modals')

@php $cb_ver = time(); @endphp
<script src="{{ asset('assets/js/cashbook/ui.js') }}?v={{ $cb_ver }}"></script>
<script src="{{ asset('assets/js/cashbook/core.js') }}?v={{ $cb_ver }}"></script>
<script src="{{ asset('assets/js/cashbook/accounts.js') }}?v={{ $cb_ver }}"></script>
<script src="{{ asset('assets/js/cashbook/domUtils.js') }}?v={{ $cb_ver }}"></script>
<script src="{{ asset('assets/js/cashbook/virtualList.js') }}?v={{ $cb_ver }}"></script>

<script>
// ── Core Config & Theme init (before paint) ──
window.ASSET_URL = "{{ asset('') }}";
(function() {
    const saved = localStorage.getItem('cb_theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
})();

// ── Top Nav Dropdowns ──
window.toggleNavMenu = function(menuId) {
    const menu = document.getElementById(menuId);
    const isShowing = menu.style.display === 'flex';
    closeNavMenus();
    if (!isShowing) menu.style.display = 'flex';
};
window.closeNavMenus = function() {
    document.querySelectorAll('.nav-dropdown').forEach(el => el.style.display = 'none');
};
document.addEventListener('click', function(e) {
    if (!e.target.closest('.nav-right')) {
        closeNavMenus();
    }
    // Close custom dropdowns if clicked outside
    const hasOpenDropdown = document.querySelector('.cdd-menu.open');
    if (hasOpenDropdown && !e.target.closest('.cdd')) {
        document.querySelectorAll('.cdd-menu.open').forEach(m => m.classList.remove('open'));
    }
}, { passive: true });

// ── Passive touchstart: meningkatkan scroll performance di mobile ──
document.addEventListener('touchstart', function() {}, { passive: true });


function toggleCdd(id) {
    const menu = document.getElementById(id + '-menu');
    if (!menu) return;
    const wasOpen = menu.classList.contains('open');
    document.querySelectorAll('.cdd-menu.open').forEach(m => m.classList.remove('open'));
    if (!wasOpen) menu.classList.add('open');
}
window.toggleCdd = toggleCdd;

function closeOut(bgEl) { closeModal(bgEl.id); }
window.closeOut = closeOut;

// ── confirmDialog fallback (ids may differ between versions) ──
if (!window.confirmDialog) {
    window.confirmDialog = function(title, msg, type, onConfirm) {
        const titleEl = document.getElementById('confirm-title') || document.getElementById('cmd-title');
        const msgEl   = document.getElementById('confirm-message') || document.getElementById('cmd-msg');
        const ic      = document.getElementById('confirm-icon') || document.getElementById('cmd-icon');
        const btn     = document.getElementById('confirm-ok') || document.getElementById('cmd-btn');
        const wrap    = document.getElementById('confirm-icon-wrap');
        if (titleEl) titleEl.textContent = title;
        if (msgEl)   msgEl.textContent   = msg;
        if (ic) {
            ic.className = type === 'danger' ? 'fas fa-triangle-exclamation' : 'fas fa-info-circle';
            ic.style.color = type === 'danger' ? 'var(--danger)' : 'var(--info)';
            if (wrap) { wrap.style.background = type === 'danger' ? 'rgba(239,68,68,0.1)' : 'rgba(59,130,246,0.1)'; }
        }
        if (btn) {
            btn.className = `btn btn-${type === 'danger' ? 'danger' : 'accent'}`;
            btn.textContent = type === 'danger' ? 'Ya, Hapus' : 'Lanjutkan';
            btn.onclick = () => { closeModal('modal-confirm'); if (typeof onConfirm === 'function') onConfirm(); };
        }
        openModal('modal-confirm');
    };
}

// ── Export / Import Data stubs (real impl in debts.js, loaded lazily) ──
if (!window.exportData) {
    window.exportData = async function() {
        toast('Sedang menyiapkan file backup...', 'i');
        try {
            let installments = [];
            try { installments = await localAPI.installments.getAll(); } catch (_) {}
            const backupData = {
                exported_at: new Date().toISOString(), version: 1,
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
            a.style.display = 'none'; a.href = url;
            a.download = `cuan_cashbook_backup_${new Date().toISOString().slice(0,10)}.json`;
            document.body.appendChild(a); a.click();
            window.URL.revokeObjectURL(url);
            toast('Backup berhasil diunduh!', 's');
            if (typeof closeModal === 'function') closeModal('modal-data');
        } catch (e) { toast('Gagal export: ' + e.message, 'e'); }
    };
}
if (!window.handleImportFile) {
    window.handleImportFile = async function(input) {
        if (!input.files || !input.files.length) return;
        const file = input.files[0];
        if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
            toast('File harus berformat .json', 'e'); input.value = ''; return;
        }
        toast('Sedang memulihkan data...', 'i');
        const reader = new FileReader();
        reader.onload = async (ev) => {
            try {
                const parsed = JSON.parse(ev.target.result);
                if (!parsed.data || typeof parsed.data !== 'object') throw new Error('Format JSON tidak valid');
                const d = parsed.data;
                const clearStore = (s) => localAPI._runOp(s, 'readwrite', store => store.clear());
                if (d.categories)   { await clearStore('categories');   for (const i of d.categories)   await localAPI.categories.save(i); }
                if (d.accounts)     { await clearStore('accounts');     for (const i of d.accounts)     await localAPI.accounts.save(i); }
                if (d.transactions) { await clearStore('transactions'); for (const i of d.transactions) await localAPI.transactions.save(i); }
                if (d.budgets)      { await clearStore('budgets');      for (const i of d.budgets)      await localAPI.budgets.save(i); }
                if (d.debts)        { await clearStore('debts');        for (const i of d.debts)        await localAPI.debts.save(i); }
                if (d.installments && d.installments.length) {
                    try { await clearStore('installments'); for (const i of d.installments) await localAPI.installments.save(i); } catch (_) {}
                }
                if (d.meta) { for (const i of d.meta) await localAPI.meta.set(i.key, i.value); }
                toast('Restore Sukses! Memuat ulang...', 's');
                if (typeof closeModal === 'function') closeModal('modal-data');
                setTimeout(() => window.location.reload(), 1500);
            } catch (err) { toast('Gagal membaca JSON: ' + err.message, 'e'); }
        };
        reader.readAsText(file); input.value = '';
    };
}

// ── Intercept Back Button (Exit Confirmation) ──
window.addEventListener('load', () => {
    history.pushState(null, null, location.href);
});
window.addEventListener('popstate', function onPopState() {
    if (typeof confirmDialog === 'function') {
        confirmDialog('Keluar Aplikasi', 'Apakah Anda yakin ingin keluar dari halaman Cashbook dan kembali ke menu utama?', 'warning', () => {
            window.removeEventListener('popstate', onPopState);
            window.location.href = '/';
        });
        history.pushState(null, null, location.href);
    } else {
        window.location.href = '/';
    }
});

// ── Service Worker Registration ──
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').then(reg => {
      console.log('SW registered for offline caching.');
    }).catch(err => console.log('SW registration failed:', err));
  });
}
</script>

</body>
</html>
