window.RoleplayEngine = {
    mountEl: null,
    simulationId: null,
    currentData: null,

    // Ensure mobile reader view is visible whenever we render into #lesson-reader
    _ensureMobileReader: function () {
        if (window.coursesHub && typeof window.coursesHub._isMobile === 'function' && window.coursesHub._isMobile()) {
            window.coursesHub._setMobileView('reader');
            // Set back label for back button
            const label = document.getElementById('mobile-back-label');
            if (label) label.textContent = 'Kembali ke Modul';
        }
    },

    start: async function (simId, mountId) {
        this.simulationId = simId;
        this.mountEl = document.getElementById(mountId);
        if (!this.mountEl) return;

        // Switch to reader view on mobile BEFORE loading so user sees spinner
        this._ensureMobileReader();

        this.mountEl.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10">
                <i class="fas fa-spinner fa-spin text-emerald-500 text-3xl mb-4"></i>
                <p class="text-slate-400 text-sm animate-pulse">Memuat Simulasi Bisnis...</p>
            </div>
        `;

        try {
            const json = await window.coursesHub._post('/api/simulation/start', { simulation_id: simId });
            if (!json.success) throw new Error(json.message);

            this.currentData = json.data;
            this.renderIntro();
        } catch (e) {
            console.error(e);
            this.mountEl.innerHTML = `<div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat simulator. Coba lagi.</div>`;
        }
    },

    renderIntro: function () {
        const sim = this.currentData.simulation;
        const attempts = this.currentData.attempts;
        const eligible = this.currentData.is_eligible_for_xp;

        let attemptHtml = '';
        if (attempts > 0) {
            attemptHtml = `<div class="text-xs ${eligible ? 'text-amber-400' : 'text-rose-400'} mb-4 text-center">
                <i class="fas fa-info-circle mr-1"></i> Kamu sudah mencoba simulasi ini ${attempts} kali. ${!eligible ? '(Batas XP habis)' : ''}
            </div>`;
        }

        let rawLines = sim.intro_text.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        let descriptionLines = [];
        let infoGraphicLines = [];

        rawLines.forEach((line) => {
            const lower = line.toLowerCase();
            if (lower.startsWith('objective:') || lower.startsWith('metrics') || lower.startsWith('stat awal:') || lower.includes('valuation =') || lower.startsWith('target:') || lower.includes('mempengaruhi') || lower.includes('profitability:') || line.startsWith('BOARD BRIEFING')) {
                infoGraphicLines.push(line);
            } else {
                if (infoGraphicLines.length > 0 && !line.startsWith('BOARD BRIEFING')) {
                    infoGraphicLines.push(line);
                } else {
                    descriptionLines.push(line);
                }
            }
        });

        const descHtml = descriptionLines.map(l => `<p class="text-slate-300 text-sm md:text-base leading-relaxed mb-3">${l}</p>`).join('');

        let rulesHtml = '';
        if (infoGraphicLines.length > 0) {
            rulesHtml = `
                <div class="mt-6 mb-8 text-left bg-slate-900 border border-slate-700 rounded-xl p-5 shadow-lg relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                        <i class="fas fa-chess-board text-6xl"></i>
                    </div>
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-800 relative z-10">
                        <i class="fas fa-clipboard-list text-emerald-400"></i>
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">Mission Briefing & Rules</h4>
                    </div>
                    <div class="space-y-3 relative z-10">
                        ${infoGraphicLines.map(l => {
                let icon = 'fa-info-circle text-slate-500';
                let textClass = 'text-slate-300';

                const lower = l.toLowerCase();
                if (lower.includes('objective:')) {
                    icon = 'fa-bullseye text-amber-400';
                    textClass = 'text-amber-100 font-bold';
                    l = l.replace(/objective:/i, '<span class="text-amber-400">OBJECTIVE:</span>');
                }
                else if (lower.includes('valuation =') || lower.includes('formula')) {
                    icon = 'fa-calculator text-blue-400';
                    textClass = 'text-blue-200 font-mono text-[11px] bg-blue-900/30 px-2 py-1 rounded inline-block';
                }
                else if (lower.includes('profitability:') || lower.includes('stat awal:')) {
                    icon = 'fa-chart-pie text-purple-400';
                    textClass = 'text-purple-200 font-bold';
                }
                else if (lower.includes('target:')) {
                    icon = 'fa-flag-checkered text-rose-400';
                    textClass = 'text-white font-bold';
                }
                else if (l.startsWith('BOARD BRIEFING')) {
                    icon = 'fa-file-signature text-violet-400';
                    textClass = 'text-violet-300 font-black tracking-widest text-[10px] uppercase';
                }

                return `<div class="flex items-start gap-3">
                                <i class="fas ${icon} mt-1 shrink-0 text-[13px]"></i>
                                <span class="text-sm ${textClass} leading-snug">${l}</span>
                            </div>`;
            }).join('')}
                    </div>
                </div>
            `;
        }

        const html = `
            <div class="animate-card-in border border-emerald-500/30 bg-gradient-to-b from-slate-900 to-slate-950 rounded-2xl p-6 md:p-8 relative overflow-hidden shadow-[0_10px_40px_rgba(16,185,129,0.1)] text-left">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-violet-500"></div>
                
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-xl text-emerald-400 shrink-0">
                        <i class="fas ${sim.difficulty_level === 'master' ? 'fa-crown' : 'fa-chess-knight'}"></i>
                    </div>
                    <div>
                        <h3 class="text-[10px] text-emerald-500 uppercase tracking-[0.2em] font-black mb-1">
                            ${sim.difficulty_level === 'master' ? 'Master Command Module' : 'Simulation Engine'}
                        </h3>
                        <h2 class="text-xl md:text-2xl font-black text-white">${sim.title}</h2>
                    </div>
                </div>
                
                <div class="mb-4">
                    ${descHtml}
                </div>
                
                ${rulesHtml}
                
                ${attemptHtml}

                <div class="mt-8 border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4 text-[11px] text-slate-400 font-bold uppercase tracking-wider">
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500/10 border border-orange-500/20 text-orange-400"><i class="fas fa-fire"></i> Decision Impact</span>
                        <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400"><i class="fas fa-star"></i> Max +${sim.xp_reward + 25} XP</span>
                    </div>
                    <button onclick="RoleplayEngine.renderStep()" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl shadow-[0_4px_20px_rgba(16,185,129,0.4)] transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                        Start Mission <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        `;
        this.mountEl.innerHTML = html;
        this._ensureMobileReader();
        this.mountEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    },


    renderStep: function () {
        const session = this.currentData.session;
        const sim = this.currentData.simulation;
        const currentStepId = session.current_step_id;

        // Find current step in simulation
        const step = sim.steps.find(s => s.id === currentStepId);

        if (!step) {
            // Already finished or error
            this.showResult();
            return;
        }

        const state = session.state_json || { profit: 0, traffic: 0, brand: 0 };

        let optionsHtml = step.options.map(opt => `
            <button onclick="RoleplayEngine.submitAnswer(${step.id}, ${opt.id})" class="w-full text-left p-4 md:p-5 rounded-xl border border-slate-700 bg-slate-800/50 hover:bg-slate-800 hover:border-emerald-500/50 transition-all duration-300 group flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-500 group-hover:text-emerald-400 group-hover:border-emerald-500 shrink-0 mt-0.5 transition-colors">
                    <i class="fas fa-check text-xs"></i>
                </div>
                <div class="flex-1 font-semibold text-slate-200 group-hover:text-white leading-relaxed text-sm md:text-base">
                    ${opt.label}
                </div>
            </button>
        `).join('');

        let formattedQuestion = step.question;
        let stageBadgeHtml = '';

        const stageRegex = /^((?:STAGE|PHASE|PRESSURE EVENT|TAHAP)[^\n]+)\n+([\s\S]*)$/i;
        const match = formattedQuestion.match(stageRegex);

        if (match) {
            const badgeText = match[1].trim();
            formattedQuestion = match[2].trim();

            let badgeColorClass = 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400';
            let icon = 'fa-flag';
            if (badgeText.toUpperCase().includes('PRESSURE EVENT') || badgeText.toUpperCase().includes('CRISIS')) {
                badgeColorClass = 'bg-rose-500/10 border-rose-500/30 text-rose-400';
                icon = 'fa-bolt';
            }

            stageBadgeHtml = `<div class="inline-flex items-center gap-1.5 px-3 py-1 ${badgeColorClass} border border-dashed text-[10px] font-black uppercase tracking-widest rounded-md mb-4"><i class="fas ${icon}"></i> ${badgeText}</div>\n`;
        }

        const html = `
            <div class="animate-card-in border border-slate-700 bg-slate-900 rounded-2xl overflow-hidden shadow-2xl">
                <!-- Status Bar -->
                <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between text-xs font-bold uppercase tracking-wider flex-wrap gap-2">
                    <div class="text-slate-400 shrink-0">Step ${step.order} <span class="text-slate-600 mx-1">/</span> ${sim.steps.length}</div>
                    <div class="flex gap-4 overflow-x-auto hide-scrollbar">
                        ${sim.difficulty_level === 'master' ? `
                            <span class="text-amber-400 whitespace-nowrap" title="Profitability"><i class="fas fa-chart-line mr-1"></i> ${state.profitability ?? 50}</span>
                            <span class="text-emerald-400 whitespace-nowrap" title="Growth"><i class="fas fa-rocket mr-1"></i> ${state.growth_rate ?? 50}</span>
                            <span class="text-purple-400 whitespace-nowrap" title="Brand"><i class="fas fa-gem mr-1"></i> ${state.brand_equity ?? 50}</span>
                            <span class="text-blue-400 whitespace-nowrap" title="System"><i class="fas fa-cogs mr-1"></i> ${state.system_strength ?? 50}</span>
                            <span class="text-rose-400 whitespace-nowrap" title="Investor"><i class="fas fa-handshake mr-1"></i> ${state.investor_confidence ?? 50}</span>
                        ` : `
                            <span class="text-emerald-400 whitespace-nowrap" title="Profit"><i class="fas fa-dollar-sign mr-1"></i> ${state.profit > 0 ? '+' + state.profit : (state.profit || 0)}</span>
                            <span class="text-blue-400 whitespace-nowrap" title="Traffic"><i class="fas fa-users mr-1"></i> ${state.traffic > 0 ? '+' + state.traffic : (state.traffic || 0)}</span>
                            <span class="text-purple-400 whitespace-nowrap" title="Brand"><i class="fas fa-gem mr-1"></i> ${state.brand > 0 ? '+' + state.brand : (state.brand || 0)}</span>
                        `}
                    </div>
                </div>
                
                <!-- Question -->
                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        ${stageBadgeHtml}
                        ${step.is_irreversible ? '<div class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-500/10 border border-rose-500/30 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-md mb-4"><i class="fas fa-exclamation-triangle"></i> Keputusan Irreversible</div>' : ''}
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-white mb-8 leading-relaxed whitespace-pre-line">${formattedQuestion}</h3>
                    
                    <div class="space-y-3">
                        ${optionsHtml}
                    </div>
                </div>
            </div>
        `;

        this.mountEl.innerHTML = html;
        this.mountEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    },

    submitAnswer: async function (stepId, optionId) {
        // Disable buttons
        const btns = this.mountEl.querySelectorAll('button');
        btns.forEach(b => { b.disabled = true; b.classList.add('opacity-50', 'cursor-not-allowed'); });

        try {
            const json = await window.coursesHub._post('/api/simulation/answer', {
                simulation_id: this.simulationId,
                step_id: stepId,
                option_id: optionId
            });
            if (!json.success) throw new Error(json.message);

            const data = json.data;

            // Update session state locally
            this.currentData.session.current_step_id = data.next_step_id;
            this.currentData.session.state_json = data.new_state;

            this.showFeedback(data.feedback, data.effects, data.new_state, data.is_finished);
        } catch (e) {
            console.error(e);
            alert("Terjadi kesalahan saat menyimpan pilihan. Coba lagi.");
            btns.forEach(b => { b.disabled = false; b.classList.remove('opacity-50', 'cursor-not-allowed'); });
        }
    },

    showFeedback: function (feedback, effects, state, isFinished) {
        const effHtml = Object.keys(effects).map(k => {
            const v = effects[k];
            if (v === 0) return '';
            const color = v > 0 ? 'text-emerald-400' : 'text-rose-400';
            const icon = v > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            const label = k.charAt(0).toUpperCase() + k.slice(1);
            return `<div class="flex items-center gap-2 ${color} bg-slate-900/50 px-3 py-1.5 rounded-lg text-sm font-bold border border-slate-700"><i class="fas ${icon} text-xs"></i> ${label} ${v > 0 ? '+' + v : v}</div>`;
        }).join('');

        const isMaster = this.currentData.simulation.difficulty_level === 'master';
        let statHtml = '';
        if (isMaster) {
            statHtml = `
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2 my-5">
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-2 text-center">
                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mb-0.5">Profitability</p>
                    <p class="text-base font-black text-amber-400">${state.profitability ?? 50}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-2 text-center">
                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mb-0.5">Growth</p>
                    <p class="text-base font-black text-emerald-400">${state.growth_rate ?? 50}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-2 text-center">
                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mb-0.5">Brand</p>
                    <p class="text-base font-black text-purple-400">${state.brand_equity ?? 50}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-2 text-center">
                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mb-0.5">System</p>
                    <p class="text-base font-black text-blue-400">${state.system_strength ?? 50}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-2 text-center">
                    <p class="text-[8px] text-slate-500 uppercase tracking-wider mb-0.5">Investor</p>
                    <p class="text-base font-black text-rose-400">${state.investor_confidence ?? 50}</p>
                </div>
            </div>`;
        } else {
            statHtml = `
            <div class="grid grid-cols-3 gap-2 my-5">
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-3 text-center">
                    <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Profit</p>
                    <p class="text-xl font-black text-emerald-400">${state.profit ?? 0}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-3 text-center">
                    <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Traffic</p>
                    <p class="text-xl font-black text-blue-400">${state.traffic ?? 0}</p>
                </div>
                <div class="rounded-xl bg-slate-800/60 border border-slate-700/50 p-3 text-center">
                    <p class="text-[9px] text-slate-500 uppercase tracking-wider mb-1">Brand</p>
                    <p class="text-xl font-black text-purple-400">${state.brand ?? 0}</p>
                </div>
            </div>
            `;
        }

        const btnLabel = isFinished
            ? `<i class="fas fa-flag-checkered mr-2"></i>Lihat Hasil Akhir`
            : `Lanjut ke Tahap Berikutnya <i class="fas fa-arrow-right ml-2"></i>`;

        const html = `
            <div class="animate-card-in border border-slate-700 bg-slate-900 rounded-2xl p-6 md:p-8 relative overflow-hidden">
                <div class="h-0.5 w-full bg-gradient-to-r from-emerald-500 via-teal-400 to-violet-500 mb-6"></div>

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0">
                        <i class="fas fa-bolt text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="text-xs text-slate-400 uppercase tracking-widest font-black">Market Reaction</h3>
                        <p class="text-[10px] text-slate-600 mt-0.5">Dampak dari keputusanmu</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mb-5">
                    ${effHtml || '<div class="text-slate-500 text-sm">Tidak ada perubahan signifikan</div>'}
                </div>

                <div class="bg-slate-800/50 border border-slate-700/50 rounded-xl p-4 mb-2">
                    <p class="text-white text-sm leading-relaxed">${feedback}</p>
                </div>

                ${statHtml}

                <button id="sim-next-btn"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-black hover:from-emerald-400 hover:to-teal-400 hover:shadow-[0_4px_20px_rgba(16,185,129,0.4)] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                    ${btnLabel}
                </button>
            </div>
        `;

        this.mountEl.innerHTML = html;

        document.getElementById('sim-next-btn').addEventListener('click', () => {
            if (isFinished) {
                this.showResult();
            } else {
                this.renderStep();
            }
        });
    },

    showResult: async function () {
        this.mountEl.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10">
                <i class="fas fa-sync fa-spin text-emerald-500 text-3xl mb-4"></i>
                <p class="text-slate-400 text-sm animate-pulse">Menghitung Evaluasi Perusahaan...</p>
            </div>
        `;

        try {
            const json = await window.coursesHub._get('/api/simulation/result?simulation_id=' + this.simulationId);
            if (!json.success) throw new Error(json.message);

            const data = json.data;
            const metrics = data.metrics;
            const isPerfect = data.rating === 'Perfect Strategist';

            const isMaster = this.currentData.simulation.difficulty_level === 'master';
            let badgeColor = 'from-slate-600 to-slate-800';
            let icon = 'fa-chart-bar';

            if (data.rating === 'Failed' || data.rating === 'Operator') { badgeColor = 'from-rose-500 to-red-600'; icon = 'fa-skull-crossbones'; }
            if (data.rating === 'Beginner' || data.rating === 'Founder') { badgeColor = 'from-amber-400 to-orange-500'; icon = 'fa-seedling'; }
            if (data.rating === 'Good' || data.rating === 'CEO') { badgeColor = 'from-teal-400 to-emerald-500'; icon = 'fa-bolt'; }
            if (data.rating === 'Expert' || data.rating === 'Builder') { badgeColor = 'from-indigo-500 to-violet-600'; icon = 'fa-crown'; }
            if (isPerfect || data.rating === 'Legend' || data.rating === 'Titan') { badgeColor = 'from-amber-300 via-yellow-400 to-orange-500'; icon = 'fa-trophy'; }

            let metricsHtml = '';
            if (isMaster) {
                metricsHtml = `
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 w-full mb-8">
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-2 px-1">
                        <div class="text-[8px] text-slate-500 uppercase font-black mb-0.5" style="letter-spacing: -0.5px;">Profitability</div>
                        <div class="font-bold text-sm text-amber-400">${metrics.profitability ?? 50}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-2 px-1">
                        <div class="text-[8px] text-slate-500 uppercase font-black mb-0.5">Growth</div>
                        <div class="font-bold text-sm text-emerald-400">${metrics.growth_rate ?? 50}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-2 px-1">
                        <div class="text-[8px] text-slate-500 uppercase font-black mb-0.5">Brand</div>
                        <div class="font-bold text-sm text-purple-400">${metrics.brand_equity ?? 50}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-2 px-1">
                        <div class="text-[8px] text-slate-500 uppercase font-black mb-0.5">System</div>
                        <div class="font-bold text-sm text-blue-400">${metrics.system_strength ?? 50}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-2 px-1">
                        <div class="text-[8px] text-slate-500 uppercase font-black mb-0.5">Investor</div>
                        <div class="font-bold text-sm text-rose-400">${metrics.investor_confidence ?? 50}</div>
                    </div>
                </div>`;
            } else {
                metricsHtml = `
                <div class="grid grid-cols-3 gap-3 max-w-md mx-auto mb-8">
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-3 px-2">
                        <div class="text-[10px] text-slate-500 uppercase font-black mb-1">Profit</div>
                        <div class="font-bold text-sm ${metrics.profit > 0 ? 'text-emerald-400' : 'text-slate-400'}">${metrics.profit > 0 ? '+' + metrics.profit : (metrics.profit || 0)}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-3 px-2">
                        <div class="text-[10px] text-slate-500 uppercase font-black mb-1">Traffic</div>
                        <div class="font-bold text-sm ${metrics.traffic > 0 ? 'text-blue-400' : 'text-slate-400'}">${metrics.traffic > 0 ? '+' + metrics.traffic : (metrics.traffic || 0)}</div>
                    </div>
                    <div class="bg-slate-800/50 border border-slate-700 rounded-xl py-3 px-2">
                        <div class="text-[10px] text-slate-500 uppercase font-black mb-1">Brand</div>
                        <div class="font-bold text-sm ${metrics.brand > 0 ? 'text-purple-400' : 'text-slate-400'}">${metrics.brand > 0 ? '+' + metrics.brand : (metrics.brand || 0)}</div>
                    </div>
                </div>`;
            }

            const html = `
                <div class="animate-card-in border border-slate-700 bg-slate-900 rounded-2xl p-6 md:p-10 text-center relative overflow-hidden shadow-2xl">
                    ${isPerfect || data.rating === 'Legend' || data.rating === 'Titan' ? '<div class="absolute inset-0 bg-yellow-400/5 z-0 background-shine"></div>' : ''}
                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black mb-6">Simulation Complete</p>
                        
                        <!-- Rating Badge -->
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br ${badgeColor} flex items-center justify-center mx-auto mb-6 text-4xl text-white shadow-xl transform rotate-3">
                            <i class="fas ${icon}"></i>
                        </div>
                        
                        <h2 class="text-3xl font-black text-white mb-2">${data.rating}</h2>
                        <p class="text-slate-400 text-sm mb-8">Score Akhir: <strong class="text-white">${data.score}</strong> ${isMaster ? 'Valuation Score' : '/ 100'}</p>
                        
                        <!-- Metrics -->
                        ${metricsHtml}

                        ${data.gained_xp > 0 ? `
                            <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-6 py-3 rounded-full font-bold shadow-[0_0_20px_rgba(16,185,129,0.2)] mb-8">
                                <i class="fas fa-star text-amber-400 shrink-0"></i>
                                Memperoleh +${data.gained_xp} XP
                            </div>
                        ` : `
                            <div class="text-slate-500 text-xs mb-8 italic">Batas klaim XP harian untuk modul ini telah habis.</div>
                        `}

                        <div class="text-center">
                            <button onclick="window.location.reload()" class="text-sm font-bold text-slate-400 hover:text-white transition-colors bg-slate-800 hover:bg-slate-700 px-6 py-2.5 rounded-lg border border-slate-700">
                                <i class="fas fa-undo mr-1"></i> Kembali ke Menu
                            </button>
                        </div>
                    </div>
                </div>
            `;

            this.mountEl.innerHTML = html;

            // Dispatch Gamification Event
            if (data.gained_xp > 0 && typeof window.GamificationEngine !== 'undefined') {
                document.dispatchEvent(new CustomEvent('xp-earned', {
                    detail: { source: 'simulation_complete', title: 'Mastery Simulation', xp: data.gained_xp }
                }));
            }

        } catch (e) {
            console.error(e);
            this.mountEl.innerHTML = `<div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal mengambil hasil evaluasi.</div>`;
        }
    }
};
