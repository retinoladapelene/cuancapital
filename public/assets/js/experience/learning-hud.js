import { gamificationEngine } from './gamification-engine.js';

export const learningHud = {
    state: {
        lastInsightShown: null,
        nudgeTimer: null
    },

    init() {
        this.bindConceptInsights();
        this.startIdleNudgeTracker();

        // Initial intro 
        setTimeout(() => this.showNudge("Halo! Saya Aksa. Kalau bingung istilah bisnis, hover saja teksnya ya! 🚀"), 3000);
    },

    /**
     * Finds elements with class `.concept-term` and binds hover logic
     * Example HTML: <span class="concept-term border-b border-dashed border-emerald-500 cursor-help" data-concept="margin">Margin</span>
     */
    bindConceptInsights() {
        // Event delegation on body to handle dynamically added elements
        document.body.addEventListener('mouseover', (e) => {
            if (e.target && e.target.classList && e.target.classList.contains('concept-term')) {
                const conceptKey = e.target.getAttribute('data-concept');
                this.showConceptInsight(conceptKey, e.target);
            }
        });

        document.body.addEventListener('mouseout', (e) => {
            if (e.target && e.target.classList && e.target.classList.contains('concept-term')) {
                this.hideConceptInsight();
            }
        });
    },

    showConceptInsight(conceptKey, targetElement) {
        if (!conceptKey) return;

        const card = document.getElementById('concept-insight-card');
        const content = document.getElementById('insight-content');
        if (!card || !content) return;

        // Dictionary of simple, dynamic explanations
        const dictionary = {
            'margin': `
                <p><strong>Margin</strong> = Keuntungan per produk (%).</p>
                <p class="text-[10px] mt-1 text-emerald-400">🔥 Jika margin kecil → target unit/traffic naik.<br>🔥 Jika margin besar → beban traffic lebih ringan.</p>
                <div class="mt-2 p-2 bg-slate-950/50 rounded border border-white/5">
                    Rekomendasi ideal: <strong>30% - 50%</strong> untuk pemula.
                </div>
            `,
            'conversion': `
                <p><strong>Conversion Rate</strong> = Persentase pengunjung yang berujung jadi pembeli.</p>
                <p class="text-[10px] mt-1 text-emerald-400">💡 Formula: (Pembeli / Pengunjung) x 100.</p>
                <div class="mt-2 p-2 bg-slate-950/50 rounded border border-white/5">
                    Rata-rata industri: <strong>1% - 3%</strong>.
                </div>
            `,
            'cpa': `
                <p><strong>Cost Per Acquisition (CPA)</strong> = Biaya iklan untuk mendapatkan 1 pembeli.</p>
                <p class="text-[10px] mt-1 text-rose-400">⚠️ CPA tidak boleh lebih besar dari Margin Anda, atau Anda akan rugi (Boncos).</p>
            `,
            'roas': `
                <p><strong>ROAS</strong> = Return on Ad Spend.</p>
                <p class="text-[10px] mt-1 text-emerald-400">💡 Setiap Rp1 yang dikeluarkan untuk iklan, menghasilkan Rp X pendapatan.</p>
                <div class="mt-2 p-2 bg-slate-950/50 rounded border border-white/5">
                    ROAS > 1 = Untung kotor.<br>Namun hitung ROAS <em>Break-Even</em> berdasarkan margin Anda.
                </div>
            `
        };

        const explanation = dictionary[conceptKey] || `<p>Istilah teknis. Fokus pada simulasi angka terlebih dahulu.</p>`;

        content.innerHTML = explanation;

        // Un-hide and animate
        card.classList.remove('hidden');
        // Let browser register removal of hidden before transforming opacity
        setTimeout(() => {
            card.classList.remove('opacity-0', 'translate-y-4');
        }, 10);
    },

    hideConceptInsight() {
        const card = document.getElementById('concept-insight-card');
        if (!card) return;

        card.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => {
            card.classList.add('hidden');
        }, 300); // match transition duration
    },

    startIdleNudgeTracker() {
        // Reset timer on user interaction
        const resetTimer = () => {
            clearTimeout(this.state.nudgeTimer);
            this.state.nudgeTimer = setTimeout(() => this.evaluateSmartNudge(), 60000); // 1 minute idle
        };

        window.addEventListener('mousemove', resetTimer);
        window.addEventListener('keypress', resetTimer);
        window.addEventListener('click', resetTimer);
        resetTimer();
    },

    evaluateSmartNudge() {
        const gs = gamificationEngine.state;

        // Smart Nudging based on Funnel completion
        if (gs.milestones.rgp && !gs.milestones.simulator) {
            this.showNudge("🎯 RGP selesai! Coba klik 'Running Simulation' untuk melihat probabilitasnya.");
            return;
        }

        if (gs.milestones.simulator && !gs.milestones.mentor) {
            this.showNudge("🧬 Simulasi sukses. Waktunya menemukan Strategic DNA bisnismu di Mentor Lab!");
            return;
        }

        if (gs.milestones.mentor && !gs.milestones.roadmap) {
            this.showNudge("🚀 DNA ditemukan! Segera generate Execution Roadmap agar ada langkah nyata.");
            return;
        }
    },

    showNudge(message) {
        const card = document.getElementById('concept-insight-card');
        const content = document.getElementById('insight-content');
        if (!card || !content) return;

        content.innerHTML = `
            <div class="flex items-start gap-3">
                <img src="/assets/icon/aksa_notif.png" class="w-8 h-8 rounded-full border border-emerald-500/50">
                <p class="text-sm font-medium text-white leading-relaxed">"${message}"</p>
            </div>
        `;

        card.classList.remove('hidden');
        setTimeout(() => {
            card.classList.remove('opacity-0', 'translate-y-4');
        }, 10);

        this.notifyOrb();

        // Auto-hide nudge after 8 seconds
        setTimeout(() => this.hideConceptInsight(), 8000);
    },

    notifyOrb() {
        // Red dot alert on Orb
        const dot = document.getElementById('orb-notif-dot');
        const pulse = document.getElementById('orb-pulse');
        if (dot) dot.classList.remove('hidden');
        if (pulse) pulse.classList.remove('opacity-0');

        setTimeout(() => {
            if (pulse) pulse.classList.add('opacity-0');
        }, 3000);
    }
};
