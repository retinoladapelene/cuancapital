/**
 * UI Utilities, Theme, Toasts, and Modals for Cashbook
 */

// Theme toggle
function toggleTheme() {
    const root = document.documentElement;
    const current = root.getAttribute('data-theme') || 'dark';
    const newTheme = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', newTheme);
    localStorage.setItem('cb_theme', newTheme);
}

// Toast Notification System
const toastQ = [];
let isToasting = false;

function toast(msg, type = 'i') {
    toastQ.push({ msg, type });
    if (!isToasting) showNextToast();
}

function showNextToast() {
    if (!toastQ.length) { isToasting = false; return; }
    isToasting = true;
    const { msg, type } = toastQ.shift();
    const tEl = document.getElementById('cb-toast');
    if (!tEl) return; // Fallback if no toast element

    // Set colors & icons based on type
    const colors = {
        s: ['#10b981', 'rgba(16,185,129,0.1)'], // Success (Green)
        e: ['#ef4444', 'rgba(239,68,68,0.1)'],   // Error (Red)
        w: ['#f59e0b', 'rgba(245,158,11,0.1)'],  // Warning (Orange)
        i: ['#3b82f6', 'rgba(59,130,246,0.1)']   // Info (Blue)
    };
    const icons = {
        s: 'fa-check-circle',
        e: 'fa-triangle-exclamation',
        w: 'fa-exclamation-circle',
        i: 'fa-info-circle'
    };
    const c = colors[type] || colors.i;
    const i = icons[type] || icons.i;

    tEl.innerHTML = `<img src="${window.ASSET_URL || '/'}assets/icon/aksa_notif.png" style="position:absolute; left:0; top:0; height:100%; object-fit:cover; opacity:0.8; z-index:0; mix-blend-mode:overlay; border-radius:12px 0 0 12px; width:40px;"><div style="position:relative; z-index:1; display:flex; align-items:center; gap:12px;"><div style="width:28px; height:28px; border-radius:50%; background:${c[1]}; display:flex; align-items:center; justify-content:center; color:${c[0]};"><i class="fas ${i}"></i></div><div style="font-size:13px; font-weight:600; color:var(--text);">${msg}</div></div>`;
    tEl.style.borderLeft = `3px solid ${c[0]}`;

    requestAnimationFrame(() => {
        tEl.classList.add('show');
    });

    setTimeout(() => {
        requestAnimationFrame(() => {
            tEl.classList.remove('show');
            setTimeout(showNextToast, 300);
        });
    }, 3000);
}

// Custom Dropdown Helper (Used in forms)
function setCddDisplay(iconId, labelId, text, iconClass, color, focusNext = false) {
    const ico = document.getElementById(iconId);
    const lbl = document.getElementById(labelId);
    if (ico) {
        ico.className = 'fas ' + iconClass;
        ico.style.color = color || 'var(--text-muted)';
        if (color) ico.parentElement.style.background = color + '20';
        else ico.parentElement.style.background = 'var(--surface2)';
    }
    if (lbl) {
        lbl.textContent = text;
        lbl.style.color = 'var(--text)';
    }
    if (focusNext && document.getElementById(focusNext)) {
        setTimeout(() => document.getElementById(focusNext).focus(), 50);
    }
}

// Modal handling
function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.add('open');
        m.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('open');
        m.classList.remove('active');
        if (!document.querySelectorAll('.modal.active').length) {
            document.body.style.overflow = '';
            document.body.style.touchAction = ''; // reset manipulation if set
        }

        // Reset forms inside modal if they exist
        const forms = m.querySelectorAll('form');
        forms.forEach(f => {
            if (f.id !== 'form-edit-budget') f.reset();
        });

        // Reset custom dropdowns specifically
        const selects = m.querySelectorAll('select.hidden-input');
        selects.forEach(s => {
            s.value = '';
            // Generic reset for custom dropdown display if IDs match a pattern
            const baseId = s.id.replace('tx-', '').replace('bgt-', ''); // heuristic
            const lbl = document.getElementById(`cdd-${baseId}-lbl`);
            const ico = document.getElementById(`cdd-${baseId}-icon`);
            if (lbl) { lbl.textContent = `Pilih ${baseId}`; lbl.style.color = 'var(--text-muted)'; }
            if (ico) {
                ico.className = 'fas fa-chevron-down';
                ico.style.color = 'var(--text-muted)';
                ico.parentElement.style.background = 'var(--surface2)';
            }
        });

        // Reset specific UI states
        const bgtVal = document.getElementById('bgt-limit');
        if (bgtVal) bgtVal.value = '';
        const modalBgt = document.getElementById('modal-budget');
        if (modalBgt) modalBgt.dataset.editId = '';

        const instAccBal = document.getElementById('inst-account-balance');
        if (instAccBal) instAccBal.textContent = '';
    }
}

