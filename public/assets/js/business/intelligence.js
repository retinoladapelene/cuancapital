/**
 * Business Manager — intelligence.js
 * Smart Profit Analyzer, Cashflow Prediction, Business Health Score
 * Zero AI, offline-ready, cached in localStorage
 */

const BIZ_CACHE_TTL = {
    healthScore: 24 * 60 * 60 * 1000,   // 1 day
    cashForecast: 60 * 60 * 1000,         // 1 hour
    profitAnalyzer: 30 * 60 * 1000,         // 30 min
};

function _iCacheGet(key) {
    try {
        const item = JSON.parse(localStorage.getItem('bizai_' + key) || 'null');
        if (!item || Date.now() > item.expires) return null;
        return item.data;
    } catch { return null; }
}
function _iCacheSet(key, data, ttl) {
    try { localStorage.setItem('bizai_' + key, JSON.stringify({ data, expires: Date.now() + ttl })); } catch { }
}

// ── 1. Business Health Score ─────────────────────────────────────────────────
function bizHealthScore(snapshots, businessId) {
    const cached = _iCacheGet('health_' + businessId);
    if (cached) return cached;

    const now = new Date();
    const thisM = now.toISOString().slice(0, 7);
    const prevM = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().slice(0, 7);

    const filt = (mk) => snapshots.filter(s => s.business_id === businessId && s.snapshot_date?.startsWith(mk));
    const sum = (snaps, field) => snaps.reduce((a, s) => a + (s[field] || 0), 0);

    const currRevenue = sum(filt(thisM), 'revenue');
    const prevRevenue = sum(filt(prevM), 'revenue');
    const currProfit = sum(filt(thisM), 'profit');
    const currOrders = sum(filt(thisM), 'orders_count');

    // A. Revenue Growth Score (40%)
    let growthScore = 60;
    if (prevRevenue > 0) {
        const growth = (currRevenue - prevRevenue) / prevRevenue * 100;
        growthScore = growth > 20 ? 90 : growth > 10 ? 75 : growth > 0 ? 60 : growth > -10 ? 45 : 30;
    } else if (currRevenue > 0) {
        growthScore = 70; // first month with revenue
    }

    // B. Profit Margin Score (40%)
    let marginScore = 50;
    if (currRevenue > 0) {
        const margin = (currProfit / currRevenue) * 100;
        marginScore = margin > 30 ? 90 : margin > 20 ? 75 : margin > 10 ? 60 : margin > 0 ? 40 : 20;
    }

    // C. Activity Score (20%) — based on orders
    const actScore = currOrders > 30 ? 90 : currOrders > 10 ? 75 : currOrders > 5 ? 60 : currOrders > 0 ? 45 : 30;

    const score = Math.round((growthScore * 0.4) + (marginScore * 0.4) + (actScore * 0.2));
    const status = score >= 80 ? 'Excellent' : score >= 60 ? 'Healthy' : score >= 40 ? 'Perlu Perhatian' : 'Kritis';
    const color = score >= 80 ? 'var(--biz-success)' : score >= 60 ? 'var(--biz-primary)' : score >= 40 ? 'var(--biz-warning)' : 'var(--biz-danger)';

    const result = { score, status, color, growthScore, marginScore, actScore };
    _iCacheSet('health_' + businessId, result, BIZ_CACHE_TTL.healthScore);
    return result;
}
window.bizHealthScore = bizHealthScore;

