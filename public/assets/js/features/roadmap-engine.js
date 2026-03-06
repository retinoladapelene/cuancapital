import { api } from '../services/api.js';
import { select, showToast, showConfirm } from '../utils/helpers.js';

class RoadmapHandler {
    constructor() {
        this.container = null;
        this.cardsWrapper = null;
        this.svg = null;
        this.cards = [];
        this.isMobile = false;
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {

        // Listen for Wizard Submit
        document.addEventListener('wizard:submit', (e) => {
            console.log("Roadmap Engine: Received Wizard Data", e.detail);
            this.generateRoadmap(e.detail);
        });

        // Initialize Container Structure
        const mainContainer = select('#roadmap-steps');
        if (mainContainer) {
            // Ensure the required HTML structure exists
            if (!mainContainer.querySelector('.roadmap-lines')) {
                mainContainer.className = "roadmap-wrapper relative w-full mx-auto pt-10 pb-20 md:px-0";
                mainContainer.innerHTML = `
                    <svg class="roadmap-lines"></svg>
                    <div class="roadmap-cards roadmap-cards-container space-y-24 relative z-10 w-full"></div>
                `;
            }

            this.container = mainContainer;
            this.cardsWrapper = this.container.querySelector('.roadmap-cards');
            this.svg = this.container.querySelector('.roadmap-lines');

            // Pre-render without revealing section (section shown by journeyEngine)
            this.preloadRoadmap();
        }
    }

    /* ===============================
       DATA FETCHING & GENERATION
    =============================== */

    async generateRoadmap(wizardData = null) {
        // Enforce Authentication via Guard
        if (!window.AUTH || !window.AUTH.loggedIn) {
            if (window.openLoginModal) {
                window.openLoginModal('generateRoadmap');
            } else {
                window.location.href = '/login';
            }
            return;
        }

        let btn = null;
        let originalText = '';

        // Handle Legacy Button State
        if (!wizardData) {
            btn = select('#btn-generate-roadmap');
            if (!btn) return;
            originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            btn.disabled = true;
        }

        // Gather Inputs (Wizard or Legacy)
        let inputs;
        if (wizardData) {
            inputs = this._mapWizardToInputs(wizardData);
        } else {
            inputs = this._getSimulationInputs();
        }

        try {
            const response = await api.post('/roadmap/generate', inputs, { suppressAuthRedirect: true, useApiPrefix: true });

            if (!response.success || !response.data || !response.data.job_id) {
                showToast(response.message || 'Gagal memulai pembuatan roadmap.', 'error');
                return;
            }

            const jobId = response.data.job_id;
            let attempts = 0;
            const maxAttempts = 20; // Max ~60s
            let currentDelay = 2000;

            const pollStatus = async () => {
                attempts++;
                if (btn) {
                    if (attempts > 3) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyusun Action Plan...';
                    if (attempts > 7) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghitung Impact Score...';
                }

                try {
                    const statusRes = await fetch(`/api/jobs/${jobId}/status`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        }
                    });
                    const statusData = await statusRes.json();

                    if (statusData.status === 'completed') {
                        // Fetch the final V2 formatted data
                        const finalRes = await api.get('/mentor/roadmap/v2', {}, { suppressAuthRedirect: true, useApiPrefix: true });

                        if (finalRes.success && finalRes.data) {
                            const container = select('#roadmap-container');
                            if (container) container.classList.remove('hidden');

                            this.renderRoadmapV2(finalRes.data);
                            showToast('Roadmap Strategi Berhasil Dibuat! 🚀', 'success');

                            document.dispatchEvent(new CustomEvent('cuan:roadmap-generated', {
                                detail: { referenceId: finalRes.data.id }
                            }));

                            const section = select('#roadmap-container');
                            if (section) section.scrollIntoView({ behavior: 'smooth' });
                        } else {
                            showToast('Gagal memuat mapping roadmap.', 'error');
                        }
                        this._finalizeLoading(btn, originalText, wizardData);
                        return;
                    }

                    if (statusData.status === 'failed') {
                        showToast(statusData.error_message || "Generasi roadmap gagal di background.", "error");
                        this._finalizeLoading(btn, originalText, wizardData);
                        return;
                    }

                    if (attempts >= maxAttempts) {
                        showToast("Waktu pembuatan timeout. Silahkan coba lagi nanti.", "error");
                        this._finalizeLoading(btn, originalText, wizardData);
                        return;
                    }

                    currentDelay = Math.min(currentDelay + 1000, 5000);
                    setTimeout(pollStatus, currentDelay);

                } catch (e) {
                    console.error("Polling error:", e);
                    setTimeout(pollStatus, currentDelay);
                }
            };

            setTimeout(pollStatus, currentDelay);

        } catch (e) {
            console.error(e);
            if (e.message && (e.message.includes('login') || e.message.includes('Unauthenticated') || e.message.includes('Unauthorized'))) {
                showToast('Fitur pembuatan Roadmap ini membutuhkan login.', 'error');
            } else {
                showToast(e.message || 'Gagal memulai pembuatan roadmap', 'error');
            }
            this._finalizeLoading(btn, originalText, wizardData);
        }
    }

    _finalizeLoading(btn, originalText, wizardData) {
        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
        if (wizardData) {
            document.dispatchEvent(new CustomEvent('roadmap:generated'));
        }
    }

    _mapWizardToInputs(data) {
        // Map Wizard Context to API Constants
        let traffic = 1000;
        let conversion = 1.0;
        let margin = 0.3; // Default 30% margin assumption

        // Stage Impact
        if (data.stage === 'growing') traffic = 5000;
        if (data.stage === 'scaling') traffic = 25000;

        // Channel Benchmark
        let channel = data.channel;
        if (data.channel === 'marketplace') conversion = 2.5;
        if (data.channel === 'ads') conversion = 1.8;
        if (data.channel === 'website') {
            conversion = 1.2;
            channel = 'organic'; // Map 'website' to 'organic' for API validation
        }
        if (data.channel === 'sosmed') conversion = 0.8;

        return {
            traffic: traffic,
            conversion_rate: conversion,
            margin: margin,
            channel: channel
        };
    }

    /**
     * Pull simulation inputs from data attributes set by profit-simulator.js
     */
    _getSimulationInputs() {
        const meta = select('#roadmap-sim-meta');
        if (meta && meta.dataset) {
            return {
                traffic: parseFloat(meta.dataset.traffic) || 0,
                conversion_rate: parseFloat(meta.dataset.conversion) || 0,
                margin: parseFloat(meta.dataset.margin) || 0,
                channel: meta.dataset.channel || null,
            };
        }
        // Fallback: read the live input fields
        return {
            traffic: parseFloat(select('[name="traffic"]')?.value || select('#traffic')?.value) || 0,
            conversion_rate: parseFloat(select('[name="conversion"]')?.value || select('#conversion')?.value) || 0,
            margin: parseFloat(select('[name="margin"]')?.value || select('#margin')?.value) || 0,
            channel: (select('[name="channel"]')?.value || select('#channel')?.value) || null,
        };
    }

    // Called on page init: pre-renders content silently without revealing the section
    async preloadRoadmap() {
        if (!localStorage.getItem('auth_token')) return;
        try {
            const response = await api.get('/mentor/roadmap/v2', { useApiPrefix: true });
            if (response.success && response.data) {
                this.renderRoadmapV2(response.data);
                // Do NOT unhide #roadmap-container here — let journeyEngine control visibility
            }
        } catch (e) {
            // Silently fail on init — user may not have a roadmap yet
        }
    }

    // Called when user explicitly navigates to Phase 4
    async loadRoadmap() {
        if (!localStorage.getItem('auth_token')) return;
        try {
            const response = await api.get('/mentor/roadmap/v2', { useApiPrefix: true });
            if (response.success && response.data) {
                const container = select('#roadmap-container');
                if (container) container.classList.remove('hidden');
                this.renderRoadmapV2(response.data);
            } else {
                this.renderEmptyState();
            }
        } catch (e) {
            console.error(e);
            this.renderEmptyState();
        }
    }

    renderEmptyState() {
        const container = select('#roadmap-container');
        if (container) container.classList.add('hidden');
        const btn = select('#btn-generate-roadmap');
        if (btn) btn.disabled = false;
    }

    /* ===============================
       V2 RENDERING (Phase-based)
    =============================== */

    /**
     * Main render entry for the V2 engine response.
     * Renders: Strategy Header → Diagnosis Card → Phase blocks → Step cards
     */
    renderRoadmapV2(data) {
        if (!this.cardsWrapper) return;
        this.cardsWrapper.innerHTML = '';

        const { strategy, confidence_score, reliability, diagnosis, phases, summary } = data;

        // ── Strategy + Confidence Header ─────────────────────────────────────────
        const reliabilityColor = reliability === 'High' ? 'emerald' : (reliability === 'Medium' ? 'amber' : 'rose');
        const strategyLabel = strategy.primary_strategy.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const secondaryLabel = strategy.secondary_strategy.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

        const header = document.createElement('div');
        header.className = 'mb-10 p-6 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-xl animate-fade-in relative overflow-hidden';
        header.innerHTML = `
            <div class="flex flex-col md:flex-row items-start justify-between gap-6 relative z-10">
                <div class="flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Strategi Utama</p>
                    <h3 class="text-2xl font-bold text-white mb-2 leading-tight">${strategyLabel}</h3>
                    <p class="text-sm text-slate-400">Secondary: <span class="text-slate-300">${secondaryLabel}</span></p>
                </div>
                
                <div class="flex flex-col items-end gap-4 w-full md:w-auto">
                    <!-- Confidence Score -->
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Confidence Score</p>
                        <div class="flex items-center justify-end gap-2">
                            <span class="text-3xl font-black text-${reliabilityColor}-400">${confidence_score}%</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-${reliabilityColor}-500/20 text-${reliabilityColor}-400 border border-${reliabilityColor}-500/30">${reliability}</span>
                        </div>
                    </div>
                    
                    <!-- Save Action -->
                    <button id="btn-save-roadmap-blueprint" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Save Blueprint
                    </button>
                </div>
            </div>
            
            ${diagnosis ? `
            <div class="mt-6 pt-5 border-t border-slate-700/50 flex items-center gap-3 relative z-10 bg-rose-500/5 p-4 rounded-xl border-rose-500/10 border">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse shadow-[0_0_8px_rgba(244,63,94,0.6)] shrink-0"></div>
                <p class="text-sm text-slate-300">
                    <span class="font-semibold text-rose-400">Bottleneck:</span> <span class="text-white capitalize mr-2">${diagnosis.primary_problem}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400">Severity: ${diagnosis.severity}%</span>
                </p>
            </div>` : ''}
            
            <!-- Decorative Background Element -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full pointer-events-none -mt-20 -mr-20"></div>
        `;
        this.cardsWrapper.appendChild(header);

        // Bind Save Action
        const btnSave = header.querySelector('#btn-save-roadmap-blueprint');
        if (btnSave) {
            btnSave.addEventListener('click', () => {
                // Pass the actual mentor evaluate OUTPUT (not input) so blueprint restores correctly
                let dataToSave = (window.mentorWizard && window.mentorWizard._lastOutput)
                    ? window.mentorWizard._lastOutput
                    : (window.mentorWizard && window.mentorWizard.data) ? window.mentorWizard.data : {};

                if (window.mentorWizard && typeof window.mentorWizard.openSaveBlueprintModal === 'function') {
                    window.mentorWizard.openSaveBlueprintModal(dataToSave);
                } else {
                    showToast('Gagal menyimpan: Fitur Save Blueprint hanya tersedia melalui Mentor Lab.', 'error');
                }
            });
        }

        // ── Render Each Phase ────────────────────────────────────────────────────
        let globalIndex = 0;
        phases.forEach(phase => {
            // Phase Divider
            const phaseLabel = document.createElement('div');
            phaseLabel.className = 'flex items-center gap-3 my-8 first:mt-2';
            phaseLabel.innerHTML = `
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400 whitespace-nowrap">${phase.name}</span>
                <div class="flex-1 h-px bg-gradient-to-r from-emerald-500/30 to-transparent"></div>
                <span class="text-[10px] text-slate-500">${phase.duration}</span>
            `;
            this.cardsWrapper.appendChild(phaseLabel);

            // Phase Steps
            phase.steps.forEach(step => {
                this._appendStepCardV2(step, globalIndex);
                globalIndex++;
            });
        });

        this.initObservers();
        this.initScrollAnimation();
        this.handleResponsiveMode();
        requestAnimationFrame(() => {
            this.renderConnectors();
            this.watchCompletion();
            this.bindCheckboxes(); // Bind action checkbox toggles after rendering
        });
    }

    _appendStepCardV2(step, index) {
        const alignClass = index % 2 === 0 ? 'md:justify-start' : 'md:justify-end';
        const tools = this.getRecommendedTools(step.category);
        const impactStars = '★'.repeat(Math.round((step.impact_score || 5) / 2));
        const difficultyLabel = step.difficulty_score <= 3 ? 'Easy' : (step.difficulty_score <= 6 ? 'Medium' : 'Hard');
        const difficultyColor = step.difficulty_score <= 3 ? 'emerald' : (step.difficulty_score <= 6 ? 'amber' : 'rose');

        const card = document.createElement('div');
        card.className = `roadmap-card flex ${alignClass} justify-center w-full step-item group perspective-1000`;
        card.dataset.stepId = step.id;
        card.dataset.index = index;
        card.dataset.status = 'unlocked'; // V2 steps are always unlocked

        card.innerHTML = `
            <div class="
                relative w-full md:w-5/12
                bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl
                rounded-2xl
                border border-slate-200 dark:border-slate-700
                shadow-sm transition-all duration-500
                hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.15)] hover:-translate-y-1 hover:border-emerald-500/30
                overflow-hidden z-20 group/card
            " onclick="roadmapHandler.toggleCard(this)">
                
                <!-- Ambient Glow Effect inside card -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                <!-- Header -->
                <div class="p-6 md:p-8 flex justify-between items-start gap-4 cursor-pointer">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">STEP ${index + 1}</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-${difficultyColor}-100 dark:bg-${difficultyColor}-900/30 text-${difficultyColor}-600 dark:text-${difficultyColor}-400">${difficultyLabel}</span>
                            <span class="text-amber-400 text-xs">${impactStars}</span>
                        </div>
                        <h3 class="font-bold text-xl md:text-2xl text-slate-900 dark:text-white mb-1 leading-tight">${step.title}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">${step.description || ''}</p>
                    </div>
                    <div class="transform transition-transform duration-300 chevron-icon mt-2 text-slate-400">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>

                <!-- Body -->
                <div class="card-body hidden border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/30">
                    <div class="p-6 md:p-8 space-y-5">
                        <!-- Reasoning -->
                        <div class="flex items-start gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <i class="fas fa-lightbulb text-amber-400 mt-0.5"></i>
                            <p>${step.reasoning || ''}</p>
                        </div>

                        <!-- Meta Row -->
                        <div class="flex flex-wrap gap-2">
                            ${step.estimated_time ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-xs text-slate-500 dark:text-slate-400"><i class="fas fa-clock"></i> ${step.estimated_time}</span>` : ''}
                            ${step.outcome_type ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-xs text-emerald-600 dark:text-emerald-400"><i class="fas fa-bullseye"></i> ${step.outcome_type.replace(/_/g, ' ')}</span>` : ''}
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-xs text-slate-500"><i class="fas fa-chart-bar"></i> Score: ${step.priority_score}</span>
                        </div>

                        <!-- Actions -->
                        ${step.actions && step.actions.length > 0 ? `
                        <div class="relative z-10 pt-4 mt-4 border-t border-slate-200 dark:border-slate-700 border-dashed" onclick="event.stopPropagation()">
                            <h5 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Action Items</h5>
                            <div class="space-y-3 text-left">
                                ${step.actions.map(action => {
            // Support both string (legacy/mock) and object (DB) actions
            const actionId = typeof action === 'string' ? '' : action.id;
            const actionText = typeof action === 'string' ? action : action.action_text;
            const isComplete = typeof action === 'string' ? false : action.is_completed;
            const hasId = !!actionId;

            return `
                                    <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all duration-300 border border-transparent hover:border-slate-200 dark:hover:border-slate-600 group/item ${hasId ? 'cursor-pointer' : ''}">
                                        <div class="relative flex items-center mt-0.5">
                                            ${hasId ? `
                                                <input type="checkbox" 
                                                    class="peer h-5 w-5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 accent-emerald-500 cursor-pointer roadmap-action-checkbox transition-all group-hover/item:scale-110 group-hover/item:drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                                                    ${isComplete ? 'checked' : ''}
                                                    data-action-id="${actionId}"
                                                >
                                            ` : `
                                                <i class="fas fa-check-circle text-emerald-500 opacity-50 group-hover/item:opacity-100 group-hover/item:scale-110 group-hover/item:drop-shadow-[0_0_8px_rgba(16,185,129,0.5)] transition-all duration-300"></i>
                                            `}
                                        </div>
                                        <span class="text-sm font-medium text-slate-600 dark:text-slate-300 transition-colors select-text group-hover/item:text-slate-900 dark:group-hover/item:text-white ${isComplete ? 'text-emerald-600 line-through decoration-emerald-500/50' : ''} ${hasId ? 'peer-checked:text-emerald-600 peer-checked:line-through peer-checked:decoration-emerald-500/50' : ''}">
                                            ${actionText}
                                        </span>
                                    </label>
                                    `;
        }).join('')}
                            </div>
                        </div>` : ''}

                        <!-- Tools -->
                        ${tools.length > 0 ? `
                        <div class="relative z-10 pt-4 mt-4 border-t border-slate-200 dark:border-slate-700 border-dashed" onclick="event.stopPropagation()">
                            <h5 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Recommended Tools</h5>
                            <div class="flex flex-wrap gap-2">
                                ${tools.map(tool => `
                                    <a href="${tool.url}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:border-emerald-400 hover:text-emerald-500 hover:shadow-[0_0_15px_-3px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 transition-all duration-300 group/tool">
                                        <img src="https://www.google.com/s2/favicons?domain=${tool.domain}&sz=32" class="w-4 h-4 rounded-full group-hover/tool:scale-110 transition-transform duration-300" alt="${tool.name}">
                                        ${tool.name}
                                    </a>
                                `).join('')}
                            </div>
                        </div>` : ''}

                        <!-- Complete Button -->
                        <div class="relative z-10 pt-5 mt-2 border-t border-slate-200 dark:border-slate-700" onclick="event.stopPropagation()">
                            <button
                                class="v2-complete-btn w-full py-3 rounded-xl text-sm font-bold transition-all duration-500
                                       bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400
                                       hover:bg-gradient-to-r hover:from-emerald-500 hover:to-teal-400 hover:text-white
                                       border border-slate-200 dark:border-slate-600 hover:border-transparent hover:shadow-[0_4px_20px_-5px_rgba(16,185,129,0.4)] hover:-translate-y-0.5"
                                data-step-id="${step.id}"
                                onclick="roadmapHandler.markStepComplete(this)">
                                <i class="fas fa-check mr-2"></i>Tandai Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        this.cardsWrapper.appendChild(card);
    }

    /**
     * Mark a V2 library step as completed.
     * Calls POST /api/roadmap/update and updates the card UI optimistically.
     */
    async markStepComplete(btn) {
        const stepId = btn.dataset.stepId;
        if (!stepId) return;

        const card = btn.closest('.roadmap-card');
        const alreadyDone = card?.dataset.status === 'completed';
        if (alreadyDone) return;

        // Optimistic UI
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        try {
            await api.post(`/mentor/roadmap/action/${stepId}/toggle`, null, { useApiPrefix: true });

            // Mark card as completed
            if (card) {
                card.dataset.status = 'completed';
                const inner = card.firstElementChild;
                if (inner) {
                    inner.classList.remove('border-slate-200', 'dark:border-slate-700');
                    inner.classList.add('border-emerald-500/50', 'shadow-[0_0_15px_rgba(16,185,129,0.1)]');
                }
                // Update step label
                const stepLabel = card.querySelector('.step-label-tag');
                if (stepLabel) stepLabel.classList.add('text-emerald-500');
            }

            btn.innerHTML = '<i class="fas fa-check-circle mr-2 text-emerald-500"></i>Selesai!';
            btn.classList.remove('bg-slate-100', 'dark:bg-slate-700', 'text-slate-500', 'hover:bg-emerald-50');
            btn.classList.add('bg-emerald-50', 'dark:bg-emerald-900/20', 'text-emerald-600', 'dark:text-emerald-400', 'border-emerald-400');

            showToast('Step selesai! Lanjut ke step berikutnya. 🚀', 'success');

            // --- PHASE 3: Gamification Event Dispatch ---
            document.dispatchEvent(new CustomEvent('cuan:roadmap-item-completed'));

            // Update Progress Directly for snappier UI, but Observer will also catch it
            this.updateConnectorProgress(true);

        } catch (e) {
            console.error(e);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Tandai Selesai';
            showToast('Gagal menyimpan status. Coba lagi.', 'error');
        }
    }

    /* ===============================
       CARD RENDERING (Legacy flat steps)
    =============================== */

    renderCards(steps) {
        if (!this.cardsWrapper) return;

        this.cardsWrapper.innerHTML = '';

        steps.forEach((step, index) => {
            const isCompleted = step.status === 'completed';
            const isLocked = step.status === 'locked';
            const tools = this.getRecommendedTools(step.strategy_tag);

            // Layout Alignment Logic
            const alignClass = index % 2 === 0 ? 'md:justify-start' : 'md:justify-end';

            const card = document.createElement('div');
            card.className = `roadmap-card flex ${alignClass} justify-center w-full step-item group perspective-1000`;
            card.dataset.stepId = step.id;
            card.dataset.index = index;
            card.dataset.status = step.status;

            // Inner visual card HTML
            const innerHTML = `
                <div class="
                    relative
                    w-full md:w-5/12
                    bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl
                    rounded-2xl
                    border ${isCompleted ? 'border-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'border-slate-200 dark:border-slate-700'}
                    shadow-sm
                    transition-all duration-500
                    hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.15)] hover:-translate-y-1 hover:border-emerald-500/30
                    overflow-hidden
                    z-20 group/card
                " onclick="roadmapHandler.toggleCard(this)">
                
                    <!-- Ambient Glow Effect inside card -->
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/20 rounded-full blur-3xl opacity-0 group-hover/card:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                    
                    <!-- Header -->
                    <div class="p-6 md:p-8 flex justify-between items-start gap-4 cursor-pointer">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider mb-2 ${isCompleted ? 'text-emerald-500' : 'text-slate-400'}">
                                STEP ${index + 1} • ${step.strategy_tag}
                            </div>
                            <h3 class="font-bold text-xl md:text-2xl text-slate-900 dark:text-white mb-2 leading-tight">
                                ${step.title}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2">
                                ${step.description}
                            </p>
                        </div>
                        <div class="transform transition-transform duration-300 chevron-icon mt-2 text-slate-400">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <!-- Body (Expandable) -->
                    <div class="card-body hidden border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-900/30">
                        <div class="p-6 md:p-8 space-y-6">
                            
                            <!-- Actions -->
                            <div class="relative z-10" onclick="event.stopPropagation()">
                                <h5 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-wider">Action Items</h5>
                                <div class="space-y-3 text-left">
                                    ${step.actions.map(action => `
                                        <label class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-all duration-300 border border-transparent hover:border-slate-200 dark:hover:border-slate-600 group/item">
                                            <div class="relative flex items-center mt-0.5">
                                                <input type="checkbox" 
                                                    class="peer h-5 w-5 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 accent-emerald-500 cursor-pointer roadmap-action-checkbox transition-all group-hover/item:scale-110 group-hover/item:drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                                                    ${action.is_completed ? 'checked' : ''}
                                                    ${isLocked ? 'disabled' : ''}
                                                    data-action-id="${action.id}"
                                                >
                                            </div>
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 ${action.is_completed ? 'text-emerald-600 line-through decoration-emerald-500/50' : ''} peer-checked:text-emerald-600 peer-checked:line-through peer-checked:decoration-emerald-500/50 transition-colors select-text group-hover/item:text-slate-900 dark:group-hover/item:text-white">
                                                ${action.action_text}
                                            </span>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                            
                            <!-- Tools -->
                            ${tools.length > 0 ? `
                            <div class="relative z-10 pt-6 border-t border-slate-200 dark:border-slate-700 border-dashed" onclick="event.stopPropagation()">
                                <h5 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-wider">Recommended Tools</h5>
                                <div class="flex flex-wrap gap-3">
                                    ${tools.map(tool => `
                                        <a href="${tool.url}" target="_blank" onclick="event.stopPropagation()" class="inline-flex items-center gap-2 px-4 py-2 bg-white/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-medium text-slate-600 dark:text-slate-300 hover:border-emerald-400 hover:text-emerald-500 hover:shadow-[0_0_15px_-3px_rgba(16,185,129,0.3)] hover:-translate-y-0.5 transition-all duration-300 group/tool">
                                            <img src="https://www.google.com/s2/favicons?domain=${tool.domain}&sz=32" class="w-4 h-4 rounded-full group-hover/tool:scale-110 transition-transform duration-300" alt="${tool.name}">
                                            ${tool.name}
                                        </a>
                                    `).join('')}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Locked Overlay -->
                    ${isLocked ? `
                    <div class="absolute inset-0 bg-slate-100/60 dark:bg-slate-900/60 backdrop-blur-[2px] flex items-center justify-center z-30 cursor-not-allowed transition-all duration-300" onclick="event.stopPropagation()">
                         <div class="bg-white dark:bg-slate-800 px-6 py-3 rounded-full shadow-xl flex items-center gap-3 text-slate-500 text-sm font-bold border border-slate-200 dark:border-slate-700 animate-pulse">
                            <i class="fas fa-lock text-slate-400"></i> Locked Step
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;

            card.innerHTML = innerHTML;
            this.cardsWrapper.appendChild(card);
        });

        this.bindCheckboxes();
        this.initObservers();
        this.initScrollAnimation();

        // Initial responsive check
        this.handleResponsiveMode();

        // Initial render of connectors
        requestAnimationFrame(() => {
            this.renderConnectors();
            this.watchCompletion();
        });
    }

    /* ===============================
       INTERACTION LOGIC (Expand/Checkbox)
    =============================== */

    toggleCard(cardElement) {
        // cardElement is the inner div passed by onclick
        const body = cardElement.querySelector('.card-body');
        const chevron = cardElement.querySelector('.chevron-icon');
        const isHidden = body.classList.contains('hidden');

        if (isHidden) {
            // OPEN
            body.classList.remove('hidden');
            body.style.maxHeight = '0';
            body.style.opacity = '0';
            body.style.overflow = 'hidden';

            void body.offsetHeight; // Force reflow

            body.style.transition = 'max-height 0.6s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.5s ease-out';
            body.style.maxHeight = body.scrollHeight + 'px';
            body.style.opacity = '1';

            chevron.classList.add('rotate-180');
        } else {
            // CLOSE
            body.style.maxHeight = body.scrollHeight + 'px';
            body.style.opacity = '1';
            body.style.overflow = 'hidden';

            void body.offsetHeight; // Force reflow

            body.style.transition = 'max-height 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease-in';
            body.style.maxHeight = '0';
            body.style.opacity = '0';

            chevron.classList.remove('rotate-180');

            setTimeout(() => {
                if (body.style.maxHeight === '0px') {
                    body.classList.add('hidden');
                    body.style.removeProperty('max-height');
                    body.style.removeProperty('opacity');
                    body.style.removeProperty('transition');
                    body.style.removeProperty('overflow');
                }
            }, 500);
        }
    }

    bindCheckboxes() {
        const checkboxes = document.querySelectorAll('.roadmap-action-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', (e) => this.toggleAction(e.target));
        });
    }

    async toggleAction(checkbox) {
        const actionId = checkbox.dataset.actionId;
        const isChecked = checkbox.checked;
        const label = checkbox.closest('label').querySelector('span');

        // Optimistic Update
        if (label) {
            if (isChecked) label.classList.add('text-emerald-600', 'line-through', 'decoration-emerald-500/50');
            else label.classList.remove('text-emerald-600', 'line-through', 'decoration-emerald-500/50');
        }

        try {
            const response = await api.post(`/mentor/roadmap/action/${actionId}/toggle`, null, { useApiPrefix: true });

            if (response.success && response.step_completed) {
                showToast('Step Completed! Unleashing next level...', 'success');
                this.loadRoadmap(); // Reload to update status and unlocks
            }
        } catch (e) {
            console.error(e);
            // Revert
            checkbox.checked = !isChecked;
            if (label) {
                if (isChecked) label.classList.remove('text-emerald-600', 'line-through', 'decoration-emerald-500/50');
                else label.classList.add('text-emerald-600', 'line-through', 'decoration-emerald-500/50');
            }
            showToast('Failed to update action', 'error');
        }
    }


    /* ===============================
       CONNECTOR ENGINE (SVG ROADMAP LINES)
    =============================== */

    renderConnectors() {
        if (!this.svg) return;

        this.cards = Array.from(this.cardsWrapper.querySelectorAll('.roadmap-card'));
        if (this.cards.length < 2) return;

        this.svg.innerHTML = `
        <defs>
            <linearGradient id="gradientLine" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#10b981"/>
                <stop offset="100%" stop-color="#6366f1"/>
            </linearGradient>
        </defs>
        `;

        const wrapperRect = this.svg.getBoundingClientRect();

        this.cards.forEach((card, index) => {
            if (index === this.cards.length - 1) return;

            const nextCard = this.cards[index + 1];

            // Target the main visual block to connect
            const visualA = card.firstElementChild;
            const visualB = nextCard.firstElementChild;

            const rect1 = visualA.getBoundingClientRect();
            const rect2 = visualB.getBoundingClientRect();

            const startX = rect1.left + rect1.width / 2 - wrapperRect.left;
            const startY = rect1.bottom - wrapperRect.top;

            const endX = rect2.left + rect2.width / 2 - wrapperRect.left;
            const endY = rect2.top - wrapperRect.top;

            const path = document.createElementNS("http://www.w3.org/2000/svg", "path");

            const curve = `
                M ${startX} ${startY}
                C ${startX} ${(startY + endY) / 2},
                  ${endX} ${(startY + endY) / 2},
                  ${endX} ${endY}
            `;

            path.setAttribute("d", curve);
            path.setAttribute("class", "connector-base");

            this.svg.appendChild(path);

            const progress = document.createElementNS("http://www.w3.org/2000/svg", "path");
            progress.setAttribute("d", curve);
            progress.setAttribute("class", "connector-progress");
            progress.setAttribute("data-index", index);
            this.svg.appendChild(progress);

            // Set exact length for accurate animation
            const length = progress.getTotalLength();
            progress.style.strokeDasharray = length;
            progress.style.strokeDashoffset = length;
        });

        // initial sync
        this.updateConnectorProgress();
    }


    updateConnectorProgress(animate = false) {
        const cards = document.querySelectorAll('.roadmap-card');
        const lines = document.querySelectorAll('.connector-progress');

        cards.forEach((card, index) => {
            if (index >= lines.length) return;

            const isCompleted = card.dataset.status === 'completed' || card.classList.contains('completed');
            const line = lines[index];
            if (!line) return;

            const length = line.getTotalLength();
            line.style.strokeDasharray = length + 'px';

            if (isCompleted) {
                if (animate && line.style.strokeDashoffset !== '0px') {
                    // Start hidden
                    line.style.transition = 'none';
                    line.style.strokeDashoffset = length + 'px';

                    // Force browser layout reflow
                    void line.getBoundingClientRect();

                    // Animate to full
                    line.style.transition = 'stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    line.style.strokeDashoffset = '0px';
                } else if (!animate) {
                    // Show instantly without animation
                    line.style.transition = 'none';
                    line.style.strokeDashoffset = '0px';
                }
            } else {
                // Keep hidden
                line.style.transition = 'none';
                line.style.strokeDashoffset = length + 'px';
            }
        });
    }

    watchCompletion() {
        if (this.completionObserver) {
            this.completionObserver.disconnect();
        }

        this.completionObserver = new MutationObserver(() => {
            this.updateConnectorProgress(true);
        });

        const cards = document.querySelectorAll('.roadmap-card');
        cards.forEach(card => {
            this.completionObserver.observe(card, { attributes: true, attributeFilter: ['data-status', 'class'] });
        });
    }

    /* ===============================
       OBSERVERS
    =============================== */

    initObservers() {
        const resizeObserver = new ResizeObserver(() => {
            requestAnimationFrame(() => this.renderConnectors());
        });

        if (this.container) resizeObserver.observe(this.container);

        // Observe cards for expansion
        const visuals = this.cardsWrapper.querySelectorAll('.roadmap-card > div'); // Inner visuals
        visuals.forEach(v => resizeObserver.observe(v));

        window.addEventListener("resize", () => {
            this.handleResponsiveMode();
            this.renderConnectors();
        });
    }

    /* ===============================
       SCROLL TRIGGERED ANIMATION
    =============================== */

    initScrollAnimation() {
        // Observer to reveal connectors as they come into view
        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.renderConnectors();
                        // Optional: Could trigger staggered reveal of cards
                    }
                });
            },
            { threshold: 0.1 }
        );

        if (this.cardsWrapper) observer.observe(this.cardsWrapper);
    }

    /* ===============================
       RESPONSIVE LOGIC
    =============================== */

    handleResponsiveMode() {
        this.isMobile = window.innerWidth < 768;

        if (!this.cardsWrapper) return;

        // Dynamic Spacing Adjustment
        if (this.isMobile) {
            this.cardsWrapper.classList.remove("space-y-24");
            this.cardsWrapper.classList.add("space-y-16");
        } else {
            this.cardsWrapper.classList.remove("space-y-16");
            this.cardsWrapper.classList.add("space-y-24");
        }
    }

    /* ===============================
       SVG TOOLS
    =============================== */

    getGradientDefs() {
        return `
            <defs>
                <linearGradient id="flowGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#10B981" />
                    <stop offset="100%" stop-color="#22D3EE" />
                </linearGradient>
                <filter id="neon-glow" x="-50%" y="-50%" width="200%" height="200%">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="2" result="blur5" />
                    <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur10" />
                    <feMerge>
                        <feMergeNode in="blur5" />
                        <feMergeNode in="blur10" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
            </defs>
        `;
    }

    // Helpers — map category/tag to tool recommendations
    getRecommendedTools(categoryOrTag) {
        const toolsMap = {
            // V2 categories
            'traffic': [
                { name: 'Canva', url: 'https://canva.com', domain: 'canva.com' },
                { name: 'CapCut', url: 'https://capcut.com', domain: 'capcut.com' },
                { name: 'Meta Ads', url: 'https://business.facebook.com/', domain: 'facebook.com' },
                { name: 'TikTok Ads', url: 'https://ads.tiktok.com/', domain: 'tiktok.com' },
                { name: 'SEMrush', url: 'https://semrush.com', domain: 'semrush.com' }
            ],
            'conversion': [
                { name: 'Google Optimize', url: 'https://optimize.google.com', domain: 'google.com' },
                { name: 'Hotjar', url: 'https://hotjar.com', domain: 'hotjar.com' },
                { name: 'Mailchimp', url: 'https://mailchimp.com', domain: 'mailchimp.com' },
                { name: 'Typeform', url: 'https://typeform.com', domain: 'typeform.com' }
            ],
            'finance': [
                { name: 'Google Sheets', url: 'https://sheets.google.com', domain: 'google.com' },
                { name: 'Wave Apps', url: 'https://waveapps.com', domain: 'waveapps.com' },
                { name: 'Jurnal.id', url: 'https://jurnal.id', domain: 'jurnal.id' },
                { name: 'Xero', url: 'https://xero.com', domain: 'xero.com' }
            ],
            // Legacy tags mapping
            'Traffic Scaling': [
                { name: 'Canva', url: 'https://canva.com', domain: 'canva.com' },
                { name: 'CapCut', url: 'https://capcut.com', domain: 'capcut.com' },
                { name: 'Meta Ads', url: 'https://business.facebook.com/', domain: 'facebook.com' },
                { name: 'TikTok Ads', url: 'https://ads.tiktok.com/', domain: 'tiktok.com' },
                { name: 'Hootsuite', url: 'https://hootsuite.com', domain: 'hootsuite.com' }
            ],
            'Margin Improvement': [
                { name: 'Google Sheets', url: 'https://sheets.google.com', domain: 'google.com' },
                { name: 'Excel', url: 'https://office.com', domain: 'office.com' },
                { name: 'Jurnal.id', url: 'https://jurnal.id', domain: 'jurnal.id' },
                { name: 'Moka POS', url: 'https://mokapos.com', domain: 'mokapos.com' }
            ],
            'Monetization Expansion': [
                { name: 'Midtrans', url: 'https://midtrans.com', domain: 'midtrans.com' },
                { name: 'Xendit', url: 'https://xendit.co', domain: 'xendit.co' },
                { name: 'Mailchimp', url: 'https://mailchimp.com', domain: 'mailchimp.com' },
                { name: 'Notion', url: 'https://notion.so', domain: 'notion.so' },
                { name: 'Stripe', url: 'https://stripe.com', domain: 'stripe.com' }
            ]
        };
        return toolsMap[categoryOrTag] || [];
    }
}

export const roadmapHandler = new RoadmapHandler();
window.roadmapHandler = roadmapHandler;