// Confirm Dialog Utility
function confirmDialog(title, message, type = 'danger', onConfirm) {
    // Support both old cmd-* IDs and new confirm-* IDs
    const getEl = (newId, oldId) => document.getElementById(newId) || document.getElementById(oldId);

    const titleEl = getEl('confirm-title', 'cmd-title');
    const msgEl = getEl('confirm-message', 'cmd-msg');
    const ic = getEl('confirm-icon', 'cmd-icon');
    const btn = getEl('confirm-ok', 'cmd-btn');

    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;

    // Icon styling
    if (ic) {
        if (type === 'danger') {
            ic.className = 'fas fa-triangle-exclamation';
            ic.style.color = 'var(--danger)';
            const wrap = document.getElementById('confirm-icon-wrap');
            if (wrap) { wrap.style.background = 'rgba(239,68,68,0.12)'; }
        } else if (type === 'warning') {
            ic.className = 'fas fa-exclamation-circle';
            ic.style.color = 'var(--warning)';
            const wrap = document.getElementById('confirm-icon-wrap');
            if (wrap) { wrap.style.background = 'rgba(245,158,11,0.12)'; }
        } else {
            ic.className = 'fas fa-info-circle';
            ic.style.color = 'var(--info)';
            const wrap = document.getElementById('confirm-icon-wrap');
            if (wrap) { wrap.style.background = 'rgba(59,130,246,0.12)'; }
        }
    }

    // Confirm Button
    if (btn) {
        btn.className = `btn btn-${type === 'danger' ? 'danger' : 'accent'}`;
        btn.textContent = type === 'danger' ? 'Ya, Hapus' : 'Lanjutkan';
        btn.style.background = type === 'danger' ? 'var(--danger)' : '';
        btn.onclick = () => {
            closeModal('modal-confirm');
            if (typeof onConfirm === 'function') onConfirm();
        };
    }

    openModal('modal-confirm');
}

// ── ╔═══════════════════════════════════╗ ──
// ── ║  SINGLE ACTIVE VIEW ROUTER        ║ ──
// ── ╚═══════════════════════════════════╝ ──
let activeTab = 'overview';
let activeViewCleanup = null; // Holds the cleanup fn from the current view
let isSwitching = false;      // Tab Debounce Guard

