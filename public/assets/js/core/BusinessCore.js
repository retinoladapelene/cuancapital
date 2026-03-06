/**
 * @file BusinessCore.js
 * @description Central Logic for Cashflow Engine.
 * Handles state, calculations, and API persistence.
 */

import { api } from '../services/api.js';
import { showToast } from '../utils/helpers.js';

class BusinessCore {
    constructor() {
        if (BusinessCore.instance) {
            return BusinessCore.instance;
        }
        BusinessCore.instance = this;

        this.state = {
            // Core Financials
            sellingPrice: 0,
            variableCosts: 0,
            fixedCosts: 0,

            // Marketing & Traffic
            traffic: 0,
            conversionRate: 0,
            adSpend: 0,

            // Goals
            targetRevenue: 0,

            // Capacity (for reality checks)
            availableCash: 0,
            maxCapacity: 1000, // Manual Override Capacity

            // Full Metrics (calculated)
            fullMetrics: null,
            businessName: "My Business",
            currency: "IDR"
        };

        // Observers for reactive UI
        this.observers = [];
        this.autoSaveTimer = null;
        this.isLoading = false;

        console.log("🧠 BusinessCore Initialized (API Mode)");
    }

    // ==========================================
    // INITIALIZATION & STATE MANAGEMENT
    // ==========================================

    async init() {
        // Load data from API
        await this.loadFromApi();
        this.notifyObservers();
    }

    subscribe(callback) {
        this.observers.push(callback);
    }

    notifyObservers() {
        const fullState = this.calculateFullBusinessState();
        this.observers.forEach(callback => callback(fullState));
    }

    updateState(updates, autoSave = true) {
        this.state = { ...this.state, ...updates };
        this.notifyObservers();

        if (autoSave) {
            this.triggerAutoSave();
        }
    }

    /**
     * Updates goal parameters (backwards compatibility for calculator.js)
     * @param {Object} params - { targetRevenue, targetPrice }
     */
    updateGoalParams(params) {
        const updates = {};
        if (params.targetRevenue !== undefined) updates.targetRevenue = params.targetRevenue;

        // Map targetPrice to sellingPrice if provided (Calculator "Goal Price" drives Selling Price)
        if (params.targetPrice !== undefined && params.targetPrice > 0) {
            updates.sellingPrice = params.targetPrice;
        }

        this.updateState(updates);
    }

    // ==========================================
    // PERSISTENCE (API)
    // ==========================================

    async loadFromApi() {
        if (!localStorage.getItem('auth_token')) return; // Skip if guest

        this.isLoading = true;
        try {
            const data = await api.get('/business', { useApiPrefix: true });
            if (data) {
                // Map API response (snake_case) to State (camelCase)
                this.state = {
                    ...this.state,
                    sellingPrice: Number(data.selling_price || 0),
                    variableCosts: Number(data.variable_costs || 0),
                    fixedCosts: Number(data.fixed_costs || 0),
                    traffic: Number(data.traffic || 0),
                    conversionRate: Number(data.conversion_rate || 0),
                    adSpend: Number(data.ad_spend || 0),
                    targetRevenue: Number(data.target_revenue || 0),
                    availableCash: Number(data.available_cash || 0),
                    maxCapacity: Number(data.max_capacity || 1000),
                    businessName: data.business_name || "My Business",
                    currency: data.currency || "IDR"
                };
                console.log("☁️ Business Data Loaded from API");
            }
        } catch (error) {
            console.error("Failed to load business data:", error);
            // Non-critical, maybe offline or new user
        } finally {
            this.isLoading = false;
        }
    }

    triggerAutoSave() {
        clearTimeout(this.autoSaveTimer);
        this.autoSaveTimer = setTimeout(() => {
            this.saveToApi();
        }, 2000); // Debounce 2s
    }

    async saveToApi() {
        if (!localStorage.getItem('auth_token')) return; // Skip if guest

        try {
            const parseCurrency = (val) => typeof val === 'string' ? Number(val.replace(/\D/g, '')) : Number(val);

            // Map State (camelCase) to API Payload (snake_case)
            const payload = {
                selling_price: parseCurrency(this.state.sellingPrice),
                variable_costs: parseCurrency(this.state.variableCosts),
                fixed_costs: parseCurrency(this.state.fixedCosts),
                traffic: Number(this.state.traffic),
                conversion_rate: Number(this.state.conversionRate),
                ad_spend: parseCurrency(this.state.adSpend),
                target_revenue: parseCurrency(this.state.targetRevenue),
                available_cash: parseCurrency(this.state.availableCash),
                max_capacity: Number(this.state.maxCapacity),
                business_name: this.state.businessName,
                currency: this.state.currency
            };

            await api.post('/business', payload, { useApiPrefix: true });

            this.hasUnsavedChanges = false;
            console.log("Auto-saved to API");
        } catch (error) {
            if (error.status === 503) {
                console.warn("⚠️ Server busy (503), retrying auto-save in 5s...");
                clearTimeout(this.autoSaveTimer);
                this.autoSaveTimer = setTimeout(() => this.saveToApi(), 5000);
                return;
            }
            console.error("Auto-save failed:", error);
            // showToast("Gagal menyimpan data otomatis", "error"); // Optional
        }
    }

    // ==========================================
    // CORE CALCULATIONS (Business Logic)
    // ==========================================

