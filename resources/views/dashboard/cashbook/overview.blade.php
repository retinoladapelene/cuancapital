<div id="tab-overview" class="tab-pane active">

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

        </div>
