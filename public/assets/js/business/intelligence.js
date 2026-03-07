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

// ── 5. Stock Burn Rate & Inventory Health (SaaS Level Upgrade) ──────────────
async function bizInventoryHealth(businessId) {
    const cacheKey = 'inv_health_' + businessId;
    const cached = _iCacheGet(cacheKey);
    // if (cached) return cached; // Temporarily disabled cache for development

    const [products, saleItems, sales] = await Promise.all([
        BizDB.products.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.sales.getAll()
    ]);

    const physical = products.filter(p => p.type === 'physical' && p.is_active !== false && p.business_id === businessId);

    // Calculate avg sales per day over last 30 days for more accurate velocity
    const cutoff30 = new Date();
    cutoff30.setDate(cutoff30.getDate() - 30);
    const recentSales30 = sales.filter(s => s.business_id === businessId && new Date(s.sale_date || s.created_at) >= cutoff30);
    const recentSalesIds = new Set(recentSales30.map(s => s.id));

    // Also track last sold date
    const lastSoldMap = {};
    const dailySalesMap = {};

    // We need to loop through items to build the maps
    const myItems = saleItems.filter(si => {
        // Need a way to tie item to sale date. Since saleItems don't have date, we cross ref.
        return recentSalesIds.has(si.sale_id);
    });

    // Build a map of sale_id -> date for quick lookup
    const saleDateMap = {};
    recentSales30.forEach(s => saleDateMap[s.id] = new Date(s.sale_date || s.created_at));

    myItems.forEach(si => {
        dailySalesMap[si.product_id] = (dailySalesMap[si.product_id] || 0) + si.quantity;

        const saleDate = saleDateMap[si.sale_id];
        if (saleDate) {
            if (!lastSoldMap[si.product_id] || saleDate > lastSoldMap[si.product_id]) {
                lastSoldMap[si.product_id] = saleDate;
            }
        }
    });

    const burnRates = [];
    let stateCounts = { fast: 0, normal: 0, slow: 0, dead: 0, low: 0, out: 0, over: 0, expiring: 0 };

    const now = new Date();

    physical.forEach(p => {
        const soldIn30 = dailySalesMap[p.id] || 0;
        const avgDaily = soldIn30 / 30;
        const daysLeft = avgDaily > 0 ? Math.floor(p.stock / avgDaily) : 999;
        const lastSold = lastSoldMap[p.id] || null;

        // Days since last sold
        let daysSinceSold = 999;
        if (lastSold) {
            daysSinceSold = Math.floor((now - lastSold) / (1000 * 60 * 60 * 24));
        } else {
            // Check if created recently
            const created = p.created_at ? new Date(p.created_at) : null;
            if (created) daysSinceSold = Math.floor((now - created) / (1000 * 60 * 60 * 24));
        }

        let velocityLabel = 'NORMAL';
        let stockLabel = 'OK';

        // Velocity logic
        if (daysSinceSold < 30 && soldIn30 === 0) { velocityLabel = 'NEW'; } // Prevent new products from being labeled DEAD
        else if (avgDaily > 2) { velocityLabel = 'FAST'; stateCounts.fast++; }
        else if (avgDaily >= 0.5) { velocityLabel = 'NORMAL'; stateCounts.normal++; }
        else if (avgDaily > 0) { velocityLabel = 'SLOW'; stateCounts.slow++; }
        else { velocityLabel = 'DEAD'; stateCounts.dead++; }

        // Stock quantity logic
        if (p.stock <= 0) { stockLabel = 'OUT'; stateCounts.out++; }
        else if (p.stock <= (p.low_stock_alert || 5)) { stockLabel = 'LOW'; stateCounts.low++; }
        else if (avgDaily > 0 && p.stock > (avgDaily * 30 * 3)) { stockLabel = 'OVER'; stateCounts.over++; } // Over 3 months of stock

        // Expiry logic (if applicable, assuming format YYYY-MM-DD or similar in meta)
        if (p.meta && p.meta.expiry_date) {
            const expD = new Date(p.meta.expiry_date);
            const diffDays = Math.ceil((expD - now) / (1000 * 60 * 60 * 24));
            if (diffDays >= 0 && diffDays <= 7) {
                stockLabel = 'EXPIRING';
                stateCounts.expiring++;
            }
        }

        // Determine final master status for badge
        let masterStatus = velocityLabel;
        if (stockLabel === 'OUT') masterStatus = 'OUT';
        else if (stockLabel === 'EXPIRING') masterStatus = 'EXPIRING';
        else if (stockLabel === 'LOW') masterStatus = 'LOW';
        else if (stockLabel === 'OVER') masterStatus = 'OVER';

        const hpp = p.hpp || 0;
        const price = p.price || 0;
        const marginPct = price > 0 && hpp > 0 ? ((price - hpp) / price) * 100 : 0;

        burnRates.push({
            id: p.id,
            name: p.name,
            sku: p.sku || '-',
            category: p.category || 'Uncategorized',
            stock: p.stock,
            hpp: hpp,
            price: price,
            marginPct: Number(marginPct.toFixed(1)),
            stockValue: p.stock * hpp,
            avgDaily: Number(avgDaily.toFixed(2)),
            daysLeft: daysLeft > 900 ? '90+' : daysLeft,
            lastSold: lastSold,
            daysSinceSold: daysSinceSold,
            velocityLabel: velocityLabel,
            stockLabel: stockLabel,
            masterStatus: masterStatus
        });
    });

    // Gamified Health Score via Ratio Logic
    const totalPhysical = physical.length || 1; // prevent div by zero
    const rDead = stateCounts.dead / totalPhysical;
    const rLow = (stateCounts.low + stateCounts.out) / totalPhysical;
    const rOver = stateCounts.over / totalPhysical;
    const rFast = stateCounts.fast / totalPhysical;

    let score = 100
        - (rDead * 40)
        - (rLow * 25)
        - (rOver * 20)
        + (rFast * 10);

    score = Math.max(0, Math.min(100, Math.round(score)));

    // Generate Predictive Alerts
    const alerts = [];
    if (stateCounts.low > 0) alerts.push({ type: 'danger', icon: 'fa-triangle-exclamation', text: `${stateCounts.low} produk stok kritis/menipis.` });
    if (stateCounts.out > 0) alerts.push({ type: 'danger', icon: 'fa-ban', text: `${stateCounts.out} produk out of stock.` });
    if (stateCounts.over > 0) alerts.push({ type: 'warning', icon: 'fa-boxes-packing', text: `${stateCounts.over} produk overstock (modal menganggur).` });
    if (stateCounts.expiring > 0) alerts.push({ type: 'danger', icon: 'fa-clock', text: `${stateCounts.expiring} produk akan expired dalam waktu dekat.` });
    if (stateCounts.dead > 0 && stateCounts.dead > (totalPhysical * 0.1)) alerts.push({ type: 'warning', icon: 'fa-skull', text: `${stateCounts.dead} produk dead stock (${Math.round(rDead * 100)}% dari katalog).` });

    // AI Insight Engine Generation
    const insights = [];
    const valByCat = {};
    burnRates.forEach(b => valByCat[b.category] = (valByCat[b.category] || 0) + b.stockValue);
    const sortedCats = Object.entries(valByCat).sort((a, b) => b[1] - a[1]);
    const totalVal = burnRates.reduce((a, b) => a + b.stockValue, 0) || 1;

    // Only push if the top category isn't Uncategorized and still owns > 40% of value
    if (sortedCats.length > 0) {
        let topCat = sortedCats[0];
        if (topCat[0] === 'Uncategorized' && sortedCats.length > 1) topCat = sortedCats[1];

        if (topCat[0] !== 'Uncategorized' && (topCat[1] / totalVal) > 0.4) {
            insights.push(`Kategori "${topCat[0]}" menyumbang ${Math.round((topCat[1] / totalVal) * 100)}% dari total nilai stok (Rp ${(topCat[1] / 1000000).toFixed(1)}jt).`);
        }
    }

    const fastItems = burnRates.filter(b => b.velocityLabel === 'FAST').sort((a, b) => b.marginPct - a.marginPct);
    if (fastItems.length > 0) {
        insights.push(`Produk paling profitable & laku keras: ${fastItems[0].name} (${fastItems[0].marginPct.toFixed(0)}% margin).`);
    }

    // Predictive Insight
    const soonEmpty = burnRates.filter(b => b.daysLeft !== '90+' && b.daysLeft > 0 && b.daysLeft <= 5);
    if (soonEmpty.length > 0) {
        soonEmpty.sort((a, b) => a.daysLeft - b.daysLeft);
        insights.push(`Prediksi: ${soonEmpty[0].name} akan habis dalam ${soonEmpty[0].daysLeft} hari berdasarkan tren saat ini.`);
    }

    const result = { score, counts: stateCounts, burnRates, alerts, insights, totalValue: totalVal };
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

// ── 7. Product Intelligence (SaaS Level Upgrade) ────────────────────────────────
async function bizProductIntelligence(businessId) {
    const cacheKey = 'prod_intel_' + businessId;
    const cached = _iCacheGet(cacheKey);
    // if (cached) return cached; // Temporarily disabled cache for development

    const [products, saleItems, sales] = await Promise.all([
        BizDB.products.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.sales.getAll()
    ]);

    const activeProducts = products.filter(p => p.is_active !== false && p.business_id === businessId);

    const now = new Date();
    const cutoff30 = new Date(now);
    cutoff30.setDate(cutoff30.getDate() - 30);

    const recentSales30 = sales.filter(s => s.business_id === businessId && new Date(s.sale_date || s.created_at) >= cutoff30);
    const recentSalesIds = new Set(recentSales30.map(s => s.id));

    // Maps
    const lastSoldMap = {};
    const dailySalesMap = {};
    const revenue30Map = {};
    const profit30Map = {};

    const saleDateMap = {};
    recentSales30.forEach(s => saleDateMap[s.id] = new Date(s.sale_date || s.created_at));

    const myItems = saleItems.filter(si => recentSalesIds.has(si.sale_id));

    myItems.forEach(si => {
        const pid = si.product_id;
        const qty = si.quantity || 0;
        const price = si.price || 0;
        const hpp = si.hpp || 0;

        dailySalesMap[pid] = (dailySalesMap[pid] || 0) + qty;
        revenue30Map[pid] = (revenue30Map[pid] || 0) + (qty * price);
        profit30Map[pid] = (profit30Map[pid] || 0) + (qty * (price - hpp));

        const saleDate = saleDateMap[si.sale_id];
        if (saleDate) {
            if (!lastSoldMap[pid] || saleDate > lastSoldMap[pid]) {
                lastSoldMap[pid] = saleDate;
            }
        }
    });

    const enrichedProducts = [];
    let highMarginCount = 0;
    let lowMarginCount = 0;
    let stagnantCount = 0;
    let totalRevenue30d = 0;
    let marginDistribution = { under10: 0, tenTo20: 0, twentyTo30: 0, over30: 0 };

    // For calculating cross-system alerts
    let alerts = [];
    let insights = [];

    activeProducts.forEach(p => {
        const sold30d = dailySalesMap[p.id] || 0;
        const rev30d = revenue30Map[p.id] || 0;
        const prof30d = profit30Map[p.id] || 0;
        const lastSold = lastSoldMap[p.id] || null;

        totalRevenue30d += rev30d;

        const hpp = p.hpp || 0;
        const price = p.price_sell || p.price || 0;
        const marginPct = price > 0 ? Math.round(((price - hpp) / price) * 100) : 0;

        if (marginPct >= 30) highMarginCount++;
        if (marginPct < 10 && marginPct >= 0 && price > 0) lowMarginCount++;
        if (sold30d === 0) stagnantCount++;

        // Margin Distribution
        if (marginPct < 10) marginDistribution.under10++;
        else if (marginPct < 20) marginDistribution.tenTo20++;
        else if (marginPct <= 30) marginDistribution.twentyTo30++;
        else marginDistribution.over30++;

        // Days since creation roughly
        let daysSinceLaunch = 999;
        if (p.created_at) {
            daysSinceLaunch = Math.floor((now - new Date(p.created_at)) / (1000 * 60 * 60 * 24));
        }

        let lifecycle = 'STABLE';
        if (daysSinceLaunch <= 30 && sold30d > 0) lifecycle = 'NEW';
        else if (sold30d === 0 && daysSinceLaunch > 30) lifecycle = 'DECLINING';
        // Basic growth heuristic if not enough historical data: strong sales volume
        else if (sold30d >= 15) lifecycle = 'GROWING';

        // Cross-System Alert Triggers
        if (lifecycle === 'GROWING' && p.stock <= (p.low_stock_alert || 5) && p.stock > 0) {
            alerts.push({ type: 'warning', icon: 'fa-triangle-exclamation', text: `Barang Laris <b>${p.name}</b> sisa stok hanya ${p.stock}` });
        }
        if (sold30d === 0 && p.stock > 5 && daysSinceLaunch >= 30) {
            alerts.push({ type: 'danger', icon: 'fa-turtle', text: `<b>${p.name}</b> tidak terjual 30 hari (Overstock: ${p.stock})` });
        }
        if (marginPct < 10 && price > 0) {
            alerts.push({ type: 'danger', icon: 'fa-tag', text: `Margin <b>${p.name}</b> sgt tipis (${marginPct}%)` });
        }

        enrichedProducts.push({
            id: p.id,
            name: p.name,
            sku: p.sku || '-',
            category: p.category || '-',
            price: price,
            hpp: hpp,
            stock: p.stock || 0,
            sold30d: sold30d,
            rev30d: rev30d,
            prof30d: prof30d,
            marginPct: marginPct,
            lastSold: lastSold,
            lifecycle: lifecycle,
            avgDaily: (sold30d / 30).toFixed(1),
            image_data: p.image_data || null
        });
    });

    // Sort heavily by revenue
    const sortedByRevenue = [...enrichedProducts].sort((a, b) => b.rev30d - a.rev30d);
    const sortedByProfit = [...enrichedProducts].sort((a, b) => b.prof30d - a.prof30d);

    // Insights Generation
    if (sortedByRevenue.length > 0) {
        let top3Rev = 0;
        for (let i = 0; i < Math.min(3, sortedByRevenue.length); i++) top3Rev += sortedByRevenue[i].rev30d;
        let revConcentration = totalRevenue30d > 0 ? (top3Rev / totalRevenue30d) * 100 : 0;

        if (revConcentration > 50) {
            insights.push(`💡 Ketergantungan tinggi: 3 produk teratas menyumbang ${revConcentration.toFixed(0)}% dari total Revenue.`);
        }
    }

    if (sortedByRevenue.length > 0 && sortedByProfit.length > 0) {
        if (sortedByRevenue[0].id !== sortedByProfit[0].id) {
            insights.push(`✨ <b>${sortedByProfit[0].name}</b> adalah produk paling profit, meskipun <b>${sortedByRevenue[0].name}</b> menang di omset.`);
        }
    }

    if (lowMarginCount > 0) {
        insights.push(`⚠ Peringatan Harga: ${lowMarginCount} produk memiliki margin sangat tipis (<10%).`);
    }

    // Prepare chart data (Top 10)
    let top10RevLabels = [], top10RevData = [];
    for (let i = 0; i < Math.min(10, sortedByRevenue.length); i++) {
        let n = sortedByRevenue[i].name;
        top10RevLabels.push(n.length > 15 ? n.substring(0, 12) + '...' : n);
        top10RevData.push(sortedByRevenue[i].rev30d);
    }

    let top10ProfLabels = [], top10ProfData = [];
    for (let i = 0; i < Math.min(10, sortedByProfit.length); i++) {
        let n = sortedByProfit[i].name;
        top10ProfLabels.push(n.length > 15 ? n.substring(0, 12) + '...' : n);
        top10ProfData.push(sortedByProfit[i].prof30d);
    }

    // Concentrate
    let top5Rev = 0;
    for (let i = 0; i < Math.min(5, sortedByRevenue.length); i++) top5Rev += sortedByRevenue[i].rev30d;
    let restRev = totalRevenue30d - top5Rev;

    // Deduplicate alerts (max 5)
    alerts = alerts.slice(0, 5);

    const intelResult = {
        stats: {
            totalProducts: activeProducts.length,
            totalRev30d: totalRevenue30d,
            highMargin: highMarginCount,
            lowMargin: lowMarginCount,
            topSellerName: sortedByRevenue.length > 0 && sortedByRevenue[0].rev30d > 0 ? sortedByRevenue[0].name : '-',
            stagnant: stagnantCount
        },
        distribution: marginDistribution,
        concentration: { top5: top5Rev, rest: restRev },
        top10Rev: { labels: top10RevLabels, data: top10RevData },
        top10Prof: { labels: top10ProfLabels, data: top10ProfData },
        alerts: alerts,
        insights: insights,
        products: sortedByRevenue // Return sorted by rev by default
    };

    _iCacheSet(cacheKey, intelResult, 1000 * 60 * 5); // 5 mins cache
    return intelResult;
}
window.bizProductIntelligence = bizProductIntelligence;

// ── 8. Sales Intelligence (8-Layer Command Center) ───────────────────────────
async function bizSalesIntelligence(businessId) {
    const cacheKey = 'biz_sales_intel_' + businessId;
    const cached = _iCacheGet(cacheKey);
    // if (cached) return cached; // Temporarily disable cache for development

    const [sales, saleItems, products, customers] = await Promise.all([
        BizDB.sales.getAll(),
        BizDB.saleItems.getAll(),
        BizDB.products.getAll(),
        BizDB.customers ? BizDB.customers.getAll() : Promise.resolve([])
    ]);

    const sAll = sales.filter(s => s.business_id === businessId);

    const now = new Date();
    const cutoff30d = new Date(now); cutoff30d.setDate(cutoff30d.getDate() - 30);
    const cutoff60d = new Date(now); cutoff60d.setDate(cutoff60d.getDate() - 60);

    // Filter Helper to handle both Date objects and string formats
    const getSafeDate = (dt) => {
        if (!dt) return 0;
        const res = new Date(dt);
        if (dt.includes('T')) return res.getTime(); // Precise to the minute if created_at
        // If it's just "YYYY-MM-DD", let it pass as midnight
        return res.getTime();
    };

    // 1. Split Timeframes
    const s30d = sAll.filter(s => getSafeDate(s.sale_date || s.created_at) >= cutoff30d.getTime());
    const sPrev30d = sAll.filter(s => {
        const dTime = getSafeDate(s.sale_date || s.created_at);
        return dTime >= cutoff60d.getTime() && dTime < cutoff30d.getTime();
    });

    // Overview KPIs
    let rev30 = 0, revPrev30 = 0;
    let ord30 = 0, ordPrev30 = 0;
    let units30 = 0;
    let canceled30 = 0;

    let cogs30 = 0; // To calculate profit30

    s30d.forEach(s => {
        if (s.status === 'cancelled') {
            canceled30++;
        } else {
            rev30 += (s.final_total || s.total_amount || 0);
            ord30++;
        }
    });

    sPrev30d.forEach(s => {
        if (s.status !== 'cancelled') {
            revPrev30 += (s.final_total || s.total_amount || 0);
            ordPrev30++;
        }
    });

    const revGrowth = revPrev30 > 0 ? ((rev30 - revPrev30) / revPrev30) * 100 : 0;
    const aov = ord30 > 0 ? (rev30 / ord30) : 0;
    const refundRate = s30d.length > 0 ? (canceled30 / s30d.length) * 100 : 0;

    // Extrapolate Revenue for month
    const daysPassed = now.getDate();
    const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    const currMonthSales = sAll.filter(s => new Date(s.sale_date || s.created_at).getMonth() === now.getMonth() && s.status !== 'cancelled');
    const currMonthRev = currMonthSales.reduce((sum, s) => sum + (s.final_total || s.total_amount || 0), 0);
    const projectedRev = daysPassed > 0 ? (currMonthRev / daysPassed) * daysInMonth : 0;

    // Real-Time Pulse (Last 60 mins) using strict created_at or falling back correctly
    const cutoff60m = new Date(now.getTime() - (60 * 60 * 1000));
    const s60m = sAll.filter(s => {
        // ALWAYS use created_at for live pulsing, because sale_date ("YYYY-MM-DD") parses to midnight
        const exactTimeStr = s.created_at || s.sale_date;
        const d = new Date(exactTimeStr);
        // If it was just "YYYY-MM-DD", d runs at 00:00:00 UTC. To make it pass for 'today' if missing created_at:
        // we'll strictly trust `d >= cutoff60m` which requires accurate ISO timestamps.
        return d >= cutoff60m && s.status !== 'cancelled';
    });
    const rev60m = s60m.reduce((sum, s) => sum + (s.final_total || s.total_amount || 0), 0);
    const ord60m = s60m.length;

    // Maps construction
    const pMap = {};
    products.forEach(p => pMap[p.id] = p);

    const s30Ids = new Set(s30d.filter(s => s.status !== 'cancelled').map(s => s.id));
    const items30 = saleItems.filter(si => s30Ids.has(si.sale_id));

    let catRev = {};
    let prodRev = {};
    let prodVol = {};

    // Bundles (frequently bought together) logic
    const saleToProductsMap = {};

    items30.forEach(si => {
        units30 += si.quantity;
        const p = pMap[si.product_id];
        const pName = p ? p.name : 'Unknown';
        const pCat = p ? (p.category || 'General') : 'General';
        const lineVal = (si.price * si.quantity) - (si.discount || 0);

        cogs30 += (si.hpp || 0) * si.quantity; // Sum COGS for profit30

        catRev[pCat] = (catRev[pCat] || 0) + lineVal;
        prodRev[pName] = (prodRev[pName] || 0) + lineVal;
        prodVol[pName] = (prodVol[pName] || 0) + si.quantity;

        // Group by sale_id for bundling
        if (!saleToProductsMap[si.sale_id]) saleToProductsMap[si.sale_id] = [];
        saleToProductsMap[si.sale_id].push(pName);
    });

    const profit30 = rev30 - cogs30; // Final 30D Profit

    // Calculate Bundles (O(N^2) on order lines)
    let pairCounts = {};
    Object.values(saleToProductsMap).forEach(prodArr => {
        // deduplicate within order
        const unique = [...new Set(prodArr)].sort();
        for (let i = 0; i < unique.length; i++) {
            for (let j = i + 1; j < unique.length; j++) {
                const pairStr = unique[i] + ' + ' + unique[j];
                pairCounts[pairStr] = (pairCounts[pairStr] || 0) + 1;
            }
        }
    });
    const sortedBundles = Object.entries(pairCounts).map(([pair, count]) => ({ pair, count })).sort((a, b) => b.count - a.count).slice(0, 3);

    // Categories array
    const catData = Object.entries(catRev).map(([label, value]) => ({ label, value })).sort((a, b) => b.value - a.value);

    // Channels
    const chanMap = {};
    s30d.filter(s => s.status !== 'cancelled').forEach(s => {
        const c = s.payment_method || 'POS';
        if (!chanMap[c]) chanMap[c] = { rev: 0, ord: 0 };
        chanMap[c].rev += (s.final_total || s.total_amount || 0);
        chanMap[c].ord++;
    });
    const chanData = Object.entries(chanMap).map(([channel, data]) => ({
        channel,
        revenue: data.rev,
        orders: data.ord,
        aov: data.ord > 0 ? data.rev / data.ord : 0
    })).sort((a, b) => b.revenue - a.revenue);

    // Products
    const sortedRev = Object.entries(prodRev).map(([name, rev]) => ({ name, rev })).sort((a, b) => b.rev - a.rev).slice(0, 5);
    const sortedVol = Object.entries(prodVol).map(([name, vol]) => ({ name, vol })).sort((a, b) => b.vol - a.vol).slice(0, 5);

    // Customer Intelligence
    let repeatCount = 0;
    let orderCounts = { one: 0, two: 0, multi: 0 };
    let totalClv = 0;
    let custMap = {};

    // Calculate off all time to get true repeat
    sAll.filter(s => s.status !== 'cancelled').forEach(s => {
        const cid = s.customer_id || s.customer_name || 'Guest';
        if (!custMap[cid]) custMap[cid] = { orders: 0, spent: 0 };
        custMap[cid].orders++;
        custMap[cid].spent += (s.final_total || s.total_amount || 0);
    });

    const definedCustNames = Object.keys(custMap).filter(k => k !== 'Guest');
    definedCustNames.forEach(cid => {
        const c = custMap[cid];
        if (c.orders > 1) repeatCount++;
        if (c.orders === 1) orderCounts.one++;
        else if (c.orders === 2) orderCounts.two++;
        else orderCounts.multi++;
        totalClv += c.spent;
    });

    const trueRepeatRate = definedCustNames.length > 0 ? (repeatCount / definedCustNames.length) * 100 : 0;
    const avgClv = definedCustNames.length > 0 ? (totalClv / definedCustNames.length) : 0;

    // Trend Visuals & Heatmap
    // We will do 30D for the initial payload. We create a map of YYYY-MM-DD
    const trendMap = {};
    const heatmap = { '0': {}, '1': {}, '2': {}, '3': {}, '4': {}, '5': {}, '6': {} }; // 0=Sun
    for (let d = 0; d < 7; d++) {
        for (let h = 0; h < 24; h++) heatmap[d][h] = 0;
    }

    for (let i = 29; i >= 0; i--) {
        const d = new Date(now);
        d.setDate(d.getDate() - i);
        const ymd = d.toISOString().split('T')[0];
        trendMap[ymd] = { rev: 0, vol: 0 };
    }

    // Process all 30d for Trends & Heatmap
    s30d.filter(s => s.status !== 'cancelled').forEach(s => {
        const d = new Date(s.sale_date || s.created_at);
        const ymd = d.toISOString().split('T')[0];

        const saleRev = (s.final_total || s.total_amount || 0);
        if (trendMap[ymd]) {
            trendMap[ymd].rev += saleRev;
            // Get vol logic. We have to map sale items again
            let v = items30.filter(si => si.sale_id === s.id).reduce((sum, si) => sum + si.quantity, 0);
            trendMap[ymd].vol += v;
        }

        // Heatmap populator
        const day = d.getDay(); // 0-6
        const hr = d.getHours(); // 0-23
        heatmap[day][hr]++;
    });

    // Arrays for charts
    const trendLabels = Object.keys(trendMap);
    const trendRev = trendLabels.map(k => trendMap[k].rev);
    const trendVol = trendLabels.map(k => trendMap[k].vol);

    // Alerts
    const alerts = [];
    if (sortedRev.length > 0 && sortedRev[0].rev > 0 && rev30 > 0) {
        if ((sortedRev[0].rev / rev30) > 0.4) {
            alerts.push({ type: 'warning', icon: 'fa-triangle-exclamation', text: `40%+ Revenue bergantung pada produk <b>${sortedRev[0].name}</b>` });
        }
    }
    if (chanData.length > 0 && chanData[0].orders > 0) {
        alerts.push({ type: 'primary', icon: 'fa-bolt', text: `Channel <b>${chanData[0].channel}</b> mensponsori ${chanData[0].orders} orders.` });
    }
    if (refundRate > 5) {
        alerts.push({ type: 'danger', icon: 'fa-rotate-left', text: `Tingkat pembatalan/refund tinggi (${refundRate.toFixed(1)}%). Segera cek kualitas.` });
    }
    if (sortedBundles.length > 0) {
        alerts.push({ type: 'success', icon: 'fa-boxes-packing', text: `Pelanggan sering membeli <b>${sortedBundles[0].pair}</b> bersamaan.` });
    }

    // Pre-process Smart Database (latest 100 sales)
    const smartDb = s30d.sort((a, b) => new Date(b.created_at || b.sale_date) - new Date(a.created_at || a.sale_date)).slice(0, 100).map(s => {
        // compute cogs from items
        const is = items30.filter(si => si.sale_id === s.id);
        const cogs = is.reduce((sum, si) => sum + ((si.hpp || 0) * si.quantity), 0);
        const rev = s.final_total || s.total_amount || 0;
        const prof = rev - cogs;
        const margin = rev > 0 ? (prof / rev) * 100 : 0;
        const qty = is.reduce((sum, si) => sum + si.quantity, 0);
        const prodNames = is.map(si => pMap[si.product_id] ? pMap[si.product_id].name : 'Unknown').join(', ');

        return {
            id: s.id,
            date: s.created_at || s.sale_date,
            trx: s.receipt_no || '-',
            products: prodNames,
            qty: qty,
            price: rev,
            discount: s.discount_amount || 0,
            cogs: cogs,
            profit: prof,
            margin: margin,
            channel: s.payment_method || 'POS',
            customer: s.customer_name || 'Guest',
            status: s.status || 'completed'
        }
    });

    const result = {
        overview: { rev30, revGrowth, ord30, profit30, units30, aov, repeatRate: trueRepeatRate, refundRate, projectedRev },
        realtime: { rev60m, ord60m },
        alerts: alerts,
        trends: { labels: trendLabels, rev: trendRev, vol: trendVol, heatmap: heatmap },
        composition: {
            category: { labels: catData.map(c => c.label), data: catData.map(c => c.value) },
            channel: chanData
        },
        product: { topRev: sortedRev, topVol: sortedVol, bundles: sortedBundles },
        customer: {
            ordersCounts: [orderCounts.one, orderCounts.two, orderCounts.multi],
            clv: avgClv
        },
        database: smartDb
    };

    _iCacheSet(cacheKey, result, 30000); // 30 sec TTL
    return result;
}
window.bizSalesIntelligence = bizSalesIntelligence;

// ── 9. Global Command Intelligence (BizOS Engine) ────────────────────────────
async function bizHealthScoreAnalytics(businessId) {
    const cacheKey = 'biz_health_radar_' + businessId;
    const cached = _iCacheGet(cacheKey);
    // if (cached) return cached;

    const [salesData, invData, sales, expenses] = await Promise.all([
        typeof bizSalesIntelligence === 'function' ? bizSalesIntelligence(businessId) : null,
        typeof bizInventoryHealth === 'function' ? bizInventoryHealth(businessId) : null,
        BizDB.sales.getAll(),
        BizDB.expenses ? BizDB.expenses.getAll() : Promise.resolve([])
    ]);

    if (!salesData || !invData) return null;

    let totalCashBal = 0;
    sales.forEach(s => { if (s.business_id === businessId && s.status !== 'cancelled') totalCashBal += (s.final_total || s.total_amount || 0); });
    expenses.forEach(e => { if (e.business_id === businessId) totalCashBal -= (e.amount || 0); });

    // 1. Revenue Growth Score (0-100)
    const rgRaw = salesData.overview.revGrowth || 0;
    let revScore = 50 + (rgRaw * 0.5); // flat 0% = 50.
    if (revScore > 100) revScore = 100;
    if (revScore < 0) revScore = 0;

    // 2. Profit Margin Score (0-100)
    const pmRaw = salesData.overview.rev30 > 0 ? (salesData.overview.profit30 / salesData.overview.rev30) * 100 : 0;
    let profScore = (pmRaw / 30) * 100;
    if (profScore > 100) profScore = 100;
    if (profScore < 0) profScore = 0;

    // 3. Inventory Health (0-100)
    const totalProd = Math.max(1, invData.burnRates.length);
    const deadCount = invData.counts ? invData.counts.dead : 0;
    const invScoreRaw = 100 - ((deadCount / totalProd) * 100);
    let invScore = invScoreRaw;
    if (invScore < 0) invScore = 0;

    // 4. Cashflow Stability (0-100)
    const cashBal = totalCashBal;
    const rev30 = salesData.overview.rev30 || 1;
    let cashScore = (cashBal / rev30) * 100;
    if (cashScore > 100) cashScore = 100;
    if (cashScore < 0) cashScore = 0;

    // 5. Customer Loyalty (0-100)
    // Repeat rate directly
    const rrRaw = salesData.overview.repeatRate || 0;
    // 50% repeat rate = 100 score
    let loyalScore = (rrRaw / 50) * 100;
    if (loyalScore > 100) loyalScore = 100;
    if (loyalScore < 0) loyalScore = 0;

    // Overall Calculation
    const totalScore = Math.round((revScore + profScore + invScore + cashScore + loyalScore) / 5);
    let status = 'KRITIS';
    if (totalScore >= 80) status = 'SEHAT SEKALI';
    else if (totalScore >= 60) status = 'SEHAT';
    else if (totalScore >= 40) status = 'WASPADA';

    const result = {
        axes: {
            revenue: Math.round(revScore),
            profit: Math.round(profScore),
            inventory: Math.round(invScore),
            cashflow: Math.round(cashScore),
            customer: Math.round(loyalScore)
        },
        raw: { growth: rgRaw, margin: pmRaw, deadStock: deadCount, repeat: rrRaw },
        score: totalScore,
        status: status
    };

    _iCacheSet(cacheKey, result, 60000); // 1 minute TTL for radar
    return result;
}
window.bizHealthScoreAnalytics = bizHealthScoreAnalytics;

async function bizGenerateGlobalInsights(businessId) {
    const cacheKey = 'biz_global_cfo_' + businessId;
    const cached = _iCacheGet(cacheKey);
    // if (cached) return cached;

    const [sales, inv] = await Promise.all([
        typeof bizSalesIntelligence === 'function' ? bizSalesIntelligence(businessId) : null,
        typeof bizInventoryHealth === 'function' ? bizInventoryHealth(businessId) : null
    ]);

    if (!sales || !inv) return [];

    const insights = [];
    const marginPct = sales.overview.rev30 > 0 ? (sales.overview.profit30 / sales.overview.rev30) * 100 : 0;
    const deadStock = inv.counts ? inv.counts.dead : 0;
    const lowStock = inv.counts ? inv.counts.low : 0;

    // CFO Insight 1: Sales / Revenue Growth
    if (sales.overview.revGrowth > 10) {
        insights.push({ icon: '💡', text: `Revenue bisnis naik stabil (+${sales.overview.revGrowth.toFixed(1)}%). Pertahankan momentum!` });
    } else if (sales.overview.revGrowth < -10) {
        insights.push({ icon: '📉', text: `Penjualan turun ${Math.abs(sales.overview.revGrowth).toFixed(1)}%. Coba buat promo atau bundling baru.` });
    }

    // CFO Insight 2: Profit Margin Trap
    if (marginPct > 0 && marginPct < 15) {
        insights.push({ icon: '⚠', text: `Margin profit rata-rata kamu hanya ${marginPct.toFixed(1)}%. Evaluasi harga jual atau COGS agar bisnis tetap bernapas.` });
    } else if (marginPct >= 30) {
        insights.push({ icon: '💰', text: `Margin profit bisnis sangat bagus (>30%). Margin tebal membuat bisnis aman dari krisis.` });
    }

    // CFO Insight 3: Inventory Leaks
    if (deadStock > 0) {
        insights.push({ icon: '📦', text: `Ada ${deadStock} produk berstatus Dead-Stock (>90 hari tidak laku). Segera obral agar uang kembali.` });
    }
    if (lowStock > 0) {
        insights.push({ icon: '⚡', text: `${lowStock} produk terlaris mulai kehabisan stok. Segera restock sebelum kehabisan!` });
    }

    // CFO Insight 4: Customer Retention
    if (sales.overview.repeatRate > 30) {
        insights.push({ icon: '🔁', text: `${sales.overview.repeatRate.toFixed(1)}% pelanggan rutin belanja kembali. Loyalitas pelanggan terbangun kuat!` });
    } else if (sales.overview.repeatRate < 10) {
        insights.push({ icon: '👥', text: `Pelanggan repeat order sangat rendah (${sales.overview.repeatRate.toFixed(1)}%). Tawarkan voucher next-order!` });
    }

    // CFO Insight 5: Product Bundling
    if (sales.product.bundles && sales.product.bundles.length > 0) {
        insights.push({ icon: '🎁', text: `Pelanggan sering membeli <b>${sales.product.bundles[0].pair}</b>. Jadikan menu bundling permanen untuk naikkan AOV.` });
    }

    _iCacheSet(cacheKey, insights, 60000); // 1 min TTL
    return insights;
}
window.bizGenerateGlobalInsights = bizGenerateGlobalInsights;
