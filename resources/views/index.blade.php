<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden w-full">

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/badges.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/badges-v2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/zodiac-borders.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/elements-borders.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/universe-borders.css') }}">
    <style>
        .locked {
            display: none !important;
        }

        /* Mobile SaaS Grade Structure */
        .scroll-row {
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scroll-padding-inline: 1rem;
            /* Hide scrollbar for cleaner look */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scroll-row::-webkit-scrollbar {
            display: none;
        }
        .scroll-mask {
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }
        .card-dynamic {
            flex: 0 0 clamp(120px, 40%, 160px);
            scroll-snap-align: start;
        }
        @media (min-width: 640px) {
            .card-dynamic {
                flex: 0 0 clamp(220px, 70%, 320px);
            }
        }
        .sticky-cta {
            position: sticky;
            bottom: 0;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 40;
            padding-bottom: env(safe-area-inset-bottom);
        }
        .bottom-sheet {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Smart Accordion Logic */
        .smart-accordion > summary { list-style: none; }
        .smart-accordion > summary::-webkit-details-marker { display: none; }
        @media (min-width: 768px) {
            .smart-accordion > summary { pointer-events: none; }
            .smart-accordion > summary .toggle-icon { display: none; }
            .smart-accordion:not([open]) > .smart-accordion-content { display: block; }
        }

        /* Custom Dropdown Styles */
        .custom-dropdown {
            position: relative;
            width: auto;
            min-width: 100px;
        }

        .dropdown-btn {
            width: 100%;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .dark .dropdown-btn {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        .dropdown-btn:hover {
            border-color: #10b981;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 140px;
            overflow: hidden;
        }

        .dark .dropdown-menu {
            background-color: #1e293b;
            border-color: #334155;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 0.6rem 1rem;
            font-size: 0.75rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .dark .dropdown-item {
            color: #94a3b8;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #10b981;
            padding-left: 1.25rem;
        }

        .dark .dropdown-item:hover {
            background-color: #334155;
            color: #10b981;
        }

        .dropdown-item.active {
            background-color: #ecfdf5;
            color: #10b981;
            font-weight: 700;
        }

        .dark .dropdown-item.active {
            background-color: #064e3b;
            color: #34d399;
        }

        /* --- UI Redesign Personalized Roadmap --- */
        .roadmap-wrapper {
            position: relative;
        }

        .roadmap-lines {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .roadmap-cards-container {
            position: relative;
            z-index: 2;
        }

        .connector-base {
            fill: none;
            stroke: rgba(255,255,255,0.1);
            stroke-width: 3;
        }

        .connector-progress {
            fill: none;
            stroke: url(#gradientLine);
            stroke-width: 3;
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 0 6px #10b981);
        }
        /* ------------------------------------------- */

        /* --- Animated Glass Reflection System --- */
        :root {
            --glass-blur: 22px;
            --glass-opacity: .05;
            --glass-reflection-opacity: .25;
        }

        .glass {
            position: relative;
            background: rgba(255, 255, 255, var(--glass-opacity));
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(var(--glass-blur)) saturate(180%);
            -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(180%);
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, .55), 
                inset 0 0 30px rgba(255, 255, 255, .04);
        }

        .glass-reflection {
            position: absolute;
            inset: -40%;
            background: linear-gradient(
                120deg,
                transparent 40%,
                rgba(255, 255, 255, var(--glass-reflection-opacity)) 50%,
                transparent 60%
            );
            transform: translateX(-120%) rotate(8deg);
            animation: glassSweep 6s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes glassSweep {
            0% { transform: translateX(-120%) rotate(8deg); }
            100% { transform: translateX(120%) rotate(8deg); }
        }

        .glass::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence baseFrequency='.8'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.03'/%3E%3C/svg%3E");
            mix-blend-mode: overlay;
            pointer-events: none;
            z-index: 1;
            border-radius: inherit;
        }

        .glass::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                145deg,
                rgba(255, 255, 255, .25),
                transparent 40%
            );
            opacity: .4;
            pointer-events: none;
            z-index: 1;
        }

        @media (prefers-reduced-motion: reduce) {
            .glass-reflection { animation: none; }
        }

        @media (hover: hover) {
            .glass:hover .glass-reflection {
                animation-duration: 2.5s;
                opacity: .7;
            }
        }

        /* All children inside .glass should stack above the reflection layer,
           EXCEPT decoration elements like .glass-reflection and .glow-layer which must stay absolute */
        .glass > *:not(.glass-reflection):not(.glow-layer) {
            position: relative;
            z-index: 2;
        }

        /* Glow decoration layer — must always be absolute, never in layout flow */
        .glow-layer {
            position: absolute !important;
            pointer-events: none;
        }

        .dark .glass {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* --- Hero Layout Structure (Absolute Overlap) --- */
        .cards-wrapper {
            position: relative;
            width: 100%;
            max-width: 100%;
            margin-bottom: 2rem; /* Adjusted for mobile */
            z-index: 10;
        }
        @media (min-width: 768px) {
            .cards-wrapper {
                margin-bottom: 5rem; /* Space for the absolute overlapping card */
            }
        }

        /* Hero card: white on light mode, dark glass on dark mode */
        .card-main {
            background: rgba(255, 255, 255, 0.92) !important; /* Light mode: nearly white */
        }

        .hero-panel {
            background: rgba(255, 255, 255, 0.88) !important; /* Light mode: nearly white */
        }

        .dark .card-main {
            background: rgba(15, 23, 42, 0.85) !important; /* Dark mode: dark slate */
        }

        .dark .hero-panel {
            background: rgba(15, 23, 42, 0.75) !important; /* Dark mode: dark slate */
        }

        .card-main {
            position: relative;
            width: 100%;
            /* min-height dihilangkan agar tinggi menyesuaikan isi konten */
            border-radius: 28px;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Konten di tengah secara vertikal jika ada sisa ruang */
            overflow: hidden;
            z-index: 1;
        }

        /* SMALL CARD (Dynamic Card) */
        .card-overlap {
            position: relative;
            width: 100%;
            margin-top: -24px; /* overlap ke hero section di atasnya */
            z-index: 10;
        }

        @media (max-width: 767px) {
            .card-overlap {
                padding: 0 16px;
            }
        }

        @media (min-width: 768px) {
            .card-overlap {
                position: absolute;
                right: 0;
                bottom: -4rem;
                width: 440px;
                margin-top: 0;
                padding: 0;
                z-index: 2;
                transform-origin: bottom right;
                transform: scale(0.75);
                transition: transform 0.3s ease-out;
            }
        }

        @media (min-width: 1024px) {
            .card-overlap {
                bottom: -5rem;
                transform: scale(1);
            }
        }

    </style>
    
    <!-- GLOBAL AUTH STATE -->
    <script>
        window.AUTH = {
            loggedIn: @json(auth()->check()),
            user: @json(auth()->user()),
            csrf: '{{ csrf_token() }}'
        };

        window.saveGuestSimulationAndLogin = function() {
            if (window.latestResult && window.manualBaseline) {
                const payload = {
                    baseline: window.manualBaseline,
                    simulation: window.latestResult,
                    timestamp: new Date().getTime()
                };
                localStorage.setItem('cuan_guest_simulation_save', JSON.stringify(payload));
            }
            window.location.href = "{{ route('login') }}?intent=save_simulation";
        };
    </script>
    
    <script type="module">
        import { initAuthListener } from './assets/js/core/auth-engine.js';
        initAuthListener();
    </script>

    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        window.savedPlannerData = @json($latestSession);
        window.savedSimulationData = @json($latestSimulation);
        window.initialBlueprint = @json($latestBlueprint);
        window.blueprintData = @json(session('blueprintData') ?? null);
        
        // Dirty State Tracking
        window.reverseGoalDirty = false;
        window.simulationDirty = false;
        
        // --- 🚀 Guest Mode Recovery System (Post-Login Hook) ---
        // Jika user baru login dan punya tabungan data simulasi dari Mode Guest,
        // kita pulihkan data tersebut agar UX mulus tanpa harus repot ketik ulang!
        if (window.AUTH.loggedIn) {
            const savedGuestData = localStorage.getItem('cuan_guest_simulation_save');
            if (savedGuestData) {
                try {
                    const parsedData = JSON.parse(savedGuestData);
                    // Cek masa kedaluwarsa (misal max 24 jam)
                    const now = new Date().getTime();
                    if (now - parsedData.timestamp < (24 * 60 * 60 * 1000)) {
                        // Timpa state awal dengan data dari Guest Session
                        window.initialBlueprint = parsedData.baseline;
                        window.savedSimulationData = parsedData.simulation;
                        // Tandai form kotor agar langsung merender dan menagih simpan ke backend
                        window.reverseGoalDirty = true;
                        window.simulationDirty = true;
                        console.log("💎 [Guest Recovery] Data kalkulasi kamu yang belum tersimpan berhasil dipulihkan!");
                    } else {
                        console.warn("⚠️ [Guest Recovery] Sesi tamu sudah kedaluwarsa.");
                    }
                } catch (e) {
                    console.error("Kesalahan membaca sesi Guest:", e);
                } finally {
                    // Bersihkan jejak dari localStorage setelah direnggut
                    localStorage.removeItem('cuan_guest_simulation_save');
                    
                    // Bersihkan parameter ?intent= URI agar tidak nyangkut
                    if (window.history.replaceState) {
                        const url = new URL(window.location);
                        url.searchParams.delete('intent');
                        window.history.replaceState({path: url.href}, '', url.href);
                    }
                }
            }
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuanCapital – Simulator Cuan Sebelum Kamu Bakar Modal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/icon/logo-2.svg') }}" type="image/svg+xml">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script>
        // It's best to inline this in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <link rel="preload" as="style" href="{{ asset('assets/css/style.css') }}">
    <link rel="preload" as="script" href="{{ asset('assets/js/main.js') }}">
    <script type="module" src="{{ asset('assets/js/core/system-handler.js') }}"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Learning Layer Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/experience.css') }}?v=19.1">
    <style>
        .glossary-tooltip {
            position: absolute;
            background: rgba(20,20,30,0.95);
            border: 1px solid rgba(0,255,200,0.4);
            backdrop-filter: blur(10px);
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,255,200,0.3);
            max-width: 300px;
            z-index: 9999;
            animation: fadeInTooltip 0.2s ease-out;
            color: #fff;
        }
        .glossary-tooltip h4 { margin: 0 0 8px 0; color: #00ffc8; font-size: 16px; font-weight: bold; }
        .glossary-tooltip p { margin: 0; font-size: 14px; line-height: 1.4; color: #cbd5e1; }
        .glossary-tooltip .formula { margin-top: 10px; padding: 10px; background: rgba(0,0,0,0.4); border-radius: 8px; font-family: monospace; font-size: 12px; color: #60a5fa; border: 1px solid rgba(96, 165, 250, 0.2); }
        
        @keyframes fadeInTooltip {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .learning-banner {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #00ffc8, #0066ff);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            animation: slideDownBanner 0.3s ease-out;
        }
        @keyframes slideDownBanner {
            from { opacity: 0; transform: translate(-50%, -20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans antialiased overflow-x-hidden transition-colors duration-300 locked-screen">


    <!-- System Broadcast Banner -->
    <div id="system-broadcast"
        class="hidden fixed top-0 left-0 right-0 z-[110] flex items-center gap-x-6 bg-amber-600 px-6 py-2.5 sm:px-3.5 sm:before:flex-1 transition-all duration-300">
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
            <p class="text-sm leading-6 text-white">
                <strong class="font-bold"><i class="fas fa-bullhorn mr-2"></i>Announcement</strong>
                <span id="broadcast-text" class="font-medium">System update in progress...</span>
            </p>
        </div>
        <div class="flex flex-1 justify-end">
            <button type="button"
                class="-m-3 p-3 focus-visible:outline-offset-[-4px] hover:bg-amber-700/50 rounded-lg transition"
                onclick="hideBroadcastBanner()">
                <span class="sr-only">Dismiss</span>
                <i class="fas fa-times text-white text-sm"></i>
            </button>
        </div>
    </div>
    <script>
        function hideBroadcastBanner() {
            const banner = document.getElementById('system-broadcast');
            const nav = document.getElementById('main-nav');
            const mainEl = document.querySelector('main');
            if (banner) banner.classList.add('hidden');
            if (nav) nav.style.top = '0px';
            if (mainEl) { mainEl.style.marginTop = ''; }
        }
    </script>


    <!-- Main Nav -->
    <nav id="main-nav"
        class="fixed top-0 z-[100] bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800/80 shadow-sm transition-colors duration-300 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 sm:h-16 items-center">
                <div class="flex items-center gap-2">
                    <span class="text-2xl"></span>
                    <img id="site-logo" src="{{ asset('assets/icon/logo.svg') }}" alt="CuanCapital Logo" class="h-6 sm:h-8 md:h-10">
                </div>
                <div class="flex items-center gap-1 sm:gap-2 md:gap-4">
                    <!-- Admin Only Link -->
                    <a href="{{ route('admin') }}"
                        class="hidden admin-only p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors mr-1"
                        title="Admin Dashboard">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </a>

                    <!-- Gamification XP Bar UI (Phase 16) -->
                    <div class="relative z-50 hidden auth-only" id="xp-bar-wrapper">
                        <div class="xp-container flex flex-col justify-center mr-1 sm:mr-2 md:mr-4 w-28 sm:w-32 md:w-36 lg:w-48 cursor-pointer group"
                             title="View Achievements & Badges" onclick="openBadgeModal()">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-[9px] md:text-[10px] font-bold text-amber-400 tracking-widest transition-colors group-hover:text-emerald-400 truncate w-full flex items-center gap-1">
                                    <span id="level-badge" class="uppercase">Lvl 1</span>&bull;<span id="xp-count" class="tabular-nums">0</span> XP
                                </span>
                            </div>
                            <div class="xp-bar w-full h-1.5 bg-slate-800 rounded-full overflow-hidden border border-white/5">
                                <div id="xp-progress-bar" class="h-full w-0 bg-gradient-to-r from-emerald-500 to-cyan-400 transition-all duration-700 ease-out shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                            </div>
                        </div>
                    </div>




                    <!-- Settings Dropdown -->
                    <div class="relative hidden auth-only">
                        <button id="settings-menu-btn"
                            class="p-1.5 sm:p-2 text-slate-500 dark:text-slate-400 hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none"
                            title="Settings">
                            <i class="fas fa-cog text-lg sm:text-xl"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="settings-dropdown"
                            class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 transform origin-top-right transition-all duration-200">
                            <div class="py-1">
                                <a href="{{ route('settings') }}"
                                    class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400">
                                    <i class="fas fa-cog w-5 text-center"></i> Settings
                                </a>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                
                                <!-- Learning Mode Toggle (Moved to Dropdown) -->
                                <button id="nav-learning-btn" class="w-full text-left flex items-center px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group group-[.active]" title="Learning Mode">
                                    <div class="relative w-5 h-5 mr-3 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center transition-colors group-[.active]:bg-emerald-500 text-slate-400 group-[.active]:text-white flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-[10px]"></i>
                                    </div>
                                    <span class="font-medium flex-1">
                                        Learning Mode <span class="text-xs group-[.active]:hidden text-slate-400 ml-1">OFF</span><span class="hidden text-xs group-[.active]:inline text-emerald-500 ml-1">ON</span>
                                    </span>
                                </button>
                                
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <button id="dropdown-logout-btn"
                                    class="w-full text-left block px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-700 dark:hover:text-rose-300 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5 text-center"></i> Log Out
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Guest Login CTA -->
                    <button onclick="window.openLoginModal('login')" class="hidden guest-only inline-flex items-center gap-1 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs sm:text-sm font-bold rounded-xl transition-colors shadow-lg shadow-emerald-500/20 active:scale-95">
                        <i class="fas fa-sign-in-alt"></i> <span class="hidden sm:inline">Masuk / Daftar</span>
                    </button>
                    
                    <div class="hidden md:flex items-center space-x-6">
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12 mt-14 sm:mt-16">

        <section id="hero" class="relative pt-4 pb-24 lg:pt-8 lg:pb-40 max-w-7xl mx-auto">

            <div class="absolute inset-0 -z-10 h-full w-full bg-white dark:bg-slate-900 bg-[size:6rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-20 pointer-events-none"
                style="background-image: linear-gradient(0deg, transparent 24%, #e2e8f0 25%, #e2e8f0 26%, transparent 27%, transparent 74%, #e2e8f0 75%, #e2e8f0 76%, transparent 77%, transparent), linear-gradient(90deg, transparent 24%, #e2e8f0 25%, #e2e8f0 26%, transparent 27%, transparent 74%, #e2e8f0 75%, #e2e8f0 76%, transparent 77%, transparent);">
            </div>

            <!-- Smooth Scroll & Layout Update -->
            <style>html { scroll-behavior: smooth; }</style>

            <div class="cards-wrapper" id="desktop-cards-wrapper">
                
                <!-- NEW Control Center Card (Main Card) -->
                <div class="card-main glass p-5 pt-4 pb-12 md:p-8 md:pt-6 md:pb-8 pr-4 sm:pr-8 md:pr-[40%] lg:pr-[460px] group" role="region" aria-label="Hero Control Center">
                    <div class="glass-reflection"></div>
                    <!-- Glow effect -->
                        <div class="glow-layer top-0 right-0 w-64 h-64 bg-emerald-500/10 blur-[80px] rounded-full group-hover:bg-emerald-500/20 transition-all duration-1000"></div>

                        <!-- Unified Client-Side Auth Rendering (Production Hardened) -->
                        <div class="flex items-center gap-3 md:gap-4 mb-6 z-10 w-full" role="region" aria-label="User Control Center">
                            <!-- Profile Image (Badge V2 container) -->
                            <div class="w-12 h-12 md:w-14 md:h-14 rounded-full border-2 border-emerald-500 shadow-md aspect-square bg-slate-700 relative flex-shrink-0 cursor-pointer hover:scale-105 transition-all duration-300" id="hero-profile-container" aria-label="User Avatar" role="img" onclick="openBadgeModal()">
                                <div id="hero-badge-fx" class="absolute -inset-1 rounded-full pointer-events-none"></div>
                                <img id="hero-avatar" 
                                     src="https://ui-avatars.com/api/?name=Guest&background=1e293b&color=fff" 
                                     alt="Profile" class="w-full h-full rounded-full object-cover transition-opacity duration-300">
                            </div>

                            <!-- Text Content -->
                            <div class="flex-1 min-w-0">
                                <h2 id="hero-greeting" class="text-[13px] sm:text-lg font-semibold text-slate-900 dark:text-white leading-tight w-full break-words flex items-center flex-wrap gap-x-2">
                                    <span><span id="greeting-time">Selamat Malam</span>, <br class="sm:hidden"><span id="greeting-name">Calon Sultan</span></span>
                                </h2>
                                
                                <div class="flex flex-col items-start gap-1.5 mt-2">
                                    <!-- Equipped Badge Name (Auth Only) -->
                                    <span id="hero-equipped-badge-name" class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-600/50 text-[9px] md:text-[10px] font-bold text-slate-700 dark:text-slate-300 shadow-sm backdrop-blur-sm transition-all max-w-full truncate">
                                        <i id="hero-badge-icon" class="fas fa-medal text-slate-400 shrink-0"></i> <span id="hero-badge-text" class="truncate">No Badge</span>
                                    </span>
                                </div>

                                <!-- Login CTA (Guest Only) -->
                                <button onclick="window.openLoginModal('login')" id="hero-login-cta" class="hidden guest-only inline-flex items-center gap-1 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition-colors mt-1" aria-label="Login to Dashboard">
                                    Login untuk personal dashboard <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ================= GUEST HERO SECTION ================= -->
                        <div class="guest-only block w-full transition-all duration-500">
                            <!-- 2. Headline -->
                            <h1 class="text-[clamp(1.2rem,4vw,3.75rem)] sm:text-[clamp(1.5rem,5vw,3.75rem)] font-black text-slate-900 dark:text-white leading-tight z-10 mb-3 md:mb-4 tracking-tight w-full" style="word-break: break-word;">
                                Hitung Potensi Cuan <br class="hidden sm:block">
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400">Sebelum Kamu Bakar Modal.</span>
                            </h1>

                            <!-- 3. Subheadline -->
                            <p class="text-[clamp(0.8rem,2vw,1.1rem)] text-slate-600 dark:text-slate-300 max-w-xl z-10 mb-6 md:mb-8 leading-relaxed">
                                Masukkan angka sederhana. Lihat simulasi <strong class="text-slate-800 dark:text-slate-100 font-semibold">profit, risiko, dan cashflow bisnis kamu</strong> — dalam 60 detik. Tanpa ribet.
                            </p>

                            <!-- 4. CTA Button Group -->
                            <div class="flex flex-col items-start gap-2 z-10 w-full mb-2 relative">
                                <!-- Microcopy -->
                                <p class="text-[10px] md:text-xs text-slate-400 dark:text-slate-500 font-medium flex items-center gap-1.5">
                                    <i class="fas fa-lock text-emerald-400 text-[9px]"></i>
                                    Gratis. Tidak perlu kartu kredit.
                                </p>
                                <div class="flex flex-row flex-wrap items-center gap-3">
                                    <!-- Primary CTA: Coba Hitung Gratis -->
                                    <button type="button" onclick="const tgt = document.getElementById('reverse-planner-section'); if(tgt) { tgt.scrollIntoView({behavior:'smooth'}); }" 
                                            class="w-auto relative group overflow-hidden rounded-xl transition-all hover:scale-[1.02] active:scale-95 shadow-[0_0_24px_rgba(16,185,129,0.35)] border border-emerald-500/60 bg-emerald-500/15 backdrop-blur-xl hover:bg-emerald-500/25">
                                        <div class="relative flex items-center justify-center gap-2 px-5 md:px-8 py-2.5 md:py-3.5">
                                            <span class="relative text-emerald-400 text-xs md:text-base group-hover:text-emerald-300 font-black tracking-wide flex items-center gap-2 transition-colors">
                                                <i class="fas fa-calculator"></i> Coba Hitung Gratis
                                            </span>
                                        </div>
                                        <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent"></div>
                                    </button>
                                    
                                    <!-- Secondary CTA: Lihat Contoh Hasil (HIDDEN to reduce friction) -->
                                    <button onclick="window.openLoginModal('login')" 
                                            class="hidden w-auto relative group overflow-hidden rounded-xl transition-all hover:scale-[1.02] active:scale-95 shadow-sm bg-transparent hover:bg-white/5 border border-slate-300 dark:border-slate-700">
                                        <div class="relative flex items-center justify-center gap-2 px-4 md:px-6 py-2.5 md:py-3.5">
                                            <span class="relative text-slate-600 dark:text-slate-300 text-xs md:text-base group-hover:text-slate-900 dark:group-hover:text-white font-semibold flex items-center gap-2 transition-colors">
                                                <i class="fas fa-chart-bar text-slate-400"></i> Masuk / Daftar
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ================= AUTH HERO SECTION ================= -->
                        <div class="auth-only hidden w-full transition-all duration-500">

                            <!-- 2. Headline -->
                            <h1 class="text-[clamp(1.2rem,4vw,3.75rem)] sm:text-[clamp(1.5rem,5vw,3.75rem)] font-black text-slate-900 dark:text-white leading-tight z-10 mb-3 md:mb-4 tracking-tight w-full" style="word-break: break-word;">
                                Lanjutkan Perjalananmu <br class="hidden sm:block">
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400">Menuju Profit Pertama.</span>
                            </h1>

                            <!-- 3. Subheadline -->
                            <p class="text-[clamp(0.8rem,2vw,1.05rem)] text-slate-600 dark:text-slate-300 max-w-md z-10 mb-5 md:mb-7 leading-relaxed">
                                Tahu kapan bisnis kamu mulai untung.
                                <strong class="text-slate-800 dark:text-slate-100 font-semibold">Tahu berapa minimal penjualan supaya tidak rugi.</strong> Semua di satu tempat.
                            </p>

                            <!-- 4. CTA: App-Like Dominant Widget Action -->
                            <div class="flex flex-col items-start gap-4 z-10 w-full mb-2 relative">
                                <!-- Primary Action: Fat Button Widget -->
                                <button type="button" onclick="const tgt = document.getElementById('reverse-planner-section'); if(tgt) { tgt.scrollIntoView({behavior:'smooth'}); }"
                                        class="group relative w-full sm:w-auto inline-flex items-center justify-between gap-5 p-2 pr-4 md:pr-6 bg-slate-900/50 dark:bg-white/5 backdrop-blur-md border border-slate-300 dark:border-white/10 rounded-2xl hover:bg-slate-900/60 dark:hover:bg-white/10 transition-all hover:scale-[1.02] active:scale-95 shadow-xl sm:min-w-[340px]">
                                    
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                            <i class="fas fa-calculator text-white text-lg md:text-xl"></i>
                                        </div>
                                        <div class="text-left">
                                            <p class="text-emerald-500 dark:text-emerald-400 font-black text-sm md:text-base leading-tight">Mulai Simulasi Profit</p>
                                            <p class="text-slate-500 dark:text-slate-400 text-[10px] md:text-xs mt-0.5 font-medium flex items-center gap-1.5">
                                                <i class="fas fa-bolt text-amber-400"></i> Selesai dalam 60 detik
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="w-8 h-8 rounded-full bg-slate-200/20 dark:bg-white/10 flex items-center justify-center group-hover:bg-emerald-500 transition-colors shadow-sm group-hover:shadow-emerald-500/50">
                                        <i class="fas fa-arrow-right text-slate-500 dark:text-slate-400 group-hover:text-white text-sm transform group-hover:translate-x-0.5 transition-all"></i>
                                    </div>
                                    
                                    <!-- Embedded Edge Glow -->
                                    <div class="absolute inset-0 rounded-2xl border-2 border-emerald-500/0 group-hover:border-emerald-500/20 pointer-events-none transition-colors"></div>
                                </button>

                                <!-- Secondary Context: Merged line to reduce cognitive load -->
                                <div class="px-2 flex flex-wrap items-center gap-2 text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide">
                                    <span>Atau eksplorasi:</span>
                                    <button onclick="switchDesktopNavTab('product'); document.getElementById('desktop-nav-tabs').scrollIntoView({behavior:'smooth', block:'start'})" class="hover:text-emerald-500 underline decoration-slate-300 dark:decoration-slate-700 underline-offset-4 transition-colors">Semua Produk</button>
                                    <span class="text-slate-300 dark:text-slate-700">&bull;</span>
                                    <button onclick="switchDesktopNavTab('tools'); document.getElementById('desktop-nav-tabs').scrollIntoView({behavior:'smooth', block:'start'})" class="hover:text-amber-500 underline decoration-slate-300 dark:decoration-slate-700 underline-offset-4 transition-colors flex items-center gap-1.5">
                                        Premium Tools <i class="fas fa-crown text-[9px] text-amber-500/70 mb-0.5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                     </div>
                <!-- </div> removed closing tag here to keep overlap card INSIDE cards-wrapper -->

                <!-- Visual Content (Dynamic Card / Overlap Card) -->
                <!-- Removed animate-fade-in-up from wrapper because transform resets absolute positioning context in some browsers, applied inner instead -->
                <div class="card-overlap auth-only">
                     <!-- 
                        Card container scaling adjusted for overlap layout 
                     -->
                    <div class="origin-center lg:origin-right transform-gpu translate-x-0 w-full h-full animate-fade-in-up delay-200">
                        
                        <!-- Dynamic Card (Interactive & Advanced) -->
                        <div onclick="if(window.safeScrollTo) window.safeScrollTo('reverse-planner-section', event);"
                            class="relative z-10 max-w-md mx-auto hover:-translate-y-2 hover:shadow-emerald-500/20 transition-all duration-300 cursor-pointer group active:scale-95 transform md:scale-100 origin-top bg-white/5">
                            
                            <!-- Floating Badge: context label -->
                            <div class="absolute -top-3 -right-3 bg-slate-900 text-white border border-slate-700 px-3 py-1 rounded-full shadow-lg z-20 flex items-center gap-1.5">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs sm:text-[11px] font-bold uppercase tracking-wider">live Simulation</span>
                            </div>

                            <!-- Absolute Mascot Container (Moved to Left, outside glass) -->
                            <div id="mascot-container" class="absolute -top-4 -left-6 sm:-top-6 sm:-left-10 z-30 w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 cursor-pointer transition-transform hover:scale-105 active:scale-95">
                                <div class="relative w-full h-full">
                                    <div class="absolute inset-0 bg-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
                                    <!-- Interactive Mascot -->
                                    <div class="relative w-full h-full rounded-2xl flex items-center justify-center group/mascot">
                                        <img loading="lazy" id="mascot-image" src="{{ asset('assets/icon/mascotfinal.png') }}" alt="Mascot Aksa" 
                                            class="absolute -bottom-2 w-[160%] h-[160%] max-w-none object-contain drop-shadow-2xl origin-bottom transition-transform duration-300">
                                        
                                        <!-- Chat Bubble -->
                                        <div id="mascot-bubble" class="absolute -top-10 left-20 w-36 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-bold px-3 py-2 rounded-xl shadow-xl ring-1 ring-slate-900/5 opacity-0 pointer-events-none transition-all duration-300 z-50 text-center leading-tight whitespace-normal transform origin-bottom-left scale-90">
                                            <span id="mascot-bubble-text">Yuk, Aksa temenin capai target kamu!</span>
                                            <div class="absolute bottom-3 -left-1 w-2 h-2 bg-white dark:bg-slate-800 transform rotate-45"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- The Glass Background & Container -->
                            <div class="hero-panel glass p-6 overflow-hidden relative w-full h-full">
                                <div class="glass-reflection"></div>

                                <!-- Card Header (Aligned Right) -->
                                <div class="mb-5 pl-16 text-right relative z-10">
                                    <p class="text-xs sm:text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mb-1">Model Bisnis</p>
                                    <h3 id="hero-card-title" class="font-black text-2xl sm:text-2xl text-slate-900 dark:text-white group-hover:text-emerald-400 transition-colors">Model Bisnis</h3>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 italic">Beginilah hasil simulasi akan terlihat.</p>
                                </div>

                                <!-- Main Goal -->
                                <div class="mb-6 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800">
                                    <div class="flex justify-between items-end mb-1">
                                        <p class="biz-term text-base sm:text-sm text-slate-500 font-medium cursor-help" data-term="net_profit">Target Profit/Bulan </p>
                                        <i class="fas fa-bullseye text-emerald-500/50 text-base sm:text-sm"></i>
                                    </div>
                                    <h3 id="hero-card-target" class="text-4xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300 tracking-tight">Rp 100 Juta</h3>
                                </div>

                                <!-- Stats 2x2 Grid -->
                                <div class="grid grid-cols-2 gap-3 mb-5 mt-2 relative z-10">
                                    <!-- Traffic -->
                                    <div class="bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-emerald-100 dark:border-emerald-900/40 hover:border-emerald-400/50 transition-colors group/stat">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="biz-term text-xs sm:text-[11px] text-slate-400 uppercase font-bold cursor-help" data-term="traffic">Traffic </p>
                                            <i class="fas fa-users text-base sm:text-sm text-emerald-400/60"></i>
                                        </div>
                                        <p id="hero-card-traffic" class="font-black text-slate-700 dark:text-slate-100 text-xl sm:text-lg leading-none">15.2K</p>
                                        <p class="text-[11px] sm:text-[10px] text-slate-400 mt-1">pengunjung/bln</p>
                                    </div>
                                    <!-- Conversion -->
                                    <div class="bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-blue-100 dark:border-blue-900/40 hover:border-blue-400/50 transition-colors group/stat">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="biz-term text-xs sm:text-[11px] text-slate-400 uppercase font-bold cursor-help" data-term="conversion_rate">Konversi </p>
                                            <i class="fas fa-funnel-dollar text-base sm:text-sm text-blue-400/60"></i>
                                        </div>
                                        <p id="hero-card-conv" class="font-black text-blue-500 text-xl sm:text-lg leading-none">2.4%</p>
                                        <p class="text-[11px] sm:text-[10px] text-slate-400 mt-1">dari total traffic</p>
                                    </div>
                                    <!-- Sales Target -->
                                    <div class="bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-amber-100 dark:border-amber-900/40 hover:border-amber-400/50 transition-colors group/stat">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs sm:text-[11px] text-slate-400 uppercase font-bold">Sales Target</p>
                                            <i class="fas fa-shopping-cart text-base sm:text-sm text-amber-400/60"></i>
                                        </div>
                                        <p id="hero-card-sales" class="font-black text-slate-700 dark:text-slate-100 text-xl sm:text-lg leading-none">— Unit</p>
                                        <p class="text-[11px] sm:text-[10px] text-slate-400 mt-1">penjualan/bln</p>
                                    </div>
                                    <!-- Price -->
                                    <div class="bg-white dark:bg-slate-800/80 p-4 rounded-xl border border-violet-100 dark:border-violet-900/40 hover:border-violet-400/50 transition-colors group/stat">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs sm:text-[11px] text-slate-400 uppercase font-bold">Harga Jual</p>
                                            <i class="fas fa-tag text-base sm:text-sm text-violet-400/60"></i>
                                        </div>
                                        <p id="hero-card-price" class="font-black text-slate-700 dark:text-slate-100 text-xl sm:text-lg leading-none">Rp —</p>
                                        <p class="text-[11px] sm:text-[10px] text-slate-400 mt-1">per unit produk</p>
                                    </div>
                                </div>

                            <!-- Probability Footer -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-[10px] text-slate-400">
                                    <span>Probabilitas Keberhasilan</span>
                                    <span id="hero-card-progress-text" class="font-bold text-emerald-500">High</span>
                                </div>
                                <div class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div id="hero-card-progress-bar" class="h-full bg-emerald-500 w-[75%] rounded-full animate-pulse transition-all duration-1000"></div>
                                </div>
                            </div>
                            
                            </div> <!-- End of .glass panel -->
                        </div>

                        <!-- Decorative Elements -->
                        <div class="absolute -z-10 top-10 -right-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
                        <div class="absolute -z-10 -bottom-10 -left-10 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
                </div>

            </div>

            <!-- STATIC SOCIAL PROOF -->
            <div class="mt-8 md:mt-16 w-full max-w-3xl mx-auto flex flex-col items-center justify-center gap-4 px-4 guest-only animate-fade-in delay-300">
                <div class="flex items-center gap-3 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200 dark:border-slate-700/50 rounded-2xl px-5 py-3 shadow-lg shadow-slate-200/50 dark:shadow-none">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-50 dark:border-slate-900 bg-slate-200" src="https://ui-avatars.com/api/?name=BU&background=10b981&color=fff" alt="User">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-50 dark:border-slate-900 bg-slate-200" src="https://ui-avatars.com/api/?name=AD&background=3b82f6&color=fff" alt="User">
                        <img class="w-8 h-8 rounded-full border-2 border-slate-50 dark:border-slate-900 bg-slate-200" src="https://ui-avatars.com/api/?name=IR&background=f59e0b&color=fff" alt="User">
                        <div class="w-8 h-8 rounded-full border-2 border-slate-50 dark:border-slate-900 bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500">
                            1k+
                        </div>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">
                        Sudah <span class="font-bold text-emerald-600 dark:text-emerald-400">1.243 simulasi</span> dilakukan dalam 30 hari terakhir.
                    </div>
                </div>
            </div>






    <script>
        window.hydrateHeroUI = function() {
            const greetingTimeEl = document.getElementById('greeting-time');
            const greetingNameEl = document.getElementById('greeting-name');
            const avatarEl = document.getElementById('hero-avatar');
            const statusBadge = document.getElementById('hero-status-badge');
            const loginCta = document.getElementById('hero-login-cta');
            const profileContainer = document.getElementById('hero-profile-container');

            // 1. Time-based Greeting
            const h = new Date().getHours();
            const timeGreeting = h < 11 ? "Selamat Pagi" : h < 15 ? "Selamat Siang" : h < 18 ? "Selamat Sore" : "Selamat Malam";
            
            // 2. Auth Check (Read from sessionStorage populated by auth-engine.js)
            const userName = sessionStorage.getItem('cuan_user_display_name');
            const userAvatar = sessionStorage.getItem('cuan_user_avatar');
            const hasToken = !!localStorage.getItem('auth_token');

            if (userName) {
                // AUTHENTICATED STATE
                if (greetingTimeEl) greetingTimeEl.innerText = timeGreeting;
                if (greetingNameEl) greetingNameEl.innerText = userName;
                
                if (avatarEl) {
                    try {
                        avatarEl.src = userAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=10b981&color=fff`;
                    } catch (e) {
                         avatarEl.src = "https://ui-avatars.com/api/?name=User&background=10b981&color=fff";
                    }
                }
                
                // Show Badge, Hide Login CTA
                if (statusBadge) statusBadge.classList.remove('hidden');
                if (loginCta) loginCta.classList.add('hidden');
                
                // ── Clean Auth Bootstrap ─────────────────────────────────────────────────
                // Isolated state — badge and border NEVER share state
                const profileState = {
                    badge: null,
                    border: null
                };

                // Ensure profile container is in base state (no badge-fx attribute)
                if (profileContainer) {
                    profileContainer.removeAttribute('data-badge-fx');
                    profileContainer.classList.remove('border-badge-bronze','border-badge-silver','border-badge-gold','border-badge-platinum','border-badge-diamond','border-badge-mythic');
                    profileContainer.classList.add('bg-slate-700', 'border-2', 'border-slate-700');
                    // Restore clean avatar HTML (removes any previous BadgeFX-injected canvases/SVGs)
                    profileContainer.innerHTML = `<div id="hero-badge-fx" class="absolute -inset-1 rounded-full pointer-events-none hidden"></div><img id="hero-avatar" src="${userAvatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=10b981&color=fff`}" alt="Profile" class="w-full h-full rounded-full object-cover z-10 relative">`;
                }

                // ── Renderer: Badge (ONLY name label — NO border) ────────────────────────
                function renderBadgeLabel(badge) {
                    profileState.badge = badge;
                    const badgeNameEl = document.getElementById('hero-equipped-badge-name');
                    if (!badgeNameEl) return;

                    if (!badge) {
                        badgeNameEl.className = 'inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-600/50 text-[9px] md:text-[10px] font-bold text-slate-700 dark:text-slate-300 shadow-sm backdrop-blur-sm transition-all max-w-full truncate';
                        badgeNameEl.innerHTML = `<i class="fas fa-medal text-slate-400 shrink-0"></i> <span class="truncate">No Badge</span>`;
                        badgeNameEl.classList.remove('hidden');
                        return;
                    }

                    const rarityColors = {
                        'bronze':   'text-amber-500 border-amber-700/50 bg-amber-900/30',
                        'silver':   'text-slate-300 border-slate-500/50 bg-slate-700/30',
                        'gold':     'text-yellow-400 border-yellow-500/50 bg-yellow-900/40',
                        'platinum': 'text-teal-200 border-teal-500/50 bg-teal-900/40',
                        'diamond':  'text-cyan-300 border-cyan-500/50 bg-cyan-900/40',
                        'mythic':   'text-fuchsia-400 border-fuchsia-500/50 bg-fuchsia-900/40'
                    };
                    const colorClass = rarityColors[badge.rarity] || 'text-slate-300 border-slate-600/50 bg-slate-800/80';
                    badgeNameEl.className = `inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full text-[9px] md:text-[10px] font-bold shadow-sm backdrop-blur-sm transition-all border max-w-full truncate ${colorClass}`;
                    badgeNameEl.innerHTML = `<i class="fas fa-medal shrink-0"></i> <span class="truncate">${badge.name}</span>`;
                    badgeNameEl.classList.remove('hidden');
                }

                // ── Renderer: Border (ONLY avatar ring — NO badge touch) ─────────────────
                function renderAvatarBorder(border) {
                    profileState.border = border;
                    const fxEl = document.getElementById('hero-badge-fx');
                    if (!fxEl) return;

                    if (!border || !border.css_class) {
                        fxEl.className = 'absolute -inset-1 rounded-full pointer-events-none hidden';
                        return;
                    }

                    fxEl.className = `absolute -inset-1 rounded-full pointer-events-none border-2 ${border.css_class} transition-all duration-300`;
                }

                // ── Fetch badge and border in parallel — completely independent ──────────
                Promise.all([
                    fetch('/api/me/badges', { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } }).then(r => r.json()),
                    fetch('/api/me/borders', { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } }).then(r => r.json())
                ]).then(([badgeJson, borderJson]) => {
                    // Render badge label
                    window.cuanUserBadges = badgeJson.data?.badges || [];
                    const equippedBadge = window.cuanUserBadges.find(b => b.is_equipped) || null;
                    renderBadgeLabel(equippedBadge);

                    // Render border — completely separate, does NOT reference badge
                    const equippedBorder = borderJson.success ? (borderJson.data?.equipped_border || null) : null;
                    renderAvatarBorder(equippedBorder);

                    // Expose clean updaters globally so equipBadge / equipBorder can call them independently
                    window.renderBadgeLabel = renderBadgeLabel;
                    window.renderAvatarBorder = renderAvatarBorder;
                    window.profileState = profileState;
                }).catch(console.error);
            } else if (!hasToken) {
                // GUEST STATE (Only render if there is NO token present!)
                if (greetingTimeEl) greetingTimeEl.innerText = "Selamat Datang";
                if (greetingNameEl) greetingNameEl.innerText = "Calon Sultan"; // Or just 'Guest'
                
                // Hide Badge, Show Login CTA
                if (statusBadge) statusBadge.classList.add('hidden');
                if (loginCta) {
                    loginCta.classList.remove('hidden');
                    loginCta.innerHTML = 'Login untuk klaim akses <i class="fas fa-arrow-right"></i>';
                    loginCta.className = 'hidden guest-only inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors mt-1 hover:underline';
                }
                
                // Allow generic avatar or modify container for guest look
                if (avatarEl) avatarEl.src = "https://ui-avatars.com/api/?name=Guest&background=1e293b&color=fff";
                
            } else {
                // LOADING STATE (Token exists, awaiting auth_resolved)
                if (greetingTimeEl) greetingTimeEl.innerText = "Menyelaraskan";
                if (greetingNameEl) greetingNameEl.innerHTML = 'Data... <i class="fas fa-spinner fa-spin text-emerald-500 ml-1"></i>';
                
                if (statusBadge) statusBadge.classList.add('hidden');
                if (loginCta) loginCta.classList.add('hidden'); // keep login hidden while loading
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            if (!window.__greetingLoaded) {
                window.__greetingLoaded = true;
                
                // Session Sync Safety: Ensure token exists before trusting sessionStorage
                if (!localStorage.getItem('auth_token')) {
                    sessionStorage.clear();
                }

                window.hydrateHeroUI();
            }
        });

        // Listen for the global event from auth-engine.js to re-hydrate UI flawlessly after login
        window.addEventListener('auth_resolved', function() {
            window.hydrateHeroUI();
        });
    </script>

        <!-- ═══════════════════════════════════════════════════════
             MOBILE UI SYNC — mirrors desktop hero to mobile navbar
             & mobile hero section. Called after hydrateHeroUI.
             ═══════════════════════════════════════════════════════ -->
        <script>
        (function() {
            function syncMobileNavbarUI() {
                const userName   = sessionStorage.getItem('cuan_user_display_name');
                const userAvatar = sessionStorage.getItem('cuan_user_avatar');
                const hasToken   = !!localStorage.getItem('auth_token');
                const h = new Date().getHours();
                const timeGreeting = h < 11 ? 'Selamat Pagi' : h < 15 ? 'Selamat Siang' : h < 18 ? 'Selamat Sore' : 'Selamat Malam';
                const avatarUrl = userAvatar ||
                    (userName ? `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=10b981&color=fff`
                              : 'https://ui-avatars.com/api/?name=Guest&background=1e293b&color=fff');

                // ── Mobile Navbar avatar ──────────────────────────────────
                const mNavAvatar = document.getElementById('m-nav-avatar');
                if (mNavAvatar) mNavAvatar.src = avatarUrl;

                // ── Mobile Hero avatar + greeting ─────────────────────────
                const mHeroAvatar = document.getElementById('m-hero-avatar');
                if (mHeroAvatar) mHeroAvatar.src = avatarUrl;

                const greetTime = document.getElementById('m-hero-greeting-time');
                const greetName = document.getElementById('m-hero-greeting-name');
                if (greetTime) greetTime.textContent = timeGreeting;
                if (greetName) greetName.textContent = userName || 'Calon Sultan';

                // ── Sync XP pill in mobile navbar ─────────────────────────
                const srcLabel = document.getElementById('xp-label');
                const srcBar   = document.getElementById('xp-progress-bar');
                const mNavLabel = document.getElementById('m-nav-xp-label');
                const mNavFill  = document.getElementById('m-nav-xp-fill');
                if (srcLabel && mNavLabel) mNavLabel.textContent = srcLabel.textContent;
                if (srcBar   && mNavFill)  mNavFill.style.width = srcBar.style.width || '0%';
            }

            // Run on load
            document.addEventListener('DOMContentLoaded', function() {
                const xpBar = document.getElementById('xp-progress-bar');
                if (xpBar) {
                    new MutationObserver(syncMobileNavbarUI).observe(xpBar, { attributes: true, attributeFilter: ['style'] });
                }
            });

            // Run after main hydration (delay to let hydrateHeroUI complete)
            setTimeout(syncMobileNavbarUI, 500);
            // Also re-run when auth resolves
            window.addEventListener('auth_resolved', () => setTimeout(syncMobileNavbarUI, 300));
        })();
        </script>

        <style>
        /* ── Mobile: Background ────────────────────────────────
           Remove explicit hardcoded background to let main Tailwind flow work properly. */
        @media (max-width: 1023px) {
            /* Shrink top padding of main on mobile — hero handled by m-navbar */
            main {
                padding-top: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            /* Hero section: no extra padding on mobile */
            #hero {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
            }
            /* Hide desktop hero grid background pattern on mobile */
            #hero > div:first-child {
                display: none !important;
            }
        }
        </style>
        </section>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- SOCIAL PROOF STRIP                                             -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- ELEGANT SOCIAL PROOF STRIP                                     -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="w-full px-4 sm:px-6 lg:px-8 py-8 -mt-2 relative z-10 border-b border-slate-200/50 dark:border-slate-800/50">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-center gap-6 md:gap-0 md:divide-x md:divide-slate-200 dark:md:divide-slate-800">
                <div class="px-8 text-center md:flex-1">
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight font-mono"><span id="sim-counter">1.087</span>+</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest font-semibold mt-1">Simulasi Selesai</p>
                </div>
                <div class="px-8 text-center md:flex-1">
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight font-mono">Rp 3,2 Jt+</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest font-semibold mt-1">Rata-rata Potensi Profit/Bln</p>
                </div>
                <div class="px-8 text-center md:flex-1">
                    <p class="text-2xl sm:text-3xl font-black text-slate-800 dark:text-slate-100 tracking-tight font-mono">2 - 3x</p>
                    <p class="text-[10px] sm:text-xs text-slate-500 uppercase tracking-widest font-semibold mt-1">Peningkatan Margin Pemula</p>
                </div>
            </div>
        </div>
        <script>
        (function() {
            // Animated counter: increment from 1087 toward 1243 randomly every 3-8 seconds
            let count = 1087;
            const target = 1243;
            function tick() {
                if (count < target) {
                    const inc = Math.floor(Math.random() * 3) + 1;
                    count = Math.min(count + inc, target);
                    const el = document.getElementById('sim-counter');
                    if (el) el.textContent = count.toLocaleString('id-ID');
                }
                const delay = Math.floor(Math.random() * 5000) + 3000;
                setTimeout(tick, delay);
            }
            setTimeout(tick, 2500);
        })();
        </script>



        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- Desktop Secondary Navigation (Below Hero)               -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <div class="flex justify-start sm:justify-center w-full relative z-20 mt-4 sm:-mt-6 mb-12 px-2 sm:px-6 lg:px-8">
            <nav id="desktop-nav-tabs" class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200 dark:border-slate-800/80 rounded-2xl px-1 sm:px-2 py-1.5 sm:py-2 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] flex items-center justify-between w-full sm:w-auto transition-all mx-auto md:mx-0">
                <a href="#" onclick="switchDesktopNavTab('feature', event)" class="desktop-nav-link active px-2 sm:px-5 py-1.5 sm:py-2.5 rounded-xl text-[10px] sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-all flex items-center justify-center gap-1.5 sm:gap-2 group cursor-pointer flex-1 sm:flex-none block bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 shadow-sm whitespace-nowrap">
                    <i class="fas fa-star text-emerald-500 transition-colors"></i> Feature
                </a>
                <div class="w-px h-4 sm:h-5 bg-slate-200 dark:bg-slate-700/50 mx-1"></div>
                <a href="#" onclick="switchDesktopNavTab('product', event)" class="desktop-nav-link px-2 sm:px-5 py-1.5 sm:py-2.5 rounded-xl text-[10px] sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all flex items-center justify-center gap-1.5 sm:gap-2 group cursor-pointer flex-1 sm:flex-none whitespace-nowrap">
                    <i class="fas fa-cube text-slate-400 group-hover:text-blue-500 transition-colors"></i> Product
                </a>
                <div class="w-px h-4 sm:h-5 bg-slate-200 dark:bg-slate-700/50 mx-1"></div>
                <a href="#" onclick="switchDesktopNavTab('tools', event)" class="desktop-nav-link px-2 sm:px-5 py-1.5 sm:py-2.5 rounded-xl text-[10px] sm:text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all flex items-center justify-center gap-1.5 sm:gap-2 group cursor-pointer flex-1 sm:flex-none relative whitespace-nowrap">
                    <i class="fas fa-crown text-slate-400 group-hover:text-amber-500 transition-colors"></i> Tools Premium
                    <span class="absolute top-1 sm:top-2.5 right-1 sm:right-2 w-1 sm:w-1.5 h-1 sm:h-1.5 bg-rose-500 rounded-full animate-pulse shadow-[0_0_5px_rgba(244,63,94,0.6)]"></span>
                </a>
            </nav>
        </div>
        
        <script>
            function switchDesktopNavTab(tabName, event) {
                if(event) event.preventDefault();
                
                // Content sections
                const featureSection = document.getElementById('features-bento-grid');
                const productSection = document.getElementById('other-products');
                const toolsSection = document.getElementById('mini-course-teaser');
                const workspaceContainer = document.getElementById('bento-workspace-container');
                const roadmapContainer = document.getElementById('roadmap-container');
                
                // Hide all sections natively on all sizes
                if (featureSection) { 
                    featureSection.classList.add('hidden'); 
                    featureSection.classList.remove('block', 'animate-fade-in-up');
                }
                if (productSection) { 
                    productSection.classList.add('hidden'); 
                    productSection.classList.remove('block', 'animate-fade-in-up');
                }
                if (toolsSection) { 
                    toolsSection.classList.add('hidden'); 
                    toolsSection.classList.remove('block', 'animate-fade-in-up');
                }
                if (workspaceContainer) { 
                    workspaceContainer.classList.add('hidden'); 
                    workspaceContainer.classList.remove('block', 'animate-fade-in-up');
                }
                if (roadmapContainer) { 
                    roadmapContainer.classList.add('hidden'); 
                    roadmapContainer.classList.remove('block', 'animate-fade-in-up');
                }
                
                // Show selected section natively on all sizes
                if (tabName === 'feature') {
                    if (featureSection) { 
                        featureSection.classList.remove('hidden'); 
                        featureSection.classList.add('block', 'animate-fade-in-up'); 
                    }
                    if (workspaceContainer) { 
                        workspaceContainer.classList.remove('hidden'); 
                        workspaceContainer.classList.add('block'); 
                    }
                    if (roadmapContainer) {
                        const roadmapTabBtn = document.getElementById('tab-roadmap-container');
                        if (roadmapTabBtn && roadmapTabBtn.classList.contains('scale-105')) {
                            roadmapContainer.classList.remove('hidden');
                            roadmapContainer.classList.add('block', 'animate-fade-in-up');
                        }
                    }
                } else if (tabName === 'product') {
                    if (productSection) { 
                        productSection.classList.remove('hidden'); 
                        productSection.classList.add('block', 'animate-fade-in-up'); 
                    }
                } else if (tabName === 'tools') {
                    if (toolsSection) { 
                        toolsSection.classList.remove('hidden'); 
                        toolsSection.classList.add('block', 'animate-fade-in-up'); 
                    }
                }
                
                // Update Nav Links Styles
                const navLinks = document.querySelectorAll('.desktop-nav-link');
                navLinks.forEach(link => {
                    // Reset to unselected state
                    link.classList.remove('bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400', 'shadow-sm', 'bg-blue-50', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-400', 'bg-amber-50', 'dark:bg-amber-900/30', 'text-amber-600', 'dark:text-amber-400');
                    link.classList.add('text-slate-600', 'dark:text-slate-300');
                    
                    const icon = link.querySelector('i:first-child');
                    if(icon) {
                        icon.classList.remove('text-emerald-500', 'text-blue-500', 'text-amber-500');
                        icon.classList.add('text-slate-400', 'group-hover:text-emerald-500', 'group-hover:text-blue-500', 'group-hover:text-amber-500'); 
                    }
                });
                
                // Set active state
                if(event && event.currentTarget) {
                    const currentLink = event.currentTarget;
                    currentLink.classList.remove('text-slate-600', 'dark:text-slate-300');
                    const icon = currentLink.querySelector('i:first-child');
                    if(icon) {
                        icon.classList.remove('text-slate-400', 'group-hover:text-emerald-500', 'group-hover:text-blue-500', 'group-hover:text-amber-500');
                    }
                    
                    if (tabName === 'feature') {
                        currentLink.classList.add('bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-600', 'dark:text-emerald-400', 'shadow-sm');
                        if(icon) icon.classList.add('text-emerald-500');
                    } else if (tabName === 'product') {
                        currentLink.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'text-blue-600', 'dark:text-blue-400', 'shadow-sm');
                        if(icon) icon.classList.add('text-blue-500');
                    } else if (tabName === 'tools') {
                        currentLink.classList.add('bg-amber-50', 'dark:bg-amber-900/30', 'text-amber-600', 'dark:text-amber-400', 'shadow-sm');
                        if(icon) icon.classList.add('text-amber-500');
                    }
                }
            }
        </script>

        <div id="desktop-tab-content">
        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- BENTO GRID HUB (Interactive Tab System)                 -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="features-bento-grid" class="block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16 space-y-10">
            
            <!-- Category 1: New Users (Belum Punya Bisnis) -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-8 w-1.5 bg-emerald-500 rounded-full"></div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight">Mulai dari Nol</h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Belum punya bisnis atau bingung mulai darimana? Ikuti alur ini.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 sm:gap-6">
                    <!-- Tab 1: Reverse Goal Planner -->
                    <div onclick="switchBentoTab('goal-planner')" id="tab-goal-planner" class="bento-tab cursor-pointer text-left relative overflow-hidden rounded-[1.25rem] sm:rounded-3xl p-4 sm:p-6 border-2 transition-all duration-300 transform border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 shadow-[0_8px_30px_rgba(16,185,129,0.2)] dark:shadow-[0_0_25px_rgba(16,185,129,0.15)] ring-2 ring-emerald-500/50 hover:-translate-y-1 sm:hover:scale-[1.02] z-10 flex flex-col justify-center min-h-[140px] sm:min-h-[160px]">
                        <div class="absolute top-2 right-2 sm:top-3 sm:right-3 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider backdrop-blur-md border z-20 bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 shadow-sm">Phase 1</div>
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-emerald-500/20 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none transition-all duration-500 bento-glow"></div>
                        <div class="absolute border-t border-l border-white/40 dark:border-white/10 inset-0 rounded-[1.25rem] sm:rounded-3xl pointer-events-none opacity-50"></div>
                        <div class="relative z-10 flex flex-col items-start">
                            <div class="shrink-0 w-8 h-8 sm:w-14 sm:h-14 rounded-lg sm:rounded-xl shadow-inner bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center transition-colors mb-2 sm:mb-4">
                                <i class="fas fa-bullseye text-sm sm:text-2xl text-emerald-600 dark:text-emerald-400 drop-shadow-md"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-xl font-black text-slate-900 dark:text-white mb-1 drop-shadow-sm leading-tight">Reverse Planner</h3>
                                <p class="text-[9px] sm:text-sm text-slate-600 dark:text-slate-400 leading-snug sm:leading-relaxed font-medium">Urai target finansial besar jadi target harian logis & masuk akal.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Profit Simulator -->
                    <div onclick="switchBentoTab('profit-simulator-section')" id="tab-profit-simulator-section" class="bento-tab cursor-pointer text-left relative overflow-hidden rounded-[1.25rem] sm:rounded-3xl p-4 sm:p-6 border-2 transition-all duration-300 transform border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 hover:border-violet-400 dark:hover:border-violet-500/50 hover:bg-violet-50 dark:hover:bg-violet-900/10 hover:-translate-y-1 hover:shadow-xl flex flex-col justify-center min-h-[140px] sm:min-h-[160px] group">
                        <div class="absolute top-2 right-2 sm:top-3 sm:right-3 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider backdrop-blur-md border z-20 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 group-hover:bg-violet-500/10 group-hover:border-violet-500/30 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">Phase 2</div>
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-violet-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500 bento-glow"></div>
                        <div class="absolute border-t border-l border-white/40 dark:border-white/10 inset-0 rounded-[1.25rem] sm:rounded-3xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex flex-col items-start">
                            <div class="shrink-0 w-8 h-8 sm:w-14 sm:h-14 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition-colors group-hover:bg-violet-100 dark:group-hover:bg-violet-500/20 group-hover:border-violet-200 dark:group-hover:border-violet-500/30 mb-2 sm:mb-4">
                                <i class="fas fa-chart-line text-sm sm:text-2xl text-slate-400 dark:text-slate-500 transition-colors group-hover:text-violet-500 dark:group-hover:text-violet-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-xl font-black text-slate-900 dark:text-white mb-1 leading-tight">Profit Simulator</h3>
                                <p class="text-[9px] sm:text-sm text-slate-600 dark:text-slate-400 leading-snug sm:leading-relaxed font-medium">Cari celah cuan dan uji berbagai skenario (*traffic* & konversi).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category 2: Experienced Users (Sudah Punya Bisnis) -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-8 w-1.5 bg-blue-500 rounded-full"></div>
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight">Scale-Up Bisnis</h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Sudah punya bisnis dan ingin omzet meroket? Mulai dari sini.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 sm:gap-6">
                    <!-- Tab 3: Mentor Lab -->
                    <div data-feat="mentor_lab" onclick="switchBentoTab('business-simulation-lab')" id="tab-business-simulation-lab" class="bento-tab cursor-pointer text-left relative overflow-hidden rounded-[1.25rem] sm:rounded-3xl p-4 sm:p-6 border-2 transition-all duration-300 transform border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 hover:border-blue-400 dark:hover:border-blue-500/50 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:-translate-y-1 hover:shadow-xl flex flex-col justify-center min-h-[140px] sm:min-h-[160px] group">
                        <div class="absolute top-2 right-2 sm:top-3 sm:right-3 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider backdrop-blur-md border z-20 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 group-hover:bg-blue-500/10 group-hover:border-blue-500/30 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Phase 3</div>
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500 bento-glow"></div>
                        <div class="absolute border-t border-l border-white/40 dark:border-white/10 inset-0 rounded-[1.25rem] sm:rounded-3xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex flex-col items-start">
                            <div class="shrink-0 w-8 h-8 sm:w-14 sm:h-14 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition-colors group-hover:bg-blue-100 dark:group-hover:bg-blue-500/20 group-hover:border-blue-200 dark:group-hover:border-blue-500/30 mb-2 sm:mb-4">
                                <i class="fas fa-brain text-sm sm:text-2xl text-slate-400 dark:text-slate-500 transition-colors group-hover:text-blue-500 dark:group-hover:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-xl font-black text-slate-900 dark:text-white mb-1 leading-tight">Virtual Mentor</h3>
                                <p class="text-[9px] sm:text-sm text-slate-600 dark:text-slate-400 leading-snug sm:leading-relaxed font-medium">Diagnosis *bottleneck* bisnismu & dapatkan solusi taktis langsung.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Roadmap -->
                    <div onclick="switchBentoTab('roadmap-container')" id="tab-roadmap-container" class="bento-tab cursor-pointer text-left relative overflow-hidden rounded-[1.25rem] sm:rounded-3xl p-4 sm:p-6 border-2 transition-all duration-300 transform border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 hover:border-amber-400 dark:hover:border-amber-500/50 hover:bg-amber-50 dark:hover:bg-amber-900/10 hover:-translate-y-1 hover:shadow-xl flex flex-col justify-center min-h-[140px] sm:min-h-[160px] group">
                        <div class="absolute top-2 right-2 sm:top-3 sm:right-3 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[8px] sm:text-[10px] font-black uppercase tracking-wider backdrop-blur-md border z-20 bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 group-hover:bg-amber-500/10 group-hover:border-amber-500/30 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Phase 4</div>
                        <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-amber-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-500 bento-glow"></div>
                        <div class="absolute border-t border-l border-white/40 dark:border-white/10 inset-0 rounded-[1.25rem] sm:rounded-3xl pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex flex-col items-start">
                            <div class="shrink-0 w-8 h-8 sm:w-14 sm:h-14 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition-colors group-hover:bg-amber-100 dark:group-hover:bg-amber-500/20 group-hover:border-amber-200 dark:group-hover:border-amber-500/30 mb-2 sm:mb-4">
                                <i class="fas fa-map-marked-alt text-sm sm:text-2xl text-slate-400 dark:text-slate-500 transition-colors group-hover:text-amber-500 dark:group-hover:text-amber-400"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-xl font-black text-slate-900 dark:text-white mb-1 leading-tight">Strategy Roadmap</h3>
                                <p class="text-[9px] sm:text-sm text-slate-600 dark:text-slate-400 leading-snug sm:leading-relaxed font-medium">Jalur eksekusi harian rahasia dari mentor menuju *goals* akhir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- AD ARSENAL (The Premium Vault)                          -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="other-products" class="hidden relative overflow-hidden w-full max-w-7xl mx-auto mb-16 rounded-[2.5rem] bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl dark:shadow-2xl">
            
            <!-- Premium Background Grids & Orbs -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAzNHYtbDItMi0ydi00aC00di0yaC0ydi0yaC0ydi0yaS0yai0ybC0yLTJoLTR2MmgtMnYyaC0ydjJoLTh2NGg0djRoOHYzNGg0di00aDR2LTRoNHYtNGg0di00aDR2LTRoNCIgc3Ryb2tlPSIjZmZmZmZmIiBzdHJva2Utb3BhY2l0eT0iMC4wMyIvPjwvZz48L3N2Zz4=')] opacity-50 pointer-events-none"></div>
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>

            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-emerald-500/50 to-transparent"></div>



            <!-- Header Section: Hooking the User -->
            <div class="relative z-10 pt-16 pb-8 px-4 sm:px-8 text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/80 dark:bg-slate-800/80 border border-emerald-200 dark:border-emerald-700/50 backdrop-blur-sm rounded-full mb-6 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    <span class="text-[10px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-widest">Digital Product Arsenal</span>
                </div>
                
                <div class="mb-10 space-y-3">
                    <h3 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        Sudah Tau Cara Untung Tapi
                        <span class="relative inline-block mt-1 sm:mt-2">
                            <span class="absolute -inset-1 sm:-inset-2 bg-rose-500/20 dark:bg-rose-500/30 blur-lg rounded-full"></span>
                            <span class="relative text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-orange-500 dark:from-rose-400 dark:to-orange-400 border-b-4 sm:border-b-8 border-rose-500/40 pb-1">Belum Punya Produk?</span>
                        </span>
                    </h3>
                    
                    <div class="flex items-center justify-center gap-4 py-4">
                        <div class="h-px bg-gradient-to-r from-transparent to-slate-200 dark:to-slate-700 w-16 sm:w-24"></div>
                        <span class="text-slate-400 dark:text-slate-500 font-black tracking-widest uppercase text-xs sm:text-sm">ATAU</span>
                        <div class="h-px bg-gradient-to-l from-transparent to-slate-200 dark:to-slate-700 w-16 sm:w-24"></div>
                    </div>
                    
                    <h3 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-800 dark:text-slate-200 tracking-tight leading-tight">
                        Pembeli Sepi Karena
                        <span class="relative inline-block mt-1 sm:mt-2">
                            <span class="absolute -inset-1 sm:-inset-2 bg-amber-500/20 dark:bg-amber-500/30 blur-lg rounded-full"></span>
                            <span class="relative text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-yellow-500 dark:from-amber-400 dark:to-yellow-400 border-b-4 sm:border-b-8 border-amber-500/40 pb-1">Kurang Variasi?</span>
                        </span>
                    </h3>
                </div>
                
                <!-- High-Impact Offer Box (Replacing long text for better UX) -->
                <div class="max-w-2xl mx-auto mb-10 transform hover:scale-[1.02] transition-transform duration-300">
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/80 rounded-3xl border border-emerald-100 dark:border-emerald-800/40 shadow-xl shadow-emerald-500/10 relative overflow-hidden group">
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-400 to-cyan-500 opacity-80 group-hover:opacity-100 transition-opacity"></div>
                        <!-- Background Glow -->
                        <div class="absolute -inset-20 bg-emerald-400/5 dark:bg-emerald-400/5 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                        <div class="relative z-10 flex flex-col items-center text-center">
                            <!-- Badge -->
                            <div class="flex items-center justify-center mb-4">
                                <span class="px-4 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-[11px] sm:text-xs font-black uppercase tracking-widest border border-emerald-200 dark:border-emerald-700/50">
                                    Lisensi Bebas Jual
                                </span>
                            </div>
                            
                            <!-- Massive Hook -->
                            <div class="mb-4 w-full">
                                <span class="block text-slate-800 dark:text-slate-200 font-black text-xl sm:text-2xl mb-1 leading-tight tracking-tight">Beli 1x. Jual Berkali-kali.</span>
                                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-cyan-600 dark:from-emerald-400 dark:to-cyan-300 font-black text-4xl sm:text-5xl leading-tight tracking-tight drop-shadow-sm pb-1">
                                    100% Profit Instan.
                                </span>
                            </div>

                            <!-- Concise Explanation & Dynamic Upsell Hook -->
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                                <p id="dynamic-upsell-text" class="text-slate-600 dark:text-slate-400 font-medium text-sm sm:text-base max-w-lg mx-auto leading-relaxed hidden">
                                    Untuk mencapai <span id="rp-upsell-target" class="font-black text-emerald-600 dark:text-emerald-400">Rp0</span>, kamu hanya perlu menjual <span id="rp-upsell-unit" class="font-black text-rose-500">0</span> lisensi produk ini. Tanpa keluar biaya produksi, murni 100% profit untukmu.
                                </p>
                                <p id="static-upsell-text" class="text-slate-600 dark:text-slate-400 font-medium text-sm sm:text-base max-w-md mx-auto leading-relaxed block">
                                    Produk digital siap branding. Langsung pasarkan dan amankan profit Anda hari ini juga.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Value Proposition Badges -->
                <div class="flex flex-row justify-center items-stretch gap-1.5 sm:gap-4 mb-10 max-w-3xl mx-auto w-full px-1 sm:px-0">
                    <!-- Point 1 -->
                    <div class="flex-1 flex flex-col justify-start items-center gap-1 sm:gap-2 bg-gradient-to-b from-white to-slate-50 dark:from-slate-800/80 dark:to-slate-900/80 p-2 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/80 hover:-translate-y-1 transition-transform group">
                        <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-500 dark:text-rose-400 mb-0.5 sm:mb-1 group-hover:scale-110 transition-transform shrink-0">
                            <i class="fas fa-ban text-[10px] sm:text-lg"></i>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300 text-[9px] sm:text-sm font-bold text-center leading-tight">Tidak Perlu<br class="block sm:hidden"> Produksi</span>
                    </div>
                    
                    <!-- Point 2 -->
                    <div class="flex-1 flex flex-col justify-start items-center gap-1 sm:gap-2 bg-gradient-to-b from-white to-slate-50 dark:from-slate-800/80 dark:to-slate-900/80 p-2 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/80 hover:-translate-y-1 transition-transform group">
                        <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-500 dark:text-amber-400 mb-0.5 sm:mb-1 group-hover:scale-110 transition-transform shrink-0">
                            <i class="fas fa-search-minus text-[10px] sm:text-lg"></i>
                        </div>
                        <span class="text-slate-700 dark:text-slate-300 text-[9px] sm:text-sm font-bold text-center leading-tight">Tanpa Riset<br class="block sm:hidden"> Panjang</span>
                    </div>

                    <!-- Point 3 (Highlighted) -->
                    <div class="flex-1 flex flex-col justify-start items-center gap-1 sm:gap-2 bg-gradient-to-b from-emerald-50 to-teal-50 dark:from-emerald-900/40 dark:to-slate-900/80 p-2 sm:p-4 rounded-xl sm:rounded-2xl shadow-md border border-emerald-300 dark:border-emerald-600/50 transform scale-100 sm:scale-105 hover:-translate-y-1 transition-transform relative overflow-hidden group">
                        <!-- Subtle Background Glow -->
                        <div class="absolute inset-0 bg-emerald-400/10 dark:bg-emerald-400/5 opacity-50 pointer-events-none"></div>
                        <div class="relative w-7 h-7 sm:w-10 sm:h-10 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white mb-0.5 sm:mb-1 group-hover:scale-110 transition-transform shrink-0">
                            <i class="fas fa-bolt text-[10px] sm:text-lg"></i>
                        </div>
                        <span class="relative text-emerald-900 dark:text-emerald-300 text-[10px] sm:text-sm font-black text-center leading-tight">Tinggal<br class="block sm:hidden"> Eksekusi!</span>
                    </div>
                </div>

                <!-- Urgency Block -->
                <div class="inline-block relative group mb-8">
                    <div class="absolute inset-x-4 -inset-y-2 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 dark:from-emerald-500/20 dark:to-cyan-500/20 blur-xl group-hover:opacity-100 transition duration-500 rounded-full"></div>
                    <div class="relative py-2 flex flex-col items-center justify-center">
                        <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm font-medium uppercase tracking-widest mb-3">Karena waktu terbaik untuk mulai...</p>
                        <p class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-none uppercase flex flex-wrap justify-center items-center gap-3">
                            Adalah 
                            <span class="px-4 py-1.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl shadow-xl inline-block transform -rotate-2 hover:rotate-0 transition-transform">Sekarang</span>
                        </p>
                    </div>
                </div>
                
                <!-- Micro-commitment / Scroll Hint -->
                <div class="flex items-center justify-center gap-3 text-xs text-emerald-600/70 dark:text-emerald-400/70 font-bold uppercase tracking-widest">
                    <div class="h-px w-8 bg-emerald-500/30"></div>
                    <span>Eksplorasi Produk</span>
                    <div class="h-px w-8 bg-emerald-500/30"></div>
                </div>
            </div>

            <!-- The Horizontal Scroll Container -->
            <div class="relative z-10 pb-16">
                <!-- Fade Gradients on edges to make scroll feel endless & elegant -->
                <div class="absolute left-0 top-0 bottom-0 w-8 md:w-16 bg-gradient-to-r from-slate-50 dark:from-slate-900 to-transparent z-20 pointer-events-none"></div>
                
                <div id="ad-arsenal-container" class="flex md:flex-wrap justify-center md:justify-center gap-6 overflow-x-auto px-8 md:px-0 py-4 snap-x snap-mandatory no-scrollbar hide-scroll relative z-10">
                    <!-- Data loaded via ad-arsenal-frontend.js -->
                </div>

                <div class="absolute right-0 top-0 bottom-0 w-8 md:w-16 bg-gradient-to-l from-slate-50 dark:from-slate-900 to-transparent z-20 pointer-events-none"></div>
            </div>
            
            <!-- Hide scrollbar styling for this section specifically -->
            <style>
                .hide-scroll::-webkit-scrollbar { display: none; }
                .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
            </style>
        </section>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- Mini Course Teaser Section (Phase 6) — Redesigned      -->
        <!-- Moved to Tools Premium Tab                             -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <section id="mini-course-teaser" class="hidden relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                <button onclick="switchBentoTab('goal-planner')" id="tab-goal-planner-mc" class="bento-tab cursor-pointer text-left relative overflow-hidden rounded-3xl p-6 border-2 transition-all duration-300 transform border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 shadow-[0_8px_30px_rgba(16,185,129,0.2)] dark:shadow-[0_0_25px_rgba(16,185,129,0.15)] ring-2 ring-emerald-500/50 -translate-y-2 scale-105 z-10 hidden">
                </button>

                {{-- Ambient background orbs for the section --}}
                <div class="pointer-events-none absolute -top-32 -left-32 w-80 h-80 rounded-full bg-violet-600/15 blur-[100px]"></div>
                <div class="pointer-events-none absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-fuchsia-500/10 blur-[120px]"></div>
                <div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] rounded-full bg-indigo-500/5 blur-[80px]"></div>

                <div class="flex flex-col gap-10">
                    <!-- Mini Course Card -->
                    <div class="relative rounded-3xl overflow-hidden w-full mx-auto group/card transition-all duration-500
                        bg-white dark:bg-slate-900/[0.03]
                        backdrop-blur-2xl
                        border border-slate-200 dark:border-white/5
                        shadow-xl dark:shadow-[0_8px_40px_rgba(139,92,246,0.18),inset_0_1px_0_rgba(255,255,255,0.08)]
                        hover:shadow-2xl dark:hover:shadow-[0_12px_60px_rgba(139,92,246,0.3),inset_0_1px_0_rgba(255,255,255,0.12)]
                        hover:border-fuchsia-400/50 dark:hover:border-fuchsia-500/25
                        hover:-translate-y-1">
                        {{-- Glass decorative elements --}}
                        <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.04]" style="background-image: linear-gradient(to right, #8b5cf6 1px, transparent 1px), linear-gradient(to bottom, #8b5cf6 1px, transparent 1px); background-size: 40px 40px;"></div>
                        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-violet-400/20 dark:bg-violet-500/10 blur-3xl pointer-events-none group-hover/card:bg-violet-400/30 dark:group-hover/card:bg-violet-500/20 transition-colors duration-700"></div>
                        <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-fuchsia-400/20 dark:bg-fuchsia-500/10 blur-3xl pointer-events-none group-hover/card:bg-fuchsia-400/30 dark:group-hover/card:bg-fuchsia-500/20 transition-colors duration-700"></div>
                        {{-- Top highlight line --}}
                        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-violet-500/30 dark:via-violet-400/40 to-transparent"></div>
                        

                        
                        <div class="relative p-6 sm:p-8 md:p-12 flex flex-col md:flex-row items-center gap-8 md:gap-10">
                            <!-- Left: Graphic Elements -->
                            <div class="md:w-1/2 relative order-2 md:order-1">
                                <!-- Floating Elements -->
                                <div class="absolute -top-6 -right-6 w-24 h-24 bg-fuchsia-500/20 rounded-full blur-2xl animate-pulse"></div>
                                <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-violet-500/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                                
                                <!-- Video Thumbnail / Graphic -->
                                <div class="relative rounded-2xl overflow-hidden border border-slate-700/50 shadow-2xl group cursor-pointer aspect-video bg-slate-100 dark:bg-slate-800/80 transform group-hover/card:scale-[1.02] transition-transform duration-500">
                                    <!-- Mascot Image -->
                                    <img src="{{ asset('assets/icon/aksa_mengajar.png') }}" alt="Mascot Mengajar" class="w-full h-full object-contain opacity-90 group-hover/card:opacity-100 group-hover/card:scale-105 transition-all duration-700 p-2 sm:p-4">
                                    
                                    <div class="absolute inset-0 bg-slate-900/30 group-hover/card:bg-slate-900/10 transition-colors flex items-center justify-center opacity-0 group-hover/card:opacity-100 backdrop-blur-[2px] transition-all duration-300">
                                        <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-md border border-white/50 flex items-center justify-center group-hover/card:scale-110 shadow-2xl transition-transform duration-500">
                                            <i class="fas fa-play text-2xl text-white ml-1 shadow-sm drop-shadow-md"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Floating Badge -->
                                    <div class="absolute top-4 left-4 px-3 py-1 bg-violet-500/20 backdrop-blur-sm border border-violet-500/50 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-star text-amber-400 text-xs shadow-sm"></i>
                                        <span class="text-[10px] font-black text-white uppercase tracking-widest">Premium Rated</span>
                                    </div>
                                </div>
                            </div>

                             <!-- Right: Copy and CTA -->
                             <div class="w-1/2 flex-shrink-0 space-y-5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-500/15 border border-orange-500/30 text-orange-300 text-xs font-bold tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                    <span class="uppercase">Harsh Truth</span>
                                </div>
                                
                                <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight">
                                    Bakar Uang Buat Ads,<br>Tapi <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-fuchsia-600 dark:from-violet-400 dark:to-fuchsia-400">Sales Jalan di Tempat?</span>
                                </h2>
                                
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm md:text-base font-medium">
                                    Fakta brutal: Strategi ads lama sudah mati. Pemula fokus cari *traffic* murah, sementara Top 1% fokus membangun <span class="font-bold text-slate-900 dark:text-white">ekosistem konversi</span>. Ini adalah jalan pintasmu keluar dari siklus boncos harian.
                                </p>
                                
                                <ul class="space-y-3 pt-2 text-sm text-slate-700 dark:text-slate-300 font-medium">
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-violet-600 dark:text-violet-400 mt-0.5 shadow-sm shadow-violet-500/20"></i> <span><strong class="text-slate-900 dark:text-white">Unlock Blueprint:</strong> Rahasia struktur penawaran yang mustahil ditolak.</span></li>
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-violet-600 dark:text-violet-400 mt-0.5 shadow-sm shadow-violet-500/20"></i> <span><strong class="text-slate-900 dark:text-white">Zero to Scale:</strong> Formula pasti merubah *traffic* nyasar jadi pembeli loyal.</span></li>
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-violet-600 dark:text-violet-400 mt-0.5 shadow-sm shadow-violet-500/20"></i> <span><strong class="text-slate-900 dark:text-white">VIP Vault:</strong> Tiru habis-habisan strategi dan *template* daging para mastah.</span></li>
                                </ul>
                                
                                <div class="pt-2 flex flex-col sm:flex-row gap-4">
                                    <!-- Akses Mini Course button (Directs to /course-fba) -->
                                    <a href="/courses" class="px-6 py-4 bg-gradient-to-r from-slate-900 to-slate-800 dark:from-violet-600 dark:to-fuchsia-600 hover:from-slate-800 hover:to-slate-700 dark:hover:from-violet-500 dark:hover:to-fuchsia-500 text-white font-bold rounded-xl transition-all shadow-xl shadow-slate-900/20 dark:shadow-violet-500/20 hover:shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-2 group border border-slate-700 dark:border-violet-400/30 w-full sm:w-auto">
                                        <i class="fas fa-fire text-orange-400 group-hover:scale-110 transition-transform"></i> Akses Blueprint Sekarang
                                    </a>
                                </div>
                            </div>

                            </div>
                        </div>
                    </div>

                    {{-- ── Cashbook V2 Card ──────────────────────────────────────────── --}}
                    <div class="relative rounded-3xl overflow-hidden w-full mx-auto group/cashbook transition-all duration-500
                        bg-white dark:bg-slate-900/[0.03]
                        backdrop-blur-2xl
                        border border-slate-200 dark:border-white/5
                        shadow-xl dark:shadow-[0_8px_40px_rgba(16,185,129,0.12),inset_0_1px_0_rgba(255,255,255,0.08)]
                        hover:shadow-2xl dark:hover:shadow-[0_12px_60px_rgba(16,185,129,0.25),inset_0_1px_0_rgba(255,255,255,0.12)]
                        hover:border-emerald-400/50 dark:hover:border-emerald-500/25
                        hover:-translate-y-1">

                        {{-- Grid texture --}}
                        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.04]" style="background-image: linear-gradient(to right, #10b981 1px, transparent 1px), linear-gradient(to bottom, #10b981 1px, transparent 1px); background-size: 40px 40px;"></div>
                        {{-- Ambient orbs --}}
                        <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-emerald-400/20 dark:bg-emerald-500/10 blur-3xl pointer-events-none group-hover/cashbook:bg-emerald-400/30 dark:group-hover/cashbook:bg-emerald-500/20 transition-colors duration-700"></div>
                        <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full bg-teal-400/15 dark:bg-teal-500/10 blur-3xl pointer-events-none group-hover/cashbook:bg-teal-400/25 dark:group-hover/cashbook:bg-teal-500/20 transition-colors duration-700"></div>
                        {{-- Top highlight --}}
                        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-emerald-500/40 dark:via-emerald-400/50 to-transparent"></div>

                        <div class="relative p-6 sm:p-8 md:p-12 flex flex-col md:flex-row items-center gap-8 md:gap-10">

                            {{-- Left: Graphic panel --}}
                            <div class="md:w-1/2 relative order-2 md:order-1 w-full">
                                {{-- Floating glow blobs --}}
                                <div class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-500/20 rounded-full blur-2xl animate-pulse"></div>
                                <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-teal-500/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 1.2s;"></div>

                                <div class="relative rounded-2xl overflow-hidden border border-emerald-700/30 dark:border-emerald-500/20 shadow-2xl aspect-video bg-slate-900 transform group-hover/cashbook:scale-[1.02] transition-transform duration-500">
                                    {{-- Fintech Dashboard Graphic --}}
                                    <div class="absolute inset-0 p-5 flex flex-col gap-3">
                                        {{-- Top bar mock --}}
                                        <div class="flex items-center justify-between">
                                            <div class="flex gap-2 items-center">
                                                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                                                <div class="h-2 w-20 bg-slate-700 rounded-full"></div>
                                            </div>
                                            <div class="h-2 w-12 bg-emerald-900/70 rounded-full"></div>
                                        </div>
                                        {{-- Metric rows --}}
                                        <div class="grid grid-cols-3 gap-2 mt-1">
                                            <div class="rounded-lg bg-emerald-900/40 border border-emerald-700/30 p-2">
                                                <div class="h-1.5 w-10 bg-slate-600 rounded mb-1.5"></div>
                                                <div class="h-3 w-14 bg-emerald-400/70 rounded font-mono"></div>
                                            </div>
                                            <div class="rounded-lg bg-slate-800/60 border border-slate-700/30 p-2">
                                                <div class="h-1.5 w-8 bg-slate-600 rounded mb-1.5"></div>
                                                <div class="h-3 w-10 bg-slate-400/50 rounded"></div>
                                            </div>
                                            <div class="rounded-lg bg-slate-800/60 border border-slate-700/30 p-2">
                                                <div class="h-1.5 w-8 bg-slate-600 rounded mb-1.5"></div>
                                                <div class="h-3 w-10 bg-teal-400/50 rounded"></div>
                                            </div>
                                        </div>
                                        {{-- Fake chart bars --}}
                                        <div class="flex-1 rounded-lg bg-slate-800/40 border border-slate-700/20 p-3 flex items-end gap-1.5">
                                            @foreach([35,60,45,75,50,80,65,90,55,70] as $h)
                                            <div class="flex-1 rounded-sm bg-emerald-500/{{ $h > 60 ? '70' : '30' }} hover:bg-emerald-400/80 transition-colors" style="height: {{ $h }}%"></div>
                                            @endforeach
                                        </div>
                                        {{-- Budget progress bar --}}
                                        <div class="rounded-lg bg-slate-800/40 border border-slate-700/20 p-2.5">
                                            <div class="flex justify-between text-[9px] text-slate-500 mb-1.5">
                                                <span>Budget</span><span class="text-emerald-400">67%</span>
                                            </div>
                                            <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full w-2/3 bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Overlay badge --}}
                                    <div class="absolute top-4 left-4 px-3 py-1 bg-emerald-500/20 backdrop-blur-sm border border-emerald-500/50 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-shield-halved text-emerald-400 text-xs"></i>
                                        <span class="text-[10px] font-black text-white uppercase tracking-widest">Fintech Grade</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Copy and CTA --}}
                            <div class="md:w-1/2 flex-shrink-0 space-y-5 order-1 md:order-2 w-full">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-bold tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="uppercase">Discipline OS</span>
                                </div>

                                <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white leading-tight">
                                    Kontrol Keuanganmu<br>Seperti <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-400">Mini Fintech.</span>
                                </h2>

                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm md:text-base font-medium">
                                    Bukan sekedar catatan. Ini <span class="font-bold text-slate-900 dark:text-white">Financial Control Panel</span> — lengkap dengan Health Score, 90-Day Runway Projection, Budget Tracker, dan laporan SHA-256 export yang tamper-evident.
                                </p>

                                <ul class="space-y-3 pt-2 text-sm text-slate-700 dark:text-slate-300 font-medium">
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 mt-0.5"></i> <span><strong class="text-slate-900 dark:text-white">Financial Health Score:</strong> Metrik 0–100 berbasis saving rate, stabilitas, dan runway.</span></li>
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 mt-0.5"></i> <span><strong class="text-slate-900 dark:text-white">Budget Engine:</strong> Set limit per kategori dan pantau real-time setiap bulan.</span></li>
                                    <li class="flex items-start gap-3"><i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 mt-0.5"></i> <span><strong class="text-slate-900 dark:text-white">Projection & Export:</strong> Proyeksi 90-hari otomatis + export JSON dengan integrity hash.</span></li>
                                </ul>

                                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                                    <button id="btn-cashbook-access"
                                        onclick="
                                            if (localStorage.getItem('auth_token')) {
                                                window.location.href = '/cashbook';
                                            } else {
                                                localStorage.setItem('login_redirect', '/cashbook');
                                                document.dispatchEvent(new CustomEvent('open-login-modal'));
                                            }
                                        "
                                        class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold rounded-xl transition-all shadow-xl shadow-emerald-500/20 hover:shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-2 group w-full sm:w-auto">
                                        <i class="fas fa-layer-group group-hover:scale-110 transition-transform"></i>
                                        <span id="btn-cashbook-label">Buka Dashboard</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </section>
        <script>
        (function(){
            var lbl = document.getElementById('btn-cashbook-label');
            if (lbl && !localStorage.getItem('auth_token')) {
                lbl.textContent = 'Login & Akses Gratis';
            }
        })();
        </script>

        <style>
        /* ── Product Cards: always show desktop side-by-side layout, scaled down on mobile ── */

        /* Ensure the cards look great on desktop but stack naturally on mobile */
        /* To achieve standard responsive layout on mobile without breaking anything */
        @media (max-width: 1023px) {
            #mini-course-teaser .group\/card .flex-col.md\:flex-row {
                flex-direction: column !important;
                gap: 1.5rem !important;
                padding-bottom: 2rem !important;
            }
            
            #mini-course-teaser .group\/card .md\:w-1\/2 {
                width: 100% !important;
            }
            
            /* Order: image on top, text on bottom */
            #mini-course-teaser .group\/card .order-2 { order: 1 !important; }
            #mini-course-teaser .group\/card .md\:order-1 { order: 1 !important; }
            #mini-course-teaser .group\/card .order-1 { order: 2 !important; }
            #mini-course-teaser .group\/card .md\:order-2 { order: 2 !important; }
            
            /* Center align text content on mobile for better appearance */
            #mini-course-teaser .group\/card .w-1\/2.flex-shrink-0 {
                width: 100% !important;
                text-align: center;
                align-items: center;
            }
            
            #mini-course-teaser .group\/card ul {
                text-align: left; /* Keep list items left-aligned */
            }
        }
        </style>
        </div><!-- /#desktop-tab-content -->

        <!-- Bento Grid Tab Logic -->
        <script>
            function switchBentoTab(targetId) {
                // Ensure desktop nav is set to 'feature' so bento grid and roadmap are visible
                if (typeof switchDesktopNavTab === 'function') {
                    const featureLink = document.querySelector('.desktop-nav-link:first-child');
                    if (featureLink && !featureLink.classList.contains('bg-emerald-50')) {
                        featureLink.click();
                    }
                }
                
                // 1. Array of all tab IDs and content IDs
                const sections = ['goal-planner', 'profit-simulator-section', 'business-simulation-lab', 'roadmap-container'];
                
                // 2. Hide all contents and reset tab styles
                sections.forEach(id => {
                    // Update content visibility
                    const contentObj = document.getElementById(id);
                    if (contentObj) {
                        if (id === targetId) {
                            contentObj.classList.remove('hidden');
                            contentObj.classList.add('animate-fade-in-up');
                        } else {
                            contentObj.classList.add('hidden');
                            contentObj.classList.remove('animate-fade-in-up');
                        }
                    }

                    // Update Tab Button styles
                    const tabObj = document.getElementById('tab-' + id);
                    if (tabObj) {
                        const glow = tabObj.querySelector('.bento-glow');
                        const bevel = tabObj.querySelector('.bevel-glow');
                        const iconGlow = tabObj.querySelector('.icon-glow');
                        const iconBox = tabObj.querySelector('.w-12');
                        const cardTitle = tabObj.querySelector('h3');

                        if (id === targetId) {
                            // Apply Active Style (3D highlighted popup effect)
                            tabObj.classList.remove('border-slate-200', 'dark:border-slate-700/60', 'bg-white', 'dark:bg-slate-800/40', 'hover:-translate-y-1', 'hover:border-violet-400', 'dark:hover:border-violet-500/50', 'hover:border-blue-400', 'dark:hover:border-blue-500/50', 'hover:bg-violet-50', 'dark:hover:bg-violet-900/10', 'hover:bg-blue-50', 'dark:hover:bg-blue-900/10', 'hover:shadow-lg', 'scale-100', 'z-0');
                            
                            tabObj.classList.add('-translate-y-2', 'scale-105', 'z-10');
                            
                            if (bevel) bevel.classList.add('opacity-50');
                            if (cardTitle) {
                                cardTitle.classList.add('drop-shadow');
                                cardTitle.style.textShadow = '0 2px 4px rgba(0,0,0,0.1)';
                            }

                            if (id === 'goal-planner') {
                                tabObj.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20', 'shadow-[0_8px_30px_rgba(16,185,129,0.2)]', 'dark:shadow-[0_0_25px_rgba(16,185,129,0.15)]', 'ring-2', 'ring-emerald-500/50');
                                glow.classList.add('opacity-100', 'bg-emerald-500/30');
                                glow.classList.remove('opacity-0', 'bg-violet-500/30', 'bg-blue-500/30');
                                if (iconBox) iconBox.className = 'w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center mb-4 transition-colors shadow-inner';
                                if (iconGlow) iconGlow.className = 'fas fa-bullseye text-2xl text-emerald-600 dark:text-emerald-400 drop-shadow-md icon-glow';
                            } else if (id === 'profit-simulator-section') {
                                tabObj.classList.add('border-violet-500', 'bg-violet-50', 'dark:bg-violet-900/20', 'shadow-[0_8px_30px_rgba(139,92,246,0.2)]', 'dark:shadow-[0_0_25px_rgba(139,92,246,0.15)]', 'ring-2', 'ring-violet-500/50');
                                glow.classList.add('opacity-100', 'bg-violet-500/30');
                                glow.classList.remove('opacity-0', 'bg-emerald-500/30', 'bg-blue-500/30');
                                if (iconBox) iconBox.className = 'w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-500/20 border border-violet-500/30 flex items-center justify-center mb-4 transition-colors shadow-inner';
                                if (iconGlow) iconGlow.className = 'fas fa-chart-line text-2xl text-violet-600 dark:text-violet-400 drop-shadow-md icon-glow';
                            } else if (id === 'business-simulation-lab') {
                                tabObj.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'shadow-[0_8px_30px_rgba(59,130,246,0.2)]', 'dark:shadow-[0_0_25px_rgba(59,130,246,0.15)]', 'ring-2', 'ring-blue-500/50');
                                glow.classList.add('opacity-100', 'bg-blue-500/30');
                                glow.classList.remove('opacity-0', 'bg-emerald-500/30', 'bg-violet-500/30', 'bg-amber-500/30');
                                if (iconBox) iconBox.className = 'w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 border border-blue-500/30 flex items-center justify-center mb-4 transition-colors shadow-inner';
                                if (iconGlow) iconGlow.className = 'fas fa-brain text-2xl text-blue-600 dark:text-blue-400 drop-shadow-md icon-glow';
                            } else if (id === 'roadmap-container') {
                                tabObj.classList.add('border-amber-500', 'bg-amber-50', 'dark:bg-amber-900/20', 'shadow-[0_8px_30px_rgba(245,158,11,0.2)]', 'dark:shadow-[0_0_25px_rgba(245,158,11,0.15)]', 'ring-2', 'ring-amber-500/50');
                                glow.classList.add('opacity-100', 'bg-amber-500/30');
                                glow.classList.remove('opacity-0', 'bg-emerald-500/30', 'bg-violet-500/30', 'bg-blue-500/30');
                                if (iconBox) iconBox.className = 'w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 border border-amber-500/30 flex items-center justify-center mb-4 transition-colors shadow-inner';
                                if (iconGlow) iconGlow.className = 'fas fa-map-marked-alt text-2xl text-amber-600 dark:text-amber-400 drop-shadow-md icon-glow';
                            }
                        } else {
                            // Apply Inactive Style
                            tabObj.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20', 'shadow-[0_8px_30px_rgba(16,185,129,0.2)]', 'dark:shadow-[0_0_25px_rgba(16,185,129,0.15)]', 'ring-2', 'ring-emerald-500/50', 'border-violet-500', 'bg-violet-50', 'dark:bg-violet-900/20', 'shadow-[0_8px_30px_rgba(139,92,246,0.2)]', 'dark:shadow-[0_0_25px_rgba(139,92,246,0.15)]', 'ring-2', 'ring-violet-500/50', 'border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'shadow-[0_8px_30px_rgba(59,130,246,0.2)]', 'dark:shadow-[0_0_25px_rgba(59,130,246,0.15)]', 'ring-2', 'ring-blue-500/50', 'border-amber-500', 'bg-amber-50', 'dark:bg-amber-900/20', 'shadow-[0_8px_30px_rgba(245,158,11,0.2)]', 'dark:shadow-[0_0_25px_rgba(245,158,11,0.15)]', 'ring-2', 'ring-amber-500/50', '-translate-y-2', 'scale-105', 'z-10');
                            
                            tabObj.classList.add('border-slate-200', 'dark:border-slate-700/60', 'bg-white', 'dark:bg-slate-800/40', 'hover:-translate-y-1', 'scale-100', 'z-0');
                            
                            // Re-apply correct hover styles based on the ID to maintain the hover glow color
                            if (id === 'profit-simulator-section') tabObj.classList.add('hover:border-violet-400', 'dark:hover:border-violet-500/50', 'hover:bg-violet-50', 'dark:hover:bg-violet-900/10', 'hover:shadow-lg');
                            else if (id === 'business-simulation-lab') tabObj.classList.add('hover:border-blue-400', 'dark:hover:border-blue-500/50', 'hover:bg-blue-50', 'dark:hover:bg-blue-900/10', 'hover:shadow-lg');
                            else if (id === 'roadmap-container') tabObj.classList.add('hover:border-amber-400', 'dark:hover:border-amber-500/50', 'hover:bg-amber-50', 'dark:hover:bg-amber-900/10', 'hover:shadow-lg');
                            else if (id === 'goal-planner') tabObj.classList.add('hover:border-emerald-400', 'dark:hover:border-emerald-500/50', 'hover:bg-emerald-50', 'dark:hover:bg-emerald-900/10', 'hover:shadow-lg');
                            
                            if (bevel) bevel.classList.remove('opacity-50');
                            if (cardTitle) {
                                cardTitle.classList.remove('drop-shadow');
                                cardTitle.style.textShadow = 'none';
                            }

                            glow.classList.remove('opacity-100');
                            glow.classList.add('opacity-0');
                            
                            // Reset icon styles
                            if (iconBox) iconBox.className = 'w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 flex items-center justify-center mb-4 transition-colors';
                            
                            // Reset icon color based on ID
                            if (iconGlow) {
                                if (id === 'goal-planner') iconGlow.className = 'fas fa-bullseye text-2xl text-slate-400 dark:text-emerald-400 transition-colors icon-glow';
                                else if (id === 'profit-simulator-section') iconGlow.className = 'fas fa-chart-line text-2xl text-slate-400 dark:text-violet-400 transition-colors icon-glow';
                                else if (id === 'business-simulation-lab') iconGlow.className = 'fas fa-brain text-2xl text-slate-400 dark:text-blue-400 transition-colors icon-glow';
                                else if (id === 'roadmap-container') iconGlow.className = 'fas fa-map-marked-alt text-2xl text-slate-400 dark:text-amber-400 transition-colors icon-glow';
                            }
                        }
                    }
                });
            }

            // Initialization on DOMContentLoaded
            document.addEventListener('DOMContentLoaded', () => {
                switchBentoTab('goal-planner');
            });
        </script>

        <!-- Container for Feature Tabs -->
        <div id="bento-workspace-container">

        @if(($settings['feature_calculator'] ?? '1') == '1')
        <section id="goal-planner" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 relative animate-fade-in-up">

            {{-- Glow orbs background --}}
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative bg-slate-900 rounded-3xl overflow-hidden border border-slate-700/60 shadow-2xl shadow-slate-900/50">


                {{-- Top accent bar --}}
                <div class="h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-400 to-violet-500"></div>

                <div class="p-8 lg:p-12">

                    {{-- Header --}}
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-4">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            Live Strategy Engine
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-black text-white mb-3">Reverse Goal <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Planner</span></h3>
                        <p class="text-slate-400 text-sm max-w-md mx-auto">Dari target hidup kamu → sistem otomatis breakdown jadi angka yang bisa dieksekusi.</p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            <button data-start-tour="reversePlanner" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-emerald-500/40 hover:border-emerald-400 text-emerald-400 text-xs font-bold rounded-xl transition-all shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/20 active:scale-95">
                                <i class="fas fa-graduation-cap"></i> Mulai Tour
                            </button>
                            <button onclick="window.open('/guide/phase-1', '_blank')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-500 dark:text-emerald-400 text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                                <i class="fas fa-book-open"></i> Panduan
                            </button>
                        </div>
                    </div>

                    {{-- Two-column layout: Inputs | Live Preview --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

                        {{-- ══ LEFT: Inputs ══════════════════════════════════════════ --}}
                        <div class="lg:col-span-7 xl:col-span-8 space-y-4 sm:space-y-8" id="rgp-core-layer">

                            {{-- Step 1: Model Bisnis --}}
                            <details class="smart-accordion group bg-slate-100 dark:bg-slate-800/30 md:bg-transparent md:dark:bg-transparent border border-slate-200 dark:border-slate-700/50 md:border-none rounded-xl sm:rounded-2xl md:rounded-none p-3 sm:p-4 md:p-0 overflow-hidden" open>
                                <summary class="flex justify-between items-center cursor-pointer md:cursor-default mb-2 sm:mb-4">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-500 text-white text-[10px] font-black shrink-0">1</div>
                                        <p class="text-[11px] sm:text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Pilih Model Bisnis Kamu</p>
                                    </div>
                                    <i class="fas fa-chevron-down text-slate-400 toggle-icon group-open:rotate-180 transition-transform text-sm"></i>
                                </summary>
                                <div class="smart-accordion-content">
                                <div class="flex flex-nowrap gap-2 sm:gap-4 pb-2 sm:pb-4 scroll-row scroll-mask" id="rgp-model-cards">
                                    <button type="button" data-model="dropship" data-default-price="80000"
                                        class="card-dynamic shrink-0 rgp-model-card group relative flex flex-col items-center gap-1.5 sm:gap-3 p-3 sm:p-5 rounded-xl sm:rounded-2xl border-2 border-slate-700/80 bg-slate-800/40 hover:border-orange-500/50 hover:bg-orange-500/10 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden backdrop-blur-sm">
                                        <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-orange-500/0 group-hover:via-orange-500/50 to-transparent transition-opacity"></div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-orange-500/0 to-orange-500/0 group-hover:from-orange-500/5 group-hover:to-transparent transition-all duration-300 pointer-events-none rounded-xl sm:rounded-2xl"></div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-orange-500/15 border border-orange-500/30 flex items-center justify-center group-hover:bg-orange-500/25 group-hover:border-orange-500/50 group-hover:scale-110 transition-all duration-300 shadow-inner">
                                            <i class="fas fa-boxes-stacked text-orange-400 text-sm sm:text-lg group-hover:drop-shadow-[0_0_8px_rgba(249,115,22,0.8)] transition-all"></i>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[11px] sm:text-sm font-black text-slate-200 leading-tight group-hover:text-orange-400 transition-colors">Dropship</p>
                                            <p class="text-[8px] sm:text-[10px] uppercase tracking-wider font-bold text-slate-500 mt-0.5 sm:mt-1">Low Margin, High Vol</p>
                                        </div>
                                    </button>
                                    
                                    <button type="button" data-model="digital" data-default-price="300000"
                                        class="card-dynamic shrink-0 rgp-model-card group relative flex flex-col items-center gap-1.5 sm:gap-3 p-3 sm:p-5 rounded-xl sm:rounded-2xl border-2 border-slate-700/80 bg-slate-800/40 hover:border-violet-500/50 hover:bg-violet-500/10 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden backdrop-blur-sm">
                                        <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-violet-500/0 group-hover:via-violet-500/50 to-transparent transition-opacity"></div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-violet-500/0 to-violet-500/0 group-hover:from-violet-500/5 group-hover:to-transparent transition-all duration-300 pointer-events-none rounded-xl sm:rounded-2xl"></div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-violet-500/15 border border-violet-500/30 flex items-center justify-center group-hover:bg-violet-500/25 group-hover:border-violet-500/50 group-hover:scale-110 transition-all duration-300 shadow-inner">
                                            <i class="fas fa-bolt text-violet-400 text-sm sm:text-lg group-hover:drop-shadow-[0_0_8px_rgba(139,92,246,0.8)] transition-all"></i>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[11px] sm:text-sm font-black text-slate-200 leading-tight group-hover:text-violet-400 transition-colors">Digital Product</p>
                                            <p class="text-[8px] sm:text-[10px] uppercase tracking-wider font-bold text-slate-500 mt-0.5 sm:mt-1">High Margin</p>
                                        </div>
                                    </button>
                                    
                                    <button type="button" data-model="service" data-default-price="1500000"
                                        class="card-dynamic shrink-0 rgp-model-card group relative flex flex-col items-center gap-1.5 sm:gap-3 p-3 sm:p-5 rounded-xl sm:rounded-2xl border-2 border-slate-700/80 bg-slate-800/40 hover:border-emerald-500/50 hover:bg-emerald-500/10 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden backdrop-blur-sm">
                                        <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-emerald-500/0 group-hover:via-emerald-500/50 to-transparent transition-opacity"></div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/0 to-emerald-500/0 group-hover:from-emerald-500/5 group-hover:to-transparent transition-all duration-300 pointer-events-none rounded-xl sm:rounded-2xl"></div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center group-hover:bg-emerald-500/25 group-hover:border-emerald-500/50 group-hover:scale-110 transition-all duration-300 shadow-inner">
                                            <i class="fas fa-handshake text-emerald-400 text-sm sm:text-lg group-hover:drop-shadow-[0_0_8px_rgba(52,211,153,0.8)] transition-all"></i>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[11px] sm:text-sm font-black text-slate-200 leading-tight group-hover:text-emerald-400 transition-colors">Service/Agency</p>
                                            <p class="text-[8px] sm:text-[10px] uppercase tracking-wider font-bold text-slate-500 mt-0.5 sm:mt-1">High Ticket</p>
                                        </div>
                                    </button>
                                    
                                    <button type="button" data-model="stock" data-default-price="150000"
                                        class="card-dynamic shrink-0 rgp-model-card group relative flex flex-col items-center gap-1.5 sm:gap-3 p-3 sm:p-5 rounded-xl sm:rounded-2xl border-2 border-slate-700/80 bg-slate-800/40 hover:border-blue-500/50 hover:bg-blue-500/10 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden backdrop-blur-sm">
                                        <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500/0 group-hover:via-blue-500/50 to-transparent transition-opacity"></div>
                                        <div class="absolute inset-0 bg-gradient-to-b from-blue-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:to-transparent transition-all duration-300 pointer-events-none rounded-xl sm:rounded-2xl"></div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center group-hover:bg-blue-500/25 group-hover:border-blue-500/50 group-hover:scale-110 transition-all duration-300 shadow-inner">
                                            <i class="fas fa-store text-blue-400 text-sm sm:text-lg group-hover:drop-shadow-[0_0_8px_rgba(96,165,250,0.8)] transition-all"></i>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[11px] sm:text-sm font-black text-slate-200 leading-tight group-hover:text-blue-400 transition-colors">Stock / Retail</p>
                                            <p class="text-[8px] sm:text-[10px] uppercase tracking-wider font-bold text-slate-500 mt-0.5 sm:mt-1">Avg Margin</p>
                                        </div>
                                    </button>
                                </div>
                                </div>
                            </details>

                            {{-- Step 2: Target + Harga --}}
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 sm:gap-6">
                                {{-- Target Cuan --}}
                                <details class="col-span-1 smart-accordion group bg-slate-100 dark:bg-slate-800/30 md:bg-transparent md:dark:bg-transparent border border-slate-200 dark:border-slate-700/50 md:border-none rounded-xl sm:rounded-2xl md:rounded-none p-3 sm:p-4 md:p-0" open>
                                    <summary class="flex justify-between items-center cursor-pointer md:cursor-default mb-2 sm:mb-4">
                                        <div class="flex items-center gap-1.5 sm:gap-3">
                                            <div class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-500 text-white text-[10px] sm:text-[10px] font-black shrink-0">2</div>
                                            <p class="text-[9px] sm:text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Target Profit</p>
                                        </div>
                                        <i class="fas fa-chevron-down text-slate-400 toggle-icon group-open:rotate-180 transition-transform text-xs"></i>
                                    </summary>
                                    <div class="smart-accordion-content">
                                        <div class="flex flex-wrap gap-1 sm:gap-2 mb-2 sm:mb-3" id="rgp-target-presets">
                                        <button type="button" data-value="5000000"  class="rgp-preset-btn rgp-target-preset px-1.5 py-1 sm:px-3 sm:py-1.5 rounded-md sm:rounded-lg text-[9px] sm:text-xs font-bold border border-slate-700 bg-slate-800 text-slate-300 hover:border-emerald-500/70 hover:text-emerald-400 hover:bg-emerald-500/10 transition-all flex-1 text-center">Rp 5jt</button>
                                        <button type="button" data-value="10000000" class="rgp-preset-btn rgp-target-preset px-1.5 py-1 sm:px-3 sm:py-1.5 rounded-md sm:rounded-lg text-[9px] sm:text-xs font-bold border border-slate-700 bg-slate-800 text-slate-300 hover:border-emerald-500/70 hover:text-emerald-400 hover:bg-emerald-500/10 transition-all flex-1 text-center">Rp 10jt</button>
                                        <button type="button" data-value="30000000" class="rgp-preset-btn rgp-target-preset px-1.5 py-1 sm:px-3 sm:py-1.5 rounded-md sm:rounded-lg text-[9px] sm:text-xs font-bold border border-slate-700 bg-slate-800 text-slate-300 hover:border-emerald-500/70 hover:text-emerald-400 hover:bg-emerald-500/10 transition-all flex-1 text-center">Rp 30jt</button>
                                    </div>
                                        <div class="relative">
                                            <span class="absolute left-2.5 sm:left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs sm:text-sm">Rp</span>
                                            <input type="number" id="rp-target-profit" placeholder="Nominal..." required
                                                class="w-full pl-8 sm:pl-12 bg-slate-800 border border-slate-700 rounded-lg sm:rounded-xl px-2 py-2 sm:px-4 sm:py-3 text-white font-mono font-bold placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 focus:outline-none text-[10px] sm:text-sm transition-all focus:text-sm">
                                        </div>
                                    </div>
                                </details>

                                {{-- Harga Jual --}}
                                <details class="col-span-1 smart-accordion group bg-slate-100 dark:bg-slate-800/30 md:bg-transparent md:dark:bg-transparent border border-slate-200 dark:border-slate-700/50 md:border-none rounded-xl sm:rounded-2xl md:rounded-none p-3 sm:p-4 md:p-0" open>
                                    <summary class="flex justify-between items-center cursor-pointer md:cursor-default mb-2 sm:mb-4">
                                        <div class="flex items-center gap-1.5 sm:gap-3">
                                            <div class="flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-emerald-500 text-white text-[10px] sm:text-[10px] font-black shrink-0">3</div>
                                            <div class="flex flex-col gap-0.5 sm:gap-1">
                                                <p class="text-[9px] sm:text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Harga Jual</p>
                                                <span class="text-[8px] sm:text-[10px] text-emerald-500/70 font-normal normal-case block">auto-suggest</span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-down text-slate-400 toggle-icon group-open:rotate-180 transition-transform text-xs"></i>
                                    </summary>
                                    <div class="smart-accordion-content">
                                        <div class="flex flex-wrap gap-1 sm:gap-2 mb-2 sm:mb-3 min-h-[26px] sm:min-h-[30px]" id="rgp-price-presets">
                                        {{-- Filled dynamically by JS --}}
                                    </div>
                                        <div class="relative">
                                            <span class="absolute left-2.5 sm:left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs sm:text-sm">Rp</span>
                                            <input type="number" id="rp-price" placeholder="Nominal..." required
                                                class="w-full pl-8 sm:pl-12 bg-slate-800 border border-slate-700 rounded-lg sm:rounded-xl px-2 py-2 sm:px-4 sm:py-3 text-white font-mono font-bold placeholder-slate-600 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 focus:outline-none text-[10px] sm:text-sm transition-all focus:text-sm">
                                        </div>
                                    </div>
                                </details>
                            </div>

                            {{-- Loading indicator --}}
                            <div id="rgp-loading-indicator" class="hidden items-center gap-3 text-xs sm:text-sm text-slate-500">
                                <div class="flex gap-1">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                                </div>
                                <span>Menghitung strategi kamu...</span>
                            </div>

                            {{-- Advanced accordion --}}
                            <div class="border-t border-slate-700/60 pt-3 sm:pt-5">
                                <button type="button" id="rgp-advanced-toggle"
                                    class="flex items-center gap-2 sm:gap-3 text-[11px] sm:text-sm font-semibold text-slate-400 hover:text-emerald-400 transition-colors group w-full text-left">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center group-hover:border-emerald-500/40 transition-colors">
                                        <i class="fas fa-sliders-h text-slate-500 text-[10px] sm:text-xs group-hover:text-emerald-400 transition-colors"></i>
                                    </div>
                                    Sesuaikan Asumsi Strategi
                                    <i id="rgp-toggle-icon" class="fas fa-chevron-down ml-auto text-[10px] sm:text-xs text-slate-600 transition-transform duration-300"></i>
                                </button>

                                <div id="rgp-advanced-panel" class="hidden mt-3 sm:mt-6 grid grid-cols-2 md:grid-cols-2 gap-2 sm:gap-6 animate-fade-in">

                                    {{-- Slider: Timeline --}}
                                    <div class="bg-white dark:bg-slate-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/50">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-calendar-alt text-slate-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider text-center sm:text-left w-full">Deadline</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-emerald-500 dark:text-emerald-400 tabular-nums whitespace-nowrap"><span id="rp-timeline-display">30</span> Hari</span>
                                        </div>
                                        <input type="range" id="rp-timeline" min="7" max="90" value="30" step="1"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-emerald-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>7 hari</span><span>90 hari</span></div>
                                    </div>

                                    {{-- Slider: Modal --}}
                                    <div class="bg-white dark:bg-slate-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/50">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-wallet text-slate-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider text-center sm:text-left w-full">Modal</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-emerald-500 dark:text-emerald-400 tabular-nums whitespace-nowrap">Rp <span id="rp-capital-display">5.000...</span></span>
                                        </div>
                                        <input type="range" id="rp-capital" min="1000000" max="50000000" value="5000000" step="500000"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-emerald-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>1jt</span><span>50jt</span></div>
                                    </div>

                                    {{-- Slider: Margin --}}
                                    <div class="bg-white dark:bg-slate-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/50">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-percent text-slate-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="biz-term text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider cursor-help text-center sm:text-left w-full" data-term="margin">Margin </label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-emerald-500 dark:text-emerald-400 tabular-nums whitespace-nowrap"><span id="rp-margin-display">--</span>%</span>
                                        </div>
                                        <input type="range" id="rp-margin" min="5" max="95" value="20" step="5"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-emerald-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>5%</span><span>95%</span></div>
                                    </div>

                                    {{-- Slider: Jam Kerja --}}
                                    <div class="bg-white dark:bg-slate-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/50">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-fire text-slate-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider text-center sm:text-left w-full">Daily Grind</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-emerald-500 dark:text-emerald-400 tabular-nums whitespace-nowrap"><span id="rp-hours-display">4</span> Jam</span>
                                        </div>
                                        <input type="range" id="rp-hours" min="1" max="12" value="4" step="1"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-emerald-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>1 jam</span><span>12 jam</span></div>
                                    </div>

                                    {{-- Traffic Strategy --}}
                                    <div class="bg-white dark:bg-slate-800/50 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/50 col-span-2">
                                        <div class="flex items-center justify-center sm:justify-start gap-1.5 sm:gap-2 mb-2 sm:mb-3">
                                            <i class="fas fa-rocket text-slate-500 text-[9px] sm:text-xs"></i>
                                            <label class="biz-term text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider cursor-help" data-term="traffic">Strategi Traffic </label>
                                        </div>
                                        <div class="flex gap-1.5 sm:gap-2" id="rgp-strategy-cards">
                                            <button type="button" data-strategy="ads"
                                                class="rgp-strategy-card flex-1 py-1.5 sm:py-2.5 px-1 sm:px-2 rounded-lg sm:rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[9px] sm:text-xs font-bold text-slate-600 dark:text-slate-400 text-center hover:border-yellow-500/50 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-500/5 dark:hover:bg-yellow-500/10 transition-all">
                                                <i class="fas fa-bolt inline-block sm:block mb-0 sm:mb-1 mr-1 sm:mr-0 text-yellow-500 sm:text-yellow-400/60"></i>Ads
                                            </button>
                                            <button type="button" data-strategy="organic"
                                                class="rgp-strategy-card flex-1 py-1.5 sm:py-2.5 px-1 sm:px-2 rounded-lg sm:rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[9px] sm:text-xs font-bold text-slate-600 dark:text-slate-400 text-center hover:border-green-500/50 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-500/5 dark:hover:bg-green-500/10 transition-all">
                                                <i class="fas fa-seedling inline-block sm:block mb-0 sm:mb-1 mr-1 sm:mr-0 text-green-500 sm:text-green-400/60"></i>Organic
                                            </button>
                                            <button type="button" data-strategy="hybrid"
                                                class="rgp-strategy-card flex-1 py-1.5 sm:py-2.5 px-1 sm:px-2 rounded-lg sm:rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-[9px] sm:text-xs font-bold text-slate-600 dark:text-slate-400 text-center hover:border-blue-500/50 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-500/5 dark:hover:bg-blue-500/10 transition-all">
                                                <i class="fas fa-shuffle inline-block sm:block mb-0 sm:mb-1 mr-1 sm:mr-0 text-blue-500 sm:text-blue-400/60"></i>Hybrid
                                            </button>
                                        </div>
                                    </div>

                                    {{-- ══ MODEL SPECIFIC INPUTS ══ --}}

                                    {{-- [DROPSHIP] Persentase RTS (Return to Sender) --}}
                                    <div class="model-specific-input dropship-only bg-rose-50 dark:bg-rose-900/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-rose-200 dark:border-rose-900/50 hidden">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-undo-alt text-rose-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center sm:text-left w-full">Estimasi Retur (RTS)</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-rose-500 tabular-nums whitespace-nowrap"><span id="rp-rts-display">10</span>%</span>
                                        </div>
                                        <input type="range" id="rp-rts" min="0" max="30" value="10" step="1"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-rose-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>0% (Aman)</span><span>30% (Bahaya)</span></div>
                                    </div>

                                    {{-- [STOCK / E-COM] Biaya Gudang & Packing --}}
                                    <div class="model-specific-input stock-only bg-blue-50 dark:bg-blue-900/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-blue-200 dark:border-blue-900/50 hidden">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-warehouse text-blue-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center sm:text-left w-full">Biaya Gudang /bln</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-blue-500 tabular-nums whitespace-nowrap">Rp <span id="rp-warehouse-display">0</span></span>
                                        </div>
                                        <input type="range" id="rp-warehouse" min="0" max="10000000" value="0" step="500000"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-blue-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>Gratis</span><span>10jt/bln</span></div>
                                    </div>

                                    {{-- [DIGITAL] Komisi Affiliate / Reseller --}}
                                    <div class="model-specific-input digital-only bg-violet-50 dark:bg-violet-900/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-violet-200 dark:border-violet-900/50 hidden">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-users text-violet-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center sm:text-left w-full">Bagi Hasil / Afiliasi</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-violet-500 tabular-nums whitespace-nowrap"><span id="rp-affiliate-display">30</span>%</span>
                                        </div>
                                        <input type="range" id="rp-affiliate" min="0" max="70" value="30" step="5"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-violet-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>0% (Solo)</span><span>70%</span></div>
                                    </div>

                                    {{-- [SERVICE] Maksimal Klien (Kapasitas) --}}
                                    <div class="model-specific-input service-only bg-emerald-50 dark:bg-emerald-900/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-emerald-200 dark:border-emerald-900/50 hidden">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-users-cog text-emerald-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center sm:text-left w-full">Maks Klien /Bulan</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-emerald-500 tabular-nums whitespace-nowrap"><span id="rp-capacity-display">10</span> Klien</span>
                                        </div>
                                        <input type="range" id="rp-capacity" min="1" max="50" value="10" step="1"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-emerald-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>1 Klien</span><span>50 Klien</span></div>
                                    </div>
                                    
                                    {{-- Global Fixed Cost --}}
                                    <div class="model-specific-input dropship-only stock-only service-only digital-only col-span-2 bg-slate-100 dark:bg-slate-800/80 rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200 dark:border-slate-700/80 hidden">
                                         <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <div class="flex items-center gap-1.5 mx-auto sm:mx-0">
                                                <i class="fas fa-building text-slate-500 text-[9px] sm:text-xs hidden sm:block"></i>
                                                <label class="text-[9px] sm:text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-center sm:text-left w-full">Gaji Tim & Operasional Bulanan</label>
                                            </div>
                                            <span class="text-[10px] sm:text-sm font-black text-rose-500 tabular-nums whitespace-nowrap">Rp <span id="rp-fixedcost-display">0</span></span>
                                        </div>
                                        <input type="range" id="rp-fixedcost" min="0" max="30000000" value="0" step="1000000"
                                            class="w-full h-1.5 bg-slate-300 dark:bg-slate-700 rounded-full appearance-none cursor-pointer accent-rose-500">
                                        <div class="flex justify-between text-[8px] sm:text-[10px] text-slate-500 dark:text-slate-600 mt-1.5 sm:mt-2"><span>Tidak Ada Gaji Tim</span><span>30jt /Bulan</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ RIGHT: Live Preview Panel ════════════════════════════ --}}
                        <div class="lg:col-span-5 xl:col-span-4 mt-2 sm:mt-0">
                        <div id="mobile-status-sheet" class="md:sticky md:top-8 bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 rounded-xl sm:rounded-2xl p-3 sm:p-6 flex flex-col shadow-lg shadow-slate-200/50 dark:shadow-none">

                                {{-- Mobile header inline (no drag handle) --}}

                                {{-- Desktop Header --}}
                                <div class="hidden md:flex items-center gap-2 mb-6 shrink-0">
                                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                                    <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Status Plan Kamu</p>
                                </div>

                                {{-- Internal Scroll Wrapper for Mobile Sheet --}}
                                <div class="overflow-y-auto custom-scroll flex-1 pr-1 md:pr-0 pb-6 md:pb-0">
                                    {{-- Status Display --}}
                                <div id="rp-status-card" class="mb-2 sm:mb-4 p-3 sm:p-4 rounded-lg sm:rounded-xl border-l-4 border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900/50 transition-all duration-500 shadow-sm shadow-slate-200 dark:shadow-none">
                                    <div class="flex items-start gap-2 sm:gap-3">
                                        <div id="rp-status-icon" class="text-xl sm:text-3xl text-slate-400 dark:text-slate-700 shrink-0 mt-0.5 relative z-0">
                                            <i class="fas fa-circle-notch fa-spin"></i>
                                        </div>
                                        <div class="relative z-10 w-full">
                                            <p class="text-[8px] sm:text-[10px] text-slate-500 uppercase font-bold mb-0.5">Goal Status</p>
                                            <h3 id="rp-goal-status" class="text-sm sm:text-lg font-black text-slate-800 dark:text-slate-300 leading-tight">Menunggu input...</h3>
                                            <p id="rp-constraint-msg" class="text-[9px] sm:text-xs text-slate-500 dark:text-slate-400 mt-1 sm:mt-1.5 leading-relaxed">Pilih model bisnis dan isi target kamu.</p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                                        <button id="rp-why-btn" class="text-[9px] sm:text-[10px] font-bold text-slate-600 hover:text-emerald-400 underline decoration-dotted transition-colors">
                                            Kenapa status ini?
                                        </button>
                                    </div>
                                </div>

                                {{-- Key Numbers --}}
                                <div class="space-y-1.5 sm:space-y-2">
                                    <div class="grid grid-cols-3 gap-1.5 sm:gap-2">
                                        <div class="bg-white dark:bg-slate-900/60 rounded-lg sm:rounded-xl p-2 sm:p-3 text-center border border-slate-200 dark:border-slate-800 shadow-sm shadow-slate-200 dark:shadow-none flex flex-col justify-center">
                                            <p class="text-[8px] sm:text-[9px] text-slate-500 dark:text-slate-600 uppercase font-bold mb-0.5 sm:mb-1">Sales/bln</p>
                                            <p id="rp-req-units" class="text-xs sm:text-sm font-black text-slate-800 dark:text-white">--</p>
                                        </div>
                                        <div class="bg-white dark:bg-slate-900/60 rounded-lg sm:rounded-xl p-2 sm:p-3 text-center border border-slate-200 dark:border-slate-800 shadow-sm shadow-slate-200 dark:shadow-none flex flex-col justify-center relative group">
                                            <p class="text-[8px] sm:text-[9px] text-slate-500 dark:text-slate-600 uppercase font-bold mb-0.5 sm:mb-1">Sales/hari</p>
                                            <p id="rp-daily-units" class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400">--</p>
                                            <p id="rp-sales-micro" class="text-[7.5px] sm:text-[8px] text-slate-400 dark:text-slate-500 leading-tight mt-1"></p>
                                        </div>
                                        <div class="bg-white dark:bg-slate-900/60 rounded-lg sm:rounded-xl p-2 sm:p-3 text-center border border-slate-200 dark:border-slate-800 shadow-sm shadow-slate-200 dark:shadow-none flex flex-col justify-center relative group">
                                            <p class="text-[8px] sm:text-[9px] text-slate-500 dark:text-slate-600 uppercase font-bold mb-0.5 sm:mb-1">Traffic /hari</p>
                                            <p id="rp-req-traffic" class="text-xs sm:text-sm font-black text-blue-600 dark:text-blue-400">--</p>
                                            <p id="rp-traffic-micro" class="text-[7.5px] sm:text-[8px] text-slate-400 dark:text-slate-500 leading-tight mt-1"></p>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-slate-900/60 rounded-lg sm:rounded-xl p-2 sm:p-3 flex items-center justify-between border border-slate-200 dark:border-slate-800 shadow-sm shadow-slate-200 dark:shadow-none">
                                        <p class="biz-term text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-500 uppercase font-bold cursor-help" data-term="ad_spend">Budget Iklan </p>
                                        <p id="rp-req-budget" class="text-xs sm:text-sm font-black text-yellow-600 dark:text-yellow-400 tabular-nums">--</p>
                                    </div>
                                    <div class="bg-emerald-50 dark:bg-slate-900/60 rounded-lg sm:rounded-xl p-2 sm:p-3 flex items-center justify-between border border-emerald-200 dark:border-emerald-500/30">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-chart-bar text-emerald-500 dark:text-emerald-400 text-[8px] hidden sm:inline-block"></i>
                                            <p class="biz-term text-[9px] sm:text-[10px] text-emerald-600 dark:text-emerald-400/80 uppercase font-bold cursor-help" data-term="revenue">Revenue </p>
                                        </div>
                                        <p id="rp-revenue" class="text-xs sm:text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">--</p>
                                    </div>
                                </div>

                                    {{-- Detail Keuangan Breakdown (V3) --}}
                                    <div class="mt-3 sm:mt-4">
                                        <button id="rgp-detail-toggle" class="w-full flex items-center justify-between text-[9px] sm:text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 hover:text-emerald-400 transition-colors cursor-pointer">
                                            <span class="flex items-center gap-1.5"><i class="fas fa-calculator text-[8px]"></i> Detail Keuangan</span>
                                            <i id="rgp-detail-icon" class="fas fa-chevron-down text-[8px] transition-transform"></i>
                                        </button>
                                        <div id="rgp-detail-panel" class="hidden animate-fade-in relative mt-2">
                                            @guest
                                            <div class="guest-only absolute inset-x-0 bottom-0 top-12 z-20 flex flex-col items-center justify-center bg-gradient-to-t from-slate-100 via-slate-100/95 to-transparent dark:from-slate-900 dark:via-slate-900/95 dark:to-transparent backdrop-blur-[2px] rounded-b-xl border border-emerald-500/30 border-t-0 p-4 text-center shadow-lg transition-all">
                                                <div class="w-10 h-10 rounded-full bg-slate-800 border border-emerald-500/50 flex items-center justify-center mb-2 shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                                    <i class="fas fa-lock text-sm text-emerald-400"></i>
                                                </div>
                                                <p class="text-slate-800 dark:text-white font-bold text-xs mb-2 drop-shadow-md">Login untuk Buka Breakdown</p>
                                                <button type="button" onclick="window.saveGuestSimulationAndLogin()" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold rounded-lg shadow-md text-[10px] sm:text-xs">
                                                    Buka Full Analisis
                                                </button>
                                            </div>
                                            <div class="guest-blur select-none pointer-events-none pb-2 space-y-1.5 opacity-80" style="mask-image: linear-gradient(to bottom, black 30%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 30%, transparent 100%);">
                                            @else
                                            <div class="space-y-1.5">
                                            @endguest
                                            
                                                {{-- Profit per Unit --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="biz-term text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold cursor-help" data-term="unit_profit">💰 Profit / Unit</span>
                                                <span id="rp-unit-profit" class="text-[10px] sm:text-xs font-black text-emerald-600 dark:text-emerald-400 tabular-nums">--</span>
                                            </div>
                                            {{-- CPA per Sales --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="biz-term text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold cursor-help" data-term="cpa">🎯 Biaya per Sales (CPA)</span>
                                                <span id="rp-cpa" class="text-[10px] sm:text-xs font-black text-yellow-600 dark:text-yellow-400 tabular-nums">--</span>
                                            </div>
                                            {{-- Gross Revenue --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold">📊 Gross Revenue</span>
                                                <span id="rp-gross-revenue" class="text-[10px] sm:text-xs font-black text-blue-600 dark:text-blue-400 tabular-nums">--</span>
                                            </div>
                                            {{-- Total Biaya --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold">📉 Total Biaya (Iklan + Tetap)</span>
                                                <span id="rp-total-costs" class="text-[10px] sm:text-xs font-black text-rose-500 tabular-nums">--</span>
                                            </div>
                                            {{-- Net Profit --}}
                                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-900/10 rounded-lg p-2 sm:p-2.5 border border-emerald-200 dark:border-emerald-500/30">
                                                <span class="biz-term text-[8px] sm:text-[9px] text-emerald-600 dark:text-emerald-400 uppercase font-bold cursor-help" data-term="net_profit">✅ Net Profit (Bersih)</span>
                                                <span id="rp-net-profit" class="text-[10px] sm:text-xs font-black text-emerald-600 dark:text-emerald-400 tabular-nums">--</span>
                                            </div>
                                            {{-- Biaya Tetap Reflected --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold">🏢 Biaya Tetap /Bulan</span>
                                                <span id="rp-fixed-display" class="text-[10px] sm:text-xs font-black text-slate-600 dark:text-slate-400 tabular-nums">--</span>
                                            </div>
                                            {{-- Modal Dibutuhkan --}}
                                            <div class="flex items-center justify-between bg-white dark:bg-slate-900/60 rounded-lg p-2 sm:p-2.5 border border-slate-200 dark:border-slate-800">
                                                <span class="biz-term text-[8px] sm:text-[9px] text-slate-500 uppercase font-bold cursor-help" data-term="capital_needed">💼 Modal Dibutuhkan</span>
                                                <span id="rp-capital-needed" class="text-[10px] sm:text-xs font-black tabular-nums">
                                                    <span id="rp-capital-needed-value">--</span>
                                                </span>
                                            </div>
                                            
                                            </div> {{-- Penutup pembungkus space-y-1.5/blur --}}
                                        </div>
                                    </div>

                                    {{-- Insight --}}
                                    <div class="mt-3 sm:mt-4 bg-indigo-500/10 border border-indigo-500/20 rounded-lg sm:rounded-xl p-3 sm:p-4">
                                        <div class="flex items-center gap-1.5 sm:gap-2 mb-1.5 sm:mb-2">
                                            <i class="fas fa-lightbulb text-indigo-400 text-[9px] sm:text-xs"></i>
                                            <p class="text-[9px] sm:text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Insight Mentor</p>
                                        </div>
                                        <p id="rp-learning-moment" class="text-[10px] sm:text-xs text-slate-400 leading-relaxed">
                                            Insight akan muncul otomatis setelah kamu isi data di atas.
                                        </p>
                                    </div>

                                    {{-- Recommendations --}}
                                    <div id="rp-recommendations-box" class="hidden mt-3 sm:mt-4">
                                        <p class="text-[9px] sm:text-[10px] text-slate-500 uppercase font-bold tracking-wider mb-2 sm:mb-3">Saran Penyesuaian</p>
                                        <div id="rp-rec-container" class="space-y-1.5 sm:space-y-2">
                                            {{-- Injected by JS --}}
                                        </div>
                                    </div>
                                </div>{{-- /Internal Scroll Wrapper --}}
                            </div>
                        </div>
                    </div>

                    {{-- ══ Scorecard (shown in rp-results, kept for JS compat) ══════ --}}
                    <div id="rp-results" class="hidden mt-12 pt-10 border-t border-slate-700/60 animate-fade-in">

                        {{-- Full Scorecard row --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 text-center">
                                <p class="text-[10px] text-slate-500 uppercase font-bold mb-2">Target Cuan</p>
                                <p id="sc-target" class="text-xl font-black text-white">Rp 0</p>
                                <p class="text-[10px] text-slate-600 mt-1">per bulan</p>
                            </div>
                            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 text-center">
                                <p class="text-[10px] text-slate-500 uppercase font-bold mb-2">Deadline</p>
                                <p id="sc-duration" class="text-xl font-black text-white">0 Hari</p>
                                <p class="text-[10px] text-slate-600 mt-1">durasi sprint</p>
                            </div>
                            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-5 text-center">
                                <p class="text-[10px] text-slate-500 uppercase font-bold mb-2">Model Bisnis</p>
                                <p id="sc-model" class="text-xl font-black text-white">-</p>
                                <p class="text-[10px] text-slate-600 mt-1">tipe usaha</p>
                            </div>
                            <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5 text-center">
                                <p class="text-[10px] text-emerald-500/70 uppercase font-bold mb-2">Harga Jual</p>
                                <p id="sc-price" class="text-xl font-black text-emerald-400">Rp 0</p>
                                <p class="text-[10px] text-emerald-600/60 mt-1">per unit</p>
                            </div>
                        </div>

                        {{-- Next Step CTA to Profit Simulator --}}
                        <div class="flex flex-col items-center justify-center mb-8 relative group w-full lg:w-3/4 mx-auto cursor-pointer" onclick="document.getElementById('tab-profit-simulator-section').click(); setTimeout(() => { document.getElementById('profit-simulator-section').scrollIntoView({behavior:'smooth', block: 'start'}); }, 100);">
                            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-cyan-500 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-500"></div>
                            <div class="relative w-full bg-slate-800/80 border border-emerald-500/20 hover:border-emerald-500/50 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 transition-all">
                                <div class="flex items-center gap-4 text-left">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-chart-line text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold text-sm sm:text-base">Tahu Targetnya, Tapi Berapa Konversinya?</h4>
                                        <p class="text-emerald-400/80 text-[11px] sm:text-xs font-medium">Lanjutkan ke Profit Simulator untuk menghitung Traffic & Konversi</p>
                                    </div>
                                </div>
                                <div class="px-5 py-2.5 bg-emerald-500 text-white rounded-xl font-bold text-xs sm:text-sm whitespace-nowrap shadow-lg shadow-emerald-500/20 group-hover:bg-emerald-400 transition-colors flex items-center gap-2">
                                    Simulasi Profit <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Logic version badge --}}
                        <div class="text-center mt-4">
                            <span class="inline-flex items-center gap-1.5 text-[10px] text-slate-600 font-mono">
                                <i class="fas fa-microchip"></i>
                                Engine: <span id="rp-logic-version">v3.0</span>
                            </span>
                        </div>
                    </div>

                </div>{{-- /p-12 --}}
            </div>{{-- /bg-slate-900 --}}

        </section>
        @endif

        {{-- Hidden inputs (backend compat) --}}
        <input type="hidden" id="rp-model" value="service">
        <input type="hidden" id="rp-strategy" value="ads">
        <form id="reverse-planner-form" style="display:none"></form>

        {{-- Why Modal --}}
        <div id="rp-why-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 animate-fade-in">
            <div class="bg-slate-800 p-6 rounded-2xl shadow-2xl max-w-sm w-full border border-slate-700">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-emerald-400 text-sm"></i>
                    </div>
                    <h4 class="font-bold text-lg text-white">Analisis Cepat</h4>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-slate-700">
                        <span class="text-slate-400">Ketersediaan Modal</span>
                        <span id="rp-why-capital" class="font-bold text-white">--</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-700">
                        <span class="text-slate-400">Ketersediaan Waktu</span>
                        <span id="rp-why-hours" class="font-bold text-white">--</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="biz-term text-slate-400 cursor-help" data-term="margin">Kesehatan Margin </span>
                        <span id="rp-why-margin" class="font-bold text-white">--</span>
                    </div>
                </div>
                <button id="rp-why-close" class="mt-5 w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded-xl font-bold transition-colors text-sm">Tutup</button>
            </div>
        </div>










        <section id="profit-simulator-section" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 rounded-3xl overflow-hidden transition-all duration-500" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);">

            {{-- Glow Orbs --}}
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-violet-500/5 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Guest Overlay (Psychological Lock) --}}
            <div class="guest-only absolute inset-0 z-50 bg-white/40 dark:bg-slate-950/60 backdrop-blur-md flex flex-col items-center justify-center overflow-hidden">
                <div class="relative bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-violet-200 dark:border-violet-900/50 text-center max-w-sm mx-4 transform transition-all hover:-translate-y-2 hover:shadow-violet-500/20 duration-500 group">
                    
                    <!-- Decorative Violet Glow -->
                    <div class="absolute inset-0 bg-gradient-to-br from-violet-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl pointer-events-none"></div>

                    <div class="relative z-10 w-16 h-16 bg-gradient-to-br from-violet-500/20 to-fuchsia-600/20 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-violet-500/30 shadow-[0_0_20px_rgba(139,92,246,0.3)] animate-pulse">
                        <i class="fas fa-lock text-3xl text-violet-500 drop-shadow-lg group-hover:scale-110 transition-transform"></i>
                    </div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tight leading-tight">Temukan Titik Bocor Bisnismu 🕵️‍♂️</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 leading-relaxed">Simulasikan cashflow, traffic, dan konversimu. Lihat berapa banyak uang yang hilang di atas meja setiap bulannya.</p>
                        
                        <button onclick="window.openLoginModal('login')" class="w-full py-3.5 px-4 bg-gradient-to-r from-violet-600 to-fuchsia-500 hover:from-violet-500 hover:to-fuchsia-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-violet-500/30 hover:shadow-violet-500/50 active:scale-[0.98] flex items-center justify-center gap-2 mb-4">
                            Mulai Simulasi Bebas <i class="fas fa-key group-hover:-translate-y-1 transition-transform"></i>
                        </button>
                        
                        <p class="text-[10px] text-slate-400 font-medium tracking-wide"><i class="fas fa-shield-alt mr-1"></i>Akses VIP Terbatas.</p>
                    </div>
                </div>
            </div>

            {{-- Gate Overlay (ID preserved for JS) --}}
            <div id="ps-gate-overlay" class="hidden absolute inset-0 z-40 flex items-center justify-center p-6 text-center backdrop-blur-sm rounded-3xl" style="background: rgba(15,23,42,0.93);">
                <div class="max-w-sm">
                    <div class="w-16 h-16 rounded-2xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-lock text-2xl text-amber-400"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-3">Selesaikan Goal Dulu</h3>
                    <p class="text-slate-400 mb-6 text-sm leading-relaxed">Target bisnis kamu masih <span class="text-amber-400 font-bold">"Terlalu Berat"</span>. Optimasi baru efektif kalau fondasi sudah realistis.</p>
                    <button onclick="document.getElementById('reverse-planner-form').scrollIntoView({behavior: 'smooth'})" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl transition-colors text-sm">
                        ← Sesuaikan Goal
                    </button>
                </div>
            </div>

            <div class="relative z-10">

                {{-- Header --}}
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest mb-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                         Scale Up Engine
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-white mb-3">
                        Area Scale Up <span class="bg-gradient-to-r from-blue-400 via-emerald-400 to-emerald-300 bg-clip-text text-transparent">Paling Gacor</span>
                    </h2>
                    <p class="text-slate-400 max-w-lg mx-auto text-sm">Pilih <span class="text-white font-semibold">satu lever</span> yang mau kamu push. Langsung lihat efeknya ke angka profit kamu.</p>
                    <div class="mt-5 flex items-center justify-center gap-3">
                        <button data-start-tour="profitSimulator" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-blue-500/40 hover:border-blue-400 text-blue-400 text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 active:scale-95">
                            <i class="fas fa-graduation-cap"></i> Mulai Tour Simulator
                        </button>
                        <button onclick="window.open('/guide/phase-2', '_blank')" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-500/10 hover:bg-violet-500/20 border border-violet-500/30 text-violet-500 dark:text-violet-400 text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                            <i class="fas fa-book-open"></i> Panduan
                        </button>
                    </div>
                </div>

                {{-- Main Layout: 2 Columns --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {{-- Left: Zone Cards (7-col) --}}
                    <div class="lg:col-span-7 flex overflow-x-auto snap-x snap-mandatory sm:grid sm:grid-cols-2 gap-4 pb-4 sm:pb-0 -mx-4 px-4 sm:mx-0 sm:px-0 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

                        {{-- ── Traffic Zone ── --}}
                        <div class="zone-card group relative rounded-2xl p-4 sm:p-5 border-2 border-slate-700/60 bg-slate-800/40 backdrop-blur-sm hover:border-blue-500/70 cursor-pointer transition-all duration-300 ring-offset-slate-900 w-[85vw] max-w-[320px] sm:w-auto sm:max-w-none shrink-0 snap-center" data-zone="traffic">
                            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" style="background: radial-gradient(ellipse at top left, rgba(59,130,246,0.08) 0%, transparent 70%)"></div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                                        <i class="fas fa-users text-lg text-blue-400"></i>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-wider">Scale Up</span>
                                </div>
                                <h3 class="biz-term text-base font-black text-white mb-1 cursor-help w-max" data-term="traffic">Trafik </h3>
                                <p class="text-xs text-slate-400">Datangkan lebih banyak calon pembeli</p>
                                <div class="flex items-center gap-1.5 mt-3 mb-1">
                                    <i class="fas fa-bolt text-[10px] text-blue-400"></i>
                                    <span class="text-[10px] text-slate-400">Potensi uplift: <span class="text-blue-300 font-bold">+10% → +35%</span></span>
                                </div>

                                {{-- Level Selector (class preserved for JS) --}}
                                <div class="level-selector hidden mt-4 space-y-2 animate-fade-in">
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Pilih Tingkat Upaya:</p>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-blue-500 hover:bg-blue-500/10 transition-all text-left" data-level="1">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 text-[10px] font-black flex items-center justify-center shrink-0">L1</span>
                                            <span class="text-xs font-semibold text-slate-300">Organik</span>
                                        </div>
                                        <span class="text-[10px] font-black text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full shrink-0">+10%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-blue-500 hover:bg-blue-500/10 transition-all text-left" data-level="2">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 text-[10px] font-black flex items-center justify-center shrink-0">L2</span>
                                            <span class="text-xs font-semibold text-slate-300">Kampanye Iklan</span>
                                        </div>
                                        <span class="text-[10px] font-black text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full shrink-0">+20%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-blue-500 hover:bg-blue-500/10 transition-all text-left" data-level="3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-300 text-[10px] font-black flex items-center justify-center shrink-0">L3</span>
                                            <span class="text-xs font-semibold text-slate-300">Agresif</span>
                                        </div>
                                        <span class="text-[10px] font-black text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-full shrink-0">+35%</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ── Conversion Zone ── --}}
                        <div class="zone-card group relative rounded-2xl p-4 sm:p-5 border-2 border-slate-700/60 bg-slate-800/40 backdrop-blur-sm hover:border-emerald-500/70 cursor-pointer transition-all duration-300 ring-offset-slate-900 w-[85vw] max-w-[320px] sm:w-auto sm:max-w-none shrink-0 snap-center" data-zone="conversion">
                            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" style="background: radial-gradient(ellipse at top left, rgba(16,185,129,0.08) 0%, transparent 70%)"></div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                                        <i class="fas fa-magic text-lg text-emerald-400"></i>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider">Optimasi</span>
                                </div>
                                <h3 class="biz-term text-base font-black text-white mb-1 cursor-help w-max" data-term="conversion_rate">Konversi </h3>
                                <p class="text-xs text-slate-400">Tingkatkan efektivitas penjualan</p>
                                <div class="flex items-center gap-1.5 mt-3 mb-1">
                                    <i class="fas fa-bolt text-[10px] text-emerald-400"></i>
                                    <span class="text-[10px] text-slate-400">Potensi uplift: <span class="text-emerald-300 font-bold">+5% → +20%</span></span>
                                </div>

                                <div class="level-selector hidden mt-4 space-y-2 animate-fade-in">
                                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-2">Pilih Tingkat Upaya:</p>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all text-left" data-level="1">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-black flex items-center justify-center shrink-0">L1</span>
                                            <span class="text-xs font-semibold text-slate-300">Optimasi UI</span>
                                        </div>
                                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full shrink-0">+5%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all text-left" data-level="2">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-black flex items-center justify-center shrink-0">L2</span>
                                            <span class="text-xs font-semibold text-slate-300">Copywriting</span>
                                        </div>
                                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full shrink-0">+10%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-emerald-500 hover:bg-emerald-500/10 transition-all text-left" data-level="3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-[10px] font-black flex items-center justify-center shrink-0">L3</span>
                                            <span class="text-xs font-semibold text-slate-300">Sinkronisasi Funnel</span>
                                        </div>
                                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full shrink-0">+20%</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ── Pricing Zone ── --}}
                        <div class="zone-card group relative rounded-2xl p-4 sm:p-5 border-2 border-slate-700/60 bg-slate-800/40 backdrop-blur-sm hover:border-amber-500/70 cursor-pointer transition-all duration-300 ring-offset-slate-900 w-[85vw] max-w-[320px] sm:w-auto sm:max-w-none shrink-0 snap-center" data-zone="pricing">
                            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" style="background: radial-gradient(ellipse at top left, rgba(245,158,11,0.08) 0%, transparent 70%)"></div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/20 transition-colors">
                                        <i class="fas fa-tag text-lg text-amber-400"></i>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-wider">Nilai (Value)</span>
                                </div>
                                <h3 class="text-base font-black text-white mb-1">Harga (Pricing)</h3>
                                <p class="text-xs text-slate-400">Naikkan nilai jual produk kamu</p>
                                <div class="flex items-center gap-1.5 mt-3 mb-1">
                                    <i class="fas fa-bolt text-[10px] text-amber-400"></i>
                                    <span class="text-[10px] text-slate-400">Potensi uplift: <span class="text-amber-300 font-bold">+3% → +12%</span></span>
                                </div>

                                <div class="level-selector hidden mt-4 space-y-2 animate-fade-in">
                                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-2">Pilih Tingkat Upaya:</p>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-amber-500 hover:bg-amber-500/10 transition-all text-left" data-level="1">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-black flex items-center justify-center shrink-0">L1</span>
                                            <span class="text-xs font-semibold text-slate-300">Add-on</span>
                                        </div>
                                        <span class="text-[10px] font-black text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full shrink-0">+3%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-amber-500 hover:bg-amber-500/10 transition-all text-left" data-level="2">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-black flex items-center justify-center shrink-0">L2</span>
                                            <span class="text-xs font-semibold text-slate-300">Premium Positioning</span>
                                        </div>
                                        <span class="text-[10px] font-black text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full shrink-0">+7%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-amber-500 hover:bg-amber-500/10 transition-all text-left" data-level="3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-black flex items-center justify-center shrink-0">L3</span>
                                            <span class="text-xs font-semibold text-slate-300">Elite Positioning</span>
                                        </div>
                                        <span class="text-[10px] font-black text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-full shrink-0">+12%</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ── Cost Zone ── --}}
                        <div class="zone-card group relative rounded-2xl p-4 sm:p-5 border-2 border-slate-700/60 bg-slate-800/40 backdrop-blur-sm hover:border-rose-500/70 cursor-pointer transition-all duration-300 ring-offset-slate-900 w-[85vw] max-w-[320px] sm:w-auto sm:max-w-none shrink-0 snap-center" data-zone="cost">
                            <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none" style="background: radial-gradient(ellipse at top left, rgba(244,63,94,0.08) 0%, transparent 70%)"></div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-start mb-5">
                                    <div class="w-11 h-11 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center group-hover:bg-rose-500/20 transition-colors">
                                        <i class="fas fa-scissors text-lg text-rose-400"></i>
                                    </div>
                                    <span class="px-2 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-black uppercase tracking-wider">Efisiensi</span>
                                </div>
                                <h3 class="text-base font-black text-white mb-1">Cost (Biaya)</h3>
                                <p class="text-xs text-slate-400">Pangkas biaya, tebelin margin</p>
                                <div class="flex items-center gap-1.5 mt-3 mb-1">
                                    <i class="fas fa-bolt text-[10px] text-rose-400"></i>
                                    <span class="text-[10px] text-slate-400">Potensi saving: <span class="text-rose-300 font-bold">-5% → -15%</span></span>
                                </div>

                                <div class="level-selector hidden mt-4 space-y-2 animate-fade-in">
                                    <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2">Pilih Tingkat Upaya:</p>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-rose-500 hover:bg-rose-500/10 transition-all text-left" data-level="1">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[10px] font-black flex items-center justify-center shrink-0">L1</span>
                                            <span class="text-xs font-semibold text-slate-300">Re-sourcing</span>
                                        </div>
                                        <span class="text-[10px] font-black text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full shrink-0">-5%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-rose-500 hover:bg-rose-500/10 transition-all text-left" data-level="2">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[10px] font-black flex items-center justify-center shrink-0">L2</span>
                                            <span class="text-xs font-semibold text-slate-300">Operasional Ramping</span>
                                        </div>
                                        <span class="text-[10px] font-black text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full shrink-0">-10%</span>
                                    </button>
                                    <button class="level-btn w-full flex items-center justify-between p-3 rounded-xl bg-slate-900/80 border border-slate-700 hover:border-rose-500 hover:bg-rose-500/10 transition-all text-left" data-level="3">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[10px] font-black flex items-center justify-center shrink-0">L3</span>
                                            <span class="text-xs font-semibold text-slate-300">Outsource</span>
                                        </div>
                                        <span class="text-[10px] font-black text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full shrink-0">-15%</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right: Result Panel (5-col) --}}
                    <div class="lg:col-span-5 lg:sticky lg:top-8 space-y-4">

                        {{-- Default State (ID preserved for JS) --}}
                        <div id="ps-default-state" class="rounded-2xl p-6 sm:p-10 border-2 border-dashed border-slate-700/60 bg-slate-800/20 flex flex-col items-center justify-center text-center min-h-[220px] sm:min-h-[340px]">
                            <div class="w-14 h-14 rounded-2xl bg-slate-700/50 border border-slate-600/50 flex items-center justify-center mb-5">
                                <i class="fas fa-hand-pointer text-xl text-slate-500"></i>
                            </div>
                            <p class="text-slate-300 font-bold mb-1">← Pilih satu zona</p>
                            <p class="text-slate-500 text-sm">Klik card di kiri untuk lihat<br>potensi profit kamu secara real-time</p>
                            <div class="flex gap-2 mt-6">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-500/50 animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/50 animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500/50 animate-bounce" style="animation-delay:300ms"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500/50 animate-bounce" style="animation-delay:450ms"></span>
                            </div>
                        </div>

                        {{-- Result Card (ID preserved for JS) --}}
                        <div id="simulation-result" class="hidden rounded-2xl overflow-hidden border border-slate-700/60 relative animate-fade-in" style="background: linear-gradient(145deg, #0a0f1e 0%, #111827 100%);">

                            {{-- Ambient glows --}}
                            <div class="absolute top-0 right-0 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl -mr-28 -mt-28 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl -ml-20 -mb-20 pointer-events-none"></div>

                            <div class="relative z-10">

                                {{-- ── Section 1: Main Profit Hero ── --}}
                                <div class="p-4 sm:p-5 border-b border-white/5">
                                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">📊</span>
                                            <span class="text-[10px] sm:text-xs font-black tracking-wide text-slate-300">Hasil Simulasi Kamu</span>
                                        </div>
                                        {{-- Delta Badge – ID needed by JS --}}
                                        <span id="profit-delta-display" class="text-[10px] sm:text-xs font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full">+Rp 0 vs Goal</span>
                                    </div>

                                    {{-- Profit Value --}}
                                    <h3 id="ps-profit-range" class="text-2xl sm:text-3xl font-black text-white tracking-tight mb-1">Rp —</h3>
                                    <p id="ps-insight" class="text-[10px] sm:text-xs text-slate-400 italic leading-relaxed">Pilih zone &amp; level untuk melihat potensi cuan.</p>
                                    {{-- Auto-interpretation (populated by JS) --}}
                                    <div id="ps-interpretation" class="hidden mt-3 px-3 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                                        <p id="ps-interpretation-text" class="text-[11px] sm:text-xs text-emerald-300 font-medium leading-relaxed"></p>
                                    </div>
                                </div>

                                {{-- ── Section 2: Effort Level Visual ── --}}
                                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-white/5">
                                    <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mb-2 sm:mb-3">Tingkat Upaya &amp; Risiko</p>
                                    <div class="flex items-center gap-3">
                                        {{-- Effort Bars (dynamic via JS) --}}
                                        <div class="flex gap-0.5 sm:gap-1 items-end" id="ps-effort-bars">
                                            <div class="w-1.5 sm:w-2 rounded-full bg-slate-700" style="height:10px"></div>
                                            <div class="w-1.5 sm:w-2 rounded-full bg-slate-700" style="height:16px"></div>
                                            <div class="w-1.5 sm:w-2 rounded-full bg-slate-700" style="height:22px"></div>
                                        </div>
                                        <div>
                                            <span id="ps-effort" class="text-xs sm:text-sm font-black text-white">—</span>
                                            <span class="text-slate-600 text-[10px] sm:text-xs mx-1 sm:mx-1.5">·</span>
                                            <span id="ps-risk" class="text-xs sm:text-sm font-black">—</span>
                                        </div>
                                    </div>
                                </div>

                                @guest
                                <div class="relative group mt-4 mb-2">
                                    <!-- The Gembok Overlay -->
                                    <div class="guest-only absolute inset-x-0 bottom-0 top-14 z-20 flex flex-col items-center justify-center bg-gradient-to-t from-slate-900 via-slate-900/95 to-transparent backdrop-blur-[2px] rounded-b-xl border border-emerald-500/30 border-t-0 p-6 text-center shadow-2xl transition-all">
                                        <div class="w-14 h-14 rounded-full bg-slate-800 border border-emerald-500/50 flex items-center justify-center mb-3 shadow-[0_0_30px_rgba(16,185,129,0.3)]">
                                            <i class="fas fa-lock text-xl text-emerald-400"></i>
                                        </div>
                                        <h4 class="text-white font-black text-lg mb-2 drop-shadow-md">Simulasi Sukses! 🎉</h4>
                                        <p class="text-slate-200 text-xs mb-5 max-w-[250px] leading-relaxed mx-auto drop-shadow-sm font-medium">Login gratis untuk melihat breakdown lengkap (traffic, konversi, biaya iklan, margin) dan simpan strategi.</p>
                                        <button onclick="window.saveGuestSimulationAndLogin()" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center gap-2 transform active:scale-95 text-sm mx-auto">
                                            <i class="fas fa-key"></i> Buka Full Analisis
                                        </button>
                                    </div>
                                    <div class="guest-blur select-none pointer-events-none pb-4 opacity-80" style="mask-image: linear-gradient(to bottom, black 30%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 30%, transparent 100%);">
                                @endguest

                                {{-- ── Section 3: Funnel Breakdown ── --}}
                                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-white/5 overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                                    <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mb-2 sm:mb-3">Breakdown Simulasi</p>
                                    <div class="space-y-1.5 sm:space-y-2 min-w-[280px]" id="ps-breakdown-rows">

                                        {{-- Traffic Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-blue-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-users text-[8px] sm:text-[9px] text-blue-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Traffic</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-traffic">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-traffic">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded-full" id="ps-traffic-delta">—</span>
                                        </div>

                                        {{-- Conversion Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-emerald-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-magic text-[8px] sm:text-[9px] text-emerald-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Konversi</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-conv">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-conv">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-full" id="ps-conv-delta">—</span>
                                        </div>

                                        {{-- Price Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-amber-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-tag text-[8px] sm:text-[9px] text-amber-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Harga Jual</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-price">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-price">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded-full" id="ps-price-delta">—</span>
                                        </div>

                                        {{-- Profit Result Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-2 sm:py-2.5 px-2.5 sm:px-3 rounded-lg border mt-1" style="background: rgba(16,185,129,0.06); border-color: rgba(16,185,129,0.15);">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-emerald-500/20 flex items-center justify-center shrink-0">
                                                <i class="fas fa-chart-line text-[8px] sm:text-[9px] text-emerald-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-emerald-300 flex-1">Profit Baru</span>
                                            <span class="text-[10px] sm:text-xs text-slate-500 line-through" id="ps-base-profit">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-emerald-600"></i>
                                            <span class="text-xs sm:text-sm font-black text-emerald-300 px-1" id="ps-new-profit">—</span>
                                            <span id="ps-net-margin" class="text-[9px] sm:text-[10px] font-black text-slate-600 bg-slate-800 px-1.5 py-0.5 rounded-full">—</span>
                                        </div>
                                    </div>

                                    {{-- V3: Detail Simulasi Breakdown --}}
                                    <div class="mt-2 space-y-1.5 sm:space-y-2 min-w-[280px]">
                                        <p class="text-[9px] sm:text-[10px] text-slate-500 font-black uppercase tracking-widest mb-1">Detail Simulasi</p>

                                        {{-- Sales Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-violet-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-shopping-cart text-[8px] sm:text-[9px] text-violet-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Total Sales</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-sales">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-sales">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-violet-400 bg-violet-500/10 px-1.5 py-0.5 rounded-full" id="ps-sales-delta">—</span>
                                        </div>

                                        {{-- Revenue Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-cyan-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-chart-bar text-[8px] sm:text-[9px] text-cyan-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Revenue</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-revenue">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-revenue">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-cyan-400 bg-cyan-500/10 px-1.5 py-0.5 rounded-full" id="ps-revenue-delta">—</span>
                                        </div>

                                        {{-- Total Biaya Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-rose-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-receipt text-[8px] sm:text-[9px] text-rose-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Total Biaya</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-cost">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-cost">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded-full" id="ps-cost-delta">—</span>
                                        </div>

                                        {{-- Ad Spend Row --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-1.5 sm:py-2 px-2.5 sm:px-3 rounded-lg bg-white/[0.03] border border-white/5">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-yellow-500/15 flex items-center justify-center shrink-0">
                                                <i class="fas fa-bullhorn text-[8px] sm:text-[9px] text-yellow-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs text-slate-400 flex-1">Budget Iklan</span>
                                            <span class="text-[10px] sm:text-xs text-slate-600 line-through" id="ps-base-adspend">—</span>
                                            <i class="fas fa-arrow-right text-[8px] sm:text-[9px] text-slate-600"></i>
                                            <span class="text-[10px] sm:text-xs font-bold text-white px-1" id="ps-new-adspend">—</span>
                                            <span class="text-[9px] sm:text-[10px] font-black text-yellow-400 bg-yellow-500/10 px-1.5 py-0.5 rounded-full" id="ps-adspend-delta">—</span>
                                        </div>

                                        {{-- ROI Indicator --}}
                                        <div class="flex items-center gap-1.5 sm:gap-2 py-2 sm:py-2.5 px-2.5 sm:px-3 rounded-lg border mt-1" style="background: rgba(99,102,241,0.06); border-color: rgba(99,102,241,0.15);">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-md sm:rounded-lg bg-indigo-500/20 flex items-center justify-center shrink-0">
                                                <i class="fas fa-bullseye text-[8px] sm:text-[9px] text-indigo-400"></i>
                                            </div>
                                            <span class="text-[10px] sm:text-xs font-bold text-indigo-300 flex-1">ROI (Return on Investment)</span>
                                            <span id="ps-roi-value" class="text-xs sm:text-sm font-black text-indigo-400">—</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Section 4: Reflection ── --}}
                                <div class="px-4 sm:px-5 py-3 sm:py-4 border-b border-white/5">
                                    <div class="flex items-start gap-2 sm:gap-2.5">
                                        <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-violet-500/15 border border-violet-500/20 flex items-center justify-center shrink-0 mt-0.5">
                                            <i class="fas fa-brain text-[9px] sm:text-[10px] text-violet-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] sm:text-[10px] font-black text-violet-400 uppercase tracking-wider mb-0.5 sm:mb-1">Reality Check</p>
                                            <p id="ps-reflection" class="text-[10px] sm:text-xs text-slate-300 leading-relaxed">Udah siap eksekusi belum?</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── Section 5: Save CTA ── --}}
                                <div class="p-4 sm:p-5">
                                    <div id="save-strategy-wrapper">
                                        <button id="apply-strategy-btn" data-auth-required="true" class="w-full py-2.5 sm:py-3 px-4 rounded-xl font-black text-slate-900 bg-gradient-to-r from-emerald-400 to-emerald-500 hover:from-emerald-300 hover:to-emerald-400 transition-all flex items-center justify-center gap-2 text-xs sm:text-sm shadow-lg shadow-emerald-500/20">
                                            <i class="fas fa-layer-group"></i>
                                            Simpan Blueprint Ini
                                        </button>
                                        <div id="blueprint-saved-info" class="hidden w-full flex items-center justify-center gap-2 py-2 sm:py-3 px-3 sm:px-4 bg-emerald-500/15 border border-emerald-500/25 rounded-xl mt-2">
                                            <i class="fas fa-check-circle text-emerald-400 text-sm"></i>
                                            <span class="text-emerald-300 font-bold text-xs sm:text-sm" id="blueprint-saved-label">Blueprint sudah tersimpan</span>
                                        </div>
                                    </div>
                                </div>

                                @guest
                                    </div> <!-- end blurred content -->
                                </div> <!-- end relative group -->
                                @endguest

                                {{-- ── Section 6: Product Arsenal CTA ── --}}
                                <div class="px-4 pb-4 sm:px-5 sm:pb-5">
                                    <div class="relative w-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border border-slate-200 dark:border-blue-500/30 dark:border-dashed hover:border-blue-500/60 rounded-2xl p-4 sm:p-5 flex flex-col items-center justify-center text-center transition-all group overflow-hidden shadow-sm dark:shadow-none">
                                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-100 to-violet-100 dark:from-blue-600 dark:to-violet-500 rounded-2xl blur opacity-70 dark:opacity-10 group-hover:opacity-100 dark:group-hover:opacity-20 transition duration-500"></div>
                                        
                                        <p class="text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 font-medium mb-1 relative z-10">Profit Potential: <span id="ps-cta-profit-value" class="text-emerald-500 dark:text-emerald-400 font-bold tracking-wide">Rp 0</span> / bulan</p>
                                        <h4 class="text-slate-900 dark:text-white font-black text-lg sm:text-xl mb-4 relative z-10 leading-tight">Tapi kamu belum punya produk?</h4>
                                        <button onclick="if(typeof switchDesktopNavTab === 'function') { switchDesktopNavTab('product', event); } setTimeout(() => { document.getElementById('other-products').scrollIntoView({behavior:'smooth', block: 'start'}); }, 100);" class="w-full relative z-10 py-3 sm:py-3.5 px-4 bg-gradient-to-r from-blue-500 to-violet-500 hover:from-blue-400 hover:to-violet-400 text-white font-black rounded-xl transition-all shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 active:scale-[0.98] flex items-center justify-center gap-2">
                                            Lihat Produk Siap Jual <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PROBLEM TRIGGER SECTION (High-Urgency Pain Point)             -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div class="guest-only w-full px-4 sm:px-6 lg:px-8 mt-2 mb-10 relative z-10 transition-all duration-500">
            <div class="max-w-2xl mx-auto rounded-3xl overflow-hidden relative group transform hover:-translate-y-1 transition-transform duration-300">
                
                {{-- Border Glow --}}
                <div class="absolute inset-0 bg-gradient-to-r from-rose-600 via-orange-500 to-rose-600 opacity-60 group-hover:opacity-100 transition-opacity duration-500 blur-sm"></div>
                
                <div class="relative bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-rose-500/50 p-6 sm:p-8 rounded-3xl shadow-[0_10px_40px_rgba(225,29,72,0.15)] flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start">
                    
                    {{-- Alert Icon --}}
                    <div class="hidden md:flex relative shrink-0">
                        <div class="absolute inset-0 bg-rose-500/20 rounded-full blur-xl animate-pulse"></div>
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-100 to-rose-50 dark:from-rose-900/40 dark:to-rose-800/20 border border-rose-200 dark:border-rose-500/30 flex items-center justify-center relative z-10 shadow-inner">
                            <i class="fas fa-exclamation-triangle text-3xl text-rose-600 dark:text-rose-500 drop-shadow-md"></i>
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="flex-1 text-center md:text-left">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-full mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                            <span class="text-[9px] sm:text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">Realita Pahit Pemula</span>
                        </div>
                        
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight mb-2 tracking-tight">
                            Berhenti Bakar Modal<br class="hidden sm:block"> Karena <span class="relative inline-block"><span class="absolute -inset-1 bg-rose-500/20 blur-sm rounded-lg"></span><span class="relative text-rose-600 dark:text-rose-400">Salah Hitung!</span></span>
                        </h3>
                        
                        <p class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-400 font-medium mb-4 leading-relaxed">
                            90% bisnis mati di tahun pertama bukan karena produknya jelek, tapi karena **margin boncos**, **biaya siluman**, dan **target halu**.
                        </p>
                        
                        <ul class="space-y-2 mb-6 text-left inline-block md:block">
                            <li class="flex items-start gap-2.5 text-[10px] sm:text-xs text-slate-700 dark:text-slate-300">
                                <span class="w-4 h-4 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0 mt-0.5"><i class="fas fa-times text-rose-500 text-[8px]"></i></span>
                                <span>Harga kemurahan — <i>Lebih capek packing daripada untungnya.</i></span>
                            </li>
                            <li class="flex items-start gap-2.5 text-[10px] sm:text-xs text-slate-700 dark:text-slate-300">
                                <span class="w-4 h-4 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0 mt-0.5"><i class="fas fa-times text-rose-500 text-[8px]"></i></span>
                                <span>Lupa ngitung ongkos operasional harian.</span>
                            </li>
                            <li class="flex items-start gap-2.5 text-[10px] sm:text-xs text-slate-700 dark:text-slate-300">
                                <span class="w-4 h-4 rounded-full bg-rose-100 dark:bg-rose-900/50 flex items-center justify-center shrink-0 mt-0.5"><i class="fas fa-times text-rose-500 text-[8px]"></i></span>
                                <span>Jualan terus tapi kasir selalu kosong.</span>
                            </li>
                        </ul>
                        
                        <button onclick="document.getElementById('rgp-section') ? document.getElementById('rgp-section').scrollIntoView({behavior:'smooth'}) : document.querySelector('[data-section]') ? document.querySelector('[data-section]').scrollIntoView({behavior:'smooth'}) : window.openLoginModal('login')"
                                class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-rose-600 to-orange-500 hover:from-rose-500 hover:to-orange-400 text-white font-black rounded-xl text-xs sm:text-sm tracking-wide transition-all shadow-lg shadow-rose-500/30 hover:shadow-rose-500/50 active:scale-95 flex items-center justify-center gap-2 group/btn relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/20 w-1/4 -skew-x-12 -translate-x-full group-hover/btn:translate-x-[400%] transition-transform duration-700 ease-in-out"></div>
                            <i class="fas fa-heartbeat animate-pulse"></i>
                            Selamatkan Bisnis Saya — Gratis
                            <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <!-- BUSINESS SIMULATION LAB SECTION -->
    <section id="business-simulation-lab" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 rounded-3xl overflow-hidden transition-all duration-500 mb-20 bg-slate-50 dark:bg-gradient-to-br dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-none">
        {{-- Glow Orbs --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-emerald-500/5 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-violet-500/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 w-full">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-widest mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse"></span>
                    Validasi Ide Bisnis
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-3">
                    Business Mentor Lab <span class="bg-gradient-to-r from-blue-500 via-emerald-500 to-emerald-400 dark:from-blue-400 dark:via-emerald-400 dark:to-emerald-300 bg-clip-text text-transparent">Beta</span>
                </h2>
                <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                    Uji potensi bisnis kamu sebelum bakar duit. Optimasi yang udah jalan atau plan bisnis baru pake data real.
                </p>
                <div class="mt-5 flex items-center justify-center gap-3">
                    <button data-start-tour="mentorLab" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-emerald-500/40 hover:border-emerald-500/50 dark:hover:border-emerald-400 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-xl transition-all shadow-sm dark:shadow-lg dark:shadow-emerald-500/10 hover:shadow-md dark:hover:shadow-emerald-500/20 active:scale-95">
                        <i class="fas fa-graduation-cap"></i> Mulai Tour Mentor
                    </button>
                    <button onclick="window.open('/guide/phase-3', '_blank')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                        <i class="fas fa-book-open"></i> Panduan
                    </button>
                </div>
            </div>


            <!-- Main Lab Container -->
            <div class="flex flex-col items-center gap-8 relative rounded-3xl overflow-hidden p-1">
                
                <!-- Input Panel -->
                <!-- Input Panel -->
                <!-- Interactive Strategic Advisor Wizard -->
                <div id="mentor-input-panel" class="transition-all duration-500 w-full max-w-3xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/50 rounded-3xl p-4 md:p-8 shadow-2xl shadow-indigo-500/5 dark:shadow-indigo-500/10 flex flex-col min-h-[600px] relative overflow-hidden group/lab">
                    <!-- Tech Grid Background -->
                    <div class="absolute inset-0 opacity-[0.05] dark:opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(circle at 2px 2px, currentColor 1px, transparent 0); background-size: 24px 24px;"></div>
                    
                    <!-- Wizard Header -->
                    <div class="relative z-10 flex items-center justify-between mb-6 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <h3 class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-microchip"></i> Lab Simulator
                        </h3>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span id="wizard-step-indicator" class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Step 1/4</span>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="relative z-10 w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full mb-8 overflow-hidden shadow-inner">
                        <div id="wizard-progress-bar" class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 transition-all duration-700 ease-out relative rounded-full shadow-[0_0_10px_rgba(52,211,153,0.5)]" style="width: 0%">
                            <div class="absolute inset-0 bg-white/30" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.4) 10px, rgba(255,255,255,0.4) 20px); animation: moveStripes 1s linear infinite;"></div>
                        </div>
                    </div>

                    <!-- Wizard Container -->
                    <div id="mentor-wizard-container" class="flex-1 relative">
                        
                        <!-- STEPS CONTAINER -->
                        <div class="p-4 md:p-8 min-h-[300px]">

                            <!-- STEP 1: BUSINESS PROFILE -->
                            <div class="wizard-step" data-step="1">
                                <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-8 leading-tight drop-shadow-md">Profil <span class="text-emerald-500 dark:text-emerald-400">Bisnis Kamu.</span></h4>
                                
                                <div class="mb-8">
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-4 cursor-help w-max" data-term="mentor_business_type">Model Bisnis / Sektor</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="wizard-card-select relative overflow-hidden p-5 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-emerald-500 hover:-translate-y-1 transition-all duration-300 transform text-center group active bg-slate-50 dark:bg-slate-800" data-group="businessType" data-value="general">
                                            <!-- Massive Glow on Active -->
                                            <div class="absolute inset-0 bg-emerald-500/10 dark:bg-emerald-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-xl"></div>
                                            <!-- Core Gradient -->
                                            <div class="absolute inset-0 bg-gradient-to-b from-slate-200/50 dark:from-slate-700/50 to-transparent opacity-0 group-hover:opacity-100 group-[.active]:from-emerald-100 dark:group-[.active]:from-emerald-900/40 transition-opacity"></div>
                                            
                                            <div class="relative z-10 w-16 h-16 mx-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 group-hover:border-emerald-500/50 group-[.active]:bg-emerald-500 group-[.active]:text-white group-[.active]:border-emerald-400 transition-all mb-4 shadow-sm dark:shadow-lg group-[.active]:shadow-emerald-500/50">
                                                <i class="fas fa-store text-2xl"></i>
                                            </div>
                                            <span class="relative z-10 text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white group-[.active]:text-emerald-700 dark:group-[.active]:text-white transition-colors">Fisik / Retail</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden p-5 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-blue-500 hover:-translate-y-1 transition-all duration-300 transform text-center group bg-slate-50 dark:bg-slate-800" data-group="businessType" data-value="digital">
                                            <div class="absolute inset-0 bg-blue-500/10 dark:bg-blue-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-xl"></div>
                                            <div class="absolute inset-0 bg-gradient-to-b from-slate-200/50 dark:from-slate-700/50 to-transparent opacity-0 group-hover:opacity-100 group-[.active]:from-blue-100 dark:group-[.active]:from-blue-900/40 transition-opacity"></div>
                                            
                                            <div class="relative z-10 w-16 h-16 mx-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 group-hover:border-blue-500/50 group-[.active]:bg-blue-500 group-[.active]:text-white group-[.active]:border-blue-400 transition-all mb-4 shadow-sm dark:shadow-lg group-[.active]:shadow-blue-500/50">
                                                <i class="fas fa-wifi text-2xl"></i>
                                            </div>
                                            <span class="relative z-10 text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white group-[.active]:text-blue-700 dark:group-[.active]:text-white transition-colors">Digital / Jasa</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-4">Objektif Dominan</label>
                                    <div class="space-y-4">
                                        <div class="wizard-card-select relative overflow-hidden px-5 py-4 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-emerald-500 hover:-translate-x-1 transition-all duration-300 flex items-center gap-5 group active bg-slate-50 dark:bg-slate-800" data-group="riskIntent" data-value="stable_income">
                                            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-transparent opacity-0 group-[.active]:opacity-100 transition-opacity"></div>
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 scale-y-0 group-[.active]:scale-y-100 transition-transform origin-top"></div>
                                            
                                            <div class="relative z-10 w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 group-hover:text-emerald-500 dark:group-hover:text-emerald-400 group-hover:border-emerald-500/50 group-[.active]:bg-emerald-500 group-[.active]:text-white group-[.active]:border-emerald-400 transition-all shadow-sm dark:shadow-inner group-[.active]:shadow-emerald-500/40"><i class="fas fa-shield-alt text-xl"></i></div>
                                            <div class="relative z-10 text-left flex-1">
                                                <h5 class="text-sm font-black text-slate-600 dark:text-slate-300 group-hover:text-emerald-700 dark:group-hover:text-white group-[.active]:text-emerald-700 dark:group-[.active]:text-white transition-colors">Stable Income</h5>
                                                <p class="text-[11px] text-slate-500 mt-1">Cashflow positif, risiko operasional rendah.</p>
                                            </div>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden px-5 py-4 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-amber-500 hover:-translate-x-1 transition-all duration-300 flex items-center gap-5 group bg-slate-50 dark:bg-slate-800" data-group="riskIntent" data-value="scale_fast">
                                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-transparent opacity-0 group-[.active]:opacity-100 transition-opacity"></div>
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-500 scale-y-0 group-[.active]:scale-y-100 transition-transform origin-top"></div>
                                            
                                            <div class="relative z-10 w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 group-hover:text-amber-500 dark:group-hover:text-amber-400 group-hover:border-amber-500/50 group-[.active]:bg-amber-500 group-[.active]:text-white group-[.active]:border-amber-400 transition-all shadow-sm dark:shadow-inner group-[.active]:shadow-amber-500/40"><i class="fas fa-rocket text-xl"></i></div>
                                            <div class="relative z-10 text-left flex-1">
                                                <h5 class="text-sm font-black text-slate-600 dark:text-slate-300 group-hover:text-amber-700 dark:group-hover:text-white group-[.active]:text-amber-700 dark:group-[.active]:text-white transition-colors">Scale Fast</h5>
                                                <p class="text-[11px] text-slate-500 mt-1">Kejar omzet agresif, ekspansi cepat.</p>
                                            </div>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden px-5 py-4 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer hover:border-purple-500 hover:-translate-x-1 transition-all duration-300 flex items-center gap-5 group bg-slate-50 dark:bg-slate-800" data-group="riskIntent" data-value="market_dominance">
                                            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-transparent opacity-0 group-[.active]:opacity-100 transition-opacity"></div>
                                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-purple-500 scale-y-0 group-[.active]:scale-y-100 transition-transform origin-top"></div>
                                            
                                            <div class="relative z-10 w-12 h-12 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500 group-hover:text-purple-500 dark:group-hover:text-purple-400 group-hover:border-purple-500/50 group-[.active]:bg-purple-500 group-[.active]:text-white group-[.active]:border-purple-400 transition-all shadow-sm dark:shadow-inner group-[.active]:shadow-purple-500/40"><i class="fas fa-crown text-xl"></i></div>
                                            <div class="relative z-10 text-left flex-1">
                                                <h5 class="text-sm font-black text-slate-600 dark:text-slate-300 group-hover:text-purple-700 dark:group-hover:text-white group-[.active]:text-purple-700 dark:group-[.active]:text-white transition-colors">Domination</h5>
                                                <p class="text-[11px] text-slate-500 mt-1">High risk high return, bakar uang oke.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: OPERATIONAL REALITY -->
                            <div class="wizard-step hidden" data-step="2">
                                <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-8 leading-tight drop-shadow-md">Dasar <span class="text-blue-500 dark:text-blue-400">Operasional.</span></h4>
                                
                                <div class="mb-8 bg-slate-50 dark:bg-slate-800/40 p-6 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative shadow-sm dark:shadow-[0_8px_30px_rgba(0,0,0,0.12)] backdrop-blur-md overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent rounded-2xl pointer-events-none"></div>
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 to-indigo-500 rounded-l-2xl"></div>
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[0.15em] mb-4 relative z-10 flex items-center gap-2 cursor-help w-max" data-term="mentor_initial_capital"><i class="fas fa-wallet text-blue-500 dark:text-blue-400 text-xs"></i> Modal Awal</label>
                                    <div class="grid grid-cols-2 gap-4 relative z-10 w-full">
                                        <div class="wizard-card-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black active group backdrop-blur-sm" data-group="capital" data-value="under_5">
                                            <div class="absolute inset-0 bg-blue-500/10 dark:bg-blue-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500/0 group:hover:via-blue-500/50 group-[.active]:via-blue-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-300 group-[.active]:text-blue-600 dark:group-[.active]:text-blue-300 transition-colors drop-shadow">&lt; 5 Juta</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="capital" data-value="5_20">
                                            <div class="absolute inset-0 bg-blue-500/10 dark:bg-blue-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500/0 group:hover:via-blue-500/50 group-[.active]:via-blue-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-300 group-[.active]:text-blue-600 dark:group-[.active]:text-blue-300 transition-colors drop-shadow">5 - 20 Jt</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="capital" data-value="20_100">
                                            <div class="absolute inset-0 bg-blue-500/10 dark:bg-blue-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500/0 group:hover:via-blue-500/50 group-[.active]:via-blue-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-300 group-[.active]:text-blue-600 dark:group-[.active]:text-blue-300 transition-colors drop-shadow">20 - 100 Jt</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-blue-500/50 hover:bg-blue-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="capital" data-value="over_100">
                                            <div class="absolute inset-0 bg-blue-500/10 dark:bg-blue-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500/0 group:hover:via-blue-500/50 group-[.active]:via-blue-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-300 group-[.active]:text-blue-600 dark:group-[.active]:text-blue-300 transition-colors drop-shadow">&gt; 100 Jt</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-8 bg-slate-50 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-500 rounded-l-2xl"></div>
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-4 cursor-help w-max" data-term="gross_margin">Estimasi Gross Margin </label>
                                    <input type="range" id="margin-slider" min="10" max="90" step="5" value="30" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-blue-500 hover:accent-blue-400 transition-all">
                                    <div class="flex justify-between mt-5 items-center">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase">Tipis (10%)</span>
                                        <span id="margin-display" class="text-lg font-black text-blue-700 dark:text-white bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20 dark:border-blue-500/30 px-4 py-1.5 rounded-lg shadow-sm dark:shadow-[0_0_15px_rgba(59,130,246,0.3)]">30%</span>
                                        <span class="text-[10px] font-bold text-slate-500 uppercase">Tinggi (90%)</span>
                                    </div>
                                </div>
                                
                                <div class="mb-8 bg-slate-50 dark:bg-slate-800/40 p-6 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative shadow-sm dark:shadow-[0_8px_30px_rgba(0,0,0,0.12)] backdrop-blur-md overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent rounded-2xl pointer-events-none"></div>
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-indigo-400 to-purple-500 rounded-l-2xl"></div>
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[0.15em] mb-4 relative z-10 flex items-center gap-2 cursor-help w-max" data-term="mentor_experience"><i class="fas fa-medal text-indigo-500 dark:text-indigo-400 text-xs"></i> Tingkat Pengalaman Bisnis</label>
                                    <div class="grid grid-cols-2 gap-4 relative z-10 w-full">
                                        <div class="wizard-chip-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black active group backdrop-blur-sm" data-group="experienceLevel" data-value="0.5">
                                            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-indigo-500/0 group:hover:via-indigo-500/50 group-[.active]:via-indigo-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-[.active]:text-indigo-600 dark:group-[.active]:text-indigo-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors drop-shadow">Beginner</span>
                                        </div>
                                        <div class="wizard-chip-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="experienceLevel" data-value="1.0">
                                            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-indigo-500/0 group:hover:via-indigo-500/50 group-[.active]:via-indigo-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-[.active]:text-indigo-600 dark:group-[.active]:text-indigo-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors drop-shadow">Rookie</span>
                                        </div>
                                        <div class="wizard-chip-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="experienceLevel" data-value="1.5">
                                            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-indigo-500/0 group:hover:via-indigo-500/50 group-[.active]:via-indigo-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-[.active]:text-indigo-600 dark:group-[.active]:text-indigo-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors drop-shadow">Pro</span>
                                        </div>
                                        <div class="wizard-chip-select relative overflow-hidden py-4 px-3 sm:px-4 min-h-[70px] border-2 border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/50 rounded-xl cursor-pointer hover:border-indigo-500/50 hover:bg-indigo-500/10 hover:-translate-y-1 shadow-sm dark:shadow-inner transition-all duration-300 flex items-center justify-center text-center text-sm md:text-base font-black group backdrop-blur-sm" data-group="experienceLevel" data-value="2.0">
                                            <div class="absolute inset-0 bg-indigo-500/10 dark:bg-indigo-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md pointer-events-none"></div>
                                            <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-indigo-500/0 group:hover:via-indigo-500/50 group-[.active]:via-indigo-500/50 to-transparent transition-opacity"></div>
                                            <span class="relative z-10 text-slate-500 dark:text-slate-400 group-[.active]:text-indigo-600 dark:group-[.active]:text-indigo-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors drop-shadow">Expert</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: FINANCIAL TARGETS -->
                            <div class="wizard-step hidden" data-step="3">
                                <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-8 leading-tight drop-shadow-md">Target <span class="text-purple-600 dark:text-purple-400">yang Mau Dicapai.</span></h4>

                                <div class="mb-8 bg-slate-50 dark:bg-slate-800/80 p-6 border border-purple-500/20 rounded-2xl relative shadow-sm dark:shadow-[0_0_30px_rgba(168,85,247,0.05)]">
                                    <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                                        <i class="fas fa-crosshairs text-6xl text-purple-500 dark:text-purple-400"></i>
                                    </div>
                                    
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[0.15em] mb-4 relative z-10 cursor-help w-max" data-term="revenue">Target Omzet Bulanan </label>
                                    
                                    <div class="flex items-center gap-4 mb-6 relative z-10 focus-within:transform focus-within:scale-[1.02] transition-transform">
                                        <div class="w-12 h-12 rounded-xl bg-purple-500/5 dark:bg-purple-500/10 border border-purple-500/20 dark:border-purple-500/30 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold shrink-0 shadow-sm dark:shadow-inner">
                                            RP
                                        </div>
                                        <input type="text" id="target-revenue-input" class="w-full bg-transparent text-xl sm:text-2xl font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-700 focus:border-purple-500 pb-2 transition-colors" value="10.000.000">
                                    </div>
                                    
                                    <input type="range" id="target-revenue-slider" min="5000000" max="500000000" step="1000000" value="10000000" class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-purple-600 dark:accent-purple-500 relative z-10 mt-2">
                                </div>

                                <div>
                                    <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-4 cursor-help w-max" data-term="mentor_timeframe">Timeframe Eksekusi</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="wizard-card-select relative overflow-hidden p-4 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-2xl cursor-pointer hover:border-purple-500 transition-all duration-300 text-center active group" data-group="timeframeMonths" data-value="6">
                                            <div class="absolute inset-0 bg-purple-500/5 dark:bg-purple-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md"></div>
                                            <span class="relative z-10 block text-2xl font-black text-slate-500 dark:text-slate-400 group-[.active]:text-purple-700 dark:group-[.active]:text-white group-hover:text-purple-700 dark:group-hover:text-white transition-colors">6</span>
                                            <span class="relative z-10 text-[10px] uppercase font-bold text-slate-500 group-[.active]:text-purple-600 dark:group-[.active]:text-purple-300">Bln</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden p-4 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-2xl cursor-pointer hover:border-purple-500 transition-all duration-300 text-center group" data-group="timeframeMonths" data-value="12">
                                            <div class="absolute inset-0 bg-purple-500/5 dark:bg-purple-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md"></div>
                                            <span class="relative z-10 block text-2xl font-black text-slate-500 dark:text-slate-400 group-[.active]:text-purple-700 dark:group-[.active]:text-white group-hover:text-purple-700 dark:group-hover:text-white transition-colors">12</span>
                                            <span class="relative z-10 text-[10px] uppercase font-bold text-slate-500 group-[.active]:text-purple-600 dark:group-[.active]:text-purple-300">Bln</span>
                                        </div>
                                        <div class="wizard-card-select relative overflow-hidden p-4 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-2xl cursor-pointer hover:border-purple-500 transition-all duration-300 text-center group" data-group="timeframeMonths" data-value="24">
                                            <div class="absolute inset-0 bg-purple-500/5 dark:bg-purple-500/20 opacity-0 group-[.active]:opacity-100 transition-opacity blur-md"></div>
                                            <span class="relative z-10 block text-2xl font-black text-slate-500 dark:text-slate-400 group-[.active]:text-purple-700 dark:group-[.active]:text-white group-hover:text-purple-700 dark:group-hover:text-white transition-colors">24</span>
                                            <span class="relative z-10 text-[10px] uppercase font-bold text-slate-500 group-[.active]:text-purple-600 dark:group-[.active]:text-purple-300">Bln</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 4: KONDISI BISNIS SAAT INI -->
                            <div class="wizard-step hidden" data-step="4">
                                <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-2 leading-tight drop-shadow-md">Kondisi <span class="text-orange-500 dark:text-orange-400">Bisnis Saat Ini.</span></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Data aktual bulan terakhir. Isi sesuai kondisi riil — semakin akurat, semakin tajam diagnosisnya.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Revenue Aktual -->
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-400 to-amber-500 rounded-l-2xl"></div>
                                        <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-3 flex items-center gap-1.5 cursor-help w-max" data-term="mentor_actual_revenue">
                                            <i class="fas fa-chart-bar text-orange-500 text-xs"></i> Revenue Aktual / Bulan
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" id="actual-revenue-input" data-wizard-field="actualRevenue" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-orange-500 pb-1 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600">
                                        </div>
                                    </div>
                                    <!-- Biaya Aktual -->
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-rose-400 to-red-500 rounded-l-2xl"></div>
                                        <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-3 flex items-center gap-1.5 cursor-help w-max" data-term="mentor_actual_expenses">
                                            <i class="fas fa-receipt text-rose-500 text-xs"></i> Total Biaya / Bulan
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" id="actual-expenses-input" data-wizard-field="actualExpenses" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-rose-500 pb-1 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600">
                                        </div>
                                    </div>
                                    <!-- Saldo Kas -->
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-400 to-teal-500 rounded-l-2xl"></div>
                                        <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-3 flex items-center gap-1.5 cursor-help w-max" data-term="mentor_cash_balance">
                                            <i class="fas fa-landmark text-emerald-500 text-xs"></i> Saldo Kas Sekarang
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" id="cash-balance-input" data-wizard-field="cashBalance" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-emerald-500 pb-1 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600">
                                        </div>
                                    </div>
                                    <!-- Biaya Ads -->
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-violet-400 to-purple-500 rounded-l-2xl"></div>
                                        <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-3 flex items-center gap-1.5 cursor-help w-max" data-term="mentor_ad_spend">
                                            <i class="fas fa-ad text-violet-500 text-xs"></i> Biaya Iklan / Bulan
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" id="ad-spend-input" data-wizard-field="adSpend" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-violet-500 pb-1 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                    <!-- AOV -->
                                    <div class="bg-slate-50 dark:bg-slate-800/50 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 relative overflow-hidden">
                                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-cyan-400 to-blue-500 rounded-l-2xl"></div>
                                        <label class="biz-term block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-3 flex items-center gap-1.5 cursor-help w-max" data-term="mentor_aov">
                                            <i class="fas fa-shopping-cart text-cyan-500 text-xs"></i> AOV (Nilai per Transaksi)
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-400">Rp</span>
                                            <input type="text" id="aov-input" data-wizard-field="avgOrderValue" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-cyan-500 pb-1 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600">
                                        </div>
                                    </div>
                                    <!-- Repeat Rate + Business Age -->
                                    <div class="space-y-4">
                                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-2 flex items-center gap-1.5">
                                                <i class="fas fa-redo text-pink-500 text-xs"></i> Repeat Buyer (%)
                                            </label>
                                            <input type="number" id="repeat-rate-input" data-wizard-field="repeatRate" min="0" max="100" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-pink-500 pb-1 transition-colors placeholder:text-slate-300">
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/50">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.15em] mb-2 flex items-center gap-1.5">
                                                <i class="fas fa-calendar text-slate-400 text-xs"></i> Umur Bisnis (bulan)
                                            </label>
                                            <input type="number" id="business-age-input" data-wizard-field="businessAge" min="0" placeholder="0" class="w-full bg-transparent text-lg font-black text-slate-900 dark:text-white outline-none border-b-2 border-slate-300 dark:border-slate-600 focus:border-slate-500 pb-1 transition-colors placeholder:text-slate-300">
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-4 text-center italic">* Semua field opsional. Makin lengkap makin tajam diagnosisnya.</p>
                            </div>

                            <!-- STEP 5: PROBLEM SELECTOR -->
                            <div class="wizard-step hidden" data-step="5">
                                <h4 class="text-2xl font-black text-slate-900 dark:text-white mb-2 leading-tight drop-shadow-md">Apa yang <span class="text-red-500 dark:text-red-400">Lagi Berat?</span></h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Pilih semua masalah yang relevan (boleh lebih dari satu). Ini membantu diagnosis lebih akurat.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="problem-selector-grid">
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-rose-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="stagnant_revenue">
                                        <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center text-rose-500 flex-shrink-0 group-[.selected]:bg-rose-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-chart-line-down text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-rose-600 dark:group-[.selected]:text-rose-400">Sales/Revenue Stagnan</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Omzet tidak naik-naik walau sudah usaha</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-orange-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="ads_dependency_moderate">
                                        <div class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center text-orange-500 flex-shrink-0 group-[.selected]:bg-orange-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-fire-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-orange-600 dark:group-[.selected]:text-orange-400">Boncos di Ads</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Keluar duit buat iklan tapi ROI jelek</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-pink-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="low_retention">
                                        <div class="w-9 h-9 rounded-xl bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center text-pink-500 flex-shrink-0 group-[.selected]:bg-pink-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-user-times text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-pink-600 dark:group-[.selected]:text-pink-400">Customer Tidak Repeat</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Beli sekali lalu hilang, susah bikin loyal</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-cyan-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="cashflow_crisis">
                                        <div class="w-9 h-9 rounded-xl bg-cyan-100 dark:bg-cyan-500/20 flex items-center justify-center text-cyan-500 flex-shrink-0 group-[.selected]:bg-cyan-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-wallet text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-cyan-600 dark:group-[.selected]:text-cyan-400">Uang Ada di Kertas, Rekening Kosong</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Profit di laporan tapi kas selalu tipis</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-amber-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="margin_compression">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center text-amber-500 flex-shrink-0 group-[.selected]:bg-amber-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-compress-alt text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-amber-600 dark:group-[.selected]:text-amber-400">Margin Makin Tipis</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Harga susah naik, biaya terus naik</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-violet-400 hover:-translate-y-0.5 transition-all duration-200 group" data-problem="ops_chaos">
                                        <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center text-violet-500 flex-shrink-0 group-[.selected]:bg-violet-500 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-random text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-violet-600 dark:group-[.selected]:text-violet-400">Operasional Kacau</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Tidak bisa scale karena sistem belum ada</p>
                                        </div>
                                    </div>
                                    <div class="problem-card cursor-pointer flex items-start gap-3 p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-slate-400 hover:-translate-y-0.5 transition-all duration-200 group sm:col-span-2" data-problem="no_focus">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-600/50 flex items-center justify-center text-slate-500 flex-shrink-0 group-[.selected]:bg-slate-600 group-[.selected]:text-white transition-colors">
                                            <i class="fas fa-question text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 group-[.selected]:text-slate-800 dark:group-[.selected]:text-white">Tidak Tahu Harus Fokus ke Mana</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Banyak yang ingin dikerjakan, bingung prioritasnya</p>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[10px] text-slate-400 mt-4 text-center">* Boleh pilih lebih dari satu. Boleh skip jika tidak ada yang relevan.</p>
                            </div>

                        </div>

                    </div>

                    <!-- Navigation Footer -->
                    <div class="relative z-10 flex justify-between mt-auto pt-6 border-t border-slate-200 dark:border-slate-800">
                        <button id="wizard-btn-back" data-wizard-action="back" class="invisible px-5 py-2.5 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white font-bold text-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button id="wizard-btn-next" data-wizard-action="next" class="px-8 py-3 bg-slate-900 text-white dark:bg-white dark:text-slate-900 rounded-xl font-black text-sm shadow-md dark:shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:shadow-lg dark:hover:shadow-[0_0_25px_rgba(255,255,255,0.4)] hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            Next Step <i class="fas fa-arrow-right"></i>
                        </button>
                        <button id="wizard-btn-submit" data-wizard-action="submit" class="hidden px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-500 dark:to-teal-400 text-white rounded-xl font-black text-sm shadow-md dark:shadow-[0_0_25px_rgba(52,211,153,0.4)] hover:shadow-lg dark:hover:shadow-[0_0_35px_rgba(52,211,153,0.6)] hover:-translate-y-0.5 transition-all flex items-center gap-2 border border-emerald-500/50 dark:border-emerald-400/50">
                            <i class="fas fa-bolt text-yellow-300"></i> Initialize Simulation
                        </button>
                    </div>
                </div>

                <!-- Dashboard Area -->
                <div id="mentor-board-container" class="transition-all duration-500 w-full relative min-h-[600px]">
                    
                    <!-- Loading Overlay -->
                    <div id="mentor-loading" class="hidden absolute inset-0 bg-white/90 dark:bg-slate-900/90 z-50 flex flex-col items-center justify-center backdrop-blur-sm rounded-3xl transition-all duration-300">
                        <div class="relative w-20 h-20 mb-6">
                            <div class="absolute inset-0 border-4 border-slate-200 dark:border-slate-700 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-emerald-500 rounded-full border-t-transparent animate-spin"></div>
                            <i class="fas fa-brain absolute inset-0 flex items-center justify-center text-emerald-500 text-2xl animate-pulse"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white animate-pulse mb-2" id="loading-text">Lagi Bedah Struktur Bisnis Kamu...</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Sabar ya, lagi ngitung potensi cuan kamu.</p>
                    </div>

                    <div id="mentor-dashboard" class="space-y-6 hidden">
                        
                        <!-- Dashboard content is dynamically generated by mentor-wizard.js (Strategic V2) -->
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>

    </div><!-- /#bento-workspace-container -->

    <!-- Hidden bridge: profit-simulator.js writes simulation data here; roadmap-engine.js reads it -->
    <div id="roadmap-sim-meta" class="hidden"
         data-traffic=""
         data-conversion=""
         data-margin=""
         data-channel="">
    </div>

    <!-- H. ROADMAP SECTION (Full Width & Centered) -->
    <section id="roadmap-container" class="hidden relative overflow-hidden max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 bg-white dark:bg-slate-900 rounded-3xl mt-12 shadow-sm border border-slate-100 dark:border-slate-800">
        
        {{-- Guest Overlay (Psychological Lock) --}}
        <div class="guest-only absolute inset-0 z-50 bg-white/40 dark:bg-slate-950/60 backdrop-blur-md flex flex-col items-center justify-center">
            <div class="relative bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl border border-amber-200 dark:border-amber-900/50 text-center max-w-sm mx-4 transform transition-all hover:-translate-y-2 hover:shadow-amber-500/20 duration-500 group">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl pointer-events-none"></div>
                <div class="relative z-10 w-16 h-16 bg-gradient-to-br from-amber-500/20 to-orange-600/20 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-amber-500/30 shadow-[0_0_20px_rgba(245,158,11,0.3)] animate-pulse">
                    <i class="fas fa-lock text-3xl text-amber-500 drop-shadow-lg group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2 tracking-tight leading-tight">Blue Print Menuju Sultan 🗺️</h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 leading-relaxed">Kehilangan arah? Kami petakan langkah eksak dari A sampai Z secara live berdasarkan analisis bisnismu.</p>
                    <button onclick="window.openLoginModal('login')" class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 active:scale-[0.98] flex items-center justify-center gap-2 mb-4">
                        Unlock Roadmap <i class="fas fa-key group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wide"><i class="fas fa-shield-alt mr-1"></i>Akses VIP Terbatas.</p>
                </div>
            </div>
        </div>

        <div class="mb-12 text-center relative max-w-3xl mx-auto">
            <!-- Decorative Glow -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-3/4 bg-emerald-400/10 dark:bg-emerald-500/10 blur-3xl -z-10 rounded-full pointer-events-none"></div>
            
            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-bold tracking-widest uppercase mb-4 border border-emerald-200 dark:border-emerald-700/50 shadow-sm">
                <i class="fas fa-bolt text-amber-500 animate-pulse"></i> Action Plan Eksklusif
            </div>

            <h3 class="text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white mb-4 tracking-tight leading-tight">
                Peta Jalan <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-cyan-600 dark:from-emerald-400 dark:to-cyan-400">Eksekusi Anda</span>
            </h3>
            
            <p class="text-slate-600 dark:text-slate-300 text-lg md:text-xl leading-relaxed font-medium mb-6">
                Hasil analisis telah diformulasikan. Jangan biarkan ide menguap! <br class="hidden sm:block"/> 
                Jalankan <span class="text-emerald-600 dark:text-emerald-400 font-bold border-b-2 border-emerald-400/30">Instruksi Praktis</span> di bawah ini dan raih target Anda sekarang.
            </p>
            <button onclick="window.open('/guide/phase-4', '_blank')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-600 dark:text-amber-400 text-sm font-bold rounded-xl transition-all shadow-sm active:scale-95">
                <i class="fas fa-book-open"></i> Panduan Phase 4
            </button>
        </div>

        <div id="roadmap-steps" class="relative space-y-8">
            <svg class="roadmap-lines"></svg>
            <!-- Steps rendered by JS -->
            <div class="roadmap-cards space-y-6"></div>
        </div>
    </section>



        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-12 pb-32 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center gap-6">
                <div class="text-center">
                    <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Cuan<span
                            class="text-emerald-600 dark:text-emerald-400">Capital</span>
                    </h4>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">© 2026 Fokus pada Eksekusi, Bukan Sekadar Ide.
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <a href="https://www.instagram.com/cuancapital.id?igsh=N2Vyb3d0cWpmMzJi" target="_blank"
                        rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-emerald-400 transition-all duration-300 hover:scale-110 border-2 border-transparent hover:border-emerald-400 hover:shadow-[0_0_15px_rgba(52,211,153,0.6)] hover:bg-slate-900 group"
                        title="Instagram">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/share/15XZMS8kJKT/" target="_blank" rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-emerald-400 transition-all duration-300 hover:scale-110 border-2 border-transparent hover:border-emerald-400 hover:shadow-[0_0_15px_rgba(52,211,153,0.6)] hover:bg-slate-900 group"
                        title="Facebook">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="https://www.tiktok.com/@cuan.capital.id?_r=1&_t=ZS-93o7k6jZzfu" target="_blank"
                        rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-emerald-400 transition-all duration-300 hover:scale-110 border-2 border-transparent hover:border-emerald-400 hover:shadow-[0_0_15px_rgba(52,211,153,0.6)] hover:bg-slate-900 group"
                        title="TikTok">
                        <i class="fab fa-tiktok text-lg"></i>
                    </a>
                    <a href="mailto:team.cuancapital@gmail.com" target="_blank" rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-emerald-400 transition-all duration-300 hover:scale-110 border-2 border-transparent hover:border-emerald-400 hover:shadow-[0_0_15px_rgba(52,211,153,0.6)] hover:bg-slate-900 group"
                        title="Email">
                        <i class="fas fa-envelope text-lg"></i>
                    </a>
                </div>

                <p class="text-xs text-slate-400 dark:text-slate-500">Made with 💚 by CuanCapital Team</p>
            </div>
        </footer>

    </main>





    </div>




    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

    <button id="backToTop" aria-label="Back to Top" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script type="module" src="{{ asset('assets/js/main.js') }}"></script>
    </main>
    


    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('kamuad', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then(registration => {
                        console.log('SW registered:', registration);
                    })
                    .catch(error => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
    </script>
    <!-- Impersonation Exit Button -->
    <div id="exit-impersonation" class="fixed bottom-6 right-6 z-50 hidden">
        <div class="bg-rose-600 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-pulse">
            <i class="fas fa-user-secret text-xl"></i>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-rose-200">Viewing As</span>
                <span class="text-sm font-medium" id="imp-name">User</span>
            </div>
            <button onclick="exitImpersonation()"
                class="ml-2 bg-white text-rose-600 px-3 py-1 rounded-lg text-xs font-bold hover:bg-rose-50 transition">
                EXIT
            </button>
        </div>
    </div>
    <script type="module">
        import { logoutUser } from '/assets/js/core/auth-engine.js';
        
        // Logout logic moved to main.js with custom confirmation
    </script>
    <script type="module" src="{{ asset('assets/js/core/ad-arsenal-frontend.js') }}"></script>



    <script type="module" src="{{ asset('assets/js/profit-simulator.js') }}?v={{ time() }}"></script>
    <script type="module" src="{{ asset('assets/js/features/roadmap-engine.js') }}?v={{ time() }}"></script>
    <script type="module" src="{{ asset('assets/js/features/mentor-wizard.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/reverse-goal-planner.js') }}"></script>
    
    <!-- Profit Simulator Config -->
    <script>
        window.profitSimulatorConfig = {
            sessionId: "{{ session('reverse_goal_session_id') }}" // Or fetch from auth user latest
        };
    </script>

    <script>
        // Settings Dropdown Logic
        const settingsBtn = document.getElementById('settings-menu-btn');
        const settingsDropdown = document.getElementById('settings-dropdown');

        if (settingsBtn && settingsDropdown) {
            settingsBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                settingsDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!settingsBtn.contains(e.target) && !settingsDropdown.contains(e.target)) {
                    settingsDropdown.classList.add('hidden');
                }
            });
        }

        // Rehydration Lifecycle with Retry Limit
        let hydrationAttempts = 0;
        window.attemptHydration = function() {
            if (hydrationAttempts > 20) {
                console.log("[Blueprint] Hydration reached max attempts or timed out.");
                return;
            }

            if (window.initialBlueprint && 
                typeof window.hydrateReverseGoal === "function" && 
                typeof window.hydrateSimulation === "function") {
                
                console.log("[Blueprint] Engine ready. Starting hydration.");
                
                if (!window.reverseGoalDirty) {
                    window.hydrateReverseGoal(window.initialBlueprint.reverse_goal_data);
                }
                
                if (!window.simulationDirty) {
                    window.hydrateSimulation(window.initialBlueprint.simulation_data);
                }
            } else {
                if (window.initialBlueprint) {
                    hydrationAttempts++;
                    setTimeout(window.attemptHydration, 100);
                }
            }
        };

        // Delay start slightly to let modules initialize
        setTimeout(window.attemptHydration, 1000);
    </script>
    <!-- Save Blueprint Modal -->
    <div id="save-blueprint-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    <div class="bg-white dark:bg-slate-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-save text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Simpan Blueprint Strategi</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Beri nama untuk strategi ini agar mudah ditemukan nanti.</p>
                                    <div class="mt-4">
                                        <label for="blueprint-name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-white">Nama Blueprint</label>
                                        <div class="mt-2">
                                            <input type="text" name="blueprint-name" id="blueprint-name" 
                                                class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 bg-transparent px-3" 
                                                placeholder="Contoh: Target 100jt Q1 2026">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="confirm-save-blueprint" class="inline-flex w-full justify-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition-colors">Simpan</button>
                        <button type="button" id="cancel-save-blueprint" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-800 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Blueprints Sidebar/Drawer -->
    <div id="blueprints-sidebar" class="fixed inset-y-0 right-0 z-[1000] w-80 bg-white dark:bg-slate-900 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out border-l border-slate-200 dark:border-slate-800 flex flex-col">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-900/50">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                Ransel Aksa
            </h2>
            <button id="close-sidebar-btn" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="blueprints-list" class="flex-1 overflow-y-auto p-4 space-y-3">
            <!-- Blueprint Items will be injected here -->
            <div class="text-center text-slate-400 mt-10">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Memuat blueprints...</p>
            </div>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <button id="create-new-blueprint-btn" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors font-semibold text-sm">
                <i class="fas fa-plus"></i> Buat Strategi Baru
            </button>
        </div>
    </div>

    <!-- Floating Mascot Button -->
    <button id="open-blueprints-btn"
        class="auth-only hidden fixed bottom-4 right-4 md:bottom-6 md:right-6 z-[999] flex items-center justify-center group"
        title="My Blueprints"
        style="background:none;border:none;padding:0;">
        <div class="relative">
            <!-- Shadow disc -->
            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-16 md:w-24 h-4 md:h-5 bg-black/20 dark:bg-black/40 rounded-full blur-md pointer-events-none"></div>

            <img id="mascot-bag-img"
                 src="/assets/icon/Aksa_fullbag.png"
                 alt="Blueprint Bag"
                 class="w-24 h-24 md:w-36 md:h-36 object-contain transition-all duration-300"
                 style="transform-origin: bottom center;
                        filter: drop-shadow(0 8px 16px rgba(0,0,0,0.35)) drop-shadow(0 2px 4px rgba(0,0,0,0.2));"
            >

            <!-- Blueprint count badge -->
            <span id="mascot-badge"
                  class="hidden absolute top-2 right-2 w-6 h-6 rounded-full bg-emerald-500 text-white text-[10px] font-black flex items-center justify-center shadow-lg border-2 border-white">
                0
            </span>

            <!-- Tooltip -->
            <span class="absolute right-full mr-4 bottom-1/2 translate-y-1/2 bg-slate-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Ransel Aksa
            </span>
        </div>
    </button>

    <!-- Animation Layer (projectile lives here, above everything) -->
    <div id="anim-layer" class="fixed inset-0 z-[9000] pointer-events-none overflow-hidden"></div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed z-[9999] flex flex-col items-center gap-3 pointer-events-none" style="top:24px; left:50%; transform:translateX(-50%); width:90vw; max-width:32rem;"></div>

    <!-- Experience OS UI Removed -->

    <!-- Blueprint Mascot Animation System -->
    <script src="/assets/js/features/blueprint-mascot.js"></script>
    
    <!-- Mentor Wizard Logic -->
    <script src="/assets/js/features/mentor-wizard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Init Wizard if available
             if(document.getElementById('mentor-wizard-container') && window.MentorWizard) {
                 window.mentorWizard = new window.MentorWizard();
                 
                 // Inject Wizard Styles
                 const style = document.createElement('style');
                 style.textContent = `
                    @keyframes fadeInRight {
                        from { opacity: 0; transform: translateX(20px); }
                        to { opacity: 1; transform: translateX(0); }
                    }
                    .animate-fade-in-right {
                        animation: fadeInRight 0.4s ease-out forwards;
                    }
                    .wizard-card-select.active .check-icon {
                        opacity: 1;
                    }
                 `;
                 document.head.appendChild(style);
             }
        });
    </script>

    <!-- Learning Layer Scripts -->
    <script src="{{ asset('assets/js/learning/learning-mode.js') }}"></script>
    <script src="{{ asset('assets/js/learning/glossary-engine.js') }}"></script>
    <script src="{{ asset('assets/js/learning/guided-tour-config.js') }}"></script>
    <script src="{{ asset('assets/js/learning/guided-tour-engine.js') }}"></script>
    <script src="{{ asset('assets/js/learning/guided-tour-manager.js') }}"></script>
    <script src="{{ asset('assets/js/learning/learning-engine.js') }}"></script>
    <script src="{{ asset('assets/js/learning/learning-ui.js') }}"></script>
    
    <!-- Experience OS: Gamification Scripts -->
    <script src="{{ asset('assets/js/learning/gamification-config.js') }}"></script>
    <script src="{{ asset('assets/js/learning/gamification-engine.js') }}"></script>
    <script src="{{ asset('assets/js/learning/achievement-dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/learning/learning-hub.js') }}?v={{ time() }}"></script>
    {{-- <script src="{{ asset('assets/js/learning/nudge-engine.js') }}"></script> --}}

    <!-- Close flyouts when clicking outside -->
    <script>
        document.addEventListener('click', (e) => {
            // Achievement flyout
            const xpWrapper = document.getElementById('xp-bar-wrapper');
            const achFlyout = document.getElementById('achievement-flyout');
            if (xpWrapper && achFlyout && !xpWrapper.contains(e.target)) {
                achFlyout.classList.add('hidden');
            }
            // Learning Hub flyout
            const hubWrapper = document.getElementById('learning-hub-wrapper');
            const hubFlyout  = document.getElementById('learning-hub-flyout');
            if (hubWrapper && hubFlyout && !hubWrapper.contains(e.target)) {
                hubFlyout.classList.add('hidden');
            }
        });

        // ═══════════════════════════════════════════════════════
        // BADGE MODAL LOGIC (V2)
        // ═══════════════════════════════════════════════════════
        function openBadgeModal() {
            if (!localStorage.getItem('auth_token')) {
                if(window.openLoginModal) {
                    window.openLoginModal('viewBadges');
                } else {
                    window.location.href = '/login';
                }
                return;
            }
            document.getElementById('badge-modal').classList.remove('hidden');
            // Hide floating button while modal is open
            const floatBtn = document.getElementById('open-blueprints-btn');
            if (floatBtn) floatBtn.classList.add('!hidden');
            renderBadgeModal();
        }

        function closeBadgeModal() {
            document.getElementById('badge-modal').classList.add('hidden');
            // Restore floating button
            const floatBtn = document.getElementById('open-blueprints-btn');
            if (floatBtn) floatBtn.classList.remove('!hidden');
        }

        // ── Equip Confirmation Dialog ─────────────────────────────────────────
        function showEquipConfirm(type, id, name) {
            // Remove any existing confirm dialog
            const existing = document.getElementById('equip-confirm-dialog');
            if (existing) existing.remove();

            const dialog = document.createElement('div');
            dialog.id = 'equip-confirm-dialog';
            dialog.className = 'fixed inset-0 z-[9999] flex items-center justify-center px-4';
            dialog.innerHTML = `
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('equip-confirm-dialog').remove()"></div>
                <div class="relative bg-slate-900 border border-slate-700/60 rounded-2xl shadow-2xl p-5 w-full max-w-xs text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-${type === 'badge' ? 'medal' : 'circle-notch'} text-emerald-400 text-sm"></i>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">${type === 'badge' ? 'Pasang Badge' : 'Pasang Border'}</p>
                    <p class="text-sm font-black text-white mb-4">${name}</p>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('equip-confirm-dialog').remove()"
                                class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-[11px] font-bold text-slate-400 hover:text-white transition-all">
                            Batal
                        </button>
                        <button onclick="document.getElementById('equip-confirm-dialog').remove(); ${type === 'badge' ? `equipBadge(${id})` : `equipBorder(${id})`}"
                                class="flex-1 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-[11px] font-black text-white transition-all shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                            Ya, Pasang
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(dialog);
        }

        // ══════════════════════════════════════════════════════════════════════
        // SYNC ENGINE — Single source of truth for profile + modal preview
        // ══════════════════════════════════════════════════════════════════════
        window.syncAllProfileUI = function() {
            const state = window.profileState || {};
            const badge = state.badge || null;
            const border = state.border || null;

            // ── Hero profile: badge label ───────────────────────────────────
            if (window.renderBadgeLabel) window.renderBadgeLabel(badge);

            // ── Hero profile: avatar border ring ────────────────────────────
            if (window.renderAvatarBorder) window.renderAvatarBorder(border);

            // ── Modal preview: avatar border ring ───────────────────────────
            const modalBorderEl = document.getElementById('modal-preview-border');
            if (modalBorderEl) {
                if (border?.css_class) {
                    modalBorderEl.className = `absolute -inset-1.5 rounded-full pointer-events-none border-2 ${border.css_class} transition-all duration-300`;
                } else {
                    modalBorderEl.className = 'absolute -inset-1.5 rounded-full pointer-events-none hidden';
                }
            }

            // ── Modal preview: badge label pill ─────────────────────────────
            const modalBadgeWrap = document.getElementById('modal-preview-badge-wrap');
            if (modalBadgeWrap) {
                const rarityColors = {
                    bronze:   'text-amber-400 border-amber-700/50 bg-amber-900/30',
                    silver:   'text-slate-300 border-slate-500/50 bg-slate-700/30',
                    gold:     'text-yellow-400 border-yellow-500/50 bg-yellow-900/40',
                    platinum: 'text-teal-200 border-teal-500/50 bg-teal-900/40',
                    diamond:  'text-cyan-300 border-cyan-500/50 bg-cyan-900/40',
                    mythic:   'text-fuchsia-400 border-fuchsia-500/50 bg-fuchsia-900/40'
                };
                if (badge) {
                    const c = rarityColors[badge.rarity] || rarityColors.bronze;
                    modalBadgeWrap.innerHTML = `<div class="px-3 py-1 rounded-full border ${c} text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5 transition-all"><i class="fas fa-medal text-[8px]"></i>${badge.name}</div>`;
                } else {
                    modalBadgeWrap.innerHTML = `<div class="px-3 py-1 rounded-full bg-slate-800/50 border border-slate-700/50 text-[10px] font-bold text-slate-500 uppercase tracking-widest">No Badge Equipped</div>`;
                }
            }
        };

        async function equipBadge(badgeId) {
            try {
                const res = await fetch('/api/me/badge/equip', {
                    method: 'POST',
                    headers: { 
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ badge_id: badgeId })
                });
                const json = await res.json();
                if (json.success) {
                    // Re-fetch badge state only
                    const badgesRes = await fetch('/api/me/badges', { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } });
                    const badgesJson = await badgesRes.json();
                    window.cuanUserBadges = badgesJson.data?.badges || [];
                    const equipped = window.cuanUserBadges.find(b => b.is_equipped) || null;

                    // ✅ Update badge state only — border state stays untouched
                    if (!window.profileState) window.profileState = {};
                    window.profileState.badge = equipped;

                    // Sync both hero + modal preview in one call
                    window.syncAllProfileUI();
                    renderBadgeModal();
                    if (window.updateHeroBadge) window.updateHeroBadge();
                    if (window.CuanToast) window.CuanToast.success('Badge berhasil dipasang! 🏅');
                } else {
                    throw new Error(json.message);
                }
            } catch (err) {
                console.error('Equip Badge Error:', err);
                if (window.CuanToast) window.CuanToast.error('Gagal memasang badge: ' + err.message);
            }
        }

        // ── Equip Border Frame (ONLY avatar ring — NO badge touch) ───────────
        async function equipBorder(borderId) {
            try {
                const res = await fetch('/api/me/border/equip', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ border_id: borderId })
                });
                const json = await res.json();
                if (json.success) {
                    // Re-fetch border state only
                    const bordersRes = await fetch('/api/me/borders', { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } });
                    const bordersJson = await bordersRes.json();
                    const equippedBorder = bordersJson.success ? (bordersJson.data?.equipped_border || null) : null;

                    // ✅ Update border state only — badge state stays untouched
                    if (!window.profileState) window.profileState = {};
                    window.profileState.border = equippedBorder;

                    // Sync both hero + modal preview in one call
                    window.syncAllProfileUI();
                    renderBadgeModal();
                    if (window.updateHeroBorder) window.updateHeroBorder();
                    if (window.CuanToast) window.CuanToast.success('Bingkai avatar berhasil dipasang! ✨');
                } else {
                    throw new Error(json.message);
                }
            } catch (err) {
                console.error('Equip Border Error:', err);
                if (window.CuanToast) window.CuanToast.error('Gagal memasang bingkai: ' + err.message);
            }
        }

        // ── Badge Update (name/icon only, NO border effect) ──────────────────────
        window.updateHeroBadge = async function() {
            try {
                const res = await fetch('/api/me/badges', {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success && json.data.badges) {
                    const equipped = json.data.badges.find(b => b.is_equipped);
                    const wrapperEl = document.getElementById('hero-equipped-badge-name');
                    const nameEl = document.getElementById('hero-badge-text');
                    const iconEl = document.getElementById('hero-badge-icon');
                    
                    const rarityColors = {
                        bronze:  'text-amber-700 dark:text-amber-400 border-amber-500/30 dark:border-amber-700/50 bg-amber-500/10 dark:bg-amber-900/30',
                        silver:  'text-slate-600 dark:text-slate-300 border-slate-500/30 dark:border-slate-500/50 bg-slate-500/10 dark:bg-slate-700/30',
                        gold:    'text-yellow-700 dark:text-yellow-400 border-yellow-500/30 dark:border-yellow-500/50 bg-yellow-500/10 dark:bg-yellow-900/40',
                        platinum:'text-teal-700 dark:text-teal-200 border-teal-500/30 dark:border-teal-500/50 bg-teal-500/10 dark:bg-teal-900/40',
                        diamond: 'text-cyan-700 dark:text-cyan-300 border-cyan-500/30 dark:border-cyan-500/50 bg-cyan-500/10 dark:bg-cyan-900/40',
                        mythic:  'text-fuchsia-700 dark:text-fuchsia-400 border-fuchsia-500/30 dark:border-fuchsia-500/50 bg-fuchsia-500/10 dark:bg-fuchsia-900/40',
                        default: 'bg-slate-100 dark:bg-slate-800/80 border-slate-300 dark:border-slate-600/50 text-slate-700 dark:text-slate-300'
                    };

                    // Generic base classes for the wrapper
                    const baseClasses = 'inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-0.5 rounded-full border text-[9px] md:text-[10px] font-bold shadow-sm backdrop-blur-sm transition-all max-w-full truncate';

                    if (equipped) {
                        const customHtml = getCustomBadgeHtml(equipped);
                        if (customHtml) {
                            if (wrapperEl) {
                                wrapperEl.className = 'inline-flex items-center transition-all max-w-full justify-center scale-[1.1] sm:scale-125 my-1';
                                wrapperEl.style.transformOrigin = 'left center';
                                wrapperEl.innerHTML = customHtml;
                            }
                        } else {
                            if (nameEl) nameEl.textContent = equipped.name;
                            const colorSet = rarityColors[equipped.rarity] || rarityColors.default;
                            if (wrapperEl) wrapperEl.className = `${baseClasses} ${colorSet}`;
                            if (iconEl) iconEl.className = `fas fa-medal shrink-0 ${equipped.rarity === 'mythic' ? 'animate-pulse' : ''}`;
                        }
                    } else {
                        if (nameEl) nameEl.textContent = 'No Badge';
                        if (wrapperEl) wrapperEl.className = `${baseClasses} ${rarityColors.default}`;
                        if (iconEl) iconEl.className = 'fas fa-medal text-slate-400 shrink-0';
                    }
                }
            } catch (err) {
                console.warn('Hero badge sync failed:', err);
            }
        };

        // ── Border Update (avatar ring only, independent from badge) ────────────
        window.updateHeroBorder = async function() {
            try {
                const res = await fetch('/api/me/borders', {
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.success) {
                    const equipped = json.data.equipped_border;
                    const fxEl = document.getElementById('hero-badge-fx');
                    if (equipped) {
                        if (equipped.css_class && equipped.css_class.startsWith('zodiac-')) {
                            const size = window.innerWidth >= 768 ? 70 : 60;
                            if (fxEl) {
                                fxEl.className = 'absolute pointer-events-none z-20';
                                fxEl.style.inset = (window.innerWidth >= 768) ? '-7px' : '-6px';
                                fxEl.innerHTML = window.ZodiacBorders ? window.ZodiacBorders.renderHTML(equipped.css_class, size) : '';
                            }
                        } else if (equipped.css_class && equipped.css_class.startsWith('element-')) {
                            const size = window.innerWidth >= 768 ? 70 : 60;
                            if (fxEl) {
                                fxEl.className = 'absolute pointer-events-none z-20';
                                fxEl.style.inset = (window.innerWidth >= 768) ? '-7px' : '-6px';
                                fxEl.innerHTML = window.ElementBorders ? window.ElementBorders.renderHTML(equipped.css_class, size) : '';
                            }
                        } else if (equipped.css_class && equipped.css_class.startsWith('universe-')) {
                            const size = window.innerWidth >= 768 ? 70 : 60;
                            if (fxEl) {
                                fxEl.className = 'absolute pointer-events-none z-20';
                                fxEl.style.inset = (window.innerWidth >= 768) ? '-7px' : '-6px';
                                fxEl.innerHTML = window.UniverseBorders ? window.UniverseBorders.renderHTML(equipped.css_class, size) : '';
                            }
                        } else {
                            if (fxEl) {
                                fxEl.style.inset = '';
                                fxEl.innerHTML = '';
                                fxEl.className = `absolute -inset-1 rounded-full border-2 ${equipped.css_class} pointer-events-none transition-all duration-300 z-20`;
                            }
                        }
                    } else {
                        if (fxEl) { fxEl.innerHTML = ''; fxEl.className = 'hidden'; fxEl.style.inset = ''; }
                    }
                }
            } catch (err) {
                console.warn('Hero border sync failed:', err);
            }
        };

        // Initialize on load — both independent calls
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.updateHeroBadge();
                window.updateHeroBorder();
            }, 2000);

            // Real-time Modal / Gamification Sync
            document.addEventListener('cuan:blueprint-saved', () => {
                if(document.getElementById('badge-modal') && !document.getElementById('badge-modal').classList.contains('hidden')) {
                    renderBadgeModal(); // Auto-refresh opened modal if XP is gained in background
                }
            });
            // Also tie into simulation generic gamification events
            document.addEventListener('cuan:simulation-success', () => {
                if(document.getElementById('badge-modal') && !document.getElementById('badge-modal').classList.contains('hidden')) {
                    setTimeout(renderBadgeModal, 1500); // Slight delay to let backend persist XP
                }
            });
        });

        async function renderBadgeModal() {
            const container = document.getElementById('badge-modal-content');
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full gap-3 text-slate-400">
                    <i class="fas fa-spinner fa-spin text-3xl text-emerald-500"></i>
                    <p class="text-sm font-medium">Loading collection...</p>
                </div>`;

            try {
                const [achRes, badgeRes, borderRes] = await Promise.all([
                    fetch('/api/me/achievements', { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } }),
                    fetch('/api/me/badges',        { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } }),
                    fetch('/api/me/borders',        { headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}`, 'Accept': 'application/json' } })
                ]);

                const achJson    = await achRes.json();
                const badgeJson  = await badgeRes.json();
                const borderJson = await borderRes.json();

                if (!achJson.success || !badgeJson.success) throw new Error(achJson.message || badgeJson.message);

                // Store globally for tab switching
                window.cuanUserBadges  = badgeJson.data?.badges  || [];
                window._modalAchData   = achJson.data?.achievements || [];
                window._modalBorderData = borderJson.success ? (borderJson.data?.borders || []) : [];

                const userName   = sessionStorage.getItem('cuan_user_display_name') || 'Guest';
                const userAvatar = sessionStorage.getItem('cuan_user_avatar') || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=1e293b&color=fff`;
                window._modalUserAvatar = userAvatar;
                window._modalUserName   = userName;

                // Render default tab (achievements)
                switchAchievementTab('achievements');

            } catch (err) {
                console.error('Badge Modal Error:', err);
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full gap-2 text-rose-400">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                    <p class="text-sm font-medium">Gagal memuat data. ${err.message || ''}</p>
                </div>`;
            }
        }

        // ══════════════════════════════════════════════════════════════════════
        // TAB SWITCHER
        // ══════════════════════════════════════════════════════════════════════
        function switchAchievementTab(tab) {
            // Update button styles
            ['achievements','badges','borders'].forEach(t => {
                const btn = document.getElementById(`tab-btn-${t}`);
                if (!btn) return;
                if (t === tab) {
                    btn.className = btn.className
                        .replace('text-slate-400 dark:text-slate-500 border-transparent','')
                        .replace('border-t-2 border-transparent','border-t-2 border-emerald-500');
                    btn.classList.add('text-emerald-600','dark:text-emerald-400');
                    btn.classList.remove('text-slate-400','dark:text-slate-500');
                } else {
                    btn.classList.remove('text-emerald-600','dark:text-emerald-400','border-emerald-500');
                    btn.classList.add('text-slate-400','dark:text-slate-500');
                    btn.style.borderTopColor = 'transparent';
                }
            });

            // Render correct content
            const container = document.getElementById('badge-modal-content');
            if (tab === 'achievements') renderAchievementsTab(container);
            else if (tab === 'badges')   renderBadgesTab(container);
            else if (tab === 'borders')  renderBordersTab(container);
        }

        // ══════════════════════════════════════════════════════════════════════
        // PROFILE PREVIEW (shared across all tabs, at top)
        // ══════════════════════════════════════════════════════════════════════
        function getCustomBadgeHtml(b) {
            const icon = b.icon || '🏅';
            const name = b.name.trim();

            let cls = '';
            if (name === 'Supreme Cuan') cls = 'badge-supreme';
            else if (name === 'Scholar Elite') cls = 'badge-scholar';
            else if (name === 'Grand Master') cls = 'badge-grandmaster';
            else if (name === 'Summa Cum Laude') cls = 'badge-summa';
            else if (name === 'Prodigy') cls = 'badge-prodigy';
            else if (name === 'Valedictorian') cls = 'badge-valedictorian';
            else if (name === 'Magna Scholar') cls = 'badge-magna';
            else if (name === 'The Chairman') cls = 'badge-chairman';
            else if (name === 'Wolf of Wall St.') cls = 'badge-wolf';
            else if (name === 'Tycoon') cls = 'badge-tycoon';
            else if (name === 'Board Member') cls = 'badge-board';
            else if (name === 'The Closer') cls = 'badge-closer';
            else if (name === 'Venture Founder') cls = 'badge-founder';
            else if (name === 'Mogul Status') cls = 'badge-mogul';

            if (!cls) return null;

            if (cls === 'badge-prodigy') return `<span class="${cls}"><span class="badge-icon">${icon}</span> ${b.name} <span class="status-dot"></span></span>`;
            if (cls === 'badge-magna') return `<span class="${cls}"><span class="badge-icon">${icon}</span> ${b.name} <span class="badge-stars">★★★</span></span>`;
            
            if (cls === 'badge-chairman') return `<span class="${cls}"><span class="badge-icon">💼</span> ${b.name} <span class="rank-lines"><span></span><span></span><span></span></span></span>`;
            if (cls === 'badge-wolf') return `<span class="${cls}"><span class="badge-icon">📈</span> ${b.name} <span class="ticker">▲ LIVE</span></span>`;
            if (cls === 'badge-tycoon') return `<span class="${cls}"><span class="badge-icon">🤑</span> ${b.name} <span class="arrow">↑</span></span>`;
            if (cls === 'badge-board') return `<span class="${cls}"><span class="dot"></span> <span class="badge-icon">🏛️</span> ${b.name}</span>`;
            if (cls === 'badge-closer') return `<span class="${cls}"><span class="flame">🔥</span> ${b.name}</span>`;
            if (cls === 'badge-founder') return `<span class="${cls}"><span class="v-tag">V</span> Venture <span class="slash">/</span> Founder</span>`;
            if (cls === 'badge-mogul') return `<span class="${cls}"><span class="crown">👑</span> ${b.name}</span>`;

            return `<span class="${cls}"><span class="badge-icon">${icon}</span> ${b.name}</span>`;
        }

        function buildProfilePreviewHTML() {
            const badge  = window.profileState?.badge || null;
            const border = window.profileState?.border || null;
            const userAvatar = window._modalUserAvatar || '';
            const userName   = window._modalUserName   || 'User';

            const rarityColors = {
                bronze:  'text-amber-700 dark:text-amber-400 border-amber-500/30 dark:border-amber-700/50 bg-amber-500/10 dark:bg-amber-900/30',
                silver:  'text-slate-600 dark:text-slate-300 border-slate-500/30 dark:border-slate-500/50 bg-slate-500/10 dark:bg-slate-700/30',
                gold:    'text-yellow-700 dark:text-yellow-400 border-yellow-500/30 dark:border-yellow-500/50 bg-yellow-500/10 dark:bg-yellow-900/40',
                platinum:'text-teal-700 dark:text-teal-200 border-teal-500/30 dark:border-teal-500/50 bg-teal-500/10 dark:bg-teal-900/40',
                diamond: 'text-cyan-700 dark:text-cyan-300 border-cyan-500/30 dark:border-cyan-500/50 bg-cyan-500/10 dark:bg-cyan-900/40',
                mythic:  'text-fuchsia-700 dark:text-fuchsia-400 border-fuchsia-500/30 dark:border-fuchsia-500/50 bg-fuchsia-500/10 dark:bg-fuchsia-900/40',
            };
            const customBadgeHtml = badge ? getCustomBadgeHtml(badge) : null;
            let badgeHTML = '';
            
            if (customBadgeHtml) {
                badgeHTML = `<div class="scale-[1.1] sm:scale-125 my-1" style="transform-origin: center;">${customBadgeHtml}</div>`;
            } else if (badge) {
                badgeHTML = `<div class="px-3 py-1 rounded-full border ${rarityColors[badge.rarity]||rarityColors.bronze} text-[10px] font-bold uppercase tracking-widest flex items-center gap-1.5 transition-all"><i class="fas fa-medal text-[8px]"></i>${badge.name}</div>`;
            } else {
                badgeHTML = `<div class="px-3 py-1 rounded-full bg-black/10 dark:bg-slate-800/50 border border-black/10 dark:border-white/10 text-[10px] font-bold text-slate-500 dark:text-slate-500 uppercase tracking-widest">No Badge</div>`;
            }

            const borderRingClass = border?.css_class ? `border-2 ${border.css_class}` : 'border-2 border-slate-200 dark:border-slate-700';

            return `
            <div class="flex flex-col items-center pt-6 pb-5 px-5
                        bg-gradient-to-b from-emerald-500/5 to-transparent
                        border-b border-black/5 dark:border-white/10">
                <!-- Avatar with live border preview -->
                <div class="relative mb-3 flex items-center justify-center" style="width:88px;height:88px;">
                    ${border?.css_class?.startsWith('zodiac-') 
                        ? `<div id="modal-preview-border" class="absolute pointer-events-none transition-all duration-300 z-20" style="inset:-11px;">
                               ${window.ZodiacBorders ? window.ZodiacBorders.renderHTML(border.css_class, 110) : ''}
                           </div>`
                        : border?.css_class?.startsWith('element-')
                        ? `<div id="modal-preview-border" class="absolute pointer-events-none transition-all duration-300 z-20" style="inset:-11px;">
                               ${window.ElementBorders ? window.ElementBorders.renderHTML(border.css_class, 110) : ''}
                           </div>`
                        : border?.css_class?.startsWith('universe-')
                        ? `<div id="modal-preview-border" class="absolute pointer-events-none transition-all duration-300 z-20" style="inset:-11px;">
                               ${window.UniverseBorders ? window.UniverseBorders.renderHTML(border.css_class, 110) : ''}
                           </div>`
                        : `<div id="modal-preview-border" class="absolute -inset-2 rounded-full pointer-events-none transition-all duration-300 z-20 ${borderRingClass}"></div>`
                    }
                    <img id="modal-preview-avatar" src="${userAvatar}"
                         class="w-full h-full rounded-full object-cover border-4 border-white dark:border-slate-800 shadow-xl relative z-10">
                </div>
                <h4 class="text-base font-black text-slate-800 dark:text-white mb-2">${userName}</h4>
                <!-- Live badge preview -->
                <div id="modal-preview-badge-wrap" class="flex items-center">${badgeHTML}</div>

                <!-- My Unlocked: Badges + Borders row -->
                <div class="w-full mt-5 grid grid-cols-2 gap-3">
                    ${buildMyBadgesRow()}
                    ${buildMyBordersRow()}
                </div>
            </div>`;
        }

        function buildMyBadgesRow() {
            const owned = (window.cuanUserBadges || []).filter(b => b.is_unlocked);
            const rarityColors = {
                bronze:  'text-amber-700 dark:text-amber-400 border-amber-500/30 dark:border-amber-700/60 bg-amber-500/10 dark:bg-amber-900/30',
                silver:  'text-slate-600 dark:text-slate-300 border-slate-500/30 dark:border-slate-500/60 bg-slate-500/10 dark:bg-slate-700/30',
                gold:    'text-yellow-700 dark:text-yellow-400 border-yellow-500/30 dark:border-yellow-500/60 bg-yellow-500/10 dark:bg-yellow-900/30',
                platinum:'text-teal-700 dark:text-teal-300 border-teal-500/30 dark:border-teal-500/60 bg-teal-500/10 dark:bg-teal-900/30',
                diamond: 'text-cyan-700 dark:text-cyan-300 border-cyan-500/30 dark:border-cyan-500/60 bg-cyan-500/10 dark:bg-cyan-900/30',
                mythic:  'text-fuchsia-700 dark:text-fuchsia-400 border-fuchsia-500/30 dark:border-fuchsia-500/60 bg-fuchsia-500/10 dark:bg-fuchsia-900/30',
            };
            const cards = owned.length > 0 ? owned.map(b => {
                const c = rarityColors[b.rarity] || rarityColors.bronze;
                return `<div onclick="showEquipConfirm('badge',${b.id},'${b.name.replace(/'/g,"\\\\'")}')"
                             class="flex-shrink-0 flex flex-col items-center gap-1 p-2 rounded-xl cursor-pointer
                                    bg-white/50 dark:bg-slate-800/40 border border-white/30 dark:border-white/10
                                    backdrop-blur-md hover:scale-105 transition-all w-16
                                    ${b.is_equipped ? 'ring-1 ring-emerald-500/50' : ''}">
                    <div class="flex items-center gap-0.5 px-1 py-0.5 rounded-full border ${c} text-[6px] font-black uppercase w-full justify-center">
                        <i class="fas fa-medal text-[5px]"></i><span class="truncate">${b.rarity}</span>
                    </div>
                    <span class="text-[7px] font-bold text-slate-600 dark:text-slate-300 text-center leading-tight line-clamp-2">${b.name}</span>
                    ${b.is_equipped ? '<span class="text-[6px] font-black text-emerald-500 uppercase">✓ On</span>' : ''}
                </div>`;
            }).join('') : `<p class="text-[9px] text-slate-400 dark:text-slate-500 text-center py-2 col-span-full">Belum ada badge</p>`;
            return `
            <div class="rounded-2xl p-3 bg-white/40 dark:bg-slate-800/30 border border-white/30 dark:border-white/10 backdrop-blur-md">
                <p class="text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                    <i class="fas fa-medal text-amber-400 text-[8px]"></i> My Badges
                </p>
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">${cards}</div>
            </div>`;
        }

        function buildMyBordersRow() {
            const userAvatar = window._modalUserAvatar || '';
            const owned = (window._modalBorderData || []).filter(b => b.is_unlocked);
            const cards = owned.length > 0 ? owned.map(b => `
                <div onclick="showEquipConfirm('border',${b.id},'${b.name.replace(/'/g,"\\\\'")}')"
                     class="flex-shrink-0 flex flex-col items-center gap-1 p-2 rounded-xl cursor-pointer
                            bg-white/50 dark:bg-slate-800/40 border border-white/30 dark:border-white/10
                            backdrop-blur-md hover:scale-105 transition-all w-16
                            ${b.is_equipped ? 'ring-1 ring-purple-500/50' : ''}">
                    <div class="relative w-8 h-8 rounded-full flex items-center justify-center">
                        ${b.css_class?.startsWith('zodiac-')
                            ? `<div class="absolute pointer-events-none z-20" style="inset:-4px;">${window.ZodiacBorders ? window.ZodiacBorders.renderHTML(b.css_class, 40) : ''}</div>`
                            : b.css_class?.startsWith('element-')
                            ? `<div class="absolute pointer-events-none z-20" style="inset:-4px;">${window.ElementBorders ? window.ElementBorders.renderHTML(b.css_class, 40) : ''}</div>`
                            : b.css_class?.startsWith('universe-')
                            ? `<div class="absolute pointer-events-none z-20" style="inset:-4px;">${window.UniverseBorders ? window.UniverseBorders.renderHTML(b.css_class, 40) : ''}</div>`
                            : `<div class="absolute -inset-0.5 rounded-full border ${b.css_class || 'border-slate-600'} pointer-events-none z-20"></div>`
                        }
                        <img src="${userAvatar}" class="relative z-10 w-full h-full rounded-full object-cover">
                    </div>
                    <span class="text-[7px] font-bold text-slate-600 dark:text-slate-300 text-center leading-tight line-clamp-2">${b.name}</span>
                    ${b.is_equipped ? '<span class="text-[6px] font-black text-purple-400 uppercase">✓ On</span>' : ''}
                </div>`).join('') : `<p class="text-[9px] text-slate-400 dark:text-slate-500 text-center py-2">Belum ada border</p>`;
            return `
            <div class="rounded-2xl p-3 bg-white/40 dark:bg-slate-800/30 border border-white/30 dark:border-white/10 backdrop-blur-md">
                <p class="text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                    <i class="fas fa-circle-notch text-purple-400 text-[8px]"></i> My Borders
                </p>
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none">${cards}</div>
            </div>`;
        }

        // ── Tab 1: Achievements ───────────────────────────────────────────────
        function renderAchievementsTab(container) {
            const categories = {
                'learning':     { name: 'Learning & Knowledge', icon: 'fas fa-book' },
                'tools':        { name: 'Strategic Tools',      icon: 'fas fa-tools' },
                'skill':        { name: 'Business Mastery',     icon: 'fas fa-brain' },
                'consistency':  { name: 'Consistency Flow',     icon: 'fas fa-fire' },
                'engagement':   { name: 'Active Presence',      icon: 'fas fa-clock' },
                'mastery':      { name: 'Empire Level',         icon: 'fas fa-crown' },
                'miscellaneous':{ name: 'Miscellaneous',        icon: 'fas fa-star'  },
            };

            const grouped = {};
            (window._modalAchData || []).forEach(ach => {
                const raw = (ach.category || 'miscellaneous').toLowerCase().trim();
                const key = categories[raw] ? raw : 'miscellaneous';
                if (!grouped[key]) grouped[key] = [];
                grouped[key].push(ach);
            });

            let html = buildProfilePreviewHTML();
            html += `<div class="px-4 pb-4 space-y-8 mt-4">`;

            Object.keys(categories).forEach(catKey => {
                const list = grouped[catKey];
                if (!list || list.length === 0) return;
                html += `
                <div>
                    <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="${categories[catKey].icon} text-emerald-500 text-[9px]"></i>
                        ${categories[catKey].name}
                        <div class="h-px bg-black/10 dark:bg-white/10 flex-1 ml-1"></div>
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        ${list.map(ach => {
                            const unlocked = ach.is_unlocked;
                            const hidden   = ach.is_hidden && !unlocked;
                            const progress = Math.min(100, Math.round((ach.progress_cached / ach.condition_value) * 100)) || 0;
                            return `
                            <div class="rounded-2xl p-3.5 flex flex-col relative overflow-hidden transition-all duration-200 hover:scale-[1.02]
                                        backdrop-blur-lg border
                                        ${unlocked
                                            ? 'bg-emerald-500/8 dark:bg-emerald-500/10 border-emerald-500/20 dark:border-emerald-500/15'
                                            : 'bg-white/50 dark:bg-slate-800/40 border-white/30 dark:border-white/10'}">
                                <div class="text-2xl mb-2 ${hidden ? 'opacity-20 blur-sm' : ''}">
                                    ${hidden ? '❓' : ach.icon}
                                </div>
                                <p class="text-[11px] font-bold ${unlocked ? 'text-slate-800 dark:text-white' : 'text-slate-700 dark:text-slate-300'} leading-tight mb-1">
                                    ${hidden ? 'Secret Achievement' : ach.name}
                                </p>
                                <p class="text-[9px] text-slate-500 dark:text-slate-400 leading-relaxed mb-2 flex-1">
                                    ${hidden ? '???' : ach.description}
                                </p>
                                ${!unlocked ? `
                                    <div class="w-full h-1.5 rounded-full bg-black/10 dark:bg-white/10 overflow-hidden mb-1">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all" style="width:${progress}%"></div>
                                    </div>
                                    <p class="text-[8px] text-slate-400 tabular-nums">${hidden ? 'LOCKED' : `${ach.progress_cached} / ${ach.condition_value}`}</p>
                                ` : `
                                    <p class="text-[9px] font-black text-emerald-500 uppercase flex items-center gap-1">
                                        <i class="fas fa-check-double text-[7px]"></i> Unlocked
                                    </p>
                                `}
                                ${unlocked ? `<div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                    <i class="fas fa-check text-emerald-500 text-[7px]"></i></div>` : ''}
                            </div>`;
                        }).join('')}
                    </div>
                </div>`;
            });

            html += `</div>`;
            container.innerHTML = html;
        }

        // ── Tab 2: Badges (by rarity category) ───────────────────────────────
        function renderBadgesTab(container) {
            const all    = window.cuanUserBadges || [];
            const groups = { bronze:[], silver:[], gold:[], platinum:[], diamond:[], mythic:[], education:[], businessmen:[] };
            all.forEach(b => { if (groups[b.rarity]) groups[b.rarity].push(b); });

            const rarityMeta = {
                bronze:  { label:'Bronze',   color:'text-amber-600 dark:text-amber-500',   border:'border-amber-500/30 dark:border-amber-600/40',   bg:'bg-amber-500/10  dark:bg-amber-900/20',  icon:'fas fa-circle' },
                silver:  { label:'Silver',   color:'text-slate-600 dark:text-slate-300',   border:'border-slate-400/30 dark:border-slate-400/40',   bg:'bg-slate-500/10  dark:bg-slate-700/30',  icon:'fas fa-circle' },
                gold:    { label:'Gold',     color:'text-yellow-600 dark:text-yellow-400', border:'border-yellow-500/30 dark:border-yellow-500/40', bg:'bg-yellow-500/10 dark:bg-yellow-900/20', icon:'fas fa-star'   },
                platinum:{ label:'Platinum', color:'text-teal-600 dark:text-teal-300',     border:'border-teal-500/30 dark:border-teal-500/40',     bg:'bg-teal-500/10   dark:bg-teal-900/20',   icon:'fas fa-gem'    },
                diamond: { label:'Diamond',  color:'text-cyan-600 dark:text-cyan-300',     border:'border-cyan-500/30 dark:border-cyan-500/40',     bg:'bg-cyan-500/10   dark:bg-cyan-900/20',   icon:'fas fa-certificate' },
                mythic:  { label:'Mythic',   color:'text-fuchsia-600 dark:text-fuchsia-400', border:'border-fuchsia-500/30 dark:border-fuchsia-500/40', bg:'bg-fuchsia-500/10 dark:bg-fuchsia-900/20',icon:'fas fa-crown' },
                education: { label:'Educational', color:'text-indigo-600 dark:text-indigo-400', border:'border-indigo-500/30 dark:border-indigo-500/40', bg:'bg-indigo-500/10 dark:bg-indigo-900/20', icon:'fas fa-graduation-cap' },
                businessmen: { label:'Businessmen', color:'text-rose-600 dark:text-rose-400', border:'border-rose-500/30 dark:border-rose-500/40', bg:'bg-rose-500/10 dark:bg-rose-900/20', icon:'fas fa-user-tie' },
            };

            const badgeCardColors = {
                bronze:  'text-amber-700 dark:text-amber-400 border-amber-500/30 dark:border-amber-700/60 bg-amber-500/10 dark:bg-amber-900/30',
                silver:  'text-slate-600 dark:text-slate-300 border-slate-500/30 dark:border-slate-500/60 bg-slate-500/10 dark:bg-slate-700/30',
                gold:    'text-yellow-700 dark:text-yellow-400 border-yellow-500/30 dark:border-yellow-500/60 bg-yellow-500/10 dark:bg-yellow-900/30',
                platinum:'text-teal-700 dark:text-teal-300 border-teal-500/30 dark:border-teal-500/60 bg-teal-500/10 dark:bg-teal-900/30',
                diamond: 'text-cyan-700 dark:text-cyan-300 border-cyan-500/30 dark:border-cyan-500/60 bg-cyan-500/10 dark:bg-cyan-900/30',
                mythic:  'text-fuchsia-700 dark:text-fuchsia-400 border-fuchsia-500/30 dark:border-fuchsia-500/60 bg-fuchsia-500/10 dark:bg-fuchsia-900/30',
                education: 'text-indigo-700 dark:text-indigo-400 border-indigo-500/30 dark:border-indigo-500/60 bg-indigo-500/10 dark:bg-indigo-900/30',
                businessmen: 'text-rose-700 dark:text-rose-400 border-rose-500/30 dark:border-rose-500/60 bg-rose-500/10 dark:bg-rose-900/30',
            };

            let html = buildProfilePreviewHTML();
            html += `<div class="px-4 pb-4 space-y-8 mt-4">`;

            Object.keys(groups).forEach(rarity => {
                const list = groups[rarity];
                if (!list || list.length === 0) return;
                const m = rarityMeta[rarity];
                const c = badgeCardColors[rarity] || badgeCardColors.bronze;
                html += `
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest mb-3 flex items-center gap-2 ${m.color}">
                        <i class="${m.icon} text-[9px]"></i> ${m.label}
                        <div class="h-px bg-black/10 dark:bg-white/10 flex-1 ml-1"></div>
                    </h4>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                        ${list.map(b => {
                            const customHtml = getCustomBadgeHtml(b);
                            return `
                            <div ${b.is_unlocked ? `onclick="showEquipConfirm('badge',${b.id},'${b.name.replace(/'/g,"\\\\'")}')"` : ''}
                                 class="group rounded-2xl p-4 flex flex-col items-center text-center relative justify-center cursor-${b.is_unlocked?'pointer':'default'}
                                        backdrop-blur-lg border transition-all duration-200 min-h-[100px]
                                        ${b.is_unlocked ? `hover:scale-[1.03] hover:shadow-lg ${m.bg} ${m.border}` : 'bg-white/30 dark:bg-slate-800/30 border-white/20 dark:border-white/10 opacity-50'}
                                        ${b.is_equipped ? 'ring-1 ring-emerald-500/50' : ''}">

                                ${customHtml ? 
                                    `<div class="mb-2 max-w-full overflow-hidden flex items-center justify-center translate-y-2 scale-[1.1] sm:scale-100">${customHtml}</div>` 
                                : `
                                    <div class="flex items-center gap-0.5 px-1.5 py-0.5 rounded-full border ${c} text-[7px] font-black uppercase w-full justify-center mb-1.5">
                                        <i class="fas fa-medal text-[6px] shrink-0"></i>
                                        <span class="truncate">${b.rarity}</span>
                                    </div>
                                    <span class="text-[9px] font-bold text-slate-700 dark:text-slate-200 leading-tight mb-2">${b.name}</span>
                                `}

                                <span class="mt-auto text-[7px] font-semibold uppercase ${b.is_equipped ? 'text-emerald-500' : b.is_unlocked ? 'text-slate-400 dark:text-slate-500' : 'text-slate-500'}">
                                    ${b.is_equipped ? '✓ Equipped' : b.is_unlocked ? 'Pasang' : 'Locked'}
                                </span>
                                ${!b.is_unlocked ? `
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/40 dark:bg-slate-900/60 rounded-2xl z-20 transition-opacity duration-300 group-hover:opacity-0 pointer-events-none">
                                        <i class="fas fa-lock text-slate-300 dark:text-slate-400 text-xs"></i>
                                    </div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/95 dark:bg-slate-900/95 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-30 p-3 text-center cursor-help">
                                        <i class="fas fa-lock text-emerald-400 text-[10px] mb-1.5"></i>
                                        <span class="text-[9px] text-white leading-tight font-medium drop-shadow-sm">${b.description || b.earn_condition || 'Selesaikan misi spesifik untuk unlock badge ini.'}</span>
                                    </div>
                                ` : ''}
                                ${b.is_equipped ? `<div class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-emerald-500/20 flex items-center justify-center"><i class="fas fa-check text-emerald-500 text-[7px]"></i></div>` : ''}
                            </div>`;
                        }).join('')}
                    </div>
                </div>`;
            });

            html += `</div>`;
            container.innerHTML = html;
        }

        // ── Tab 3: Borders (by tier category) ────────────────────────────────
        function renderBordersTab(container) {
            const userAvatar = window._modalUserAvatar || '';
            const all        = window._modalBorderData || [];

            // Group borders by rarity/tier
            const tierOrder  = ['bronze','silver','gold','platinum','diamond','mythic','zodiac','element','universe'];
            const tierMeta   = {
                bronze:  { label:'Bronze',   color:'text-amber-500',   border:'border-amber-600/40',   icon:'fas fa-circle' },
                silver:  { label:'Silver',   color:'text-slate-300',   border:'border-slate-400/40',   icon:'fas fa-circle' },
                gold:    { label:'Gold',     color:'text-yellow-400',  border:'border-yellow-500/40',  icon:'fas fa-star'   },
                platinum:{ label:'Platinum', color:'text-teal-300',    border:'border-teal-500/40',    icon:'fas fa-gem'    },
                diamond: { label:'Diamond',  color:'text-cyan-300',    border:'border-cyan-500/40',    icon:'fas fa-certificate' },
                mythic:  { label:'Mythic',   color:'text-fuchsia-400', border:'border-fuchsia-500/40', icon:'fas fa-crown'  },
                zodiac:  { label:'Zodiac',   color:'text-indigo-400',  border:'border-indigo-500/40',  icon:'fas fa-star-and-crescent' },
                element: { label:'Earth Elements', color:'text-cyan-400', border:'border-cyan-500/40', icon:'fas fa-leaf' },
                universe:{ label:'Universe', color:'text-purple-400', border:'border-purple-500/40', icon:'fas fa-meteor' },
            };
            const grouped = {};
            all.forEach(b => {
                // derive tier from rarity or css_class tag
                const tier = b.rarity || (tierOrder.find(t => (b.css_class||'').includes(t)) || 'bronze');
                if (!grouped[tier]) grouped[tier] = [];
                grouped[tier].push(b);
            });

            let html = buildProfilePreviewHTML();
            html += `<div class="px-4 pb-4 space-y-8 mt-4">`;

            // If no tier grouping worked, fallback to flat list
            const anyGrouped = Object.values(grouped).some(g => g.length > 0);
            const renderList = anyGrouped ? null : all;

            const renderBorderCards = (list) => list.map(b => `
                <div ${b.is_unlocked ? `onclick="showEquipConfirm('border',${b.id},'${b.name}')"` : ''}
                     class="group rounded-2xl p-3 flex flex-col items-center text-center relative cursor-${b.is_unlocked?'pointer':'default'}
                            backdrop-blur-lg border transition-all duration-200
                            ${b.is_unlocked ? 'bg-white/50 dark:bg-slate-800/40 border-white/30 dark:border-white/10 hover:scale-[1.03]' : 'bg-white/20 dark:bg-slate-800/20 border-white/10 dark:border-white/5 opacity-50'}
                            ${b.is_equipped ? 'ring-1 ring-purple-500/50' : ''}">

                    <!-- Real avatar preview with actual ring -->
                    <div class="relative w-12 h-12 mb-2 flex items-center justify-center">
                        ${b.css_class?.startsWith('zodiac-')
                            ? `<div class="absolute pointer-events-none z-20 ${b.is_unlocked ? '' : 'opacity-30 grayscale'}" style="inset:-6px;">${window.ZodiacBorders ? window.ZodiacBorders.renderHTML(b.css_class, 60) : ''}</div>`
                            : b.css_class?.startsWith('element-')
                            ? `<div class="absolute pointer-events-none z-20 ${b.is_unlocked ? '' : 'opacity-30 grayscale'}" style="inset:-6px;">${window.ElementBorders ? window.ElementBorders.renderHTML(b.css_class, 60) : ''}</div>`
                            : b.css_class?.startsWith('universe-')
                            ? `<div class="absolute pointer-events-none z-20 ${b.is_unlocked ? '' : 'opacity-30 grayscale'}" style="inset:-6px;">${window.UniverseBorders ? window.UniverseBorders.renderHTML(b.css_class, 60) : ''}</div>`
                            : `<div class="absolute -inset-1 rounded-full pointer-events-none border-2 ${b.css_class || 'border-slate-600'} ${b.is_unlocked ? '' : 'opacity-30'} z-20"></div>`
                        }
                        <img src="${userAvatar}" class="relative z-10 w-full h-full rounded-full object-cover ${b.is_unlocked ? '' : 'grayscale'}">
                    </div>
                    <span class="text-[9px] font-bold text-slate-700 dark:text-slate-200 leading-tight">${b.name}</span>
                    <span class="mt-1 text-[7px] font-semibold uppercase ${b.is_equipped ? 'text-purple-400' : b.is_unlocked ? 'text-slate-400' : 'text-slate-500'}">
                        ${b.is_equipped ? '✓ Aktif' : b.is_unlocked ? 'Pasang' : 'Locked'}
                    </span>
                    ${!b.is_unlocked ? `
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40 dark:bg-slate-900/60 rounded-2xl z-20 transition-opacity duration-300 group-hover:opacity-0 pointer-events-none">
                            <i class="fas fa-lock text-slate-300 dark:text-slate-400 text-xs"></i>
                        </div>
                        <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/95 dark:bg-slate-900/95 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-30 p-3 text-center cursor-help">
                            <i class="fas fa-lock text-purple-400 text-[10px] mb-1.5"></i>
                            <span class="text-[9px] text-white leading-tight font-medium drop-shadow-sm">${b.description || b.unlock_condition || b.earn_condition || 'Selesaikan misi atau event untuk unlock border ini.'}</span>
                        </div>
                    ` : ''}
                    ${b.is_equipped ? `<div class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-purple-500/20 flex items-center justify-center"><i class="fas fa-check text-purple-400 text-[7px]"></i></div>` : ''}
                </div>`).join('');

            if (renderList) {
                html += `<div class="grid grid-cols-3 gap-3">${renderBorderCards(renderList)}</div>`;
            } else {
                tierOrder.forEach(tier => {
                    const list = grouped[tier];
                    if (!list || list.length === 0) return;
                    const m = tierMeta[tier] || tierMeta.bronze;
                    html += `
                    <div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest mb-3 flex items-center gap-2 ${m.color}">
                            <i class="${m.icon} text-[9px]"></i> ${m.label}
                            <div class="h-px bg-black/10 dark:bg-white/10 flex-1 ml-1"></div>
                        </h4>
                        <div class="grid grid-cols-3 gap-3">${renderBorderCards(list)}</div>
                    </div>`;
                });
            }

            html += `</div>`;
            container.innerHTML = html;
        }
    </script>
            
    <!-- Achievement Modal — Glassmorphism Redesign -->
    <div id="badge-modal" class="hidden fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4"
         style="background: rgba(0,0,0,0.55); backdrop-filter: blur(4px);">

        <!-- Glass Container -->
        <div class="relative w-full sm:max-w-2xl h-[92vh] sm:h-[88vh] rounded-t-3xl sm:rounded-3xl flex flex-col overflow-hidden shadow-2xl border
                    bg-white/80 dark:bg-slate-900/70
                    border-white/40 dark:border-white/10
                    backdrop-blur-2xl">

            <!-- Header -->
            <div class="flex-shrink-0 flex items-center justify-between px-5 py-4
                        border-b border-black/5 dark:border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center
                                bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/20">
                        <i class="fas fa-trophy text-emerald-500 text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white leading-tight">My Collection</h3>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight">Achievement & Cosmetics</p>
                    </div>
                </div>
                <button onclick="closeBadgeModal()"
                        class="w-8 h-8 rounded-xl flex items-center justify-center
                               bg-slate-100 dark:bg-slate-800/60 hover:bg-slate-200 dark:hover:bg-slate-700
                               text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white
                               transition-all border border-black/5 dark:border-white/10">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <!-- Scrollable Content Area -->
            <div id="badge-modal-content" class="flex-1 overflow-y-auto custom-scrollbar">
                <!-- Injected via JS -->
            </div>

            <!-- Bottom Tab Navigation -->
            <div class="flex-shrink-0 flex items-center border-t border-black/5 dark:border-white/10
                        bg-white/60 dark:bg-slate-900/80 backdrop-blur-xl
                        rounded-b-3xl sm:rounded-b-3xl">
                <button onclick="switchAchievementTab('achievements')" id="tab-btn-achievements"
                        class="achievement-tab-btn flex-1 flex flex-col items-center gap-0.5 py-3 text-[10px] font-bold uppercase tracking-widest transition-all
                               text-emerald-600 dark:text-emerald-400 border-t-2 border-emerald-500">
                    <i class="fas fa-trophy text-sm"></i>
                    <span>Achievement</span>
                </button>
                <button onclick="switchAchievementTab('badges')" id="tab-btn-badges"
                        class="achievement-tab-btn flex-1 flex flex-col items-center gap-0.5 py-3 text-[10px] font-bold uppercase tracking-widest transition-all
                               text-slate-400 dark:text-slate-500 border-t-2 border-transparent">
                    <i class="fas fa-medal text-sm"></i>
                    <span>Badge</span>
                </button>
                <button onclick="switchAchievementTab('borders')" id="tab-btn-borders"
                        class="achievement-tab-btn flex-1 flex flex-col items-center gap-0.5 py-3 text-[10px] font-bold uppercase tracking-widest transition-all
                               text-slate-400 dark:text-slate-500 border-t-2 border-transparent">
                    <i class="fas fa-circle-notch text-sm"></i>
                    <span>Borders</span>
                </button>
            </div>
        </div>
    </div>




    <!-- Phase 16: Gamification Level-Up Modal -->
    <div id="level-up-modal" class="hidden fixed inset-0 z-[110] bg-slate-900/90 backdrop-blur-md flex items-center justify-center p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-gradient-to-br from-emerald-900/80 to-[#0b1426] border border-emerald-500/50 rounded-3xl w-full max-w-sm flex flex-col shadow-[0_0_50px_rgba(16,185,129,0.2)] overflow-hidden transform scale-95 transition-transform duration-500 text-center relative pointer-events-auto">
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-emerald-500/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-cyan-500/30 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="p-8 relative z-10 flex flex-col items-center">
                <div class="w-24 h-24 mb-6 rounded-full bg-emerald-500/20 border-4 border-emerald-400 flex items-center justify-center shadow-[0_0_30px_rgba(16,185,129,0.6)] animate-bounce">
                    <i class="fas fa-crown text-4xl text-emerald-300"></i>
                </div>
                <h2 class="text-3xl font-black text-white mb-2 tracking-tight uppercase bg-clip-text text-transparent bg-gradient-to-r from-emerald-300 to-cyan-300">Level Up!</h2>
                <p class="text-slate-300 mb-6 font-medium">Kamu baru saja naik ke <br><span class="text-xl text-emerald-400 font-bold" id="new-level-title">Level Baru</span></p>
                <button onclick="document.getElementById('level-up-modal').classList.add('hidden', 'opacity-0'); document.querySelector('#level-up-modal .bg-gradient-to-br').classList.remove('scale-100'); document.querySelector('#level-up-modal .bg-gradient-to-br').classList.add('scale-95');" class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-bold rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.4)] transition-all hover:scale-105 active:scale-95">
                    Lanjutkan Perjalanan <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- --- PHASE 17: BOLD STARTUP LEVEL-UP OVERLAY --- -->
    <div id="levelUpOverlay" class="level-overlay hidden">
        <div class="level-card shadow-[0_0_40px_rgba(34,197,94,0.4)]">
            <div class="level-badge font-bold">LEVEL UP</div>
            <div class="level-number" id="levelUpNumber">5</div>
            <div class="level-subtitle">You're leveling your financial engine 🚀</div>
        </div>
    </div>

    <style>
        /* Phase 16: Micro-animations */
        @keyframes floatUp {
            0% { transform: translateY(0) scale(1.1); opacity: 1; }
            100% { transform: translateY(-40px) scale(0.9); opacity: 0; }
        }
        .xp-float {
            position: absolute; left: 50%; top: -10px;
            color: #22c55e; font-weight: 800; font-size: 14px;
            text-shadow: 0 2px 10px rgba(34,197,94,0.5);
            pointer-events: none; z-index: 50;
            animation: floatUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes flamePulse {
            0%, 100% { transform: scale(1); text-shadow: 0 0 5px rgba(239,68,68,0.5); }
            50% { transform: scale(1.15) translateY(-2px); text-shadow: 0 0 15px rgba(239,68,68,0.9); }
        }

        /* Phase 17: Bold Startup Level-Up Animations */
        .level-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.75); backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; transition: opacity .2s ease;
            pointer-events: none;
        }
        .level-overlay.active { 
            opacity: 1; 
            pointer-events: auto; 
        }
        .level-card {
            background: #111827; border-radius: 18px; padding: 40px 60px;
            text-align: center; transform: translateY(30px) scale(.9);
            transition: all .3s cubic-bezier(.2,.8,.2,1);
        }
        .level-overlay.active .level-card { transform: translateY(0) scale(1); }
        .level-badge {
            font-size: 14px; letter-spacing: 2px;
            color: #22c55e; margin-bottom: 10px;
        }
        .level-number {
            font-size: 72px; font-weight: 800; color: #fff;
            animation: bounceLevel .5s ease forwards;
        }
        .level-subtitle {
            margin-top: 10px; font-size: 14px; color: #94a3b8;
            opacity: 0; animation: fadeInSub .5s ease .5s forwards;
        }
        @keyframes bounceLevel {
            0% { transform: scale(.6); }
            60% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        @keyframes fadeInSub {
            to { opacity: 1; }
        }
    </style>

    <!-- Phase 16: Floating XP CSS -->
    <style>
        .xp-float {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: 900;
            font-size: 2rem;
            color: #10b981;
            text-shadow: 0 4px 15px rgba(16, 185, 129, 0.6), 0 0 2px #fff;
            animation: floatUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            z-index: 9999;
            pointer-events: none;
        }

        @keyframes floatUp {
            0% { opacity: 0; transform: translate(-50%, 20px) scale(0.8); }
            20% { opacity: 1; transform: translate(-50%, 0px) scale(1.1); }
            100% { opacity: 0; transform: translate(-50%, -80px) scale(1); }
        }
    </style>

    <!-- Ultimate Badges Engine -->
    <script src="{{ asset('assets/js/badge-fx.js') }}"></script>
    <!-- Auth Modal Component -->
    <x-auth-modal />

    <!-- Badge Rarity Styles -->
    <style>
        .border-badge-bronze { border-color: #cd7f32 !important; box-shadow: 0 0 10px rgba(205, 127, 50, 0.3); }
        .border-badge-silver { border-color: #c0c0c0 !important; box-shadow: 0 0 10px rgba(192, 192, 192, 0.3); }
        .border-badge-gold { border-color: #ffd700 !important; box-shadow: 0 0 15px rgba(255, 215, 0, 0.4); }
        .border-badge-platinum { border-color: #e5e4e2 !important; box-shadow: 0 0 20px rgba(229, 228, 226, 0.5); }
        .border-badge-diamond { border-color: #b9f2ff !important; box-shadow: 0 0 25px rgba(185, 242, 255, 0.6); }
        .border-badge-mythic { border-color: #a855f7 !important; border-width: 3px !important; box-shadow: 0 0 30px rgba(168, 85, 247, 0.7); animation: rarity-glow 2s infinite; }

        @keyframes rarity-glow {
            0%, 100% { filter: brightness(1) drop-shadow(0 0 5px rgba(168, 85, 247, 0.5)); }
            50% { filter: brightness(1.3) drop-shadow(0 0 15px rgba(168, 85, 247, 0.8)); }
        }
    </style>

    <script src="{{ asset('assets/js/learning/zodiac-borders.js') }}"></script>
    <script src="{{ asset('assets/js/learning/elements-borders.js') }}"></script>
    <script src="{{ asset('assets/js/learning/universe-borders.js') }}"></script>
    <script src="{{ asset('assets/js/core/guards.js') }}" type="module"></script>

    <!-- Phase 18: Time Tracker Heartbeat for Engagement Achievement -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const showGodModeReward = (reward) => {
                const modalId = 'god-mode-reward-' + Date.now();
                const modal = document.createElement('div');
                modal.id = modalId;
                modal.className = 'fixed inset-0 z-[99999] flex items-center justify-center p-4 transition-all duration-500';
                modal.style.background = 'rgba(15, 23, 42, 0.85)';
                modal.style.backdropFilter = 'blur(16px)';
                modal.style.opacity = '0';

                const iconHtml = reward.item_icon 
                    ? `<img src="${reward.item_icon}" class="w-16 h-16 drop-shadow-md object-contain" alt="Reward Icon">`
                    : `<i class="fas fa-gift text-5xl text-slate-900 drop-shadow-md"></i>`;

                const xpBadge = reward.xp_bonus > 0 
                    ? `<div class="mt-4 inline-flex items-center gap-2 bg-gradient-to-r from-amber-500/10 to-transparent border border-amber-500/20 px-4 py-2 rounded-full">
                         <div class="w-6 h-6 rounded-full bg-amber-500 flex items-center justify-center text-slate-900 shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                            <i class="fas fa-bolt text-xs"></i>
                         </div>
                         <span class="text-amber-400 font-bold tracking-wider">+${reward.xp_bonus.toLocaleString('id-ID')} XP</span>
                       </div>` 
                    : '';

                modal.innerHTML = `
                    <div class="relative max-w-md w-full transform scale-90 transition-all duration-700 ease-out" style="opacity: 0;" id="${modalId}-content">
                        <!-- Glowing aura -->
                        <div class="absolute -inset-1 rounded-[2rem] blur-2xl opacity-40 animate-pulse" style="background: linear-gradient(45deg, #fbbf24, #f59e0b, #d97706, #fbbf24); background-size: 200% 200%;"></div>
                        
                        <!-- Premium Card -->
                        <div class="relative bg-slate-900 border border-amber-500/30 rounded-[2rem] p-8 text-center shadow-[0_0_80px_-15px_rgba(245,158,11,0.4)] overflow-hidden">
                            <!-- Inner glow at top -->
                            <div class="absolute top-0 left-0 right-0 h-40 bg-gradient-to-b from-amber-500/20 to-transparent pointer-events-none"></div>
                            
                            <!-- Radiant lines background -->
                            <div class="absolute inset-0 opacity-10 pointer-events-none mix-blend-overlay" style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, #f59e0b 10px, #f59e0b 20px);"></div>

                            <!-- Icon Layer -->
                            <div class="relative mb-6 mx-auto flex items-center justify-center w-28 h-28 rounded-full bg-gradient-to-br from-amber-300 to-amber-600 shadow-xl shadow-amber-500/40 border-4 border-slate-900 border-t-amber-200 z-20">
                                ${iconHtml}
                                <!-- Sparkles -->
                                <i class="fas fa-star absolute -top-1 -right-4 text-amber-300 text-2xl animate-bounce" style="filter: drop-shadow(0 0 8px #fcd34d);"></i>
                                <i class="fas fa-star absolute bottom-2 -left-3 text-amber-400 text-xl animate-pulse" style="animation-delay: 0.5s; filter: drop-shadow(0 0 8px #fcd34d);"></i>
                            </div>

                            <div class="relative z-10 space-y-3">
                                <h2 class="text-[10px] tracking-[0.4em] text-amber-500 font-bold uppercase mb-1">${reward.title || 'SYSTEM OVERRIDE'}</h2>
                                <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-b from-amber-200 via-amber-400 to-amber-600 drop-shadow-sm leading-tight border-b border-amber-500/20 pb-4 inline-block">
                                    ${reward.message || 'HADIAH EKSKLUSIF'}
                                </h1>
                                
                                <div class="bg-slate-800/80 border border-slate-700/50 rounded-xl p-4 mt-6 backdrop-blur-sm relative overflow-hidden group">
                                    <div class="absolute inset-0 bg-gradient-to-r from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <p class="text-slate-400 text-xs font-medium uppercase tracking-widest mb-1">Item Diterima</p>
                                    <p class="text-amber-200 text-xl font-bold font-display" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${reward.item_name}</p>
                                    ${xpBadge}
                                </div>
                            </div>

                            <!-- Claim Button -->
                            <button onclick="document.getElementById('${modalId}').style.opacity='0'; document.getElementById('${modalId}-content').style.transform='scale(0.9)'; setTimeout(() => document.getElementById('${modalId}').remove(), 500)" 
                                class="relative z-10 mt-8 w-full py-4 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:via-amber-300 hover:to-amber-400 text-slate-900 font-black rounded-xl shadow-[0_0_20px_rgba(245,158,11,0.3)] transition-all outline-none focus:ring-4 focus:ring-amber-500/50 hover:scale-[1.03] active:scale-95 text-lg uppercase tracking-widest overflow-hidden group">
                                <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                Klaim Hadiah
                            </button>
                        </div>
                    </div>
                `;

                // Add shimmer animation keyframes if not exists
                if (!document.getElementById('shimmer-style')) {
                    const style = document.createElement('style');
                    style.id = 'shimmer-style';
                    style.innerHTML = '@keyframes shimmer { 100% { transform: skewX(-12deg) translateX(200%); } }';
                    document.head.appendChild(style);
                }

                document.body.appendChild(modal);

                // Animate in
                setTimeout(() => {
                    modal.style.opacity = '1';
                    const content = document.getElementById(modalId + '-content');
                    if (content) {
                        content.style.opacity = '1';
                        content.style.transform = 'scale(1)';
                    }
                }, 50);
            };

            const performHeartbeat = (isInitial = false) => {
                // Hanya hitung jika tab sedang dilihat/aktif oleh user (mencegah AFK eksploitasi)
                if (document.visibilityState === 'visible') {
                    const token = localStorage.getItem('auth_token');
                    if (token) {
                        fetch('/api/me/track-time', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${token}`
                            },
                            body: JSON.stringify({ minutes: isInitial ? 0 : 1 })
                        })
                        .then(res => res.json())
                        .then(json => {
                            if (json && json.success && json.data && json.data.reward_ping) {
                                // Coba parse JSON payload jika dikirim sebagai string array oleh backend
                                let rewardData = json.data.reward_ping;
                                if (typeof json.data.reward_ping === 'string') {
                                    try {
                                        rewardData = JSON.parse(json.data.reward_ping);
                                    } catch (e) {
                                        // Fallback legacy, construct dummy object
                                        rewardData = { message: json.data.reward_ping, item_name: "Hadiah" };
                                    }
                                }
                                
                                // Tampilkan notifikasi hadiah dari si Admin menggunakan UI Khusus
                                if (typeof showGodModeReward === 'function') {
                                    showGodModeReward(rewardData);
                                } else {
                                    alert(rewardData.message || 'Anda mendapatkan hadiah Admin.');
                                }
                                // Force reload bar gamifikasi user
                                if (window.gamificationEngine) {
                                    window.gamificationEngine.fetchProgress().then(() => {
                                        window.gamificationEngine.renderUI();
                                    });
                                }
                            }
                        })
                        .catch(e => console.warn('Time track/ping failed', e));
                    }
                }
            };

            // Phase 8: Initial Ping Check on Page Load (0 minutes XP)
            setTimeout(() => performHeartbeat(true), 1500);

            // Heartbeat setiap 1 Menit (60,000 ms) - Grants 1 Minute XP
            setInterval(() => performHeartbeat(false), 60000);
        });
    </script>
        
    <script>
        // Unlock Soft Gates when user is resolved structurally via frontend API.
        window.addEventListener('auth_resolved', function(e) {
            document.querySelectorAll('.guest-blur').forEach(el => {
                el.style.maskImage = 'none';
                el.style.webkitMaskImage = 'none';
                el.classList.remove('select-none', 'pointer-events-none', 'opacity-80', 'filter');
            });
        });
    </script>
</body>


</html>
