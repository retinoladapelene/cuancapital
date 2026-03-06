<div id="tab-transaksi" class="tab-pane">

            <!-- ══ LAYER 1: QUICK ADD CTA ══ -->
            <div class="tx-quick-add">
                <button class="tx-qa-main" onclick="openModal('modal-transaction');prepTx('expense')">
                    <div class="tx-qa-plus"><i class="fas fa-plus"></i></div>
                    <div>
                        <div style="font-size:14px;font-weight:800;">Catat Transaksi</div>
                        <div style="font-size:11px;opacity:.8;margin-top:2px;">Tap untuk input cepat</div>
                    </div>
                </button>
                <div class="tx-qa-types">
                    <button class="tx-qa-type-btn income" onclick="openModal('modal-transaction');prepTx('income')">
                        <i class="fas fa-arrow-down-left"></i><span>Pemasukan</span>
                    </button>
                    <button class="tx-qa-type-btn expense" onclick="openModal('modal-transaction');prepTx('expense')">
                        <i class="fas fa-arrow-up-right"></i><span>Pengeluaran</span>
                    </button>
                    <button class="tx-qa-type-btn transfer" onclick="openModal('modal-transaction');prepTx('transfer')">
                        <i class="fas fa-arrow-right-arrow-left"></i><span>Transfer</span>
                    </button>
                </div>
            </div>

            <!-- ══ LAYER 2: FILTER & SEARCH ══ -->
            <div class="card tx-filter-card">
                <div class="tx-filter-row">
                    <div class="tx-search-wrap">
                        <i class="fas fa-magnifying-glass tx-search-icon"></i>
                        <input type="text" class="tx-search-input" id="tx-search" placeholder="Cari transaksi, nominal, catatan..." oninput="applyTxFilter()">
                    </div>
                    <div class="tx-filter-chips">
                        <div class="sel-wrap" style="flex:0 0 auto; width:140px;">
                            <select class="fselect" id="tx-filter-period" onchange="applyTxFilter()">
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="7d">7 Hari</option>
                                <option value="this_month" selected>Bulan Ini</option>
                                <option value="last_month">Bulan Lalu</option>
                                <option value="this_year">Tahun Ini</option>
                            </select>
                        </div>
                        <div class="sel-wrap" style="flex:0 0 auto; width:140px;">
                            <select class="fselect" id="tx-filter-type" onchange="applyTxFilter()">
                                <option value="all">Semua Tipe</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="sel-wrap" style="flex:0 0 auto; width:150px;">
                            <select class="fselect" id="tx-filter-pillar" onchange="applyTxFilter()">
                                <option value="all">Semua Pilar</option>
                                <option value="wajib">Wajib</option>
                                <option value="growth">Growth</option>
                                <option value="lifestyle">Lifestyle</option>
                                <option value="bocor">Bocor</option>
                            </select>
                        </div>
                        <button class="btn btn-ghost btn-sm" onclick="resetTxFilter()" title="Reset filter" style="flex-shrink:0;">
                            <i class="fas fa-rotate-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ══ LAYER 3: MONTHLY SUMMARY STRIP ══ -->
            <div class="tx-summary-strip" id="tx-summary-strip">
                <div class="tx-sum-card income-sum">
                    <div class="tx-sum-icon"><i class="fas fa-arrow-down-left"></i></div>
                    <div>
                        <div class="tx-sum-label">Pemasukan</div>
                        <div class="tx-sum-val pos" id="tx-sum-inc">Rp 0</div>
                    </div>
                </div>
                <div class="tx-sum-card expense-sum">
                    <div class="tx-sum-icon"><i class="fas fa-arrow-up-right"></i></div>
                    <div>
                        <div class="tx-sum-label">Pengeluaran</div>
                        <div class="tx-sum-val neg" id="tx-sum-exp">Rp 0</div>
                    </div>
                </div>
                <div class="tx-sum-card net-sum">
                    <div class="tx-sum-icon"><i class="fas fa-scale-balanced"></i></div>
                    <div>
                        <div class="tx-sum-label">Net Cashflow</div>
                        <div class="tx-sum-val" id="tx-sum-net">Rp 0</div>
                    </div>
                </div>
            </div>

            <!-- ══ LAYER 4: TRANSACTION LIST ══ -->
            <div class="card" style="overflow:hidden;">
                <div class="card-head" style="padding:16px 20px;">
                    <span class="card-title">
                        <i class="fas fa-list-ul" style="color:var(--accent);margin-right:8px;"></i>
                        <span id="tx-tab-info" style="font-size:13px;font-weight:700;color:var(--text);">Histori Transaksi</span>
                    </span>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button class="btn btn-ghost btn-sm" id="btn-tx-loadmore" style="display:none;" onclick="loadMoreTxTab()">
                            <i class="fas fa-chevron-down"></i> Muat Lagi
                        </button>
                        <button class="btn btn-accent btn-sm" onclick="openModal('modal-transaction');prepTx('expense')">
                            <i class="fas fa-plus"></i> Tambah
                        </button>
                    </div>
                </div>
                <!-- Transaction list rendered here — grouped by day -->
                <div id="tx-tab-tbody" style="padding:0 0 8px;">
                    <div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat data...</div>
                </div>
            </div>

        </div>
