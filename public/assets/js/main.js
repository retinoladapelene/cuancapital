import { initAuthListener, logoutUser } from './core/auth-engine.js';
import { mainController } from './core/main-controller.js';
import { businessCore } from './core/BusinessCore.js'; // Import Core
import { calculateGoal, updateCalculator } from './calculator.js';
import { initTheme } from './theme.js';
import { select, listen, showToast, showConfirm } from './utils/helpers.js';
import { initScrollEffects, initBackToTop } from './ui.js';
import { educationEngine } from './features/education-engine.js';

// ... existing code ...

const logoutBtn = select('#btn-logout');
const dropdownLogoutBtn = select('#dropdown-logout-btn');

// Global smooth scroll handler with safety checks for info icons
window.safeScrollTo = function (id, e) {
    if (e && e.target && e.target.closest('.glossary-icon')) return; // Ignore if clicking info icon
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const handleLogout = () => {
    showConfirm("Apakah Anda yakin ingin keluar?", () => logoutUser());
};

if (logoutBtn) listen(logoutBtn, 'click', handleLogout);
if (dropdownLogoutBtn) listen(dropdownLogoutBtn, 'click', handleLogout);



/**
 * Initialize dynamic greeting based on time and user name
 */
function initGreeting() {
    const greetingContainer = select('#greeting-container');
    const greetingText = select('#greeting-text');

    if (!greetingContainer || !greetingText) return;

    // Get user display name from session storage
    const displayName = sessionStorage.getItem('cuan_user_display_name') || 'Sobat Cuan';

    // Get current hour
    const hour = new Date().getHours();

    // Determine greeting based on time
    let timeGreeting = '';
    if (hour >= 5 && hour < 11) {
        timeGreeting = 'Selamat Pagi';
    } else if (hour >= 11 && hour < 15) {
        timeGreeting = 'Selamat Siang';
    } else if (hour >= 15 && hour < 18) {
        timeGreeting = 'Selamat Sore';
    } else {
        timeGreeting = 'Selamat Malam';
    }

    // Set greeting text
    greetingText.textContent = `${timeGreeting}, ${displayName}`;

    // Set avatar
    const avatarImg = select('#hero-avatar');
    if (avatarImg) {
        const storedAvatar = sessionStorage.getItem('cuan_user_avatar');
        // Use stored avatar or generate UI Avatar regular fallback
        const avatarUrl = storedAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=10b981&color=fff`;
        avatarImg.src = avatarUrl;
    }

    // Show greeting container
    greetingContainer.classList.remove('hidden');
}

/**
 * Initialize custom dropdown logic
 */
function initCustomDropdowns() {
    const dropdowns = document.querySelectorAll('.custom-dropdown');

    dropdowns.forEach(dropdown => {
        const btn = dropdown.querySelector('.dropdown-btn');
        const menu = dropdown.querySelector('.dropdown-menu');
        const items = dropdown.querySelectorAll('.dropdown-item');
        const hiddenInput = dropdown.querySelector('input[type="hidden"]');
        const selectedValueSpan = dropdown.querySelector('.selected-value');

        if (!btn || !menu) return;

        // Toggle menu
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close other dropdowns first
            document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                if (m !== menu) m.classList.remove('show');
            });
            menu.classList.toggle('show');
        });

        // Handle item selection
        items.forEach(item => {
            item.addEventListener('click', () => {
                const value = item.getAttribute('data-value');
                const text = item.textContent;

                // Update UI
                if (selectedValueSpan) selectedValueSpan.textContent = text;
                items.forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                // Update hidden input and trigger events
                if (hiddenInput) {
                    hiddenInput.value = value;
                    // Trigger native events so other scripts react (e.g., businessCore, mainController)
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Close menu
                menu.classList.remove('show');
            });
        });
    });

    // Close all dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    });
}


/**
 * Fetch system broadcast from admin settings and display banner if active.
 */
async function initSystemBroadcast() {
    try {
        const res = await fetch('/api/system/settings');
        if (!res.ok) return;
        const data = await res.json();
        const broadcast = data.broadcast;

        if (broadcast && broadcast.isActive && broadcast.message) {
            const banner = document.getElementById('system-broadcast');
            const nav = document.getElementById('main-nav');
            const textEl = document.getElementById('broadcast-text');
            if (banner && textEl) {
                textEl.textContent = ' ' + broadcast.message;
                banner.classList.remove('hidden');
                // Push nav down so it sits below the broadcast banner
                const bannerH = banner.offsetHeight;
                if (nav) nav.style.top = bannerH + 'px';
            }
        }
    } catch (e) {
        console.warn('Could not load system broadcast:', e);
    }
}

/**
 * FEATURE FLAG ENFORCEMENT
 * Reads flags from /api/system/settings and hides sections + nav items
 * that belong to disabled features.
 *
 * Flag key         → Selectors to hide when flag is OFF
 * ──────────────── ──────────────────────────────────────
 * calculator       → #profit-simulator-section, #goal-planner

 * mini_course      → #mini-course-teaser, [data-feat="mini_course"]
 * gamification     → #xp-bar-wrapper, [data-feat="gamification"]
 * mentor_lab       → [data-feat="mentor_lab"]
 */
const FEATURE_SELECTOR_MAP = {
    calculator: ['#profit-simulator-section', '#goal-planner'],

    mini_course: ['#mini-course-teaser', '[data-feat="mini_course"]'],
    gamification: ['#xp-bar-wrapper', '[data-feat="gamification"]'],
    mentor_lab: ['[data-feat="mentor_lab"]'],
    registration: [], // Controlled server-side; no UI element to hide here
};

async function enforceFeatureFlags() {
    try {
        const res = await fetch('/api/system/settings');
        if (!res.ok) return;
        const { flags } = await res.json();
        if (!flags) return;

        Object.entries(FEATURE_SELECTOR_MAP).forEach(([flag, selectors]) => {
            const isEnabled = flags[flag] !== false; // default ON if missing
            selectors.forEach(sel => {
                document.querySelectorAll(sel).forEach(el => {
                    if (isEnabled) {
                        el.style.removeProperty('display');
                    } else {
                        el.style.display = 'none';
                    }
                });
            });
        });
    } catch (e) {
        console.warn('Could not enforce feature flags:', e);
    }
}

window.onload = async function () { // Make it async

    console.log('UI V.7 Initialized - Debugging Mode On');



    try {
        initTheme();
        initSystemBroadcast(); // Load system broadcast banner if active
        enforceFeatureFlags(); // Hide sections of disabled features
        initScrollEffects();
        initBackToTop();
        // Global smooth scroll handler with safety checks for info icons
        window.safeScrollTo = function (id, e) {
            if (e && e.target && e.target.closest('.glossary-icon')) return; // Ignore if clicking info icon
            const el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth' });
        };

        initAuthListener();

        mainController.init();

        // Phase 19: Smooth Page Section Reveal
        document.querySelectorAll('.reveal').forEach(el => {
            requestAnimationFrame(() => {
                el.classList.add('active');
            });
        });

        // Initialize Core (Loads data from API)
        await businessCore.init();

        // Initialize Education Engine
        await educationEngine.init();

        // Initialize custom dropdowns
        initCustomDropdowns();

        // Initialize dynamic greeting
        initGreeting();


        const reloadBtn = select('#btn-reload');
        if (reloadBtn) {
            listen(reloadBtn, 'click', () => {
                location.reload();
            });
        }

        // Logout listeners are already handled above via const logoutBtn / dropdownLogoutBtn

        const btnCalculateGoal = select('button[onclick="calculateGoal()"]');

        const btnCalcGoal = select('#btn-calculate-goal');
        if (btnCalcGoal) listen(btnCalcGoal, 'click', calculateGoal);

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js?v=15')
                .then(() => console.log('SW Registered'))
                .catch(e => console.log('SW Fail:', e));
        }

        calculateGoal();
        updateCalculator();
    } catch (e) {
        console.error("Main JS Error:", e);
        showToast("Terjadi kesalahan pada aplikasi: " + e.message + ". Silakan refresh halaman.", "error");
    }
};
