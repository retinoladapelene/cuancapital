/**
 * Reports / Analisa Tab Logic
 */

window.renderReportsTab = function (container) {
    window.injectHTML(container, `
        <!-- ══ LAYER 1: HEADER FILTER ══ -->
        <div class="lap-header-card">
            <div class="lap-header-title">
                <i class="fas fa-chart-pie"></i> Laporan Keuangan
            </div>
            <div class="lap-filter-wrap">
                <div class="lap-pill-group">
                    <button class="lap-pill active" onclick="setLapPeriod('this_month', this)">Bulan Ini</button>
                    <button class="lap-pill" onclick="setLapPeriod('last_month', this)">Bulan Lalu</button>
                    <button class="lap-pill" onclick="setLapPeriod('last_3_months', this)">3 Bulan</button>
                    <button class="lap-pill" onclick="setLapPeriod('this_year', this)">Tahun Ini</button>
                    <button class="lap-pill" onclick="setLapPeriod('custom', this)">Kustom</button>
                </div>
                <div class="lap-custom-date" id="lap-custom-date" style="display:none;">
                    <input type="date" class="finput" id="lap-date-from" onchange="generateReport()">
                    <span>-</span>
                    <input type="date" class="finput" id="lap-date-to" onchange="generateReport()">
                </div>
                <div class="sel-wrap" style="width:140px;">
                    <select class="fselect" id="lap-filter-account" onchange="generateReport()">
                        <option value="all">Semua Akun</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ══ LAYER 2: FINANCIAL HEALTH (KPI CARDS) ══ -->
        <div class="lap-metrics-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
            <div class="lap-metric-card" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(16,185,129,0.05)); border: 1px solid rgba(59,130,246,0.2);">
                <div class="lap-metric-icon" style="background:var(--accent);color:#fff;box-shadow:0 4px 10px rgba(16,185,129,0.3);"><i class="fas fa-star"></i></div>
                <div class="lap-metric-body">
                    <div class="lap-metric-label">FinHealth Score</div>
                    <div class="lap-metric-val" id="lap-val-score" style="color:var(--text);font-size:22px;">0 <span style="font-size:12px;color:var(--text-muted);font-weight:600;">/ 100</span></div>
                </div>
            </div>
            <div class="lap-metric-card">
                <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div class="lap-metric-label" style="margin:0;"><i class="fas fa-arrow-down-long" style="color:#22c55e;margin-right:4px;"></i> Pemasukan</div>
                        <div id="lap-mom-income" class="lap-mom-badge">--</div>
                    </div>
                    <div class="lap-metric-val" id="lap-val-income" style="color:#22c55e;">Rp 0</div>
                </div>
            </div>
            <div class="lap-metric-card">
                <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div class="lap-metric-label" style="margin:0;"><i class="fas fa-arrow-up-long" style="color:#ef4444;margin-right:4px;"></i> Pengeluaran</div>
                        <div id="lap-mom-expense" class="lap-mom-badge">--</div>
                    </div>
                    <div class="lap-metric-val" id="lap-val-expense" style="color:#ef4444;">Rp 0</div>
                </div>
            </div>
            <div class="lap-metric-card">
                <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div class="lap-metric-label" style="margin:0;"><i class="fas fa-chart-line" style="color:#3b82f6;margin-right:4px;"></i> Profit / Net</div>
                        <div id="lap-mom-net" class="lap-mom-badge">--</div>
                    </div>
                    <div class="lap-metric-val" id="lap-val-net" style="color:#3b82f6;">Rp 0</div>
                </div>
            </div>
            <div class="lap-metric-card">
                <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div class="lap-metric-label" style="margin:0;"><i class="fas fa-piggy-bank" style="color:var(--text);margin-right:4px;"></i> Saving Rate</div>
                        <div id="lap-mom-saving" class="lap-mom-badge">--</div>
                    </div>
                    <div class="lap-metric-val" id="lap-val-saving" style="color:var(--text);">0%</div>
                </div>
            </div>
        </div>

        <!-- ══ LAYER 3: CASHFLOW TREND ══ -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-head">
                <span class="card-title">Tren Arus Kas</span>
                <span style="font-size:11px;color:var(--text-muted);">Pemasukan vs Pengeluaran</span>
            </div>
            <div class="card-body" style="padding-top:16px;">
                <div style="height:260px;position:relative;">
                    <canvas id="lapTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ══ LAYER 4: EXPENSE ANALYSIS ══ -->
        <div class="lap-charts-grid" style="grid-template-columns: 1fr 1fr;">
            <div class="card" style="display:flex; flex-direction:column;">
                <div class="card-head"><span class="card-title">Distribusi Pengeluaran</span></div>
                <div class="card-body" style="padding-top:16px; flex:1; display:flex; flex-direction:column;">
                    <div style="height:170px;position:relative;margin-bottom:20px;">
                        <canvas id="lapDonutChart"></canvas>
                        <div id="lap-donut-empty" class="empty" style="display:none;position:absolute;inset:0;flex-direction:column;align-items:center;justify-content:center;background:var(--surface);"><i class="fas fa-chart-pie"></i>Belum ada data</div>
                    </div>
                    <div id="lap-donut-legend" style="display:flex;flex-direction:column;gap:8px;padding:0 10px;"></div>
                </div>
            </div>
            <div class="card" style="display:flex; flex-direction:column;">
                <div class="card-head"><span class="card-title">Top 5 Kategori Aktif</span></div>
                <div class="card-body" style="padding-top:16px; flex:1;">
                    <div id="lap-top-expenses" style="display:flex; flex-direction:column; gap:16px;">
                        <div class="empty"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SMART INSIGHT BOX ══ -->
        <div class="lap-insight-box">
            <i class="fas fa-lightbulb" style="color:#f59e0b;font-size:20px;"></i>
            <div style="flex:1;">
                <div style="font-size:12px;font-weight:800;color:#f59e0b;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">Smart Insights</div>
                <div style="font-size:13px;color:var(--text);line-height:1.6;" id="lap-smart-insight">Memuat analisis...</div>
            </div>
        </div>

        <!-- ══ LAYER 5: DETAILED TABLE ══ -->
        <div class="card">
            <div class="card-head" style="gap:10px; flex-wrap:wrap;">
                <span class="card-title">Detail Transaksi</span>
                <div style="display:flex; gap:8px;">
                    <input type="text" class="finput" id="lap-tx-search" placeholder="Cari..." style="width:140px;padding:6px 12px;font-size:12px;" oninput="if(!window.debouncedLapSearch) window.debouncedLapSearch = window.debounce(() => requestAnimationFrame(() => renderLapTxTable(lapCurrentData.filteredTx)), 300); window.debouncedLapSearch();">
                    <button class="btn btn-ghost btn-sm" onclick="exportCSV()"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn btn-ghost btn-sm" onclick="exportData()"><i class="fas fa-file-code"></i> JSON</button>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="lap-tx-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Kategori</th><th>Deskripsi</th>
                            <th style="text-align:right;">Masuk</th><th style="text-align:right;">Keluar</th>
                        </tr>
                    </thead>
                    <tbody id="lap-det-tbody">
                        <tr><td colspan="5"><div class="empty">Memuat data...</div></td></tr>
                    </tbody>
                </table>
            </div>
            <div class="lap-det-footer">
                <span id="lap-det-info">0 transaksi</span>
                <button class="btn btn-ghost btn-sm" id="lap-det-more" style="display:none;" onclick="lapTxVisibleCount += 20; renderLapTxTable(lapCurrentData.filteredTx);">Muat Lebih</button>
            </div>
        </div>
    `);

    setTimeout(() => { if (typeof window.generateReport === 'function') window.generateReport(); }, 1);

    return function cleanup() {
        if (window.lapTrendChart && typeof window.lapTrendChart.destroy === 'function') {
            window.lapTrendChart.destroy(); window.lapTrendChart = null;
        }
        if (window.lapDonutChart && typeof window.lapDonutChart.destroy === 'function') {
            window.lapDonutChart.destroy(); window.lapDonutChart = null;
        }
        window._lapInitDone = false;
    };
};