// ── 2. Cashflow Prediction ────────────────────────────────────────────────────
function bizCashForecast(snapshots, businessId) {
    const cacheKey = 'forecast_' + (businessId || 'all');
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    // Use last 30 days of snapshots
    const cutoff = new Date();
    cutoff.setDate(cutoff.getDate() - 30);
    const last30 = (snapshots || [])
        .filter(s => s.snapshot_date && new Date(s.snapshot_date) >= cutoff)
        .sort((a, b) => a.snapshot_date.localeCompare(b.snapshot_date));

    if (last30.length < 3) {
        const result = { netDaily: 0, trend: 'stable', runway: null, avgDailyIncome: 0, avgDailyExpense: 0 };
        _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.cashForecast);
        return result;
    }

    const avg = (arr) => arr.reduce((a, b) => a + b, 0) / arr.length;
    const avgDailyIncome = avg(last30.map(s => s.revenue || 0));
    const avgDailyExpense = avg(last30.map(s => s.expenses || 0));
    const netDaily = avgDailyIncome - avgDailyExpense;

    // Trend direction (compare first half vs second half)
    const half = Math.floor(last30.length / 2);
    const firstH = avg(last30.slice(0, half).map(s => s.revenue || 0));
    const secondH = avg(last30.slice(half).map(s => s.revenue || 0));
    const trend = secondH > firstH * 1.05 ? 'growing' : secondH < firstH * 0.95 ? 'declining' : 'stable';

    // 14-day cashflow projection (for potential sparkline)
    const totalIncome = last30.reduce((a, s) => a + (s.revenue || 0), 0);
    const totalExpense = last30.reduce((a, s) => a + (s.expenses || 0), 0);
    const runway = netDaily < 0 && totalIncome > 0
        ? Math.floor(totalIncome / Math.abs(netDaily))
        : null;

    const result = { netDaily, trend, runway, avgDailyIncome, avgDailyExpense };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.cashForecast);
    return result;
}
window.bizCashForecast = bizCashForecast;

// ── 3. Smart Profit Analyzer ─────────────────────────────────────────────────
async function bizProfitAnalyzer(businessId, monthKey) {
    const cacheKey = `profit_${businessId}_${monthKey}`;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const saleItems = await BizDB.saleItems.getAll();
    const sales = await BizDB.sales.getAll();

    // Build set of sale IDs in this month
    const monthSaleIds = new Set(
        sales.filter(s => s.business_id === businessId && s.sale_date?.startsWith(monthKey)).map(s => s.id)
    );
    const monthItems = saleItems.filter(si => monthSaleIds.has(si.sale_id));

    const products = {};
    monthItems.forEach(si => {
        if (!products[si.product_id]) {
            products[si.product_id] = { id: si.product_id, name: si.product_name, qty: 0, revenue: 0, profit: 0 };
        }
        products[si.product_id].qty += si.quantity;
        products[si.product_id].revenue += si.subtotal;
        products[si.product_id].profit += si.profit;
    });

    const sorted = Object.values(products).sort((a, b) => b.profit - a.profit);
    const totalProfit = sorted.reduce((s, p) => s + p.profit, 0);

    // Classify tiers
    sorted.forEach(p => {
        const share = totalProfit > 0 ? (p.profit / totalProfit * 100) : 0;
        const margin = p.revenue > 0 ? (p.profit / p.revenue * 100) : 0;
        p.share = share;
        p.margin = margin;
        p.tier = margin >= 25 ? 'star' : margin >= 15 ? 'good' : margin >= 5 ? 'warning' : 'danger';
    });

    // Generate insight message
    const insights = [];
    if (sorted.length > 0) {
        const top = sorted[0];
        insights.push(`${top.name} menyumbang ${top.share.toFixed(0)}% total profit bulan ini.`);
        if (top.share > 40) insights.push(`Pertimbangkan tingkatkan produksi ${top.name}.`);
        const worst = sorted[sorted.length - 1];
        if (worst.margin < 10 && worst.id !== top.id)
            insights.push(`${worst.name} margin sangat tipis (${worst.margin.toFixed(1)}%). Evaluasi harga atau HPP.`);
    }

    const result = { products: sorted, totalProfit, insights };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.profitAnalyzer);
    return result;
}
window.bizProfitAnalyzer = bizProfitAnalyzer;

// ── Health Score insight text ─────────────────────────────────────────────────
function bizHealthInsight(snapshots, businessId) {
    const { score, status, growthScore, marginScore } = bizHealthScore(snapshots, businessId);
    const forecast = bizCashForecast(snapshots, businessId);

    const lines = [];
    if (growthScore >= 75) lines.push('Revenue tumbuh dengan baik. 🚀');
    else if (growthScore <= 45) lines.push('Revenue menurun — perlu strategi penjualan baru.');

    if (marginScore >= 75) lines.push('Profit margin sehat.');
    else if (marginScore <= 40) lines.push('Margin tipis — evaluasi HPP atau harga jual.');

    if (forecast.runway !== null && forecast.runway < 30)
        lines.push(`⚠️ Estimasi kas tahan ${forecast.runway} hari — perhatikan pengeluaran.`);
    else if (forecast.netDaily > 0)
        lines.push('Cashflow positif. 💧');

    return { score, status, lines };
}
window.bizHealthInsight = bizHealthInsight;

