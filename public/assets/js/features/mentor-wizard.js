/**
 * @file mentor-wizard.js
 * @description Interactive Wizard logic for Business Mentor Lab (Strategic Engine V2).
 * Handles input collection, API communication, and Result Rendering.
 */

class MentorWizard {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 5; // V3: 5 steps (3 planning + 2 diagnostic)

        // Initial Data State (Matches StrategicInput DTO keys)
        this.data = {
            // ── Step 1–3: Planning fields ──────────────────────────────
            businessType: 'general',
            riskIntent: 'stable_income',
            capital: 'under_5',
            grossMargin: 30,
            experienceLevel: 1.0,
            targetRevenue: 10000000,
            timeframeMonths: 6,
            channel: 'marketplace',
            stage: 'running',
            // ── Step 4: Actual business data (diagnostic fields) ───────
            actualRevenue: 0,
            actualExpenses: 0,
            cashBalance: 0,
            adSpend: 0,
            avgOrderValue: 0,
            repeatRate: 0,
            businessAge: 0,
            // ── Step 5: Problem multi-select ────────────────────────────
            problemAreas: [],
        };

        this.charts = {}; // Store chart instances
        this.isLoading = false;

        this.init();
    }

    init() {
        console.log("Strategic Engine V2 Initialized");
        this.bindGlobalEvents();
        this.updateUI();
    }

    // --- 1. EVENT BINDING ---

    bindGlobalEvents() {
        // A. Card Selection (Business Type, Intent, Capital, Timeframe, Stage, Channel)
        document.querySelectorAll('.wizard-card-select, .wizard-icon-select').forEach(card => {
            card.addEventListener('click', (e) => {
                const group = card.dataset.group || 'stage'; // fallback for step 1 stage
                const value = card.dataset.value;

                // Update State
                this.data[group] = value;

                // Visual Update
                this.updateSelectionVisuals(group, value);

                if (group === 'stage') this.data.stage = value; // Explicit handle if needed
                if (group === 'channel') this.data.channel = value;

                console.log(`Selected ${group}: ${value}`, this.data);
            });
        });

        // B. Chips Selection (Experience)
        document.querySelectorAll('.wizard-chip-select').forEach(chip => {
            chip.addEventListener('click', (e) => {
                const group = chip.dataset.group;
                const value = parseFloat(chip.dataset.value);

                this.data[group] = value;

                // Visual Update (Unique for chips)
                document.querySelectorAll(`.wizard-chip-select[data-group="${group}"]`).forEach(c => {
                    c.classList.remove('bg-emerald-500', 'text-white', 'active');
                    c.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-700');
                });
                chip.classList.remove('bg-slate-100', 'text-slate-600', 'dark:bg-slate-700');
                chip.classList.add('bg-emerald-500', 'text-white', 'active');
            });
        });

        // C. Sliders (Margin, Revenue)
        this.bindSlider('margin-slider', 'margin-display', 'grossMargin', '%');
        this.bindSlider('target-revenue-slider', 'target-revenue-input', 'targetRevenue', 'RP', true);

        // D. Step 4: currency text inputs
        ['actual-revenue-input', 'actual-expenses-input', 'cash-balance-input', 'ad-spend-input', 'aov-input'].forEach(id => {
            this.bindCurrencyInput(id);
        });
        // D2. Step 4: plain numeric inputs
        ['repeat-rate-input', 'business-age-input'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('input', e => {
                this.data[el.dataset.wizardField] = parseFloat(e.target.value) || 0;
            });
        });

        // E. Step 5: Problem multi-select cards
        document.querySelectorAll('.problem-card').forEach(card => {
            card.addEventListener('click', () => {
                const problem = card.dataset.problem;
                card.classList.toggle('selected');
                if (card.classList.contains('selected')) {
                    if (!this.data.problemAreas.includes(problem)) this.data.problemAreas.push(problem);
                } else {
                    this.data.problemAreas = this.data.problemAreas.filter(p => p !== problem);
                }
            });
        });

        // F. Navigation
        document.getElementById('wizard-btn-next')?.addEventListener('click', () => this.nextStep());
        document.getElementById('wizard-btn-back')?.addEventListener('click', () => this.prevStep());
        document.getElementById('wizard-btn-submit')?.addEventListener('click', () => this.submitAnalysis());
    }

    bindSlider(sliderId, displayId, dataKey, suffix = '', isCurrency = false) {
        const slider = document.getElementById(sliderId);
        const display = document.getElementById(displayId);

        if (!slider || !display) return;

        const updateVal = (val) => {
            this.data[dataKey] = parseFloat(val);
            if (isCurrency) {
                // If input element
                if (display.tagName === 'INPUT') display.value = new Intl.NumberFormat('id-ID').format(val);
                else display.innerText = new Intl.NumberFormat('id-ID').format(val);
            } else {
                display.innerText = val + suffix;
            }
        };

        // Slider Input
        slider.addEventListener('input', (e) => updateVal(e.target.value));

        // Manual Input (Two-way)
        if (display.tagName === 'INPUT') {
            display.addEventListener('change', (e) => {
                let val = parseInt(e.target.value.replace(/\./g, '').replace(/[^0-9]/g, '')) || 0;
                slider.value = val;
                updateVal(val);
            });
        }
    }

    bindCurrencyInput(id) {
        const el = document.getElementById(id);
        if (!el) return;
        const field = el.dataset.wizardField;
        const fmt = v => v > 0 ? new Intl.NumberFormat('id-ID').format(v) : '';
        el.addEventListener('focus', () => { if (this.data[field] > 0) el.value = this.data[field]; });
        el.addEventListener('blur', () => { if (this.data[field] > 0) el.value = fmt(this.data[field]); });
        el.addEventListener('input', () => {
            const raw = parseInt(el.value.replace(/[^0-9]/g, '')) || 0;
            this.data[field] = raw;
        });
    }

    updateSelectionVisuals(group, selectedValue) {
        // Find all items in this group
        const items = document.querySelectorAll(`[data-group="${group}"], [data-value="${selectedValue}"]`);
        // Note: The selector above is a bit broad, better to scope by container if hierarchy exists.
        // But given the unique groups, let's target by group attribute primarily.

        const groupItems = document.querySelectorAll(`[data-group="${group}"]`);

        // Special case for step 1 stage which might default group if not set
        if (groupItems.length === 0 && group === 'stage') {
            // fallback for cards without data-group but are siblings
            // Logic: Find the selected card, get its siblings.
        }

        groupItems.forEach(item => {
            const isSelected = item.dataset.value == selectedValue; // loose equality for strings/nums

            if (isSelected) {
                item.classList.add('active', 'border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                item.classList.remove('border-slate-200', 'dark:border-slate-700');
                // Icon handling
                if (item.classList.contains('wizard-icon-select')) {
                    item.classList.add('text-emerald-500', 'ring-2', 'ring-emerald-500/50');
                    item.classList.remove('text-slate-400');
                }
            } else {
                item.classList.remove('active', 'border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
                item.classList.add('border-slate-200', 'dark:border-slate-700');
                // Icon handling
                if (item.classList.contains('wizard-icon-select')) {
                    item.classList.remove('text-emerald-500', 'ring-2', 'ring-emerald-500/50');
                    item.classList.add('text-slate-400');
                }
            }
        });
    }

    // --- 2. NAVIGATION ---

    nextStep() {
        if (this.validateStep(this.currentStep)) {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.updateUI();
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateUI();
        }
    }

    validateStep(step) {
        // Basic validation
        if (step === 1 && !this.data.riskIntent) { showToast("Pilih tujuan bisnismu (Intent).", "error"); return false; }
        // Add more if needed
        return true;
    }

    updateUI() {
        // Show/Hide Steps
        document.querySelectorAll('.wizard-step').forEach(el => {
            el.classList.add('hidden');
            if (parseInt(el.dataset.step) === this.currentStep) {
                el.classList.remove('hidden');
                el.classList.add('animate-fade-in-right');
            }
        });

        // Update Step Indicator
        const indicator = document.getElementById('wizard-step-indicator');
        if (indicator) {
            indicator.innerText = `Step ${this.currentStep}/${this.totalSteps}`;
        }

        // Update Progress Bar
        const progressBar = document.getElementById('wizard-progress-bar');
        if (progressBar) {
            const percentage = (this.currentStep / this.totalSteps) * 100;
            progressBar.style.width = `${percentage}%`;
        }

        // Buttons
        const backBtn = document.getElementById('wizard-btn-back');
        const nextBtn = document.getElementById('wizard-btn-next');
        const submitBtn = document.getElementById('wizard-btn-submit');

        if (backBtn) backBtn.classList.toggle('invisible', this.currentStep === 1);

        if (this.currentStep === this.totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    // --- 3. SUBMISSION & ENGINE INTEGRATION ---

    async submitAnalysis() {
        if (this.isLoading) return;
        this.isLoading = true;

        // Show Loading
        const loadingOverlay = document.getElementById('mentor-loading');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        const boardContainer = document.getElementById('mentor-dashboard');

        try {
            const token = localStorage.getItem('auth_token') || '';
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            // Send to Backend (Returns 202 Accepted with job_id)
            const endpoint = token ? '/api/mentor/evaluate' : '/mentor/evaluate';
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(this.data)
            });

            if (response.status === 429) {
                showToast("Sistem sedang sibuk. Mohon tunggu 1 menit sebelum mencoba lagi.", "error");
                this.stopLoading(loadingOverlay);
                return;
            }

            const initialResult = await response.json();

            if (!initialResult.success || !initialResult.data.job_id) {
                showToast(initialResult.message || "Gagal memulai analisis strategi.", "error");
                this.stopLoading(loadingOverlay);
                return;
            }

            const jobId = initialResult.data.job_id;

            // --- Polling Initialization ---
            let attempts = 0;
            const maxAttempts = 20; // Max ~60 seconds
            let currentDelay = 2000; // Start with 2 seconds backoff

            const pollStatus = async () => {
                attempts++;

                // UX Text updates
                const loadingText = document.getElementById('mentor-loading-text');
                if (loadingText) {
                    if (attempts > 3) loadingText.innerText = "Mengkalkulasi profitabilitas projection...";
                    if (attempts > 7) loadingText.innerText = "Menyusun Action Plan dari AI...";
                    if (attempts > 12) loadingText.innerText = "Membutuhkan waktu sedikit lebih lama dari biasanya...";
                }

                try {
                    const statusRes = await fetch(`/api/jobs/${jobId}/status`, { headers });
                    const statusData = await statusRes.json();

                    if (statusData.status === 'completed') {
                        // Job done. Now fetch the actual results.
                        // Wait, the API returns reference_id! We need to get the latest simulation.
                        const finalRes = await fetch('/api/mentor/evaluation/latest', { headers });
                        const finalResult = await finalRes.json();

                        if (finalResult.success) {
                            this.handleSuccessfulResult(finalResult.data, boardContainer, loadingOverlay);
                        } else {
                            showToast("Gagal memuat hasil analisis.", "error");
                            this.stopLoading(loadingOverlay);
                        }
                        return; // Exit loop
                    }

                    if (statusData.status === 'failed') {
                        showToast(statusData.error_message || "Proses analisis gagal di background.", "error");
                        this.stopLoading(loadingOverlay);
                        return;
                    }

                    // Still processing or pending
                    if (attempts >= maxAttempts) {
                        showToast("Waktu tunggu habis (Timeout). Coba lagi nanti.", "error");
                        this.stopLoading(loadingOverlay);
                        return;
                    }

                    // Schedule next poll with exponential backoff (Max 5s delay)
                    currentDelay = Math.min(currentDelay + 1000, 5000);
                    setTimeout(pollStatus, currentDelay);

                } catch (e) {
                    console.error("Polling Network Error:", e);
                    // Don't kill loop on network blip, just retry until maxAttempts
                    setTimeout(pollStatus, currentDelay);
                }
            };

            // Start loop
            setTimeout(pollStatus, currentDelay);

        } catch (error) {
            console.error("Engine Error:", error);
            showToast("Terjadi kesalahan sistem.", "error");
            this.stopLoading(loadingOverlay);
        }
    }

    stopLoading(overlay) {
        this.isLoading = false;
        if (overlay) overlay.classList.add('hidden');
        const loadingText = document.getElementById('mentor-loading-text');
        if (loadingText) loadingText.innerText = "AI sedang menyusun DNA Bisnis Anda...";
    }

    handleSuccessfulResult(data, boardContainer, loadingOverlay) {
        // Switch to Dashboard View FIRST so Chart.js can compute dimensions
        document.getElementById('mentor-wizard-container').classList.add('hidden');
        document.getElementById('mentor-dashboard').classList.remove('hidden');

        // Focus Mode: Hide Input Panel, Expand Dashboard entirely
        const inputPanel = document.getElementById('mentor-input-panel');
        if (inputPanel) inputPanel.classList.add('hidden');

        if (boardContainer) {
            boardContainer.classList.remove('lg:col-span-8');
            boardContainer.classList.add('lg:col-span-12');
        }

        this.renderResult(data);

        // Gamification + Refresh
        document.dispatchEvent(new CustomEvent('cuan:mentor-evaluated', {
            detail: { referenceId: data.id }
        }));
        if (window.Gamification) window.Gamification.refresh();

        if (boardContainer) boardContainer.scrollIntoView({ behavior: 'smooth' });

        this.stopLoading(loadingOverlay);
    }

    // --- 4. RESULT RENDERING ---

    renderResult(data) {
        if (!data) {
            console.warn('renderResult called with no data');
            return;
        }
        console.log("Rendering Result:", data);

        // Store for later use (e.g. saving blueprint from roadmap view)
        this._lastOutput = data;

        const { metrics, strategy, recommendations } = data;

        // A. Baseline Metrics (Existing UI slots replugged)
        if (document.getElementById('res-revenue')) document.getElementById('res-revenue').innerText = "Strategy Score: " + (metrics && metrics.feasibilityScore) + "/100";

        // Let's create a dedicated Result Renderer injection to be safe.
        const dashboard = document.getElementById('mentor-dashboard');
        if (dashboard) dashboard.innerHTML = this.buildResultHTML(data);

        // Render Charts
        this.renderChart(metrics);
        this.renderProjectionChart(data.projections || []);

        // Bind New Actions
        this.bindResultActions(data);
    }

    renderChart(metrics) {
        if (typeof Chart === 'undefined') return;

        const ctx = document.getElementById('mentorScoresChart');
        if (!ctx) return;

        if (this.charts['mentorRadar']) {
            this.charts['mentorRadar'].destroy();
        }

        // ─── Theme-aware colors ──────────────────────────────────────────
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.10)' : 'rgba(15,23,42,0.10)';
        const labelColor = isDark ? 'rgba(255,255,255,0.70)' : 'rgba(15,23,42,0.70)';
        const tooltipBg = isDark ? 'rgba(15,23,42,0.90)' : 'rgba(255,255,255,0.95)';
        const tooltipBodyColor = isDark ? 'rgba(255,255,255,0.90)' : 'rgba(15,23,42,0.90)';
        // ─────────────────────────────────────────────────────────────────

        const safeMetrics = metrics || { feasibilityScore: 0, profitScore: 0, riskScore: 0, efficiencyScore: 0 };
        const safetyScore = 100 - (safeMetrics.riskScore || 0);

        this.charts['mentorRadar'] = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Feasibility', 'Profitability', 'Risk Safety', 'Efficiency'],
                datasets: [{
                    label: 'Strategic Score',
                    data: [
                        safeMetrics.feasibilityScore || 0,
                        safeMetrics.profitScore || 0,
                        safetyScore,
                        safeMetrics.efficiencyScore || 0
                    ],
                    backgroundColor: 'rgba(52,211,153,0.25)',
                    borderColor: 'rgba(52,211,153,1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(52,211,153,1)',
                    pointBorderColor: isDark ? '#fff' : '#0f172a',
                    pointHoverBackgroundColor: isDark ? '#fff' : '#0f172a',
                    pointHoverBorderColor: 'rgba(52,211,153,1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: gridColor },
                        grid: { color: gridColor },
                        pointLabels: {
                            color: labelColor,
                            font: { size: 10, family: "'Inter', sans-serif" }
                        },
                        ticks: { display: false, min: 0, max: 100 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipBodyColor,
                        bodyColor: tooltipBodyColor,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    }

    renderProjectionChart(projections) {
        if (typeof Chart === 'undefined' || !projections || projections.length === 0) return;

        const ctx = document.getElementById('mentorProjectionChart');
        if (!ctx) return;

        if (this.charts['mentorProjection']) {
            this.charts['mentorProjection'].destroy();
        }

        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(15,23,42,0.08)';
        const labelColor = isDark ? 'rgba(255,255,255,0.60)' : 'rgba(15,23,42,0.60)';
        const tooltipBg = isDark ? 'rgba(15,23,42,0.95)' : 'rgba(255,255,255,0.98)';
        const tooltipBodyColor = isDark ? 'rgba(255,255,255,0.90)' : 'rgba(15,23,42,0.90)';

        const labels = projections.map(p => 'Bln ' + p.month);
        const fmtShort = (n) => {
            if (Math.abs(n) >= 1000000) return (n / 1000000).toFixed(1) + ' jt';
            if (Math.abs(n) >= 1000) return (n / 1000).toFixed(0) + ' rb';
            return n.toString();
        };

        this.charts['mentorProjection'] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: projections.map(p => p.revenue),
                        borderColor: 'rgba(6,182,212,1)',
                        backgroundColor: 'rgba(6,182,212,0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(6,182,212,1)',
                    },
                    {
                        label: 'Biaya',
                        data: projections.map(p => p.costs),
                        borderColor: 'rgba(244,63,94,1)',
                        backgroundColor: 'rgba(244,63,94,0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(244,63,94,1)',
                    },
                    {
                        label: 'Profit',
                        data: projections.map(p => p.profit),
                        borderColor: 'rgba(52,211,153,1)',
                        backgroundColor: 'rgba(52,211,153,0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(52,211,153,1)',
                    },
                    {
                        label: 'Kumulatif',
                        data: projections.map(p => p.cumulative),
                        borderColor: 'rgba(245,158,11,1)',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 2,
                        pointBackgroundColor: 'rgba(245,158,11,1)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: {
                            color: labelColor,
                            font: { size: 11, family: "'Inter', sans-serif" }
                        }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: labelColor,
                            font: { size: 10, family: "'Inter', sans-serif" },
                            callback: function (val) { return fmtShort(val); }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipBodyColor,
                        bodyColor: tooltipBodyColor,
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: true,
                        callbacks: {
                            label: function (ctx) {
                                return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                            }
                        }
                    }
                }
            }
        });
    }

    buildResultHTML(data) {
        const { metrics, strategy, recommendations, projections, input,
            diagnoses, gap_analysis: gap, cash_health: cash,
            is_diagnostic: isDiagnostic, problem_summary: problemSummary } = data || {};

        // Safety Defaults
        const safeMetrics = Object.assign({
            feasibilityScore: 0, profitScore: 0, riskScore: 0, efficiencyScore: 0,
            runwayMonths: 0, estimatedMonthlyBurn: 0, roiMultiplier: 0,
            estimatedProfit: 0, breakEvenMonth: 0, netMarginPct: 0, capitalEfficiencyPct: 0
        }, metrics || {});
        const safeStrategy = strategy || { label: 'Unknown Strategy', description: 'Data unavailable.' };
        const safeRecs = Array.isArray(recommendations) ? recommendations : [];
        const safeDiagnoses = Array.isArray(diagnoses) ? diagnoses : [];
        const safeInput = input || {};
        const safeGap = gap || {};
        const safeCash = cash || {};

        const scoreColor = s => s > 80 ? 'text-emerald-500' : (s > 50 ? 'text-amber-500' : 'text-rose-500');
        const scoreBg = s => s > 80 ? 'bg-emerald-500' : (s > 50 ? 'bg-amber-500' : 'bg-rose-500');
        const fmtCurrency = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
        const bepColor = safeMetrics.breakEvenMonth <= 3 ? 'text-emerald-400' : (safeMetrics.breakEvenMonth <= 6 ? 'text-amber-400' : 'text-rose-400');
        const marginColor = safeMetrics.netMarginPct >= 25 ? 'text-emerald-400' : (safeMetrics.netMarginPct >= 10 ? 'text-amber-400' : 'text-rose-400');

        const severityBadge = (sev) => ({
            critical: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800',
            warning: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
            info: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800',
        }[sev] || '');
        const severityIcon = (sev) => ({ critical: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' }[sev] || 'fa-circle');

        // Cash health UI helpers
        const cashLevel = safeCash.level || 'info';
        const cashColor = cashLevel === 'healthy' ? 'emerald' : (cashLevel === 'caution' ? 'amber' : 'rose');
        const cashLabel = cashLevel === 'healthy' ? '✅ Sehat' : (cashLevel === 'caution' ? '⚠️ Hati-hati' : '🚨 Kritis');

        // Gap analysis
        const hasGap = safeGap.actual_revenue > 0;
        const gapPct = safeGap.revenue_gap_pct || 0;
        const actualPct = hasGap ? Math.max(5, 100 - gapPct) : 0;

        // Render per-diagnosis Action Plan rows
        const renderRecs = () => {
            if (safeRecs.length === 0) return `<p class="text-sm text-slate-400 italic">Tidak ada rekomendasi spesifik tersedia.</p>`;
            return safeRecs.map((rec, i) => {
                const isObj = typeof rec === 'object';
                const text = isObj ? rec.text : rec;
                const label = isObj ? rec.label : null;
                const sev = isObj ? rec.severity : 'info';
                return `
                    <li class="group flex items-start gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/30 border border-slate-100 dark:border-slate-600/30 hover:bg-white dark:hover:bg-slate-700 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                        <div class="w-7 h-7 rounded-full ${sev === 'critical' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400' : sev === 'warning' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'} flex items-center justify-center flex-shrink-0">
                            <span class="text-[10px] font-black">${i + 1}</span>
                        </div>
                        <div class="flex-1">
                            ${label ? `<span class="text-[10px] font-bold uppercase tracking-wider ${sev === 'critical' ? 'text-rose-500' : sev === 'warning' ? 'text-amber-500' : 'text-blue-500'} block mb-0.5">${label}</span>` : ''}
                            <p class="text-sm text-slate-700 dark:text-slate-300 font-medium leading-relaxed">${text}</p>
                        </div>
                    </li>`;
            }).join('');
        };

        return `
        <div class="animate-fade-in-up space-y-6">

            <!-- ── 1. HEADER: Strategy + Problem Summary ── -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-5 md:p-8 text-white relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none"><i class="fas fa-stethoscope text-9xl"></i></div>
                <div class="relative z-10">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-wider">
                            <i class="fas fa-${isDiagnostic ? 'stethoscope' : 'chess-king'} text-emerald-400"></i>
                            ${isDiagnostic ? 'Diagnostic Report' : 'Strategic DNA'}
                        </span>
                        ${safeDiagnoses.filter(d => d.severity === 'critical').length > 0 ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-xs font-bold text-rose-300"><i class="fas fa-exclamation-circle"></i> ${safeDiagnoses.filter(d => d.severity === 'critical').length} Kritis</span>` : ''}
                        ${safeDiagnoses.filter(d => d.severity === 'warning').length > 0 ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-xs font-bold text-amber-300"><i class="fas fa-exclamation-triangle"></i> ${safeDiagnoses.filter(d => d.severity === 'warning').length} Perlu Perhatian</span>` : ''}
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black mb-2">${safeStrategy.label}</h2>
                    <p class="text-slate-400 text-base mb-6 max-w-2xl">${problemSummary || safeStrategy.description}</p>

                    <!-- Score bars + Radar -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-4 items-center">
                        <div class="md:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            ${[
                ['Feasibility', safeMetrics.feasibilityScore, 'fa-magic', 'emerald', 'score_feasibility'],
                ['Profitability', safeMetrics.profitScore, 'fa-chart-line', 'blue', 'score_profitability'],
                ['Risk Safety', 100 - safeMetrics.riskScore, 'fa-shield-alt', 'purple', 'score_risk'],
                ['Efficiency', safeMetrics.efficiencyScore, 'fa-cogs', 'amber', 'score_efficiency'],
            ].map(([label, score, icon, color, term]) => `
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 backdrop-blur-sm hover:-translate-y-1 transition-transform">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="biz-term text-xs text-slate-300 uppercase font-bold flex items-center gap-1.5 cursor-help" data-term="${term}">
                                            <i class="fas ${icon} text-${color}-400 text-[10px]"></i> ${label}
                                        </div>
                                        <div class="text-2xl font-black ${scoreColor(score)}">${score}</div>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full ${scoreBg(score)} rounded-full" style="width:${score}%"></div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <div class="md:col-span-4 flex justify-center">
                            <div style="max-width:220px;height:220px;width:100%;"><canvas id="mentorScoresChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            ${safeDiagnoses.length > 0 ? `
            <!-- ── 2. DIAGNOSIS LIST ── -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 md:p-7 border border-slate-200 dark:border-slate-700 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-stethoscope text-rose-500"></i> Masalah yang Terdeteksi
                </h3>
                <div class="space-y-3">
                    ${safeDiagnoses.map(d => `
                        <div class="flex items-start gap-3 p-4 rounded-2xl ${d.severity === 'critical' ? 'bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40' : d.severity === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40' : 'bg-slate-50 dark:bg-slate-700/30 border border-slate-200 dark:border-slate-700'}">
                            <i class="fas ${severityIcon(d.severity)} mt-0.5 ${d.severity === 'critical' ? 'text-rose-500' : d.severity === 'warning' ? 'text-amber-500' : 'text-blue-500'}"></i>
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider ${d.severity === 'critical' ? 'text-rose-600 dark:text-rose-400' : d.severity === 'warning' ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400'}">${d.label}</span>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-0.5">${d.description}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>` : ''}

            <!-- ── 3. GAP ANALYSIS + CASH HEALTH (side by side when available) ── -->
            ${(hasGap || safeCash.score !== undefined) ? `
            <div class="grid grid-cols-1 ${hasGap && safeCash.score !== undefined ? 'md:grid-cols-2' : ''} gap-4">
                ${hasGap ? `
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-md">
                    <h4 class="biz-term text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 cursor-help w-max" data-term="mentor_gap_analysis">
                        <i class="fas fa-exchange-alt text-violet-500"></i> Gap Analysis: Actual vs Target
                    </h4>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-500">Revenue Aktual</span>
                                <span class="text-slate-900 dark:text-white">${fmtCurrency(safeGap.actual_revenue)}</span>
                            </div>
                            <div class="w-full h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width:${actualPct}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-500">Target Revenue</span>
                                <span class="text-slate-400">${fmtCurrency(safeGap.target_revenue)}</span>
                            </div>
                            <div class="w-full h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-slate-300 dark:bg-slate-600 rounded-full" style="width:100%"></div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-100 dark:border-slate-700">
                            <span class="text-xs text-slate-500">Gap ke target</span>
                            <span class="text-sm font-black ${gapPct > 50 ? 'text-rose-500' : gapPct > 20 ? 'text-amber-500' : 'text-emerald-500'}">${gapPct > 0 ? gapPct.toFixed(0) + '% lagi' : 'Target tercapai! 🎉'}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-slate-500">Profit aktual/bulan</span>
                            <span class="text-sm font-black ${safeGap.actual_profit >= 0 ? 'text-emerald-500' : 'text-rose-500'}">${fmtCurrency(safeGap.actual_profit)}</span>
                        </div>
                    </div>
                </div>` : ''}
                ${safeCash.score !== undefined ? `
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-md">
                    <h4 class="biz-term text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2 cursor-help w-max" data-term="mentor_cash_health">
                        <i class="fas fa-heartbeat text-${cashColor}-500"></i> Cash Health
                    </h4>
                    <div class="text-center mb-4">
                        <div class="text-4xl font-black text-${cashColor}-500">${safeCash.coverage_months}<span class="text-lg font-bold text-slate-400"> bln</span></div>
                        <div class="text-sm font-bold mt-1 text-${cashColor}-600 dark:text-${cashColor}-400">${cashLabel}</div>
                        <p class="text-xs text-slate-400 mt-1">Runway dengan kas saat ini</p>
                    </div>
                    <div class="w-full h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-3">
                        <div class="h-full bg-${cashColor}-500 rounded-full transition-all" style="width:${Math.min(100, safeCash.score || 0)}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Kas: ${fmtCurrency(safeCash.cash_balance)}</span>
                        <span>Burn: ${fmtCurrency(safeCash.monthly_burn)}/bln</span>
                    </div>
                </div>` : ''}
            </div>` : ''}

            <!-- ── 4. FINANCIAL KPI CARDS ── -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                ${[
                ['Est. Profit/Bulan', fmtCurrency(safeMetrics.estimatedProfit), 'fa-coins', 'emerald', 'text-emerald-500', 'mentor_est_profit'],
                ['Break-Even', safeMetrics.breakEvenMonth > 0 ? 'Bulan ' + safeMetrics.breakEvenMonth : 'N/A', 'fa-flag-checkered', 'cyan', bepColor, 'mentor_break_even'],
                ['Net Margin', safeMetrics.netMarginPct + '%', 'fa-percentage', 'violet', marginColor, 'mentor_net_margin'],
                ['ROI Multiplier', safeMetrics.roiMultiplier + 'x', 'fa-rocket', 'amber', 'text-amber-400', 'mentor_roi_multiplier'],
            ].map(([label, val, icon, color, vc, term]) => `
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 shadow-md text-center hover:-translate-y-1 transition-transform">
                        <div class="w-9 h-9 mx-auto rounded-xl bg-${color}-500/10 flex items-center justify-center text-${color}-500 mb-2">
                            <i class="fas ${icon}"></i>
                        </div>
                        <p class="biz-term text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-1 cursor-help flex items-center justify-center gap-1" data-term="${term}">${label}</p>
                        <p class="text-base md:text-lg font-black ${vc}">${val}</p>
                    </div>
                `).join('')}
            </div>

            <!-- ── 5. ACTION PLAN (per-diagnosis) ── -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 md:p-7 border border-slate-200 dark:border-slate-700 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-emerald-500"></i>
                    Action Plan ${isDiagnostic ? '(Berdasarkan Diagnosa)' : '(Berdasarkan Strategi)'}
                </h3>
                <ul class="space-y-3">${renderRecs()}</ul>
            </div>

            <!-- ── 6. PROJECTION CHART ── -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 md:p-7 border border-slate-200 dark:border-slate-700 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                    <i class="fas fa-chart-area text-cyan-500"></i>
                    Proyeksi ${safeInput.timeframeMonths || 6} Bulan
                    ${isDiagnostic ? '<span class="text-xs font-normal text-slate-400 ml-2">Dimulai dari revenue aktual</span>' : ''}
                </h3>
                <p class="text-xs text-slate-400 mb-5">Simulasi pertumbuhan berdasarkan pengalaman dan ${isDiagnostic ? 'kondisi aktual' : 'target revenue'}</p>
                <div class="relative w-full" style="height:280px;"><canvas id="mentorProjectionChart"></canvas></div>
                <div class="flex flex-wrap gap-4 mt-4 justify-center">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500"><div class="w-3 h-3 rounded-full bg-cyan-500"></div> Revenue</div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500"><div class="w-3 h-3 rounded-full bg-rose-500"></div> Biaya</div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500"><div class="w-3 h-3 rounded-full bg-emerald-500"></div> Profit</div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500"><div class="w-3 h-1 bg-amber-500 rounded"></div> Kumulatif</div>
                </div>
            </div>

            <!-- ── 7. DETAILED DIAGNOSTICS TABLE ── -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 md:p-7 border border-slate-200 dark:border-slate-700 shadow-xl">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-search-dollar text-blue-500"></i> Data Diagnostik Lengkap
                </h3>
                <div class="space-y-1">
                    ${[
                ['Runway Estimasi', safeMetrics.runwayMonths > 50 ? '> 50 Bulan' : safeMetrics.runwayMonths + ' Bulan', 'text-slate-900 dark:text-white', 'mentor_runway'],
                ['Est. Monthly Burn', fmtCurrency(safeMetrics.estimatedMonthlyBurn), 'text-slate-900 dark:text-white', 'mentor_burn_rate'],
                ['Est. Monthly Profit', fmtCurrency(safeMetrics.estimatedProfit), safeMetrics.estimatedProfit >= 0 ? 'text-emerald-500' : 'text-rose-500', 'mentor_est_profit'],
                ['Break-Even Point', safeMetrics.breakEvenMonth > 0 ? 'Bulan ke-' + safeMetrics.breakEvenMonth : 'N/A', bepColor, 'mentor_break_even'],
                ['ROI Multiplier', safeMetrics.roiMultiplier + 'x', 'text-emerald-500', 'mentor_roi_multiplier'],
                ['Net Margin', safeMetrics.netMarginPct + '%', marginColor, 'mentor_net_margin'],
                ['Capital Efficiency', safeMetrics.capitalEfficiencyPct > 900 ? '∞' : safeMetrics.capitalEfficiencyPct + '%', 'text-amber-400', 'mentor_capital_efficiency'],
                ['Modal Awal', fmtCurrency(safeInput.capital || 0), 'text-slate-900 dark:text-white', 'mentor_initial_capital'],
            ].map(([label, val, vc = 'text-slate-900 dark:text-white', term = '']) => `
                        <div class="flex justify-between items-center p-3 border-b border-slate-100 dark:border-slate-700 last:border-0">
                            <span class="biz-term text-sm text-slate-500 cursor-help flex items-center gap-1" data-term="${term}">${label}</span>
                            <span class="font-bold ${vc} text-right">${val}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="mt-5 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <p class="text-[11px] text-blue-600 dark:text-blue-300 text-center">
                        *${isDiagnostic ? 'Analisis berdasarkan data aktual yang kamu input.' : 'Simulasi deterministik berdasarkan input operational cost & margin.'}
                    </p>
                </div>
            </div>

            <!-- ── 8. CTA BUTTONS ── -->
            <div class="flex justify-center gap-4 flex-wrap pt-2">
                <button id="btn-generate-roadmap" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap
                </button>
                <button id="btn-reanalyze-mentor" class="px-6 py-3 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300 font-bold text-sm hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    <i class="fas fa-redo mr-2"></i> Analisis Ulang
                </button>
            </div>

        </div>
        `;
    }

    bindResultActions(data) {
        // Trigger glossary icon injection for newly rendered UI
        if (window.glossaryEngine) setTimeout(() => window.glossaryEngine.toggleIcons(), 100);

        // Save component moved to Phase 4 Roadmap

        const btnGenerate = document.getElementById('btn-generate-roadmap');
        if (btnGenerate) {
            btnGenerate.addEventListener('click', async () => {
                btnGenerate.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating Roadmap...';
                try {
                    const token = localStorage.getItem('auth_token') || '';
                    const headers = {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    };
                    if (token) headers['Authorization'] = `Bearer ${token}`;

                    // POST to the dedicated generate-roadmap endpoint
                    const res = await fetch('/api/mentor/roadmap/generate', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: headers
                    });

                    if (res.status === 429) {
                        btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                        if (typeof showToast !== 'undefined') showToast("Sistem AI sedang sibuk. Mohon tunggu 1 menit lagi.", "error");
                        return;
                    }

                    if (res.status === 401 || res.status === 403) {
                        btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                        if (typeof showToast !== 'undefined') showToast("Sesi berakhir. Mengarahkan ke halaman login...", "warning");
                        setTimeout(() => window.location.href = '/login', 2000);
                        return;
                    }

                    const responseData = await res.json();
                    if (responseData.success) {
                        if (responseData.data && responseData.data.job_id) {
                            // Async Job Execution
                            const jobId = responseData.data.job_id;
                            let attempts = 0;
                            const maxAttempts = 30; // Max ~2 minutes
                            let currentDelay = 2000;

                            const pollRoadmap = async () => {
                                attempts++;
                                try {
                                    const statusRes = await fetch(`/api/jobs/${jobId}/status`, { headers });
                                    const statusData = await statusRes.json();

                                    if (statusData.status === 'completed') {
                                        btnGenerate.innerHTML = '<i class="fas fa-check mr-2 text-emerald-400"></i> Roadmap Ready';
                                        if (typeof showToast !== 'undefined') showToast("Execution Roadmap berhasil dibuat!", "success");

                                        // --- Gamification Event Dispatch ---
                                        document.dispatchEvent(new CustomEvent('cuan:roadmap-generated', {
                                            detail: { referenceId: statusData.reference_id || jobId }
                                        }));
                                        if (window.Gamification) window.Gamification.refresh();

                                        // Navigation Logic: Switch to Phase 4 directly
                                        if (window.journeyEngine) {
                                            window.journeyEngine.switchPhase('phase_4');
                                        }

                                        // Tell Roadmap handler to refresh
                                        if (window.roadmapHandler) {
                                            window.roadmapHandler.loadRoadmap();
                                        }
                                        return;
                                    }

                                    if (statusData.status === 'failed') {
                                        btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                                        if (typeof showToast !== 'undefined') showToast(statusData.error_message || "Gagal generate roadmap di background.", "error");
                                        return;
                                    }

                                    if (attempts >= maxAttempts) {
                                        btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                                        if (typeof showToast !== 'undefined') showToast("Waktu tunggu pembuatan roadmap habis.", "error");
                                        return;
                                    }

                                    currentDelay = Math.min(currentDelay + 1000, 5000);
                                    setTimeout(pollRoadmap, currentDelay);
                                } catch (e) {
                                    console.error("Polling Roadmap Error:", e);
                                    setTimeout(pollRoadmap, currentDelay);
                                }
                            };

                            setTimeout(pollRoadmap, currentDelay);
                        } else {
                            // Synchronous Fallback (Legacy)
                            btnGenerate.innerHTML = '<i class="fas fa-check mr-2 text-emerald-400"></i> Roadmap Ready';
                            if (typeof showToast !== 'undefined') showToast("Execution Roadmap berhasil dibuat!", "success");

                            document.dispatchEvent(new CustomEvent('cuan:roadmap-generated', {
                                detail: { referenceId: responseData.data?.roadmap?.id || responseData.data?.id }
                            }));
                            if (window.Gamification) window.Gamification.refresh();

                            if (window.journeyEngine) {
                                window.journeyEngine.switchPhase('phase_4');
                            }

                            if (window.roadmapHandler) {
                                const roadmapData = responseData.data?.roadmap || responseData.data;
                                if (roadmapData && roadmapData.strategy) {
                                    window.roadmapHandler.renderRoadmapV2(roadmapData);
                                } else {
                                    window.roadmapHandler.loadRoadmap();
                                }
                            }
                        }
                    } else {
                        btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                        if (typeof showToast !== 'undefined') showToast(responseData.message || "Gagal generate roadmap.", "error");
                    }
                } catch (e) {
                    btnGenerate.innerHTML = '<i class="fas fa-map-marked-alt mr-2"></i> Generate Execution Roadmap';
                    console.error("Roadmap generation error:", e);
                }
            });
        }

        const btnReanalyze = document.getElementById('btn-reanalyze-mentor');
        if (btnReanalyze) {
            btnReanalyze.addEventListener('click', () => {
                // SPA Re-analyze
                const inputPanel = document.getElementById('mentor-input-panel');
                const boardContainer = document.getElementById('mentor-board-container');
                const dashboard = document.getElementById('mentor-dashboard');
                const wizardContainer = document.getElementById('mentor-wizard-container');

                // Show Input Panel
                if (inputPanel) inputPanel.classList.remove('hidden');
                // Restore Dashboard size
                if (boardContainer) {
                    boardContainer.classList.remove('lg:col-span-12');
                    boardContainer.classList.add('lg:col-span-8');
                }

                // Re-enable Wizard
                if (dashboard) dashboard.classList.add('hidden');
                if (wizardContainer) wizardContainer.classList.remove('hidden');

                // Reset Data State
                this.data = {
                    businessType: 'general',
                    riskIntent: 'stable_income',
                    capital: 'under_5',
                    grossMargin: 30,
                    experienceLevel: 1.0,
                    targetRevenue: 10000000,
                    timeframeMonths: 6,
                    channel: 'marketplace',
                    stage: 'running',
                    // Step 4
                    actualRevenue: 0,
                    actualExpenses: 0,
                    cashBalance: 0,
                    adSpend: 0,
                    avgOrderValue: 0,
                    repeatRate: 0,
                    businessAge: 0,
                    // Step 5
                    problemAreas: []
                };

                // Clear input elements in DOM
                document.querySelectorAll('#mentor-wizard-container input[type="text"]').forEach(input => input.value = '');
                document.querySelectorAll('#mentor-wizard-container .problem-card').forEach(card => card.classList.remove('border-rose-500', 'selected'));

                // Keep default sliders visually
                const revSlider = document.getElementById('target-revenue-slider');
                if (revSlider) revSlider.value = 10000000;

                // Reset Selection Visuals (optional, for defaults)
                this.updateSelectionVisuals('riskIntent', 'stable_income');
                this.updateSelectionVisuals('timeframeMonths', 6);

                // Back to Step 1
                this.currentStep = 1;
                this.updateUI();

                // Scroll back to wizard smoothly
                if (inputPanel) {
                    // Small delay to allow DOM render so scroll reaches correct height
                    setTimeout(() => inputPanel.scrollIntoView({ behavior: 'smooth' }), 50);
                }
            });
        }
    }

    openSaveBlueprintModal(dataToSave) {
        let modal = document.getElementById('mentor-save-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'mentor-save-modal';
            modal.className = 'fixed inset-0 z-[1000] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-fade-in hidden';
            modal.innerHTML = `
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-2xl max-w-sm w-full border border-slate-200 dark:border-slate-700 relative">
                    <button id="close-save-modal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shrink-0">
                            <i class="fas fa-save text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">Simpan Blueprint</h3>
                            <p class="text-xs text-slate-500">Beri nama untuk analitik ini</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Blueprint</label>
                            <input type="text" id="mentor-blueprint-name" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium text-sm placeholder-slate-400" placeholder="Misal: Rencana Q3 2026">
                        </div>
                        
                        <button id="btn-confirm-save-mentor" class="w-full px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-500/30 flex justify-center items-center">
                            Simpan Sekarang
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            document.getElementById('close-save-modal').addEventListener('click', () => {
                modal.classList.add('hidden');
            });
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.add('hidden');
            });

            document.getElementById('btn-confirm-save-mentor').addEventListener('click', () => {
                const title = document.getElementById('mentor-blueprint-name').value || ('Strategic Analysis - ' + new window.Date().toLocaleDateString());
                modal.classList.add('hidden');

                // Check if this modal was opened from the roadmap context
                const isFromRoadmap = !!document.getElementById('btn-save-roadmap-blueprint');

                // Trigger animation then save
                this.playSaveAnimation(() => {
                    this.executeSave(dataToSave, title, isFromRoadmap);
                });
            });
        }

        // Set default value based on businessType
        const defaultName = (this.data && this.data.businessType) ? `Blueprint ${this.data.businessType.toUpperCase()} - ${new window.Date().toLocaleDateString()}` : '';
        const nameInput = document.getElementById('mentor-blueprint-name');
        if (nameInput) {
            nameInput.value = defaultName;
        }

        modal.classList.remove('hidden');
        if (nameInput) setTimeout(() => nameInput.focus(), 100);
    }

    playSaveAnimation(callback) {
        // Create container
        const container = document.createElement('div');
        container.className = 'fixed inset-0 z-[10000] pointer-events-none flex items-center justify-center overflow-hidden';

        // Aksa Container
        const aksaWrapper = document.createElement('div');
        aksaWrapper.className = 'absolute left-[10%] bottom-[30%] md:bottom-[40%] flex items-end gap-2 animate-fade-in transition-opacity duration-300';

        const aksaImg = document.createElement('img');
        aksaImg.src = '/assets/icon/aksa_nendang1.png';
        aksaImg.className = 'w-32 h-32 md:w-48 md:h-48 object-contain drop-shadow-2xl';

        const blueprintItem = document.createElement('div');
        blueprintItem.className = 'w-12 h-16 bg-white border-2 border-emerald-500 rounded text-emerald-500 flex items-center justify-center shadow-[0_0_15px_rgba(16,185,129,0.5)] transform -translate-y-4 transition-all duration-300 z-50';
        blueprintItem.innerHTML = '<i class="fas fa-file-invoice text-2xl"></i>';

        aksaWrapper.appendChild(aksaImg);
        aksaWrapper.appendChild(blueprintItem);
        container.appendChild(aksaWrapper);
        document.body.appendChild(container);

        // Wait a brief moment for enter animation, then switch sprite and kick
        setTimeout(() => {
            aksaImg.src = '/assets/icon/aksa_nendang2.png';

            // Unparent blueprintItem for absolute flight to bottom right
            const rect = blueprintItem.getBoundingClientRect();
            aksaWrapper.removeChild(blueprintItem);
            container.appendChild(blueprintItem);

            blueprintItem.style.position = 'absolute';
            blueprintItem.style.left = rect.left + 'px';
            blueprintItem.style.top = rect.top + 'px';
            blueprintItem.style.transition = 'all 0.6s cubic-bezier(0.5, 0, 0.2, 1)';

            // Force reflow
            void blueprintItem.offsetWidth;

            // Target bottom right corner where the float action is usually placed
            const targetX = window.innerWidth - 80;
            const targetY = window.innerHeight - 80;

            blueprintItem.style.left = targetX + 'px';
            blueprintItem.style.top = targetY + 'px';
            blueprintItem.style.transform = 'scale(0.3) rotate(360deg)';
            blueprintItem.style.opacity = '0';

            setTimeout(() => {
                // Fade out aksa
                aksaWrapper.style.opacity = '0';

                setTimeout(() => {
                    container.remove();
                    if (callback) callback();
                }, 300);
            }, 600);

        }, 300);
    }

    async executeSave(data, title, isFromRoadmap = false) {
        let btnSave = document.getElementById('btn-save-mentor');
        if (isFromRoadmap) {
            btnSave = document.getElementById('btn-save-roadmap-blueprint');
        }

        if (btnSave) btnSave.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

        try {
            const token = localStorage.getItem('auth_token') || '';
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch('/mentor/save', {
                method: 'POST',
                credentials: 'same-origin',
                headers: headers,
                body: JSON.stringify({
                    blueprint_id: this.currentBlueprintId || null,
                    title: title,
                    persona: this.data.businessType,
                    snapshot: {
                        input_snapshot: this.data,
                        mentor_output: data
                    }
                })
            });

            if (res.status === 401 || res.status === 403) {
                if (btnSave) btnSave.innerHTML = isFromRoadmap ? '<i class="fas fa-save mr-2"></i> Save Blueprint' : '<i class="fas fa-save flex-col items-center justify-center text-lg"></i>';
                if (typeof showToast !== 'undefined') showToast("Sesi berakhir atau Anda belum login. Mengarahkan ke halaman login...", "warning");
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }

            const responseData = await res.json();
            if (responseData.success) {
                this.currentBlueprintId = responseData.data.id;
                if (btnSave) btnSave.innerHTML = '<i class="fas fa-check mr-2 text-emerald-400"></i> Saved';
                if (typeof showToast !== 'undefined') showToast(isFromRoadmap ? "Roadmap Blueprint saved successfully" : "Blueprint saved successfully", "success");

                // --- PHASE 3: Gamification Event Dispatch ---
                document.dispatchEvent(new CustomEvent('cuan:blueprint-saved', {
                    detail: { referenceId: result.data?.id }
                }));

                // --- PHASE 16: Emotional Retention Loop Update ---
                if (window.Gamification) window.Gamification.refresh();
            } else {
                if (btnSave) btnSave.innerHTML = isFromRoadmap ? '<i class="fas fa-save mr-2"></i> Save Blueprint' : '<i class="fas fa-save flex-col items-center justify-center text-lg"></i>';
                if (typeof showToast !== 'undefined') showToast(responseData.message || "Failed to save blueprint", "error");
            }
        } catch (e) {
            if (btnSave) btnSave.innerHTML = isFromRoadmap ? '<i class="fas fa-save mr-2"></i> Save Blueprint' : '<i class="fas fa-save flex-col items-center justify-center text-lg"></i>';
            console.error("Save error:", e);
        }
    }

    // --- 5. LOAD FUNCTIONALITY ---
    openLoadModal() {
        // Create modal if it doesn't exist
        let modal = document.getElementById('mentor-load-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'mentor-load-modal';
            modal.className = 'fixed inset-0 z-[1000] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-fade-in hidden';
            modal.innerHTML = `
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 dark:border-slate-700 relative flex flex-col max-h-[80vh]">
                    <button id="close-mentor-modal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shrink-0">
                            <i class="fas fa-folder-open text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">Buka Session</h3>
                            <p class="text-xs text-slate-500">Lanjutkan analisis sebelumnya</p>
                        </div>
                    </div>
                    
                    <div id="mentor-blueprints-list" class="flex-1 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                        <div class="text-center py-8 text-slate-400">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-sm">Memuat data...</p>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            document.getElementById('close-mentor-modal').addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            // Close on click outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.add('hidden');
            });
        }

        modal.classList.remove('hidden');
        this.fetchBlueprints();
    }

    async fetchBlueprints() {
        const listDiv = document.getElementById('mentor-blueprints-list');
        listDiv.innerHTML = `
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Memuat data...</p>
            </div>
        `;
        try {
            const token = localStorage.getItem('auth_token') || '';
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch('/mentor/blueprints', { headers });
            const result = await res.json();

            if (result.success && result.data.length > 0) {
                listDiv.innerHTML = result.data.map(bp => `
                    <div class="group cursor-pointer bg-slate-50 dark:bg-slate-700/30 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 p-4 rounded-xl transition-all" onclick="window.mentorWizardInstance.loadBlueprint(${bp.id})">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">${bp.name || 'Untitled Blueprint'}</h4>
                            <span class="text-[10px] text-slate-400 bg-white dark:bg-slate-800 px-2 py-0.5 rounded border border-slate-100 dark:border-slate-700">${new Date(bp.created_at).toLocaleDateString('id-ID')}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                listDiv.innerHTML = `
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-folder-open text-3xl mb-3 opacity-20"></i>
                        <p class="text-sm">Belum ada sesi tersimpan.</p>
                    </div>
                `;
            }
        } catch (e) {
            console.error(e);
            listDiv.innerHTML = `<p class="text-center text-rose-500 py-4 text-sm">Gagal memuat data.</p>`;
        }
    }

    async loadBlueprint(id) {
        document.getElementById('mentor-load-modal')?.classList.add('hidden');

        // Show Loading
        const loadingOverlay = document.getElementById('mentor-loading');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        try {
            const token = localStorage.getItem('auth_token') || '';
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch('/mentor/blueprints/' + id, { headers });
            const result = await res.json();

            if (result.success && result.data && result.data.data) {
                let snapshot = result.data.data; // Inner JSON data from table 'data' column

                // If the snapshot is string, parse it
                if (typeof snapshot === 'string') {
                    snapshot = JSON.parse(snapshot);
                }

                // Restore state
                this.currentBlueprintId = result.data.id;
                this.data = snapshot.input_snapshot || this.data;

                // Switch mode if the UI is currently hidden (dashboard is hidden at start)
                document.getElementById('mentor-wizard-container').classList.add('hidden');
                document.getElementById('mentor-dashboard').classList.remove('hidden');

                // Render Result
                this.renderResult(snapshot.mentor_output);

                // Handle Roadmap state if saved
                if (snapshot.roadmap_data) {
                    if (window.roadmapHandler) {
                        const renderableRoadmap = this._transformRoadmapData(snapshot.roadmap_data);
                        window.roadmapHandler.renderRoadmapV2(renderableRoadmap);
                    }
                    if (window.journeyEngine) {
                        window.journeyEngine.switchPhase('phase_4'); // Navigate to roadmap
                    }
                    if (typeof window.switchBentoTab === 'function') {
                        window.switchBentoTab('roadmap-container');
                        setTimeout(() => {
                            const rContainer = document.getElementById('roadmap-container');
                            if (rContainer) rContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                } else {
                    if (typeof window.switchBentoTab === 'function') {
                        window.switchBentoTab('business-simulation-lab');
                    }
                    // Make sure we scroll up to mentor lab if no roadmap
                    const container = document.getElementById('mentor-board-container');
                    if (container) container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                if (typeof showToast !== 'undefined') showToast("Sesi berhasil dimuat", "success");
            } else {
                if (typeof showToast !== 'undefined') showToast("Gagal memuat detail sesi", "error");
            }
        } catch (e) {
            console.error(e);
            if (typeof showToast !== 'undefined') showToast("Terjadi kesalahan saat memuat", "error");
        } finally {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
        }
    }

    hydrateBlueprint(blueprint) {
        if (!blueprint || !blueprint.data) return;

        try {
            let snapshot = blueprint.data;
            if (typeof snapshot === 'string') {
                snapshot = JSON.parse(snapshot);
            }

            // Restore state
            this.currentBlueprintId = blueprint.id;
            this.data = snapshot.input_snapshot || this.data;

            // Switch mode
            document.getElementById('mentor-wizard-container').classList.add('hidden');
            document.getElementById('mentor-dashboard').classList.remove('hidden');

            // Render Result
            this.renderResult(snapshot.mentor_output);

            // Handle Roadmap state if saved
            if (snapshot.roadmap_data) {
                if (window.roadmapHandler) {
                    // Slight delay to ensure phase UI has rendered the roadmap container
                    setTimeout(() => {
                        const renderableRoadmap = this._transformRoadmapData(snapshot.roadmap_data);
                        window.roadmapHandler.renderRoadmapV2(renderableRoadmap);
                    }, 100);
                }
                if (window.journeyEngine) {
                    window.journeyEngine.switchPhase('phase_4'); // Navigate to roadmap
                }
                if (typeof window.switchBentoTab === 'function') {
                    window.switchBentoTab('roadmap-container');
                    setTimeout(() => {
                        const rContainer = document.getElementById('roadmap-container');
                        if (rContainer) rContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }
            } else {
                if (typeof window.switchBentoTab === 'function') {
                    window.switchBentoTab('business-simulation-lab');
                }
                const container = document.getElementById('mentor-board-container');
                if (container) container.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            if (typeof showToast !== 'undefined') showToast("Sesi " + (blueprint.title || blueprint.name || "") + " berhasil dimuat", "success");
        } catch (e) {
            console.error("Hydration error:", e);
        }
    }
    /**
     * Transform raw DB roadmap shape into the shape expected by renderRoadmapV2().
     * Handles both already-transformed data (has strategy.primary_strategy) and raw DB data (has steps[]).
     */
    _transformRoadmapData(roadmapData) {
        if (!roadmapData) return null;
        // Already in correct shape
        if (roadmapData.strategy && roadmapData.strategy.primary_strategy) return roadmapData;

        const steps = roadmapData.steps || [];
        const phaseNames = ['Foundation Phase', 'Growth Phase', 'Scale Phase', 'Mastery Phase'];
        const phaseDurations = ['Minggu 1–4', 'Minggu 5–8', 'Bulan 3–4', 'Bulan 5–6'];

        // Group steps into phases of 3
        const phases = [];
        for (let i = 0; i < steps.length; i += 3) {
            const chunk = steps.slice(i, i + 3);
            const phaseIdx = Math.floor(i / 3);
            phases.push({
                name: phaseNames[phaseIdx] || ('Phase ' + (phaseIdx + 1)),
                duration: phaseDurations[phaseIdx] || '',
                steps: chunk.map(step => ({
                    id: step.id,
                    title: step.title,
                    description: step.description || '',
                    category: step.strategy_tag || 'General',
                    impact_score: 8,
                    difficulty_score: 5,
                    reasoning: step.description || '',
                    estimated_time: phaseIdx === 0 ? '2–4 minggu' : (phaseIdx === 1 ? '4–6 minggu' : '8–12 minggu'),
                    outcome_type: 'revenue_growth',
                    priority_score: 10 - phaseIdx,
                    actions: (step.actions || []).map(a => ({
                        id: a.id,
                        action_text: a.action_text,
                        is_completed: !!a.is_completed,
                    }))
                }))
            });
        }

        const primaryTag = (steps[0] && steps[0].strategy_tag) ? steps[0].strategy_tag : 'Growth Strategy';
        const secondaryTag = (steps[1] && steps[1].strategy_tag) ? steps[1].strategy_tag : 'Traffic Scaling';

        return {
            id: roadmapData.id,
            strategy: {
                primary_strategy: primaryTag.toLowerCase().replace(/ /g, '_'),
                secondary_strategy: secondaryTag.toLowerCase().replace(/ /g, '_'),
            },
            confidence_score: 78,
            reliability: 'High',
            diagnosis: null,
            phases,
            summary: 'Roadmap dimuat dari Blueprint tersimpan.',
        };
    }
}

// Global Init (Blade can call this or it auto-inits)
window.MentorWizard = MentorWizard;

// Self Init if DOM Ready
document.addEventListener('DOMContentLoaded', () => {
    // Only init if container exists
    if (document.getElementById('mentor-wizard-container')) {
        window.mentorWizardInstance = new MentorWizard();
        window.mentorWizard = window.mentorWizardInstance; // Attach for roadmap-engine.js

        // Hydrate from window.blueprintData if it's for mentor lab
        if (window.blueprintData && window.blueprintData.type === 'mentor_lab') {
            window.mentorWizardInstance.hydrateBlueprint(window.blueprintData);
        }
    }
});
