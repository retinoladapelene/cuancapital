/**
 * CuanCapital — Achievement Dashboard Engine (Phase 4 + Phase 5)
 * Phase 4: Fetches achievement data with progress hints
 * Phase 5: Badge Showcase — pin/unpin up to 3 badges to your profile
 */

class AchievementDashboard {

    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.showcaseBadges = []; // currently pinned badge keys
        this.unlockedKeys = [];  // keys the user has unlocked
        if (this.container) this._init();
    }

    async _init() {
        this._renderSkeleton();
        await Promise.all([
            this.fetchShowcase(),
            this.fetchAndRender(),
        ]);
    }

    // ─── API Calls ───────────────────────────────────────────────────────────────

    async fetchShowcase() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) return;
            const res = await fetch('/api/profile/showcase', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const json = await res.json();
            if (json.success) {
                this.showcaseBadges = (json.data || []).map(b => b.achievement_id);
            }
        } catch (_) { }
    }

    async fetchAndRender() {
        try {
            const token = localStorage.getItem('auth_token');
            if (!token) { this._renderGuest(); return; }

            const res = await fetch('/api/achievements/dashboard', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error();
            const json = await res.json();
            if (json.success) {
                this.unlockedKeys = (json.data.achievements || []).filter(a => a.unlocked).map(a => a.id);
                this._render(json.data);
            }
        } catch (_) { this._renderError(); }
    }

    async _pinBadge(achievementId) {
        if (this.showcaseBadges.includes(achievementId)) {
            // Unpin
            this.showcaseBadges = this.showcaseBadges.filter(k => k !== achievementId);
        } else {
            if (this.showcaseBadges.length >= 3) {
                this._showToast('Max 3 showcase badges. Unpin one first.', 'warning');
                return;
            }
            this.showcaseBadges.push(achievementId);
        }

        try {
            const token = localStorage.getItem('auth_token');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch('/api/profile/showcase', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': csrf || '',
                },
                body: JSON.stringify({ badges: this.showcaseBadges }),
            });
            // Re-render the flyout contents
            await this.fetchAndRender();
        } catch (_) { }
    }

    // ─── Main Render ─────────────────────────────────────────────────────────────

    _render(data) {
        if (!this.container) return;
        const completionPct = data.completion_pct || 0;

        this.container.innerHTML = `
            <!-- Showcase Slots -->
            ${this._renderShowcaseSlots()}

            <!-- Overall Progress Header -->
            <div class="flex items-center justify-between mb-3 mt-4">
                <div>
                    <p class="text-[10px] font-bold text-white uppercase tracking-widest">All Achievements</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">${data.unlocked} / ${data.total} Unlocked</p>
                </div>
                <div class="text-right">
                    <div class="text-xl font-black text-amber-400">${completionPct}%</div>
                </div>
            </div>
            <div class="h-1 bg-slate-800 rounded-full overflow-hidden mb-4 border border-white/5">
                <div class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 transition-all duration-1000 rounded-full"
                     style="width:${completionPct}%"></div>
            </div>

            <!-- Achievement List -->
            <div class="space-y-2">
                ${data.achievements.map(a => this._renderCard(a)).join('')}
            </div>
        `;

        // Bind pin buttons
        this.container.querySelectorAll('[data-pin-id]').forEach(btn => {
            btn.addEventListener('click', () => this._pinBadge(btn.dataset.pinId));
        });
    }

    _renderShowcaseSlots() {
        const slots = [1, 2, 3].map(slot => {
            const badgeKey = this.showcaseBadges[slot - 1];
            if (badgeKey) {
                // Filled slot — get meta from the achievement config (approximate from common pattern)
                const icons = { roadmap_builder: '🗺️', first_goal: '🎪', mentor_graduate: '🎓', blueprint_saver: '💾', first_simulation: '🎯', first_1000_xp: '⭐', regular_planner: '📋', mentor_master: '🧠', power_simulator: '⚡', blueprint_hoarder: '🗂️', xp_5k: '💥', level_5: '🚀', level_10: '📈' };
                const icon = icons[badgeKey] || '🏅';
                return `
                    <div class="relative group/slot" data-pin-id="${badgeKey}">
                        <div class="w-full aspect-square rounded-xl bg-amber-500/10 border border-amber-500/40
                                    flex flex-col items-center justify-center
                                    shadow-[0_0_14px_rgba(245,158,11,0.25)]
                                    cursor-pointer hover:border-rose-500/60 hover:bg-rose-500/10 transition-all duration-300"
                             data-pin-id="${badgeKey}">
                            <span class="text-2xl mb-1">${icon}</span>
                            <span class="text-[8px] text-amber-400/70 max-w-full truncate px-1">Slot ${slot}</span>
                        </div>
                        <div class="absolute -top-1 -right-1 hidden group-hover/slot:flex w-5 h-5 rounded-full
                                    bg-rose-500 items-center justify-center pointer-events-none">
                            <i class="fas fa-times text-[8px] text-white"></i>
                        </div>
                    </div>
                `;
            }
            return `
                <div class="w-full aspect-square rounded-xl bg-slate-800/50 border border-dashed border-slate-600/50
                            flex flex-col items-center justify-center text-slate-600">
                    <i class="fas fa-plus text-sm mb-1 opacity-40"></i>
                    <span class="text-[8px] opacity-40">Slot ${slot}</span>
                </div>
            `;
        });

        return `
            <div>
                <p class="text-[10px] font-bold text-amber-400/80 uppercase tracking-widest mb-2">📌 Showcase</p>
                <div class="grid grid-cols-3 gap-2 mb-1">
                    ${slots.join('')}
                </div>
                <p class="text-[9px] text-slate-600 mb-1">Click a filled badge to unpin. Click "Pin" on any unlocked badge to showcase it.</p>
            </div>
        `;
    }

    _renderCard(a) {
        const isPinned = this.showcaseBadges.includes(a.id);
        const pinLabel = isPinned ? '📌 Pinned' : '+ Pin';
        const pinClass = isPinned
            ? 'bg-amber-500/20 text-amber-400 border-amber-500/30'
            : 'bg-slate-700/50 text-slate-400 border-slate-600/50 hover:bg-emerald-500/20 hover:text-emerald-400 hover:border-emerald-500/30';

        if (a.unlocked) {
            return `
                <div class="flex items-center gap-3 p-3 rounded-xl ${isPinned ? 'bg-amber-500/8 border border-amber-500/25' : 'bg-emerald-500/8 border border-emerald-500/20'}">
                    <div class="w-9 h-9 rounded-full ${isPinned ? 'bg-amber-500/20 border-amber-500/40 shadow-[0_0_12px_rgba(245,158,11,0.3)]' : 'bg-emerald-500/20 border-emerald-500/40 shadow-[0_0_10px_rgba(16,185,129,0.25)]'} border flex items-center justify-center text-base shrink-0">
                        ${a.icon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold ${isPinned ? 'text-amber-300' : 'text-emerald-300'} truncate">${a.title}</p>
                        <p class="text-[10px] ${isPinned ? 'text-amber-500/60' : 'text-emerald-500/60'}">✓ Unlocked</p>
                    </div>
                    <button data-pin-id="${a.id}"
                            class="shrink-0 px-2 py-1 rounded-lg text-[9px] font-bold border transition-all duration-200 ${pinClass}">
                        ${pinLabel}
                    </button>
                </div>
            `;
        }

        const progress = a.progress;
        if (!progress) {
            return `
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 border border-slate-700/50 opacity-50">
                    <div class="w-9 h-9 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center text-base shrink-0 grayscale">${a.icon}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-400 truncate">${a.title}</p>
                        <p class="text-[10px] text-slate-500">${a.description}</p>
                    </div>
                    <i class="fas fa-lock text-slate-600 text-xs shrink-0"></i>
                </div>
            `;
        }

        const barColor = this._barColor(progress.tone);
        const microcopy = this._microcopy(progress.percent);

        return `
            <div class="p-3 rounded-xl bg-slate-800/70 border border-slate-700/60 hover:border-slate-600 transition-colors">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-9 h-9 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center text-base shrink-0 grayscale">${a.icon}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-200 truncate">${a.title}</p>
                        <p class="text-[10px] text-slate-500">${progress.hint}</p>
                    </div>
                    <span class="text-[10px] font-black ${this._pctColor(progress.tone)} shrink-0">${progress.percent}%</span>
                </div>
                <div class="h-1.5 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                    <div class="h-full rounded-full transition-all duration-1000 ${barColor}" style="width:${progress.percent}%"></div>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">${microcopy}</p>
            </div>
        `;
    }

    // ─── Tone / Style Utilities ───────────────────────────────────────────────────

    _barColor(tone) {
        return { gentle: 'bg-gradient-to-r from-slate-500 to-slate-400', motivating: 'bg-gradient-to-r from-blue-500 to-cyan-400', urgency: 'bg-gradient-to-r from-amber-500 to-orange-400' }[tone] || 'bg-slate-500';
    }
    _pctColor(tone) {
        return { gentle: 'text-slate-400', motivating: 'text-blue-400', urgency: 'text-amber-400' }[tone] || 'text-slate-400';
    }
    _microcopy(pct) {
        if (pct >= 90) return 'Almost unlocked 🔥';
        if (pct >= 70) return 'So close! Keep going 💪';
        if (pct >= 50) return "You're halfway there!";
        if (pct >= 30) return 'Good progress, keep it up!';
        if (pct >= 10) return 'Just getting started 🚀';
        return 'Start your journey!';
    }
    _showToast(msg, type = 'info') {
        if (typeof showToast === 'function') showToast(msg, type);
        else console.warn('[Achievement]', msg);
    }

    // ─── State Renderers ──────────────────────────────────────────────────────────

    _renderSkeleton() {
        if (!this.container) return;
        this.container.innerHTML = Array(4).fill(`
            <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/50 animate-pulse flex gap-3 items-center mb-2">
                <div class="w-9 h-9 rounded-full bg-slate-700 shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-2.5 bg-slate-700 rounded w-2/3"></div>
                    <div class="h-1.5 bg-slate-700 rounded-full w-full"></div>
                </div>
            </div>
        `).join('');
    }
    _renderGuest() {
        if (!this.container) return;
        this.container.innerHTML = `<div class="text-center py-8 text-slate-500"><i class="fas fa-lock text-3xl mb-3 opacity-40"></i><p class="text-sm">Login untuk melihat achievement</p></div>`;
    }
    _renderError() {
        if (!this.container) return;
        this.container.innerHTML = `<div class="text-center py-6 text-slate-500"><i class="fas fa-exclamation-triangle text-2xl mb-2 opacity-40"></i><p class="text-xs">Gagal memuat achievement</p></div>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('achievement-dashboard-container')) {
        window.achievementDashboard = new AchievementDashboard('achievement-dashboard-container');
    }
});