// ── 5. Stock Burn Rate & Inventory Health ────────────────────────────────────
async function bizInventoryHealth(businessId) {
    const cacheKey = 'inv_health_' + businessId;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const [products, saleItems, sales] = await Promise.all([
        BizDB.products.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.sales.getAll()
    ]);

    const physical = products.filter(p => p.type === 'physical' && p.is_active !== false && p.business_id === businessId);

    // Calculate avg sales per day over last 14 days
    const cutoff = new Date();
    cutoff.setDate(cutoff.getDate() - 14);
    const recentSales = new Set(
        sales.filter(s => s.business_id === businessId && new Date(s.sale_date) >= cutoff).map(s => s.id)
    );
    const recentItems = saleItems.filter(si => recentSales.has(si.sale_id));

    const dailySalesMap = {};
    recentItems.forEach(si => {
        dailySalesMap[si.product_id] = (dailySalesMap[si.product_id] || 0) + si.quantity;
    });

    const burnRates = [];
    let lowStockCount = 0;
    let outStockCount = 0;
    let overStockCount = 0;

    physical.forEach(p => {
        const soldIn14 = dailySalesMap[p.id] || 0;
        const avgDaily = soldIn14 / 14;
        const daysLeft = avgDaily > 0 ? Math.floor(p.stock / avgDaily) : 999;

        if (p.stock <= 0) outStockCount++;
        else if (p.stock <= (p.low_stock_alert || 5)) lowStockCount++;
        else if (daysLeft > 90 && p.stock > 20) overStockCount++; // Overstock (3 months runway)

        burnRates.push({
            id: p.id,
            name: p.name,
            stock: p.stock,
            avgDaily: Number(avgDaily.toFixed(1)),
            daysLeft: daysLeft > 900 ? '90+' : daysLeft
        });
    });

    burnRates.sort((a, b) => a.daysLeft === '90+' ? 1 : b.daysLeft === '90+' ? -1 : a.daysLeft - b.daysLeft);

    let score = 100;
    score -= (outStockCount * 10);
    score -= (lowStockCount * 5);
    score -= (overStockCount * 3);
    score = Math.max(0, Math.min(100, score));

    const result = { score, outStockCount, lowStockCount, overStockCount, burnRates };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.healthScore);
    return result;
}
window.bizInventoryHealth = bizInventoryHealth;

// ── 6. Best Sales Time Detector ─────────────────────────────────────────────
async function bizBestSalesTime(businessId) {
    const cacheKey = 'best_time_' + businessId;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const sales = await BizDB.sales.getAll();
    const mySales = sales.filter(s => s.business_id === businessId);

    const hours = new Array(24).fill(0);
    mySales.forEach(s => {
        if (!s.created_at) return;
        const date = new Date(s.created_at);
        if (!isNaN(date.getHours())) {
            hours[date.getHours()]++;
        }
    });

    let peakHour = 0;
    let maxSales = 0;
    hours.forEach((count, h) => {
        if (count > maxSales) { maxSales = count; peakHour = h; }
    });

    const formatHour = (h) => `${h.toString().padStart(2, '0')}:00`;
    const result = { peakHour, timeLabel: `${formatHour(peakHour)} — ${formatHour(peakHour + 1)}`, maxSales };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.healthScore);
    return result;
}
window.bizBestSalesTime = bizBestSalesTime;

