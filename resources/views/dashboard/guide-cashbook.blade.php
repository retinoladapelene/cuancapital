<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Cashbook — CuanCapital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --theme:#10b981;
            --theme-dark:#059669;
            --emerald:#10b981;
            --rose:#f43f5e;
            --amber:#f59e0b;
            --indigo:#6366f1;
            --slate-900:#0f172a;
            --slate-800:#1e293b;
            --radius:16px;
        }
        html{scroll-behavior:smooth;-webkit-tap-highlight-color:transparent}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:#0f172a;color:#e2e8f0;min-height:100vh;overflow-x:hidden;-webkit-font-smoothing:antialiased}
        .progress-bar{position:fixed;top:0;left:0;height:3px;background:var(--theme);z-index:999;transition:width .1s}
        .top-nav{position:sticky;top:0;z-index:100;background:rgba(15,23,42,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06);padding:0 16px;height:56px;display:flex;align-items:center;justify-content:space-between;gap:12px}
        .top-nav .brand{font-weight:900;font-size:15px;color:var(--theme);display:flex;align-items:center;gap:8px;text-decoration:none;flex-shrink:0}
        .brand-badge{background:var(--theme);color:#fff;font-size:9px;font-weight:800;padding:2px 6px;border-radius:20px;letter-spacing:1px;text-transform:uppercase}
        @media(max-width:360px){.brand-badge{display:none}}
        .back-btn{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#94a3b8;font-size:12px;font-weight:700;padding:8px 14px;border-radius:10px;text-decoration:none;transition:all .2s;display:flex;align-items:center;gap:6px;white-space:nowrap;flex-shrink:0}
        .back-btn:hover,.back-btn:active{background:rgba(255,255,255,.1);color:#fff}
        @media(max-width:420px){.back-btn .btn-label{display:none}.back-btn{padding:8px 10px}}
        .hero{text-align:center;padding:36px 16px 32px;position:relative;overflow:hidden}
        .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(16,185,129,.18),transparent);pointer-events:none}
        .hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);color:var(--theme);font-size:11px;font-weight:800;padding:6px 14px;border-radius:100px;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
        .hero h1{font-size:clamp(24px,6vw,52px);font-weight:900;line-height:1.15;color:#fff;margin-bottom:12px}
        .hero h1 span{color:var(--theme)}
        .hero p{font-size:15px;color:#94a3b8;max-width:560px;margin:0 auto 28px;line-height:1.7}
        .mobile-nav-wrap{padding:0 16px 4px;background:rgba(15,23,42,.5);border-bottom:1px solid rgba(255,255,255,.04);position:sticky;top:56px;z-index:90}
        .scroll-hint{display:flex;align-items:center;justify-content:flex-end;gap:4px;font-size:10px;color:#475569;font-weight:600;padding:4px 0}
        @media(min-width:640px){.scroll-hint{display:none}}
        .mobile-nav{display:flex;overflow-x:auto;gap:6px;padding:10px 0;scrollbar-width:none;-ms-overflow-style:none}
        .mobile-nav::-webkit-scrollbar{display:none}
        .mobile-pill{flex-shrink:0;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:100px;padding:7px 14px;font-size:12px;font-weight:700;color:#94a3b8;cursor:pointer;transition:all .2s;white-space:nowrap;-webkit-tap-highlight-color:transparent}
        .mobile-pill.active{background:var(--theme);border-color:var(--theme);color:#fff}
        .guide-layout{max-width:1200px;margin:0 auto;padding:0 16px 80px;display:grid;grid-template-columns:1fr;gap:0}
        .sidebar{display:none}
        @media(min-width:900px){
            .guide-layout{padding:0 24px 80px;grid-template-columns:260px 1fr;gap:32px;align-items:start}
            .sidebar{display:flex;flex-direction:column;gap:4px;position:sticky;top:80px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:var(--radius);padding:16px}
            .mobile-nav-wrap{display:none}
        }
        @media(max-width:640px){.guide-layout{padding-bottom:100px}}
        .sidebar-label{font-size:10px;font-weight:800;color:#475569;letter-spacing:2px;text-transform:uppercase;padding:8px 12px 4px}
        .sidebar-item{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;text-decoration:none;color:#94a3b8;font-size:13px;font-weight:600;transition:all .2s;cursor:pointer;border:none;background:none;width:100%;text-align:left}
        .sidebar-item:hover,.sidebar-item.active{background:rgba(255,255,255,.06);color:#fff}
        .sidebar-item.active{color:var(--theme)}
        .sidebar-item .icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
        .ic-theme{background:rgba(16,185,129,.15);color:var(--theme)}
        .ic-slate{background:rgba(148,163,184,.1);color:#94a3b8}
        .ic-blue{background:rgba(99,102,241,.15);color:#6366f1}
        .ic-amber{background:rgba(245,158,11,.15);color:#f59e0b}
        .ic-rose{background:rgba(244,63,94,.15);color:#f43f5e}
        .content{display:flex;flex-direction:column}
        .section{padding:36px 0;border-bottom:1px solid rgba(255,255,255,.05);animation:fadeUp .4s ease forwards}
        @media(min-width:640px){.section{padding:48px 0}}
        .section:last-child{border-bottom:none}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .section-tag{display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px;color:var(--theme)}
        .section-tag.blue{color:#6366f1}
        .section-tag.amber{color:#f59e0b}
        .section-tag.rose{color:#f43f5e}
        .section h2{font-size:clamp(20px,4.5vw,32px);font-weight:900;color:#fff;margin-bottom:8px;line-height:1.2}
        .section .subtitle{font-size:14px;color:#64748b;margin-bottom:24px;line-height:1.7}
        @media(min-width:640px){.section .subtitle{font-size:15px}}
        .steps{display:flex;flex-direction:column;gap:12px;margin:20px 0}
        .step{display:flex;gap:14px;align-items:flex-start;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:16px;transition:all .2s}
        @media(min-width:640px){.steps{gap:16px;margin:24px 0}.step{padding:18px 20px}}
        .step:hover{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)}
        .step-num{width:30px;height:30px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;background:rgba(16,185,129,.2);color:var(--theme);border:1.5px solid rgba(16,185,129,.4)}
        .step-body .step-title{font-size:13px;font-weight:800;color:#fff;margin-bottom:4px;line-height:1.4}
        .step-body .step-desc{font-size:12px;color:#64748b;line-height:1.6}
        @media(min-width:640px){.step-body .step-title{font-size:14px}.step-body .step-desc{font-size:13px}}
        .callout{display:flex;gap:12px;padding:14px 16px;border-radius:12px;margin:16px 0}
        .callout.tip{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2)}
        .callout.warn{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2)}
        .callout.info{background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2)}
        .callout-icon{font-size:16px;margin-top:2px;flex-shrink:0;color:var(--theme)}
        .callout.warn .callout-icon{color:var(--amber)}
        .callout.info .callout-icon{color:#6366f1}
        .callout-body strong{font-size:12px;font-weight:800;color:#fff;display:block;margin-bottom:3px}
        .callout-body p{font-size:12px;color:#94a3b8;line-height:1.6}
        /* Feature grid */
        .feat-grid{display:grid;grid-template-columns:1fr;gap:12px;margin:20px 0}
        @media(min-width:640px){.feat-grid{grid-template-columns:1fr 1fr}}
        .feat-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:16px 18px;transition:all .2s}
        .feat-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)}
        .feat-card-head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
        .feat-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
        .feat-card-title{font-size:13px;font-weight:800;color:#fff}
        .feat-card-desc{font-size:12px;color:#64748b;line-height:1.6}
        /* Tab badge */
        .tab-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;font-size:11px;font-weight:700;margin-bottom:16px}
        .tab-badge.green{background:rgba(16,185,129,.12);color:var(--theme);border:1px solid rgba(16,185,129,.2)}
        .tab-badge.blue{background:rgba(99,102,241,.12);color:#6366f1;border:1px solid rgba(99,102,241,.2)}
        .tab-badge.amber{background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.2)}
        .tab-badge.purple{background:rgba(139,92,246,.12);color:#8b5cf6;border:1px solid rgba(139,92,246,.2)}
        /* CTA */
        .cta-section{text-align:center;padding:44px 20px;background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(15,23,42,.1));border:1px solid rgba(255,255,255,.06);border-radius:20px;margin-top:40px}
        @media(min-width:640px){.cta-section{padding:64px 24px;border-radius:24px;margin-top:48px}}
        .cta-section h2{font-size:clamp(22px,5vw,28px);font-weight:900;color:#fff;margin-bottom:10px}
        .cta-section p{color:#64748b;margin-bottom:24px;font-size:14px}
        .btn-cta{display:inline-flex;align-items:center;gap:8px;background:var(--theme);color:#fff;font-size:14px;font-weight:800;padding:14px 24px;border-radius:12px;text-decoration:none;transition:all .2s;box-shadow:0 8px 24px rgba(16,185,129,.3)}
        .btn-cta:hover{background:var(--theme-dark);transform:translateY(-2px);box-shadow:0 12px 30px rgba(16,185,129,.4)}
        .btn-cta:active{transform:translateY(0)}
        /* Pillar tags */
        .pillar-row{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0}
        .pillar-tag{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:10px;font-size:12px;font-weight:700}
        .pillar-tag.wajib{background:rgba(59,130,246,.12);color:#3b82f6;border:1px solid rgba(59,130,246,.2)}
        .pillar-tag.growth{background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.2)}
        .pillar-tag.lifestyle{background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.2)}
        .pillar-tag.bocor{background:rgba(239,68,68,.12);color:#ef4444;border:1px solid rgba(239,68,68,.2)}
    </style>
</head>
<body>

<div class="progress-bar" id="progress-bar"></div>

<nav class="top-nav">
    <a href="{{ route('cashbook') ?? '#' }}" class="brand">
        <i class="fas fa-book-open"></i>
        Cashbook
        <span class="brand-badge">Panduan</span>
    </a>
    <a href="{{ route('cashbook') ?? '#' }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span class="btn-label">Kembali ke Cashbook</span>
    </a>
</nav>

<div class="mobile-nav-wrap">
    <div class="scroll-hint"><i class="fas fa-chevron-right" style="font-size:9px"></i> geser untuk lihat semua</div>
    <div class="mobile-nav" id="mobile-nav">
        <div class="mobile-pill active" onclick="scrollToSection('s-overview')">Overview</div>
        <div class="mobile-pill" onclick="scrollToSection('s-transaksi')">Transaksi</div>
        <div class="mobile-pill" onclick="scrollToSection('s-laporan')">Laporan</div>
        <div class="mobile-pill" onclick="scrollToSection('s-utang')">Utang</div>
        <div class="mobile-pill" onclick="scrollToSection('s-anggaran')">Anggaran</div>
        <div class="mobile-pill" onclick="scrollToSection('s-tips')">Tips</div>
    </div>
</div>

<div class="hero">
    <div class="hero-eyebrow"><i class="fas fa-book-open"></i> Panduan Lengkap</div>
    <h1>Kuasai <span>Cashbook</span><br>Dalam 10 Menit</h1>
    <p>Pelajari semua fitur Cashbook — dari mencatat transaksi harian hingga menganalisa laporan keuangan lengkap dengan budget tracker.</p>
</div>

<div class="guide-layout">
    <aside class="sidebar">
        <div class="sidebar-label">Daftar Isi</div>
        <button class="sidebar-item active" onclick="scrollToSection('s-overview')"><span class="icon ic-slate"><i class="fas fa-map"></i></span> Overview</button>
        <button class="sidebar-item" onclick="scrollToSection('s-transaksi')"><span class="icon ic-theme"><i class="fas fa-arrow-right-arrow-left"></i></span> Tab Transaksi</button>
        <button class="sidebar-item" onclick="scrollToSection('s-laporan')"><span class="icon ic-amber"><i class="fas fa-chart-line"></i></span> Tab Laporan</button>
        <button class="sidebar-item" onclick="scrollToSection('s-utang')"><span class="icon ic-rose"><i class="fas fa-credit-card"></i></span> Tab Utang</button>
        <button class="sidebar-item" onclick="scrollToSection('s-anggaran')"><span class="icon ic-blue"><i class="fas fa-sliders"></i></span> Tab Anggaran</button>
        <button class="sidebar-item" onclick="scrollToSection('s-tips')"><span class="icon" style="background:rgba(217,70,239,.15);color:#d946ef"><i class="fas fa-star"></i></span> Tips Harian</button>
    </aside>

    <main class="content">

        <!-- OVERVIEW -->
        <section class="section" id="s-overview">
            <div class="section-tag"><i class="fas fa-map"></i> Overview</div>
            <h2>Apa Itu Cashbook?</h2>
            <p class="subtitle">Cashbook adalah sistem pencatatan keuangan pribadi terintegrasi. Catat setiap sen yang masuk dan keluar, set batas pengeluaran, dan analisa arus kas kamu dalam satu tampilan.</p>

            <div class="feat-grid">
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-theme"><i class="fas fa-arrow-right-arrow-left"></i></div>
                        <div class="feat-card-title">Transaksi</div>
                    </div>
                    <div class="feat-card-desc">Catat pemasukan, pengeluaran, dan transfer antar akun. Filter berdasarkan tanggal, kategori, dan pilar.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-blue"><i class="fas fa-wallet"></i></div>
                        <div class="feat-card-title">Anggaran</div>
                    </div>
                    <div class="feat-card-desc">Set batas pengeluaran per kategori dan pantau pemakaiannya secara real-time tiap bulan.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-amber"><i class="fas fa-chart-line"></i></div>
                        <div class="feat-card-title">Laporan</div>
                    </div>
                    <div class="feat-card-desc">Analisa arus kas, saving rate, expense ratio, dan komparasi budget vs realisasi per periode.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(139,92,246,.15);color:#8b5cf6;"><i class="fas fa-building-columns"></i></div>
                        <div class="feat-card-title">Multi Akun</div>
                    </div>
                    <div class="feat-card-desc">Kelola beberapa akun (bank, kas, e-wallet, investasi) dalam satu tampilan ringkas.</div>
                </div>
            </div>

            <div class="callout tip">
                <div class="callout-icon"><i class="fas fa-lightbulb"></i></div>
                <div class="callout-body">
                    <strong>Filosofi Cashbook</strong>
                    <p>Uang yang tidak dicatat adalah uang yang bocor tanpa kamu sadari. Cashbook membantu kamu melihat setiap rupiah dengan transparansi penuh.</p>
                </div>
            </div>
        </section>

        <!-- TAB TRANSAKSI -->
        <section class="section" id="s-transaksi">
            <div class="section-tag"><i class="fas fa-arrow-right-arrow-left"></i> Tab Transaksi</div>
            <div class="tab-badge green"><i class="fas fa-circle" style="font-size:7px"></i> Tab 1</div>
            <h2>Mencatat Setiap Transaksi</h2>
            <p class="subtitle">Tab utama untuk mencatat semua aktivitas keuangan. Dirancang agar secepat mungkin — dari buka app hingga transaksi tercatat hanya butuh beberapa detik.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Tekan tombol + (FAB) di pojok kanan bawah</div>
                        <div class="step-desc">Pilih "Transaksi" dari menu yang muncul untuk membuka form pencatatan.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Pilih tipe: Pemasukan / Pengeluaran / Transfer</div>
                        <div class="step-desc"><b>Pemasukan</b> = uang masuk ke akun (gaji, penjualan, dll). <b>Pengeluaran</b> = uang keluar. <b>Transfer</b> = pindah antar akun sendiri (tidak mempengaruhi total kekayaan).</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Isi nominal, pilih kategori & akun</div>
                        <div class="step-desc">Pastikan memilih kategori yang tepat agar laporan pilar-mu akurat. Pilih akun sumber dana yang sesuai.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-body">
                        <div class="step-title">Gunakan filter untuk melihat history</div>
                        <div class="step-desc">Filter berdasarkan rentang tanggal, kategori, atau pilar untuk menemukan transaksi tertentu dengan cepat.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">5</div>
                    <div class="step-body">
                        <div class="step-title">Edit atau hapus transaksi yang salah</div>
                        <div class="step-desc">Klik ikon pensil pada baris transaksi untuk mengedit, atau ikon tempat sampah untuk menghapus.</div>
                    </div>
                </div>
            </div>

            <div class="callout info">
                <div class="callout-icon"><i class="fas fa-circle-info"></i></div>
                <div class="callout-body">
                    <strong>Kelompok Transaksi per Hari</strong>
                    <p>Transaksi ditampilkan dikelompokkan per tanggal dengan total pemasukan & pengeluaran harian di header grupnya, sehingga kamu bisa langsung lihat "hari ini habis berapa".</p>
                </div>
            </div>
        </section>

        <!-- TAB UTANG -->
        <section class="section" id="s-utang">
            <div class="section-tag rose"><i class="fas fa-credit-card"></i> Tab Utang</div>
            <div class="tab-badge rose" style="background:rgba(244,63,94,.12);color:#f43f5e;border:1px solid rgba(244,63,94,.2)"><i class="fas fa-circle" style="font-size:7px"></i> Tab 4</div>
            <h2>Manajemen Pinjaman dan Piutang</h2>
            <p class="subtitle">Tab utang dirancang untuk memastikan Anda tidak pernah melewatkan jatuh tempo cicilan atau lupa siapa yang berutang kepada Anda.</p>
            
            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Tekan tombol Buka Utang Baru</div>
                        <div class="step-desc">Tentukan apakah ini utang (Anda meminjam) atau piutang (orang lain meminjam uang Anda). Masukkan nominal total pinjaman.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Lakukan pembayaran cicilan</div>
                        <div class="step-desc">Gunakan menu Bayar Cicilan untuk mencicil utang. Pilih sumber akun kas mana yang digunakan untuk membayar agar saldo bank otomatis berkurang.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Pantau Status Lunas</div>
                        <div class="step-desc">Terdapat riwayat detail cicilan per utang. Setelah saldo utang habis (Rp 0), utang akan otomatis ditandai sebagai "Lunas".</div>
                    </div>
                </div>
            </div>
            
            <div class="callout info">
                <div class="callout-icon"><i class="fas fa-circle-info"></i></div>
                <div class="callout-body">
                    <strong>Rasio Utang terhadap Aset (Debt-to-Asset Ratio)</strong>
                    <p>Sistem secara otomatis menghitung berapa persentase total utang Anda berbanding total uang yang ada di seluruh akun kas Anda. Rasio di atas 50% berarti status bahaya.</p>
                </div>
            </div>
        </section>

        <!-- TAB ANGGARAN -->
        <section class="section" id="s-anggaran">
            <div class="section-tag blue"><i class="fas fa-wallet"></i> Tab Anggaran</div>
            <div class="tab-badge blue"><i class="fas fa-circle" style="font-size:7px"></i> Tab 5</div>
            <h2>Mengontrol Pengeluaran dengan Budget</h2>
            <p class="subtitle">Set batas pengeluaran per kategori dan pantau pemakaiannya secara real-time melalui *Progress Card* premium. Sistem akan memperingatkan saat kamu mendekati atau melewati batas.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <div class="step-title">Pilih bulan yang ingin di-set budget</div>
                        <div class="step-desc">Gunakan menu navigasi tanggal di atas header untuk berpindah antar bulan. Budget bersifat spesifik per-bulan.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <div class="step-title">Klik tombol "+ Tambah" di Header Kategori</div>
                        <div class="step-desc">Pilih kategori dan masukkan limit maksimal pengeluaran untuk kategori tersebut dalam sebulan berjalan.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <div class="step-title">Pantau Kartu Progress</div>
                        <div class="step-desc">Aplikasi akan me-*render* UI Card untuk setiap anggaran. Bar hijau = aman, kuning = waspada (80%), merah = *over budget*. Kamu bisa langsung melihat sisa nominal yang bisa dipakai.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <div class="step-body">
                        <div class="step-title">Aksi Langsung dari Kartu</div>
                        <div class="step-desc">Arahkan kursor ke kartu anggaran untuk menampilkan tombol Edit dan Hapus tersembunyi.</div>
                    </div>
                </div>
            </div>

            <div class="section-tag" style="margin-top:24px;"><i class="fas fa-th-large"></i> Sistem 4 Pilar</div>
            <p style="font-size:14px;color:#64748b;margin-bottom:12px;line-height:1.7;">Setiap kategori termasuk dalam salah satu dari 4 pilar pengeluaran:</p>
            <div class="pillar-row">
                <span class="pillar-tag wajib"><i class="fas fa-shield-halved"></i> Wajib — Kebutuhan pokok tidak bisa ditunda (makan, listrik, cicilan)</span>
                <span class="pillar-tag growth"><i class="fas fa-seedling"></i> Growth — Investasi diri & bisnis (kursus, tools, iklan)</span>
                <span class="pillar-tag lifestyle"><i class="fas fa-star"></i> Lifestyle — Gaya hidup yang bisa dikurangi jika perlu</span>
                <span class="pillar-tag bocor"><i class="fas fa-droplet"></i> Bocor — Pengeluaran sia-sia yang harus dieliminasi</span>
            </div>

            <div class="feat-grid" style="margin-top:20px;">
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(99,102,241,.15);color:#6366f1;"><i class="fas fa-chart-bar"></i></div>
                        <div class="feat-card-title">Chart Budget vs Aktual</div>
                    </div>
                    <div class="feat-card-desc">Visualisasi bar chart horizontal perbandingan pengeluaran nyata vs batas budget per kategori.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;"><i class="fas fa-lightbulb"></i></div>
                        <div class="feat-card-title">Saran Budget AI</div>
                    </div>
                    <div class="feat-card-desc">Sistem menganalisa rata-rata pengeluaran 3 bulan terakhir dan menyarankan limit budget yang realistis.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(16,185,129,.15);color:#10b981;"><i class="fas fa-rotate"></i></div>
                        <div class="feat-card-title">Rollover Budget</div>
                    </div>
                    <div class="feat-card-desc">Aktifkan agar sisa budget yang tidak terpakai bulan ini otomatis ditambahkan ke budget bulan depan.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(139,92,246,.15);color:#8b5cf6;"><i class="fas fa-chart-line"></i></div>
                        <div class="feat-card-title">Tren 6 Bulan</div>
                    </div>
                    <div class="feat-card-desc">Grafik persentase pemakaian budget dibanding batas per bulan selama 6 bulan terakhir.</div>
                </div>
            </div>

            <div class="callout warn">
                <div class="callout-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <div class="callout-body">
                    <strong>Peringatan Otomatis</strong>
                    <p>Sistem menampilkan badge <b>WASPADA</b> saat pemakaian mencapai 80% dari limit, dan badge <b>OVER</b> saat melampaui 100%. Pantau secara rutin setiap minggu.</p>
                </div>
            </div>
        </section>

        <!-- TAB LAPORAN -->
        <section class="section" id="s-laporan">
            <div class="section-tag amber"><i class="fas fa-chart-line"></i> Tab Laporan</div>
            <div class="tab-badge amber"><i class="fas fa-circle" style="font-size:7px"></i> Tab 3</div>
            <h2>Analisa Keuangan Mendalam</h2>
            <p class="subtitle">Tab laporan adalah pusat analisa keuangan. Lihat kondisi riil keuanganmu dari berbagai sudut pandang: rasio, tren, perbandingan, dan proyeksi kekayaan.</p>

            <div class="feat-grid">
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-theme"><i class="fas fa-piggy-bank"></i></div>
                        <div class="feat-card-title">Saving Rate</div>
                    </div>
                    <div class="feat-card-desc">Persentase pemasukan yang berhasil kamu simpan. Target sehat: minimal 20%. Di bawah 10% = berbahaya.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-amber"><i class="fas fa-percent"></i></div>
                        <div class="feat-card-title">Expense Ratio</div>
                    </div>
                    <div class="feat-card-desc">Persentase pengeluaran dari total pemasukan. Target sehat: di bawah 70%. Di atas 85% = lampu merah.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-blue"><i class="fas fa-arrow-trend-up"></i></div>
                        <div class="feat-card-title">Perbandingan Bulan</div>
                    </div>
                    <div class="feat-card-desc">Badge delta % di setiap metric card membandingkan periode aktif vs periode sebelumnya.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon" style="background:rgba(139,92,246,.15);color:#8b5cf6;"><i class="fas fa-chart-area"></i></div>
                        <div class="feat-card-title">Net Worth Track</div>
                    </div>
                    <div class="feat-card-desc">Grafik pertumbuhan total kekayaan bersih (semua saldo akun) selama 12 bulan terakhir.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-rose"><i class="fas fa-bullseye"></i></div>
                        <div class="feat-card-title">Budget vs Realisasi</div>
                    </div>
                    <div class="feat-card-desc">Tabel perbandingan pengeluaran nyata vs budget per kategori, lengkap dengan status OK / WASPADA / OVER.</div>
                </div>
                <div class="feat-card">
                    <div class="feat-card-head">
                        <div class="feat-icon ic-theme"><i class="fas fa-table-list"></i></div>
                        <div class="feat-card-title">Detail Transaksi</div>
                    </div>
                    <div class="feat-card-desc">Tabel semua transaksi dalam periode terpilih. Klik "Tampilkan" untuk melihat, bisa muat lebih per 50 transaksi.</div>
                </div>
            </div>

            <div class="steps" style="margin-top:24px;">
                <div class="step">
                    <div class="step-num"><i class="fas fa-calendar-alt" style="font-size:11px"></i></div>
                    <div class="step-body">
                        <div class="step-title">Pilih Periode Laporan</div>
                        <div class="step-desc">Pilih Bulan Ini, Bulan Lalu, Tahun Ini, atau Kustom (masukkan tanggal dari-sampai bebas). Filter Akun juga tersedia untuk melihat per-akun.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num"><i class="fas fa-chart-pie" style="font-size:11px"></i></div>
                    <div class="step-body">
                        <div class="step-title">Baca Distribusi Pilar & Sumber Pemasukan</div>
                        <div class="step-desc">Panel kanan atas menampilkan donut chart distribusi pengeluaran per pilar. Klik tab "Pemasukan" untuk melihat breakdown sumber income.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num"><i class="fas fa-list" style="font-size:11px"></i></div>
                    <div class="step-body">
                        <div class="step-title">Analisa Semua Kategori</div>
                        <div class="step-desc">Panel "Semua Kategori" menampilkan seluruh kategori dengan nilai, persentase, dan mini bar chart. Bisa diurutkan berdasarkan Terbesar / Nama / Persentase.</div>
                    </div>
                </div>
            </div>

            <div class="callout tip">
                <div class="callout-icon"><i class="fas fa-star"></i></div>
                <div class="callout-body">
                    <strong>Cara Baca Laporan dengan Tepat</strong>
                    <p>Mulai dari Saving Rate — jika di bawah 20%, cari kategori terbesar di tabel "Semua Kategori" lalu turunkan budget-nya. Gunakan tabel Budget vs Realisasi untuk identifikasi kategori mana yang paling sering OVER.</p>
                </div>
            </div>
        </section>

        <!-- TIPS HARIAN -->
        <section class="section" id="s-tips">
            <div class="section-tag rose"><i class="fas fa-star"></i> Tips Harian</div>
            <h2>Kebiasaan yang Mengubah Segalanya</h2>
            <p class="subtitle">Cashbook hanya berguna jika digunakan secara rutin. Ini adalah rutinitas harian/mingguan yang direkomendasikan.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">H</div>
                    <div class="step-body">
                        <div class="step-title">Harian (2 menit): Catat semua transaksi</div>
                        <div class="step-desc">Jangan biarkan lebih dari 1 hari berlalu tanpa mencatat. Makin lama kamu tunda, makin banyak yang terlupa.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">M</div>
                    <div class="step-body">
                        <div class="step-title">Mingguan (5 menit): Cek dashboard anggaran</div>
                        <div class="step-desc">Pantau kategori mana yang sudah mendekati batas. Sesuaikan perilaku pengeluaran di sisa minggu jika ada yang level WASPADA.</div>
                    </div>
                </div>
                <div class="step">
                    <div class="step-num">B</div>
                    <div class="step-body">
                        <div class="step-title">Bulanan (15 menit): Review laporan penuh</div>
                        <div class="step-desc">Buka tab Laporan, pilih "Bulan Lalu". Baca saving rate, expense ratio, dan tabel Budget vs Realisasi. Putuskan budget bulan depan berdasarkan data ini.</div>
                    </div>
                </div>
            </div>

            <div class="callout warn">
                <div class="callout-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <div class="callout-body">
                    <strong>Tanda Dana Sudah Tidak Sehat</strong>
                    <p>Saving Rate di bawah 10%, atau Expense Ratio di atas 90% secara konsisten selama 3 bulan berturut-turut adalah sinyal bahwa pengeluaran perlu direstrukturisasi segera.</p>
                </div>
            </div>
        </section>

        <div class="cta-section">
            <h2>Siap Mulai Catat?</h2>
            <p>Buka Cashbook dan mulai rekam keuanganmu hari ini. Satu transaksi pertama adalah langkah terbesar.</p>
            <a href="{{ route('cashbook') ?? '#' }}" class="btn-cta"><i class="fas fa-book-open"></i> Buka Cashbook</a>
        </div>

    </main>
</div>

<script>
window.addEventListener('scroll',()=>{
    const pct=document.body.scrollHeight-window.innerHeight>0?(window.scrollY/(document.body.scrollHeight-window.innerHeight))*100:0;
    document.getElementById('progress-bar').style.width=pct+'%';
});
const sectionIds=['s-overview','s-transaksi','s-laporan','s-utang','s-anggaran','s-tips'];
function getScrollOffset(){
    const nav=document.querySelector('.top-nav');
    const mob=document.querySelector('.mobile-nav-wrap');
    let off=nav?nav.offsetHeight:56;
    if(mob&&window.innerWidth<900)off+=mob.offsetHeight;
    return off+12;
}
function scrollToSection(id){
    const el=document.getElementById(id);
    if(el){const y=el.getBoundingClientRect().top+window.scrollY-getScrollOffset();window.scrollTo({top:y,behavior:'smooth'});}
    updateActive(id);
}
function updateActive(id){
    const idx=sectionIds.indexOf(id);
    if(idx===-1)return;
    document.querySelectorAll('.sidebar-item').forEach((b,i)=>b.classList.toggle('active',i===idx));
    const pills=document.querySelectorAll('.mobile-pill');
    pills.forEach((b,i)=>b.classList.toggle('active',i===idx));
    if(pills[idx])pills[idx].scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'});
}
const observer=new IntersectionObserver((entries)=>{
    entries.forEach(e=>{if(e.isIntersecting)updateActive(e.target.id);});
},{rootMargin:'-30% 0px -60% 0px',threshold:0});
document.querySelectorAll('.section').forEach(s=>observer.observe(s));
</script>
</body>
</html>