    calculateFullBusinessState() {
        const s = this.state;

        // === UNIT ECONOMICS ===
        const profitPerUnit = s.sellingPrice - s.variableCosts;
        const marginPercentage = s.sellingPrice > 0 ? (profitPerUnit / s.sellingPrice) * 100 : 0;

        // === CURRENT STATE METRICS ===
        const currentSalesQty = Math.floor(s.traffic * (s.conversionRate / 100));
        const monthlyRevenue = currentSalesQty * s.sellingPrice;
        const monthlyCosts = (currentSalesQty * s.variableCosts) + s.fixedCosts + s.adSpend;
        const monthlyProfit = monthlyRevenue - monthlyCosts;
        const annualProfit = monthlyProfit * 12;

        // === SURVIVAL METRICS (BREAK-EVEN POINT) ===
        let bepUnits = Infinity;
        let bepRevenue = Infinity;
        let dailySalesTarget = Infinity;
        let burnRate = s.fixedCosts + s.adSpend;
        let cashRunway = 0;

        if (profitPerUnit > 0) {
            bepUnits = Math.ceil(s.fixedCosts / profitPerUnit);
            bepRevenue = bepUnits * s.sellingPrice;
            dailySalesTarget = Math.ceil(bepUnits / 30);

            if (s.availableCash > 0) {
                const monthlyBurn = burnRate - (currentSalesQty * profitPerUnit);
                cashRunway = monthlyBurn > 0 ? Math.max(0, s.availableCash / monthlyBurn) : 999;
            }
        }

        // === GOAL FEASIBILITY ===
        const requiredSalesQty = s.targetRevenue > 0 && s.sellingPrice > 0
            ? Math.ceil(s.targetRevenue / s.sellingPrice)
            : 0;

        const requiredTraffic = requiredSalesQty > 0 && s.conversionRate > 0
            ? Math.ceil(requiredSalesQty / (s.conversionRate / 100))
            : 0;

        const requiredDailySales = Math.ceil(requiredSalesQty / 30);
        const goalMargin = s.targetRevenue - monthlyRevenue; // Gap to close

        // === CAPACITY GAP ===
        const gapCapacity = s.maxCapacity - requiredSalesQty;

        // === REALITY CHECK VALIDATION ===
        const realityCheck = this.validateCrossFeatureReality({
            profitPerUnit,
            requiredTraffic,
            requiredDailySales,
            currentSalesQty,
            bepUnits,
            cashRunway,
            requiredSalesQty, // Pass this needed for capacity check
            gapCapacity // Pass calculated gap
        });

        return {
            ...s,
            metrics: {
                profitPerUnit,
                marginPercentage,
                currentSalesQty,
                monthlyRevenue,
                monthlyCosts,
                monthlyProfit,
                annualProfit,

                // Goals
                unitsNeededForGoal: requiredSalesQty,
                revenueNeeded: s.targetRevenue,
                trafficNeededForGoal: requiredTraffic,
                requiredDailySales,
                goalMargin,

                // BEP
                bepUnits,
                bepRevenue,
                dailySalesTarget,
                burnRate,
                cashRunway: parseFloat(cashRunway.toFixed(1)),

                // Health Check
                isProfitable: monthlyProfit > 0,
                isGoalReachable: requiredSalesQty <= s.maxCapacity,

                // Capacity Metrics
                maxCapacity: s.maxCapacity,
                gapCapacity,

                // Reality Check
                isRealistic: realityCheck.isRealistic,
                warnings: realityCheck.warnings,
                recommendations: realityCheck.recommendations,

                // Timestamp
                calculatedAt: new Date().toISOString()
            }
        };
    }

    validateCrossFeatureReality(metrics) {
        const s = this.state;
        const warnings = [];
        const recommendations = [];
        let isRealistic = true;

        if (metrics.profitPerUnit <= 0) {
            isRealistic = false;
            warnings.push("❌ MARGIN NEGATIF: Harga jual lebih rendah dari biaya variabel!");
            recommendations.push("Naikkan harga minimum ke Rp " + (s.variableCosts * 1.5).toLocaleString('id-ID'));
        }

        const trafficMultiplier = s.traffic > 0 ? metrics.requiredTraffic / s.traffic : 0;
        if (trafficMultiplier > 10 && s.adSpend < 1000000) {
            isRealistic = false;
            warnings.push(`⚠️ TRAFFIC TIDAK REALISTIS: Butuh ${trafficMultiplier.toFixed(1)}x lipat traffic saat ini!`);
            recommendations.push(`Tingkatkan budget iklan atau turunkan target revenue`);
        }

        if (metrics.gapCapacity < 0) {
            isRealistic = false;
            const shortage = Math.abs(metrics.gapCapacity);
            warnings.push(`🚫 KAPASITAS JEBOL: Kurang ${shortage} unit!`);
            recommendations.push(`Upgrade kapasitas produksi.`);
        }

        if (metrics.cashRunway < 6 && metrics.cashRunway > 0) {
            warnings.push(`⏰ RUNWAY KRITIS: Hanya ${metrics.cashRunway.toFixed(1)} bulan tersisa!`);
            recommendations.push(`Cari funding atau pangkas fixed cost.`);
        }

        return { isRealistic, warnings, recommendations };
    }
}

// Singleton Instance
export const businessCore = new BusinessCore();