// ── 7. Expense Leak Detector ────────────────────────────────────────────────
async function bizExpenseLeakDetector(businessId) {
    const cacheKey = 'expense_leak_' + businessId;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const expenses = await BizDB.expenses.getAll();
    const myExp = expenses.filter(e => e.business_id === businessId && e.expense_date);

    const now = new Date();
    const last7 = new Date(now); last7.setDate(last7.getDate() - 7);
    const prev7 = new Date(now); prev7.setDate(prev7.getDate() - 14);

    const sumMap = (arr) => {
        const map = {};
        arr.forEach(e => map[e.category] = (map[e.category] || 0) + e.amount);
        return map;
    };

    const thisWeek = sumMap(myExp.filter(e => new Date(e.expense_date) >= last7));
    const lastWeek = sumMap(myExp.filter(e => {
        const d = new Date(e.expense_date);
        return d >= prev7 && d < last7;
    }));

    const leaks = [];
    Object.keys(thisWeek).forEach(cat => {
        const curr = thisWeek[cat];
        const prev = lastWeek[cat] || 0;
        if (prev > 0) {
            const jump = ((curr - prev) / prev) * 100;
            if (jump > 20) leaks.push({ category: cat, jumpPct: jump, curr, prev });
        } else if (curr > 50000) {
            leaks.push({ category: cat, jumpPct: 100, curr, prev: 0 });
        }
    });

    leaks.sort((a, b) => b.jumpPct - a.jumpPct);
    const result = { leaks };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.cashForecast);
    return result;
}
window.bizExpenseLeakDetector = bizExpenseLeakDetector;

// ── 8. Product Momentum ─────────────────────────────────────────────────────
async function bizProductMomentum(businessId) {
    const cacheKey = 'momentum_' + businessId;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const [sales, saleItems] = await Promise.all([
        BizDB.sales.getAll(),
        BizDB.saleItems.getAll()
    ]);

    const now = new Date();
    const last7 = new Date(now); last7.setDate(last7.getDate() - 7);
    const prev7 = new Date(now); prev7.setDate(prev7.getDate() - 14);

    const getSalesData = (start, end) => {
        const sIds = new Set(sales.filter(s => {
            if (s.business_id !== businessId) return false;
            const d = new Date(s.sale_date);
            return d >= start && d < end;
        }).map(s => s.id));

        const map = {};
        saleItems.filter(si => sIds.has(si.sale_id)).forEach(si => {
            if (!map[si.product_name]) map[si.product_name] = { qty: 0, id: si.product_id };
            map[si.product_name].qty += si.quantity;
        });
        return map;
    };

    const thisWeek = getSalesData(last7, now);
    const lastWeek = getSalesData(prev7, last7);

    const trending = [];
    Object.keys(thisWeek).forEach(name => {
        const w1 = thisWeek[name].qty;
        const w2 = lastWeek[name]?.qty || 0;
        if (w2 > 0) {
            const growth = ((w1 - w2) / w2) * 100;
            if (growth > 15) trending.push({ name, growth, qty: w1 });
        } else if (w1 >= 5) {
            trending.push({ name, growth: 100, qty: w1 });
        }
    });

    trending.sort((a, b) => b.growth - a.growth);
    const result = { trending };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.profitAnalyzer);
    return result;
}
window.bizProductMomentum = bizProductMomentum;

// ── 10. Smart Pricing Detector ──────────────────────────────────────────────
async function bizSmartPricingDetector(businessId) {
    const cacheKey = 'pricing_' + businessId;
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const products = await BizDB.products.getAll();
    const active = products.filter(p => p.is_active !== false && p.business_id === businessId);

    const warnings = [];
    active.forEach(p => {
        if (p.price_sell > 0) {
            const hpp = p.hpp || 0;
            const margin = ((p.price_sell - hpp) / p.price_sell) * 100;
            if (margin >= 0 && margin < 20) {
                warnings.push({ id: p.id, name: p.name, price: p.price_sell, hpp, margin });
            } else if (margin < 0) {
                warnings.push({ id: p.id, name: p.name, price: p.price_sell, hpp, margin, critical: true });
            }
        }
    });

    warnings.sort((a, b) => a.margin - b.margin);
    const result = { warnings };
    _iCacheSet(cacheKey, result, BIZ_CACHE_TTL.profitAnalyzer);
    return result;
}
window.bizSmartPricingDetector = bizSmartPricingDetector;

