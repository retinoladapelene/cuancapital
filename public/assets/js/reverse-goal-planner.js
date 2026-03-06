/**
 * Reverse Goal Planner 3.0
 * Outcome-driven engine: progressive disclosure, auto-calculate, smart defaults.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ─── Constants ────────────────────────────────────────────────────────────

    /** Smart price suggestions per model */
    const PRICE_PRESETS = {
        dropship: [30000, 80000, 150000, 300000],
        digital: [97000, 300000, 500000, 997000],
        service: [500000, 1500000, 3000000, 10000000],
        stock: [50000, 150000, 500000, 1000000],
        affiliate: [0]  // no price for affiliate
    };

    const MARGIN_PRESETS = {
        dropship: 20,
        digital: 80,
        service: 50,
        stock: 40,
        affiliate: 10
    };

    const PRICE_LABELS = {
        dropship: ['30rb', '80rb', '150rb', '300rb'],
        digital: ['97rb', '300rb', '500rb', '997rb'],
        service: ['500rb', '1,5jt', '3jt', '10jt'],
        stock: ['50rb', '150rb', '500rb', '1jt'],
        affiliate: []
    };

    /** Mascot configuration per model */
    const MASCOT_CONFIG = {
        default: {
            img: 'assets/icon/mascotfinal.png',
            text: "Yuk, Aksa temenin capai target kamu! 🚀"
        },
        dropship: {
            img: 'assets/icon/Aksa_dropshipper.png',
            text: "Yuk bareng Aksa, kita mulai dropship! Anti ribet, cuan bisa ngalir kapan aja"
        },
        digital: {
            img: 'assets/icon/aksa_produkdigital.png',
            text: "Produk digital itu masa depan, bareng Aksa kita bisa jualan kapan aja, di mana aja"
        },
        service: {
            img: 'assets/icon/aksa_agncy.png',
            text: "Agency itu bukan cuma kerjaan serius, bareng Aksa kita bisa bikin layanan yang fun tapi tetap profesional"
        },
        stock: {
            img: 'assets/icon/aksa_retail.png',
            text: "Ayo bareng Aksa, kita kelola stok dan retail biar cuan nggak pernah berhenti"
        }
    };

    // ─── State ─────────────────────────────────────────────────────────────────
    window.rgpState = {
        model: null,
        target_profit: null,
        selling_price: null,
        timeline: 30,
        capital: 5000000,
        hours: 4,
        margin: null,
        traffic_strategy: 'ads',
        // New RGP V3 Inputs
        fixed_costs: 0,
        return_rate: 10,
        affiliate_rate: 30,
        capacity: 10,
        warehouse_cost: 0
    };

    // ─── Element refs ──────────────────────────────────────────────────────────
    const el = {
        resultsContainer: document.getElementById('rp-results'),
        loadingIndicator: document.getElementById('rgp-loading-indicator'),
        modelCards: document.querySelectorAll('.rgp-model-card'),
        targetPresets: document.querySelectorAll('.rgp-target-preset'),
        pricePresetsWrap: document.getElementById('rgp-price-presets'),
        targetInput: document.getElementById('rp-target-profit'),
        priceInput: document.getElementById('rp-price'),
        timelineSlider: document.getElementById('rp-timeline'),
        timelineDisplay: document.getElementById('rp-timeline-display'),
        capitalSlider: document.getElementById('rp-capital'),
        capitalDisplay: document.getElementById('rp-capital-display'),
        hoursSlider: document.getElementById('rp-hours'),
        hoursSlider: document.getElementById('rp-hours'),
        hoursDisplay: document.getElementById('rp-hours-display'),
        marginSlider: document.getElementById('rp-margin'), // New
        marginDisplay: document.getElementById('rp-margin-display'), // New
        strategyCards: document.querySelectorAll('.rgp-strategy-card'),
        advancedToggle: document.getElementById('rgp-advanced-toggle'),
        advancedPanel: document.getElementById('rgp-advanced-panel'),
        toggleIcon: document.getElementById('rgp-toggle-icon'),
        // New V3 Specific Sliders
        rtsSlider: document.getElementById('rp-rts'),
        rtsDisplay: document.getElementById('rp-rts-display'),
        warehouseSlider: document.getElementById('rp-warehouse'),
        warehouseDisplay: document.getElementById('rp-warehouse-display'),
        affiliateSlider: document.getElementById('rp-affiliate'),
        affiliateDisplay: document.getElementById('rp-affiliate-display'),
        capacitySlider: document.getElementById('rp-capacity'),
        capacityDisplay: document.getElementById('rp-capacity-display'),
        fixedCostSlider: document.getElementById('rp-fixedcost'),
        fixedCostDisplay: document.getElementById('rp-fixedcost-display'),

        // hidden inputs (read by backend-compat layer)
        hiddenModel: document.getElementById('rp-model'),
        hiddenStrategy: document.getElementById('rp-strategy'),
        hiddenTimeline: null, // now a range, we'll sync below
        hiddenCapital: null,
        hiddenHours: null,
    };

    if (!el.resultsContainer) return; // guard: not on this page

    // ─── Utilities ─────────────────────────────────────────────────────────────

    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

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

        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', maximumFractionDigits: 0
        }).format(num);
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    // ─── Core Readiness Guard ───────────────────────────────────────────────────

    function isCoreReady() {
        return (
            window.rgpState.model &&
            Number(window.rgpState.target_profit) > 0 &&
            Number(window.rgpState.selling_price) > 0
        );
    }

    // ─── Price Presets Builder ──────────────────────────────────────────────────

    function buildPricePresets(model) {
        if (!el.pricePresetsWrap) return;
        el.pricePresetsWrap.innerHTML = '';

        const values = PRICE_PRESETS[model] || [];
        const labels = PRICE_LABELS[model] || [];

        values.forEach((val, i) => {
            if (val === 0) return;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.value = val;
            btn.className = 'rgp-preset-btn rgp-price-preset px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all';
            btn.textContent = 'Rp ' + (labels[i] || val);
            btn.addEventListener('click', () => {
                setPrice(val, btn);
                autoCalculate();
            });
            el.pricePresetsWrap.appendChild(btn);
        });
    }

    // ─── Active state helpers ───────────────────────────────────────────────────

    const MODEL_COLORS = {
        'dropship': 'orange',
        'digital': 'violet',
        'service': 'emerald',
        'stock': 'blue'
    };

    const INACTIVE_CARD_CLASSES = ['border-slate-700/80', 'bg-slate-800/40'];

    function setActiveCard(group, activeBtn) {
        group.forEach(btn => {
            // Remove active classes generic to all colors
            const prevModel = btn.dataset.model;
            if (prevModel) {
                const color = MODEL_COLORS[prevModel] || 'emerald';
                btn.classList.remove(`border-${color}-500`, `bg-${color}-500/20`, `shadow-lg`, `shadow-${color}-500/30`, `-translate-y-1`);
            }
            btn.classList.add(...INACTIVE_CARD_CLASSES);
        });

        if (activeBtn) {
            const model = activeBtn.dataset.model;
            // Only apply specific coloring if data-model exists
            if (model) {
                const color = MODEL_COLORS[model] || 'emerald';
                activeBtn.classList.remove(...INACTIVE_CARD_CLASSES);
                activeBtn.classList.add(`border-${color}-500`, `bg-${color}-500/20`, `shadow-lg`, `shadow-${color}-500/30`, `-translate-y-1`);
            } else {
                // Fallback
                activeBtn.classList.remove(...INACTIVE_CARD_CLASSES);
                activeBtn.classList.add('border-emerald-500', 'bg-emerald-500/20', 'shadow-lg', 'shadow-emerald-500/30', '-translate-y-1');
            }
        }
    }

    const ACTIVE_PRESET_CLASSES = ['border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20', 'text-emerald-700'];

    function setActivePreset(group, activeBtn) {
        group.forEach(btn => btn.classList.remove(...ACTIVE_PRESET_CLASSES));
        if (activeBtn) activeBtn.classList.add(...ACTIVE_PRESET_CLASSES);
    }

    function updateMascot() {
        const titleEl = document.getElementById('hero-card-title');
        // Get text from card, default to empty string
        const text = titleEl ? titleEl.textContent.trim().toLowerCase() : '';

        let config = MASCOT_CONFIG['default']; // Default fallback

        if (text === 'model bisnis' || text === '') {
            config = MASCOT_CONFIG['default'];
        } else if (text.includes('dropship')) {
            config = MASCOT_CONFIG['dropship'];
        } else if (text.includes('digital') || text.includes('produk')) {
            config = MASCOT_CONFIG['digital'];
        } else if (text.includes('service') || text.includes('jasa') || text.includes('agency')) {
            config = MASCOT_CONFIG['service'];
        } else if (text.includes('stock') || text.includes('retail') || text.includes('stok')) {
            config = MASCOT_CONFIG['stock'];
        }

        if (!config) return;

        const imgEl = document.getElementById('mascot-image');
        const textEl = document.getElementById('mascot-bubble-text');

        if (imgEl) {
            imgEl.style.transition = 'all 0.3s ease';
            // Only animate if source changes to avoid flicker
            if (!imgEl.src.includes(config.img)) {
                imgEl.style.opacity = '0';
                imgEl.style.transform = 'translateY(10px) scale(0.9)';

                setTimeout(() => {
                    imgEl.src = config.img;
                    imgEl.style.opacity = '1';
                    imgEl.style.transform = 'translateY(0) scale(1)';
                }, 300);
            }
        }

        if (textEl) {
            textEl.textContent = config.text;
        }
    }

    // ─── State setters ──────────────────────────────────────────────────────────

    function setModel(model, cardBtn) {
        window.rgpState.model = model;
        if (el.hiddenModel) el.hiddenModel.value = model;
        setActiveCard(el.modelCards, cardBtn);
        buildPricePresets(model);

        // --- NEW V3 LOGIC: Show/Hide Specific Inputs based on classes ---
        const specificInputs = document.querySelectorAll('.model-specific-input');
        specificInputs.forEach(input => {
            // Hide all first
            input.classList.add('hidden');
            // Show only if it has the class matching the model
            if (input.classList.contains(`${model}-only`)) {
                input.classList.remove('hidden');
            }
        });

        // Auto-set default margin
        const defaultMargin = MARGIN_PRESETS[model] || 20;
        setMargin(defaultMargin);

        // Auto-set default price for this model if price not set yet
        const defaults = PRICE_PRESETS[model];
        if (defaults && defaults.length > 0 && defaults[0] > 0) {
            const mid = defaults[Math.floor(defaults.length / 2)] ?? defaults[0];
            if (!window.rgpState.selling_price) {
                setPrice(mid, null);
            }
        }
    }

    function setPrice(value, presetBtn) {
        window.rgpState.selling_price = Number(value);
        if (el.priceInput) el.priceInput.value = value;
        // Update active preset
        const allPricePresets = el.pricePresetsWrap?.querySelectorAll('.rgp-price-preset');
        if (allPricePresets) setActivePreset(allPricePresets, presetBtn);
    }

    const STRATEGY_COLORS = {
        'ads': 'yellow',
        'organic': 'green',
        'hybrid': 'blue'
    };

    const STRATEGY_INACTIVE_CLASSES = ['border-slate-200', 'dark:border-slate-700', 'bg-slate-50', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-400'];

    function setActiveStrategyCard(group, activeBtn) {
        // Reset all strategy cards to inactive
        group.forEach(btn => {
            const strat = btn.dataset.strategy;
            const c = STRATEGY_COLORS[strat] || 'emerald';
            // Remove any active styling
            btn.classList.remove(
                `border-${c}-500`, `bg-${c}-500/10`, `dark:bg-${c}-500/20`,
                `text-${c}-600`, `dark:text-${c}-400`,
                `ring-1`, `ring-${c}-500/50`
            );
            // Re-apply inactive
            STRATEGY_INACTIVE_CLASSES.forEach(cls => btn.classList.add(cls));
        });

        if (activeBtn) {
            const strat = activeBtn.dataset.strategy;
            const c = STRATEGY_COLORS[strat] || 'emerald';
            // Remove inactive
            activeBtn.classList.remove(...STRATEGY_INACTIVE_CLASSES);
            // Apply active — using colors already present in hover styles
            activeBtn.classList.add(
                `border-${c}-500`, `bg-${c}-500/10`, `dark:bg-${c}-500/20`,
                `text-${c}-600`, `dark:text-${c}-400`,
                `ring-1`, `ring-${c}-500/50`
            );
        }
    }

    function setStrategy(strategy, cardBtn) {
        window.rgpState.traffic_strategy = strategy;
        if (el.hiddenStrategy) el.hiddenStrategy.value = strategy;
        setActiveStrategyCard(el.strategyCards, cardBtn);
    }

    function setMargin(val) {
        window.rgpState.margin = Number(val);
        if (el.marginSlider) el.marginSlider.value = val;
        if (el.marginDisplay) el.marginDisplay.textContent = val;
    }

    // ─── Auto-Calculate Engine ──────────────────────────────────────────────────

    function showLoading() {
        if (el.loadingIndicator) el.loadingIndicator.classList.remove('hidden');
    }

    function hideLoading() {
        if (el.loadingIndicator) el.loadingIndicator.classList.add('hidden');
    }

    async function autoCalculate() {
        if (!isCoreReady()) return;

        showLoading();

        const payload = {
            business_model: window.rgpState.model,
            target_profit: window.rgpState.target_profit,
            capital_available: window.rgpState.capital,
            timeline_days: window.rgpState.timeline,
            selling_price: window.rgpState.selling_price,
            hours_per_day: window.rgpState.hours,
            custom_margin: window.rgpState.margin,
            traffic_strategy: window.rgpState.traffic_strategy,

            // New V3 specific data
            fixed_costs: window.rgpState.fixed_costs,
            return_rate: window.rgpState.return_rate,
            warehouse_cost: window.rgpState.warehouse_cost,
            affiliate_rate: window.rgpState.affiliate_rate,
            capacity: window.rgpState.capacity,
        };

        // Mark dirty only when not hydrating
        if (!window.isHydrating) {
            window.reverseGoalDirty = true;
            if (window.updateSaveStrategyUI) window.updateSaveStrategyUI();
            console.log('[RGP] autoCalculate triggered — marked dirty');
        }

        try {
            const response = await fetch('/reverse-planner/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                // The actual calculation results are now inside data.data
                const rgpResult = data.data;

                renderResults(rgpResult);
                el.resultsContainer.classList.remove('hidden');

                // Update global state for Blueprint
                window.reverseGoalState = rgpResult;

                // Build baseline for Profit Simulator
                const rInput = data.data.input;
                const rOutput = data.data.output;
                const cogs = rOutput.selling_price * (1 - (rInput.assumed_margin / 100));

                window.manualBaseline = {
                    price: rOutput.selling_price,
                    traffic: rOutput.required_traffic,
                    conversion_rate: rInput.assumed_conversion,
                    cogs: cogs,
                    fixed_cost: rOutput.fixed_costs || 0,
                    ad_spend: rOutput.total_ad_spend,
                    // V3 enhanced data for realistic simulation
                    business_model: rInput.business_model || 'dropship',
                    return_rate: rInput.return_rate || 0,
                    warehouse_cost: rInput.warehouse_cost || 0,
                    total_capital_needed: rOutput.total_capital_needed || 0,
                    unit_profit: rOutput.unit_profit || 0,
                    cpa: rOutput.cpa || 0
                };

                window.dispatchEvent(new CustomEvent('reverse-goal-planner:update', {
                    detail: window.manualBaseline
                }));

                // --- PHASE 3: Gamification Event Dispatch ---
                const scores = data.data?.scores || {};
                const feasibilityStatus = scores.status_color === 'green' ? 'High' :
                    (scores.status_color === 'yellow' ? 'Medium' : 'Low');

                if (data.data.session_id) window.latestSessionId = data.data.session_id;

                window.dispatchEvent(new CustomEvent('cuan:reverse-planner-calculated', {
                    detail: {
                        feasibility: feasibilityStatus,
                        model: window.rgpState.model,
                        referenceId: window.latestSessionId
                    }
                }));

                // --- PHASE 16: Emotial Retention Loop 60fps refresh ---
                if (window.Gamification) window.Gamification.refresh();
            } else {
                console.warn('[RGP] Calculation failed:', data.message);
            }
        } catch (err) {
            console.error('[RGP] Error:', err);
        } finally {
            hideLoading();
        }
    }

    const debouncedRecalc = debounce(autoCalculate, 350);

    // ─── Events: Model Cards ────────────────────────────────────────────────────

    el.modelCards.forEach(card => {
        card.addEventListener('click', () => {
            const model = card.dataset.model;
            setModel(model, card);
            if (isCoreReady()) autoCalculate();
        });
    });

    // ─── Events: Target Presets ─────────────────────────────────────────────────

    el.targetPresets.forEach(btn => {
        btn.addEventListener('click', () => {
            const val = Number(btn.dataset.value);
            window.rgpState.target_profit = val;
            if (el.targetInput) el.targetInput.value = val;
            setActivePreset(el.targetPresets, btn);
            autoCalculate();
        });
    });

    // Target manual input
    if (el.targetInput) {
        el.targetInput.addEventListener('input', () => {
            window.rgpState.target_profit = Number(el.targetInput.value) || null;
            setActivePreset(el.targetPresets, null); // deselect presets
            if (isCoreReady()) debouncedRecalc();
        });
    }

    // Price manual input
    if (el.priceInput) {
        el.priceInput.addEventListener('input', () => {
            const allPricePresets = el.pricePresetsWrap?.querySelectorAll('.rgp-price-preset');
            window.rgpState.selling_price = Number(el.priceInput.value) || null;
            if (allPricePresets) setActivePreset(allPricePresets, null);
            if (isCoreReady()) debouncedRecalc();
        });
    }

    // ─── Events: Sliders (debounced) ────────────────────────────────────────────

    if (el.timelineSlider) {
        el.timelineSlider.addEventListener('input', () => {
            window.rgpState.timeline = Number(el.timelineSlider.value);
            if (el.timelineDisplay) el.timelineDisplay.textContent = el.timelineSlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.capitalSlider) {
        el.capitalSlider.addEventListener('input', () => {
            window.rgpState.capital = Number(el.capitalSlider.value);
            if (el.capitalDisplay) el.capitalDisplay.textContent = formatRupiah(el.capitalSlider.value);
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.hoursSlider) {
        el.hoursSlider.addEventListener('input', () => {
            window.rgpState.hours = Number(el.hoursSlider.value);
            if (el.hoursDisplay) el.hoursDisplay.textContent = el.hoursSlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.marginSlider) {
        el.marginSlider.addEventListener('input', () => {
            window.rgpState.margin = Number(el.marginSlider.value);
            if (el.marginDisplay) el.marginDisplay.textContent = el.marginSlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    // ─── Events: New V3 Sliders (debounced) ─────────────────────────────────────

    if (el.rtsSlider) {
        el.rtsSlider.addEventListener('input', () => {
            window.rgpState.return_rate = Number(el.rtsSlider.value);
            if (el.rtsDisplay) el.rtsDisplay.textContent = el.rtsSlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.warehouseSlider) {
        el.warehouseSlider.addEventListener('input', () => {
            window.rgpState.warehouse_cost = Number(el.warehouseSlider.value);
            if (el.warehouseDisplay) el.warehouseDisplay.textContent = formatRupiah(el.warehouseSlider.value);
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.affiliateSlider) {
        el.affiliateSlider.addEventListener('input', () => {
            window.rgpState.affiliate_rate = Number(el.affiliateSlider.value);
            if (el.affiliateDisplay) el.affiliateDisplay.textContent = el.affiliateSlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.capacitySlider) {
        el.capacitySlider.addEventListener('input', () => {
            window.rgpState.capacity = Number(el.capacitySlider.value);
            if (el.capacityDisplay) el.capacityDisplay.textContent = el.capacitySlider.value;
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    if (el.fixedCostSlider) {
        el.fixedCostSlider.addEventListener('input', () => {
            window.rgpState.fixed_costs = Number(el.fixedCostSlider.value);
            if (el.fixedCostDisplay) el.fixedCostDisplay.textContent = formatRupiah(el.fixedCostSlider.value);
            showLoading();
            if (isCoreReady()) debouncedRecalc();
        });
    }

    // ─── Events: Strategy Cards ─────────────────────────────────────────────────

    el.strategyCards.forEach(card => {
        card.addEventListener('click', () => {
            setStrategy(card.dataset.strategy, card);
            if (isCoreReady()) autoCalculate();
        });
    });

    // ─── Events: Advanced Toggle ────────────────────────────────────────────────

    if (el.advancedToggle && el.advancedPanel) {
        el.advancedToggle.addEventListener('click', () => {
            const isOpen = !el.advancedPanel.classList.contains('hidden');
            el.advancedPanel.classList.toggle('hidden', isOpen);
            if (el.toggleIcon) el.toggleIcon.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    }

    // ─── Events: Detail Keuangan Toggle ──────────────────────────────────────────

    const detailToggle = document.getElementById('rgp-detail-toggle');
    const detailPanel = document.getElementById('rgp-detail-panel');
    const detailIcon = document.getElementById('rgp-detail-icon');

    if (detailToggle && detailPanel) {
        detailToggle.addEventListener('click', () => {
            const isOpen = !detailPanel.classList.contains('hidden');
            detailPanel.classList.toggle('hidden', isOpen);
            if (detailIcon) detailIcon.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    }

    // ─── Sync UI from state (for hydration) ────────────────────────────────────

    function syncPresetUI() {
        // Model cards
        el.modelCards.forEach(card => {
            if (card.dataset.model === window.rgpState.model) {
                setModel(window.rgpState.model, card);
            }
        });

        // Target presets
        el.targetPresets.forEach(btn => {
            if (Number(btn.dataset.value) === Number(window.rgpState.target_profit)) {
                setActivePreset(el.targetPresets, btn);
            }
        });
        if (el.targetInput) el.targetInput.value = window.rgpState.target_profit || '';

        // Price presets (rebuild first, then highlight)
        buildPricePresets(window.rgpState.model);
        if (el.priceInput) el.priceInput.value = window.rgpState.selling_price || '';
        const allPricePresets = el.pricePresetsWrap?.querySelectorAll('.rgp-price-preset');
        if (allPricePresets) {
            allPricePresets.forEach(btn => {
                if (Number(btn.dataset.value) === Number(window.rgpState.selling_price)) {
                    setActivePreset(allPricePresets, btn);
                }
            });
        }

        // Strategy cards
        el.strategyCards.forEach(card => {
            if (card.dataset.strategy === window.rgpState.traffic_strategy) {
                setActiveStrategyCard(el.strategyCards, card);
            }
        });
    }

    function syncSliderUI() {
        if (el.timelineSlider) {
            el.timelineSlider.value = window.rgpState.timeline;
            if (el.timelineDisplay) el.timelineDisplay.textContent = window.rgpState.timeline;
        }
        if (el.capitalSlider) {
            el.capitalSlider.value = window.rgpState.capital;
            if (el.capitalDisplay) el.capitalDisplay.textContent = formatRupiah(window.rgpState.capital);
        }
        if (el.hoursSlider) {
            el.hoursSlider.value = window.rgpState.hours;
            if (el.hoursDisplay) el.hoursDisplay.textContent = window.rgpState.hours;
        }
        if (el.marginSlider) {
            el.marginSlider.value = window.rgpState.margin;
            if (el.marginDisplay) el.marginDisplay.textContent = window.rgpState.margin;
        }

        // V3 Specific Sync
        if (el.rtsSlider) {
            el.rtsSlider.value = window.rgpState.return_rate;
            if (el.rtsDisplay) el.rtsDisplay.textContent = window.rgpState.return_rate;
        }
        if (el.warehouseSlider) {
            el.warehouseSlider.value = window.rgpState.warehouse_cost;
            if (el.warehouseDisplay) el.warehouseDisplay.textContent = formatRupiah(window.rgpState.warehouse_cost);
        }
        if (el.affiliateSlider) {
            el.affiliateSlider.value = window.rgpState.affiliate_rate;
            if (el.affiliateDisplay) el.affiliateDisplay.textContent = window.rgpState.affiliate_rate;
        }
        if (el.capacitySlider) {
            el.capacitySlider.value = window.rgpState.capacity;
            if (el.capacityDisplay) el.capacityDisplay.textContent = window.rgpState.capacity;
        }
        if (el.fixedCostSlider) {
            el.fixedCostSlider.value = window.rgpState.fixed_costs;
            if (el.fixedCostDisplay) el.fixedCostDisplay.textContent = formatRupiah(window.rgpState.fixed_costs);
        }
    }

    // ─── Hydration API ──────────────────────────────────────────────────────────

    window.hydrateReverseGoal = function (data) {
        if (!data) return;
        console.log('[RGP] hydrateReverseGoal called with:', data);

        window.isHydrating = true;

        // Populate state from saved blueprint data
        const inp = data.input || data;
        const out = data.output || {};

        window.rgpState = {
            model: inp.business_model || 'service',
            target_profit: inp.target_profit || null,
            selling_price: out.selling_price || inp.selling_price || null,
            timeline: inp.timeline_days || 30,
            capital: inp.capital_available || 5000000,
            hours: inp.hours_per_day || 4,
            traffic_strategy: inp.traffic_strategy || 'ads',
            margin: inp.assumed_margin || inp.custom_margin || null,

            // V3 specific hydration defaults
            fixed_costs: inp.fixed_costs || 0,
            return_rate: inp.return_rate || 10,
            warehouse_cost: inp.warehouse_cost || 0,
            affiliate_rate: inp.affiliate_rate || 30,
            capacity: inp.capacity || 10
        };

        // Update global state for blueprint saving
        window.reverseGoalState = data;

        // Update hidden inputs (backend compat)
        if (el.hiddenModel) el.hiddenModel.value = window.rgpState.model;
        if (el.hiddenStrategy) el.hiddenStrategy.value = window.rgpState.traffic_strategy;

        // Sync all visual UI elements
        syncPresetUI();
        syncSliderUI();

        window.reverseGoalDirty = false;
        window.isHydrating = false;

        // Render output from saved data (not recalculate)
        renderResults(data);
        el.resultsContainer.classList.remove('hidden');

        // Build baseline for Profit Simulator
        if (data.input && data.output) {
            const rInput = data.input;
            const rOutput = data.output;
            const cogs = rOutput.selling_price * (1 - (rInput.assumed_margin / 100));
            window.manualBaseline = {
                price: rOutput.selling_price,
                traffic: rOutput.required_traffic,
                conversion_rate: rInput.assumed_conversion,
                cogs: cogs,
                fixed_cost: 0,
                ad_spend: rOutput.total_ad_spend
            };
            window.dispatchEvent(new CustomEvent('reverse-goal-planner:update', {
                detail: { ...window.manualBaseline, isInit: true }
            }));
        }

        if (data.id) window.latestSessionId = data.id;

        console.log('[RGP] Hydration complete. rgpState:', window.rgpState);
    };

    // ─── Initialize UI ──────────────────────────────────────────────────────────

    // Set default model (service) and build its price presets
    (() => {
        const defaultCard = document.querySelector('.rgp-model-card[data-model="service"]');
        setModel('service', defaultCard);
        setStrategy('ads', document.querySelector('.rgp-strategy-card[data-strategy="ads"]'));
    })();

    // Old-data compatibility: if savedPlannerData exists (from Blade), hydrate it
    if (window.savedPlannerData) {
        const d = window.savedPlannerData;
        const mockData = {
            id: d.id,
            input: {
                business_model: d.business_model,
                traffic_strategy: d.traffic_strategy,
                target_profit: d.target_profit,
                timeline_days: d.timeline_days,
                capital_available: d.capital_available,
                hours_per_day: d.hours_per_day,
                assumed_margin: d.assumed_margin,
                assumed_conversion: d.assumed_conversion,
                assumed_cpc: d.assumed_cpc,
                selling_price: d.selling_price,

                // V3 back-compat mapping (defaults if not present)
                fixed_costs: d.fixed_costs || 0,
                return_rate: d.return_rate || 10,
                warehouse_cost: d.warehouse_cost || 0,
                affiliate_rate: d.affiliate_rate || 30,
                capacity: d.capacity || 10
            },
            output: {
                unit_profit: d.unit_net_profit,
                required_units: d.required_units,
                required_traffic: d.required_traffic,
                total_ad_spend: d.required_ad_budget,
                execution_load_ratio: d.execution_load_ratio,
                selling_price: d.selling_price || 0
            },
            scores: {
                goal_status: d.risk_level === 'Realistic' ? 'Siap Gaskeun' : (d.risk_level === 'Challenging' ? 'Butuh Penyesuaian' : 'Terlalu Berat'),
                constraint_message: JSON.parse(d.constraint_snapshot || '{}').message || 'Berdasarkan data tersimpan.',
                status_color: d.risk_level === 'Realistic' ? 'green' : (d.risk_level === 'Challenging' ? 'yellow' : 'red'),
                learning_moment: 'Rencana ini dimuat dari sesi terakhir kamu.',
                recommendations: []
            },
            logic_version: d.logic_version
        };
        window.isHydrating = true;
        window.hydrateReverseGoal(mockData);
        window.isHydrating = false;
    }

    // ─── Result Renderer ────────────────────────────────────────────────────────

    function renderResults(data) {
        const input = data.input;
        const output = data.output;
        const scores = data.scores;

        if (!input || !output || !scores) return;

        // Check feasibility (New Logic V2)
        if (output.feasible === false) {
            renderInfeasibleState(data);
            return;
        }

        // Version
        const versionEl = document.getElementById('rp-logic-version');
        if (versionEl) versionEl.textContent = data.logic_version || 'v3.0';

        // Scorecard
        updateText('sc-target', formatCurrency(input.target_profit));
        updateText('sc-duration', input.timeline_days + ' Hari');
        updateText('sc-model', (input.business_model || 'N/A').charAt(0).toUpperCase() + (input.business_model || 'N/A').slice(1));
        updateText('sc-price', formatCurrency(output.selling_price || input.selling_price || 0));

        // Milestones — compute daily sales since backend doesn't return it
        const reqUnits = output.required_units || output.required_sales || 0;
        const dailySales = (input.timeline_days > 0) ? Math.ceil(reqUnits / input.timeline_days) : 0;
        updateText('rp-req-units', formatNumber(reqUnits));
        updateText('rp-daily-units', dailySales);

        // ─── INJEKSI: EDUKASI MIKRO SALES ───
        const salesMicroEl = document.getElementById('rp-sales-micro');
        if (salesMicroEl) {
            if (dailySales === 0) {
                salesMicroEl.textContent = 'Belum ada data.';
            } else if (dailySales <= 2) {
                salesMicroEl.textContent = 'Sangat ringan. Cuma butuh 1-2 pembeli riil.';
            } else if (dailySales <= 5) {
                salesMicroEl.textContent = 'Mudah dicapai dengan konsistensi.';
            } else if (dailySales <= 15) {
                salesMicroEl.textContent = 'Level menengah. Butuh strategi promosi aktif.';
            } else {
                salesMicroEl.textContent = 'Fokus pada volume dan tim sales.';
            }
        }

        const reqTraffic = output.required_traffic || 0;
        updateText('rp-req-traffic', formatNumber(reqTraffic));

        // ─── INJEKSI: EDUKASI MIKRO TRAFFIC ───
        const trafficMicroEl = document.getElementById('rp-traffic-micro');
        if (trafficMicroEl) {
            const dailyTraffic = (input.timeline_days > 0) ? Math.ceil(reqTraffic / input.timeline_days) : 0;
            if (dailyTraffic === 0) {
                trafficMicroEl.textContent = 'Belum ada data.';
            } else if (dailyTraffic <= 50) {
                trafficMicroEl.textContent = 'Artinya ± 2 pengunjung per jam.';
            } else if (dailyTraffic <= 240) {
                trafficMicroEl.textContent = 'Artinya ± 10 pengunjung per jam. Masih santai.';
            } else if (dailyTraffic <= 1000) {
                trafficMicroEl.textContent = 'Level kolaborasi / Iklan medium.';
            } else {
                trafficMicroEl.textContent = 'Butuh ledakan viral / Iklan masif.';
            }
        }

        updateText('rp-req-budget', formatCurrency(output.total_ad_spend || 0));

        // Revenue = units × selling price
        const sellingPrice = output.selling_price || input.selling_price || window.rgpState?.selling_price || 0;
        const revenue = reqUnits * sellingPrice;
        updateText('rp-revenue', revenue > 0 ? formatCurrency(revenue) : '--');

        // Hero section
        const heroRevenue = document.getElementById('hero-total-revenue');
        if (heroRevenue) {
            heroRevenue.textContent = formatCurrency(input.target_profit);
            heroRevenue.classList.remove('animate-pulse');
            void heroRevenue.offsetWidth;
            heroRevenue.classList.add('animate-pulse');
        }
        const heroSales = document.getElementById('hero-target-sales');
        if (heroSales) heroSales.textContent = formatNumber(output.required_units || 0) + ' Unit';

        // Hero cards
        const heroCardTarget = document.getElementById('hero-card-target');
        const heroCardTraffic = document.getElementById('hero-card-traffic');
        const heroCardConv = document.getElementById('hero-card-conv');
        const heroCardSales = document.getElementById('hero-card-sales');
        const heroCardPrice = document.getElementById('hero-card-price');
        const heroCardBar = document.getElementById('hero-card-progress-bar');
        const heroCardText = document.getElementById('hero-card-progress-text');
        const heroCardTitle = document.getElementById('hero-card-title');

        if (heroCardTarget) heroCardTarget.textContent = formatCurrency(input.target_profit);
        if (heroCardTraffic) heroCardTraffic.textContent = formatNumber(output.required_traffic || 0);
        if (heroCardConv) heroCardConv.textContent = (input.assumed_conversion || 0) + '%';
        if (heroCardSales) heroCardSales.textContent = formatNumber(output.required_units || 0) + ' Unit';
        if (heroCardPrice) heroCardPrice.textContent = formatCurrency(input.selling_price || 0);
        if (heroCardTitle) heroCardTitle.textContent = input.business_model || 'Model Bisnis';

        // Sync mascot with the card content
        updateMascot();

        let probability = 40, probColor = 'bg-rose-500';
        if (scores.status_color === 'green') { probability = 95; probColor = 'bg-emerald-500'; }
        if (scores.status_color === 'yellow') { probability = 75; probColor = 'bg-amber-500'; }

        if (heroCardBar) {
            heroCardBar.style.width = probability + '%';
            heroCardBar.className = `h-full ${probColor} rounded-full animate-pulse transition-all duration-1000`;
        }
        if (heroCardText) {
            heroCardText.textContent = probability + '% Success Rate';
            heroCardText.className = (scores.status_color === 'green' ? 'text-emerald-500' : (scores.status_color === 'yellow' ? 'text-amber-500' : 'text-rose-500')) + ' font-bold text-[10px] md:text-xs';
        }

        // Status Card — FULL CLASSNAME RESET to prevent stale color classes
        const statusCard = document.getElementById('rp-status-card');
        const statusIcon = document.getElementById('rp-status-icon');

        updateText('rp-goal-status', scores.goal_status);
        updateText('rp-constraint-msg', scores.constraint_message);

        if (statusCard) {
            // Base classes that are always present
            const baseClasses = 'mb-2 sm:mb-4 p-3 sm:p-4 rounded-lg sm:rounded-xl border-l-4 transition-all duration-300 shadow-sm';

            let colorClasses = '';
            let iconClass = '';
            if (scores.status_color === 'green') {
                colorClasses = 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/10 shadow-emerald-200/50 dark:shadow-none';
                iconClass = 'fas fa-check-circle text-emerald-500';
            } else if (scores.status_color === 'yellow') {
                colorClasses = 'border-amber-500 bg-amber-50 dark:bg-amber-900/10 shadow-amber-200/50 dark:shadow-none';
                iconClass = 'fas fa-exclamation-circle text-amber-500';
            } else {
                colorClasses = 'border-rose-500 bg-rose-50 dark:bg-rose-900/10 shadow-rose-200/50 dark:shadow-none';
                iconClass = 'fas fa-times-circle text-rose-500';
            }

            statusCard.className = baseClasses + ' ' + colorClasses;
            if (statusIcon) statusIcon.innerHTML = `<i class="${iconClass}"></i>`;
        }

        // 🤖 Insight / Learning Moment (Auto-Interpreter for Cold Users)
        const learningEl = document.getElementById('rp-learning-moment');
        if (learningEl) {
            let insightHtml = scores.learning_moment || '';

            // Generate synthetic insight if backend didn't provide one (e.g. Guest Mode fast compute)
            if (!insightHtml || insightHtml.includes('akan muncul otomatis')) {
                const margin = window.rgpState.margin || 0;

                if (scores.status_color === 'green') {
                    insightHtml = `<b>🔥 Skenario Solid!</b> Dengan margin <b>${margin}%</b>, target kamu sangat masuk akal. Bisnis punya ruang napas yang cukup untuk bertumbuh tanpa harus perang harga berdarah-darah.`;
                } else if (scores.status_color === 'yellow') {
                    if (margin < 30) {
                        insightHtml = `<b>⚠️ Awas Boncos Terselubung!</b> Margin <b>${margin}%</b> terlalu tipis. Kamu butuh volume jualan ekstra tinggi hanya untuk nutup operasional. Coba naikkan harga jual atau cari produk dengan margin > 40%.`;
                    } else {
                        insightHtml = `<b>⚖️ Skala Menengah.</b> Upayamu butuh konsistensi tinggi. Pastikan angka <i>Break Even Point</i> (BEP) bisa tersentuh di pertengahan bulan agar sisa waktu murni jadi profit.`;
                    }
                } else { // red
                    insightHtml = `<b>🚨 Skala Berbahaya!</b> Hitungan ini berpotensi membakar modalmu habis sebelum balik modal. Target terlalu jauh dari kemampuan kapital/waktumu saat ini. Turunkan ekspektasi atau cari produk *High-Ticket*!`;
                }
            }

            learningEl.innerHTML = insightHtml;
        }

        // Recommendations
        const recBox = document.getElementById('rp-recommendations-box');
        const recContainer = document.getElementById('rp-rec-container');
        if (recContainer) recContainer.innerHTML = '';

        if (scores.recommendations && scores.recommendations.length > 0 && recBox && recContainer) {
            recBox.classList.remove('hidden');
            scores.recommendations.forEach(rec => {
                const btn = document.createElement('button');
                btn.className = 'w-full text-left p-3 rounded-xl border transition-all hover:shadow-md bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-emerald-400 group';
                btn.innerHTML = `
                    <p class="text-xs font-bold uppercase text-emerald-600 mb-1">Solusi: ${rec.type?.toUpperCase() ?? ''}</p>
                    <p class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-emerald-600">${rec.label ?? ''}</p>
                    ${(rec.desc || rec.description) ? `<p class="text-xs text-slate-500 mt-1">${rec.desc ?? rec.description}</p>` : ''}
                `;
                btn.onclick = (e) => {
                    e.preventDefault();
                    applyRecommendation(rec.type, rec.value);
                };
                recContainer.appendChild(btn);
            });
        } else if (recBox) {
            recBox.classList.add('hidden');
        }

        // Why modal data
        const totalCapitalNeeded = output.total_capital_needed || output.total_ad_spend || 0;
        const capCover = totalCapitalNeeded > 0 ? (window.rgpState.capital / totalCapitalNeeded) * 100 : 100;
        const execCover = output.daily_hours_needed > 0 ? (window.rgpState.hours / output.daily_hours_needed) * 100 : 100;
        updateText('rp-why-capital', capCover >= 100 ? 'Cukup' : `Kurang (Cover ${Math.round(capCover)}%)`);
        updateText('rp-why-hours', execCover >= 100 ? 'Cukup' : `Overload (Cover ${Math.round(execCover)}%)`);
        updateText('rp-why-margin', (input.assumed_margin || 0) + '%');

        // ─── V3 Detail Keuangan Panel ───────────────────────────────────────────
        const totalCosts = (output.total_ad_spend || 0) + (output.fixed_costs || 0) + (output.stock_capital || 0);

        updateText('rp-unit-profit', output.unit_profit ? formatCurrency(output.unit_profit) : '--');
        updateText('rp-cpa', output.cpa ? formatCurrency(output.cpa) : 'Rp 0 (Organik)');
        updateText('rp-gross-revenue', output.gross_revenue ? formatCurrency(output.gross_revenue) : '--');
        updateText('rp-total-costs', totalCosts > 0 ? formatCurrency(totalCosts) : '--');
        updateText('rp-net-profit', output.net_profit ? formatCurrency(output.net_profit) : '--');
        updateText('rp-fixed-display', output.fixed_costs > 0 ? formatCurrency(output.fixed_costs) : 'Rp 0');

        // Capital needed with color indicator
        const capitalNeededEl = document.getElementById('rp-capital-needed');
        if (capitalNeededEl) {
            const gap = output.capital_gap || 0;
            const colorClass = gap <= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500';
            capitalNeededEl.className = `text-[10px] sm:text-xs font-black tabular-nums ${colorClass}`;
            capitalNeededEl.textContent = totalCapitalNeeded > 0 ? formatCurrency(totalCapitalNeeded) : '--';
        }

        // ─── 🚀 DYNAMIC UPSELL INJECTION (Beli 1x Jual Berkali-kali) ───────────
        const dynamicUpsell = document.getElementById('dynamic-upsell-text');
        const staticUpsell = document.getElementById('static-upsell-text');
        if (dynamicUpsell && staticUpsell && output.net_profit > 0) {
            // Asumsi harga 1 lisensi produk digital adalah Rp 150.000
            const productPrice = 150000;
            const unitsNeeded = Math.ceil(output.net_profit / productPrice);

            const targetEl = document.getElementById('rp-upsell-target');
            const unitEl = document.getElementById('rp-upsell-unit');

            if (targetEl && unitEl) {
                targetEl.textContent = formatCurrency(output.net_profit);
                unitEl.textContent = unitsNeeded.toLocaleString('id-ID');

                // Swap visibility
                staticUpsell.classList.replace('block', 'hidden');
                dynamicUpsell.classList.replace('hidden', 'block');
            }
        }

        // Auto-open detail panel when results render
        const detailPanel = document.getElementById('rgp-detail-panel');
        if (detailPanel && detailPanel.classList.contains('hidden')) {
            detailPanel.classList.remove('hidden');
            const detailIcon = document.getElementById('rgp-detail-icon');
            if (detailIcon) detailIcon.style.transform = 'rotate(180deg)';
        }

        // Simulator gate
        const isLocked = (scores.status_color === 'red');
        window.dispatchEvent(new CustomEvent('reverse-goal-planner:update', {
            detail: { baseline: window.manualBaseline, isLocked, statusMsg: scores.constraint_message }
        }));
    }

    // ─── Expose renderResults for external re-render (e.g. currency change) ─────
    window._rgpRenderResults = renderResults;

    // ─── Apply Recommendation ───────────────────────────────────────────────────

    function applyRecommendation(type, value) {
        if (type === 'timeline') {
            window.rgpState.timeline = Number(value);
            if (el.timelineSlider) el.timelineSlider.value = value;
            if (el.timelineDisplay) el.timelineDisplay.textContent = value;
        } else if (type === 'target') {
            window.rgpState.target_profit = Number(value);
            if (el.targetInput) el.targetInput.value = value;
        } else if (type === 'hours') {
            window.rgpState.hours = Number(value);
            if (el.hoursSlider) el.hoursSlider.value = value;
            if (el.hoursDisplay) el.hoursDisplay.textContent = value;
        }
        if (isCoreReady()) autoCalculate();
    }

    // ─── "Why?" Modal ───────────────────────────────────────────────────────────

    const whyBtn = document.getElementById('rp-why-btn');
    const whyModal = document.getElementById('rp-why-modal');
    const whyClose = document.getElementById('rp-why-close');

    if (whyBtn) {
        whyBtn.onclick = () => whyModal.classList.remove('hidden');
        whyClose.onclick = () => whyModal.classList.add('hidden');
    }

    // ─── Helper ─────────────────────────────────────────────────────────────────

    function updateText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function renderInfeasibleState(data) {
        const input = data.input;
        const scores = data.scores;

        // Version
        const versionEl = document.getElementById('rp-logic-version');
        if (versionEl) versionEl.textContent = data.logic_version;

        // Update Hero Title & Mascot (Visual Sync)
        const heroCardTitle = document.getElementById('hero-card-title');
        if (heroCardTitle) heroCardTitle.textContent = input.business_model || 'Model Bisnis';
        updateMascot();

        // Hero metrics -> Invalid
        updateText('hero-card-target', formatCurrency(input.target_profit));
        updateText('hero-card-traffic', 'Mustahil');
        updateText('hero-card-sales', 'Mustahil'); // Unit
        updateText('hero-card-conv', (input.assumed_conversion || 0) + '%');

        // Progress Bar -> 0% Red
        const heroCardBar = document.getElementById('hero-card-progress-bar');
        const heroCardText = document.getElementById('hero-card-progress-text');
        if (heroCardBar) { heroCardBar.style.width = '100%'; heroCardBar.className = 'h-full bg-rose-600 rounded-full'; }
        if (heroCardText) { heroCardText.textContent = '0% Success Rate'; heroCardText.className = 'text-rose-600 font-bold text-xs'; }

        // Status Card -> Red
        const statusCard = document.getElementById('rp-status-card');
        const statusIcon = document.getElementById('rp-status-icon');
        updateText('rp-goal-status', 'MODEL TIDAK LAYAK');
        updateText('rp-constraint-msg', scores.constraint_message);

        if (statusCard) {
            statusCard.classList.remove('border-emerald-500', 'border-amber-500', 'border-rose-500', 'bg-emerald-50', 'bg-amber-50', 'bg-rose-50', 'dark:bg-emerald-900/10', 'dark:bg-amber-900/10', 'dark:bg-rose-900/10');
            statusCard.classList.add('border-rose-600', 'bg-rose-100', 'dark:bg-rose-900/30');
            if (statusIcon) statusIcon.innerHTML = `<i class="fas fa-times-circle text-rose-600 text-2xl"></i>`;
        }

        // Hide recommendations, show error with breakdown
        const recContainer = document.getElementById('rp-rec-container');

        if (recContainer) {
            const debug = data.output.debug || {};
            const breakdown = debug.breakdown || {};

            let breakdownHtml = '<ul class="mt-3 space-y-1 text-xs text-left bg-white/60 dark:bg-black/20 p-3 rounded-lg border border-rose-100 dark:border-rose-900/50">';
            // Manually order for clarity if needed, or iterate
            if (Object.keys(breakdown).length > 0) {
                for (const [key, val] of Object.entries(breakdown)) {
                    const isNegative = key.includes('Rugi') || key.includes('Biaya');
                    const color = key.includes('Rugi') ? 'text-rose-600 font-bold' : (key.includes('Biaya') ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300');
                    breakdownHtml += `<li class="flex justify-between items-center ${color}">
                        <span>${key}</span> 
                        <span class="font-mono">${val}</span>
                    </li>`;
                }
            } else {
                breakdownHtml += '<li class="text-slate-500">Data detail tidak tersedia.</li>';
            }
            breakdownHtml += '</ul>';

            recContainer.innerHTML = `
                <div class="p-5 border border-rose-200 bg-rose-50 dark:bg-rose-900/20 rounded-xl shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-bug text-rose-600 text-lg"></i>
                        <h4 class="font-bold text-rose-700 dark:text-rose-400 text-sm">Diagnosis: Boncos!</h4>
                    </div>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mb-2 leading-relaxed">
                        ${data.scores.constraint_message}
                    </p>
                    
                    ${breakdownHtml}

                    <div class="mt-4 flex gap-2 items-start text-xs text-slate-500 bg-rose-100/50 dark:bg-rose-900/30 p-2 rounded">
                        <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                        <span><strong>Saran:</strong> Margin profit Anda terlalu tipis untuk main iklan berbayar (Ads). Coba naikkan harga, cari produk profit >50rb, atau gunakan strategi organik.</span>
                    </div>
                </div>
            `;
        }
    }

    // Init Mascot to default state
    updateMascot();

    // Mascot Click Interaction
    const mascotContainer = document.getElementById('mascot-container');
    const mascotBubble = document.getElementById('mascot-bubble');

    if (mascotContainer && mascotBubble) {
        mascotContainer.addEventListener('click', (e) => {
            e.stopPropagation(); // prevent bubbling if any
            mascotBubble.classList.toggle('opacity-0');
            mascotBubble.classList.toggle('pointer-events-none');
            mascotBubble.classList.toggle('scale-90');
        });

        // Optional: Close bubble when clicking elsewhere
        document.addEventListener('click', (e) => {
            if (!mascotContainer.contains(e.target)) {
                mascotBubble.classList.add('opacity-0', 'pointer-events-none', 'scale-90');
            }
        });
    }

});
