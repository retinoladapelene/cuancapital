/**
 * @file ad-arsenal-frontend.js
 * @description Renders Ad Arsenal cards on the Landing Page with UX Psychology.
 * Uses Public API Endpoint. Designed for maximum conversion.
 * 
 * Psychology Triggers Used:
 * - Visual Attraction: Hero image with gradient overlay
 * - Scarcity/Urgency: Animated HOT/NEW badges with pulse
 * - Social Proof: User count indicators
 * - Authority: PREMIUM verified badge
 * - Loss Aversion: "Jangan Lewatkan" CTA framing
 * - Curiosity Gap: Teaser descriptions
 * - Visual Hierarchy: Image → Tag → Title → Desc → CTA
 */

import { api } from '../services/api.js';

document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('ad-arsenal-container');
    if (!container) return;

    // Render Skeleton with shimmer animation
    container.innerHTML = `
        ${[1, 2, 3].map((_, i) => `
        <div class="animate-pulse min-w-[85vw] md:min-w-[22rem] max-w-sm w-full rounded-[2rem] overflow-hidden ${i > 0 ? 'hidden md:block' : ''} border border-slate-200 dark:border-slate-700/50">
            <div class="bg-slate-200 dark:bg-slate-800/50 h-56 rounded-t-[2rem]"></div>
            <div class="bg-white/80 dark:bg-slate-900/80 p-6 rounded-b-[2rem] space-y-4">
                <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                <div class="h-12 bg-slate-200 dark:bg-slate-800 rounded-xl w-full mt-4"></div>
            </div>
        </div>`).join('')}
    `;

    try {
        const ads = await api.get('/arsenal', { useApiPrefix: true });

        if (!ads || ads.length === 0) {
            container.innerHTML = `
                <div class="w-full text-center py-10">
                    <p class="text-slate-500 font-medium">Vault saat ini sedang kosong. Aset premium sedang disiapkan.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        ads.forEach((data, index) => {
            const card = document.createElement('div');
            // Advanced Premium Styling: Glassmorphism, subtle borders, floating hover
            card.className = "min-w-[85vw] md:min-w-[22rem] md:max-w-sm w-full snap-center rounded-[2rem] overflow-hidden group cursor-pointer relative flex flex-col transform transition-all duration-700 hover:-translate-y-3 z-10 hover:z-20";
            card.style.animationDelay = `${index * 150}ms`;

            // Inner wrapper for border gradient glow effect on hover
            const innerWrapper = document.createElement('div');
            innerWrapper.className = "absolute inset-0 rounded-[2rem] border border-slate-200 dark:border-slate-700/50 group-hover:border-transparent transition-colors duration-500 z-20 pointer-events-none";
            const glowBorder = document.createElement('div');
            glowBorder.className = "absolute -inset-[1px] rounded-[2rem] bg-gradient-to-b from-slate-200 to-transparent dark:from-slate-600/50 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-10 pointer-events-none";

            // --- Tag Config (Psychology: Scarcity & Authority) ---
            const tagConfig = {
                'HOT': {
                    label: '🔥 INSANE VALUE',
                    bgClass: 'bg-white/90 dark:bg-slate-900/80 border-rose-200 dark:border-rose-500/30',
                    textClass: 'text-rose-600 dark:text-rose-400',
                    animate: 'animate-pulse',
                    gradientOverlay: 'from-rose-100/90 via-white/60 dark:from-rose-950/90 dark:via-slate-900/60 to-transparent',
                    accentColor: 'rose',
                    ctaGradient: 'from-rose-500 to-orange-400 hover:from-rose-400 hover:to-orange-300 dark:from-rose-600 dark:to-orange-500 dark:hover:from-rose-500 dark:hover:to-orange-400',
                    ctaShadow: 'shadow-[0_0_20px_rgba(225,29,72,0.15)] hover:shadow-[0_0_30px_rgba(225,29,72,0.25)] dark:shadow-[0_0_20px_rgba(225,29,72,0.3)] dark:hover:shadow-[0_0_30px_rgba(225,29,72,0.5)]',
                    socialIcon: 'fa-fire',
                    socialText: 'Trending di kalangan Founders',
                    socialBgClass: 'bg-white/80 dark:bg-slate-950/60 border-slate-200 dark:border-slate-700/50',
                    socialTextClass: 'text-slate-700 dark:text-slate-200'
                },
                'NEW': {
                    label: '✨ BARU RILIS',
                    bgClass: 'bg-white/90 dark:bg-slate-900/80 border-cyan-200 dark:border-cyan-500/30',
                    textClass: 'text-cyan-600 dark:text-cyan-400',
                    animate: '',
                    gradientOverlay: 'from-cyan-100/90 via-white/60 dark:from-cyan-950/90 dark:via-slate-900/60 to-transparent',
                    accentColor: 'cyan',
                    ctaGradient: 'from-cyan-500 to-blue-400 hover:from-cyan-400 hover:to-blue-300 dark:from-cyan-600 dark:to-blue-500 dark:hover:from-cyan-500 dark:hover:to-blue-400',
                    ctaShadow: 'shadow-[0_0_20px_rgba(6,182,212,0.15)] hover:shadow-[0_0_30px_rgba(6,182,212,0.25)] dark:shadow-[0_0_20px_rgba(6,182,212,0.3)] dark:hover:shadow-[0_0_30px_rgba(6,182,212,0.5)]',
                    socialIcon: 'fa-bolt',
                    socialText: 'Aset Strategi Terbaru',
                    socialBgClass: 'bg-white/80 dark:bg-slate-950/60 border-slate-200 dark:border-slate-700/50',
                    socialTextClass: 'text-slate-700 dark:text-slate-200'
                },
                'FOUNDATION': {
                    label: '⚡ CORE ASSET',
                    bgClass: 'bg-white/90 dark:bg-slate-900/80 border-emerald-200 dark:border-emerald-500/30',
                    textClass: 'text-emerald-600 dark:text-emerald-400',
                    animate: '',
                    gradientOverlay: 'from-emerald-100/90 via-white/60 dark:from-emerald-950/90 dark:via-slate-900/60 to-transparent',
                    accentColor: 'emerald',
                    ctaGradient: 'from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 dark:from-emerald-600 dark:to-teal-500 dark:hover:from-emerald-500 dark:hover:to-teal-400',
                    ctaShadow: 'shadow-[0_0_20px_rgba(16,185,129,0.15)] hover:shadow-[0_0_30px_rgba(16,185,129,0.25)] dark:shadow-[0_0_20px_rgba(16,185,129,0.3)] dark:hover:shadow-[0_0_30px_rgba(16,185,129,0.5)]',
                    socialIcon: 'fa-check-double',
                    socialText: 'Wajib Dimiliki Pemula',
                    socialBgClass: 'bg-white/80 dark:bg-slate-950/60 border-slate-200 dark:border-slate-700/50',
                    socialTextClass: 'text-slate-700 dark:text-slate-200'
                },
                'PREMIUM': {
                    label: '💎 EXECUTIVE',
                    bgClass: 'bg-white/90 dark:bg-slate-900/80 border-fuchsia-200 dark:border-fuchsia-500/30',
                    textClass: 'text-fuchsia-600 dark:text-fuchsia-400',
                    animate: '',
                    gradientOverlay: 'from-fuchsia-100/90 via-white/60 dark:from-fuchsia-950/90 dark:via-slate-900/60 to-transparent',
                    accentColor: 'fuchsia',
                    ctaGradient: 'from-fuchsia-500 to-purple-400 hover:from-fuchsia-400 hover:to-purple-300 dark:from-fuchsia-600 dark:to-purple-500 dark:hover:from-fuchsia-500 dark:hover:to-purple-400',
                    ctaShadow: 'shadow-[0_0_20px_rgba(192,38,211,0.15)] hover:shadow-[0_0_30px_rgba(192,38,211,0.25)] dark:shadow-[0_0_20px_rgba(192,38,211,0.3)] dark:hover:shadow-[0_0_30px_rgba(192,38,211,0.5)]',
                    socialIcon: 'fa-crown',
                    socialText: 'Akses Terbatas 1% Top',
                    socialBgClass: 'bg-white/80 dark:bg-slate-950/60 border-slate-200 dark:border-slate-700/50',
                    socialTextClass: 'text-slate-700 dark:text-slate-200'
                }
            };

            const tag = tagConfig[data.tag] || tagConfig['NEW'];

            // --- Image Section ---
            const hasImage = data.image_url && data.image_url !== 'null';
            const imageSection = hasImage
                ? `<img src="${data.image_url}" alt="${data.title}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out" loading="lazy">`
                : `<div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                     <i class="fas fa-cube text-5xl text-slate-300 dark:text-slate-700 group-hover:scale-110 transition-transform duration-1000 ease-out"></i>
                   </div>`;

            // --- CTA Text (Psychology: Loss Aversion + Curiosity Gap) ---
            const ctaTexts = {
                'HOT': 'Amankan Harga Sekarang →',
                'NEW': 'Bongkar Rahasianya →',
                'FOUNDATION': 'Mulai Scale-Up →',
                'PREMIUM': 'Akses Vault Eksklusif →'
            };
            const ctaText = ctaTexts[data.tag] || 'Eksplorasi Aset →';

            const contentHtml = `
                <!-- Background ambient glow based on tag color -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-${tag.accentColor}-500/20 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-0 pointer-events-none"></div>

                <!-- Hero Image Section with Deep Gradient Overlay -->
                <div class="relative h-56 overflow-hidden z-20">
                    ${imageSection}
                    
                    <!-- Deep Gradient Overlay (bottom) integrating smoothly to the dark card body -->
                    <div class="absolute inset-0 bg-gradient-to-t ${tag.gradientOverlay}"></div>
                    
                    <!-- Top Overlay Gradient (for tag readability) -->
                    <div class="absolute inset-0 bg-gradient-to-b from-white/50 dark:from-slate-950/50 to-transparent h-24"></div>

                    <!-- Tag Badge (Psychology: Scarcity/Authority with Glassmorphism) -->
                    <div class="absolute top-4 left-4 px-3.5 py-1.5 rounded-full ${tag.bgClass} border backdrop-blur-md ${tag.textClass} text-[10px] font-black uppercase tracking-widest shadow-lg ${tag.animate} flex items-center gap-1.5 transition-all">
                        ${tag.label}
                    </div>

                    <!-- Social Proof Badge (Psychology: Authority / Bandwagon) -->
                    <div class="absolute bottom-4 left-4 flex items-center gap-2 px-3 py-1.5 rounded-full ${tag.socialBgClass} backdrop-blur-md ${tag.socialTextClass} text-[10px] font-semibold">
                        <i class="fas ${tag.socialIcon} text-${tag.accentColor}-500 dark:text-${tag.accentColor}-400"></i>
                        <span>${tag.socialText}</span>
                    </div>
                </div>

                <!-- Deep Content Section (Glassmorphism & High Contrast) -->
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl p-6 md:p-8 flex flex-col flex-grow z-20 relative">
                    
                    <!-- Title (Visual Hierarchy: Elegant Serif/Sans mix) -->
                    <h4 class="font-black text-xl md:text-2xl text-slate-900 dark:text-white mb-3 leading-tight group-hover:text-${tag.accentColor}-600 dark:group-hover:text-${tag.accentColor}-400 transition-colors line-clamp-2 tracking-tight">
                        ${data.title}
                    </h4>
                    
                    <!-- Description (Curiosity Gap: Keep it sharp and readable) -->
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-8 flex-grow line-clamp-3">
                        ${data.description}
                    </p>

                    <!-- CTA Button (Psychology: High-Value Action Button) -->
                    <a href="${data.link}" target="_blank" rel="noopener noreferrer"
                       class="w-full py-4 bg-gradient-to-r ${tag.ctaGradient} text-white rounded-xl font-bold text-xs uppercase tracking-widest flex items-center justify-center gap-2 transition-all ${tag.ctaShadow} active:scale-[0.98] group/cta relative overflow-hidden">
                        
                        <!-- CTA Shine Effect using Tailwind JIT -->
                        <div class="absolute top-0 -left-[100%] h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-20 group-hover/cta:left-[200%] transition-all duration-1000 ease-in-out"></div>
                        
                        <span class="relative z-10">${ctaText}</span>
                        <i class="fas fa-arrow-right text-xs group-hover/cta:translate-x-1.5 transition-transform relative z-10"></i>
                    </a>
                </div>
            `;

            card.innerHTML = contentHtml;
            card.appendChild(glowBorder);
            card.appendChild(innerWrapper);

            container.appendChild(card);
        });

    } catch (error) {
        console.error("Ad Arsenal Frontend Load Failed:", error);
        container.innerHTML = `
            <div class="w-full text-center py-10">
                <p class="text-rose-500 font-medium">Gagal menghubungkan ke Vault.</p>
            </div>
        `;
    }
});