// ── 11. Business Insight Feed (AI Advisor) ────────────────────────────────────
async function bizGenerateInsights(businessId) {
    const cacheKey = 'insights_' + (businessId || 'all');
    const cached = _iCacheGet(cacheKey);
    if (cached) return cached;

    const insights = [];

    // Get needed data
    const [snapshots, products, momentumData, pricingData, leakData] = await Promise.all([
        BizDB.finSnapshots.getAll(),
        BizDB.products.getAll(),
        bizProductMomentum(businessId),
        bizSmartPricingDetector(businessId),
        bizExpenseLeakDetector(businessId)
    ]);

    // 1. Stock / Inventory Health
    const physical = products.filter(p => p.type === 'physical' && p.is_active !== false);
    const lowStock = physical.filter(p => p.stock <= (p.low_stock_alert || 5) && p.stock > 0);
    const outStock = physical.filter(p => (p.stock || 0) <= 0);

    if (outStock.length > 0) {
        insights.push({
            type: 'danger',
            icon: 'fa-circle-xmark',
            text: `Darurat! ${outStock.length} produk kehabisan stok (${outStock[0].name}${outStock.length > 1 ? ', dll' : ''}). Segera restock!`
        });
    }

    // 2. Profit Analyzer Insights
    const monthKey = bizMonthKey();
    const profitData = await bizProfitAnalyzer(businessId, monthKey);

    if (profitData && profitData.products && profitData.products.length > 0) {
        const top = profitData.products[0];
        if (top.share > 30) {
            insights.push({
                type: 'success',
                icon: 'fa-fire',
                text: `Produk <b>${_esc(top.name)}</b> menyumbang ${top.share.toFixed(0)}% profil bulanan. Pertimbangkan buat promo bundling!`
            });
        }
    }

    // 3. Smart Pricing
    if (pricingData && pricingData.warnings.length > 0) {
        const crit = pricingData.warnings.find(w => w.critical);
        if (crit) {
            insights.push({
                type: 'danger',
                icon: 'fa-skull-crossbones',
                text: `<b>${_esc(crit.name)}</b> dijual RUGI! Harga jual (${bizRp(crit.price)}) lebih rendah dari HPP (${bizRp(crit.hpp)}). Segera perbaiki harga!`
            });
        } else {
            insights.push({
                type: 'warning',
                icon: 'fa-tags',
                text: `${pricingData.warnings.length} produk (termasuk <b>${_esc(pricingData.warnings[0].name)}</b>) memiliki profit margin kurang dari 20%. Awas margin tipis!`
            });
        }
    }

    // 4. Expense Leaks
    if (leakData && leakData.leaks.length > 0) {
        const topLeak = leakData.leaks[0];
        insights.push({
            type: 'warning',
            icon: 'fa-droplet',
            text: `Pengeluaran <b>${topLeak.category}</b> naik ${topLeak.jumpPct.toFixed(0)}% dibanding minggu lalu. Perhatikan cashflow!`
        });
    }

    // 5. Product Momentum
    if (momentumData && momentumData.trending.length > 0) {
        const topTrend = momentumData.trending[0];
        insights.push({
            type: 'primary',
            icon: 'fa-arrow-trend-up',
            text: `<b>${_esc(topTrend.name)}</b> sedang laris! Penjualan naik ${topTrend.growth.toFixed(0)}% minggu ini. Jangan sampai kehabisan stok.`
        });
    }

    // 6. Cashflow
    const forecast = bizCashForecast(snapshots, businessId);
    if (forecast.netDaily < 0) {
        insights.push({
            type: 'danger',
            icon: 'fa-wallet',
            text: `Cashflow harianmu negatif (-${bizRp(Math.abs(forecast.netDaily))}). Biaya rata-rata lebih besar dari pemasukan.`
        });
    }

    // Add default positive insight if too empty
    if (insights.length === 0) {
        insights.push({
            type: 'success',
            icon: 'fa-check-circle',
            text: `Bisnismu berjalan cerah stabil hari ini! Mumpung sepi alert, coba lakukan promosi ke pelanggan lama.`
        });
    }

    // Sort: danger -> warning -> primary -> success
    const weight = { 'danger': 1, 'warning': 2, 'primary': 3, 'success': 4 };
    insights.sort((a, b) => weight[a.type] - weight[b.type]);

    // Keep top 4 only to avoid overwhelming user
    const finalInsights = insights.slice(0, 4);

    _iCacheSet(cacheKey, finalInsights, BIZ_CACHE_TTL.profitAnalyzer);
    return finalInsights;
}
window.bizGenerateInsights = bizGenerateInsights;

// ── Clear intelligence cache ──────────────────────────────────────────────────
function bizClearIntelligenceCache() {
    Object.keys(localStorage)
        .filter(k => k.startsWith('bizai_'))
        .forEach(k => localStorage.removeItem(k));
}
window.bizClearIntelligenceCache = bizClearIntelligenceCache;
