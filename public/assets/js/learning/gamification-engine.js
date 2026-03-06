/**
 * CuanCapital Experience OS: Gamification Engine (Phase 3B)
 * Server-synced XP & Level system.
 * - Fetches XP from /api/learning/progress on load
 * - POSTs action keys to /api/learning/xp on each reward
 * - Falls back to localStorage if not authenticated or API is down
 * - Non-intrusive: zero impact on core business logic
 */

class GamificationEngine {

    constructor() {
        // Optimistic local state (overwritten by server on load)
        this.xp = parseInt(localStorage.getItem('cuan_xp') || 0);
        this.level = parseInt(localStorage.getItem('cuan_level') || 1);
        this.usingServer = false;

        // Init sequence: fetch from server first, then bind events and render
        this._init();
    }

    async _init() {
        // Defer until DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this._boot());
        } else {
            this._boot();
        }
    }

    async _boot() {
        await this.fetchProgress();
        this.initListeners();
        this.renderUI();
    }

    // ─── Server Sync ────────────────────────────────────────────────────────────

    async fetchProgress() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) return; // Guest — stay on localStorage

            const res = await fetch('/api/learning/progress', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                }
            });

            if (!res.ok) return;

            const json = await res.json();
            if (json.success && json.data) {
                this.xp = json.data.xp;
                this.level = json.data.level;
                this.usingServer = true;

                // Keep localStorage in sync as an offline cache
                localStorage.setItem('cuan_xp', this.xp);
                localStorage.setItem('cuan_level', this.level);
            }
        } catch (err) {
            console.warn('[Gamification] Could not fetch progress from server, using localStorage.', err);
        }
    }

    // ─── Event Listeners ────────────────────────────────────────────────────────

    initListeners() {
        // Most Gamification actions (like saving blueprints, calculating roadmaps, checking off steps)
        // are natively handled by backend Domain Event listeners and ProcessXpAward queues (Phase 21).
        // For those actions, experience/gamification-engine.js will passively detect the XP gain 
        // after Gamification.refresh() is called and display the popup.

        // This file only handles purely frontend-isolated actions that don't trigger backend saves natively:

        // Profit Simulator: dispatched on `document` (Simulating over 10m does not save to DB immediately)
        document.addEventListener('cuan:simulation-success', (e) => {
            if (e.detail && e.detail.netProfit >= 10000000) {
                // To prevent 400 Bad Request, simulation sends the latest session reference ID.
                // If the user hasn't saved/loaded a session yet, we use a fallback ID (10000000)
                // so they can earn the "10 Juta Target Hit!" reward exactly once as a global achievement.
                const safeRefId = e.detail?.referenceId || 10000000;
                this.addXP('profit_over_10m', '10 Juta Target Hit!', safeRefId);
            }
        });
    }

    // ─── XP Logic ────────────────────────────────────────────────────────────────

    async addXP(actionKey, reasonText = '', referenceId = null) {
        const config = window.GAMIFICATION_CONFIG;
        if (!config) return;

        const reward = config.xpRewards[actionKey];
        if (!reward) return;

        const oldLevel = this.level;

        if (this.usingServer) {
            // Server handles level_up detection — removes the client-side duplicate check
            await this._postXPToServer(actionKey, reward, reasonText, referenceId);
        } else {
            // Fallback: local computation
            this.xp += reward;
            this.level = Math.floor(this.xp / 500) + 1;
            localStorage.setItem('cuan_xp', this.xp);
            localStorage.setItem('cuan_level', this.level);
            this.showXPAnimation(reward, reasonText);

            // Client-side level up check (fallback only)
            if (this.level > oldLevel) {
                this.triggerLevelUpModal(oldLevel, this.level, this.getLevelTitle());
            }
        }

        this.renderUI();
        this.checkAchievements();
    }

    async _postXPToServer(actionKey, localReward, reasonText, referenceId) {
        try {
            const token = localStorage.getItem('auth_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const payload = { action: actionKey };
            if (referenceId) {
                payload.reference_id = referenceId;
            }

            const res = await fetch('/api/learning/xp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify(payload),
            });

            if (res.status === 409) return; // Already awarded — idempotent, silent skip
            if (!res.ok) throw new Error('Server returned ' + res.status);

            const json = await res.json();
            if (json.success && json.data) {
                this.xp = json.data.xp;
                this.level = json.data.level;
                localStorage.setItem('cuan_xp', this.xp);
                localStorage.setItem('cuan_level', this.level);

                // reward & level_up are top-level in data (set by controller)
                const reward = json.data.reward || localReward;
                this.showXPAnimation(reward, reasonText);

                // Server-authoritative level-up detection
                if (json.data.level_up === true) {
                    this.triggerLevelUpModal(json.data.old_level, json.data.new_level, json.data.level_title);
                }

                // Use server percent to avoid any client-side mismatch
                this.renderUI(json.data.progress_pct ?? null);
                this.checkAchievements();
            }
        } catch (err) {
            // Degrade gracefully — still show animation, just don't persist to server
            console.warn('[Gamification] XP sync failed, showing animation anyway.', err);
            this.xp += localReward;
            this.level = Math.floor(this.xp / 500) + 1;
            localStorage.setItem('cuan_xp', this.xp);
            localStorage.setItem('cuan_level', this.level);
            this.showXPAnimation(localReward, reasonText);
        }
    }

    // ─── Level Logic ─────────────────────────────────────────────────────────────

    getLevelTitle() {
        const level = this.level;
        const titles = window.GAMIFICATION_CONFIG?.levelTitles || {};

        let closest = 1;
        Object.keys(titles).forEach(lvl => {
            if (level >= parseInt(lvl)) closest = parseInt(lvl);
        });
        return titles[closest] || 'Rookie Planner';
    }

    // ─── UI Render ────────────────────────────────────────────────────────────────

    renderUI(serverPercent = null) {
        const level = this.level;
        const title = this.getLevelTitle();

        // Prefer server-calculated percent (avoids rounding mismatch)
        const percent = serverPercent !== null
            ? Math.min(100, serverPercent)
            : Math.min(100, Math.round(((this.xp % 500) / 500) * 100));

        const bars = document.querySelectorAll('#xp-progress-bar');
        const levelBadges = document.querySelectorAll('#level-badge');
        const xpCounts = document.querySelectorAll('#xp-count');
        const labels = document.querySelectorAll('#xp-label'); // fallback for old UI wrappers

        bars.forEach(bar => {
            bar.style.width = `${percent}%`;
        });

        levelBadges.forEach(levelBadge => {
            if (levelBadge.innerText !== `Lvl ${level}`) {
                levelBadge.innerText = `Lvl ${level}`;
                levelBadge.classList.remove('animate-pulse-once');
                void levelBadge.offsetWidth;
                levelBadge.classList.add('animate-pulse-once');

                // Also animate the wrapper if we level up
                if (levelBadge.parentElement) {
                    levelBadge.parentElement.classList.add('text-emerald-400', 'scale-105');
                    setTimeout(() => levelBadge.parentElement.classList.remove('text-emerald-400', 'scale-105'), 500);
                }
            }
        });

        // Animate XP count counting up dynamically
        xpCounts.forEach(xpCount => {
            if (xpCount.innerText !== this.xp.toString()) {
                const startVal = parseInt(xpCount.innerText) || 0;
                const endVal = this.xp;
                if (endVal > startVal) {
                    // simple counter animation
                    let current = startVal;
                    const increment = Math.ceil((endVal - startVal) / 20);

                    // We need a timer per-element to avoid shared state issues
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= endVal) {
                            current = endVal;
                            clearInterval(timer);
                        }
                        xpCount.innerText = current;
                    }, 40);
                } else {
                    xpCount.innerText = endVal;
                }
            }
        });

        // Keep legacy support for any elements still using xp-label
        labels.forEach(label => {
            const newText = `Lvl ${level} • ${title}`;
            if (label.innerText !== newText) {
                label.innerText = newText;
            }
        });
    }

    // ─── XP Popup Animation ───────────────────────────────────────────────────────

    showXPAnimation(amount, reason = '') {
        const div = document.createElement('div');
        div.className = 'xp-popup flex items-center gap-2';

        let html = `<span class="font-black text-amber-300 drop-shadow-md text-lg tracking-tight">+${amount} XP</span>`;
        if (reason) {
            html += `<span class="text-xs text-emerald-100 opacity-90 font-medium pl-2 border-l border-emerald-500/30">${reason}</span>`;
        }

        div.innerHTML = html;
        document.body.appendChild(div);

        const existingPopups = document.querySelectorAll('.xp-popup');
        if (existingPopups.length > 1) {
            const offset = (existingPopups.length - 1) * 60;
            div.style.top = `calc(80px + ${offset}px)`;
        }

        setTimeout(() => div.remove(), 2500);
    }

    // ─── Level Up Modal (Full 5-Step Animation Timeline) ──────────────────────────

    triggerLevelUpModal(oldLevel, newLevel, title = '') {
        // Safety flag — never show more than once per XP response
        if (this._levelUpShown) return;
        this._levelUpShown = true;
        setTimeout(() => { this._levelUpShown = false; }, 1000); // Reset after 1s

        const displayTitle = title || this.getLevelTitle();

        const backdrop = document.createElement('div');
        backdrop.id = 'levelup-modal-backdrop';
        backdrop.className = 'fixed inset-0 z-[10000] flex items-center justify-center p-4';
        backdrop.style.cssText = 'background:rgba(2,6,23,0.85);backdrop-filter:blur(8px);opacity:0;transition:opacity 0.4s ease';

        backdrop.innerHTML = `
            <div id="levelup-modal-card"
                 style="transform:scale(0.5);opacity:0;transition:transform 0.5s cubic-bezier(0.34,1.56,0.64,1),opacity 0.4s ease;transition-delay:0.2s"
                 class="relative bg-slate-900 border border-amber-500/40 rounded-3xl p-8 max-w-sm w-full text-center
                        shadow-[0_0_80px_rgba(245,158,11,0.25)] overflow-hidden">

                <!-- Ambient glow rings -->
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div id="lv-ring1" class="absolute w-40 h-40 rounded-full border border-amber-500/20" style="animation:none"></div>
                    <div id="lv-ring2" class="absolute w-56 h-56 rounded-full border border-amber-500/10" style="animation:none"></div>
                </div>

                <!-- Trophy -->
                <div class="relative z-10 w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-amber-500/30 to-amber-700/20
                            border-2 border-amber-400/60 flex items-center justify-center mb-5
                            shadow-[0_0_40px_rgba(245,158,11,0.4)]">
                    <i class="fas fa-trophy text-5xl text-amber-400"></i>
                </div>

                <!-- Level Up text -->
                <p class="relative z-10 text-[10px] font-black text-amber-400 uppercase tracking-[0.3em] mb-2">⭐ Level Up! ⭐</p>

                <!-- Old → New Level -->
                <div class="relative z-10 flex items-center justify-center gap-3 mb-3">
                    <span class="text-2xl font-black text-slate-500">Lv ${oldLevel}</span>
                    <span class="text-amber-400 text-2xl">→</span>
                    <span class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-amber-500">Lv ${newLevel}</span>
                </div>

                <!-- Title badge -->
                <div class="relative z-10 inline-flex items-center gap-2 px-5 py-2 rounded-full
                            bg-gradient-to-r from-amber-500/20 to-amber-700/10 border border-amber-500/30 mb-6">
                    <i class="fas fa-crown text-amber-400 text-xs"></i>
                    <span class="text-sm font-bold text-amber-300">${displayTitle}</span>
                </div>

                <!-- Continue button — fades in at 1300ms -->
                <button id="lv-continue-btn"
                        style="opacity:0;transition:opacity 0.4s ease;transition-delay:1.3s"
                        onclick="document.getElementById('levelup-modal-backdrop').remove()"
                        class="relative z-10 w-full py-3 rounded-2xl font-bold text-sm text-slate-900
                               bg-gradient-to-r from-amber-400 to-amber-500
                               hover:from-amber-300 hover:to-amber-400
                               shadow-[0_4px_20px_rgba(245,158,11,0.4)]
                               transform hover:scale-105 transition-all duration-300">
                    Continue 🚀
                </button>
            </div>
        `;

        document.body.appendChild(backdrop);

        // Step 1 (0ms): Backdrop fade in
        requestAnimationFrame(() => {
            backdrop.style.opacity = '1';

            // Step 2 (200ms): Card scale bounce
            const card = document.getElementById('levelup-modal-card');
            if (card) {
                card.style.transform = 'scale(1)';
                card.style.opacity = '1';
            }

            // Step 3 (600ms): Glow rings pulse
            setTimeout(() => {
                ['lv-ring1', 'lv-ring2'].forEach((id, i) => {
                    const el = document.getElementById(id);
                    if (el) el.style.animation = `ping ${1.5 + i * 0.5}s cubic-bezier(0,0,0.2,1) infinite`;
                });
            }, 600);

            // Step 4 (900ms): Confetti burst
            setTimeout(() => {
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 120, spread: 80, origin: { y: 0.55 }, colors: ['#fbbf24', '#34d399', '#818cf8', '#f472b6'] });
                }
            }, 900);

            // Step 5 (1300ms): Button fade in (handled by CSS transition-delay)
            setTimeout(() => {
                const btn = document.getElementById('lv-continue-btn');
                if (btn) btn.style.opacity = '1';
            }, 1300);
        });

        // Auto-dismiss at 5s
        setTimeout(() => {
            const el = document.getElementById('levelup-modal-backdrop');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => el?.remove(), 500);
            }
        }, 5000);
    }


    // ─── Achievements ─────────────────────────────────────────────────────────────

    checkAchievements() {
        const unlocks = [
            { key: 'ach_first_1000', xp: 1000, label: 'First 1000 XP!' },
            { key: 'ach_first_5k', xp: 5000, label: '5K XP — Growth Hacker' },
            { key: 'ach_first_10k', xp: 10000, label: '10K XP — Strategy Overlord' },
        ];

        unlocks.forEach(({ key, xp, label }) => {
            if (this.xp >= xp && !localStorage.getItem(key)) {
                this.unlockAchievement(label);
                localStorage.setItem(key, 'true');
            }
        });
    }

    unlockAchievement(name) {
        const div = document.createElement('div');
        div.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] bg-gradient-to-r from-violet-600 to-indigo-600 border border-indigo-400 rounded-2xl p-4 shadow-2xl flex items-center gap-4 animate-slide-up-fade pointer-events-none';

        div.innerHTML = `
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <i class="fas fa-award text-white text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-0.5">Achievement Unlocked</p>
                <p class="text-sm font-bold text-white">${name}</p>
            </div>
        `;
        document.body.appendChild(div);

        setTimeout(() => {
            div.style.transform = 'translate(-50%, -10px)';
            div.style.opacity = '0';
            div.style.transition = 'all 0.5s ease';
            setTimeout(() => div.remove(), 500);
        }, 4000);
    }
}

window.gamificationEngine = new GamificationEngine();