let lapCurrentPeriod = 'this_month';
// lapTxVisibleCount is declared in core.js (global shared state)
let lapTxDataBuffer = [];
let lapCashflowChart = null, lapPillarChart = null, lapNetWorthChart = null;
let lapChartType = 'bar'; // default

function setLapPeriod(period, btnEl) {
    lapCurrentPeriod = period;
    document.querySelectorAll('.lap-pill').forEach(btn => btn.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    const customDate = document.getElementById('lap-custom-date');
    if (period === 'custom') {
        if (customDate) customDate.style.display = 'flex';
    } else {
        if (customDate) customDate.style.display = 'none';
        generateReport();
    }
}

function getLapActiveDateRange() {
    const now = new Date();
    const y = now.getFullYear();
    const m = now.getMonth();
    let start, end;

    if (lapCurrentPeriod === 'this_month') {
        start = new Date(y, m, 1);
        end = new Date(y, m + 1, 0, 23, 59, 59);
    } else if (lapCurrentPeriod === 'last_month') {
        start = new Date(y, m - 1, 1);
        end = new Date(y, m, 0, 23, 59, 59);
    } else if (lapCurrentPeriod === 'last_3_months') {
        start = new Date(y, m - 2, 1);
        end = new Date(y, m + 1, 0, 23, 59, 59);
    } else if (lapCurrentPeriod === 'this_year') {
        start = new Date(y, 0, 1);
        end = new Date(y, 11, 31, 23, 59, 59);
    } else if (lapCurrentPeriod === 'custom') {
        const fEl = document.getElementById('lap-date-from');
        const tEl = document.getElementById('lap-date-to');
        if (fEl && tEl && fEl.value && tEl.value) {
            start = new Date(fEl.value + 'T00:00:00');
            end = new Date(tEl.value + 'T23:59:59');
        } else { return null; }
    }
    return { start, end };
}

function renderLapTxTable(forceData = null) {
    if (forceData) lapTxDataBuffer = forceData;
    const tbody = document.getElementById('lap-det-tbody');
    if (!tbody) return;

    let dataToRender = lapTxDataBuffer || [];
    const searchQ = document.getElementById('lap-tx-search')?.value.toLowerCase() || '';

    if (searchQ) {
        dataToRender = dataToRender.filter(tx => {
            const note = (tx.note || '').toLowerCase();
            const cat = (tx.category?.name || '').toLowerCase();
            return note.includes(searchQ) || cat.includes(searchQ);
        });
    }

    const countEl = document.getElementById('lap-det-info');
    if (countEl) countEl.textContent = `${dataToRender.length} transaksi`;

    const toShow = dataToRender.slice(0, lapTxVisibleCount);
    if (!toShow.length) {
        tbody.innerHTML = '<tr><td colspan="5"><div class="empty" style="padding:30px 0;"><i class="fas fa-search"></i>Tidak ada transaksi ditemukan</div></td></tr>';
        const moreBtn = document.getElementById('lap-det-more');
        if (moreBtn) moreBtn.style.display = 'none';
        return;
    }

    const frag = document.createDocumentFragment();

    toShow.forEach(tx => {
        const isInc = tx.type === 'income';
        const amt = parseFloat(tx.amount || 0);

        const incCol = isInc ? `<span style="color:#22c55e;font-family:'JetBrains Mono',monospace;font-weight:700;">+${rp(amt)}</span>` : '-';
        const expCol = !isInc ? `<span style="color:#ef4444;font-family:'JetBrains Mono',monospace;font-weight:700;">-${rp(amt)}</span>` : '-';

        let dateStr = tx.transaction_date;
        try {
            const dp = tx.transaction_date.split('T')[0].split('-');
            dateStr = `${dp[2]}/${dp[1]}/${dp[0].slice(2)}`;
        } catch (e) { }

        const catName = tx.category?.name || (tx.type === 'transfer' ? 'Transfer' : 'Lainnya');
        const note = tx.note || '<em style="color:var(--text-muted);font-size:11px;">Tanpa catatan</em>';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="color:var(--text-muted);font-family:'JetBrains Mono',monospace;">${dateStr}</td>
            <td><span class="tag" style="background:var(--surface2);color:var(--text);padding:3px 8px;">${catName}</span></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${note}</td>
            <td style="text-align:right;">${incCol}</td>
            <td style="text-align:right;">${expCol}</td>
        `;
        frag.appendChild(tr);
    });

    tbody.replaceChildren(frag);

    const moreBtn = document.getElementById('lap-det-more');
    if (moreBtn) moreBtn.style.display = lapTxVisibleCount < dataToRender.length ? 'inline-block' : 'none';
}

function exportCSV() {
    const data = lapTxDataBuffer || [];
    if (!data.length) return toast('Tidak ada data untuk diekspor', 'e');

    const rows = [['Tanggal', 'Kategori', 'Deskripsi', 'Tipe', 'Jumlah']];
    data.forEach(tx => {
        rows.push([
            tx.transaction_date.split('T')[0],
            (tx.category?.name || '').replace(/,/g, ''),
            (tx.note || '').replace(/,/g, ' '),
            tx.type,
            tx.amount
        ]);
    });

    const csv = rows.map(r => r.join(',')).join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `CuanCapital_Laporan_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    toast('CSV berhasil diunduh!', 's');
}

function printLaporan() {
    window.print();
}

function switchLapChart(type, btn) {
    lapChartType = type;
    const barBtn = document.getElementById('lap-btn-bar');
    const lineBtn = document.getElementById('lap-btn-line');
    if (barBtn) barBtn.style.background = type === 'bar' ? 'var(--surface2)' : '';
    if (lineBtn) lineBtn.style.background = type === 'line' ? 'var(--surface2)' : '';
    generateReport();
}

// Ensure account filter is populated initially
function populateLapAccountFilter() {
    const fn = typeof window.populateLapAccountFilterOriginal === 'function' ? window.populateLapAccountFilterOriginal : function () {
        const sel = document.getElementById('lap-filter-account');
        if (!sel || (sel.options && sel.options.length > 1)) return; // Already populated
        sel.innerHTML = '<option value="all">Semua Akun</option>';
        (accounts || []).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.name;
            sel.appendChild(opt);
        });
    };
    fn();
}

async function generateReport() {
    populateLapAccountFilter();

    // ── Lazy Load Chart.js if needed ──
    if (typeof window.Chart === 'undefined') {
        try {
            await window.loadModule('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js');
            Chart.defaults.animation = false;
            Chart.defaults.animations = { colors: false, x: false };
            Chart.defaults.transitions = { active: { animation: { duration: 0 } } };
        } catch (e) { console.error('Gagal load Chart.js', e); }
    }

    if (!allTxList || !allTxList.length) {
        const emptyEl = document.getElementById('lap-donut-empty');
        if (emptyEl) emptyEl.style.display = 'flex';

        const tbody = document.getElementById('lap-det-tbody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="5"><div class="empty"><i class="fas fa-receipt"></i>Belum ada data transaksi</div></td></tr>';

        const smartEl = document.getElementById('lap-smart-insight');
        if (smartEl) smartEl.innerHTML = 'Mulai catat transaksi untuk mendapatkan insight keuangan otomatis.';
        return;
    }

    const range = getLapActiveDateRange();
    const accFilter = document.getElementById('lap-filter-account')?.value || 'all';
    lapTxVisibleCount = 20;

    const filteredTx = allTxList.filter(tx => {
        if (tx.type === 'transfer') return false;
        if (accFilter !== 'all' && String(tx.account_id) !== String(accFilter)) return false;
        if (range) {
            const d = new Date(tx.transaction_date);
            if (d < range.start || d > range.end) return false;
        }
        return true;
    });

    let totalInc = 0, totalExp = 0;
    const catExpMap = {};
    const tsData = {}; // time series data for chart

    filteredTx.forEach(tx => {
        const amt = parseFloat(tx.amount || 0);
        const isInc = tx.type === 'income';

        if (isInc) { totalInc += amt; }
        else {
            totalExp += amt;
            const cName = tx.category?.name || 'Lain-lain';
            catExpMap[cName] = (catExpMap[cName] || 0) + amt;
        }

        // Daily aggregation for line chart
        const dStr = tx.transaction_date.split('T')[0];
        if (!tsData[dStr]) tsData[dStr] = { inc: 0, exp: 0 };
        if (isInc) tsData[dStr].inc += amt; else tsData[dStr].exp += amt;
    });

    const net = totalInc - totalExp;
    const savingRate = totalInc > 0 ? (net / totalInc) * 100 : 0;

    // ── 0. Month-over-Month (MoM) Logic & Financial Score ──
    let prevInc = 0, prevExp = 0, prevNet = 0, prevSav = 0;
    if (range && allTxList && allTxList.length) {
        const rangeDuration = range.end.getTime() - range.start.getTime();
        const prevStart = new Date(range.start.getTime() - rangeDuration);
        const prevEnd = new Date(range.end.getTime() - rangeDuration);

        allTxList.forEach(tx => {
            if (tx.type === 'transfer') return;
            const d = new Date(tx.transaction_date);
            if (d >= prevStart && d <= prevEnd) {
                const amt = parseFloat(tx.amount || 0);
                if (tx.type === 'income') prevInc += amt;
                else prevExp += amt;
            }
        });
        prevNet = prevInc - prevExp;
        prevSav = prevInc > 0 ? (prevNet / prevInc) * 100 : 0;
    }

    function updateMom(elId, curr, prev, invert = false) {
        const el = document.getElementById(elId);
        if (!el) return;
        if (prev === 0 && curr === 0) {
            el.innerHTML = '--'; el.className = 'lap-mom-badge neutral'; return;
        }
        if (prev === 0) {
            el.innerHTML = `<i class="fas fa-arrow-up"></i> 100%`;
            el.className = `lap-mom-badge ${invert ? 'negative' : 'positive'}`;
            return;
        }
        const diffPct = ((curr - prev) / prev) * 100;
        const isPos = diffPct >= 0;
        if (diffPct === 0) { el.innerHTML = '--'; el.className = 'lap-mom-badge neutral'; return; }

        const isGood = invert ? !isPos : isPos; // For expenses, down is good (positive class)
        el.innerHTML = `<i class="fas fa-arrow-${isPos ? 'up' : 'down'}"></i> ${Math.abs(diffPct).toFixed(1)}%`;
        el.className = `lap-mom-badge ${isGood ? 'positive' : 'negative'}`;
    }

    updateMom('lap-mom-income', totalInc, prevInc);
    updateMom('lap-mom-expense', totalExp, prevExp, true);
    updateMom('lap-mom-net', net, prevNet);
    updateMom('lap-mom-saving', savingRate, prevSav);

    // Calculate FinHealth Score
    let score = 0;
    if (totalInc > 0) {
        if (savingRate > 20) score += 60;
        else if (savingRate > 10) score += 40;
        else if (savingRate > 0) score += 20;

        const expRatio = totalExp / totalInc;
        if (expRatio <= 0.4) score += 40;
        else if (expRatio <= 0.6) score += 30;
        else if (expRatio <= 0.8) score += 15;
        else if (expRatio < 1) score += 5;
    } else if (totalExp === 0 && totalInc === 0) {
        score = 0;
    } else {
        score = 10; // Only spending, no income
    }

    // Ensure no unrealistic zeroes if active
    if (score === 0 && (totalInc > 0 || totalExp > 0)) score = 50;

    const scoreEl = document.getElementById('lap-val-score');
    if (scoreEl) {
        scoreEl.innerHTML = `${Math.round(score)} <span style="font-size:12px;color:var(--text-muted);font-weight:600;">/ 100</span>`;
        if (score >= 80) scoreEl.style.color = 'var(--accent)';
        else if (score >= 50) scoreEl.style.color = 'var(--text)';
        else scoreEl.style.color = '#ef4444';
    }

    // ── 1. Update Metrics ──
    const setEl = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
    setEl('lap-val-income', rp(totalInc));
    setEl('lap-val-expense', rp(totalExp));
    setEl('lap-val-net', rp(net));
    setEl('lap-val-saving', Math.max(0, savingRate).toFixed(1) + '%');

    const netEl = document.getElementById('lap-val-net');
    if (netEl) {
        if (net < 0) netEl.style.color = '#ef4444';
        else netEl.style.color = '#3b82f6';
    }

    // ── 2. Line/Bar Chart (Cashflow Trend) ──
    if (window.renderLapCashflowChart) {
        window.renderLapCashflowChart(tsData, lapChartType);
    } else {
        // inline logic if not segregated further
        let labels = Object.keys(tsData).sort();
        // Mobile performance: down-sample to max 30 points to avoid GPU lag
        if (labels.length > 30) {
            const step = Math.ceil(labels.length / 30);
            labels = labels.filter((_, i) => i % step === 0 || i === labels.length - 1);
        }
        const chartLabels = labels.map(l => {
            const d = new Date(l);
            return `${d.getDate()} ${d.toLocaleString('id-ID', { month: 'short' })}`;
        });
        const incData = labels.map(k => tsData[k].inc);
        const expData = labels.map(k => tsData[k].exp);

        const canvasObj = document.getElementById('lapTrendChart');
        if (canvasObj) {
            const ctxTrend = canvasObj.getContext('2d');

            if (window.lapTrendChart && typeof window.lapTrendChart.update === 'function' && window.lapTrendChart.config.type === (lapChartType === 'bar' ? 'bar' : 'line')) {
                // Reuse existing chart instance
                window.lapTrendChart.data.labels = chartLabels;
                window.lapTrendChart.data.datasets[0].data = incData;
                window.lapTrendChart.data.datasets[1].data = expData;
                window.lapTrendChart.update();
            } else {
                // Create new only if type changed or didn't exist
                if (window.lapTrendChart && typeof window.lapTrendChart.destroy === 'function') window.lapTrendChart.destroy();

                const gInc = ctxTrend.createLinearGradient(0, 0, 0, 240);
                gInc.addColorStop(0, 'rgba(34,197,94,0.3)'); gInc.addColorStop(1, 'rgba(34,197,94,0.02)');
                const gExp = ctxTrend.createLinearGradient(0, 0, 0, 240);
                gExp.addColorStop(0, 'rgba(239,68,68,0.3)'); gExp.addColorStop(1, 'rgba(239,68,68,0.02)');

                window.lapTrendChart = new Chart(ctxTrend, {
                    type: lapChartType === 'bar' ? 'bar' : 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            { label: 'Pemasukan', data: incData, borderColor: '#22c55e', backgroundColor: lapChartType === 'line' ? gInc : '#22c55e', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 2, pointHoverRadius: 4, borderRadius: lapChartType === 'bar' ? 4 : 0 },
                            { label: 'Pengeluaran', data: expData, borderColor: '#ef4444', backgroundColor: lapChartType === 'line' ? gExp : '#ef4444', fill: true, tension: 0.4, borderWidth: 2, pointRadius: 2, pointHoverRadius: 4, borderRadius: lapChartType === 'bar' ? 4 : 0 }
                        ]
                    },
                    options: {
                        animation: false,
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: true, position: 'top', align: 'end', labels: { boxWidth: 10, usePointStyle: true, font: { size: 11, family: "'Inter', sans-serif" } } },
                            tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', padding: 10, cornerRadius: 8, callbacks: { label: c => ' ' + c.dataset.label + ': ' + rp(c.raw) } }
                        },
                        scales: {
                            y: { border: { display: false }, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#64748b', font: { size: 10 }, callback: v => rp(v) } },
                            x: { border: { display: false }, grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 }, maxTicksLimit: 10 } }
                        }
                    }
                });
            }
        }
    }

    // ── 3. Donut Chart (Category Breakdown) ──
    const catSorted = Object.entries(catExpMap).sort((a, b) => b[1] - a[1]);
    const topCats = catSorted.slice(0, 5);
    if (catSorted.length > 5) {
        const othersAmt = catSorted.slice(5).reduce((acc, curr) => acc + curr[1], 0);
        topCats.push(['Lainnya', othersAmt]);
    }

    const dLabels = topCats.map(c => c[0]);
    const dData = topCats.map(c => c[1]);
    const dColors = ['#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ef4444', '#94a3b8'];

    const donutEmpty = document.getElementById('lap-donut-empty');
    if (donutEmpty) donutEmpty.style.display = totalExp > 0 ? 'none' : 'flex';

    const cObj = document.getElementById('lapDonutChart');
    if (cObj) {
        const ctxDonut = cObj.getContext('2d');

        if (totalExp > 0) {
            if (window.lapDonutChart && typeof window.lapDonutChart.update === 'function') {
                window.lapDonutChart.data.labels = dLabels;
                window.lapDonutChart.data.datasets[0].data = dData;
                window.lapDonutChart.update();
            } else {
                if (window.lapDonutChart && typeof window.lapDonutChart.destroy === 'function') window.lapDonutChart.destroy();
                window.lapDonutChart = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: { labels: dLabels, datasets: [{ data: dData, backgroundColor: dColors, borderWidth: 0, hoverOffset: 4 }] },
                    options: {
                        animation: false,
                        responsive: true, maintainAspectRatio: false, cutout: '75%',
                        plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,0.9)', callbacks: { label: c => ' ' + rp(c.raw) } } }
                    }
                });
            }

            // Build custom legend
            const legEl = document.getElementById('lap-donut-legend');
            if (legEl) legEl.innerHTML = topCats.map((c, i) => {
                const pct = ((c[1] / totalExp) * 100).toFixed(1);
                return `<div class="lap-legend-item">
                    <div class="lap-legend-left">
                        <div class="lap-legend-color" style="background:${dColors[i]}"></div>
                        <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px;" title="${c[0]}">${c[0]}</span>
                    </div>
                    <div class="lap-legend-right">
                        <span class="lap-legend-val">${rp(c[1])}</span>
                        <span class="lap-legend-pct">${pct}%</span>
                    </div>
                </div>`;
            }).join('');

            // Build Top Expenses Progress Bars
            const maxExpAmt = topCats.length > 0 ? topCats[0][1] : 1;
            const topExpEl = document.getElementById('lap-top-expenses');
            if (topExpEl) topExpEl.innerHTML = topCats.map((c, i) => {
                const fillPct = (c[1] / maxExpAmt) * 100;
                return `
                <div class="lap-exp-row">
                    <div class="lap-exp-header">
                        <span style="color:var(--text);">${c[0]}</span>
                        <span class="lap-exp-val">${rp(c[1])}</span>
                    </div>
                    <div class="lap-exp-track">
                        <div class="lap-exp-fill" style="width:${fillPct}%; background:${dColors[i]}"></div>
                    </div>
                </div>`;
            }).join('');

        } else {
            const legE = document.getElementById('lap-donut-legend');
            if (legE) legE.innerHTML = '';
            const teE = document.getElementById('lap-top-expenses');
            if (teE) teE.innerHTML = '<div class="empty"><i class="fas fa-check-circle" style="color:var(--accent);"></i>Belum ada pengeluaran</div>';
        }
    }

    // ── 4. Smart Insight ──
    const insightEl = document.getElementById('lap-smart-insight');
    if (insightEl) {
        let insights = [];
        if (totalExp === 0 && totalInc === 0) {
            insights.push('Belum ada aktivitas transaksi di periode ini.');
        } else {
            if (savingRate >= 20) {
                insights.push(`🔥 <strong>Sehat:</strong> Saving rate kamu berada di angka aman (${savingRate.toFixed(1)}%). Pertahankan!`);
            } else if (savingRate > 0) {
                insights.push(`💡 <strong>Saran:</strong> Kamu masih mencatat surplus, cobalah alokasikan sedikit pengeluaran konsumtif ke tabungan untuk menaikkan Financial Score-mu.`);
            } else if (totalExp > 0) {
                const deficit = totalExp - totalInc;
                insights.push(`⚠️ <strong>Perhatian:</strong> Kamu mengalami defisit sebesar ${rp(deficit)}. Pantau ketat arus kasmu minggu ini.`);
            }

            if (topCats.length > 0) {
                insights.push(`📈 <strong>Terkendali:</strong> <strong>${topCats[0][0]}</strong> adalah area pengeluaran paling aktif, mengambil porsi ${((topCats[0][1] / totalExp) * 100).toFixed(0)}% dari total pengeluaran.`);
            }

            if (prevExp > 0) {
                if (totalExp < prevExp) {
                    insights.push(`⭐ <strong>Tren Bagus:</strong> Pengeluaranmu lebih rendah ${((prevExp - totalExp) / prevExp * 100).toFixed(1)}% dibanding periode sebelumnya.`);
                }
            }
        }
        insightEl.innerHTML = `<ul>${insights.map(i => `<li>${i}</li>`).join('')}</ul>`;
    }

    // ── 5. Detailed Table ──
    renderLapTxTable(filteredTx);

    // Additional Charts if implemented in separate functions
    if (typeof window.renderNetWorthChart === 'function') window.renderNetWorthChart(filteredTx);
    if (typeof window.renderIncomeSourcesPanel === 'function') window.renderIncomeSourcesPanel(filteredTx);
}

// Ensure init uses global values when tabs load
function initReportsTab() {
    generateReport();
}

// Expose to window
window.setLapPeriod = setLapPeriod;
window.getLapActiveDateRange = getLapActiveDateRange;
window.renderLapTxTable = renderLapTxTable;
window.exportCSV = exportCSV;
window.printLaporan = printLaporan;
window.switchLapChart = switchLapChart;
window.generateReport = generateReport;
window.initReportsTab = initReportsTab;
