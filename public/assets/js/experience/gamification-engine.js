import { api } from '../services/api.js';
import { Toast } from '../components/toast-notification.js';
import { progressEngine } from './progress-engine.js';
import { learningHud } from './learning-hud.js';

// --- PHASE 17: BOLD LEVEL-UP ENGINE ---
class LevelUpEngine {
    constructor() {
        this.overlay = document.getElementById('levelUpOverlay');
        this.numberEl = document.getElementById('levelUpNumber');
    }

    trigger(newLevel) {
        if (!this.overlay || !this.numberEl) return;

        this.numberEl.textContent = newLevel;

        this.overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            this.overlay.classList.add('active');
        });

        setTimeout(() => {
            this.overlay.classList.remove('active');
        }, 2200);

        setTimeout(() => {
            this.overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 2500);
    }
}

window.levelUpEngine = new LevelUpEngine();

export const gamificationEngine = {
    state: {
        xp: 0,
        level: 'Loading...',
        nextLevelXp: 50,
        roadmapPercent: 0,
        milestones: {
            rgp: false,
            simulator: false,
            mentor: false,
            roadmap: false
        }
    },

    isRefreshing: false,      // Anti-spam guard
    xpAnimationId: null,      // Request callback ID

    /**
     * Fetch the single source of truth from the Backend Authority 
     */
    async loadState() {
        if (this.isRefreshing) return;
        this.isRefreshing = true;

        try {
            // Append cache-buster so aggressive browser caching doesn't return stale 304s
            const res = await api.get(`/experience/progress?_=${Date.now()}`, { useApiPrefix: true });

            if (res.success) {
                const prevLevel = this.state.level;
                const prevXp = this.state.xp;

                this.state.xp = res.data.xp;
                this.state.level = res.data.level;
                this.state.nextLevelXp = res.data.next_level_xp;
                this.state.roadmapPercent = res.data.roadmap_percent;
                this.state.milestones = res.data.milestones;

                this.updateUI(prevXp, prevLevel);
                progressEngine.recalculate(this.state.milestones, this.state.roadmapPercent);

                // Phase 18: Check for newly unlocked achievements from the async evaluators
                if (res.data.unlocked_achievements && res.data.unlocked_achievements.length > 0) {
                    res.data.unlocked_achievements.forEach((ach, index) => {
                        // Stagger the toasts by 3.5 seconds if multiple elements are unlocked simultaneously
                        setTimeout(() => {
                            this.showAchievementUnlock(ach.icon, ach.name);
                        }, index * 3500);
                    });
                }
            }
        } catch (error) {
            console.error("Experience OS: Failed to load gamification state", error);
        } finally {
            this.isRefreshing = false;
        }
    },

    /**
     * Dispatch an event to the backend. The backend will verify boolean flags to prevent double XP.
     * Deprecated in Phase 13/14 since backend event architecture handles this now.
     */
    async syncEvent(eventName, payload = {}) {
        // Obsolete: XP awarding is now handled by Domain Events in the backend
        // We leave this to avoid breaking legacy code, but it just triggers a refresh
        window.Gamification.refresh();
    },

    updateUI(prevXp = null, prevLevel = null) {
        const { xp, level, nextLevelXp, milestones } = this.state;

        // Level Up Detection (Soft Trigger)
        if (prevLevel && prevLevel !== 'Loading...' && prevLevel < level) {
            this.showLevelUpModal(level, this.getLevelTitle(level));
        }

        // Calculate Bar Percentage
        const tierMax = level < 20 ? 500 : 1000;
        const currentTierBase = level <= 20 ? (level - 1) * 500 : 9500 + (level - 20) * 1000;
        const progressInTier = Math.max(0, xp - currentTierBase);
        const percent = Math.min(100, Math.round((progressInTier / tierMax) * 100));

        // 60FPS Animated XP Counter
        const startXp = prevXp !== null ? prevXp : xp;
        document.querySelectorAll('#xp-count').forEach(el => {
            this.animateXP(el, startXp, xp);
        });

        // Trigger Floating XP if user gained XP and it's not the initial load
        if (prevXp !== null && xp > prevXp && prevLevel !== 'Loading...') {
            this.showFloatingXP(xp - prevXp);

            // Phase 19: XP Counter Micro-Bounce Effect
            document.querySelectorAll('#xp-count').forEach(el => {
                el.classList.add('xp-pop');
                setTimeout(() => el.classList.remove('xp-pop'), 250);
            });
        }

        // Universal DOM State Push (Supports duplicate IDs in Mobile vs Desktop layouts)
        document.querySelectorAll('#level-badge').forEach(el => el.innerText = `Lvl ${level}`);

        // CSS Transition Hotfix: Force Reflow to bypass browser paint optimization
        document.querySelectorAll('#xp-progress-bar, #panel-xp-bar').forEach(el => {
            el.style.transition = 'none';
            // Start from 0% only if no previous width inline style exists
            if (!el.style.width) el.style.width = '0%';

            // Force browser layout recalculation
            void el.offsetWidth;

            el.style.transition = 'width 0.7s ease';
            el.style.width = `${percent}%`;
        });

        // Update Legacy Panels
        document.querySelectorAll('#panel-level-text').forEach(el => el.innerText = level);
        document.querySelectorAll('#panel-xp-text').forEach(el => el.innerText = `${xp} XP`);
        document.querySelectorAll('#panel-xp-remaining-text').forEach(el => el.innerText = `${tierMax - progressInTier} XP to Next Level`);
        document.querySelectorAll('#journey-level-text').forEach(el => el.innerText = `Level: ${level}`);
        document.querySelectorAll('#journey-xp-text').forEach(el => el.innerText = `${xp} XP`);

        // Milestone Visual Checkmarks
        document.querySelectorAll('#milestone-rgp').forEach(el => this.toggleMilestoneUI(el, milestones.rgp));
        document.querySelectorAll('#milestone-sim').forEach(el => this.toggleMilestoneUI(el, milestones.simulator));
        document.querySelectorAll('#milestone-mentor').forEach(el => this.toggleMilestoneUI(el, milestones.mentor));
        document.querySelectorAll('#milestone-roadmap').forEach(el => this.toggleMilestoneUI(el, milestones.roadmap));
    },

    /**
     * Micro-Engine for 60FPS UI interpolation
     */
    animateXP(element, from, to) {
        if (from === to) {
            element.innerText = to;
            return;
        }

        // Cancel previous animation to prevent memory leak / jitter
        if (this.xpAnimationId) {
            cancelAnimationFrame(this.xpAnimationId);
        }

        const duration = 800;
        const start = performance.now();

        const animate = (time) => {
            const progress = Math.min((time - start) / duration, 1);
            // Easing function (easeOutExpo)
            const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

            const value = Math.floor(from + (to - from) * easeProgress);
            element.innerText = value;

            if (progress < 1) {
                this.xpAnimationId = requestAnimationFrame(animate);
            } else {
                this.xpAnimationId = null;
            }
        };

        this.xpAnimationId = requestAnimationFrame(animate);
    },

    /**
     * Micro-Feedback Floating Text
     */
    showFloatingXP(amount) {
        const el = document.createElement('div');
        el.className = 'xp-float';
        el.textContent = `+${amount} XP`;
        document.body.appendChild(el);

        // Auto-cleanup on animation end
        el.addEventListener('animationend', () => {
            el.remove();
        });
    },

    /**
     * Phase 18: Micro-Feedback Toast for Unlocked Achievements
     */
    showAchievementUnlock(icon, name) {
        const el = document.createElement('div');
        el.className = 'achievement-toast flex items-center gap-3';
        el.innerHTML = `
            <div class="text-3xl">${icon}</div>
            <div class="flex flex-col">
                <span class="text-[10px] text-emerald-200 uppercase font-bold tracking-widest">Achievement Unlocked</span>
                <span class="text-sm font-bold text-white">${name}</span>
            </div>
        `;
        document.body.appendChild(el);

        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-20px)';
            setTimeout(() => el.remove(), 300);
        }, 3000);
    },

    /**
     * Trigger Level Up celebration modal (Legacy wrapper for Phase 17 engine)
     */
    showLevelUpModal(level, title) {
        if (window.levelUpEngine) {
            window.levelUpEngine.trigger(level);
        }
    },

    toggleMilestoneUI(element, isCompleted) {
        if (!element) return;
        if (isCompleted) {
            element.classList.remove('opacity-50');
            const parentBlock = element.closest('.milestone-block');
            if (parentBlock && parentBlock.querySelector('.milestone-check')) {
                parentBlock.querySelector('.milestone-check').classList.remove('bg-slate-950', 'border-slate-700');
                parentBlock.querySelector('.milestone-check').classList.add('bg-emerald-500', 'border-emerald-500');
            }
            const iCheck = element.querySelector('i.fa-check');
            if (iCheck) {
                iCheck.classList.remove('text-transparent');
                iCheck.classList.add('text-white');
            }
        }
    },

    showXpGained(amount, message) {
        Toast.show(`+${amount} XP: ${message}`, 'success');
    },

    getLevelTitle(level) {
        if (level >= 100) return 'Business Titan';
        if (level >= 50) return 'Cuan Architect';
        if (level >= 20) return 'Strategic Builder';
        if (level >= 10) return 'Growth Hacker';
        if (level >= 5) return 'Smart Executor';
        return 'Rookie Planner';
    }
};

window.Gamification = {
    refresh: () => {
        // Buffer of 800ms for Queue jobs to land first
        setTimeout(() => gamificationEngine.loadState(), 800);
    }
};
