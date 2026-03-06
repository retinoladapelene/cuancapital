/**
 * Custom Toast Notification System
 * Style: Glassmorphism + Neon
 * Position: Top-Center (all toasts)
 */
export class Toast {
    static containerId = 'toast-container';

    static init() {
        if (!document.getElementById(this.containerId)) {
            const container = document.createElement('div');
            container.id = this.containerId;
            // Top-center positioning via left:50% + translateX(-50%)
            // items-center keeps stacked toasts centered
            // max-h + overflow-y prevents stack overflow on mobile
            container.className = 'fixed top-6 flex flex-col items-center gap-4 pointer-events-none';
            container.style.cssText = [
                'z-index: 99999 !important',
                'position: fixed',
                'top: 24px',
                'left: 50%',
                'transform: translateX(-50%)',
                'max-height: 80vh',
                'overflow-y: auto',
            ].join('; ');
            document.body.appendChild(container);
        }
    }

    static show(message, type = 'info') {
        this.init();
        const container = document.getElementById(this.containerId);

        // Styles based on type
        const styles = {
            success: {
                border: 'border-emerald-500/50',
                bg: 'bg-emerald-900/80',
                text: 'text-emerald-50',
                shadow: 'shadow-[0_0_15px_-3px_rgba(16,185,129,0.3)]'
            },
            error: {
                border: 'border-rose-500/50',
                bg: 'bg-rose-900/80',
                text: 'text-rose-50',
                shadow: 'shadow-[0_0_15px_-3px_rgba(244,63,94,0.3)]'
            },
            warning: {
                border: 'border-amber-500/50',
                bg: 'bg-amber-900/80',
                text: 'text-amber-50',
                shadow: 'shadow-[0_0_15px_-3px_rgba(245,158,11,0.3)]'
            },
            info: {
                border: 'border-blue-500/50',
                bg: 'bg-slate-900/80',
                text: 'text-slate-50',
                shadow: 'shadow-[0_0_15px_-3px_rgba(59,130,246,0.3)]'
            }
        };

        const style = styles[type] || styles.info;

        // Create notification element
        // NOTE: No translate-x-* on toast item — only container has translateX(-50%)
        // Animation: slide down from top via -translate-y-4 → translate-y-0
        const toast = document.createElement('div');
        // overflow: visible so icon can pop above the card
        toast.className = [
            'pointer-events-auto',
            'flex items-center gap-3',
            'w-[420px] max-w-[90vw] min-h-[90px] px-6 py-5 pl-28',
            'rounded-xl',
            'border', style.border,
            style.bg, 'backdrop-blur-md',
            style.text,
            style.shadow,
            'transform transition-all duration-300 ease-out',
            'opacity-0 -translate-y-4',
        ].join(' ');
        toast.style.cssText = 'margin-top: 60px; overflow: visible;';

        toast.innerHTML = `
            <img src="/assets/icon/aksa_notif.png"
                 style="position:absolute; top:40px; left:5px; width:150px; height:150px; object-fit:contain; filter:drop-shadow(0 8px 15px rgba(0,0,0,0.6)) drop-shadow(0 0 15px rgba(16,185,129,0.3));"
                 alt="notif" />
            <div class="flex-1 font-semibold text-[15px] leading-6 text-center pr-6">
                ${message}
            </div>
            <button class="text-white/40 hover:text-white transition-colors shrink-0 ml-2" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Animate in: slide down from top
        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', '-translate-y-4');
        });

        // Auto remove after 5s with slide-up exit animation
        setTimeout(() => {
            toast.classList.add('opacity-0', '-translate-y-4');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 300);
        }, 5000);
    }

    static success(message) {
        this.show(message, 'success');
    }

    static error(message) {
        this.show(message, 'error');
    }

    static warning(message) {
        this.show(message, 'warning');
    }

    static info(message) {
        this.show(message, 'info');
    }
}
