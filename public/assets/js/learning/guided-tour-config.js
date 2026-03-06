window.guidedTourSteps = {

    // ═══════════════════════════════════════════════════════════════
    // MAIN ONBOARDING TOUR — Comprehensive New User Walkthrough
    // Guides user through all platform features, switching tabs automatically.
    // ═══════════════════════════════════════════════════════════════
    mainOnboarding: [
        {
            id: 'welcome',
            selector: 'nav',
            title: 'Welcome to CuanCapital! 🚀',
            content: 'Selamat datang di Web Cashflow Engine! Ini adalah "Laboratorium Bisnis" pribadi Anda — tempat untuk menguji ide dan mensimulasikan strategi bisnis sebelum Anda benar-benar membakar uang.'
        },
        {
            id: 'hero-status',
            selector: '#xp-bar-wrapper',
            title: 'Control Center & Profil Anda',
            content: 'Setiap fitur yang Anda gunakan akan memberikan Experience Points (XP). Kumpulkan XP untuk naik level dan dapatkan badge eksklusif!'
        },
        {
            id: 'rgp-intro',
            selector: '#rgp-model-cards',
            title: 'Fitur 1: Reverse Goal Planner',
            content: 'Daripada menebak-nebak, di fitur ini kita mulai dari akhir. Masukkan target profit impian Anda, dan biarkan sistem menghitung apakah target tersebut realistis berdasarkan standar pasar saat ini.',
            action: () => {
                if (typeof window.switchBentoTab === 'function') window.switchBentoTab('goal-planner');
                setTimeout(() => {
                    const el = document.getElementById('rgp-model-cards');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        },
        {
            id: 'ps-intro',
            selector: '.zone-card[data-zone="traffic"]',
            title: 'Fitur 2: Profit Simulator',
            content: 'Di sini Anda bisa bermain "Bagaimana Jika...". Geser pengaturan di 4 Zona (Traffic, Konversi, Harga, Biaya) dan lihat efek real-time ke keuntungan bersih Anda tanpa harus tes langsung di lapangan.',
            action: () => {
                if (typeof window.switchBentoTab === 'function') window.switchBentoTab('profit-simulator-section');
                setTimeout(() => {
                    const el = document.querySelector('.zone-card[data-zone="traffic"]');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        },
        {
            id: 'mentor-intro',
            selector: '.wizard-step[data-step="1"]',
            title: 'Fitur 3: Mentor Lab & AI Evaluator',
            content: 'Kurang yakin dengan data simulasi Anda? Gunakan fitur ini. AI Mentor akan mengevaluasi bisnis Anda ke dalam 4 skor utama: Feasibility, Profitability, Risk Safety, dan Efficiency.',
            action: () => {
                if (typeof window.switchBentoTab === 'function') window.switchBentoTab('business-simulation-lab');
                setTimeout(() => {
                    const el = document.querySelector('.wizard-step[data-step="1"]');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        },
        {
            id: 'mentor-roadmap',
            selector: '#btn-generate-roadmap',
            title: 'Generate Execution Roadmap',
            content: 'Setelah evaluasi selesai, cukup klik tombol ini dan AI akan menyusunkan langkah eksekusi (SOP) dari hari 1 sampai 90 khusus untuk bisnis Anda yang bisa disimpan ke Blueprint.',
            action: () => {
                if (typeof window.switchBentoTab === 'function') window.switchBentoTab('roadmap-container');
                setTimeout(() => {
                    const el = document.getElementById('btn-generate-roadmap');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        },
        {
            id: 'extra-products',
            selector: '#other-products',
            title: 'Fitur 4: Produk Ekstra',
            content: 'Selain kalkulator di atas, Anda juga bisa menemukan berbagai katalog produk khusus yang dirancang untuk mendukung operasional bisnis Anda di halaman ini.',
            action: () => {
                if (typeof switchDesktopNavTab === 'function') switchDesktopNavTab('product');
                setTimeout(() => {
                    const el = document.getElementById('other-products');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        },
        {
            id: 'premium-tools',
            selector: '#mini-course-teaser',
            title: 'Fitur 5: Tools Premium',
            content: 'Ingin upgrade lebih jauh? Jelajahi modul edukasi dan akses alat bisnis kelas VIP (Premium) untuk memaksimalkan potensi strategi Anda.',
            action: () => {
                if (typeof switchDesktopNavTab === 'function') switchDesktopNavTab('tools');
                setTimeout(() => {
                    const el = document.getElementById('mini-course-teaser');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        },
        {
            id: 'learning-mode',
            selector: '#toggle-tutorial-btn',
            title: 'Fitur Bantuan: Learning Mode 💡',
            content: 'Terakhir, jika Anda melihat istilah bisnis yang membingungkan, pastikan tombol Learning Mode ini Menyala. Klik icon (i) yang tersebar di halaman untuk melihat penjelasan kamus bisnisnya.',
            action: () => {
                if (typeof switchDesktopNavTab === 'function') switchDesktopNavTab('feature');
                setTimeout(() => window.scrollTo({ top: 0, behavior: 'smooth' }), 300);
            }
        }
    ],

    // ═══════════════════════════════════════════════════════════════
    // FEATURE-SPECIFIC TOURS — Triggered manually via "Mulai Tour" buttons
    // ═══════════════════════════════════════════════════════════════
    reversePlanner: [
        {
            id: 'model',
            selector: '#rgp-model-cards',
            title: 'Pilih Model Bisnis',
            content: 'Setiap model punya struktur margin dan risiko berbeda. Pilih yang paling sesuai dengan usaha kamu.'
        },
        {
            id: 'target',
            selector: '#rp-target-profit',
            title: 'Set Target Profit',
            content: 'Kita mulai dari target. Berapa profit bersih per bulan yang ingin kamu capai?'
        },
        {
            id: 'price',
            selector: '#rp-price',
            title: 'Harga Jual',
            content: 'Masukkan rata-rata harga jual per unit produk/layanan kamu.'
        },
        {
            id: 'status',
            selector: '#rp-status-card',
            title: 'Live Evaluation',
            content: 'Sistem otomatis mengevaluasi kelayakan target kamu secara real-time berdasarkan asumsi yang ada.'
        }
    ],

    profitSimulator: [
        {
            id: 'traffic',
            selector: '.zone-card[data-zone="traffic"]',
            title: 'Zona Traffic',
            content: 'Datangkan lebih banyak calon pembeli. Klik area ini untuk mencoba berbagai tingkatan simulasi (Organik, Ads, Agresif).'
        },
        {
            id: 'conversion',
            selector: '.zone-card[data-zone="conversion"]',
            title: 'Zona Konversi',
            content: 'Tingkatkan efektivitas penjualanmu dari mulai optimasi UI halaman sampai copywriting.'
        },
        {
            id: 'pricing',
            selector: '.zone-card[data-zone="pricing"]',
            title: 'Zona Harga (Pricing)',
            content: 'Mensimulasikan bagaimana dampaknya jika kamu menaikkan nilai jual produk (Add-on, Premium, Elite).'
        },
        {
            id: 'cost',
            selector: '.zone-card[data-zone="cost"]',
            title: 'Zona Biaya (Cost)',
            content: 'Pangkas biaya operasional/modal (HPP) untuk menebalkan margin profit bersihmu.'
        },
        {
            id: 'live-sim',
            selector: '#ps-default-state',
            title: 'Live Simulation',
            content: 'Setelah kamu memilih upaya di zona sebelah kiri, sistem akan memuat Cuan Meter (Live Simulasi) secara real-time di panel ini.'
        }
    ],

    mentorLab: [
        {
            id: 'wizard-step-1',
            selector: '.wizard-step[data-step="1"]',
            title: 'Langkah 1: Parameter Awal',
            content: 'Pilih tipe model bisnis dan objektif dominan kamu. Sistem butuh ini untuk mencari benchmark yang tepat.',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 1;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'wizard-step-2',
            selector: '.wizard-step[data-step="2"]',
            title: 'Langkah 2: Realita Operasional',
            content: 'Tentukan berapa Estimasi Gross Margin dan Pengalaman Bisnis kamu saat ini. Jangan dinaikin, jujur aja biar akurat!',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 2;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'wizard-step-3',
            selector: '.wizard-step[data-step="3"]',
            title: 'Langkah 3: Target Cuan',
            content: 'Set Target Omzet Bulanan dan rentang waktu (Timeframe) kamu untuk mencapai target itu.',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 3;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'wizard-step-4',
            selector: '.wizard-step[data-step="4"]',
            title: 'Langkah 4: Kondisi Saat Ini',
            content: 'Isi data aktual bisnismu mulai dari revenue, expense, sampai umur bisnis untuk diagnosis yang berbobot.',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 4;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'wizard-step-5',
            selector: '.wizard-step[data-step="5"]',
            title: 'Langkah 5: Identifikasi Masalah',
            content: 'Pilih tantangan atau hambatan terbesar yang saat ini sedang kamu hadapi.',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 5;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'submit',
            selector: '#wizard-btn-submit',
            title: 'Analisa Bisnis',
            content: 'Setelah semua form selesai, klik tombol ini. Kami akan memproses seluruh parameter yang ada menjadi sebuah simulasi yang realistis.',
            action: () => {
                if (window.mentorWizard) {
                    window.mentorWizard.currentStep = 5;
                    window.mentorWizard.updateUI();
                }
            }
        },
        {
            id: 'board',
            selector: '#mentor-board-container',
            title: 'Mentor Dashboard',
            content: 'Di sinilah \'sihir\'-nya terjadi. Kami akan meracik Strategic DNA, Diagnostic Result, dan Blueprint khusus untuk bisnismu.'
        }
    ]
};
