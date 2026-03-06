/**
 * Overview Tab Logic and Charts
 */

let _ovCashflowChart = null;

window.renderOverviewTab = function (container) {
    window.injectHTML(container, `
        <!-- ══ HERO: Balance + Score ══ -->
        <div class="ov-hero-card">
            <div class="ov-hero-top">
                <div>
                    <div class="ov-hero-label">Total Saldo</div>
                    <div class="ov-hero-balance" id="ov-total-balance">Rp --</div>
                    <div class="ov-hero-sub" id="ov-balance-sub">Di semua akun</div>

                    <!-- Hari Ini chip -->
                    <div style="display:inline-flex; align-items:center; gap:12px; margin-top:12px; background:var(--surface3); padding:8px 12px; border-radius:10px; border:1px solid var(--border);">
                        <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-right:1px solid var(--border); padding-right:10px;">Hari Ini</div>
                        <div style="display:flex; gap:12px;">
                            <div style="font-size:12px; font-weight:800; font-family:'JetBrains Mono', monospace; color:var(--accent);"><i class="fas fa-arrow-down-left" style="font-size:10px;margin-right:4px;"></i><span id="ov-today-in">0</span></div>
                            <div style="font-size:12px; font-weight:800; font-family:'JetBrains Mono', monospace; color:var(--danger);"><i class="fas fa-arrow-up-right" style="font-size:10px;margin-right:4px;"></i><span id="ov-today-out">0</span></div>
                        </div>
                    </div>
                </div>

                <!-- Financial Score badge -->
                <div class="ov-health-badge" id="ov-health-badge" style="padding:12px 16px; border-radius:16px; background:linear-gradient(135deg, rgba(34,197,94,0.15) 0%, rgba(34,197,94,0.05) 100%); border:1px solid rgba(34,197,94,0.3);">
                    <div style="font-size:10px; color:var(--accent); letter-spacing:0.5px; margin-bottom:2px; text-transform:uppercase;">Financial Score</div>
                    <div style="display:flex; align-items:baseline; gap:2px;">
                        <span id="ov-health-score" style="font-size:24px; font-family:'JetBrains Mono', monospace; font-weight:900; color:var(--accent); line-height:1;">--</span>
                        <span style="font-size:12px; font-family:'JetBrains Mono', monospace; font-weight:600; color:rgba(34,197,94,0.6);">/100</span>
                    </div>
                </div>
            </div>

            <!-- Saving rate progress bar -->
            <div style="margin-top:16px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text-muted);margin-bottom:6px;">
                    <span>Progress Saving Rate</span>
                    <span id="ov-sr-pct-label">Target 20%</span>
                </div>
                <div style="height:8px;background:var(--surface3);border-radius:4px;overflow:hidden;">
                    <div id="ov-sr-bar" style="height:100%;width:0%;background:var(--accent);border-radius:4px;transition:width .8s ease;"></div>
                </div>
            </div>
        </div>

        <!-- ══ Account strip ══ -->
        <div class="ov-accounts-row" id="accounts-bar">
            <div class="acc-add-chip" onclick="openModal('modal-add-account')">
                <i class="fas fa-plus" style="font-size:12px"></i> Tambah Akun
            </div>
        </div>

        <!-- ══ KPI GRID (4 cards) ══ -->
        <div class="kpi-grid">
            <div class="kpi-card primary">
                <div class="kpi-label"><i class="fas fa-arrow-trend-up" style="color:var(--accent);margin-right:6px;"></i>Pemasukan</div>
                <div class="kpi-value pos" id="ov-income">--</div>
                <div class="kpi-sub">Bulan ini</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label"><i class="fas fa-arrow-trend-down" style="color:var(--danger);margin-right:6px;"></i>Pengeluaran</div>
                <div class="kpi-value" id="ov-expense" style="color:var(--danger);">--</div>
                <div class="kpi-sub">Bulan ini</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label"><i class="fas fa-piggy-bank" style="color:var(--info);margin-right:6px;"></i>Saving Rate</div>
                <div class="kpi-value" id="ov-saving-rate">--%</div>
                <div class="kpi-sub">Target 20%</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label"><i class="fas fa-clock" style="color:var(--warning);margin-right:6px;"></i>Runway</div>
                <div class="kpi-value" id="ov-runway">-- bln</div>
                <div class="kpi-sub">Dana darurat</div>
            </div>
        </div>

        <!-- ══ CHART GRID: Cashflow (2fr) + Donut (1fr) ══ -->
        <div class="chart-grid">
            <!-- Cashflow Trend -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title"><i class="fas fa-chart-area" style="color:var(--accent);"></i>Arus Kas 30 Hari</span>
                    <span id="ov-trend-net" style="font-size:12px;font-weight:700;color:var(--accent);"></span>
                </div>
                <div class="section-body card" style="padding:20px;">
                    <div style="height:220px;position:relative;">
                        <canvas id="ov-cashflow-chart"></canvas>
                        <div id="ov-chart-empty" class="empty" style="display:none;position:absolute;inset:0;flex-direction:column;align-items:center;justify-content:center;">
                            <i class="fas fa-chart-area"></i>Belum ada data transaksi
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spending Distribution Donut -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title"><i class="fas fa-chart-pie" style="color:var(--warning);"></i>Distribusi</span>
                    <span style="font-size:11px;color:var(--text-muted);">Bulan ini</span>
                </div>
                <div class="section-body card" id="ov-spending-dist" style="padding:20px;min-height:200px;">
                    <div class="empty"><i class="fas fa-chart-pie"></i>Belum ada pengeluaran</div>
                </div>
            </div>
        </div>

        <!-- ══ SPLIT GRID: Smart Insights + Quick Actions ══ -->
        <div class="split-grid">
            <!-- Smart Insights -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title"><i class="fas fa-lightbulb" style="color:var(--purple);"></i>Smart Insights</span>
                </div>
                <div class="section-body" id="ov-insights">
                    <div class="empty"><i class="fas fa-lightbulb"></i>Catat beberapa transaksi untuk insight</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title"><i class="fas fa-bolt" style="color:var(--warning);"></i>Aksi Cepat</span>
                </div>
                <div class="ov-qa-grid">
                    <button class="ov-qa-btn" onclick="openModal('modal-transaction');prepTx()">
                        <div class="ov-qa-icon" style="background:rgba(34,197,94,.15);color:var(--accent);"><i class="fas fa-plus"></i></div>
                        <span>Tambah Transaksi</span>
                    </button>
                    <button class="ov-qa-btn" onclick="switchMainTab('debts');setTimeout(()=>{let btn=document.querySelector('.btn-accent[onclick*=payInstallment]');if(btn)btn.click()},300)">
                        <div class="ov-qa-icon" style="background:rgba(59,130,246,.15);color:var(--info);"><i class="fas fa-hand-holding-dollar"></i></div>
                        <span>Bayar Cicilan</span>
                    </button>
                    <button class="ov-qa-btn" onclick="switchMainTab('anggaran')">
                        <div class="ov-qa-icon" style="background:rgba(245,158,11,.15);color:var(--warning);"><i class="fas fa-sliders"></i></div>
                        <span>Set Budget</span>
                    </button>
                    <button class="ov-qa-btn" onclick="switchMainTab('laporan')">
                        <div class="ov-qa-icon" style="background:rgba(139,92,246,.15);color:var(--purple);"><i class="fas fa-chart-bar"></i></div>
                        <span>Lihat Laporan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ══ RECENT TRANSACTIONS ══ -->
        <div class="section">
            <div class="section-header">
                <span class="section-title"><i class="fas fa-receipt" style="color:var(--info);"></i>Transaksi Terbaru</span>
                <button class="btn btn-ghost btn-sm" onclick="openModal('modal-transaction');prepTx()"><i class="fas fa-plus"></i> Tambah</button>
            </div>
            <div class="section-body card" id="ov-recent-tx">
                <div class="empty"><i class="fas fa-receipt"></i>Belum ada transaksi</div>
            </div>
        </div>
    `);

    // Call initialization after HTML is mounted
    setTimeout(() => { if (typeof window.loadDashboard === 'function') window.loadDashboard(); }, 1);

    // CLEANUP FUNCTION (Returns to router)
    return function cleanup() {
        if (window._ovCashflowChart && typeof window._ovCashflowChart.destroy === 'function') {
            window._ovCashflowChart.destroy();
            window._ovCashflowChart = null;
        }
        if (window.ovObserver) {
            window.ovObserver.disconnect();
            window.ovObserver = null;
        }
        window._ovInitDone = false;
    };
};
async function loadDashboard() {
    try {
        const now = new Date();
        const yr = now.getFullYear(), mo = now.getMonth() + 1;

        // ── Layer 1: Hero Metrics ──
        const totalBalance = (accounts || []).reduce((s, a) => s + parseFloat(a.balance_cached || 0), 0);
        const ovBalEl = document.getElementById('ov-total-balance');
        if (ovBalEl) ovBalEl.textContent = rp(totalBalance);

        const ovBalSubEl = document.getElementById('ov-balance-sub');
        if (ovBalSubEl) ovBalSubEl.textContent = `${(accounts || []).length} akun aktif`;

        let incThisMonth = 0, expThisMonth = 0;
        let todayIn = 0, todayOut = 0;
        // Shift to local time for "today" string
        const todayStr = new Date(new Date().getTime() - (new Date().getTimezoneOffset() * 60000)).toISOString().slice(0, 10);
        const catSpend = {};
        const monthlyByDay = {};

        (allTxList || []).forEach(tx => {
            const txD = new Date(tx.transaction_date);
            const txY = txD.getFullYear(), txM = txD.getMonth() + 1;
            const dKey = tx.transaction_date?.slice(0, 10);

            if (dKey) {
                if (!monthlyByDay[dKey]) monthlyByDay[dKey] = { inc: 0, exp: 0 };
                if (tx.type === 'income') {
                    monthlyByDay[dKey].inc += parseFloat(tx.amount || 0);
                    if (dKey === todayStr) todayIn += parseFloat(tx.amount || 0);
                }
                if (tx.type === 'expense') {
                    monthlyByDay[dKey].exp += parseFloat(tx.amount || 0);
                    if (dKey === todayStr) todayOut += parseFloat(tx.amount || 0);
                }
            }
            if (txY === yr && txM === mo) {
                const amt = parseFloat(tx.amount || 0);
                if (tx.type === 'income') incThisMonth += amt;
                if (tx.type === 'expense') {
                    expThisMonth += amt;
                    const cid = tx.category_id || 'other';
                    catSpend[cid] = (catSpend[cid] || 0) + amt;
                }
            }
        });

        // ── Render Account Cards Strip ──
        const accBar = document.getElementById('accounts-bar');
        if (accBar && accounts && accounts.length) {
            const addBtnHtml = `<div class="acc-add-chip" onclick="openModal('modal-add-account')"><i class="fas fa-plus" style="font-size:12px"></i> Tambah Akun</div>`;
            const cardsHtml = accounts.map(a => {
                let ic = 'fa-wallet';
                if (a.type?.toLowerCase().includes('cash')) ic = 'fa-money-bill-wave';
                if (a.type?.toLowerCase().includes('ewallet')) ic = 'fa-mobile-screen';
                return `
                <div class="acc-card" onclick="openModal('modal-account')">
                    <div style="font-size:18px; color:var(--accent); margin-bottom:8px;"><i class="fas ${ic}"></i></div>
                    <div class="acc-name">${a.name}</div>
                    <div class="acc-bal">${rp(a.balance_cached || 0)}</div>
                </div>
            `}).join('');
            accBar.innerHTML = cardsHtml + addBtnHtml;
        }

        // Remove skeleton loading from above-the-fold cards
        document.querySelectorAll('.ov-hero-card.skeleton, .kpi-card.skeleton, .metric-card.skeleton').forEach(el => el.classList.remove('skeleton'));

        const nc = incThisMonth - expThisMonth;
        const savingRate = incThisMonth > 0 ? Math.max(0, (nc / incThisMonth) * 100) : 0;
        const runway = expThisMonth > 0 ? Math.floor(totalBalance / expThisMonth) : (totalBalance > 0 ? 99 : 0);

        const setEl = (id, text) => { const el = document.getElementById(id); if (el) el.textContent = text; };

        setEl('ov-income', rp(incThisMonth));
        setEl('ov-expense', rp(expThisMonth));

        const srEl = document.getElementById('ov-saving-rate');
        if (srEl) {
            srEl.textContent = `${savingRate.toFixed(1)}%`;
            srEl.className = 'ov-hmetric-val' + (savingRate >= 20 ? ' pos' : savingRate >= 10 ? '' : ' neg');
        }
        setEl('ov-runway', `${runway > 99 ? '99+' : runway} bln`);

        // saving rate progress bar
        const srBar = document.getElementById('ov-sr-bar');
        if (srBar) {
            const srPct = Math.min(100, savingRate / 20 * 100);
            srBar.style.width = srPct + '%';
            srBar.style.background = savingRate >= 20 ? 'var(--accent)' : savingRate >= 10 ? 'var(--warning)' : 'var(--danger)';
        }
        setEl('ov-sr-pct-label', `${savingRate.toFixed(1)}% (Target 20%)`);

        // Today Check
        setEl('ov-today-in', rp(todayIn));
        setEl('ov-today-out', rp(todayOut));

        // Health badge gamification UI
        let hs = 50;
        if (savingRate >= 20) hs += 20; else if (savingRate > 0) hs += 10;
        if (runway >= 6) hs += 20; else if (runway >= 3) hs += 10;
        if (nc > 0) hs += 10;
        hs = Math.min(100, Math.max(0, hs));

        const healthColor = hs >= 80 ? 'var(--accent)' : hs >= 60 ? 'var(--info)' : hs >= 40 ? 'var(--warning)' : 'var(--danger)';
        const badge = document.getElementById('ov-health-badge');

        if (badge) {
            badge.style.background = `linear-gradient(135deg, ${healthColor.replace('var(', 'rgba(').replace(')', ', .15)')} 0%, ${healthColor.replace('var(', 'rgba(').replace(')', ', .02)')} 100%)`;
            badge.style.borderColor = healthColor.replace('var(', 'rgba(').replace(')', ', .3)');
            if (badge.firstElementChild) badge.firstElementChild.style.color = healthColor;
        }

        const scoreEl = document.getElementById('ov-health-score');
        if (scoreEl) {
            scoreEl.textContent = hs;
            scoreEl.style.color = healthColor;
            if (scoreEl.nextElementSibling) scoreEl.nextElementSibling.style.color = healthColor.replace('var(', 'rgba(').replace(')', ', .6)');
        }

        // Expose render function for IntersectionObserver to call
        window.renderChartsAndAnalytics = async function () {
            // ── Lazy Load Chart.js if needed ──
            if (typeof window.Chart === 'undefined') {
                try {
                    await window.loadModule('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js');
                    Chart.defaults.animation = false;
                    Chart.defaults.animations = { colors: false, x: false };
                    Chart.defaults.transitions = { active: { animation: { duration: 0 } } };
                } catch (e) { console.error('Gagal load Chart.js', e); return; }
            }

            // ── Layer 2: 30-day cashflow area chart ──
            const days30 = [];
            for (let i = 29; i >= 0; i--) {
                const d = new Date(now); d.setDate(d.getDate() - i);
                days30.push(d.toISOString().slice(0, 10));
            }
            const incData = days30.map(d => (monthlyByDay[d]?.inc || 0));
            const expData = days30.map(d => (monthlyByDay[d]?.exp || 0));
            const hasAnyData = incData.some(v => v > 0) || expData.some(v => v > 0);

            const emptyEl = document.getElementById('ov-chart-empty');
            if (emptyEl) emptyEl.style.display = hasAnyData ? 'none' : 'flex';

            const canvasObj = document.getElementById('ov-cashflow-chart');
            if (canvasObj) {
                const ctxOv = canvasObj.getContext('2d');

                if (hasAnyData) {
                    const mappedLabels = days30.map(d => { const dObj = new Date(d); return dObj.getDate() + ' ' + dObj.toLocaleString('id-ID', { month: 'short' }) });

                    if (window._ovCashflowChart && typeof window._ovCashflowChart.update === 'function') {
                        window._ovCashflowChart.data.labels = mappedLabels;
                        window._ovCashflowChart.data.datasets[0].data = incData;
                        window._ovCashflowChart.data.datasets[1].data = expData;
                        window._ovCashflowChart.update();
                    } else {
                        if (window._ovCashflowChart && typeof window._ovCashflowChart.destroy === 'function') window._ovCashflowChart.destroy();
                        const gInc = ctxOv.createLinearGradient(0, 0, 0, 160);
                        gInc.addColorStop(0, 'rgba(16,185,129,.25)'); gInc.addColorStop(1, 'rgba(16,185,129,0)');
                        const gExp = ctxOv.createLinearGradient(0, 0, 0, 160);
                        gExp.addColorStop(0, 'rgba(239,68,68,.18)'); gExp.addColorStop(1, 'rgba(239,68,68,0)');

                        window._ovCashflowChart = new Chart(ctxOv, {
                            type: 'line',
                            data: {
                                labels: mappedLabels,
                                datasets: [
                                    { label: 'In', data: incData, borderColor: '#10b981', backgroundColor: gInc, fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                                    { label: 'Out', data: expData, borderColor: '#ef4444', backgroundColor: gExp, fill: true, tension: 0.4, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 }
                                ]
                            },
                            options: {
                                animation: false, // Performance
                                responsive: true, maintainAspectRatio: false,
                                interaction: { mode: 'index', intersect: false },
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(15,23,42,.95)', callbacks: { label: c => ' ' + rp(c.raw) } } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(100,116,139,.07)' }, ticks: { color: '#64748b', font: { size: 9 }, callback: v => (v >= 1000000 ? (v / 1000000) + 'M' : v >= 1000 ? (v / 1000) + 'K' : v) } },
                                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 9 }, maxTicksLimit: 6 } }
                                }
                            }
                        });
                    }
                } else {
                    if (window._ovCashflowChart && typeof window._ovCashflowChart.destroy === 'function') {
                        window._ovCashflowChart.destroy();
                        window._ovCashflowChart = null;
                    }
                }
            }

            // ── Layer 3: Distribution Bar ──
            const catArr = Object.entries(catSpend)
                .map(([id, amt]) => ({ id, name: categories.find(c => String(c.id) === String(id))?.name || 'Lainnya', amt, color: (CAT_ICONS[categories.find(c => String(c.id) === String(id))?.name] || DEFAULT_ICON)[1] }))
                .sort((a, b) => b.amt - a.amt);

            const barW = document.getElementById('ov-dist-bar');
            const legW = document.getElementById('ov-dist-leg');
            if (barW && legW) {
                if (!catArr.length) {
                    barW.innerHTML = '<div style="width:100%;height:100%;background:var(--surface2);border-radius:4px;"></div>';
                    legW.innerHTML = '<div class="empty" style="font-size:11px;padding:10px 0;"><i class="fas fa-receipt"></i>Belum ada pengeluaran</div>';
                } else {
                    const top3 = catArr.slice(0, 3);
                    const othersAmt = catArr.slice(3).reduce((s, c) => s + c.amt, 0);
                    if (othersAmt > 0) top3.push({ id: 'other', name: 'Lainnya', amt: othersAmt, color: '#64748b' });

                    const tExp = top3.reduce((s, c) => s + c.amt, 0);
                    barW.innerHTML = top3.map((c, i) => {
                        const rCls = i === 0 ? 'border-radius:4px 0 0 4px;' : i === top3.length - 1 ? 'border-radius:0 4px 4px 0;' : '';
                        return `<div style="height:100%; width:${(c.amt / tExp) * 100}%; background:${c.color}; ${rCls}" title="${c.name}: ${rp(c.amt)}"></div>`;
                    }).join('');

                    legW.innerHTML = top3.map(c => `
                        <div class="ov-dist-item">
                            <div class="ov-dist-dot" style="background:${c.color}"></div>
                            <span class="ov-dist-name" style="color:var(--text);font-weight:600;">${c.name}</span>
                            <span class="ov-dist-pct" style="color:var(--text-muted);font-weight:600;font-size:10px;">${Math.round((c.amt / tExp) * 100)}%</span>
                        </div>
                    `).join('');
                }
            }

            // ── Layer 4: AI Insights ──
            const insEl = document.getElementById('ov-insight-list');
            if (insEl) {
                let msgs = [];
                if (savingRate >= 20) msgs.push(`🔥 <strong>Sehat:</strong> Saving rate kamu aman di angka ${savingRate.toFixed(1)}%.`);
                else if (savingRate > 0) msgs.push(`💡 <strong>Saran:</strong> Coba tekan pengeluaran tersier untuk menaikkan saving rate jadi >20%.`);
                else if (expThisMonth > 0) msgs.push(`⚠️ <strong>Defisit:</strong> Pengeluaranmu lebih besar dari pemasukan bulan ini.`);
                if (catArr.length) msgs.push(`🛒 <strong>Highlight:</strong> Pengeluaran terbesarmu bulan ini ada di <strong>${catArr[0].name}</strong> (${rp(catArr[0].amt)}).`);

                // Get debts from API to show insight if needed
                let totalPayable = 0;
                let alldbts = [];
                try {
                    alldbts = await localAPI.debts.getAll();
                    totalPayable = alldbts.filter(d => d.status !== 'paid_off' && d.debt_type === 'payable').reduce((sum, d) => sum + (parseFloat(d.total_amount || 0) - parseFloat(d.paid_amount || 0)), 0);
                } catch (e) { }

                if (totalPayable > totalBalance) msgs.push(`🚨 <strong>Kritis:</strong> Total hutang aktifmu (${rp(totalPayable)}) melebihi total kas di tangan. Atur strategi pelunasan sekarang!`);

                if (!msgs.length) msgs.push("Mulai catat transaksi untuk mendapatkan insight otomatis.");
                insEl.innerHTML = msgs.map(m => `<li>${m}</li>`).join('');
            }

            // Remove remaining skeletons
            document.querySelectorAll('.skeleton').forEach(el => el.classList.remove('skeleton'));
        };

        // If IntersectionObserver is active (from main file), it will call renderChartsAndAnalytics() automatically.
        // Otherwise, we call it manually if it's not set up.
        if (typeof ovObserver === 'undefined') {
            renderChartsAndAnalytics();
        }

        // ── Recent Transactions (Always render fast) ──
        const rWrap = document.getElementById('ov-recent-tx');
        if (rWrap) {
            const recent = (allTxList || []).slice(0, 5);
            if (!recent.length) {
                rWrap.innerHTML = '<div class="empty"><i class="fas fa-receipt"></i>Belum ada transaksi</div>';
            } else {
                rWrap.innerHTML = recent.map(tx => {
                    const isInc = tx.type === 'income', isExp = tx.type === 'expense', isT = tx.type === 'transfer';
                    const s = isInc ? '+' : isExp ? '-' : '⇌';
                    const cN = tx.category?.name || (isT ? 'Transfer' : '-');
                    const aN = (accounts || []).find(a => a.id === tx.account_id)?.name || '-';
                    const d = new Date(tx.transaction_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
                    const bg = isInc ? 'rgba(16,185,129,.15)' : isT ? 'rgba(59,130,246,.15)' : 'rgba(239,68,68,.15)';
                    const col = isInc ? 'var(--accent)' : isT ? 'var(--info)' : 'var(--danger)';
                    const ic = tx.category?.icon ? tx.category.icon : (isInc ? 'fa-arrow-down-left' : isT ? 'fa-arrow-right-arrow-left' : 'fa-arrow-up-right');
                    return `
                    <div class="ov-tx-row" onclick="typeof editTx === 'function' ? editTx('${tx.id}') : null">
                        <div class="ov-tx-icon" style="background:${bg};color:${col};"><i class="fas ${ic}"></i></div>
                        <div class="ov-tx-body">
                            <div class="ov-tx-name">${tx.note || '<em style="opacity:.5">Tanpa Catatan</em>'}</div>
                            <div class="ov-tx-meta">${cN} &bull; ${d}</div>
                        </div>
                        <div class="ov-tx-right">
                            <div class="ov-tx-amt" style="color:${col}">${s}${rp(tx.amount)}</div>
                            <div class="ov-tx-acct">${aN}</div>
                        </div>
                    </div>`;
                }).join('');
            }
        }

    } catch (err) {
        console.warn('loadDashboard error', err);
    }
}

// Expose globally
window.loadDashboard = loadDashboard;
