/**
 * Blueprint Mascot Animation System
 * ─────────────────────────────────────────────────────────────────────────────
 * State machine + micro-interaction engine for the blueprint save flow.
 *
 * Flow: Save button click → paper scroll flies on bezier arc → enters bag →
 *       mascot bounces → bag state switches empty → full.
 *
 * Hardenings:
 *  - lastAnimationTimestamp debounce (1200ms guard)
 *  - offset-path CSS motion path with translate fallback for Safari/WebView
 *  - pointer-events global lock during T1→T4
 *  - DOM cleanup after animation (no memory leak)
 *  - Sidebar-open guard (skip projectile, only increment counter)
 *  - transform-origin: bottom center on mascot (weighted bounce feel)
 *  - will-change: transform on projectile (GPU layer prep)
 *  - Idle animation after 5s inactivity
 */

(function () {
    'use strict';

    // ── Assets ────────────────────────────────────────────────────────────────
    const ASSETS = {
        empty: '/assets/icon/aksa_emptybag.png',
        full: '/assets/icon/Aksa_fullbag.png',
    };

    // ── State Machine ─────────────────────────────────────────────────────────
    const state = {
        bagState: 'full',   // 'empty' | 'full' — Default to full (closed)
        storageOpen: false,
        animationRunning: false,
        blueprintCount: 0,
        lastAnimationTimestamp: 0,        // debounce guard
    };

    // ── DOM Refs ──────────────────────────────────────────────────────────────
    const els = {
        get mascotBtn() { return document.getElementById('open-blueprints-btn'); },
        get mascotImg() { return document.getElementById('mascot-bag-img'); },
        get badge() { return document.getElementById('mascot-badge'); },
        get animLayer() { return document.getElementById('anim-layer'); },
        get sidebar() { return document.getElementById('blueprints-sidebar'); },
        get closeSidebar() { return document.getElementById('close-sidebar-btn'); },
    };

    // ── CSS motion-path support detection ────────────────────────────────────
    const supportsOffsetPath = CSS.supports('offset-path', "path('M0,0 L1,1')");

    // ── Idle Animation ────────────────────────────────────────────────────────
    let idleTimer = null;

    function resetIdleTimer() {
        clearTimeout(idleTimer);
        stopIdleAnimation();
        idleTimer = setTimeout(startIdleAnimation, 5000);
    }

    function startIdleAnimation() {
        const img = els.mascotImg;
        if (!img || state.animationRunning) return;
        img.style.animation = 'mascot-idle 2.5s ease-in-out infinite';
    }

    function stopIdleAnimation() {
        const img = els.mascotImg;
        if (img) img.style.animation = '';
    }

    // ── Badge ─────────────────────────────────────────────────────────────────
    function updateBadge() {
        const badge = els.badge;
        if (!badge) return;
        if (state.blueprintCount <= 0) {
            badge.classList.add('hidden');
        } else {
            badge.classList.remove('hidden');
            badge.textContent = state.blueprintCount > 9 ? '9+' : state.blueprintCount;
        }
    }

    // ── Bag State ─────────────────────────────────────────────────────────────
    function setBagState(newState) {
        state.bagState = newState;
        const img = els.mascotImg;
        // Pulse ring logic removed per user request
        if (!img) return;
        img.src = newState === 'full' ? ASSETS.full : ASSETS.empty;
        img.alt = newState === 'full' ? 'Bag Full' : 'Bag Empty';
    }

    // ── Pointer Lock ──────────────────────────────────────────────────────────
    function lockInteraction() {
        document.body.style.pointerEvents = 'none';
        // Always allow the anim layer itself (it's pointer-events-none anyway)
    }

    function unlockInteraction() {
        document.body.style.pointerEvents = '';
    }

    // ── Mascot Bounce Reaction ────────────────────────────────────────────────
    function playMascotReaction() {
        const img = els.mascotImg;
        if (!img) return;

        img.style.transformOrigin = 'bottom center';

        img.animate([
            { transform: 'scale(1) rotate(0deg)', offset: 0 },
            { transform: 'scale(1.12) rotate(-4deg)', offset: 0.3 },
            { transform: 'scale(0.95) rotate(2deg)', offset: 0.6 },
            { transform: 'scale(1.04) rotate(0deg)', offset: 0.8 },
            { transform: 'scale(1) rotate(0deg)', offset: 1 },
        ], {
            duration: 400,
            easing: 'ease-out',
            fill: 'forwards',
        });
    }


    // ── Mascot Bounce Reaction ────────────────────────────────────────────
    function playMascotReaction() {
        const img = els.mascotImg;
        if (!img) return;
        img.style.transformOrigin = 'bottom center';
        img.animate([
            { transform: 'scale(1) rotate(0deg)', offset: 0 },
            { transform: 'scale(1.12) rotate(-4deg)', offset: 0.3 },
            { transform: 'scale(0.95) rotate(2deg)', offset: 0.6 },
            { transform: 'scale(1.04) rotate(0deg)', offset: 0.8 },
            { transform: 'scale(1) rotate(0deg)', offset: 1 },
        ], { duration: 400, easing: 'ease-out', fill: 'forwards' });
    }

    // ── Main: Trigger Save Animation ────────────────────────────────────
    function triggerSaveAnimation() {
        // Debounce guard (1200ms)
        if (Date.now() - state.lastAnimationTimestamp < 1200) return;
        if (state.animationRunning) return;

        state.lastAnimationTimestamp = Date.now();
        state.blueprintCount++;
        updateBadge();

        // Play bounce reaction without any projectile
        setBagState('empty');
        playMascotReaction();
        setTimeout(() => setBagState('full'), 400);
        resetIdleTimer();
    }

    // ── Sidebar Control ───────────────────────────────────────────────────────
    function openStorage() {
        state.storageOpen = true;
        // Always open bag (empty) when sidebar opens
        setBagState('empty');

        // Slide mascot left so sidebar doesn't cover it
        const btn = els.mascotBtn;
        if (btn) {
            btn.style.transition = 'right 0.3s cubic-bezier(0.4,0,0.2,1)';
            btn.style.right = '336px'; // 320px sidebar + 16px gap
        }
    }

    function closeStorage() {
        state.storageOpen = false;
        // Always close bag (full) when sidebar closes
        setBagState('full');

        // Slide mascot back to original position
        const btn = els.mascotBtn;
        if (btn) {
            btn.style.transition = 'right 0.3s cubic-bezier(0.4,0,0.2,1)';
            btn.style.right = '24px'; // matches bottom-6 right-6 = 24px
        }
    }

    // ── Sidebar Button Hooks ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        // Hook sidebar toggle (open/close)
        const openBtn = els.mascotBtn;
        if (openBtn) {
            openBtn.addEventListener('click', () => {
                if (state.animationRunning) return;

                const sidebar = els.sidebar;
                if (!sidebar) return;

                // Toggle logic
                if (state.storageOpen) {
                    // Close
                    sidebar.classList.add('translate-x-full');
                    closeStorage();
                } else {
                    // Open
                    sidebar.classList.remove('translate-x-full');
                    openStorage();
                    // Load blueprints if available
                    if (typeof window.loadBlueprintsExternal === 'function') {
                        window.loadBlueprintsExternal();
                    }
                }
            });


        }

        // Hook sidebar close
        const closeBtn = els.closeSidebar;
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                const sidebar = els.sidebar;
                if (sidebar) sidebar.classList.add('translate-x-full');
                closeStorage();
            });
        }

        // ── Inject idle + hover keyframe styles ───────────────────────────────
        if (!document.getElementById('mascot-keyframes')) {
            const style = document.createElement('style');
            style.id = 'mascot-keyframes';
            style.textContent = `
                /* Idle float */
                @keyframes mascot-idle {
                    0%   { transform: translateY(0px) rotate(0deg);   }
                    25%  { transform: translateY(-6px) rotate(-2deg); }
                    50%  { transform: translateY(-9px) rotate(1.5deg);}
                    75%  { transform: translateY(-4px) rotate(-1deg); }
                    100% { transform: translateY(0px) rotate(0deg);   }
                }

                /* Jiggle on hover enter */
                @keyframes mascot-jiggle {
                    0%   { transform: rotate(0deg) scale(1);     }
                    15%  { transform: rotate(-6deg) scale(1.05); }
                    35%  { transform: rotate(5deg) scale(1.08);  }
                    55%  { transform: rotate(-4deg) scale(1.06); }
                    75%  { transform: rotate(3deg) scale(1.04);  }
                    90%  { transform: rotate(-1deg) scale(1.02); }
                    100% { transform: rotate(0deg) scale(1.1);   }
                }

                /* Shadow glow on hover */
                #mascot-bag-img:not(.animating) {
                    transition: filter 0.3s ease, transform 0.3s ease;
                }
                #open-blueprints-btn:hover #mascot-bag-img {
                    filter: drop-shadow(0 12px 24px rgba(0,0,0,0.4))
                            drop-shadow(0 0 16px rgba(16,185,129,0.5)) !important;
                    transform: translateY(-4px) scale(1.1) rotate(-3deg);
                }
                #open-blueprints-btn:active #mascot-bag-img {
                    transform: translateY(0px) scale(0.96);
                    transition: transform 0.08s ease;
                }

                /* Mascot button smooth slide transition */
                #open-blueprints-btn {
                    transition: right 0.3s cubic-bezier(0.4,0,0.2,1);
                }
            `;
            document.head.appendChild(style);
        }

        // Start idle timer
        document.addEventListener('pointermove', resetIdleTimer, { passive: true });
        document.addEventListener('keydown', resetIdleTimer, { passive: true });
        resetIdleTimer();

        // Sync mascot badge with existing blueprint count on load
        // (profit-simulator will update state.blueprintCount via MascotSystem.setCount)
    });

    // ── Public API ────────────────────────────────────────────────────────────
    window.MascotSystem = {
        triggerSaveAnimation,
        openStorage,
        closeStorage,
        setBagState,
        setCount(n) {
            state.blueprintCount = Math.max(0, n);
            updateBadge();
        },
        incrementCount() {
            state.blueprintCount++;
            updateBadge();
        },
    };

})();
