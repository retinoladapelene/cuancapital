<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Phase 2 — CuanCapital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --theme:#8b5cf6; 
            --theme-dark:#7c3aed; 
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
        .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(139,92,246,.18),transparent);pointer-events:none}
        .hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(139,92,246,.12);border:1px solid rgba(139,92,246,.3);color:var(--theme);font-size:11px;font-weight:800;padding:6px 14px;border-radius:100px;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px}
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
        .ic-theme{background:rgba(139,92,246,.15);color:var(--theme)}
        .ic-slate{background:rgba(148,163,184,.1);color:#94a3b8}
        .content{display:flex;flex-direction:column}
        .section{padding:36px 0;border-bottom:1px solid rgba(255,255,255,.05);animation:fadeUp .4s ease forwards}
        @media(min-width:640px){.section{padding:48px 0}}
        .section:last-child{border-bottom:none}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .section-tag{display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px; color:var(--theme)}
        .section h2{font-size:clamp(20px,4.5vw,32px);font-weight:900;color:#fff;margin-bottom:8px;line-height:1.2}
        .section .subtitle{font-size:14px;color:#64748b;margin-bottom:24px;line-height:1.7}
        @media(min-width:640px){.section .subtitle{font-size:15px}}
        .steps{display:flex;flex-direction:column;gap:12px;margin:20px 0}
        .step{display:flex;gap:14px;align-items:flex-start;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:14px;padding:16px;transition:all .2s}
        @media(min-width:640px){.steps{gap:16px;margin:24px 0}.step{padding:18px 20px}}
        .step:hover{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.1)}
        .step-num{width:30px;height:30px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900; background:rgba(139,92,246,.2);color:var(--theme);border:1.5px solid rgba(139,92,246,.4)}
        .step-body .step-title{font-size:13px;font-weight:800;color:#fff;margin-bottom:4px;line-height:1.4}.step-body .step-desc{font-size:12px;color:#64748b;line-height:1.6}
        @media(min-width:640px){.step-body .step-title{font-size:14px}.step-body .step-desc{font-size:13px}}
        .callout{display:flex;gap:12px;padding:14px 16px;border-radius:12px;margin:16px 0}
        .callout.tip{background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2)}
        .callout-icon{font-size:16px;margin-top:2px;flex-shrink:0; color:var(--theme)}
        .callout-body strong{font-size:12px;font-weight:800;color:#fff;display:block;margin-bottom:3px}.callout-body p{font-size:12px;color:#94a3b8;line-height:1.6}
        .cta-section{text-align:center;padding:44px 20px;background:linear-gradient(135deg,rgba(139,92,246,.1),rgba(15,23,42,.1));border:1px solid rgba(255,255,255,.06);border-radius:20px;margin-top:40px}
        @media(min-width:640px){.cta-section{padding:64px 24px;border-radius:24px;margin-top:48px}}
        .cta-section h2{font-size:clamp(22px,5vw,28px);font-weight:900;color:#fff;margin-bottom:10px}.cta-section p{color:#64748b;margin-bottom:24px;font-size:14px}
        .btn-cta{display:inline-flex;align-items:center;gap:8px;background:var(--theme);color:#fff;font-size:14px;font-weight:800;padding:14px 24px;border-radius:12px;text-decoration:none;transition:all .2s;box-shadow:0 8px 24px rgba(139,92,246,.3)}
        .btn-cta:hover{background:var(--theme-dark);transform:translateY(-2px);box-shadow:0 12px 30px rgba(139,92,246,.4)}.btn-cta:active{transform:translateY(0)}
    </style>
</head>
<body>
<div class="progress-bar" id="progress-bar"></div>
<nav class="top-nav">
    <a href="{{ route('index') }}" class="brand">
        <i class="fas fa-chart-line"></i>
        Profit Simulator
        <span class="brand-badge">Panduan</span>
    </a>
    <a href="{{ route('index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span class="btn-label">Kembali</span>
    </a>
</nav>
<div class="mobile-nav-wrap">
    <div class="scroll-hint"><i class="fas fa-chevron-right" style="font-size:9px"></i> geser untuk lihat semua</div>
    <div class="mobile-nav" id="mobile-nav">
        <div class="mobile-pill active" onclick="scrollToSection('s-overview')">Overview</div>
        <div class="mobile-pill" onclick="scrollToSection('s-fungsi')">Fungsi Utama</div>
        <div class="mobile-pill" onclick="scrollToSection('s-kerja')">Cara Kerja</div>
        <div class="mobile-pill" onclick="scrollToSection('s-pakai')">Cara Pakai</div>
    </div>
</div>
<div class="hero">
    <div class="hero-eyebrow"><i class="fas fa-book-open"></i> Phase 2 Guide</div>
    <h1>Diagnosis Kebocoran<span> & </span><br>Uji Skenario</h1>
    <p>Jangan beriklan pakai uang asli sebelum kamu mensimulasikannya di engine uji coba ini untuk mencari tau potensi terburuk (boncos).</p>
</div>
<div class="guide-layout">
    <aside class="sidebar">
        <div class="sidebar-label">Daftar Isi</div>
        <button class="sidebar-item active" onclick="scrollToSection('s-overview')"><span class="icon ic-slate"><i class="fas fa-map"></i></span> Overview</button>
        <button class="sidebar-item" onclick="scrollToSection('s-fungsi')"><span class="icon ic-theme"><i class="fas fa-crosshairs"></i></span> Apa Fungsinya?</button>
        <button class="sidebar-item" onclick="scrollToSection('s-kerja')"><span class="icon ic-theme"><i class="fas fa-cogs"></i></span> Cara Kerjanya?</button>
        <button class="sidebar-item" onclick="scrollToSection('s-pakai')"><span class="icon ic-theme"><i class="fas fa-play"></i></span> Cara Pakai (Step)</button>
    </aside>
    <main class="content">
        <section class="section" id="s-overview">
            <div class="section-tag"><i class="fas fa-map"></i> Overview</div>
            <h2>Simulasi "What If" Bisnismu</h2>
            <p class="subtitle">Banyak orang merasa jika mereka menambahkan _budget_ (Modal) promosi mereka, *sales*-nya otomatis naik. Padahal jika tingkat _Konversi_ mereka buruk, mereka hanya akan membuang-buang uang bensin promosi saja.</p>
            <div class="callout tip"><div class="callout-icon"><i class="fas fa-lightbulb"></i></div><div class="callout-body"><strong>Filosofi Inti</strong><p>Simulator ini ibarat 'Arena Latihan Tambahan'. Uji strategi apa yang paling baik: <i>membawa banyak pengunjung? atau memperbaiki gaya membalas chat / admin?</i></p></div></div>
        </section>
        <section class="section" id="s-fungsi">
            <div class="section-tag"><i class="fas fa-crosshairs"></i> Fungsi</div>
            <h2>Apa Fungsi Fiturnya?</h2>
            <p class="subtitle">Mendiagnosis kebocoran bisnis. Membantu kamu melihat apakah bisnis saat ini butuh memperbanyak anggaran iklan (Traffic) ataukah memperbaiki *skill* closing para agen pembalas chat (Konversi).</p>
        </section>
        <section class="section" id="s-kerja">
            <div class="section-tag"><i class="fas fa-cogs"></i> Traffic to Profit</div >
            <h2>Bagaimana Algoritma Bekerja?</h2>
            <p class="subtitle">Sistem mengkalkulasi modal yang dikeluarkan (Cost Per Click - CPC) secara linier dengan persentase total pengunjung yang akhirnya bersedia membeli produk (Conversion Rate).</p>
            <p class="subtitle">Hasil kalkulasi ini diturunkan (dikurangi Modal Produksi) untuk memvalidasi angka mentah ROAS (Return on Ad Spend).</p>
        </section>
        <section class="section" id="s-pakai">
            <div class="section-tag"><i class="fas fa-play"></i> Step-By-Step</div>
            <h2>Cara Penggunaan Lengkap</h2>
            <p class="subtitle">Cukup _slide_ (*geser*) parameter untuk memanipulasi variabel performa.</p>
            <div class="steps">
                <div class="step"><div class="step-num">1</div><div class="step-body"><div class="step-title">Tentukan Metrik Dasar</div><div class="step-desc">Tulis Berapa Harga Jual produkmu & HPP (Harga Poduksi/Kulakan) barang tersebut kepada supplier.</div></div></div>
                <div class="step"><div class="step-num">2</div><div class="step-body"><div class="step-title">Geser Anggaran Modal Promosi</div><div class="step-desc">Gunakan *slider* bujet iklan harian secara dinamis (misal: 100 ribu / 250 ribu sebulan).</div></div></div>
                <div class="step"><div class="step-num">3</div><div class="step-body"><div class="step-title">Bermain dengan Angka</div><div class="step-desc">Ubah nilai **Cost Per Click (CPC)** menjadi sekecil mungkin, dan angkat persentase **Konversi** menjadi sebesar mungkin (karena konversi cs yang bagus akan mendongkrak ROAS kuat-kuat).</div></div></div>
                <div class="step"><div class="step-num">4</div><div class="step-body"><div class="step-title">Amati Indikator ROAS</div><div class="step-desc">Perhatikan indikator warna (Hijau = Aman, Merah = Bahaya/Boncos). Atur skenario CPC & Konversi tersebut sampai kamu menemukan _sweet spot_ ideal. Jadikan titik tersebut referensi di lapangan.</div></div></div>
            </div>
            <div class="callout tip"><div class="callout-icon"><i class="fas fa-star"></i></div><div class="callout-body"><strong>Akurasi Penggunaan</strong><p>Bagi UMKM tahap awal, nilai aman dari ROAS adalah saat angkanya di atas rentang 3.0 (Atau warna hijau).</p></div></div>
        </section>
        <div class="cta-section">
            <h2>Uji Teorimu Sekarang!</h2>
            <p>Pindahkan angka-angka mentah pikiranmu ke dalam kalkulator di halaman utama.</p>
            <a href="{{ route('index') }}" class="btn-cta"><i class="fas fa-chart-line"></i> Buka Profit Simulator</a>
        </div>
    </main>
</div>
<script>
window.addEventListener('scroll',()=>{
    const pct=document.body.scrollHeight-window.innerHeight>0?(window.scrollY/(document.body.scrollHeight-window.innerHeight))*100:0;
    document.getElementById('progress-bar').style.width=pct+'%';
});
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
    const map={'s-overview':0,'s-fungsi':1,'s-kerja':2,'s-pakai':3};
    const idx=map[id];if(idx===undefined)return;
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
