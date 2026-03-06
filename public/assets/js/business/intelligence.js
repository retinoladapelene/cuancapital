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

// ── Clear intelligence cache ──────────────────────────────────────────────────
function bizClearIntelligenceCache() {
    Object.keys(localStorage)
        .filter(k => k.startsWith('bizai_'))
        .forEach(k => localStorage.removeItem(k));
}
window.bizClearIntelligenceCache = bizClearIntelligenceCache;
