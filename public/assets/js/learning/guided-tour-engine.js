/**
 * Guided Tour Engine - Experience OS Phase 2
 * Core logic for rendering tooltips, overlays, and highlighting UI elements
 */

class GuidedTour {
    constructor() {
        this.steps = [];
        this.currentStep = 0;
        this.overlay = null;
        this.tooltip = null;
        this.tourName = null;
    }

    start(tourName) {
        // Only run if Learning Mode is enabled
        if (!window.learningMode || !window.learningMode.enabled) return;

        this.tourName = tourName;
        this.steps = window.guidedTourSteps ? window.guidedTourSteps[tourName] : [];
        this.currentStep = 0;

        if (!this.steps || !this.steps.length) {
            console.warn(`[GuidedTour] No steps found for tour: ${tourName}`);
            return;
        }

        this.renderOverlay();
        this.showStep();
    }

    showStep() {
        const step = this.steps[this.currentStep];
        if (!step) return this.complete();

        // Trigger dynamic pre-action if defined (e.g., clicking next, opening modals)
        if (typeof step.action === 'function') {
            try {
                step.action();
            } catch (e) {
                console.error('[GuidedTour] Error executing step action:', e);
            }
        }

        const element = document.querySelector(step.selector);

        if (!element) {
            console.warn(`[GuidedTour] Element not found for selector: ${step.selector}`);
            // If element not found, auto skip to next
            return this.next();
        }

        // Slight offset for fixed headers
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Small delay to allow scroll before highlighting
        setTimeout(() => {
            this.highlight(element);
            this.renderTooltip(element, step);
        }, 300);
    }

    highlight(element) {
        // Remove previous highlights
        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
        });
        document.querySelectorAll('.tour-ancestor-fix').forEach(el => {
            el.classList.remove('tour-ancestor-fix');
            el.style.zIndex = '';
        });

        // Add highlight
        element.classList.add('tour-highlight');

        // Add stacking context fix to all ancestors up to the body
        let parent = element.parentElement;
        while (parent && parent !== document.body && parent !== document.documentElement) {
            parent.classList.add('tour-ancestor-fix');

            // Give them a z-index higher than the overlay (9998)
            // But lower than the tooltip (10001)
            parent.style.zIndex = '9999';
            parent = parent.parentElement;
        }
    }

    renderOverlay() {
        if (this.overlay) this.overlay.remove();

        this.overlay = document.createElement('div');
        this.overlay.className = 'tour-overlay';
        document.body.appendChild(this.overlay);
    }

    renderTooltip(element, step) {
        if (this.tooltip) this.tooltip.remove();

        const div = document.createElement('div');
        div.className = 'tour-tooltip animate-fade-in-up';

        div.innerHTML = `
            <div class="tour-header flex justify-between items-center mb-3 text-xs font-bold text-slate-400">
                <span class="tour-progress tracking-widest uppercase">
                    Step ${this.currentStep + 1} / ${this.steps.length}
                </span>
                <button id="tour-close-x" class="text-slate-500 hover:text-rose-400 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <h3 class="text-lg font-black text-emerald-400 mb-2">${step.title}</h3>
            <p class="text-sm text-slate-300 leading-relaxed mb-5">${step.content}</p>
            <div class="tour-controls flex justify-between items-center gap-2">
                <button id="tour-skip" class="text-xs font-bold text-slate-500 hover:text-slate-300 transition-colors">Lewati Tour</button>
                <div class="flex gap-2">
                    <button id="tour-prev" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed" ${this.currentStep === 0 ? 'disabled' : ''}>Back</button>
                    <button id="tour-next" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 transition-all">
                        ${this.currentStep < this.steps.length - 1 ? 'Next' : 'Selesai'}
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(div);

        // Positioning logic
        const rect = element.getBoundingClientRect();

        // Calculate optimal position (prefer bottom, then top, then right/left)
        let topPos = rect.bottom + window.scrollY + 15;
        let leftPos = rect.left + window.scrollX;

        // Clamp to window bounds
        if (leftPos + 320 > window.innerWidth) { // 320px is roughly max-width
            leftPos = window.innerWidth - 340;
        }
        if (leftPos < 20) leftPos = 20;

        div.style.top = topPos + 'px';
        div.style.left = leftPos + 'px';

        // Bind events
        document.getElementById('tour-next').onclick = () => this.next();
        document.getElementById('tour-prev').onclick = () => this.prev();
        document.getElementById('tour-skip').onclick = () => this.complete();
        document.getElementById('tour-close-x').onclick = () => this.end();

        this.tooltip = div;
    }

    next() {
        if (this.currentStep < this.steps.length - 1) {
            this.currentStep++;
            this.showStep();
        } else {
            this.complete();
        }
    }

    prev() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.showStep();
        }
    }

    complete() {
        localStorage.setItem(`tour_${this.tourName}_done`, 'true');
        this.end();
    }

    end() {
        if (this.overlay) this.overlay.remove();
        if (this.tooltip) this.tooltip.remove();

        document.querySelectorAll('.tour-highlight').forEach(el => {
            el.classList.remove('tour-highlight');
        });

        document.querySelectorAll('.tour-ancestor-fix').forEach(el => {
            el.classList.remove('tour-ancestor-fix');
            el.style.zIndex = '';
        });

        this.currentStep = 0;
    }
}

// Instantiate globally
window.guidedTour = new GuidedTour();
