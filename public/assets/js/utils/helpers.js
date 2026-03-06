/**
 * Menampilkan Toast Notification
 * @param {string} message - Pesan yang akan ditampilkan
 * @param {string} type - 'success', 'error', 'info', atau 'warning'
 */
export const showToast = (message, type = 'info', autoClose = true) => {
    // Auto-create container jika belum ada (fallback safety)
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed z-[9999] flex flex-col items-center gap-3 pointer-events-none';
        container.style.cssText = 'top:24px; left:50%; transform:translateX(-50%); width:90vw; max-width:24rem;';
        document.body.appendChild(container);
    }

    // ── Per-type config ──────────────────────────────────────────────────────
    const cfg = {
        success: {
            label: 'BERHASIL',
            gradient: 'linear-gradient(135deg, #071a12 0%, #0d2618 60%, #071a12 100%)',
            border: 'rgba(16,185,129,0.55)',
            glow: '0 0 32px rgba(16,185,129,0.25), 0 20px 48px rgba(0,0,0,0.5)',
            bar: '#10b981',
            badge: 'rgba(16,185,129,0.15)',
            badgeText: '#34d399',
            icon: '✓',
        },
        error: {
            label: 'GAGAL',
            gradient: 'linear-gradient(135deg, #1a0707 0%, #260d0d 60%, #1a0707 100%)',
            border: 'rgba(244,63,94,0.55)',
            glow: '0 0 32px rgba(244,63,94,0.25), 0 20px 48px rgba(0,0,0,0.5)',
            bar: '#f43f5e',
            badge: 'rgba(244,63,94,0.15)',
            badgeText: '#fb7185',
            icon: '✕',
        },
        warning: {
            label: 'PERINGATAN',
            gradient: 'linear-gradient(135deg, #1a1407 0%, #261c0d 60%, #1a1407 100%)',
            border: 'rgba(245,158,11,0.55)',
            glow: '0 0 32px rgba(245,158,11,0.2), 0 20px 48px rgba(0,0,0,0.5)',
            bar: '#f59e0b',
            badge: 'rgba(245,158,11,0.15)',
            badgeText: '#fcd34d',
            icon: '⚠',
        },
        info: {
            label: 'INFO',
            gradient: 'linear-gradient(135deg, #070d1a 0%, #0d1726 60%, #070d1a 100%)',
            border: 'rgba(59,130,246,0.55)',
            glow: '0 0 32px rgba(59,130,246,0.2), 0 20px 48px rgba(0,0,0,0.5)',
            bar: '#3b82f6',
            badge: 'rgba(59,130,246,0.15)',
            badgeText: '#93c5fd',
            icon: 'i',
        },
    };
    const c = cfg[type] || cfg.info;
    const toastId = 'toast-' + Date.now();

    // ── Outer wrapper (handles flex placement and animation) ───
    const wrapper = document.createElement('div');
    wrapper.style.cssText = `
        width: 100%;
        margin-top: 16px;
        pointer-events: auto;
        transform: translateY(-20px) scale(0.95);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
        position: relative;
    `;

    // Map FontAwesome icons
    const faIcon = type === 'success' ? 'check-circle' :
        type === 'error' ? 'times-circle' :
            type === 'warning' ? 'exclamation-circle' : 'info-circle';

    // ── Toast card ───────────────────────────────────────────────────────────
    wrapper.innerHTML = `
        <div id="${toastId}"
             class="group"
             onmouseenter="this.style.transform='translateY(-2px) scale(1.01)'; this.style.boxShadow='${c.glow.replace('0 20px 48px', '0 24px 50px')}';"
             onmouseleave="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='${c.glow}';"
             style="
                position: relative;
                overflow: visible;
                background: ${c.gradient};
                border: 1px solid ${c.border};
                border-radius: 20px;
                box-shadow: ${c.glow};
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), box-shadow 0.3s ease;
                display: flex;
                align-items: center;
                padding: 16px 20px 16px 65px;
                gap: 16px;
                z-index: 10;
             ">

            <!-- Aksa mascot overlapping left border -->
            <img src="/assets/icon/aksa_notif2.png"
                 style="position: absolute; left: -60px; bottom: -30px; height: 150px; width: 150px; object-fit: contain; z-index: 30; pointer-events: none; filter: drop-shadow(4px 4px 8px rgba(0,0,0,0.5));"
                 alt="Mascot Notification" />

            <!-- Left accent line -->
            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: ${c.bar}; border-radius: 20px 0 0 20px; z-index: 10;"></div>

            <!-- Content Area -->
            <div style="flex: 1; min-width: 0; padding-right: 8px; z-index: 10; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 4px;">
                    <i class="fas fa-${faIcon}" style="color: ${c.badgeText}; font-size: 14px; filter: drop-shadow(0 0 4px ${c.border});"></i>
                    <span style="
                        font-size: 10px; font-weight: 800; letter-spacing: 0.1em;
                        color: ${c.badgeText}; text-transform: uppercase;
                        opacity: 0.9;
                    ">${c.label}</span>
                </div>
                <div style="font-size: 13px; font-weight: 500; color: #f8fafc; line-height: 1.5; word-break: break-word;">
                    ${message}
                </div>
            </div>

            <!-- Close Button -->
            <button onclick="this.closest('[id^=toast-]').parentElement.remove()"
                    style="
                        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
                        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
                        color: rgba(148,163,184,0.7); font-size: 14px; cursor: pointer;
                        display: flex; align-items: center; justify-content: center;
                        transition: all 0.2s ease; margin-left: auto; z-index: 10;
                    "
                    onmouseenter="this.style.background='rgba(255,255,255,0.15)'; this.style.color='#fff'; this.style.transform='scale(1.1) rotate(90deg)';"
                    onmouseleave="this.style.background='rgba(255,255,255,0.05)'; this.style.color='rgba(148,163,184,0.7)'; this.style.transform='scale(1) rotate(0deg)';"
            >
                <i class="fas fa-times"></i>
            </button>

            <!-- Background subtle glow -->
            <div style="position: absolute; right: -20px; top: -20px; width: 100px; height: 100px; background: radial-gradient(circle, ${c.border} 0%, transparent 70%); opacity: 0.2; pointer-events: none; border-radius: 50%;"></div>

            <!-- Progress bar -->
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: rgba(0,0,0,0.2); z-index: 20; overflow: hidden; border-radius: 0 0 20px 20px;">
                <div id="${toastId}-bar"
                     style="height: 100%; width: 100%; background: linear-gradient(to right, ${c.bar}88, ${c.bar}); transition: width 3.8s linear; border-radius: 0 4px 4px 0;">
                </div>
            </div>
        </div>
    `;


    container.appendChild(wrapper);

    // ── Animate In ───────────────────────────────────────────────────────────
    requestAnimationFrame(() => {
        wrapper.style.transform = 'translateY(0)';
        wrapper.style.opacity = '1';
        // Start progress bar drain
        if (autoClose) {
            setTimeout(() => {
                const bar = document.getElementById(toastId + '-bar');
                if (bar) bar.style.width = '0%';
            }, 100);
        } else {
            // Hide progress bar wrapper if persistent
            const bar = document.getElementById(toastId + '-bar');
            if (bar && bar.parentElement) bar.parentElement.style.display = 'none';
        }
    });

    // ── Auto Remove ──────────────────────────────────────────────────────────
    if (autoClose) {
        setTimeout(() => {
            wrapper.style.transform = 'translateY(-12px)';
            wrapper.style.opacity = '0';
            setTimeout(() => wrapper.remove(), 350);
        }, 4000);
    }
};



