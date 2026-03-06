<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
    <title>Mini Kursus — CuanCapital</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/icon/logo-2.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else { document.documentElement.classList.remove('dark'); }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom scrollbar */
        body { font-family: 'Space Grotesk', sans-serif; }

        /* ── COURSES PREMIUM MOBILE RESPONSIVE ─────────────────────────────── */
        @media (max-width: 1023px) {
            /* General Layout Reset */
            .grid.grid-cols-1.lg\:grid-cols-12 {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }

            main.max-w-6xl {
                padding: 12px 16px 24px 16px !important;
            }

            /* Better spacing and sizing for Navbar on mobile */
            nav .max-w-6xl {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            nav .h-16 {
                height: 56px !important;
            }

            /* ── Sidebar Redesign (Vertical Stack Instead of Horizontal Scroll) ── */
            .lg\:col-span-4 {
                position: relative !important;
                margin-bottom: 8px !important;
                overflow-x: hidden !important;
            }

            .lg\:col-span-4 > .sticky {
                position: relative !important;
                top: auto !important;
                margin-bottom: 24px !important;
            }


            .lg\:col-span-4 h1 {
                font-size: 1.25rem !important; /* Slightly larger heading */
                padding: 0 4px !important;
                line-height: 1.3 !important;
            }
            
            .lg\:col-span-4 p {
                font-size: 0.8rem !important;
            }

            /* Sidebar: remove overflow restrictions on mobile so child rows can scroll horizontally */
            #course-sidebar {
                display: flex !important;
                flex-direction: column !important;
                max-height: none !important;
                height: auto !important;
                gap: 0 !important;
                padding: 0 0 16px !important;
                overflow: unset !important;
            }

            /* Course Card adjustments */
            .course-expand-btn {
                padding: 16px 18px !important;
                border-radius: 20px !important;
            }
            
            .course-expand-btn .text-\[15px\] {
                font-size: 1rem !important; /* 16px text */
                line-height: 1.4 !important;
            }

            /* ── 3-Step Mobile Flow View States ── */

            /* VIEW 1: Categories — Show sidebar, hide lesson panel and reader */
            .mobile-view-categories .lg\:col-span-8 { display: none !important; }
            .mobile-view-categories .lg\:col-span-4 { display: block !important; }
            .mobile-view-categories #course-sidebar { display: block !important; }
            .mobile-view-categories #mobile-lesson-panel { display: none !important; }
            .mobile-view-categories #mobile-back-bar { display: none !important; }
            .mobile-view-categories #mobile-header-categories { display: block !important; }

            /* VIEW 2: Lessons Grid — Show lesson panel instead of sidebar, hide reader */
            .mobile-view-lessons .lg\:col-span-8 { display: none !important; }
            .mobile-view-lessons .lg\:col-span-4 { display: block !important; }
            .mobile-view-lessons #course-sidebar { display: none !important; }
            .mobile-view-lessons #mobile-lesson-panel { display: block !important; }
            .mobile-view-lessons #mobile-back-bar { display: block !important; }
            .mobile-view-lessons #mobile-header-categories { display: none !important; }

            /* VIEW 3: Reader — Hide left column entirely, show reader */
            .mobile-view-reader .lg\:col-span-4 { display: none !important; }
            .mobile-view-reader .lg\:col-span-8 { display: block !important; }

            /* ── Mobile Category Section Styles ── */
            .mobile-category-section {
                margin-bottom: 20px !important;
            }
            .mobile-category-label {
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
                margin-bottom: 10px !important;
                font-size: 0.68rem !important;
                font-weight: 900 !important;
                letter-spacing: 0.12em !important;
                text-transform: uppercase !important;
                color: #64748b !important;
            }
            .mobile-category-label::after {
                content: '' !important;
                flex: 1 !important;
                height: 1px !important;
                background: rgba(255,255,255,0.07) !important;
            }
            /* Horizontal course card scroll row */
            .mobile-course-row {
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                overflow-x: auto !important;
                overflow-y: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                padding-bottom: 6px !important;
                scroll-snap-type: x mandatory !important;
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: none !important;
            }
            .mobile-course-row::-webkit-scrollbar { display: none !important; }
            .mobile-course-row > * {
                flex-shrink: 0 !important;
                width: clamp(160px, 44vw, 220px) !important;
                scroll-snap-align: start !important;
            }

            /* Lesson Grid 2-col */
            #mobile-lesson-panel .mobile-lesson-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 12px !important;
            }
            #mobile-lesson-panel .mobile-lesson-card {
                background: rgba(255,255,255,0.75) !important;
                border: 1.5px solid rgba(0,0,0,0.08) !important;
                border-radius: 20px !important;
                padding: 16px 14px !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 8px !important;
                cursor: pointer !important;
                transition: all 0.25s !important;
                position: relative !important;
                overflow: hidden !important;
                min-height: 110px !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
            }
            #mobile-lesson-panel .mobile-lesson-card:active {
                transform: scale(0.96) !important;
            }
            #mobile-lesson-panel .mobile-lesson-card.done {
                border-color: rgba(52,211,153,0.4) !important;
                background: rgba(209,250,229,0.7) !important;
            }
            #mobile-lesson-panel .mobile-lesson-num {
                width: 28px !important;
                height: 28px !important;
                border-radius: 8px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 11px !important;
                font-weight: 900 !important;
                /* Default (light mode): dark on light */
                background: rgba(0,0,0,0.07) !important;
                color: #475569 !important;
            }
            /* Done state: green badge */
            #mobile-lesson-panel .mobile-lesson-card.done .mobile-lesson-num {
                background: rgba(16,185,129,0.2) !important;
                color: #059669 !important;
            }
            #mobile-lesson-panel .mobile-lesson-title {
                font-size: 0.75rem !important;
                font-weight: 800 !important;
                line-height: 1.35 !important;
                color: #1e293b !important;
                flex: 1 !important;
            }
            #mobile-lesson-panel .mobile-lesson-xp {
                font-size: 0.65rem !important;
                font-weight: 900 !important;
                color: #d97706 !important;
            }

            /* ── Dark mode: restore dark lesson card colors ── */
            html.dark #mobile-lesson-panel .mobile-lesson-card {
                background: rgba(255,255,255,0.04) !important;
                border-color: rgba(255,255,255,0.08) !important;
            }
            html.dark #mobile-lesson-panel .mobile-lesson-card.done {
                border-color: rgba(52,211,153,0.35) !important;
                background: rgba(16,185,129,0.08) !important;
            }
            html.dark #mobile-lesson-panel .mobile-lesson-num {
                background: rgba(255,255,255,0.06) !important;
                color: #94a3b8 !important;
            }
            html.dark #mobile-lesson-panel .mobile-lesson-card.done .mobile-lesson-num {
                background: rgba(16,185,129,0.2) !important;
                color: #34d399 !important;
            }
            html.dark #mobile-lesson-panel .mobile-lesson-title {
                color: #e2e8f0 !important;
            }

            /* ── Lesson Reader Redesign ── */
            .lg\:col-span-8 {
                margin-top: 0 !important;
            }

            #lesson-reader > div {
                border-radius: 24px !important;
                padding: 24px 20px !important;
                box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15) !important;
            }

            /* Initial "Mulai Petualanganmu" State */
            #lesson-reader .min-h-\[480px\] {
                min-height: 350px !important;
                padding: 32px 20px !important;
            }
            #lesson-reader .min-h-\[480px\] .w-24 {
                width: 72px !important;
                height: 72px !important;
                border-radius: 24px !important;
            }
            #lesson-reader .min-h-\[480px\] .text-4xl {
                font-size: 2rem !important;
            }
            #lesson-reader .min-h-\[480px\] h2 {
                font-size: 1.5rem !important;
                line-height: 1.3 !important;
                margin-top: 8px !important;
            }
            #lesson-reader .min-h-\[480px\] p {
                font-size: 0.95rem !important;
                margin-top: 8px !important;
            }

            /* Typography & Component Scaling within Lesson */
            .lc-h2 {
                font-size: 1.2rem !important;
                margin: 2rem 0 1rem !important;
                gap: 8px !important;
            }
            
            .lc-h2-icon {
                width: 28px !important;
                height: 28px !important;
                font-size: 11px !important;
                border-radius: 8px !important;
            }

            .lc-p {
                font-size: 0.95rem !important;
                line-height: 1.75 !important;
            }

            /* Structural Elements scaled for touch & readability */
            .cause-card {
                padding: 16px !important;
                border-radius: 16px !important;
                gap: 14px !important;
            }
            .cause-num {
                min-width: 30px !important;
                height: 30px !important;
                font-size: 12px !important;
            }
            .cause-title {
                font-size: 0.9rem !important;
            }
            .cause-desc {
                font-size: 0.85rem !important;
                line-height: 1.6 !important;
            }

            .formula-card {
                padding: 20px !important;
                border-radius: 16px !important;
                margin: 1.5rem 0 !important;
            }
            .formula-text {
                font-size: 0.9rem !important;
                word-wrap: break-word !important; /* ensure long formulas wrap */
            }

            .action-step {
                padding: 14px 16px !important;
                border-radius: 16px !important;
                font-size: 0.85rem !important;
                line-height: 1.6 !important;
                gap: 14px !important;
            }
            .action-checkbox {
                min-width: 22px !important;
                height: 22px !important;
            }

            .lc-callout {
                padding: 16px 18px !important;
                border-radius: 16px !important;
                font-size: 0.85rem !important;
            }

            .lc-case-study, .lc-infographic, .lc-challenge {
                padding: 20px !important;
                border-radius: 20px !important;
                margin: 1.75rem 0 !important;
            }

            .lc-info-card {
                padding: 16px !important;
                border-radius: 16px !important;
                gap: 14px !important;
                flex-direction: column !important; /* Stack icon and text on mobile */
                align-items: flex-start !important;
                text-align: left !important;
            }

            .lc-info-icon {
                width: 40px !important;
                height: 40px !important;
                font-size: 16px !important;
                margin-bottom: 4px !important;
            }

            .lc-quiz-card {
                padding: 20px !important;
                border-radius: 20px !important;
            }
            .lc-quiz-opt {
                padding: 14px 16px !important;
                font-size: 0.9rem !important;
                border-radius: 14px !important;
            }

            .lc-table {
                display: block !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                border-radius: 12px !important;
                width: 100% !important;
            }
            .lc-table th, .lc-table td {
                padding: 12px 14px !important;
                font-size: 0.8rem !important;
                white-space: nowrap !important;
            }

            .lc-chart-wrapper {
                padding: 16px !important;
                border-radius: 16px !important;
            }

            /* Lesson navigation buttons - Stack full width for easy tapping */
            .flex.justify-between.items-center.mt-8,
            .flex.justify-between.mt-8 {
                flex-direction: column !important;
                gap: 12px !important;
            }

            .flex.justify-between.items-center.mt-8 button,
            .flex.justify-between.mt-8 button {
                width: 100% !important;
                min-height: 52px !important; /* Large touch target */
                border-radius: 16px !important;
                font-size: 0.95rem !important;
                justify-content: center !important;
            }

            /* Background blobs: minimize distraction and improve performance */
            .fixed.inset-0 .animate-blob {
                opacity: 0.3 !important;
                filter: blur(80px) !important; /* Ensure blur is active */
            }
        }

        /* ── Extra Small Phones (iPhone SE, etc.) ── */
        @media (max-width: 400px) {
            main.max-w-6xl {
                padding: 10px 12px 20px 12px !important;
            }

            .lc-h2 {
                font-size: 1.15rem !important;
            }

            #lesson-reader > div {
                border-radius: 20px !important;
                padding: 20px 16px !important;
            }

            .course-expand-btn {
                padding: 14px 16px !important;
            }
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1) rotate(0deg); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            33% { transform: translate(40px, -60px) scale(1.1) rotate(15deg); border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            66% { transform: translate(-30px, 30px) scale(0.9) rotate(-10deg); border-radius: 40% 60% 60% 40% / 50% 60% 40% 50%; }
            100% { transform: translate(0px, 0px) scale(1) rotate(0deg); border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
        }
        .animate-blob { animation: blob 18s ease-in-out infinite alternate; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 5s; }

        @keyframes cardIn {
            0% { opacity: 0; transform: translateY(24px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-card-in { animation: cardIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; opacity: 0; }

        @keyframes lc-pulse-glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.4); }
            50% { box-shadow: 0 0 0 12px rgba(74, 222, 128, 0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* ── Desktop: restore sidebar vertical scroll (removed from Tailwind HTML for mobile fix) ── */
        @media (min-width: 1024px) {
            #course-sidebar {
                max-height: calc(100vh - 160px);
                overflow-y: auto;
            }
        }

        /* 3D Button Press Effect */
        .btn-3d {
            transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative; top: 0;
            box-shadow: 0 6px 0 -1px rgba(0,0,0,0.2), 0 8px 16px rgba(0,0,0,0.1);
        }
        .btn-3d:active:not(:disabled) {
            top: 4px; box-shadow: 0 2px 0 -1px rgba(0,0,0,0.2), 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Animated Striped Progress Bar */
        @keyframes stripes {
            from { background-position: 1rem 0; }
            to { background-position: 0 0; }
        }
        .progress-bar-striped {
            background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
            background-size: 1rem 1rem;
            animation: stripes 1s linear infinite;
        }
        .anim-section { opacity: 0; animation: fadeSlideIn 0.5s ease-out forwards; }

        /* ════════════════════════════════════════════════════════════
           LIGHT MODE — fix all JS-generated dark-hardcoded elements
           ════════════════════════════════════════════════════════════ */
        html:not(.dark) {
            /* Sidebar section headings */
            #mobile-header-categories h1 { color: #1e293b !important; }
            #mobile-header-categories p  { color: #64748b !important; }
            #mobile-panel-title           { color: #1e293b !important; }
            #mobile-back-bar button       { color: #475569 !important; }

            /* Category label in mobile grouped view */
            .mobile-category-label { color: #475569 !important; }

            /* Course card: ensure text contrast on light bg */
            .course-expand-btn       { background: rgba(255,255,255,0.75) !important; }

            /* Lesson list items in sidebar accordion */
            .sidebar-lesson-btn             { background: rgba(255,255,255,0.65) !important; }
            .sidebar-lesson-btn p           { color: #1e293b !important; }
            .sidebar-lesson-btn p.text-\[10px\] { color: #64748b !important; }

            /* Lesson number circle (not-done) */
            .sidebar-lesson-btn > div:first-child {
                background: #e2e8f0 !important;
                color: #475569 !important;
            }

            /* Lesson reader: title and content */
            #lesson-reader h2.text-3xl { color: #0f172a !important; }
            #lesson-reader .rounded-\[30px\] { background: rgba(255,255,255,0.75) !important; }

            /* Simulation / master inline dark cards override */
            .lesson-list .rounded-2xl,
            .lesson-list .rounded-xl[class*="gradient"] { 
                background: rgba(255,255,255,0.7) !important;
            }

            /* Locked simulation button */
            .lesson-list button[disabled] {
                background: rgba(0,0,0,0.05) !important;
                border-color: rgba(0,0,0,0.1) !important;
            }
            .lesson-list button[disabled] p { color: #94a3b8 !important; }

            /* Mobile lesson chooser panel */
            #lesson-reader div[class*="rounded-3xl"] {
                background: rgba(255,255,255,0.75) !important;
            }
            #lesson-reader .lesson-chooser-card {
                background: rgba(255,255,255,0.65) !important;
                border-color: rgba(0,0,0,0.08) !important;
            }
            #lesson-reader .lesson-chooser-card p { color: #334155 !important; }

            /* Simulation inline card title text */
            #lesson-reader p.text-xs.font-black { color: #0f172a !important; }

            /* ══ Lesson Content Card Components ══ */

            /* Cause/reason cards */
            .cause-card {
                background: rgba(255,255,255,0.75) !important;
                border-color: rgba(0,0,0,0.08) !important;
            }
            .cause-title { color: #1e293b !important; }
            .cause-desc  { color: #64748b !important; }

            /* Formula card */
            .formula-card {
                background: linear-gradient(135deg, rgba(240,253,244,0.9), rgba(236,253,245,0.95)) !important;
                border-color: rgba(16,185,129,0.3) !important;
                box-shadow: 0 8px 32px rgba(0,0,0,0.06) !important;
            }

            /* Action steps */
            .action-step {
                background: rgba(240,253,244,0.7) !important;
                border-color: rgba(16,185,129,0.25) !important;
                color: #334155 !important;
            }

            /* Callout boxes */
            .lc-callout {
                background: rgba(255,255,255,0.75) !important;
                border-color: rgba(0,0,0,0.06) !important;
                color: #475569 !important;
            }
            .lc-callout strong { color: #1e293b !important; }
            .lc-callout.tip  { background: linear-gradient(90deg, rgba(16,185,129,0.08) 0%, rgba(255,255,255,0.6) 100%) !important; }
            .lc-callout.warn { background: linear-gradient(90deg, rgba(245,158,11,0.08) 0%, rgba(255,255,255,0.6) 100%) !important; }

            /* Case study card */
            .lc-case-study {
                background: linear-gradient(135deg, rgba(239,246,255,0.9), rgba(255,255,255,0.8)) !important;
                border-color: rgba(59,130,246,0.2) !important;
                box-shadow: 0 8px 30px rgba(59,130,246,0.08) !important;
            }

            /* Infographic */
            .lc-infographic {
                background: rgba(255,255,255,0.75) !important;
                border-color: rgba(0,0,0,0.07) !important;
            }
            .lc-info-card {
                background: rgba(255,255,255,0.7) !important;
                border-color: rgba(56,189,248,0.2) !important;
            }

            /* Quiz card */
            .lc-quiz-card {
                background: rgba(255,255,255,0.75) !important;
                border-color: rgba(0,0,0,0.07) !important;
            }
            .lc-quiz-opt {
                background: rgba(248,250,252,0.8) !important;
                border-color: rgba(0,0,0,0.08) !important;
                color: #334155 !important;
            }
            .lc-quiz-opt:hover {
                background: rgba(239,246,255,0.9) !important;
                border-color: rgba(59,130,246,0.35) !important;
                color: #1e293b !important;
            }

            /* Challenge card */
            .lc-challenge {
                background: linear-gradient(135deg, rgba(245,243,255,0.9), rgba(255,255,255,0.8)) !important;
                border-color: rgba(139,92,246,0.3) !important;
                box-shadow: 0 8px 30px rgba(139,92,246,0.08) !important;
            }

            /* Chart wrapper */
            .lc-chart-wrapper {
                background: #f8fafc !important;
                border-color: #e2e8f0 !important;
            }

            /* List items */
            .lc-list { border-top-color: rgba(0,0,0,0.06) !important; }
            .lc-list-item {
                color: #475569 !important;
                border-bottom-color: rgba(0,0,0,0.05) !important;
            }

            /* Image border */
            .lc-image { border-color: rgba(0,0,0,0.06) !important; }

            /* ── Text inside JS-generated card content ── */

            /* Case study: title and body text */
            .lc-case-study h4 { color: #1e293b !important; }
            .lc-case-study > div:last-child,
            .lc-case-study .text-slate-300 { color: #475569 !important; }
            .lc-case-study [class*="border-l-2"] { color: #475569 !important; }

            /* Challenge text */
            .lc-challenge h4,
            .lc-challenge [class*="text-slate"] { color: #334155 !important; }
            .lc-challenge [class*="text-white"]:not(i):not(button) { color: #1e293b !important; }

            /* Quiz: question text */
            .lc-quiz-card > p,
            .lc-quiz-card [class*="text-slate"] { color: #334155 !important; font-weight: 600 !important; }

            /* Info cards: title + description */
            .lc-info-card [class*="text-sky"],
            .lc-info-card [class*="text-blue"],
            .lc-info-card [class*="font-bold"] { color: #0f172a !important; }
            .lc-info-card [class*="text-slate"] { color: #475569 !important; }
            .lc-infographic > p,
            .lc-infographic h4 { color: #334155 !important; }

            /* Action step text */
            .action-step span,
            .action-step p { color: #334155 !important; }

            /* ── Custom lc- hooks for JS-generated card content ── */
            /* These classes were added to JS template literals as CSS hooks */
            .lc-card-title,
            h4.lc-card-title,
            h5.lc-card-title  { color: #1e293b !important; }
            .lc-card-body,
            div.lc-card-body,
            p.lc-card-body     { color: #475569 !important; }
            .lc-blue-label     { color: #2563eb !important; }

            /* Quiz badge and question — force dark text */
            .lc-quiz-badge {
                background: #f1f5f9 !important;
                color: #64748b !important;
            }
            h4.lc-quiz-question,
            .lc-quiz-question  { 
                color: #0f172a !important;
                font-weight: 700 !important;
            }

            /* Challenge card labels */
            h4.lc-card-title,
            .lc-challenge-label { color: #7c3aed !important; }
            /* Challenge h4 specifically */
            .lc-challenge h4.lc-card-title { color: #1e293b !important; }
        }

        /* ─── Lesson Visual Components ─────────────────────────────────── */

        /* Section headers */
        .lc-h2 {
            display: flex; align-items: center; gap: 10px;
            font-size: 1.25rem; font-weight: 800; color: #0f172a;
            margin: 2.5rem 0 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .dark .lc-h2 { color: #fff; border-bottom-color: rgba(255,255,255,0.06); }
        .lc-h2-icon {
            width: 32px; height: 32px; border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; color: white; shrink-0: 0;
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
        }

        /* Prose paragraphs */
        .lc-p {
            color: #475569; line-height: 1.8; font-size: 0.95rem; font-weight: 500;
            margin: 0.75rem 0;
        }
        .dark .lc-p { color: #94a3b8; font-weight: 400; }
        .lc-p strong { color: #0f172a; font-weight: 800; }
        .dark .lc-p strong { color: #f1f5f9; }

        /* Numbered cause cards */
        .cause-cards { display: flex; flex-direction: column; gap: 10px; margin: 0.75rem 0; }
        .cause-card {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 16px 18px;
            background: rgba(255,255,255,0.02);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 2px solid rgba(255,255,255,0.08); /* Thicker border for playfulness */
            box-shadow: 0 4px 15px -3px rgba(0,0,0,0.1);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
        }
        .cause-card:hover { border-color: rgba(56,189,248,0.5); transform: translateY(-4px) scale(1.01); box-shadow: 0 12px 25px -5px rgba(56,189,248,0.2); background: rgba(56,189,248,0.05); }
        .cause-num {
            min-width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 900; color: white; shrink: 0;
        }
        .cause-num-1 { background: linear-gradient(135deg, #ef4444, #f97316); }
        .cause-num-2 { background: linear-gradient(135deg, #f59e0b, #eab308); }
        .cause-num-3 { background: linear-gradient(135deg, #8b5cf6, #6366f1); }
        .cause-num-4 { background: linear-gradient(135deg, #10b981, #06b6d4); }
        .cause-num-5 { background: linear-gradient(135deg, #ec4899, #f43f5e); }
        .cause-title { font-size: 0.85rem; font-weight: 700; color: #f1f5f9; margin-bottom: 2px; }
        .cause-desc  { font-size: 0.8rem; color: #94a3b8; line-height: 1.5; }

        /* Formula card */
        .formula-card {
            position: relative;
            background: linear-gradient(135deg, rgba(15,23,42,0.6) 0%, rgba(13,24,41,0.7) 100%);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 16px;
            padding: 20px 24px;
            margin: 1.25rem 0;
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .formula-card::before {
            content: 'FORMULA';
            position: absolute; top: 12px; right: 14px;
            font-size: 9px; font-weight: 900; letter-spacing: 0.12em;
            color: #10b981; opacity: 0.5;
        }
        .formula-card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, #10b981, #06b6d4, #8b5cf6);
        }
        .formula-label {
            font-size: 10px; font-weight: 700; color: #10b981;
            text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 10px;
        }
        .formula-text {
            font-family: 'Courier New', monospace;
            font-size: 0.95rem; color: #34d399; line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Minimalist List (replaces generic bullets) */
        .lc-list { display: flex; flex-direction: column; width: 100%; border-top: 1px solid rgba(255,255,255,0.05); margin: 0.75rem 0; }
        .lc-list-item {
            padding: 12px 4px;
            font-size: 0.85rem; color: #94a3b8; line-height: 1.6;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: flex-start; gap: 12px;
        }
        .lc-list-item::before {
            content: ''; display: block; width: 4px; height: 14px;
            background: #10b981; border-radius: 4px; margin-top: 4px; opacity: 0.7;
        }

        /* Action step checklist */
        .action-steps { display: flex; flex-direction: column; gap: 8px; margin: 0.75rem 0; }
        .action-step {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 16px;
            background: rgba(16,185,129,0.03);
            backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            border: 2px solid rgba(16,185,129,0.15);
            border-radius: 16px;
            font-size: 0.85rem; color: #cbd5e1; line-height: 1.5; font-weight: 500;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
        }
        .action-step:hover { background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.4); transform: translateX(6px) scale(1.01); }
        .action-checkbox {
            min-width: 18px; height: 18px; border-radius: 5px;
            border: 2px solid rgba(16,185,129,0.4);
            display: flex; align-items: center; justify-content: center;
            margin-top: 1px;
            cursor: pointer; transition: all 0.2s;
        }
        .action-checkbox.checked {
            background: #10b981; border-color: #10b981;
        }
        .action-checkbox.checked::after { content: '✓'; font-size: 10px; color: white; font-weight: 900; }

        /* Sleek Callout Box (replaces emoji tips) */
        .lc-callout {
            padding: 16px 20px; margin: 1.5rem 0;
            background: rgba(255,255,255,0.02);
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.05);
            border-left: 3px solid #3b82f6;
            border-radius: 12px;
            color: #cbd5e1; font-size: 0.85rem; line-height: 1.6;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .lc-callout strong { color: #fff; }
        .lc-callout.tip { border-left-color: #10b981; background: linear-gradient(90deg, rgba(16,185,129,0.08) 0%, transparent 100%); }
        .lc-callout.warn { border-left-color: #f59e0b; background: linear-gradient(90deg, rgba(245,158,11,0.08) 0%, transparent 100%); }

        /* Premium Subheaders (replaces raw H3) */
        .lc-h3 {
            font-size: 0.8rem; font-weight: 800; letter-spacing: 0.1em;
            text-transform: uppercase; color: #64748b;
            margin: 2rem 0 1rem 0; display: flex; align-items: center; gap: 10px;
        }
        .dark .lc-h3 { color: #94a3b8; }
        .lc-h3::after { content: ''; flex: 1; height: 1px; background: rgba(0,0,0,0.1); }
        .dark .lc-h3::after { background: rgba(255,255,255,0.1); }

        /* Chart Container */
        .lc-chart-wrapper {
            background: #0f172a; border: 1px solid #1e293b; border-radius: 16px;
            padding: 20px; margin: 1.5rem 0;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
        }

        /* Table styled */
        .lc-table { width: 100%; border-collapse: collapse; margin: 1rem 0; border-radius: 12px; overflow: hidden; background: rgba(255,255,255,0.4); backdrop-filter: blur(8px); border: 1px solid rgba(0,0,0,0.05); }
        .dark .lc-table { background: rgba(15,23,42,0.4); border-color: rgba(255,255,255,0.05); }
        .lc-table th { background: rgba(0,0,0,0.03); color: #334155; font-size: 0.8rem; font-weight: 800; padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05); letter-spacing: 0.05em; text-transform: uppercase; }
        .dark .lc-table th { background: rgba(255,255,255,0.03); color: #e2e8f0; border-bottom-color: rgba(255,255,255,0.05); }
        .lc-table td { color: #475569; font-size: 0.85rem; padding: 12px 14px; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .dark .lc-table td { color: #94a3b8; border-bottom-color: rgba(255,255,255,0.03); }
        .lc-table tr:hover td { background: rgba(0,0,0,0.01); color: #0f172a; }
        .dark .lc-table tr:hover td { background: rgba(255,255,255,0.01); color: #cbd5e1; }
        .lc-table td strong { color: #0f172a; }
        .dark .lc-table td strong { color: #e2e8f0; }

        /* Code inline */
        .lc-code { background: rgba(16,185,129,0.1); color: #059669; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem; font-family: monospace; font-weight: 600; border: 1px solid rgba(16,185,129,0.2); }
        .dark .lc-code { background: #0f172a; color: #34d399; border-color: transparent; }

        /* Separator */
        .lc-sep { height: 1px; background: rgba(0,0,0,0.05); margin: 2rem 0; }
        .dark .lc-sep { background: rgba(255,255,255,0.05); }

        /* Progress XP header */
        .lesson-xp-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px;
            background: rgba(251,191,36,0.12); border: 1px solid rgba(251,191,36,0.25);
            font-size: 11px; font-weight: 700; color: #fbbf24;
        }
        .lesson-time-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px;
            background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.15);
            font-size: 11px; color: #94a3b8;
        }
        .lesson-done-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px;
            background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25);
            font-size: 11px; font-weight: 700; color: #34d399;
        }

        /* ─── Storytelling Interactive CSS ─── */
        @keyframes lc-pulse-glow { 0% { box-shadow: 0 0 0 0 rgba(59,130,246,0.4); } 70% { box-shadow: 0 0 0 10px rgba(59,130,246,0); } 100% { box-shadow: 0 0 0 0 rgba(59,130,246,0); } }
        @keyframes lc-shake { 0%, 100% { transform: translateX(0); } 20%, 60% { transform: translateX(-5px); } 40%, 80% { transform: translateX(5px); } }
        
        .lc-case-study {
            background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.01));
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(59,130,246,0.25); border-radius: 20px;
            padding: 24px; margin: 2rem 0; position: relative; overflow: hidden;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .lc-case-study::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 50%); pointer-events: none;
        }
        .lc-infographic {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); 
            backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-radius: 20px; padding: 20px; margin: 2rem 0;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.2);
        }
        .lc-info-card {
            background: rgba(255,255,255,0.03); border: 2px solid rgba(56,189,248,0.2);
            border-radius: 18px; padding: 16px; margin-bottom: 12px;
            display: flex; gap: 16px; align-items: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .lc-info-card:hover { transform: translateX(8px) scale(1.02); background: rgba(255,255,255,0.06); border-color: rgba(56,189,248,0.5); box-shadow: 0 8px 25px rgba(56,189,248,0.15); }
        .lc-info-icon {
            width: 48px; height: 48px; border-radius: 16px; background: linear-gradient(135deg, #0284c7, #38bdf8);
            display: flex; align-items: center; justify-content: center; font-size: 20px; color: white; shrink: 0; box-shadow: 0 6px 20px rgba(2,132,199,0.4);
            animation: float 4s ease-in-out infinite;
        }
        .lc-quiz-card {
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-radius: 20px; padding: 24px; margin: 2rem 0;
            transition: all 0.3s; box-shadow: 0 8px 32px 0 rgba(0,0,0,0.2);
        }
        .lc-quiz-opt {
            background: rgba(255,255,255,0.03); border: 2px solid rgba(255,255,255,0.05); border-radius: 16px;
            padding: 14px 18px; margin-top: 10px; cursor: pointer; color: #94a3b8; font-size: 0.9rem; font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; gap: 12px;
            box-shadow: 0 4px 0 -1px rgba(0,0,0,0.1); position: relative; top: 0;
        }
        .lc-quiz-opt:hover { background: rgba(59,130,246,0.08); border-color: rgba(59,130,246,0.4); color: #cbd5e1; transform: translateY(-2px); box-shadow: 0 6px 0 -1px rgba(0,0,0,0.1), 0 8px 16px rgba(59,130,246,0.1); }
        .lc-quiz-opt:active { top: 2px; transform: translateY(0); box-shadow: 0 2px 0 -1px rgba(0,0,0,0.1); }
        .lc-quiz-opt.correct { background: rgba(16,185,129,0.15); border-color: #10b981; color: #fff; box-shadow: 0 4px 0 -1px rgba(16,185,129,0.4), 0 0 20px rgba(16,185,129,0.3); }
        .lc-quiz-opt.wrong { background: rgba(225,29,72,0.15); border-color: #e11d48; color: #fff; box-shadow: 0 4px 0 -1px rgba(225,29,72,0.4); animation: lc-shake 0.4s; }
        .lc-challenge {
            background: linear-gradient(135deg, rgba(139,92,246,0.08), rgba(255,255,255,0.02));
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(139,92,246,0.4); border-radius: 20px;
            padding: 24px; margin: 2.5rem 0;
            position: relative; overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.05);
        }
        .lc-image {
            width: 100%; border-radius: 16px; margin: 1.5rem 0;
            border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);
            transition: transform 0.4s ease;
        }
        .lc-image:hover { transform: translateY(-4px) scale(1.01); }
    </style>
</head>

<body class="bg-slate-50 dark:bg-[#020817] text-slate-800 dark:text-slate-200 min-h-screen relative overflow-x-hidden selection:bg-fuchsia-500/30" style="overflow-x:hidden">

    <!-- Animated Playful Glassmorphism Background -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[45vw] h-[45vw] rounded-[40%_60%_70%_30%/40%_50%_60%_50%] bg-fuchsia-500/15 dark:bg-fuchsia-600/15 blur-[120px] mix-blend-multiply dark:mix-blend-color-dodge opacity-80 animate-blob"></div>
        <div class="absolute top-[20%] right-[-10%] w-[40vw] h-[40vw] rounded-[60%_40%_30%_70%/60%_30%_70%_40%] bg-amber-400/15 dark:bg-amber-500/15 blur-[120px] mix-blend-multiply dark:mix-blend-color-dodge opacity-80 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[50vw] h-[50vw] rounded-[40%_60%_60%_40%/50%_60%_40%_50%] bg-cyan-400/15 dark:bg-cyan-500/15 blur-[120px] mix-blend-multiply dark:mix-blend-color-dodge opacity-80 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl border-b border-slate-200/50 dark:border-white/10 shadow-sm dark:shadow-[0_4px_30px_rgba(0,0,0,0.1)] transition-all duration-300">
        <div class="max-w-6xl mx-auto h-16 flex items-center justify-between">
            <a href="{{ route('index') }}" class="flex items-center gap-2 text-slate-400 hover:text-white transition-colors group">
                <i class="fas fa-arrow-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                <img loading="lazy" src="{{ asset('assets/icon/logo-2.svg') }}" alt="CuanCapital" class="w-6 h-6">
                <span class="text-sm font-bold hidden sm:inline">CuanCapital</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-emerald-400 text-[10px]"></i>
                </div>
                <span class="text-sm font-bold text-white">Mini Kursus</span>
            </div>
            <!-- Gamification XP Bar UI (Mirrored from Main Page) -->
            <div class="relative z-50 mr-2 md:mr-4">
                <div class="xp-container flex flex-col justify-center w-28 sm:w-32 md:w-36 lg:w-48 cursor-pointer group" onclick="if(typeof openBadgeModal === 'function') openBadgeModal()">
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
        </div>
    </nav>

    <!-- Page Body -->
    <main class="max-w-6xl mx-auto py-8 lg:py-10 px-4 lg:px-6">
        <div id="mobile-flow-wrapper" class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mobile-view-categories">

            <!-- LEFT: Course & Lesson List -->
            <div class="lg:col-span-4">
                <div class="sticky top-20">
                    <!-- Mobile: Back button (hidden by default, shown in lessons view) -->
                    <div id="mobile-back-bar" class="hidden lg:hidden mb-4">
                        <button id="mobile-back-btn" class="flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-white transition-colors">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span id="mobile-back-label">Kembali ke Modul</span>
                        </button>
                        <h2 id="mobile-panel-title" class="text-xl font-black text-white mt-2"></h2>
                    </div>
                    
                    <!-- Desktop title (hidden on mobile when in lessons view) -->
                    <div id="mobile-header-categories" class="mb-4">
                        <h1 class="text-lg font-black text-white">Kursus Bisnis Digital</h1>
                        <p class="text-xs text-slate-400 mt-1">Pilih kursus untuk mulai belajar dan dapat XP.</p>
                    </div>
                    
                    <!-- Course Sidebar (Categories view) -->
                    <div id="course-sidebar" class="space-y-3 pr-1 pb-4 lg:block"></div>

                    <!-- Mobile: Lesson Grid Panel (Lessons view) -->
                    <div id="mobile-lesson-panel" class="hidden pb-4"></div>
                </div>
            </div>


            <!-- RIGHT: Lesson Reader -->
            <div class="lg:col-span-8">
                <div id="lesson-reader" class="min-h-[500px]">
                    <div class="flex flex-col items-center justify-center min-h-[480px] text-center p-8 overflow-hidden relative
                                rounded-3xl border border-white/20 dark:border-white/10 bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl shadow-[0_8px_32px_rgba(0,0,0,0.1)]">
                        <div class="absolute inset-0 bg-gradient-to-br from-fuchsia-500/5 to-transparent pointer-events-none"></div>
                        <div class="w-24 h-24 rounded-[30px] bg-fuchsia-500/10 border-2 border-fuchsia-500/20
                                    flex items-center justify-center mb-6 relative z-10 transition-transform hover:scale-110 hover:rotate-6 duration-300 shadow-[0_10px_30px_rgba(217,70,239,0.2)]">
                            <i class="fas fa-gamepad text-4xl text-fuchsia-600 dark:text-fuchsia-400"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-3 relative z-10 tracking-tight">Mulai Petualanganmu!</h2>
                        <p class="text-slate-600 dark:text-slate-400 text-[15px] max-w-sm relative z-10 leading-relaxed font-medium">Pilih kursus di samping untuk mulai bermain dan belajar. Kumpulkan XP dan raih badge baru!</p>
                        <div class="mt-8 flex items-center gap-4 flex-wrap justify-center relative z-10">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 flex items-center gap-1.5 shadow-sm"><i class="fas fa-star"></i> +XP</span>
                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-500/20 dark:text-violet-400 flex items-center gap-1.5 shadow-sm"><i class="fas fa-rocket"></i> Level Up</span>
                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center gap-1.5 shadow-sm"><i class="fas fa-certificate"></i> Badge</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div id="xp-toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="{{ asset('assets/js/learning/gamification-config.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/learning/gamification-engine.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/js/learning/learning-hub.js') }}?v={{ time() }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        class CoursesPageHub extends LearningHub {
            constructor() {
                super(null);
                this.sidebarEl = document.getElementById('course-sidebar');
                this.readerEl  = document.getElementById('lesson-reader');
                // ──  Realtime client-side completion cache ──────────────────
                // Maps lesson_id → true; populated when lessons load and
                // updated immediately on successful completion so we never
                // need a page-refresh to reflect the new state.
                this.completedLessonIds = new Set();
                this._courseProgressCache = {}; // { courseId: { done, total } }
                this._initPage();
            }

            _getLevelStyle(level) {
                const levelStyles = { 
                    beginner: {
                        badge: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                        borderHover: 'hover:border-emerald-400/50 dark:hover:border-emerald-500/50',
                        textHover: 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400',
                        iconHoverBg: 'group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/20',
                        iconHoverText: 'group-hover:text-emerald-500',
                        progress: 'bg-gradient-to-r from-emerald-500 to-teal-400 shadow-[0_0_10px_rgba(16,185,129,0.5)]',
                        lessonDoneBg: 'bg-emerald-50 dark:bg-emerald-500/15 border-2 border-emerald-400/40',
                        lessonDoneIcon: 'bg-emerald-400 text-white shadow-[0_4px_15px_rgba(52,211,153,0.5)]',
                        lessonDoneText: 'text-emerald-700 dark:text-emerald-300',
                        lessonDonePlay: 'text-emerald-500',
                        lessonHoverBg: 'hover:bg-emerald-50/50 dark:hover:bg-slate-800/80',
                        lessonHoverBorder: 'hover:border-emerald-400/40 dark:hover:border-emerald-500/40',
                        lessonHoverPlayIcon: 'group-hover:text-emerald-500'
                    },
                    intermediate: {
                        badge: 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                        borderHover: 'hover:border-blue-400/50 dark:hover:border-blue-500/50',
                        textHover: 'group-hover:text-blue-600 dark:group-hover:text-blue-400',
                        iconHoverBg: 'group-hover:bg-blue-100 dark:group-hover:bg-blue-500/20',
                        iconHoverText: 'group-hover:text-blue-500',
                        progress: 'bg-gradient-to-r from-blue-500 to-cyan-400 shadow-[0_0_10px_rgba(59,130,246,0.5)]',
                        lessonDoneBg: 'bg-blue-50 dark:bg-blue-500/15 border-2 border-blue-400/40',
                        lessonDoneIcon: 'bg-blue-400 text-white shadow-[0_4px_15px_rgba(59,130,246,0.5)]',
                        lessonDoneText: 'text-blue-700 dark:text-blue-300',
                        lessonDonePlay: 'text-blue-500',
                        lessonHoverBg: 'hover:bg-blue-50/50 dark:hover:bg-slate-800/80',
                        lessonHoverBorder: 'hover:border-blue-400/40 dark:hover:border-blue-500/40',
                        lessonHoverPlayIcon: 'group-hover:text-blue-500'
                    },
                    advanced: {
                        badge: 'text-rose-400 bg-rose-500/10 border-rose-500/20',
                        borderHover: 'hover:border-rose-400/50 dark:hover:border-rose-500/50',
                        textHover: 'group-hover:text-rose-600 dark:group-hover:text-rose-400',
                        iconHoverBg: 'group-hover:bg-rose-100 dark:group-hover:bg-rose-500/20',
                        iconHoverText: 'group-hover:text-rose-500',
                        progress: 'bg-gradient-to-r from-rose-500 to-pink-400 shadow-[0_0_10px_rgba(225,29,72,0.5)]',
                        lessonDoneBg: 'bg-rose-50 dark:bg-rose-500/15 border-2 border-rose-400/40',
                        lessonDoneIcon: 'bg-rose-400 text-white shadow-[0_4px_15px_rgba(225,29,72,0.5)]',
                        lessonDoneText: 'text-rose-700 dark:text-rose-300',
                        lessonDonePlay: 'text-rose-500',
                        lessonHoverBg: 'hover:bg-rose-50/50 dark:hover:bg-slate-800/80',
                        lessonHoverBorder: 'hover:border-rose-400/40 dark:hover:border-rose-500/40',
                        lessonHoverPlayIcon: 'group-hover:text-rose-500'
                    },
                    expert: {
                        badge: 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                        borderHover: 'hover:border-amber-400/50 dark:hover:border-amber-500/50',
                        textHover: 'group-hover:text-amber-600 dark:group-hover:text-amber-400',
                        iconHoverBg: 'group-hover:bg-amber-100 dark:group-hover:bg-amber-500/20',
                        iconHoverText: 'group-hover:text-amber-500',
                        progress: 'bg-gradient-to-r from-amber-500 to-orange-400 shadow-[0_0_10px_rgba(245,158,11,0.5)]',
                        lessonDoneBg: 'bg-amber-50 dark:bg-amber-500/15 border-2 border-amber-400/40',
                        lessonDoneIcon: 'bg-amber-400 text-white shadow-[0_4px_15px_rgba(245,158,11,0.5)]',
                        lessonDoneText: 'text-amber-700 dark:text-amber-300',
                        lessonDonePlay: 'text-amber-500',
                        lessonHoverBg: 'hover:bg-amber-50/50 dark:hover:bg-slate-800/80',
                        lessonHoverBorder: 'hover:border-amber-400/40 dark:hover:border-amber-500/40',
                        lessonHoverPlayIcon: 'group-hover:text-amber-500'
                    },
                    master: {
                        badge: 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                        borderHover: 'hover:border-violet-400/50 dark:hover:border-violet-500/50',
                        textHover: 'group-hover:text-violet-600 dark:group-hover:text-violet-400',
                        iconHoverBg: 'group-hover:bg-violet-100 dark:group-hover:bg-violet-500/20',
                        iconHoverText: 'group-hover:text-violet-500',
                        progress: 'bg-gradient-to-r from-violet-500 to-purple-400 shadow-[0_0_10px_rgba(139,92,246,0.5)]',
                        lessonDoneBg: 'bg-violet-50 dark:bg-violet-500/15 border-2 border-violet-400/40',
                        lessonDoneIcon: 'bg-violet-400 text-white shadow-[0_4px_15px_rgba(139,92,246,0.5)]',
                        lessonDoneText: 'text-violet-700 dark:text-violet-300',
                        lessonDonePlay: 'text-violet-500',
                        lessonHoverBg: 'hover:bg-violet-50/50 dark:hover:bg-slate-800/80',
                        lessonHoverBorder: 'hover:border-violet-400/40 dark:hover:border-violet-500/40',
                        lessonHoverPlayIcon: 'group-hover:text-violet-500'
                    }
                };
                return levelStyles[level] || levelStyles.beginner;
            }

            async _initPage() {
                this._renderSidebarSkeleton();
                await this.loadSidebarCourses();
                
                // Delegation for Interactive Quizzes
                document.body.addEventListener('click', (e) => {
                    const opt = e.target.closest('.lc-quiz-opt');
                    if (!opt) return;

                    const card = opt.closest('.lc-quiz-card');
                    if (card.dataset.answered === 'true') return;

                    const correctIdx = parseInt(card.dataset.correct);
                    const chosenIdx  = parseInt(opt.dataset.idx);

                    card.dataset.answered = 'true';
                    const allOpts = card.querySelectorAll('.lc-quiz-opt');
                    
                    if (chosenIdx === correctIdx) {
                        opt.classList.add('correct');
                        opt.innerHTML = '<i class="fas fa-check-circle text-emerald-400"></i>' + opt.innerHTML;
                        // small celebration particles here if wanted
                    } else {
                        opt.classList.add('wrong');
                        opt.innerHTML = '<i class="fas fa-times-circle text-rose-400"></i>' + opt.innerHTML;
                        allOpts[correctIdx].classList.add('correct');
                        allOpts[correctIdx].innerHTML = '<i class="fas fa-check-circle text-emerald-400"></i>' + allOpts[correctIdx].innerHTML;
                    }
                });
            }

            async loadSidebarCourses() {
                try {
                    const json = await this._get('/api/courses');
                    this._renderSidebar(json.data || []);
                } catch (err) {
                    this.sidebarEl.innerHTML = `<div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <p class="text-sm text-rose-400 font-bold mb-1">Akses Ditolak</p>
                        <p class="text-xs text-slate-400">Mohon login terlebih dahulu untuk mengakses kursus.</p>
                    </div>`;
                }
            }

            // ─── Mobile View State Manager ──────────────────────────────────
            _isMobile() { return window.innerWidth < 1024; }

            _setMobileView(view) {
                const wrapper = document.getElementById('mobile-flow-wrapper');
                if (!wrapper) return;
                wrapper.classList.remove('mobile-view-categories', 'mobile-view-lessons', 'mobile-view-reader');
                wrapper.classList.add('mobile-view-' + view);
                // Scroll to top on every view transition
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Show 2-column lesson grid on mobile for a given course
            async _showMobileLessons(course) {
                const panel      = document.getElementById('mobile-lesson-panel');
                const backBar    = document.getElementById('mobile-back-bar');
                const panelTitle = document.getElementById('mobile-panel-title');
                const backLabel  = document.getElementById('mobile-back-label');
                if (!panel) return;

                // Update back bar
                if (panelTitle) panelTitle.textContent = course.title;
                if (backLabel)  backLabel.textContent  = 'Kembali ke Modul';

                // Loading state
                panel.innerHTML = `<div class="mobile-lesson-grid">${Array(6).fill('<div class="mobile-lesson-card" style="animation: pulse 1.5s infinite"><div style="height:28px;width:28px;border-radius:8px;background:rgba(255,255,255,0.06)"></div><div style="height:12px;border-radius:6px;background:rgba(255,255,255,0.04);margin-top:8px"></div></div>').join('')}</div>`;
                this._setMobileView('lessons');

                try {
                    const json     = await this._get(`/api/courses/${course.id}`);
                    const cData    = json.data;
                    const lessons  = cData.lessons || [];
                    const sim      = cData.simulation;
                    const isMaster = cData.category === 'master';
                    const isSim    = cData.category === 'simulation';
                    const style    = this._getLevelStyle(cData.level);

                    // Cache progress
                    lessons.forEach(l => { if (l.is_completed) this.completedLessonIds.add(l.id); });
                    this._courseProgressCache[course.id] = {
                        done:  cData.user_completed_lessons ?? lessons.filter(l => l.is_completed).length,
                        total: cData.lessons_count ?? lessons.length,
                        courseEl: document.querySelector(`[data-course-id="${course.id}"]`)
                    };

                    // ── Master course: show Command Missions ──────────────────────
                    if (isMaster) {
                        const sims = cData.simulations || [];
                        if (sims.length > 0) {
                            const missionsHtml = sims.map((m, idx) => `
                                <div class="rounded-2xl overflow-hidden border border-amber-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80 mb-3 relative group">
                                    <div class="h-0.5 bg-gradient-to-r from-amber-500 via-orange-400 to-rose-500"></div>
                                    <div class="p-4">
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0">
                                                <i class="fas fa-crown text-amber-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-white leading-tight">${m.title}</p>
                                                <p class="text-[9px] text-amber-500 uppercase tracking-widest mt-0.5 font-bold">Mission 0${idx+1} · Executive</p>
                                            </div>
                                        </div>
                                        <button onclick="RoleplayEngine.start(${m.id}, 'lesson-reader')"
                                                class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-slate-900 text-xs font-black hover:from-amber-400 hover:to-orange-400 transition-all">
                                            <i class="fas fa-play mr-1.5 text-[10px]"></i>Eksekusi Keputusan
                                        </button>
                                    </div>
                                </div>`).join('');
                            panel.innerHTML = `<div class="px-2 py-2">${missionsHtml}</div>`;
                        } else {
                            panel.innerHTML = `<p class="text-slate-500 text-sm p-4 text-center">Mission belum tersedia.</p>`;
                        }
                        return;
                    }

                    // ── Simulation-only course: show play button ──────────────────
                    if (isSim || (lessons.length === 0 && sim)) {
                        if (sim) {
                            panel.innerHTML = `
                                <div class="px-2 py-2">
                                    <div class="rounded-2xl overflow-hidden border border-emerald-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80">
                                        <div class="h-0.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-violet-500"></div>
                                        <div class="p-5">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-chess-knight text-emerald-400 text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-white leading-tight">${sim.title}</p>
                                                    <p class="text-[9px] text-emerald-500/80 uppercase tracking-widest font-bold mt-1">Decision Training · +${sim.xp_reward} XP</p>
                                                </div>
                                            </div>
                                            <p class="text-[11px] text-slate-400 leading-relaxed mb-4">Uji semua keputusan strategismu dalam simulasi bisnis nyata. Setiap pilihan punya konsekuensi.</p>
                                            <button onclick="RoleplayEngine.start(${sim.id}, 'lesson-reader')"
                                                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-black hover:from-emerald-400 hover:to-teal-400 hover:shadow-[0_4px_20px_rgba(16,185,129,0.4)] transition-all duration-300 active:scale-[0.98]">
                                                <i class="fas fa-play mr-2 text-xs"></i>Mulai Simulasi
                                            </button>
                                        </div>
                                    </div>
                                </div>`;
                        } else {
                            panel.innerHTML = `<p class="text-slate-500 text-sm p-4 text-center">Simulasi belum tersedia.</p>`;
                        }
                        return;
                    }

                    if (lessons.length === 0) {
                        panel.innerHTML = `<p class="text-slate-500 text-sm p-4 text-center">Belum ada lesson tersedia.</p>`;
                        return;
                    }

                    const cardsHtml = lessons.map((l, idx) => {
                        const isDone   = l.is_completed || this.completedLessonIds.has(l.id);
                        const isLocked = l.is_locked === true;
                        
                        let cardStyle = '';
                        if (isLocked) {
                            cardStyle = 'opacity: 0.5; filter: grayscale(1); cursor: not-allowed;';
                        }
                        return `
                            <div class="mobile-lesson-card${isDone ? ' done' : ''}" data-lesson-idx="${idx}" data-locked="${isLocked}" style="${cardStyle}">
                                <div class="mobile-lesson-num">
                                    ${isDone ? '<i class="fas fa-check" style="font-size:9px"></i>' : (isLocked ? '<i class="fas fa-lock text-slate-400" style="font-size:9px"></i>' : l.order)}
                                </div>
                                <p class="mobile-lesson-title">${l.title}</p>
                                <p class="mobile-lesson-xp"><i class="fas fa-star" style="font-size:9px;margin-right:3px"></i>+${l.xp_reward} XP${l.estimated_minutes ? ` · ${l.estimated_minutes}m` : ''}</p>
                            </div>
                        `;
                    }).join('');

                    panel.innerHTML = `<div class="mobile-lesson-grid">${cardsHtml}</div>`;

                    // Wire clicks
                    panel.querySelectorAll('.mobile-lesson-card').forEach((card, idx) => {
                        card.addEventListener('click', () => {
                            if (card.dataset.locked === 'true') {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Lesson Terkunci. Selesaikan lesson sebelumnya.', 'warning');
                                } else {
                                    alert('Lesson Terkunci. Selesaikan lesson sebelumnya.');
                                }
                                return;
                            }
                            const lesson = lessons[parseInt(card.dataset.lessonIdx)];
                            if (lesson) {
                                this._currentMobileLessons = lessons;
                                this._currentMobileCourse  = cData;
                                this._openLessonInReader(lesson, cData);
                                this._setMobileView('reader');
                            }
                        });
                    });

                } catch(e) {
                    panel.innerHTML = `<p class="text-rose-400 text-sm p-4 text-center">Gagal memuat lesson.</p>`;
                }
            }

            // ─── Desktop: Load lesson chooser in reader panel ───────────────────
            async _loadLessonChooserInReader(course) {
                if (!this.readerEl) return;

                // Show loading state in reader
                this.readerEl.innerHTML = `
                    <div class="animate-card-in">
                        <div class="rounded-[30px] border border-white/20 dark:border-white/10 bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl p-8 mb-4">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800 animate-pulse shrink-0"></div>
                                <div class="flex-1"><div class="h-4 bg-slate-800 rounded w-2/3 animate-pulse mb-2"></div><div class="h-3 bg-slate-800 rounded w-1/2 animate-pulse"></div></div>
                            </div>
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                                ${Array(6).fill('<div class="h-24 rounded-2xl bg-slate-800/60 animate-pulse"></div>').join('')}
                            </div>
                        </div>
                    </div>`;

                try {
                    const json    = await this._get(`/api/courses/${course.id}`);
                    const cData   = json.data;
                    const lessons = cData.lessons || [];
                    const style   = this._getLevelStyle(cData.level);
                    const sim     = cData.simulation;
                    const isMaster = cData.category === 'master';
                    const isSim    = cData.category === 'simulation';

                    // Cache completions
                    lessons.forEach(l => { if (l.is_completed) this.completedLessonIds.add(l.id); });
                    this._courseProgressCache[course.id] = {
                        done:  cData.user_completed_lessons ?? lessons.filter(l => l.is_completed).length,
                        total: cData.lessons_count ?? lessons.length,
                    };

                    const total = cData.lessons_count ?? lessons.length;
                    const done  = cData.user_completed_lessons ?? lessons.filter(l => l.is_completed).length;
                    const pct   = total > 0 ? Math.round((done / total) * 100) : 0;

                    let bodyHtml = '';

                    if (isMaster) {
                        const sims = cData.simulations || [];
                        bodyHtml = sims.length > 0
                            ? `<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">${sims.map((m, idx) => `
                                <div class="rounded-2xl border border-amber-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80 p-4 flex flex-col gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0"><i class="fas fa-crown text-amber-400 text-sm"></i></div>
                                        <div><p class="text-xs font-black text-white leading-tight">${m.title}</p><p class="text-[9px] text-amber-500 uppercase tracking-widest font-bold">Mission 0${idx+1} · Executive</p></div>
                                    </div>
                                    <button onclick="RoleplayEngine.start(${m.id}, 'lesson-reader')" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-slate-900 text-xs font-black hover:from-amber-400 hover:to-orange-400 transition-all duration-300"><i class="fas fa-play mr-1.5 text-[10px]"></i>Eksekusi Keputusan</button>
                                </div>`).join('')}</div>`
                            : '<p class="text-slate-500 text-sm text-center py-8">Mission belum tersedia.</p>';
                    } else if (isSim && sim) {
                        bodyHtml = `
                            <div class="rounded-2xl border border-emerald-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80 p-5">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center shrink-0"><i class="fas fa-chess-knight text-emerald-400 text-lg"></i></div>
                                    <div><p class="text-sm font-black text-white">${sim.title}</p><p class="text-[9px] text-emerald-500/80 uppercase tracking-widest font-bold mt-0.5">Decision Training · +${sim.xp_reward} XP</p></div>
                                </div>
                                <button onclick="RoleplayEngine.start(${sim.id}, 'lesson-reader')" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-black hover:from-emerald-400 hover:to-teal-400 transition-all"><i class="fas fa-play mr-2"></i>Mulai Simulasi</button>
                            </div>`;
                    } else if (lessons.length === 0) {
                        bodyHtml = '<p class="text-slate-500 text-sm text-center py-8">Belum ada lesson tersedia.</p>';
                    } else {
                        const cardsHtml = lessons.map((l, idx) => {
                            const isDone   = l.is_completed || this.completedLessonIds.has(l.id);
                            const isLocked = l.is_locked === true;
                            const numBg    = isDone ? `background:rgba(16,185,129,0.2);color:#34d399;` : `background:rgba(255,255,255,0.06);color:#94a3b8;`;
                            
                            let cardStyle = `border:1.5px solid ${isDone ? 'rgba(52,211,153,0.25)' : 'rgba(255,255,255,0.07)'};`;
                            let cardClass = `lesson-chooser-card cursor-pointer rounded-2xl border border-white/08 bg-white/04 dark:bg-slate-800/50 p-4 flex flex-col gap-2 transition-all duration-200`;
                            
                            if (isLocked) {
                                cardStyle += ' opacity: 0.5; filter: grayscale(1); cursor: not-allowed;';
                            } else {
                                cardClass += ` hover:bg-slate-800/70 hover:border-white/20 hover:-translate-y-0.5 hover:shadow-lg${isDone ? ' border-emerald-500/20 bg-emerald-500/05' : ''}`;
                            }

                            return `
                                <div class="${cardClass}"
                                     data-lesson-idx="${idx}" data-locked="${isLocked}" style="${cardStyle}">
                                    <div class="flex items-center gap-2">
                                        <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;flex-shrink:0;${numBg}">
                                            ${isDone ? '<i class="fas fa-check" style="font-size:9px"></i>' : (isLocked ? '<i class="fas fa-lock text-slate-400" style="font-size:9px"></i>' : l.order)}
                                        </div>
                                        ${isDone ? '<i class="fas fa-check-circle text-emerald-400 text-xs ml-auto"></i>' : ''}
                                    </div>
                                    <p class="text-xs font-black text-white leading-snug">${l.title}</p>
                                    <p class="text-[10px] font-bold text-amber-400"><i class="fas fa-star" style="font-size:8px;margin-right:2px"></i>+${l.xp_reward} XP${l.estimated_minutes ? ` · ${l.estimated_minutes}m` : ''}</p>
                                </div>`;
                        }).join('');

                        bodyHtml = `<div class="grid grid-cols-2 lg:grid-cols-3 gap-3">${cardsHtml}</div>`;

                        // Sim unlock button if applicable
                        if (sim && cData.user_completed) {
                            bodyHtml += `<div class="mt-4"><button onclick="RoleplayEngine.start(${sim.id}, 'lesson-reader')" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-black hover:from-emerald-400 hover:to-teal-400 transition-all"><i class="fas fa-chess-knight mr-2"></i>${sim.title}</button></div>`;
                        } else if (sim && !cData.user_completed) {
                            bodyHtml += `<div class="mt-4"><button disabled class="w-full py-3 rounded-xl bg-slate-900/50 border border-slate-700/50 text-slate-500 text-sm font-black opacity-60 cursor-not-allowed"><i class="fas fa-lock mr-2"></i>Selesaikan semua lesson untuk buka simulasi</button></div>`;
                        }
                    }

                    this.readerEl.innerHTML = `
                        <div class="animate-card-in">
                            <div class="rounded-[30px] border border-white/20 dark:border-white/10 bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl overflow-hidden mb-4">
                                <div class="h-1.5 w-full bg-gradient-to-r from-cyan-400 via-fuchsia-400 to-amber-400"></div>
                                <div class="p-6 md:p-8">
                                    <div class="flex items-start justify-between gap-4 mb-5">
                                        <div>
                                            <span class="inline-block text-[10px] uppercase font-black px-2.5 py-1 rounded-full border border-white/40 dark:border-transparent mb-2 ${style.badge} tracking-wider">${cData.level}</span>
                                            <h2 class="text-2xl font-black text-white leading-tight">${cData.title}</h2>
                                        </div>
                                        <span class="text-xs font-black text-slate-300 bg-slate-800/60 px-3 py-1.5 rounded-xl whitespace-nowrap" data-chooser-done="${done}" data-chooser-total="${total}">${done}/${total} ✓</span>
                                    </div>
                                    <div class="h-2 bg-slate-800/80 rounded-full overflow-hidden mb-6 shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-700 ${style.progress}" style="width:${pct}%" data-chooser-bar></div>
                                    </div>
                                    <p class="text-sm text-slate-400 mb-6 font-medium">Pilih lesson untuk mulai belajar:</p>
                                    ${bodyHtml}
                                </div>
                            </div>
                        </div>`;

                    // Wire clicks for lesson cards
                    this.readerEl.querySelectorAll('.lesson-chooser-card').forEach((card, idx) => {
                        card.addEventListener('click', () => {
                            if (card.dataset.locked === 'true') {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Lesson Terkunci. Selesaikan lesson sebelumnya.', 'warning');
                                } else {
                                    alert('Lesson Terkunci. Selesaikan lesson sebelumnya.');
                                }
                                return;
                            }
                            const lesson = lessons[parseInt(card.dataset.lessonIdx)];
                            if (lesson) {
                                this._currentMobileLessons = lessons;
                                this._currentMobileCourse  = cData;
                                this._openLessonInReader(lesson, cData);
                            }
                        });
                    });

                } catch(e) {
                    this.readerEl.innerHTML = `<div class="rounded-[30px] border border-rose-500/20 bg-rose-500/05 p-8 text-center"><p class="text-rose-400 font-bold">Gagal memuat lesson.</p></div>`;
                }
            }

            _renderSidebar(courses) {
                if (!this.sidebarEl) return;
                if (courses.length === 0) {
                    this.sidebarEl.innerHTML = `<p class="text-slate-500 text-xs p-4">Belum ada kursus aktif.</p>`;
                    return;
                }

                const levelWeights = { 'beginner': 1, 'intermediate': 2, 'advanced': 3, 'expert': 4, 'master': 5 };

                courses.sort((a, b) => {
                    const weightA = levelWeights[a.level] || 99;
                    const weightB = levelWeights[b.level] || 99;
                    if (weightA !== weightB) return weightA - weightB;
                    const isSimA = (a.category === 'simulation' || a.category === 'master') ? 1 : 0;
                    const isSimB = (b.category === 'simulation' || b.category === 'master') ? 1 : 0;
                    if (isSimA !== isSimB) return isSimA - isSimB;
                    return a.id - b.id;
                });

                // ── Group by level ──
                const grouped = {};
                courses.forEach(c => {
                    const key = c.level || 'other';
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(c);
                });

                const levelOrder = ['beginner','intermediate','advanced','expert','master','other'];
                const levelIcons = { beginner:'fa-seedling', intermediate:'fa-chart-line', advanced:'fa-bolt', expert:'fa-fire', master:'fa-crown', other:'fa-star' };
                const levelColors = { beginner:'#34d399', intermediate:'#60a5fa', advanced:'#f87171', expert:'#fbbf24', master:'#a78bfa', other:'#94a3b8' };

                const _self = this;

                // ── Render each course card HTML ──
                const _courseCardHtml = (c, ci) => {
                    const total    = c.lessons_count || 0;
                    const done     = c.user_completed_lessons || 0;
                    const pct      = total > 0 ? Math.round((done / total) * 100) : 0;
                    const style    = this._getLevelStyle(c.level);
                    const isSim    = (c.category === 'simulation' || c.category === 'master');
                    const isLocked = c.is_locked === true;
                    const simBadge = isSim ? `<span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full border border-teal-500/30 bg-teal-500/10 text-teal-400 capitalize mb-1.5 ml-1"><i class="fas fa-gamepad mr-1"></i>Simulation</span>` : '';
                    const lockBadge = isLocked ? `<span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full border border-slate-600/50 bg-slate-800 text-slate-400 mb-1.5 ml-1"><i class="fas fa-lock mr-1"></i>Terkunci</span>` : '';

                    if (isSim) {
                        return `
                        <div class="animate-card-in" style="animation-delay:${ci * 60}ms">
                            <button class="course-expand-btn w-full text-left p-4 rounded-2xl
                                           ${isLocked ? 'bg-slate-900/60 border border-slate-800 opacity-60 cursor-not-allowed' : 'bg-gradient-to-br from-amber-900/40 to-slate-900/80 border border-amber-500/40 hover:border-amber-400 hover:from-amber-900/60 hover:to-slate-900'}
                                           transition-all duration-300 group shadow-[0_0_15px_rgba(245,158,11,0.1)] relative overflow-hidden" data-course-id="${c.id}" data-locked="${isLocked}">
                                ${!isLocked ? '<div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>' : ''}
                                <div class="flex items-start justify-between gap-2 mb-1 relative z-10">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <span class="inline-block text-[9px] font-bold px-2 py-0.5 rounded-full border capitalize ${style.badge}">${c.level}</span>
                                            ${simBadge} ${lockBadge}
                                        </div>
                                        <p class="text-sm font-black text-amber-50 ${!isLocked ? 'group-hover:text-amber-300' : ''} transition-colors leading-snug">${c.title}</p>
                                    </div>
                                    <div class="w-8 h-8 rounded-full ${isLocked ? 'bg-slate-800' : 'bg-amber-500/20'} flex items-center justify-center shrink-0 mt-2 border ${isLocked ? 'border-slate-700' : 'border-amber-500/30'}">
                                        <i class="fas ${isLocked ? 'fa-lock text-slate-500' : 'fa-play text-amber-400'} text-xs ml-0.5 ${!isLocked ? 'group-hover:scale-110' : ''} transition-transform"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] ${isLocked ? 'text-slate-500' : 'text-amber-400/80'} mt-2 font-medium tracking-wide flex items-center gap-1.5"><i class="fas fa-bolt"></i> Interactive Module</p>
                            </button>
                            <div class="lesson-list hidden mt-1 ml-2 space-y-1.5" id="lessons-${c.id}">
                                <p class="text-[10px] text-slate-500 px-2 py-1 flex items-center gap-1"><i class="fas fa-spinner fa-spin text-xs"></i> Memuat…</p>
                            </div>
                        </div>
                        `;
                    }

                    return `
                        <div class="animate-card-in" style="animation-delay:${ci * 60}ms">
                            <button class="course-expand-btn w-full text-left p-5 rounded-[24px]
                                           ${isLocked ? 'bg-slate-900/60 border-2 border-slate-800 opacity-60 cursor-not-allowed' : `bg-white/40 dark:bg-slate-900/40 border-2 border-slate-200/50 dark:border-white/10 ${style.borderHover} hover:-translate-y-1 active:translate-y-0 active:shadow-md btn-3d`} shadow-[0_8px_20px_rgba(0,0,0,0.04)]
                                           backdrop-blur-xl transition-all duration-300 group relative overflow-hidden" data-course-id="${c.id}" data-locked="${isLocked}">
                                <div class="absolute inset-0 bg-gradient-to-br from-white/30 dark:from-white/5 to-transparent pointer-events-none"></div>
                                <div class="flex items-start justify-between gap-3 mb-3 relative z-10">
                                    <div class="flex-1 min-w-0">
                                        <span class="inline-block text-[10px] uppercase font-black px-2.5 py-1 rounded-full border border-white/40 dark:border-transparent mb-2 ${style.badge} shadow-sm tracking-wider">${c.level}</span>
                                        ${lockBadge}
                                        <p class="text-[15px] font-black text-slate-800 dark:text-white ${!isLocked ? style.textHover : ''} transition-colors leading-snug tracking-tight">${c.title}</p>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-slate-100/50 dark:bg-slate-800/50 flex items-center justify-center shrink-0 mt-3 ${!isLocked ? style.iconHoverBg : ''} transition-colors">
                                        <i class="fas ${isLocked ? 'fa-lock text-slate-500/50' : 'fa-chevron-down text-slate-400 dark:text-slate-500'} text-xs transition-transform duration-300 ${!isLocked ? style.iconHoverText : ''}" id="chevron-${c.id}"></i>
                                    </div>
                                </div>
                                <div class="h-2 bg-slate-200/50 dark:bg-slate-800/80 rounded-full overflow-hidden mb-2 relative z-10 shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-700 ${isLocked ? 'bg-slate-600' : style.progress}" data-progress-bar style="width:${pct}%;"></div>
                                </div>
                                <div class="flex justify-between items-center relative z-10">
                                    <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400" data-progress-text>${done}/${total} lesson selesai</p>
                                    ${done === total && total > 0 ? '<span class="text-[11px] text-amber-600 dark:text-amber-400 font-black tracking-wide px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-500/10">✓ TUNTAS!</span>' : `<span class="text-[11px] font-black text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg" data-pct-text>${pct}%</span>`}
                                </div>
                            </button>
                            <div class="lesson-list relative hidden mt-3 ml-4 space-y-2 border-l-2 border-slate-200/60 dark:border-slate-800/60 pl-4 py-1" id="lessons-${c.id}">
                                <p class="text-[10px] text-slate-500 px-2 py-1 flex items-center gap-1"><i class="fas fa-spinner fa-spin text-xs"></i> Memuat…</p>
                            </div>
                        </div>
                    `;
                };

                // ── Build HTML (desktop: flat list; mobile: grouped sections) ──
                const isMobile = this._isMobile();

                if (isMobile) {
                    const sectionsHtml = levelOrder
                        .filter(lvl => grouped[lvl])
                        .map(lvl => {
                            const icon  = levelIcons[lvl]  || 'fa-star';
                            const color = levelColors[lvl] || '#94a3b8';
                            const cardsInRow = grouped[lvl].map((c, ci) => _courseCardHtml(c, ci)).join('');
                            return `
                                <div class="mobile-category-section">
                                    <div class="mobile-category-label">
                                        <i class="fas ${icon}" style="color:${color};font-size:9px"></i>
                                        ${lvl}
                                    </div>
                                    <div class="mobile-course-row">${cardsInRow}</div>
                                </div>
                            `;
                        }).join('');
                    this.sidebarEl.innerHTML = sectionsHtml;
                } else {
                    this.sidebarEl.innerHTML = courses.map((c, ci) => _courseCardHtml(c, ci)).join('');
                }

                // ── Wire course expand buttons ──
                this.sidebarEl.querySelectorAll('.course-expand-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        if (btn.dataset.locked === 'true') {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Kursus Terkunci. Selesaikan kursus sebelumnya.', 'warning');
                            } else {
                                alert('Kursus Terkunci. Selesaikan kursus sebelumnya.');
                            }
                            return;
                        }

                        const courseId = parseInt(btn.dataset.courseId);
                        const course   = courses.find(c => c.id === courseId);

                        if (this._isMobile()) {
                            // Mobile: navigate to lesson grid view
                            if (course) this._showMobileLessons(course);
                            return;
                        }

                        // Desktop: accordion expand
                        const listEl  = document.getElementById(`lessons-${courseId}`);
                        const chevron = document.getElementById(`chevron-${courseId}`);
                        const isOpen  = !listEl.classList.contains('hidden');

                        document.querySelectorAll('.lesson-list').forEach(el => el.classList.add('hidden'));
                        document.querySelectorAll('[id^="chevron-"]').forEach(el => { if (el) el.style.transform = ''; });

                        if (!isOpen) {
                            listEl.classList.remove('hidden');
                            if (chevron) chevron.style.transform = 'rotate(180deg)';
                            await this._loadLessonsIntoSidebar(courseId, listEl);
                        }
                    });
                });

                // ── Wire mobile Back button ──
                const backBtn = document.getElementById('mobile-back-btn');
                if (backBtn) {
                    backBtn.replaceWith(backBtn.cloneNode(true)); // remove old listener
                    document.getElementById('mobile-back-btn').addEventListener('click', () => {
                        const wrapper = document.getElementById('mobile-flow-wrapper');
                        if (wrapper && wrapper.classList.contains('mobile-view-reader')) {
                            // Back from reader → lesson grid
                            document.getElementById('mobile-back-label').textContent = 'Kembali ke Modul';
                            this._setMobileView('lessons');
                        } else {
                            // Back from lessons → categories
                            this._setMobileView('categories');
                        }
                    });
                }
            }

            async _loadLessonsIntoSidebar(courseId, listEl, preloadedCourse = null) {
                try {
                    const course  = preloadedCourse || (await this._get(`/api/courses/${courseId}`)).data;
                    const lessons = course.lessons || [];
                    const sim     = course.simulation;

                    // ── Master Course: render 5 Command Missions ──
                    if (course.category === 'master') {
                        const sims = course.simulations || [];
                        if (sims.length > 0) {
                            const missionsHtml = sims.map((m, idx) => {
                                return `
                                    <div class="rounded-2xl overflow-hidden border border-amber-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80 mb-3 relative group">
                                        <div class="absolute inset-0 bg-amber-500/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        <div class="h-0.5 bg-gradient-to-r from-amber-500 via-orange-400 to-rose-500"></div>
                                        <div class="p-4 relative">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-crown text-amber-400 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-white leading-tight">${m.title}</p>
                                                    <p class="text-[9px] text-amber-500 uppercase tracking-widest mt-0.5 font-bold">Mission 0${idx + 1} · Executive</p>
                                                </div>
                                            </div>
                                            <button onclick="RoleplayEngine.start(${m.id}, 'lesson-reader')"
                                                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-slate-900 text-xs font-black hover:from-amber-400 hover:to-orange-400 hover:shadow-[0_4px_20px_rgba(245,158,11,0.4)] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                                                <i class="fas fa-play mr-1.5 text-[10px]"></i>Eksekusi Keputusan
                                            </button>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                            listEl.innerHTML = `<div class="px-1 py-2">${missionsHtml}</div>`;
                        } else {
                            listEl.innerHTML = `<p class="text-slate-500 text-xs px-2">Mission belum tersedia.</p>`;
                        }
                        return;
                    }

                    // ── Simulation-only course: render play button immediately ──
                    if (course.category === 'simulation' || (lessons.length === 0 && sim)) {
                        if (sim) {
                            listEl.innerHTML = `
                                <div class="px-1 py-2 space-y-3">
                                    <p class="text-[10px] text-slate-500 uppercase tracking-widest px-1">Beginner Capstone</p>
                                    <div class="rounded-2xl overflow-hidden border border-emerald-500/30 bg-gradient-to-br from-slate-900 to-slate-900/80">
                                        <div class="h-0.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-violet-500"></div>
                                        <div class="p-4">
                                            <div class="flex items-center gap-3 mb-3">
                                                <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-chess-knight text-emerald-400 text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-black text-white leading-tight">${sim.title}</p>
                                                    <p class="text-[9px] text-emerald-500/80 uppercase tracking-widest mt-0.5 font-bold">Decision Training · +${sim.xp_reward} XP</p>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-slate-400 leading-relaxed mb-3">Uji semua keputusan strategismu dalam 5 tahap simulasi bisnis nyata. Setiap pilihan punya konsekuensi.</p>
                                            <div class="grid grid-cols-3 gap-1.5 mb-3">
                                                <div class="rounded-lg bg-slate-800/60 border border-slate-700/40 p-2 text-center">
                                                    <p class="text-[8px] text-slate-500 uppercase tracking-wider">Profit</p>
                                                    <p class="text-xs font-black text-amber-400">50</p>
                                                </div>
                                                <div class="rounded-lg bg-slate-800/60 border border-slate-700/40 p-2 text-center">
                                                    <p class="text-[8px] text-slate-500 uppercase tracking-wider">Traffic</p>
                                                    <p class="text-xs font-black text-blue-400">50</p>
                                                </div>
                                                <div class="rounded-lg bg-slate-800/60 border border-slate-700/40 p-2 text-center">
                                                    <p class="text-[8px] text-slate-500 uppercase tracking-wider">Brand</p>
                                                    <p class="text-xs font-black text-violet-400">50</p>
                                                </div>
                                            </div>
                                            <button onclick="RoleplayEngine.start(${sim.id}, 'lesson-reader')"
                                                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-black hover:from-emerald-400 hover:to-teal-400 hover:shadow-[0_4px_20px_rgba(16,185,129,0.4)] transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                                                <i class="fas fa-play mr-1.5 text-[10px]"></i>Mulai Simulasi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            listEl.innerHTML = `<p class="text-slate-500 text-xs px-2">Simulasi belum tersedia.</p>`;
                        }
                        return;
                    }

                    if (lessons.length === 0) {
                        listEl.innerHTML = `<p class="text-slate-500 text-xs px-2">Belum ada lesson.</p>`;
                        return;
                    }

                    const style = this._getLevelStyle(course.level);

                    const htmlList = lessons.map((l) => {
                        const done     = l.is_completed;
                        const isLocked = l.is_locked === true;
                        
                        let cardClass = `sidebar-lesson-btn group w-full text-left flex items-center gap-3 px-4 py-3.5 rounded-[18px] backdrop-blur-md transition-all duration-300 shadow-sm`;
                        let playIcon  = '';
                        
                        if (isLocked) {
                            cardClass += ` bg-white/20 dark:bg-slate-900/40 border-2 border-transparent opacity-60 cursor-not-allowed`;
                            playIcon = `<i class="fas fa-lock text-[8px] text-slate-500 shrink-0 ml-0.5"></i>`;
                        } else if (done) {
                            cardClass += ` ${style.lessonDoneBg} btn-3d`;
                            playIcon = `<i class="fas fa-play text-[8px] ${style.lessonDonePlay} shrink-0 ml-0.5"></i>`;
                        } else {
                            cardClass += ` bg-white/50 dark:bg-slate-900/50 border-2 border-transparent ${style.lessonHoverBorder} ${style.lessonHoverBg} hover:-translate-y-1 active:translate-y-0 hover:shadow-md btn-3d`;
                            playIcon = `<i class="fas fa-play text-[8px] text-slate-400 dark:text-slate-500 shrink-0 ${style.lessonHoverPlayIcon} transition-colors ml-0.5"></i>`;
                        }

                        return `
                            <button class="${cardClass}"
                                    data-lesson-id="${l.id}" data-course-id="${courseId}" data-locked="${isLocked}">
                                <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-[12px] font-black transition-transform ${!isLocked ? 'group-hover:scale-110 group-hover:rotate-6' : ''}
                                            ${done ? style.lessonDoneIcon : 'bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400'}">
                                    ${done ? '<i class="fas fa-check"></i>' : (isLocked ? '<i class="fas fa-lock text-[10px]"></i>' : l.order)}
                                </div>
                                <div class="flex-1 min-w-0 text-left">
                                    <p class="text-[12px] font-black ${done ? style.lessonDoneText : 'text-slate-700 dark:text-white'} truncate leading-tight tracking-tight">${l.title}</p>
                                    <p class="text-[10px] font-bold text-slate-500 mt-1 flex items-center gap-1">${l.estimated_minutes ? '<i class="far fa-clock"></i> ' + l.estimated_minutes + ' min <span class="text-slate-300 dark:text-slate-600">•</span> ' : ''}<span class="text-amber-500 font-black">+${l.xp_reward} XP</span></p>
                                </div>
                                <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800/80 flex items-center justify-center ${!isLocked ? style.iconHoverBg : ''} transition-colors">
                                    ${playIcon}
                                </div>
                            </button>
                        `;
                    });

                    const isCourseDone = course.user_completed;

                    // ── Populate realtime completion cache ──────────────────
                    lessons.forEach(l => {
                        if (l.is_completed) this.completedLessonIds.add(l.id);
                    });
                    // ── Cache progress for the DOM update later ─────────────
                    this._courseProgressCache[courseId] = {
                        done: course.user_completed_lessons ?? lessons.filter(l => l.is_completed).length,
                        total: course.lessons_count ?? lessons.length,
                        courseEl: document.querySelector(`[data-course-id="${courseId}"]`)
                    };

                    if (sim && isCourseDone) {
                        htmlList.push(`
                            <button class="w-full text-left flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-gradient-to-r from-emerald-600/20 to-teal-500/20 border border-emerald-500/40 hover:bg-emerald-500/30 transition-all duration-300 mt-2 shadow-[0_4px_20px_rgba(16,185,129,0.1)] group" onclick="RoleplayEngine.start(${sim.id}, 'lesson-reader')">
                                <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/50 shrink-0 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-chess-knight text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-emerald-400 mb-0.5">${sim.title}</p>
                                    <p class="text-[9px] text-emerald-500/70 uppercase tracking-[0.15em] font-black">Final Test • +${sim.xp_reward + 25} XP</p>
                                </div>
                            </button>
                        `);
                    } else if (sim && !isCourseDone) {
                        htmlList.push(`
                            <button disabled class="w-full text-left flex items-center gap-2.5 px-3.5 py-3 rounded-xl bg-slate-900/50 border border-slate-700/50 opacity-60 cursor-not-allowed mt-2">
                                <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-600 border border-slate-700 shrink-0">
                                    <i class="fas fa-lock text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-500 mb-0.5">Mastery Simulation</p>
                                    <p class="text-[9px] text-slate-600 uppercase tracking-widest font-black">Selesaikan modul</p>
                                </div>
                            </button>
                        `);
                    }

                    listEl.innerHTML = htmlList.join('');

                    listEl.querySelectorAll('.sidebar-lesson-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (btn.dataset.locked === 'true') {
                                if (typeof window.showToast === 'function') {
                                    window.showToast('Lesson Terkunci. Selesaikan lesson sebelumnya.', 'warning');
                                } else {
                                    alert('Lesson Terkunci. Selesaikan lesson sebelumnya.');
                                }
                                return;
                            }
                            const lesson = lessons.find(l => l.id === parseInt(btn.dataset.lessonId));
                            if (lesson) this._openLessonInReader(lesson, course);

                            // Highlight active lesson
                            listEl.querySelectorAll('.sidebar-lesson-btn').forEach(b => b.classList.remove('ring-1','ring-emerald-500/30'));
                            btn.classList.add('ring-1','ring-emerald-500/30');
                        });
                    });

                } catch(e) {
                    listEl.innerHTML = `<p class="text-slate-500 text-xs px-2">Gagal memuat.</p>`;
                }
            }

            // ─── Enhanced Lesson Reader ─────────────────────────────────────────
            _openLessonInReader(lesson, course) {
                if (!this.readerEl) return;

                // Check live cache first — the server payload may be stale
                // if the user completed this lesson during the current session.
                const isActuallyCompleted = lesson.is_completed || this.completedLessonIds.has(lesson.id);
                
                window.pendingCharts = [];
                const html = this._renderRichContent(lesson.content);

                this.readerEl.innerHTML = `
                    <div class="animate-card-in">
                        <!-- Playful Header Card -->
                        <div class="relative rounded-[30px] overflow-hidden mb-8
                                    bg-white/60 dark:bg-slate-900/60 backdrop-blur-2xl
                                    border-2 border-white/50 dark:border-white/10 shadow-[0_12px_40px_rgba(0,0,0,0.06)]">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/40 dark:from-white/5 to-transparent pointer-events-none"></div>
                            <div class="h-1.5 w-full bg-gradient-to-r from-cyan-400 via-fuchsia-400 to-amber-400 relative z-10 shadow-[0_2px_15px_rgba(217,70,239,0.4)]"></div>
                            <div class="p-8 md:p-10 relative z-10">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-600 dark:text-slate-400 uppercase tracking-widest mb-4 font-black shadow-inner">
                                    <i class="fas fa-layer-group text-[10px]"></i> ${course.title}
                                </span>
                                <h2 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white mb-6 leading-tight tracking-tight drop-shadow-sm">${lesson.title}</h2>
                                <div class="flex items-center gap-3 flex-wrap">
                                    ${lesson.estimated_minutes ? `<span class="lesson-time-badge bg-blue-50 text-blue-600 dark:bg-blue-500/20 px-3 py-1.5 rounded-xl font-bold text-xs"><i class="fas fa-clock mr-1.5"></i>${lesson.estimated_minutes} min</span>` : ''}
                                    <span class="lesson-xp-badge bg-amber-50 text-amber-600 dark:bg-amber-500/20 px-3 py-1.5 rounded-xl font-black text-xs shadow-[0_2px_10px_rgba(245,158,11,0.2)] border border-amber-200 dark:border-amber-500/30"><i class="fas fa-star mr-1.5 text-amber-400"></i>+${lesson.xp_reward} XP</span>
                                    ${isActuallyCompleted ? `<span class="lesson-done-badge bg-emerald-50 text-emerald-600 dark:bg-emerald-500/20 px-3 py-1.5 rounded-xl font-bold text-xs"><i class="fas fa-check-circle mr-1.5"></i>Completed</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Rich Content -->
                        <div class="space-y-4 mb-10 px-2 lg:px-4">${html}</div>

                        <!-- Reading Progress Hint -->
                        <div class="flex items-center gap-4 mb-8 px-4 opacity-70">
                            <div class="flex-1 h-0.5 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                <i class="fas fa-flag-checkered text-xs"></i>
                            </div>
                            <div class="flex-1 h-0.5 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                        </div>

                        <!-- Complete CTA (Playful 3D Button) -->
                        <button id="reader-complete-btn"
                                ${isActuallyCompleted ? 'disabled' : ''}
                                class="group w-full py-5 rounded-[24px] font-black text-base transition-all duration-300 btn-3d
                                       ${isActuallyCompleted
                                            ? 'bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 cursor-default border-2 border-slate-200 dark:border-white/5 box-shadow-none'
                                            : 'bg-gradient-to-r from-fuchsia-500 to-violet-500 text-white hover:from-fuchsia-400 hover:to-violet-400 shadow-[0_8px_0_#9333ea,0_15px_20px_rgba(0,0,0,0.2)] hover:-translate-y-1 active:shadow-[0_0px_0_#9333ea,0_0px_0_rgba(0,0,0,0.2)] active:translate-y-2'}">
                            <div class="relative z-10 flex items-center justify-center gap-3">
                            ${isActuallyCompleted
                                ? `<i class="fas fa-check-circle text-xl mr-1"></i> Misi Selesai!`
                                : `<span class="tracking-wide">Klaim XP Sekarang!</span>
                                   <span class="px-3 py-1 rounded-[10px] bg-white/20 text-white text-sm font-black shadow-inner">+${lesson.xp_reward} XP</span>
                                   <i class="fas fa-rocket text-xl group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform duration-300"></i>`}
                            </div>
                        </button>

                        <!-- Mobile-only Navigation Row -->
                        <div class="lg:hidden flex gap-3 mt-4" id="mobile-reader-nav">
                            <button id="mobile-back-to-lessons" class="flex-1 flex items-center justify-center gap-2 py-4 rounded-[18px] font-bold text-sm
                                bg-white/10 border border-white/10 text-slate-300 hover:bg-white/15 transition-all backdrop-blur-md">
                                <i class="fas fa-th-large text-xs"></i> Daftar Lesson
                            </button>
                            <button id="mobile-next-lesson" class="flex-1 flex items-center justify-center gap-2 py-4 rounded-[18px] font-bold text-sm
                                bg-gradient-to-r from-cyan-500/80 to-emerald-500/80 text-white hover:from-cyan-400/90 hover:to-emerald-400/90
                                shadow-[0_6px_0_rgba(6,182,212,0.4)] hover:-translate-y-0.5 active:translate-y-1 active:shadow-none transition-all">
                                <i class="fas fa-arrow-right text-xs"></i> Lesson Berikutnya
                            </button>
                        </div>
                    </div>
                `;

                this.readerEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

                const btn = document.getElementById('reader-complete-btn');
                if (btn && !lesson.is_completed) {
                    btn.addEventListener('click', () => this._completeInReader(lesson, course, btn));
                }

                // Wire mobile nav buttons
                const backToLessons = document.getElementById('mobile-back-to-lessons');
                if (backToLessons) {
                    backToLessons.addEventListener('click', () => {
                        this._setMobileView('lessons');
                    });
                }

                const nextLessonBtn = document.getElementById('mobile-next-lesson');
                if (nextLessonBtn) {
                    const lessons = this._currentMobileLessons || [];
                    const currentIdx = lessons.findIndex(l => l.id === lesson.id);
                    const nextLesson = lessons[currentIdx + 1];
                    if (nextLesson) {
                        nextLessonBtn.addEventListener('click', () => {
                            this._openLessonInReader(nextLesson, course);
                        });
                    } else {
                        nextLessonBtn.disabled = true;
                        nextLessonBtn.textContent = 'Selesai! 🎉';
                        nextLessonBtn.classList.remove('from-cyan-500/80','to-emerald-500/80');
                        nextLessonBtn.classList.add('bg-slate-800','text-slate-500','cursor-default');
                        nextLessonBtn.style.boxShadow = 'none';
                    }
                }

                // Wire action checkboxes
                this.readerEl.querySelectorAll('.action-checkbox').forEach(cb => {
                    cb.addEventListener('click', () => {
                        cb.classList.toggle('checked');
                        const step = cb.closest('.action-step');
                        if (cb.classList.contains('checked')) {
                            step.style.opacity = '0.6';
                            step.style.textDecoration = 'line-through';
                        } else {
                            step.style.opacity = '';
                            step.style.textDecoration = '';
                        }
                    });
                });
            }

            // ─── Rich Content Renderer ──────────────────────────────────────────
            _renderRichContent(text) {
                const lines  = text.split('\n');
                let   output = [];
                let   i      = 0;
                let   sectionCount = 0;

                while (i < lines.length) {
                    const line = lines[i];

                    // ── H2: Section header ──────────────────────────────────────
                    if (/^## (.+)$/.test(line)) {
                        const title = line.replace(/^## /, '');
                        sectionCount++;
                        const icons = ['fa-lightbulb','fa-chart-line','fa-bullseye','fa-rocket','fa-trophy','fa-brain','fa-star'];
                        const icon  = icons[sectionCount % icons.length];
                        output.push(`<div class="lc-h2 anim-section" style="animation-delay:${sectionCount*80}ms">
                            <div class="lc-h2-icon"><i class="fas ${icon} text-[10px]"></i></div>
                            ${this._inline(title)}
                        </div>`);
                        i++; continue;
                    }

                    // ── H3: Sub-section ─────────────────────────────────────────
                    if (/^### (.+)$/.test(line)) {
                        const title = line.replace(/^### /, '');
                        output.push(`<div class="lc-h3">${this._inline(title)}</div>`);
                        i++; continue;
                    }

                    // ── Storytelling: Case Study ─────────────────────────────────
                    if (/^\[CASE_STUDY\]/.test(line)) {
                        let content = [];
                        i++;
                        while (i < lines.length && !/^\[\/CASE_STUDY\]/.test(lines[i])) {
                            content.push(lines[i]);
                            i++;
                        }
                        output.push(`
                            <div class="lc-case-study">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-blue-500/20 border border-blue-500/40 flex items-center justify-center shrink-0" style="animation: lc-pulse-glow 2s infinite">
                                        <i class="fas fa-book-open text-blue-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="lc-card-title text-slate-200 font-bold text-sm tracking-wide">Studi Kasus Bisnis</h4>
                                        <p class="text-[10px] lc-blue-label text-blue-400 uppercase tracking-widest">Real-world Example</p>
                                    </div>
                                </div>
                                <div class="lc-card-body text-slate-300 text-sm leading-relaxed italic border-l-2 border-blue-500/30 pl-4">
                                    ${this._renderRichContent(content.join('\n'))}
                                </div>
                            </div>
                        `);
                        i++; continue;
                    }

                    // ── Interactive: Challenge ───────────────────────────────────
                    if (/^\[CHALLENGE\]/.test(line)) {
                        let content = [];
                        i++;
                        while (i < lines.length && !/^\[\/CHALLENGE\]/.test(lines[i])) {
                            content.push(lines[i]);
                            i++;
                        }
                        output.push(`
                            <div class="lc-challenge">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-violet-500/10 rounded-full blur-2xl pointer-events-none"></div>
                                <div class="flex items-start gap-4 relative z-10">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center shrink-0 shadow-[0_0_20px_rgba(139,92,246,0.4)]">
                                        <i class="fas fa-rocket text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="lc-card-title text-white font-black text-lg mb-1">Tantangan Eksekusi!</h4>
                                        <p class="lc-challenge-label text-violet-300 text-[11px] uppercase tracking-widest mb-4 font-bold">Action Plan Hari Ini</p>
                                        <div class="lc-card-body text-slate-300 text-sm leading-relaxed space-y-2">
                                            ${this._renderRichContent(content.join('\n'))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        i++; continue;
                    }

                    // ── Interactive: Quiz ────────────────────────────────────────
                    // Format: [QUIZ:Question text here|Option A|Option B|Option C|1] (0-indexed correct answer)
                    if (/^\[QUIZ:/.test(line)) {
                        const parts = line.replace(/^\[QUIZ:/, '').replace(/\]$/, '').split('|');
                        if (parts.length >= 3) {
                            const question = parts[0];
                            const correctIdx = parseInt(parts[parts.length - 1]);
                            const options = parts.slice(1, parts.length - 1);
                            
                            const quizId = 'q_' + Math.random().toString(36).substr(2, 6);
                            const optHtml = options.map((opt, idx) => `
                                <div class="lc-quiz-opt" data-idx="${idx}">
                                    <div class="w-5 h-5 rounded border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 flex items-center justify-center text-[10px] font-bold shrink-0">${String.fromCharCode(65 + idx)}</div>
                                    <span class="flex-1">${this._inline(opt)}</span>
                                </div>
                            `).join('');

                            output.push(`
                                <div class="lc-quiz-card" data-correct="${correctIdx}" id="${quizId}">
                                    <div class="lc-quiz-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 text-slate-400 text-[9px] font-bold uppercase tracking-wider mb-3">
                                        <i class="fas fa-question-circle text-blue-400"></i> Pop Quiz
                                    </div>
                                    <h4 class="lc-quiz-question text-slate-200 font-bold text-sm mb-4 leading-snug">${this._inline(question)}</h4>
                                    <div class="space-y-2">${optHtml}</div>
                                </div>
                            `);
                        }
                        i++; continue;
                    }

                    // ── Visual: Infographic CSS Builder ──────────────────────────
                    if (/^\[INFOGRAPHIC:(.+)\]/.test(line)) {
                        const title = line.match(/^\[INFOGRAPHIC:(.+)\]/)[1];
                        let cardsHtml = '';
                        const icons = ['fa-bolt', 'fa-fire', 'fa-gem', 'fa-star', 'fa-bullseye', 'fa-compass'];
                        let c = 0;
                        i++;
                        while (i < lines.length && !/^\[\/INFOGRAPHIC\]/.test(lines[i])) {
                            const parts = lines[i].split('|').map(s => s.trim());
                            if (parts.length >= 2) {
                                const header = parts[0];
                                const desc = parts.slice(1).join(' | ');
                                const icon = icons[c % icons.length];
                                
                                cardsHtml += `
                                <div class="lc-info-card">
                                    <div class="lc-info-icon"><i class="fas ${icon}"></i></div>
                                    <div>
                                        <h5 class="lc-card-title text-sky-400 font-bold text-sm tracking-wide mb-1">${this._inline(header)}</h5>
                                        <p class="lc-card-body text-slate-300 text-[13px] leading-relaxed">${this._inline(desc)}</p>
                                    </div>
                                </div>`;
                                c++;
                            }
                            i++;
                        }
                        
                        output.push(`
                            <div class="lc-infographic">
                                <h4 class="text-white font-black text-base text-center tracking-wide mb-4">✨ ${this._inline(title)}</h4>
                                ${cardsHtml}
                            </div>
                        `);
                        i++; continue;
                    }

                    // ── Visual: Image Embed ──────────────────────────────────────
                    if (/^\[IMAGE:(.+)\]/.test(line)) {
                        const url = line.match(/^\[IMAGE:(.+)\]/)[1];
                        output.push(`<img loading="lazy" src="${url}" class="lc-image" alt="Lesson Illustration" loading="lazy">`);
                        i++; continue;
                    }

                    // ── Code block → Formula card ───────────────────────────────
                    if (/^```/.test(line)) {
                        let code = [];
                        i++;
                        while (i < lines.length && !/^```/.test(lines[i])) {
                            code.push(lines[i]);
                            i++;
                        }
                        output.push(`<div class="formula-card">
                            <div class="formula-label"><i class="fas fa-square-root-alt mr-1.5"></i>Formula / Persamaan</div>
                            <div class="formula-text">${code.join('\n')}</div>
                        </div>`);
                        i++; continue;
                    }

                    // ── Blockquote → Sleek Callout ────────────────────────────────
                    if (/^> /.test(line)) {
                        // Gather all contiguous blockquotes
                        let contentLines = [];
                        while (i < lines.length && /^> /.test(lines[i])) {
                            contentLines.push(lines[i].replace(/^> /, ''));
                            i++;
                        }
                        const content = contentLines.join(' ');
                        const isWarn  = content.toLowerCase().includes('hati-hati') || content.toLowerCase().includes('warning');
                        const isTip   = content.toLowerCase().includes('tip') || content.toLowerCase().includes('pro tip');
                        
                        let flavor = '';
                        if (isWarn) flavor = 'warn';
                        else if (isTip) flavor = 'tip';

                        // Stripe out emoji if they typed one anyway just to be clean
                        const cleanContent = content.replace(/^(💡|🎯|📌|🔑|✅|🚀|📊|⚠️) /, '').replace(/^Tip: /, '');
                        
                        output.push(`<div class="lc-callout ${flavor}">${this._inline(cleanContent)}</div>`);
                        continue;
                    }

                    // ── Table ───────────────────────────────────────────────────
                    if (/^\|/.test(line)) {
                        let rows = [];
                        while (i < lines.length && /^\|/.test(lines[i])) {
                            if (!/^\|[-| :]+\|$/.test(lines[i])) rows.push(lines[i]);
                            i++;
                        }
                        if (rows.length > 0) {
                            const header = rows[0].split('|').filter(c => c.trim()).map(c => `<th>${c.trim()}</th>`).join('');
                            const body   = rows.slice(1).map(r => {
                                const cells = r.split('|').filter(c => c.trim()).map(c => `<td>${this._inline(c.trim())}</td>`).join('');
                                return `<tr>${cells}</tr>`;
                            }).join('');
                            output.push(`<div class="overflow-x-auto rounded-xl border border-slate-700/60 my-4">
                                <table class="lc-table"><thead><tr>${header}</tr></thead><tbody>${body}</tbody></table>
                            </div>`);
                        }
                        continue;
                    }

                    // ── Numbered list → Cause cards (grouped) ───────────────────
                    if (/^\d+\. /.test(line)) {
                        let items = [];
                        while (i < lines.length && /^\d+\. /.test(lines[i])) {
                            // Check if has em-dash separator (title — desc)
                            const text    = lines[i].replace(/^\d+\. /, '');
                            const dashIdx = text.indexOf(' — ');
                            if (dashIdx > -1) {
                                items.push({ title: text.slice(0, dashIdx), desc: text.slice(dashIdx + 3) });
                            } else {
                                items.push({ title: text, desc: '' });
                            }
                            i++;
                        }
                        const cards = items.map((item, idx) => `
                            <div class="cause-card">
                                <div class="cause-num cause-num-${(idx % 5) + 1}">${idx + 1}</div>
                                <div>
                                    <p class="cause-title">${this._inline(item.title)}</p>
                                    ${item.desc ? `<p class="cause-desc">${this._inline(item.desc)}</p>` : ''}
                                </div>
                            </div>
                        `).join('');
                        output.push(`<div class="cause-cards">${cards}</div>`);
                        continue;
                    }

                    // ── Checkbox list → Action steps ────────────────────────────
                    if (/^- \[ \] /.test(line)) {
                        let steps = [];
                        while (i < lines.length && /^- \[ \] /.test(lines[i])) {
                            steps.push(lines[i].replace(/^- \[ \] /, ''));
                            i++;
                        }
                        const stepHtml = steps.map(s => `
                            <div class="action-step">
                                <div class="action-checkbox" title="Centang jika sudah dilakukan"></div>
                                <span>${this._inline(s)}</span>
                            </div>
                        `).join('');
                        output.push(`<div class="action-steps">${stepHtml}</div>`);
                        continue;
                    }

                    // ── Dash list → Visual minimalist list ───────────────────────
                    if (/^- /.test(line) && !/^- \[ \] /.test(line)) {
                        let items = [];
                        while (i < lines.length && /^- /.test(lines[i]) && !/^- \[ \] /.test(lines[i])) {
                            items.push(lines[i].replace(/^- /, ''));
                            i++;
                        }
                        const listHtml = items.map(b => `<div class="lc-list-item">${this._inline(b)}</div>`).join('');
                        output.push(`<div class="lc-list">${listHtml}</div>`);
                        continue;
                    }

                    // ── Paragraph ───────────────────────────────────────────────
                    if (line.trim()) {
                        output.push(`<p class="lc-p">${this._inline(line)}</p>`);
                    } else if (i > 0 && lines[i - 1].trim()) {
                        output.push(`<div class="lc-sep"></div>`);
                    }

                    i++;
                }

                return output.join('');
            }

            // Inline formatting: bold, code, italic
            _inline(text) {
                return text
                    .replace(/\*\*(.+?)\*\*/g, '<strong class="text-slate-900 dark:text-white font-bold">$1</strong>')
                    .replace(/`([^`]+)`/g,      '<span class="lc-code">$1</span>')
                    .replace(/_([^_]+)_/g,       '<em class="text-slate-500 dark:text-slate-300">$1</em>');
            }

            async _completeInReader(lesson, course, btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan progress…';

                try {
                    const json = await this._post('/api/lesson/complete', { lesson_id: lesson.id });

                    if ((json.status === 'success' || json.success) && json.xp > 0) {
                        // Always update localStorage XP directly as ground truth
                        const currentXP = parseInt(localStorage.getItem('cuan_xp') || '0');
                        const newXP = currentXP + json.xp;
                        localStorage.setItem('cuan_xp', newXP);

                        if (window.Gamification && typeof window.Gamification.refresh === 'function') {
                            window.Gamification.refresh();
                        } else if (typeof syncXPBar === 'function') {
                            syncXPBar();
                        }
                        if (typeof confetti === 'function') {
                            confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }, colors: ['#34d399','#fbbf24','#818cf8','#67e8f9'] });
                        }
                    }

                    // ── 1. Mark button as done permanently in this session ──────────
                    btn.innerHTML = '<i class="fas fa-check-circle text-xl mr-1"></i> Misi Selesai!';
                    btn.className = 'group w-full py-5 rounded-[24px] font-black text-base transition-all duration-300 btn-3d bg-slate-100 dark:bg-slate-800/80 text-slate-400 dark:text-slate-500 cursor-default border-2 border-slate-200 dark:border-white/5 box-shadow-none';

                    // ── 2. Add to live cache so navigating back won't re-enable it ──
                    this.completedLessonIds.add(lesson.id);

                    const courseId = course.id;
                    const style = this._getLevelStyle(course.level);

                    // ── 3. Update sidebar lesson checkmark inline ──────────────
                    const lessonBtn = document.querySelector(`.sidebar-lesson-btn[data-lesson-id="${lesson.id}"]`);
                    if (lessonBtn) {
                        const iconEl = lessonBtn.querySelector('.w-8.h-8');
                        if (iconEl) {
                            iconEl.className = `w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-[12px] font-black transition-transform group-hover:scale-110 group-hover:rotate-6 ${style.lessonDoneIcon}`;
                            iconEl.innerHTML = '<i class="fas fa-check"></i>';
                        }
                        const textEl = lessonBtn.querySelector('p.truncate');
                        if (textEl) textEl.className = `text-[12px] font-black ${style.lessonDoneText} truncate leading-tight tracking-tight`;

                        const playIcon = lessonBtn.querySelector('i.fa-play');
                        if(playIcon) {
                             playIcon.className = `fas fa-play text-[8px] ${style.lessonDonePlay} shrink-0 group-hover:text-fuchsia-500 transition-colors ml-0.5`;
                        }

                        lessonBtn.className = `sidebar-lesson-btn group w-full text-left flex items-center gap-3 px-4 py-3.5 rounded-[18px] ${style.lessonDoneBg} backdrop-blur-md transition-all duration-300 shadow-sm hover:shadow-md btn-3d`;
                    }

                    // ── 4. Update progress bar and counter inline ───────────────
                    const cache = this._courseProgressCache[courseId];
                    if (cache) {
                        cache.done = (cache.done || 0) + 1;
                        const pct = cache.total > 0 ? Math.round((cache.done / cache.total) * 100) : 0;

                        // Update sidebar course card
                        const courseBtn = document.querySelector(`.course-expand-btn[data-course-id="${courseId}"]`);
                        if (courseBtn) {
                            const bar = courseBtn.querySelector('[data-progress-bar]');
                            if (bar) bar.style.width = pct + '%';
                            const counter = courseBtn.querySelector('[data-progress-text]');
                            if (counter) counter.textContent = `${cache.done}/${cache.total} lesson selesai`;
                            const pctLabel = courseBtn.querySelector('[data-pct-text]');
                            if (pctLabel) pctLabel.textContent = pct + '%';
                        }

                        // Update lesson chooser panel progress (if currently visible in reader)
                        const chooserBar = document.querySelector('[data-chooser-bar]');
                        if (chooserBar) chooserBar.style.width = pct + '%';
                        const chooserDone = document.querySelector('[data-chooser-done]');
                        if (chooserDone) {
                            chooserDone.textContent = `${cache.done}/${cache.total} ✓`;
                            chooserDone.setAttribute('data-chooser-done', cache.done);
                        }

                        // Update lesson chooser card to show checkmark
                        const chooserCards = document.querySelectorAll('.lesson-chooser-card[data-lesson-idx]');
                        chooserCards.forEach(card => {
                            const idx = parseInt(card.dataset.lessonIdx);
                            const cardLesson = (this._currentMobileLessons || [])[idx];
                            if (cardLesson && cardLesson.id === lesson.id) {
                                card.classList.add('border-emerald-500/20');
                                card.style.border = '1.5px solid rgba(52,211,153,0.25)';
                                const numBadge = card.querySelector('[style*="border-radius:8px"]');
                                if (numBadge) {
                                    numBadge.style.background = 'rgba(16,185,129,0.2)';
                                    numBadge.style.color = '#34d399';
                                    numBadge.innerHTML = '<i class="fas fa-check" style="font-size:9px"></i>';
                                }
                            }
                        });

                        // Update mobile lesson card in 2-col grid
                        const mobileLessonCards = document.querySelectorAll('.mobile-lesson-card[data-lesson-idx]');
                        mobileLessonCards.forEach(card => {
                            const idx = parseInt(card.dataset.lessonIdx);
                            const cardLesson = (this._currentMobileLessons || [])[idx];
                            if (cardLesson && cardLesson.id === lesson.id) {
                                card.classList.add('done');
                                const numDiv = card.querySelector('.mobile-lesson-num');
                                if (numDiv) numDiv.innerHTML = '<i class="fas fa-check" style="font-size:9px"></i>';
                            }
                        });
                    }

                    // Update XP bar
                    if (typeof syncXPBar === 'function') syncXPBar();

                    // Update Achievement Dashboard progress
                    if (window.achievementDashboard) window.achievementDashboard.fetchAndRender();

                    // ── 5. Auto-unlock the next lesson & refresh UI ─────────────
                    try {
                        const json = await this._get('/api/courses/' + courseId);
                        const freshCourse = json.data;
                        if (freshCourse) {
                            // a. Update Desktop Chooser seamlessly
                            if (this.readerEl.querySelector('.lesson-chooser-card')) {
                                this._loadLessonChooserInReader(freshCourse);
                            }
                            // b. Update Sidebar List seamlessly
                            const listEl = document.getElementById(`lessons-${courseId}`);
                            if (listEl && !listEl.classList.contains('hidden')) {
                                this._loadLessonsIntoSidebar(courseId, listEl, freshCourse);
                            }
                            // c. Update Mobile View seamlessly
                            if (this._currentMobileCourse && this._currentMobileCourse.id === courseId) {
                                this._currentMobileLessons = freshCourse.lessons;
                                this._currentMobileCourse  = freshCourse;
                                this._showMobileLessons(freshCourse);
                            }
                        }
                    } catch (e) {
                         console.error("Failed to auto-unlock next module context", e);
                    }

                } catch(e) {
                    btn.disabled = false;
                    btn.innerHTML = `<span class="flex items-center justify-center gap-2">Selesaikan Lesson <span class="px-2 py-0.5 rounded-lg bg-white/20 text-xs font-black">+${lesson.xp_reward} XP</span> <i class="fas fa-rocket"></i></span>`;
                }
            }

            _renderSidebarSkeleton() {
                if (!this.sidebarEl) return;
                this.sidebarEl.innerHTML = Array(3).fill(`
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-700/50 animate-pulse">
                        <div class="h-3 bg-slate-800 rounded w-1/4 mb-2"></div>
                        <div class="h-4 bg-slate-800 rounded w-3/4 mb-3"></div>
                        <div class="h-1.5 bg-slate-800 rounded-full mb-2"></div>
                        <div class="h-2 bg-slate-800 rounded w-1/3"></div>
                    </div>
                `).join('');
            }
        }

        window.coursesHub = new CoursesPageHub();
        // Note: gamificationEngine auto-boots on DOMContentLoaded,
        // fetches /api/learning/progress, and calls renderUI() which
        // targets #xp-progress-bar and #xp-label above — no custom sync needed.
    });
    </script>

    <!-- Global Helpers -->
    <script type="module" src="{{ asset('assets/js/utils/helpers.js') }}"></script>

    <!-- Module Scripts & Interactive Drivers -->
    <script src="{{ asset('assets/js/learning/roleplay-engine.js') }}"></script>
</body>
</html>
