<!-- ─── MODAL: Panduan Cashbook ─── -->
    <div class="modal-bg" id="modal-panduan" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()" style="max-width:550px">
            <div class="modal-head">
                <span class="modal-title"><i class="fas fa-book" style="color:var(--accent);margin-right:8px"></i>Panduan Cashbook</span>
                <button class="modal-close" onclick="closeModal('modal-panduan')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="max-height:60vh; overflow-y:auto; line-height:1.6; font-size:13px; color:var(--text);">
                <div style="margin-bottom:16px;">
                    <strong>Fungsi Cashbook</strong><br>
                    Cashbook (Buku Kas) adalah fitur untuk memantau, mencatat, dan mengelola seluruh arus kas masuk dan keluar secara disiplin dan akurat.
                </div>
                
                <div style="margin-bottom:16px;">
                    <strong><i class="fas fa-house" style="color:var(--accent);width:16px;"></i> Tab Overview</strong><br>
                    Ringkasan eksekutif keuangan Anda. Menampilkan Total Saldo, Financial Score, progress Saving Rate, skor Runway (Dana Darurat), serta grafik distribusi arus kas 30 hari terakhir.
                </div>
                
                <div style="margin-bottom:16px;">
                    <strong><i class="fas fa-receipt" style="color:var(--accent);width:16px;"></i> Tab Transaksi</strong><br>
                    Buku besar cerdas (Smart Ledger) untuk mencatat semua transaksi harian. Anda dapat memfilter berdasarkan tanggal/tipe, mencari transaksi spesifik, mengedit, menduplikasi, maupun menghapus catatan.
                </div>
                
                <div style="margin-bottom:16px;">
                    <strong><i class="fas fa-chart-bar" style="color:var(--accent);width:16px;"></i> Tab Laporan</strong><br>
                    Pusat analisa riwayat keuangan. Menampilkan tren arus kas yang dapat di-*toggle* (Line vs Bar chart), rincian Metrik Keuangan (Month-over-month), dan distribusi pengeluaran top 5 kategori.
                </div>

                <div style="margin-bottom:16px;">
                    <strong><i class="fas fa-credit-card" style="color:var(--accent);width:16px;"></i> Tab Utang</strong><br>
                    Manajemen utang dan piutang. Anda dapat melacak sisa pinjaman, membayar cicilan, serta melihat persentase rasio utang (Debt-to-Asset Ratio) terhadap total aset Anda.
                </div>
                
                <div style="margin-bottom:16px;">
                    <strong><i class="fas fa-sliders" style="color:var(--accent);width:16px;"></i> Tab Anggaran</strong><br>
                    Sistem kendali keuangan. Di sini Anda menetapkan limit maksimal pengeluaran (budget) per kategori. Tab ini akan memberikan insight jika pengeluaran hampir mendekati atau melewati batas limit.
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn btn-accent" style="width:100%" onclick="closeModal('modal-panduan')">Mengerti</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Add Debt ─── -->
    <div class="modal-bg" id="modal-add-debt" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()" style="max-width:440px">
            <div class="modal-head">
                <span class="modal-title"><i class="fas fa-hand-holding-dollar" style="color:var(--danger);margin-right:8px"></i>Tambah Utang / Cicilan</span>
                <button class="modal-close" onclick="closeModal('modal-add-debt')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="fg">
                    <label class="flabel">Nama Utang</label>
                    <input class="finput" id="debt-name" type="text" placeholder="cth: KPR BCA, Hutang ke Budi...">
                </div>
                <div class="fg">
                    <label class="flabel">Tipe</label>
                    <div class="sel-wrap">
                        <select class="fselect" id="debt-type">
                            <option value="payable">Utang (Saya yang berutang)</option>
                            <option value="receivable">Piutang (Orang lain yang berutang ke saya)</option>
                        </select>
                    </div>
                </div>
                <div class="fg">
                    <label class="flabel">Total Jumlah</label>
                    <input class="finput" id="debt-total" type="number" placeholder="0" min="0">
                </div>
                <div class="fg">
                    <label class="flabel">Tanggal Jatuh Tempo (opsional)</label>
                    <input class="finput" id="debt-due" type="date">
                </div>
                <div class="fg">
                    <label class="flabel">Catatan (opsional)</label>
                    <textarea class="finput" id="debt-notes" rows="2" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn btn-ghost" onclick="closeModal('modal-add-debt')">Batal</button>
                <button class="btn btn-accent" onclick="submitDebt()"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Pay Installment ─── -->
    <div class="modal-bg" id="modal-pay-installment" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()" style="max-width:400px">
            <div class="modal-head">
                <span class="modal-title"><i class="fas fa-money-bill-wave" style="color:var(--accent);margin-right:8px"></i>Bayar Cicilan</span>
                <button class="modal-close" onclick="closeModal('modal-pay-installment')"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inst-debt-id">
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;" id="inst-debt-name-display"></div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:16px;" id="inst-debt-remaining-display"></div>
                <div class="fg">
                    <label class="flabel">Bayar dari Akun</label>
                    <div class="sel-wrap">
                        <select class="fselect" id="inst-account-id">
                            <option value="">-- Pilih akun --</option>
                        </select>
                    </div>
                    <div id="inst-account-balance" style="font-size:11px;color:var(--text-muted);margin-top:4px;"></div>
                </div>
                <div class="fg">
                    <label class="flabel">Jumlah Cicilan</label>
                    <input class="finput" id="inst-amount" type="number" placeholder="0" min="0.01">
                </div>
                <div class="fg">
                    <label class="flabel">Tanggal Bayar</label>
                    <input class="finput" id="inst-date" type="date">
                </div>
                <div class="fg">
                    <label class="flabel">Catatan (opsional)</label>
                    <input class="finput" id="inst-notes" type="text" placeholder="Catatan pembayaran...">
                </div>
            </div>
            <div class="modal-foot">
                <button class="btn btn-ghost" onclick="closeModal('modal-pay-installment')">Batal</button>
                <button class="btn btn-accent" onclick="submitInstallment()"><i class="fas fa-paper-plane"></i> Bayar</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Transaction ─── -->
    <div class="modal-bg" id="modal-transaction" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-head">
                <span class="modal-title">Tambah Transaksi</span>
                <button class="modal-x" onclick="closeModal('modal-transaction')">&times;</button>
            </div>
            <div class="type-tabs">
                <button class="ttab i" id="tab-income" onclick="setType('income')">Pemasukan</button>
                <button class="ttab" id="tab-expense" onclick="setType('expense')">Pengeluaran</button>
                <button class="ttab" id="tab-transfer" onclick="setType('transfer')">Transfer</button>
            </div>
            <div id="tx-single-acc" class="fg">
                <label class="flabel">Akun</label>
                <div class="sel-wrap"><select class="fselect" id="tx-account-id"></select></div>
            </div>
            <div id="tx-transfer-box" style="display:none">
                <div class="fg"><label class="flabel">Dari Akun</label>
                    <div class="sel-wrap"><select class="fselect" id="tx-from-acc"></select></div>
                </div>
                <div class="fg"><label class="flabel">Ke Akun</label>
                    <div class="sel-wrap"><select class="fselect" id="tx-to-acc"></select></div>
                </div>
            </div>
            <div class="fg" id="tx-cat-wrap">
                <label class="flabel" id="cat-label">Kategori</label>
                <input type="hidden" id="tx-category-id" value="">
                <div class="cdd" id="cdd-category">
                    <div class="cdd-trigger" onclick="toggleCdd('cdd-category')">
                        <div class="cdd-trigger-icon" id="cdd-cat-icon"><i class="fas fa-tag"
                                style="color:var(--text-muted)"></i></div>
                        <span class="cdd-trigger-lbl placeholder" id="cdd-cat-lbl">Tanpa kategori</span>
                        <i class="fas fa-chevron-down cdd-arrow"></i>
                    </div>
                    <div class="cdd-menu" id="cdd-category-menu"></div>
                </div>
            </div>
            <div class="fg"><label class="flabel">Jumlah (Rp)</label><input type="number" class="finput" id="tx-amount"
                    placeholder="0" min="0"></div>
            <div class="fg"><label class="flabel">Tanggal</label><input type="date" class="finput" id="tx-date"></div>
            <div class="fg"><label class="flabel">Keterangan</label><input type="text" class="finput" id="tx-note"
                    placeholder="Opsional..." maxlength="500"></div>
            <div class="form-actions">
                <button class="btn btn-ghost" onclick="closeModal('modal-transaction')">Batal</button>
                <button class="btn btn-accent" id="btn-save-tx" onclick="submitTransaction()"><i
                        class="fas fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Add Account ─── -->
    <div class="modal-bg" id="modal-add-account" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-head">
                <span class="modal-title">Tambah Akun</span>
                <button class="modal-x" onclick="closeModal('modal-add-account')">&times;</button>
            </div>
            <div class="fg"><label class="flabel">Nama Akun</label><input type="text" class="finput" id="acc-name"
                    placeholder="BCA, Dana, Dompet..." maxlength="50"></div>
            <div class="fg"><label class="flabel">Tipe</label>
                <div class="sel-wrap">
                    <select class="fselect" id="acc-type">
                        <option value="bank"> Bank</option>
                        <option value="cash"> Tunai (Cash)</option>
                        <option value="ewallet"> E-Wallet</option>
                    </select>
                </div>
            </div>
            <div class="fg"><label class="flabel">Saldo Awal (Rp)</label><input type="number" class="finput"
                    id="acc-initial" placeholder="0" min="0"></div>
            <div class="form-actions">
                <button class="btn btn-ghost" onclick="closeModal('modal-add-account')">Batal</button>
                <button class="btn btn-accent" onclick="submitAccount()"><i class="fas fa-check"></i> Buat Akun</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Manage Accounts ─── -->
    <div class="modal-bg" id="modal-account" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-head">
                <span class="modal-title">Kelola Akun</span>
                <button class="modal-x" onclick="closeModal('modal-account')">&times;</button>
            </div>
            <div id="acc-modal-list">
                <p style="color:var(--text-muted);font-size:13px">Memuat...</p>
            </div>
            <div class="form-actions">
                <button class="btn btn-accent" onclick="closeModal('modal-account');openModal('modal-add-account')"><i
                        class="fas fa-plus"></i> Akun Baru</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Budget ─── -->
    <div class="modal-bg" id="modal-budget" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-head">
                <span class="modal-title">Set Budget Bulanan</span>
                <button class="modal-x" onclick="closeModal('modal-budget')">&times;</button>
            </div>
            <div class="fg"><label class="flabel">Kategori Pengeluaran</label>
                <input type="hidden" id="bgt-cat-val" value="">
                <div class="cdd" id="cdd-budget">
                    <div class="cdd-trigger" onclick="toggleCdd('cdd-budget')">
                        <div class="cdd-trigger-icon" id="cdd-bgt-icon"><i class="fas fa-tag"
                                style="color:var(--text-muted)"></i></div>
                        <span class="cdd-trigger-lbl placeholder" id="cdd-bgt-lbl">Pilih kategori...</span>
                        <i class="fas fa-chevron-down cdd-arrow"></i>
                    </div>
                    <div class="cdd-menu" id="cdd-budget-menu"></div>
                </div>
            </div>
            <div class="fg"><label class="flabel">Bulan</label><input type="month" class="finput" id="bgt-month"></div>
            <div class="fg"><label class="flabel">Limit (Rp)</label><input type="number" class="finput" id="bgt-limit"
                    placeholder="0" min="0"></div>
            <div class="form-actions">
                <button class="btn btn-ghost" onclick="closeModal('modal-budget')">Batal</button>
                <button class="btn btn-accent" onclick="submitBudget()"><i class="fas fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>


    <!-- ─── MODAL: Edit Budget ─── -->
    <div class="modal-bg" id="modal-edit-budget" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-head">
                <span class="modal-title"><i class="fas fa-pen" style="margin-right:8px;color:var(--info);"></i>Edit Budget</span>
                <button class="modal-x" onclick="closeModal('modal-edit-budget')">&times;</button>
            </div>
            <input type="hidden" id="edit-bgt-id" value="">
            <div class="fg">
                <label class="flabel">Kategori</label>
                <div id="edit-bgt-cat-name" style="padding:10px 14px; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius-sm); font-size:14px; font-weight:600; color:var(--text);">-</div>
            </div>
            <div class="fg"><label class="flabel">Bulan</label><input type="month" class="finput" id="edit-bgt-month"></div>
            <div class="fg"><label class="flabel">Limit Baru (Rp)</label><input type="number" class="finput" id="edit-bgt-limit" placeholder="0" min="0"></div>
            <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;margin-bottom:16px;font-size:12px;color:var(--text-muted);">
                <i class="fas fa-info-circle" style="margin-right:6px;color:var(--info);"></i>Mengubah limit akan mempengaruhi rekomendasi dan sisa budget yang ditampilkan.
            </div>
            <div class="form-actions">
                <button class="btn btn-ghost" onclick="closeModal('modal-edit-budget')">Batal</button>
                <button class="btn btn-accent" onclick="submitEditBudget()"><i class="fas fa-check"></i> Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Custom Confirm Dialog ─── -->
    <div class="modal-bg" id="modal-confirm" onclick="">
        <div class="modal" onclick="event.stopPropagation()" style="max-width:380px">
            <div class="modal-head" style="margin-bottom:14px">
                <div style="display:flex;align-items:center;gap:12px">
                    <div id="confirm-icon-wrap" style="width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i id="confirm-icon" class="fas fa-circle-question" style="font-size:16px"></i>
                    </div>
                    <span class="modal-title" id="confirm-title" style="font-size:15px">Konfirmasi</span>
                </div>
            </div>
            <p id="confirm-message" style="font-size:14px;color:var(--text-sub);line-height:1.65;margin-bottom:24px"></p>
            <div class="form-actions">
                <button class="btn btn-ghost" id="confirm-cancel" onclick="closeModal('modal-confirm')">Batal</button>
                <button class="btn" id="confirm-ok">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL: Data & Backup ─── -->
    <div class="modal-bg" id="modal-data" onclick="closeOut(this)">
        <div class="modal" onclick="event.stopPropagation()" style="max-width:440px;">
            <div class="modal-head">
                <span class="modal-title"><i class="fas fa-database" style="color:var(--accent);margin-right:8px;"></i>Data &amp; Backup</span>
                <button class="modal-x" onclick="closeModal('modal-data')">&times;</button>
            </div>

            <!-- ── Manual Backup ── -->
            <div style="margin-bottom:20px;">
                <h4 style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">
                    <i class="fas fa-download" style="color:var(--accent);margin-right:6px;"></i>Export JSON (Backup Manual)
                </h4>
                <p style="font-size:12px;color:var(--text-muted);line-height:1.5;margin-bottom:12px;">Unduh seluruh data Cashbook ke file JSON. Simpan file ini sebagai cadangan.</p>
                <button class="btn btn-accent" style="width:100%;justify-content:center;" onclick="exportData()">
                    <i class="fas fa-download"></i> Unduh File Backup Sekarang
                </button>
            </div>

            <hr style="border:0;border-top:1px solid var(--border);margin:20px 0;">

            <!-- ── Auto Backup Settings ── -->
            <div style="margin-bottom:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <h4 style="font-size:13px;font-weight:700;color:var(--text);">
                        <i class="fas fa-clock-rotate-left" style="color:var(--info);margin-right:6px;"></i>Backup Otomatis
                    </h4>
                    <!-- Toggle -->
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <div style="position:relative;">
                            <input type="checkbox" id="ab-enabled-toggle" onchange="onAutoBackupToggle(this)" style="opacity:0;width:0;height:0;position:absolute;">
                            <div id="ab-track" style="width:40px;height:22px;background:var(--surface3);border-radius:11px;border:1px solid var(--border);transition:.25s;position:relative;cursor:pointer;">
                                <div id="ab-thumb" style="position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:var(--text-muted);transition:.25s;"></div>
                            </div>
                        </div>
                        <span id="ab-toggle-label" style="font-size:12px;color:var(--text-muted);font-weight:600;">Nonaktif</span>
                    </label>
                </div>

                <!-- Interval Options (shown only when enabled) -->
                <div id="ab-options" style="display:none;">
                    <p style="font-size:11px;color:var(--text-muted);margin-bottom:10px;">Pilih interval backup otomatis:</p>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:14px;">
                        <label id="ab-opt-1" class="ab-opt-card" onclick="selectBackupInterval(1)">
                            <i class="fas fa-calendar-day"></i>
                            <span>Harian</span>
                            <small>Setiap 1 hari</small>
                        </label>
                        <label id="ab-opt-7" class="ab-opt-card" onclick="selectBackupInterval(7)">
                            <i class="fas fa-calendar-week"></i>
                            <span>Mingguan</span>
                            <small>Setiap 7 hari</small>
                        </label>
                        <label id="ab-opt-30" class="ab-opt-card" onclick="selectBackupInterval(30)">
                            <i class="fas fa-calendar"></i>
                            <span>Bulanan</span>
                            <small>Setiap 30 hari</small>
                        </label>
                    </div>

                    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px;">
                        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px;">
                            <span style="color:var(--text-muted);">Backup terakhir:</span>
                            <span id="ab-last-backup" style="color:var(--text);font-weight:600;">Belum pernah</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:12px;">
                            <span style="color:var(--text-muted);">Backup berikutnya:</span>
                            <span id="ab-next-backup" style="color:var(--accent);font-weight:600;">—</span>
                        </div>
                    </div>

                    <button class="btn btn-accent" style="width:100%;justify-content:center;margin-top:12px;" onclick="saveBackupSettings()">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>

                <p id="ab-hint" style="font-size:11px;color:var(--text-muted);margin-top:6px;">Aktifkan untuk mengunduh backup JSON secara otomatis saat Cashbook dibuka.</p>
            </div>

            <hr style="border:0;border-top:1px solid var(--border);margin:20px 0;">

            <!-- ── Import / Restore ── -->
            <div>
                <h4 style="font-size:13px;font-weight:700;color:var(--danger);margin-bottom:6px;">
                    <i class="fas fa-upload" style="color:var(--danger);margin-right:6px;"></i>Import JSON (Restore)
                </h4>
                <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);padding:10px;border-radius:8px;margin-bottom:12px;">
                    <p style="font-size:11px;color:var(--danger);line-height:1.5;display:flex;gap:8px;">
                        <i class="fas fa-triangle-exclamation" style="margin-top:2px;"></i>
                        <span><strong>Peringatan:</strong> Import akan <strong>MENGHAPUS SEMUA DATA CASHBOOK SAAT INI</strong> dan menggantinya dengan isi file JSON. Tindakan ini tidak dapat dibatalkan!</span>
                    </p>
                </div>
                <input type="file" id="import-file" accept=".json" style="display:none;" onchange="handleImportFile(this)">
                <button class="btn" style="width:100%;justify-content:center;background:var(--surface2);color:var(--text);" onclick="document.getElementById('import-file').click()">
                    <i class="fas fa-file-import"></i> Pilih File &amp; Restore Data
                </button>
            </div>
        </div>
    </div>
    
    <!-- INFO MODAL -->
    <div class="modal-bg" id="modal-info" onclick="closeOut(this)">
        <div class="modal-box" onclick="event.stopPropagation()" style="max-width:400px; text-align:center; padding:32px 24px;">
            <div style="width:56px; height:56px; border-radius:16px; background:var(--accent-glow); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:24px; margin:0 auto 16px;">
                <i class="fas fa-circle-info"></i>
            </div>
            <h3 id="info-modal-title" style="font-size:18px; font-weight:800; color:var(--text); margin-bottom:12px; line-height:1.3;">Penjelasan Fitur</h3>
            <p id="info-modal-desc" style="font-size:14px; color:var(--text-muted); line-height:1.6; margin-bottom:24px;">Detail penjelasan akan muncul di sini.</p>
            <button class="btn btn-primary" style="width:100%;" onclick="closeModal('modal-info')">Mengerti</button>
        </div>
    </div>
