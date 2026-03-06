/**
 * Profit Simulator 3.0 (Mentally Safe)
 * Handles Zone Selection, Level Adjustment, and API Simulation.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Config & Global State
    const config = {
        apiEndpoint: '/profit-simulator/simulate'
    };

    // 2. Listener for Reverse Goal Planner Updates
    window.addEventListener('reverse-goal-planner:update', function (e) {
        // Update global baseline
        window.manualBaseline = e.detail.baseline || e.detail; // Handle both direct and nested formats

        // Check Goal Status for Guardrail
        const isLocked = e.detail.isLocked || (window.manualBaseline && window.manualBaseline.risk_level === 'High Risk');
        const gate = document.getElementById('ps-gate-overlay');
        if (gate) {
            if (isLocked) gate.classList.remove('hidden');
            else gate.classList.add('hidden');
        }

        // Reset Simulator State ONLY if this is a fresh update (not init)
        if (!e.detail.isInit) {
            resetSimulatorUI();
        }

        // Store Session ID
        if (e.detail.session_id) {
            window.latestSessionId = e.detail.session_id;
        }
    });

    function resetSimulatorUI() {
        document.querySelectorAll('.zone-card').forEach(c => {
            c.classList.remove('border-blue-500', 'border-emerald-500', 'border-amber-500', 'border-rose-500', 'ring-2', 'ring-offset-2');
            const selector = c.querySelector('.level-selector');
            if (selector) selector.classList.add('hidden');
        });
        const resultEl = document.getElementById('simulation-result');
        if (resultEl) {
            resultEl.classList.add('hidden');
            // Remove banners
            const existingBanner = resultEl.querySelector('#ps-warning-banner');
            if (existingBanner) existingBanner.remove();
        }

        const defaultEl = document.getElementById('ps-default-state');
        if (defaultEl) {
            defaultEl.classList.remove('hidden');
            // Restore original content if we modified it
            const content = defaultEl.querySelector('#ps-default-content');
            const error = defaultEl.querySelector('#ps-error-content');
            if (content && error) {
                content.classList.remove('hidden');
                error.classList.add('hidden');
            }
        }
    }
    // Expose to global for button click
    window.resetSimulatorUI = resetSimulatorUI;

    // 3. Zone Selection Logic (Single Focus)
    const zones = document.querySelectorAll('.zone-card');
    zones.forEach(zone => {
        zone.addEventListener('click', function (e) {
            if (e.target.classList.contains('level-btn')) return;

            zones.forEach(z => {
                z.classList.remove('border-blue-500', 'border-emerald-500', 'border-amber-500', 'border-rose-500', 'ring-2', 'ring-offset-2');
                const selector = z.querySelector('.level-selector');
                if (selector) selector.classList.add('hidden');
            });

            const zoneType = this.dataset.zone;
            let borderColor = 'border-blue-500';
            if (zoneType === 'conversion') borderColor = 'border-emerald-500';
            if (zoneType === 'pricing') borderColor = 'border-amber-500';
            if (zoneType === 'cost') borderColor = 'border-rose-500';

            this.classList.add(borderColor, 'ring-2', 'ring-offset-2');
            const selector = this.querySelector('.level-selector');
            if (selector) selector.classList.remove('hidden');
        });
    });

    // 4. Level Selection & Simulation Trigger
    const levelBtns = document.querySelectorAll('.level-btn');
    levelBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const parent = this.closest('.level-selector');
            parent.querySelectorAll('.level-btn').forEach(b => b.classList.remove('bg-slate-200', 'dark:bg-slate-700'));
            this.classList.add('bg-slate-200', 'dark:bg-slate-700');

            const zoneCard = this.closest('.zone-card');
            const zone = zoneCard.dataset.zone;
            const level = this.dataset.level;

            runSimulation(zone, level);
        });
    });


    // Helper: Calculate Simulation Result locally (for immediate feedback)
    async function calculateSimulation(baseline, zone, level) {
        // Base values
        let newTraffic = baseline.traffic || 0;
        let newConv = baseline.conversion_rate || 0;
        let newPrice = baseline.price || 0;
        let newCogs = baseline.cogs || 0;
        let newAdSpend = baseline.ad_spend || 0;
        let newFixedCost = baseline.fixed_cost || 0;

        // Apply Modifiers based on Zone & Level
        let transformationLabel = "";
        let riskLabel = "Low Risk";
        let insight = "";

        if (zone === 'traffic') {
            const modifiers = [1.10, 1.20, 1.35]; // +10%, +20%, +35%
            newTraffic *= modifiers[level - 1];
            newAdSpend *= (1 + ((modifiers[level - 1] - 1) * 0.5));
            transformationLabel = `Traffic Boost +${Math.round((modifiers[level - 1] - 1) * 100)}%`;
            insight = "Fokus pada akuisisi user baru akan meningkatkan top-line revenue secara signifikan.";
            if (level === 3) riskLabel = "High Burn Rate";
        } else if (zone === 'conversion') {
            const modifiers = [1.05, 1.10, 1.20]; // +5%, +10%, +20%
            newConv *= modifiers[level - 1];
            transformationLabel = `Conv. Rate +${Math.round((modifiers[level - 1] - 1) * 100)}%`;
            insight = "Optimasi funnel dan copywriting akan meningkatkan efisiensi setiap pengunjung.";
        } else if (zone === 'pricing') {
            const modifiers = [1.03, 1.07, 1.12]; // +3%, +7%, +12%
            newPrice *= modifiers[level - 1];
            transformationLabel = `Price Increase +${Math.round((modifiers[level - 1] - 1) * 100)}%`;
            insight = "Meningkatkan perceived value memungkinkan kenaikan harga tanpa mengorbankan konversi.";
            if (level === 3) riskLabel = "Churn Risk";
        } else if (zone === 'cost') {
            const modifiers = [0.95, 0.90, 0.85]; // -5%, -10%, -15%
            newCogs *= modifiers[level - 1];
            newFixedCost *= modifiers[level - 1];
            transformationLabel = `Cost Reduction -${Math.round((1 - modifiers[level - 1]) * 100)}%`;
            insight = "Efisiensi operasional langsung berdampak pada bottom-line profit.";
        }

        // ─── VALIDATION INJECTION ─────────────────────────────────────────────
        try {
            const { validate } = await import('./utils/FeasibilityValidator.js');

            // Prepare inputs for validation
            // We need to derive implied inputs from the calculated state
            // Removed: useless sales calculation
            const effectiveCpc = (newTraffic > 0 && newAdSpend > 0) ? (newAdSpend / newTraffic) : 0;
            const effectiveMargin = newPrice > 0 ? (newPrice - newCogs) / newPrice : 0;

            const validationInputs = {
                price: newPrice,
                cost: newCogs, // Validator checks Price <= Cost
                margin: effectiveMargin, // Validator checks Margin <= 0 (or low)
                cpc: effectiveCpc,
                conversion: newConv / 100 // Validator expects 0.015 for 1.5% ?? check validator logic
                // Validator: const safeConv = Number(conversion) || 0; 
                // In checkImpossible: const cpa = safeConv > 0 ? (safeCpc / safeConv) : ...
                // If I pass 1.5 (as %), cpa = cpc / 1.5. This is wrong.
                // Standard formula: CPA = CPC / (Conv%) e.g. 1000 / 0.01 = 100,000.
                // Re-reading Validator: "safeConv = Number(conversion)".
                // "cpc / conversion".
                // If I pass 0.015, cpa = cpc / 0.015. Correct.
                // `newConv` is percentage (e.g. 1.5). So divide by 100.
            };

            const validationContext = {
                channel: (baseline.traffic_strategy || 'ads')
            };

            const validation = validate(validationInputs, validationContext);

            if (validation.status === 'invalid') {
                return {
                    type: 'error',
                    validation: validation
                };
            }

            var warnings = null;
            if (validation.status === 'warning') {
                warnings = validation;
            }

        } catch (e) {
            console.error("Critical: Validation engine failed to load", e);
            return {
                type: 'error',
                validation: {
                    reason: "Sistem Validasi Gagal Dimuat. Harap refresh halaman.",
                    discrepancies: ["Module load error: " + e.message]
                }
            };
        }
        // ──────────────────────────────────────────────────────────────────────

        // Calculate New Profit
        const newSales = Math.floor(newTraffic * (newConv / 100));
        const newRevenue = newSales * newPrice;
        const newTotalCost = (newSales * newCogs) + newFixedCost + newAdSpend;
        const newProfit = newRevenue - newTotalCost;

        // Calculate Baseline Profit
        const baseSales = Math.floor((baseline.traffic || 0) * ((baseline.conversion_rate || 0) / 100));
        const baseRevenue = baseSales * (baseline.price || 0);
        const baseTotalCost = (baseSales * (baseline.cogs || 0)) + (baseline.fixed_cost || 0) + (baseline.ad_spend || 0);
        const baseProfit = baseRevenue - baseTotalCost;

        const delta = newProfit - baseProfit;

        // --- PHASE 3: Gamification Event Dispatch ---
        const simEvent = new CustomEvent('cuan:simulation-success', {
            detail: {
                newSales,
                netProfit: newProfit,
                zone,
                level,
                referenceId: window.latestSessionId
            }
        });
        document.dispatchEvent(simEvent);

        return {
            type: 'success', // Wrapper
            warnings: warnings || null, // Wrapper
            data: {
                projected_range: {
                    min: newProfit * 0.9,
                    max: newProfit * 1.1,
                    label: formatCurrency(newProfit)
                },
                insight: insight,
                effort_level: level === 1 ? 'Rendah' : (level === 2 ? 'Sedang' : 'Tinggi'),
                risk_level: level === 1 ? 'Stabil' : (level === 2 ? 'Moderat' : 'Tinggi'),
                risk_label: riskLabel,
                reflection_prompt: `Strategi ${transformationLabel} dapat menambah profit sebesar ${formatCurrency(delta)}. Siap eksekusi?`,
                delta_val: delta,
                // Raw data for breakdown
                raw: {
                    traffic: newTraffic,
                    conversion: newConv,
                    price: newPrice,
                    profit: newProfit,
                    // V3 Enhanced data
                    sales: newSales,
                    revenue: newRevenue,
                    totalCost: newTotalCost,
                    adSpend: newAdSpend,
                    fixedCost: newFixedCost,
                    cogs: newCogs
                },
                base: {
                    sales: baseSales,
                    revenue: baseRevenue,
                    totalCost: baseTotalCost,
                    profit: baseProfit,
                    adSpend: baseline.ad_spend || 0,
                    fixedCost: baseline.fixed_cost || 0
                }
            }
        };
    }

    // 5. Simulation Logic
    async function runSimulation(zone, level) {
        if (!window.latestSessionId && !window.manualBaseline) {
            showToast('Silakan buat rencana di Reverse Goal Planner terlebih dahulu.', 'error');
            const plannerSection = document.getElementById('reverse-planner-section');
            if (plannerSection) {
                plannerSection.scrollIntoView({ behavior: 'smooth' });
            }
            return;
        }

        // await calculation (now async & wrapped)
        const response = await calculateSimulation(window.manualBaseline, zone, parseInt(level));

        if (response.type === 'error') {
            renderErrorState(response.validation);
            return;
        }

        const simulationResult = response.data;
        const warnings = response.warnings;

        // Track active selection as globals (used by collectBlueprintState)
        window.activeZone = zone;
        window.activeLevel = parseInt(level);

        const payload = {
            zone: zone,
            level: parseInt(level),
            session_id: window.latestSessionId || null,
            baseline: window.manualBaseline || {},
            result: simulationResult
        };

        // Store full payload (with zone + level) — do NOT overwrite later
        window.latestResult = payload;
        window.simulationDirty = true;

        // Update UI
        renderResult(simulationResult, warnings);

        // Visual Selection Logic
        updateVisualSelection(zone, level);

        // ── Bridge to Roadmap Engine V2 ───────────────────────────────────────────
        // Write simulation inputs as data-attributes so roadmap-engine.js can pass
        // them to /api/roadmap/generate when the user clicks Generate Roadmap.
        const simMeta = document.getElementById('roadmap-sim-meta');
        if (simMeta) {
            const baseline = window.manualBaseline || {};
            simMeta.dataset.traffic = baseline.traffic ?? 0;
            simMeta.dataset.conversion = baseline.conversion_rate ?? 0;
            simMeta.dataset.margin = baseline.margin ?? 0;
            simMeta.dataset.channel = baseline.channel || '';
        }
    }

    function renderErrorState(validation) {
        const resultCard = document.getElementById('simulation-result');
        if (resultCard) {
            resultCard.classList.add('hidden');
            // Clear previous warnings
            const existingBanner = resultCard.querySelector('#ps-warning-banner');
            if (existingBanner) existingBanner.remove();
        }

        const defaultEl = document.getElementById('ps-default-state');
        if (defaultEl) {
            defaultEl.classList.remove('hidden');

            // Non-destructive: Check if we already have the structure
            let contentContainer = defaultEl.querySelector('#ps-default-content');
            let errorContainer = defaultEl.querySelector('#ps-error-content');

            if (!contentContainer) {
                // First run: Wrap existing content
                const originalHTML = defaultEl.innerHTML;
                defaultEl.innerHTML = `
                    <div id="ps-default-content">${originalHTML}</div>
                    <div id="ps-error-content" class="hidden"></div>
                `;
                contentContainer = defaultEl.querySelector('#ps-default-content');
                errorContainer = defaultEl.querySelector('#ps-error-content');
            }

            // Show Error, Hide Default
            contentContainer.classList.add('hidden');
            errorContainer.classList.remove('hidden');

            errorContainer.innerHTML = `
                <div class="p-6 text-center animate-fade-in">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/30 mb-4">
                        <i class="fas fa-ban text-3xl text-rose-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Model Tidak Feasible</h3>
                    <p class="text-rose-500 font-medium mb-4">${validation.reason}</p>
                    <div class="text-xs text-slate-500 bg-slate-100 dark:bg-slate-800 p-3 rounded text-left mx-auto max-w-sm border border-slate-200 dark:border-slate-700">
                        <strong class="text-rose-600 dark:text-rose-400">Detail Masalah:</strong>
                        <ul class="list-disc pl-4 mt-1 space-y-1">
                            ${validation.discrepancies.map(d => `<li>${d}</li>`).join('')}
                        </ul>
                        <button onclick="resetSimulatorUI()" class="mt-3 text-xs w-full py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded text-slate-600 dark:text-slate-300 font-bold transition-colors">
                            Kembali ke Awal
                        </button>
                    </div>
                </div>
            `;
        } else {
            showToast('Validasi gagal: ' + validation.reason, 'error');
        }
    }

    function updateVisualSelection(zone, level) {
        // Visual Selection Logic
        document.querySelectorAll('.zone-card').forEach(z => {
            z.classList.remove('border-blue-500', 'border-emerald-500', 'border-amber-500', 'border-rose-500', 'ring-2', 'ring-offset-2');
            const selector = z.querySelector('.level-selector');
            if (selector) selector.classList.add('hidden');
        });

        const zoneCard = document.querySelector(`.zone-card[data-zone="${zone}"]`);
        if (zoneCard) {
            let borderColor = 'border-blue-500';
            if (zone === 'conversion') borderColor = 'border-emerald-500';
            if (zone === 'pricing') borderColor = 'border-amber-500';
            if (zone === 'cost') borderColor = 'border-rose-500';

            zoneCard.classList.add(borderColor, 'ring-2', 'ring-offset-2');
            const selector = zoneCard.querySelector('.level-selector');
            if (selector) {
                selector.classList.remove('hidden');
                selector.querySelectorAll('.level-btn').forEach(btn => {
                    btn.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                    if (btn.dataset.level == level) {
                        btn.classList.add('bg-slate-200', 'dark:bg-slate-700');
                    }
                });
            }
        }
    }

    // ... (rest of code)



    function renderResult(result, warnings = null) {
        // NOTE: Do NOT set window.latestResult here.
        // runSimulation() sets it as the full payload {zone, level, result}.
        // Overwriting here would strip zone/level and break blueprint save.
        const saveBtn = document.getElementById('apply-strategy-btn');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            saveBtn.innerHTML = '<i class="fas fa-layer-group mr-2"></i>Simpan Blueprint Ini';
        }

        const defaultState = document.getElementById('ps-default-state');
        if (defaultState) defaultState.classList.add('hidden');

        const resultCard = document.getElementById('simulation-result');
        if (resultCard) {
            resultCard.classList.remove('hidden');

            // Remove old banner
            const oldBanner = resultCard.querySelector('#ps-warning-banner');
            if (oldBanner) oldBanner.remove();

            // Inject Warning Banner
            if (warnings) {
                const banner = document.createElement('div');
                banner.id = 'ps-warning-banner';
                banner.className = 'bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 mb-6 rounded-r-lg animate-fade-in';
                banner.innerHTML = `
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                        <div>
                            <h4 class="text-sm font-bold text-amber-600 dark:text-amber-400">Peringatan Risiko Bisnis</h4>
                            <p class="text-xs text-amber-600/80 dark:text-amber-400/80 mt-1">${warnings.reason}</p>
                            <ul class="list-disc pl-4 mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                                ${warnings.discrepancies.map(d => `<li>${d}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `;
                resultCard.prepend(banner);
            }
        }

        const rangeEl = document.getElementById('ps-profit-range');
        if (rangeEl) rangeEl.textContent = result.projected_range.label;

        const insightEl = document.getElementById('ps-insight');
        if (insightEl) insightEl.textContent = result.insight;

        const effortEl = document.getElementById('ps-effort');
        if (effortEl) effortEl.textContent = result.effort_level;

        const riskEl = document.getElementById('ps-risk');
        if (riskEl) {
            riskEl.textContent = result.risk_level + " (" + result.risk_label + ")";
            riskEl.className = 'font-bold text-sm';
            if (result.risk_level === 'High' || result.risk_level === 'Tinggi') riskEl.classList.add('text-rose-400');
            else if (result.risk_level === 'Moderate' || result.risk_level === 'Moderat') riskEl.classList.add('text-amber-400');
            else riskEl.classList.add('text-emerald-400');
        }

        const reflectionEl = document.getElementById('ps-reflection');
        if (reflectionEl) reflectionEl.textContent = result.reflection_prompt;

        // ── Delta Badge ─────────────────────────────────────────────────────────
        const deltaDisplay = document.getElementById('profit-delta-display');
        if (deltaDisplay) {
            const isPositive = result.delta_val >= 0;
            deltaDisplay.textContent = (isPositive ? '+' : '') + formatCurrency(result.delta_val) + ' vs Baseline';
            deltaDisplay.className = isPositive
                ? 'text-xs font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-full'
                : 'text-xs font-black text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2.5 py-1 rounded-full';
        }

        // ── Effort Level Visual Bars ─────────────────────────────────────────────
        const effortBars = document.querySelectorAll('#ps-effort-bars div');
        const level = result.effort_level === 'Rendah' ? 1 : result.effort_level === 'Sedang' ? 2 : 3;
        const barColor = level === 1 ? 'bg-emerald-400' : level === 2 ? 'bg-amber-400' : 'bg-rose-400';
        effortBars.forEach((bar, idx) => {
            bar.className = (idx < level)
                ? `w-2 rounded-full ${barColor} transition-all duration-300`
                : 'w-2 rounded-full bg-slate-700 transition-all duration-300';
        });

        // ── Breakdown Rows ───────────────────────────────────────────────────────
        const baseline = window.manualBaseline || {};
        const raw = result.raw || {};

        const baseTraffic = baseline.traffic || 0;
        const newTraffic = raw.traffic || 0;
        const trafficPct = baseTraffic > 0 ? Math.round(((newTraffic - baseTraffic) / baseTraffic) * 100) : 0;

        const baseConv = baseline.conversion_rate || 0;
        const newConv = raw.conversion || 0;
        const convPct = baseConv > 0 ? Math.round(((newConv - baseConv) / baseConv) * 100) : 0;

        const basePrice = baseline.price || 0;
        const newPrice = raw.price || 0;
        const pricePct = basePrice > 0 ? Math.round(((newPrice - basePrice) / basePrice) * 100) : 0;

        // Baseline profit calculation (mirrors calculateSimulation logic)
        const baseSales = Math.floor(baseTraffic * (baseConv / 100));
        const baseRev = baseSales * basePrice;
        const baseCost = (baseSales * (baseline.cogs || 0)) + (baseline.fixed_cost || 0) + (baseline.ad_spend || 0);
        const baseProfit = baseRev - baseCost;

        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

        set('ps-base-traffic', baseTraffic.toLocaleString('id-ID'));
        set('ps-new-traffic', Math.round(newTraffic).toLocaleString('id-ID'));
        set('ps-traffic-delta', (trafficPct >= 0 ? '+' : '') + trafficPct + '%');

        set('ps-base-conv', baseConv.toFixed(1) + '%');
        set('ps-new-conv', newConv.toFixed(2) + '%');
        set('ps-conv-delta', (convPct >= 0 ? '+' : '') + convPct + '%');

        set('ps-base-price', formatCurrency(basePrice));
        set('ps-new-price', formatCurrency(newPrice));
        set('ps-price-delta', (pricePct >= 0 ? '+' : '') + pricePct + '%');

        set('ps-base-profit', formatCurrency(baseProfit));
        set('ps-new-profit', formatCurrency(raw.profit || 0));
        set('ps-cta-profit-value', formatCurrency(raw.profit || 0));

        // Color delta pills by direction
        const colorPill = (id, pct) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.className = pct >= 0
                ? 'text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-full'
                : 'text-[10px] font-black text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded-full';
        };
        colorPill('ps-traffic-delta', trafficPct);
        colorPill('ps-conv-delta', convPct);
        colorPill('ps-price-delta', pricePct);

        // ── V3 Enhanced Breakdown Rows ────────────────────────────────────────────
        const base = result.base || {};

        // Sales row
        const baseSalesVal = base.sales || 0;
        const newSalesVal = raw.sales || 0;
        const salesPct = baseSalesVal > 0 ? Math.round(((newSalesVal - baseSalesVal) / baseSalesVal) * 100) : 0;
        set('ps-base-sales', baseSalesVal.toLocaleString('id-ID'));
        set('ps-new-sales', newSalesVal.toLocaleString('id-ID'));
        set('ps-sales-delta', (salesPct >= 0 ? '+' : '') + salesPct + '%');
        colorPill('ps-sales-delta', salesPct);

        // Revenue row
        const baseRevVal = base.revenue || 0;
        const newRevVal = raw.revenue || 0;
        const revPct = baseRevVal > 0 ? Math.round(((newRevVal - baseRevVal) / baseRevVal) * 100) : 0;
        set('ps-base-revenue', formatCurrency(baseRevVal));
        set('ps-new-revenue', formatCurrency(newRevVal));
        set('ps-revenue-delta', (revPct >= 0 ? '+' : '') + revPct + '%');
        colorPill('ps-revenue-delta', revPct);

        // Total Cost row
        const baseCostVal = base.totalCost || 0;
        const newCostVal = raw.totalCost || 0;
        const costPct = baseCostVal > 0 ? Math.round(((newCostVal - baseCostVal) / baseCostVal) * 100) : 0;
        set('ps-base-cost', formatCurrency(baseCostVal));
        set('ps-new-cost', formatCurrency(newCostVal));
        set('ps-cost-delta', (costPct >= 0 ? '+' : '') + costPct + '%');
        // Invert color for cost — increase is bad
        colorPill('ps-cost-delta', -costPct);

        // Ad spend row
        const baseAdVal = base.adSpend || 0;
        const newAdVal = raw.adSpend || 0;
        const adPct = baseAdVal > 0 ? Math.round(((newAdVal - baseAdVal) / baseAdVal) * 100) : 0;
        set('ps-base-adspend', formatCurrency(baseAdVal));
        set('ps-new-adspend', formatCurrency(newAdVal));
        set('ps-adspend-delta', (adPct >= 0 ? '+' : '') + adPct + '%');
        colorPill('ps-adspend-delta', -adPct);

        // ROI
        const roi = newCostVal > 0 ? Math.round(((raw.profit || 0) / newCostVal) * 100) : 0;
        const roiEl = document.getElementById('ps-roi-value');
        if (roiEl) {
            roiEl.textContent = roi + '%';
            roiEl.className = roi >= 50
                ? 'text-xs sm:text-sm font-black text-emerald-400'
                : roi >= 20
                    ? 'text-xs sm:text-sm font-black text-amber-400'
                    : 'text-xs sm:text-sm font-black text-rose-400';
        }

        // Net Margin
        const netMargin = newRevVal > 0 ? Math.round(((raw.profit || 0) / newRevVal) * 100) : 0;
        const marginEl = document.getElementById('ps-net-margin');
        if (marginEl) {
            marginEl.textContent = netMargin + '% margin';
            marginEl.className = netMargin >= 30
                ? 'text-[9px] sm:text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-full'
                : netMargin >= 15
                    ? 'text-[9px] sm:text-[10px] font-black text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded-full'
                    : 'text-[9px] sm:text-[10px] font-black text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded-full';
        }

        // ── Auto-Interpretation Text ──────────────────────────────────────────────
        // Memberikan kalimat bimbingan satu-kalimat agar user merasa "diarahkan"
        const interpretationEl = document.getElementById('ps-interpretation');
        const interpretationText = document.getElementById('ps-interpretation-text');
        if (interpretationEl && interpretationText) {
            const rawProfit = raw.profit || 0;
            const riskLvl = result.risk_level || '';
            let msg = '';

            if (rawProfit <= 0) {
                msg = '⚠️ Dengan skenario ini, bisnis kamu belum mencapai titik untung. Coba ubah harga jual atau kurangi biaya operasional.';
                interpretationEl.className = 'mt-3 px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20';
                interpretationText.className = 'text-[11px] sm:text-xs text-rose-300 font-medium leading-relaxed';
            } else if (netMargin >= 30 && (riskLvl === 'Stabil' || riskLvl === 'Rendah')) {
                msg = '✅ Dengan skenario ini, bisnis kamu punya potensi profit yang sehat dan risiko masih dalam batas aman. Layak dijalankan!';
                interpretationEl.className = 'mt-3 px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20';
                interpretationText.className = 'text-[11px] sm:text-xs text-emerald-300 font-medium leading-relaxed';
            } else if (netMargin >= 15) {
                msg = '📈 Potensi profit ada, tapi margin bisa ditingkatkan. Pertimbangkan efisiensi biaya atau naikkan sedikit harga jual untuk hasil lebih optimal.';
                interpretationEl.className = 'mt-3 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20';
                interpretationText.className = 'text-[11px] sm:text-xs text-amber-300 font-medium leading-relaxed';
            } else {
                msg = '⚡ Margin masih tipis. Dengan strategi yang tepat, bisnis ini bisa profitable — tapi perlu kontrol biaya yang ketat dari awal.';
                interpretationEl.className = 'mt-3 px-3 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20';
                interpretationText.className = 'text-[11px] sm:text-xs text-amber-300 font-medium leading-relaxed';
            }

            interpretationText.textContent = msg;
            interpretationEl.classList.remove('hidden');
        }

    }


    // Track Dirty State
    document.querySelectorAll('.zone-card, .level-btn').forEach(el => {
        el.addEventListener('click', () => {
            window.simulationDirty = true;
            console.log("[Blueprint] Profit Simulator is now DIRTY");
            updateSaveStrategyUI();
        });
    });

    // 6. Blueprint Management System

    // --- State ---
    const state = {
        blueprints: [],
        currentBlueprintId: null,
        isBlueprintLoaded: false,
    };

    // --- UI Elements ---
    const ui = {
        saveModal: document.getElementById('save-blueprint-modal'),
        sidebar: document.getElementById('blueprints-sidebar'),
        blueprintList: document.getElementById('blueprints-list'),
        saveBtn: document.getElementById('apply-strategy-btn'),
        savedInfo: document.getElementById('blueprint-saved-info'),
        savedLabel: document.getElementById('blueprint-saved-label'),
        modalInput: document.getElementById('blueprint-name'),
        modalConfirmBtn: document.getElementById('confirm-save-blueprint'),
        modalCancelBtn: document.getElementById('cancel-save-blueprint'),
        openSidebarBtn: document.getElementById('open-blueprints-btn'),
        closeSidebarBtn: document.getElementById('close-sidebar-btn'),
        createBtn: document.getElementById('create-new-blueprint-btn')
    };

    // ─── Save Button State Controller ────────────────────────────────────────────
    // Single source of truth for what the save area shows.
    function updateSaveStrategyUI() {
        if (!ui.saveBtn || !ui.savedInfo) return;

        if (!state.isBlueprintLoaded) {
            // No blueprint loaded — always show the save button
            ui.saveBtn.classList.remove('hidden');
            ui.saveBtn.innerHTML = 'Simpan Blueprint Ini <i class="fas fa-arrow-right ml-2 opacity-50"></i>';
            ui.savedInfo.classList.add('hidden');
            return;
        }

        const isDirty = window.reverseGoalDirty || window.simulationDirty;

        if (isDirty) {
            // Blueprint loaded but user made changes → show 'Simpan Perubahan'
            ui.saveBtn.classList.remove('hidden');
            ui.saveBtn.innerHTML = 'Simpan Perubahan <i class="fas fa-save ml-2 opacity-70"></i>';
            ui.savedInfo.classList.add('hidden');
        } else {
            // Blueprint loaded, nothing changed → show 'Tersimpan'
            ui.saveBtn.classList.add('hidden');
            ui.savedInfo.classList.remove('hidden');
            if (ui.savedLabel) {
                ui.savedLabel.textContent = `Blueprint "${state.currentBlueprintName || 'Strategi'}" sudah tersimpan`;
            }
        }
    }

    // Expose so other modules (e.g. reverse-goal-planner) can call it
    window.updateSaveStrategyUI = updateSaveStrategyUI;

    // --- Event Listeners ---

    // 1. Open Save Modal
    if (ui.saveBtn) {
        ui.saveBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openSaveModal();
        });
    }

    // 2. Modal Actions
    if (ui.modalConfirmBtn) {
        ui.modalConfirmBtn.addEventListener('click', () => executeSaveBlueprint());
    }
    if (ui.modalCancelBtn) {
        ui.modalCancelBtn.addEventListener('click', closeSaveModal);
    }

    // 3. Sidebar Handlers
    // NOTE: The floating mascot button (#open-blueprints-btn) click is owned by
    // blueprint-mascot.js. We only handle the close button here.
    if (ui.closeSidebarBtn) {
        ui.closeSidebarBtn.addEventListener('click', () => {
            ui.sidebar.classList.add('translate-x-full');
            // Also notify mascot system
            if (window.MascotSystem) window.MascotSystem.closeStorage();
        });
    }
    if (ui.createBtn) {
        ui.createBtn.addEventListener('click', () => {
            ui.sidebar.classList.add('translate-x-full'); // Close sidebar
            // Desync fix: notify mascot system
            if (window.MascotSystem) window.MascotSystem.closeStorage();
            showToast("Atur Reverse Goal dan Simulator, lalu klik 'Simpan Strategi'.", 'info');
        });
    }

    // --- Core Functions ---

    // Top-center flash notification — consistent with Toast system
    function showCenterFlash(message) {
        const el = document.createElement('div');
        // Position: top-center (same as Toast container), not screen-center
        // transform: translateX(-50%) only — no translateY to avoid vertical misalignment
        el.style.cssText = [
            'position: fixed',
            'top: 24px',
            'left: 50%',
            'transform: translateX(-50%) translateY(-10px)',
            'z-index: 99999',
            'background: linear-gradient(135deg, #0f172a, #1e293b)',
            'border: 1px solid rgba(16,185,129,0.4)',
            'border-radius: 16px',
            'padding: 16px 24px',
            'display: flex',
            'align-items: center',
            'gap: 12px',
            'box-shadow: 0 0 40px rgba(16,185,129,0.3), 0 10px 30px rgba(0,0,0,0.5)',
            'opacity: 0',
            'pointer-events: none',
            'transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.34,1.56,0.64,1)',
            'white-space: nowrap',
            'max-width: 90vw',
        ].join('; ');

        el.innerHTML = `
            <img src="/assets/icon/aksa_notif.png"
                 style="width:160px;height:160px;object-fit:contain;flex-shrink:0;"
                 alt="notif" />
            <div>
                <div style="color:#10b981;font-weight:800;font-size:14px;letter-spacing:0.05em;">TERSIMPAN!</div>
                <div style="color:#94a3b8;font-size:12px;margin-top:2px;">${message.replace('💾', '').trim()}</div>
            </div>
        `;
        document.body.appendChild(el);

        // Animate in: slide down from top
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateX(-50%) translateY(0)';
        });

        // Animate out: slide back up
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(-50%) translateY(-10px)';
            setTimeout(() => el.remove(), 300);
        }, 2200);
    }

    function openSaveModal() {
        // Collect State first to validate
        const currentData = collectBlueprintState();
        if (!currentData) return;

        ui.saveModal.classList.remove('hidden');
        ui.modalInput.value = ''; // Reset input
        ui.modalInput.focus();
    }

    function closeSaveModal() {
        ui.saveModal.classList.add('hidden');
    }

    function collectBlueprintState() {
        // Build simulation payload. window.latestResult may be the full payload
        // {zone, level, result} from runSimulation, or just the result object.
        // We always prefer explicit activeZone/activeLevel globals.
        let simulationPayload = null;
        if (window.activeZone && window.activeLevel) {
            const resultObj = (window.latestResult && window.latestResult.result)
                ? window.latestResult.result   // full payload format
                : window.latestResult;          // plain result format
            simulationPayload = {
                zone: window.activeZone,
                level: window.activeLevel,
                result: resultObj
            };
        }

        const data = {
            reverseGoal: window.reverseGoalState || window.manualBaseline || null,
            simulation: simulationPayload,
            sessionId: window.latestSessionId || null
        };

        if (!data.reverseGoal && !data.simulation) {
            showToast('Belum ada data strategi. Silakan isi Simulator terlebih dahulu.', 'error');
            return null;
        }
        return data;
    }

    async function executeSaveBlueprint() {
        const name = ui.modalInput.value.trim();
        if (!name) {
            showToast('Nama blueprint tidak boleh kosong.', 'error');
            ui.modalInput.focus();
            return;
        }

        const payload = collectBlueprintState();
        if (!payload) return;

        payload.name = name; // Add name to payload

        const originalBtnText = ui.modalConfirmBtn.innerHTML;
        ui.modalConfirmBtn.disabled = true;
        ui.modalConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            const authToken = localStorage.getItem('auth_token');
            const response = await fetch('/profit-simulator/blueprints', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok) {
                // 📸 Capture position of the confirm button BEFORE closing modal (otherwise rect is 0)
                const sourceRect = ui.modalConfirmBtn.getBoundingClientRect();

                closeSaveModal();
                // ✅ Center flash notification for save success
                showCenterFlash('Blueprint berhasil disimpan! 💾');

                // --- PHASE 3: Gamification Event Dispatch ---
                document.dispatchEvent(new CustomEvent('cuan:blueprint-saved', {
                    detail: { referenceId: result.data?.id }
                }));

                // Update state
                state.isBlueprintLoaded = true;
                state.currentBlueprintId = result.data?.id || null;
                state.currentBlueprintName = result.data?.name || ui.modalInput.value.trim();
                window.reverseGoalDirty = false;
                window.simulationDirty = false;

                // Show updated save state UI
                updateSaveStrategyUI();

                // 🎒 Trigger mascot animation (passing rect, not element)
                if (window.MascotSystem) {
                    window.MascotSystem.triggerSaveAnimation(sourceRect);
                }

                // Refresh blueprint list (but don't auto-open sidebar — mascot handles UX)
                loadBlueprints();

            } else {
                throw new Error(result.message || 'Gagal menyimpan.');
            }
        } catch (error) {
            console.error(error);
            showToast('Gagal menyimpan: ' + error.message, 'error');
        } finally {
            ui.modalConfirmBtn.disabled = false;
            ui.modalConfirmBtn.innerHTML = originalBtnText;
        }
    }

    async function loadBlueprints() {
        const authToken = localStorage.getItem('auth_token');
        if (!authToken) return;

        ui.blueprintList.innerHTML = `
            <div class="text-center text-slate-400 mt-10">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Memuat blueprints...</p>
            </div>
        `;

        try {
            const response = await fetch('/api/blueprints', {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                }
            });
            const result = await response.json();

            if (result.success) {
                renderBlueprintList(result.data);
                // 🎒 Sync mascot badge count based on total blueprints
                if (window.MascotSystem && typeof result.data === 'object' && !Array.isArray(result.data)) {
                    const totalBlueprints = Object.values(result.data).reduce((acc, curr) => acc + (Array.isArray(curr) ? curr.length : 0), 0);
                    window.MascotSystem.setCount(totalBlueprints);
                }
            } else {
                ui.blueprintList.innerHTML = `<p class="text-rose-500 text-center">Gagal memuat data.</p>`;
            }
        } catch (error) {
            console.error("Failed to load blueprints", error);
            ui.blueprintList.innerHTML = `<p class="text-rose-500 text-center">Gagal memuat data.</p>`;
        }
    }

    // Expose so MascotSystem can trigger a refresh when sidebar opens
    window.loadBlueprintsExternal = loadBlueprints;

    function renderBlueprintList(groupedBlueprints) {
        if (!groupedBlueprints || Object.keys(groupedBlueprints).length === 0) {
            ui.blueprintList.innerHTML = `
                <div class="text-center text-slate-400 mt-10">
                    <p>Belum ada strategi tersimpan.</p>
                </div>
            `;
            return;
        }

        let html = '';
        const typesMap = {
            'reverse_goal': { title: '🎯 Reverse Goal Planner', color: 'blue' },
            'profit_simulator': { title: '📊 Profit Simulation', color: 'emerald' },
            'profit_simulation': { title: '📊 Profit Simulation', color: 'emerald' }, // legacy fallback
            'mentor_lab': { title: '🧠 Business Mentor Lab', color: 'amber' },
            'null': { title: '📂 Lainnya', color: 'slate' }
        };

        for (const [type, blueprints] of Object.entries(groupedBlueprints)) {
            if (!blueprints || !blueprints.length) continue;

            const meta = typesMap[type] || typesMap['null'];

            html += `<h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-4 mb-2 px-1 border-b border-slate-200 dark:border-slate-700 pb-1">${meta.title}</h3>`;

            html += blueprints.map(bp => `
                <div class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700 flex justify-between items-center mb-2 group hover:border-${meta.color}-500 transition-colors">
                    <div class="flex-1 min-w-0 cursor-pointer" onclick="window.loadBlueprintDetail(${bp.id}, '${type}')">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white truncate" title="${bp.name || bp.title}">${bp.name || bp.title}</h4>
                        <p class="text-xs text-slate-500">${new Date(bp.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: '2-digit', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.loadBlueprintDetail(${bp.id}, '${type}')" class="text-${meta.color}-500 hover:text-${meta.color}-600 p-1" title="Load">
                            <i class="fas fa-upload"></i>
                        </button>
                        <button onclick="window.renameBlueprint(${bp.id}, '${bp.name || bp.title}', '${type}')" class="text-amber-500 hover:text-amber-600 p-1" title="Ubah Nama">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="window.deleteBlueprint(${bp.id}, '${type}')" class="text-slate-400 hover:text-rose-500 p-1" title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        ui.blueprintList.innerHTML = html;
    }

    // Export to window for onclick handlers
    window.loadBlueprintDetail = async function (id, type) {
        // If type is mentor_lab and mentor instance exists, delegate to it
        if (type === 'mentor_lab' && window.mentorWizardInstance) {
            window.mentorWizardInstance.loadBlueprint(id);
            document.getElementById('blueprints-sidebar')?.classList.add('translate-x-full');
            if (window.MascotSystem) window.MascotSystem.closeStorage(); // ← fix: reset mascot position

            // Auto Navigation to Mentor Lab
            if (typeof window.switchBentoTab === 'function') {
                window.switchBentoTab('business-simulation-lab');
            }

            // If there's a mobile nav tab, try to click it to sync UI state
            const mobileTab = document.querySelector('[onclick*="business-simulation-lab"]') || document.getElementById('mobile-nav-mentor');
            if (mobileTab && window.innerWidth < 768) mobileTab.click();

            return;
        }

        try {
            const token = localStorage.getItem('auth_token') || '';
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch(`/api/blueprints/${id}`, { headers });

            if (res.status === 401 || res.status === 403) {
                if (typeof showToast !== 'undefined') showToast("Sesi berakhir. Mengarahkan ke halaman login...", "warning");
                setTimeout(() => window.location.href = '/login', 2000);
                return;
            }

            const result = await res.json();

            if (result.success && result.data) {
                window.blueprintData = result.data;

                // Hydrate the UI
                if (typeof hydrateUI === 'function') {
                    hydrateUI(window.blueprintData);
                }

                // Close sidebar if open
                document.getElementById('blueprints-sidebar')?.classList.add('translate-x-full');
                if (window.MascotSystem) window.MascotSystem.closeStorage(); // ← fix: reset mascot position

                // Show success toast
                if (typeof showToast !== 'undefined') showToast(`Blueprint "${result.data.title || result.data.name}" berhasil dimuat`, "success");

                // Auto Navigation to Area Scale Up for both RGP and Simulator blueprints
                if (typeof window.switchBentoTab === 'function') {
                    window.switchBentoTab('profit-simulator-section');
                }

                // If there's a mobile nav tab, try to click it to sync UI state
                const mobileTab = document.querySelector('[onclick*="profit-simulator-section"]') || document.getElementById('mobile-nav-simulator');
                if (mobileTab && window.innerWidth < 768) mobileTab.click();

                // Scroll to Area Scale Up smoothly after a short delay for DOM to settle
                setTimeout(() => {
                    document.getElementById('profit-simulator-section')?.scrollIntoView({ behavior: 'smooth' });
                }, 300);
            } else {
                if (typeof showToast !== 'undefined') showToast(result.message || "Gagal memuat blueprint", "error");
            }
        } catch (e) {
            console.error("Load Blueprint Error:", e);
            if (typeof showToast !== 'undefined') showToast("Terjadi kesalahan saat memuat data blueprint", "error");
        }
    };

    window.renameBlueprint = async function (id, currentName, type) {
        // Find the blueprint item in the list and swap its title to an input
        const btn = document.querySelector(`[onclick="window.renameBlueprint(${id}, '${currentName}', '${type}')"]`);
        const titleEl = btn?.closest('.bg-slate-50, .dark\\:bg-slate-800')?.querySelector('h4');

        if (titleEl) {
            // Inline edit mode
            const originalText = titleEl.textContent;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = originalText;
            input.className = 'w-full text-sm font-semibold bg-white dark:bg-slate-700 border border-emerald-400 rounded px-2 py-0.5 text-slate-900 dark:text-white outline-none';
            titleEl.replaceWith(input);
            input.focus();
            input.select();

            const doSave = async () => {
                const newName = input.value.trim();
                if (!newName || newName === originalText) {
                    loadBlueprints();
                    return;
                }
                const authToken = localStorage.getItem('auth_token');
                try {
                    const response = await fetch(`/api/blueprints/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${authToken}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: newName })
                    });
                    if (response.ok) {
                        showToast('Nama diperbarui!', 'success');
                        loadBlueprints();
                    } else {
                        showToast('Gagal mengubah nama.', 'error');
                        loadBlueprints();
                    }
                } catch (error) {
                    console.error(error);
                    showToast('Error saat mengubah nama.', 'error');
                    loadBlueprints();
                }
            };

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') doSave();
                if (e.key === 'Escape') loadBlueprints();
            });
            input.addEventListener('blur', doSave);
        } else {
            // Fallback: showConfirm-based rename if DOM element not found
            showToast('Klik nama blueprint untuk mengedit langsung.', 'info');
        }
    };

    window.deleteBlueprint = async function (id, type) {
        const confirmed = await new Promise(resolve => {
            showConfirm(
                'Hapus blueprint ini? Tindakan ini tidak bisa dibatalkan.',
                () => resolve(true),
                () => resolve(false)
            );
        });
        if (!confirmed) return;

        const authToken = localStorage.getItem('auth_token');
        try {
            const response = await fetch(`/api/blueprints/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${authToken}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                showToast('Blueprint dihapus.', 'success');
                loadBlueprints();
            } else {
                showToast('Gagal menghapus.', 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Error saat menghapus.', 'error');
        }
    };

    // ─── Snapshot Hydration (no recalc) ───────────────────────────────────────
    // Directly renders saved simulation data. Used by:
    //   - loadBlueprintDetail() (load from sidebar)
    //   - window.attemptHydration() (auto-load on page open)
    window.hydrateSimulation = function (simData) {
        if (!simData) return;
        console.log("[Blueprint] hydrateSimulation called with:", simData);

        const zone = simData.zone || null;
        const level = simData.level || null;
        // The result may be nested under simData.result (stored format)
        // or directly on simData (older format from runSimulation)
        const result = simData.result || simData;

        // If no result data at all, bail
        if (!result || typeof result !== 'object') {
            console.warn("[Blueprint] hydrateSimulation: no result data", simData);
            return;
        }

        // 1. Highlight the correct zone card
        document.querySelectorAll('.zone-card').forEach(z => {
            z.classList.remove('border-blue-500', 'border-emerald-500', 'border-amber-500', 'border-rose-500', 'ring-2', 'ring-offset-2');
            const s = z.querySelector('.level-selector');
            if (s) s.classList.add('hidden');
        });

        if (zone) {
            const zoneCard = document.querySelector(`.zone-card[data-zone="${zone}"]`);
            if (zoneCard) {
                let borderColor = 'border-blue-500';
                if (zone === 'conversion') borderColor = 'border-emerald-500';
                if (zone === 'pricing') borderColor = 'border-amber-500';
                if (zone === 'cost') borderColor = 'border-rose-500';

                zoneCard.classList.add(borderColor, 'ring-2', 'ring-offset-2');

                const selector = zoneCard.querySelector('.level-selector');
                if (selector) {
                    selector.classList.remove('hidden');
                    selector.querySelectorAll('.level-btn').forEach(btn => {
                        btn.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                        if (btn.dataset.level == level) {
                            btn.classList.add('bg-slate-200', 'dark:bg-slate-700');
                        }
                    });
                }
            }
        }

        // 2. Render the result panel directly from saved data
        renderResult(result);

        // 3. Sync globals
        window.latestResult = result;
        window.activeZone = zone;
        window.activeLevel = level;
        window.simulationDirty = false;

        console.log("[Blueprint] hydrateSimulation complete. zone:", zone, "level:", level);
    };

    // ─── Full Blueprint UI Hydration (used by loadBlueprintDetail) ────────────
    function hydrateUI(minifiedData) {
        console.log("[Blueprint] hydrateUI called with:", minifiedData);

        // 1. Build flat baseline from reverse_goal_data
        if (minifiedData.reverse_goal_data) {
            const rgData = minifiedData.reverse_goal_data;
            let flatBaseline = {};

            if (rgData.input && rgData.output) {
                const input = rgData.input;
                const output = rgData.output;
                const cogs = output.selling_price * (1 - (input.assumed_margin / 100));

                flatBaseline = {
                    price: output.selling_price,
                    traffic: output.required_traffic,
                    conversion_rate: input.assumed_conversion,
                    cogs: cogs,
                    fixed_cost: 0,
                    ad_spend: output.total_ad_spend
                };
            } else {
                flatBaseline = rgData;
            }

            window.manualBaseline = flatBaseline;
            window.reverseGoalState = rgData;

            // Hydrate RGP form
            if (window.hydrateReverseGoal) {
                window.reverseGoalDirty = false;
                window.hydrateReverseGoal(rgData);
            }

            // Notify simulator the baseline changed (isInit=true → no UI reset)
            window.dispatchEvent(new CustomEvent('reverse-goal-planner:update', {
                detail: { ...flatBaseline, isInit: true }
            }));
        }

        // 2. Hydrate simulator as a snapshot — NO recalculation
        if (minifiedData.simulation_data) {
            window.hydrateSimulation(minifiedData.simulation_data);
        }

        // 3. Mark blueprint as loaded & reset dirty state
        state.isBlueprintLoaded = true;
        state.currentBlueprintId = minifiedData.id || null;
        state.currentBlueprintName = minifiedData.name || 'Strategi';
        window.reverseGoalDirty = false;
        window.simulationDirty = false;

        // Update save button UI
        updateSaveStrategyUI();
    }


    // Helper
    function formatCurrency(num) {
        if (num >= 1000000) {
            let abbrev = '';
            let val = num;
            if (num >= 1000000000000) {
                val = num / 1000000000000;
                abbrev = ' T';
            } else if (num >= 1000000000) {
                val = num / 1000000000;
                abbrev = ' M';
            } else {
                val = num / 1000000;
                abbrev = ' jt';
            }
            return 'Rp ' + val.toLocaleString('id-ID', { maximumFractionDigits: 4 }) + abbrev;
        }

        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    }

    // Hydrate from window.blueprintData if it belongs to reverse goal or profit simulator
    if (window.blueprintData && (!window.blueprintData.type || window.blueprintData.type === 'profit_simulator' || window.blueprintData.type === 'profit_simulation' || window.blueprintData.type === 'reverse_goal')) {
        hydrateUI(window.blueprintData);
    }
});