async function switchMainTab(tabId, subId = null, btnEl = null) {
    // 1. DEBOUNCE: Prevent collision from spamming tab buttons
    if (isSwitching) return;
    isSwitching = true;

    try {
        activeTab = tabId;
        if (window.cashbookState) window.cashbookState.activeTab = tabId;

        // 2. UPDATE NAVIGATION ACTIVE STATES
        document.querySelectorAll('.mob-nav-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
        const mobBtn = document.getElementById(`mob-btn-${tabId}`);
        if (mobBtn) mobBtn.classList.add('active');
        const ntabEl = document.getElementById(`ntab-${tabId}`);
        if (ntabEl) ntabEl.classList.add('active');

        // 3. VIEW CLEANUP: Destroy charts, disconnect observers, reset flags
        if (typeof activeViewCleanup === 'function') {
            try { activeViewCleanup(); } catch (e) { console.warn('View cleanup error:', e); }
            activeViewCleanup = null;
        }

        // 4. CLEAR THE CONTAINER (Single Active View)
        const container = document.getElementById('cashbook-app-container');
        if (!container) { isSwitching = false; return; }

        // 4. INSTANT UX FEEDBACK: Skeleton Loader
        container.innerHTML = `
            <div style="padding: 20px;">
                <div style="height: 120px; border-radius: 16px; background: var(--surface2); animation: pulse 1.5s infinite; margin-bottom: 20px;"></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div style="height: 80px; border-radius: 12px; background: var(--surface2); animation: pulse 1.5s infinite;"></div>
                    <div style="height: 80px; border-radius: 12px; background: var(--surface2); animation: pulse 1.5s infinite;"></div>
                </div>
                <div style="height: 250px; border-radius: 16px; background: var(--surface2); animation: pulse 1.5s infinite;"></div>
            </div>
            <style>@keyframes pulse { 0%, 100% { opacity: 0.7; } 50% { opacity: 0.3; } }</style>
        `;
        window.scrollTo({ top: 0, behavior: 'auto' });

        // 5. LAZY LOAD the right module & CSS
        const v = "?v=2.2"; // hardcoded cache buster
        const moduleMap = {
            overview: { name: 'overview', src: 'assets/js/cashbook/overview.js' + v, css: 'assets/css/cashbook-overview.css' + v },
            transaksi: { name: 'transactions', src: 'assets/js/cashbook/transactions.js' + v, css: 'assets/css/cashbook-transactions.css' + v },
            laporan: { name: 'reports', src: 'assets/js/cashbook/reports.js' + v, css: 'assets/css/cashbook-laporan.css' + v },
            anggaran: { name: 'budgets', src: 'assets/js/cashbook/budgets.js' + v, css: 'assets/css/cashbook-anggaran.css' + v },
            utang: { name: 'debts', src: 'assets/js/cashbook/debts.js' + v, css: 'assets/css/cashbook-debts.css' + v },
        };
        const mod = moduleMap[tabId];
        if (mod) {
            await Promise.all([
                loadModule(mod.name, window.ASSET_URL + mod.src),
                loadCSS(mod.name, window.ASSET_URL + mod.css)
            ]);
        }

        // 6. DISPATCH: call the correct renderXTab(container) and save its cleanup fn
        const renderMap = {
            overview: window.renderOverviewTab,
            transaksi: window.renderTransactionsTab,
            laporan: window.renderReportsTab,
            anggaran: window.renderBudgetsTab,
            utang: window.renderDebtsTab,
        };
        const renderFn = renderMap[tabId];
        if (typeof renderFn === 'function') {
            activeViewCleanup = renderFn(container) || null;
        } else {
            container.innerHTML = '<div class="empty" style="padding:40px;"><i class="fas fa-exclamation-triangle"></i> Tab tidak ditemukan.</div>';
        }

    } catch (e) {
        console.error('switchMainTab error:', e);
    } finally {
        // 7. Always unlock debounce when done
        isSwitching = false;
    }
}
window.switchMainTab = switchMainTab;

// ── Lazy Loading Utility ──
const _loadedModules = new Set();
async function loadModule(name, src) {
    if (_loadedModules.has(name)) return true;
    return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = src;
        s.onload = () => { _loadedModules.add(name); resolve(true); };
        s.onerror = reject;
        document.body.appendChild(s);
    });
}
window.loadModule = loadModule;

const _loadedCSS = new Set();
async function loadCSS(name, src) {
    if (_loadedCSS.has(name)) return true;
    return new Promise((resolve, reject) => {
        const l = document.createElement('link');
        l.rel = 'stylesheet';
        l.href = src;
        l.onload = () => { _loadedCSS.add(name); resolve(true); };
        l.onerror = () => { console.warn('Failed to load CSS:', src); resolve(false); }; // Don't block JS execution
        document.head.appendChild(l);
    });
}
window.loadCSS = loadCSS;

// prepTx: pre-set the transaction type before opening modal
function prepTx(type = 'expense') {
    // Wait a tick so modal DOM is ready
    setTimeout(() => {
        if (typeof setType === 'function') setType(type);
        // Also set the date to today if blank
        const dateEl = document.getElementById('tx-date');
        if (dateEl && !dateEl.value) {
            const today = new Date().toISOString().split('T')[0];
            dateEl.value = today;
        }
    }, 50);
}

// Expose to window
window.toggleTheme = toggleTheme;
window.toast = toast;
window.setCddDisplay = setCddDisplay;
window.openModal = openModal;
window.closeModal = closeModal;
window.confirmDialog = confirmDialog;
window.switchMainTab = switchMainTab;
window.prepTx = prepTx;

// ── Debounce Utility ──
window.debounce = function (func, wait = 300) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

// ── closeOut: close modal when clicking the backdrop ──
function closeOut(backdropEl) {
    const id = backdropEl?.id;
    if (id) closeModal(id);
}
window.closeOut = closeOut;

// ── toggleCdd: open/close custom dropdown menu ──
function toggleCdd(cddId) {
    const cdd = document.getElementById(cddId);
    if (!cdd) return;
    const menu = cdd.querySelector('.cdd-menu');
    if (!menu) return;

    const isOpen = menu.classList.contains('open');

    // Close all other CDDs first
    document.querySelectorAll('.cdd-menu.open').forEach(m => m.classList.remove('open'));

    if (!isOpen) {
        menu.classList.add('open');
        // Close on outside click
        const handler = (e) => {
            if (!cdd.contains(e.target)) {
                menu.classList.remove('open');
                document.removeEventListener('click', handler, true);
            }
        };
        setTimeout(() => document.addEventListener('click', handler, true), 10);
    }
}
window.toggleCdd = toggleCdd;
