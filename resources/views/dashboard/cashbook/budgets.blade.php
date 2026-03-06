<div id="tab-anggaran" class="tab-pane">

            <!-- ══ LAYER 1: HEADER SUMMARY ══ -->
            <div class="ang-summary-card">
                <div class="ang-sum-title">
                    <span>Anggaran Bulan Ini</span>
                    <input type="month" class="ang-month-picker" id="ang-filter-month" onchange="loadAnggaran()">
                </div>
                
                <div class="ang-sum-amounts">
                    <div id="ang-sum-used">Rp 0</div>
                    <div class="ang-sum-divider">digunakan dari</div>
                    <div id="ang-sum-total">Rp 0</div>
                </div>

                <div class="ang-sum-progress-wrap">
                    <div class="ang-sum-progress-bar" id="ang-sum-bar"></div>
                </div>
                
                <div class="ang-sum-footer">
                    <div><span id="ang-sum-pct">0%</span> terpakai</div>
                    <div style="text-align:right">Sisa: <strong id="ang-sum-left">Rp 0</strong></div>
                </div>
            </div>

            <!-- ══ LAYER 2: BUDGET PROGRESS VISUAL ══ -->
            <div class="card" style="margin-bottom:16px;">
                <div class="card-head">
                    <span class="card-title">Progress Kategori</span>
                </div>
                <div class="card-body-sm" id="ang-progress-list">
                    <div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat progress...</div>
                </div>
            </div>

            <!-- ══ SMART INSIGHT (AI) ══ -->
            <div class="ang-insight-card" id="ang-smart-insight">
                <div class="ang-insight-icon"><i class="fas fa-lightbulb"></i></div>
                <div class="ang-insight-body">
                    <div class="ang-insight-title">Insight Bulan Ini</div>
                    <div class="ang-insight-text" id="ang-insight-text">Menganalisa pengeluaran...</div>
                </div>
            </div>

            <!-- ══ LAYER 3: CATEGORY BUDGET LIST ══ -->
            <div class="card">
                <div class="card-head" style="padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span class="card-title">Daftar Kategori</span>
                    <div style="display:flex;gap:8px;">
                        <button class="btn btn-ghost btn-sm" onclick="copyLastMonthBudget()" title="Copy budget bulan lalu">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body-sm" id="ang-category-list" style="padding:0">
                    <div class="empty" style="padding:32px 20px;"><i class="fas fa-folder-open"></i>Belum ada anggaran dibuat.<br><span style="font-size:12px;opacity:0.7;margin-top:6px;display:block;">Anggaran membantu kamu mengontrol pengeluaran.</span></div>
                </div>
            </div>

            <!-- ══ FLOATING ADD BUTTON ══ -->
            <button class="ang-fab" onclick="openModal('modal-budget')">
                <i class="fas fa-plus"></i> Tambah Anggaran
            </button>

        </div>
