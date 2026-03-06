<div id="tab-laporan" class="tab-pane">
            
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
                
                <!-- Financial Score -->
                <div class="lap-metric-card" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(16,185,129,0.05)); border: 1px solid rgba(59,130,246,0.2);">
                    <div class="lap-metric-icon" style="background:var(--accent);color:#fff;box-shadow:0 4px 10px rgba(16,185,129,0.3);"><i class="fas fa-star"></i></div>
                    <div class="lap-metric-body">
                        <div class="lap-metric-label">FinHealth Score</div>
                        <div class="lap-metric-val" id="lap-val-score" style="color:var(--text);font-size:22px;">0 <span style="font-size:12px;color:var(--text-muted);font-weight:600;">/ 100</span></div>
                    </div>
                </div>

                <!-- Pemasukan -->
                <div class="lap-metric-card">
                    <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div class="lap-metric-label" style="margin:0;"><i class="fas fa-arrow-down-long" style="color:#22c55e;margin-right:4px;"></i> Pemasukan</div>
                            <div id="lap-mom-income" class="lap-mom-badge">--</div>
                        </div>
                        <div class="lap-metric-val" id="lap-val-income" style="color:#22c55e;">Rp 0</div>
                    </div>
                </div>
                
                <!-- Pengeluaran -->
                <div class="lap-metric-card">
                    <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div class="lap-metric-label" style="margin:0;"><i class="fas fa-arrow-up-long" style="color:#ef4444;margin-right:4px;"></i> Pengeluaran</div>
                            <div id="lap-mom-expense" class="lap-mom-badge">--</div>
                        </div>
                        <div class="lap-metric-val" id="lap-val-expense" style="color:#ef4444;">Rp 0</div>
                    </div>
                </div>
                
                <!-- Net Profit -->
                <div class="lap-metric-card">
                    <div class="lap-metric-body" style="display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div class="lap-metric-label" style="margin:0;"><i class="fas fa-chart-line" style="color:#3b82f6;margin-right:4px;"></i> Profit / Net</div>
                            <div id="lap-mom-net" class="lap-mom-badge">--</div>
                        </div>
                        <div class="lap-metric-val" id="lap-val-net" style="color:#3b82f6;">Rp 0</div>
                    </div>
                </div>
                
                <!-- Saving Rate -->
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

            <!-- ══ LAYER 3: CASHFLOW TREND (FULL WIDTH) ══ -->
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
                
                <!-- Category Breakdown (Donut + Legend) -->
                <div class="card" style="display:flex; flex-direction:column;">
                    <div class="card-head">
                        <span class="card-title">Distribusi Pengeluaran</span>
                    </div>
                    <div class="card-body" style="padding-top:16px; flex:1; display:flex; flex-direction:column;">
                        <div style="height:170px;position:relative;margin-bottom:20px;">
                            <canvas id="lapDonutChart"></canvas>
                            <div id="lap-donut-empty" class="empty" style="display:none;position:absolute;inset:0;flex-direction:column;align-items:center;justify-content:center;background:var(--surface);"><i class="fas fa-chart-pie"></i>Belum ada data</div>
                        </div>
                        <div id="lap-donut-legend" style="display:flex;flex-direction:column;gap:8px;padding:0 10px;">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- Top Expenses (Horizontal Bars) -->
                <div class="card" style="display:flex; flex-direction:column;">
                    <div class="card-head">
                        <span class="card-title">Top 5 Kategori Aktif</span>
                    </div>
                    <div class="card-body" style="padding-top:16px; flex:1;">
                        <div id="lap-top-expenses" style="display:flex; flex-direction:column; gap:16px;">
                            <!-- Populated by JS -->
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
                        <input type="text" class="finput" id="lap-tx-search" placeholder="Cari..." style="width:140px;padding:6px 12px;font-size:12px;" onkeyup="requestAnimationFrame(() => renderLapTxTable(lapCurrentData.filteredTx))">
                        <button class="btn btn-ghost btn-sm" onclick="exportCSV()"><i class="fas fa-file-csv"></i> CSV</button>
                        <button class="btn btn-ghost btn-sm" onclick="exportData()"><i class="fas fa-file-code"></i> JSON</button>
                    </div>
                </div>
                <div style="overflow-x:auto;">
                    <table class="lap-tx-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th style="text-align:right;">Masuk</th>
                                <th style="text-align:right;">Keluar</th>
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

        </div>