/**
 * Shows a custom confirmation modal
 * @param {string} message - The message to display
 * @param {Function} onConfirm - Callback function when user confirms
 * @param {Function} onCancel - Callback function when user cancels (optional)
 */
export const showConfirm = (message, onConfirm, onCancel = null) => {
    // Create modal container
    const modalId = 'custom-confirm-modal';
    const existingModal = document.getElementById(modalId);
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300';

    modal.innerHTML = `
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300 border border-slate-200 dark:border-slate-700">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mb-4 text-rose-500 dark:text-rose-400">
                    <i class="fas fa-question text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Konfirmasi</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">${message}</p>
                <div class="flex gap-3 w-full">
                    <button id="btn-cancel-confirm" class="flex-1 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-xl font-bold transition-colors">
                        Batal
                    </button>
                    <button id="btn-yes-confirm" class="flex-1 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition-colors shadow-lg shadow-rose-500/20">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Animate In
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
        modal.querySelector('div').classList.add('scale-100');
    });

    // Handlers
    const close = () => {
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => modal.remove(), 300);
    };

    document.getElementById('btn-cancel-confirm').onclick = () => {
        close();
        if (onCancel) onCancel();
    };

    document.getElementById('btn-yes-confirm').onclick = () => {
        close();
        if (onConfirm) onConfirm();
    };
};

/**
 * Format Angka ke Format Mata Uang IDR (Default)
 * @param {number} number 
 * @param {string} currencyCode 
 * @returns {string}
 */
export const formatCurrency = (number, currencyCode = 'IDR') => {
    if (number >= 1000000) {
        let abbrev = '';
        let val = number;
        if (number >= 1000000000000) {
            val = number / 1000000000000;
            abbrev = ' T';
        } else if (number >= 1000000000) {
            val = number / 1000000000;
            abbrev = ' M';
        } else {
            val = number / 1000000;
            abbrev = ' jt';
        }
        return 'Rp ' + val.toLocaleString('id-ID', { maximumFractionDigits: 4 }) + abbrev;
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currencyCode,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

/**
 * Sanitasi Input String Sederhana
 * @param {string} str 
 * @returns {string}
 */
export const sanitizeInput = (str) => {
    const temp = document.createElement('div');
    temp.textContent = str;
    return temp.innerHTML;
};

// Log Activity (Stubbed / Console only for now)
export const logActivity = async (userId, action, details) => {
    console.log(`[ACTIVITY] ${userId} - ${action}: ${details}`);
};

/**
 * DOM Selector Helper
 * @param {string} selector 
 * @param {Element} scope 
 * @returns {Element}
 */
export const select = (selector, scope = document) => {
    return scope.querySelector(selector);
};

/**
 * DOM Select All Helper
 * @param {string} selector 
 * @param {Element} scope 
 * @returns {NodeList}
 */
export const selectAll = (selector, scope = document) => {
    return scope.querySelectorAll(selector);
};

/**
 * Event Listener Helper
 * @param {Element|string} target 
 * @param {string} event 
 * @param {Function} callback 
 * @param {Element} scope 
 */
export const listen = (target, event, callback, scope = document) => {
    if (typeof target === 'string') {
        const elements = selectAll(target, scope);
        elements.forEach(el => el.addEventListener(event, callback));
    } else if (target instanceof NodeList || Array.isArray(target)) {
        target.forEach(el => el.addEventListener(event, callback));
    } else if (target instanceof Element || target === window || target === document) {
        target.addEventListener(event, callback);
    }
};

/**
 * Detect Device and Browser Info
 * @returns {{device: string, browser: string}}
 */
export const getDeviceInfo = () => {
    const ua = navigator.userAgent;
    let device = "Desktop";
    let browser = "Unknown";

    // Detect Device
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua)) {
        device = "Mobile";
    }

    // Detect Browser
    if (ua.indexOf("Firefox") > -1) {
        browser = "Firefox";
    } else if (ua.indexOf("SamsungBrowser") > -1) {
        browser = "Samsung Internet";
    } else if (ua.indexOf("Opera") > -1 || ua.indexOf("OPR") > -1) {
        browser = "Opera";
    } else if (ua.indexOf("Trident") > -1) {
        browser = "Internet Explorer";
    } else if (ua.indexOf("Edge") > -1) {
        browser = "Edge";
    } else if (ua.indexOf("Chrome") > -1) {
        browser = "Chrome";
    } else if (ua.indexOf("Safari") > -1) {
        browser = "Safari";
    }

    return { device, browser };
};

/**
 * Format Date to ID-ID Locale
 * @param {string} dateString 
 * @returns {string}
 */
export const formatDate = (dateString, includeTime = true) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const options = {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    };
    if (includeTime) {
        options.hour = '2-digit';
        options.minute = '2-digit';
    }
    return new Intl.DateTimeFormat('id-ID', options).format(date);
};

// ── Expose as globals so plain (non-module) scripts can use them ──────────────
window.showToast = showToast;
window.showConfirm = showConfirm;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
