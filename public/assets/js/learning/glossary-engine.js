class GlossaryEngine {
    constructor() {
        this.tooltip = null;
        this.cache = {};
        this.init();
    }

    init() {
        // Global Click Listener for Glossary Tooltip activation & dismissal
        document.addEventListener('click', (e) => {
            // 1. If clicking outside of an active tooltip, close it
            if (this.tooltip && !e.target.closest('.glossary-tooltip') && !e.target.closest('.biz-term')) {
                this.tooltip.remove();
                this.tooltip = null;
            }

            // 2. If Learning Mode is OFF, do nothing further
            if (!window.learningMode || !window.learningMode.enabled) return;

            // 3. Handle click on a term / info icon
            const term = e.target.closest('.biz-term');
            if (!term) return;

            // Stop click from bubbling up to parent cards (e.g. scrollIntoView panels)
            e.stopPropagation();
            e.preventDefault();

            const key = term.dataset.term;
            if (key) {
                // If tooltip is already active for THIS term, clicking again closes it
                if (this.tooltip && this.tooltip.dataset.activeKey === key) {
                    this.tooltip.remove();
                    this.tooltip = null;
                    return;
                }
                this.showTooltip(term, key);
            }
        });
    }

    async showTooltip(element, key) {
        if (this.cache[key]) {
            this.renderTooltip(element, this.cache[key], key);
            return;
        }

        try {
            const res = await fetch(`/api/learning/glossary/${key}`);
            const json = await res.json();

            if (json.status === 'success' && json.data) {
                this.cache[key] = json.data;
                this.renderTooltip(element, json.data, key);
            }
        } catch (error) {
            console.error('Error fetching glossary term:', error);
        }
    }

    renderTooltip(element, data, key) {
        if (this.tooltip) this.tooltip.remove();

        const div = document.createElement('div');
        div.className = 'glossary-tooltip';
        div.dataset.activeKey = key;

        div.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h4 class="text-[#00ffc8] font-bold text-base m-0">${data.title}</h4>
                <button class="text-slate-400 hover:text-white" onclick="this.closest('.glossary-tooltip').remove(); window.glossaryEngine.tooltip = null;">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <p class="text-slate-300 text-sm m-0 leading-relaxed mb-1 font-bold">Penjelasan Sederhana:</p>
            <p class="text-slate-300 text-sm m-0 leading-relaxed mb-3">${data.simple_explanation || data.explanation}</p>
            
            <p class="text-slate-400 text-xs m-0 leading-relaxed mb-1 font-bold opacity-75">Penjelasan Kompleks:</p>
            <p class="text-slate-400 text-xs m-0 leading-relaxed">${data.advanced_explanation || ''}</p>

            ${data.formula ? `<div class="mt-3 p-2 bg-black/40 border border-blue-400/20 rounded-lg text-blue-400 font-mono text-xs">${data.formula}</div>` : ''}
        `;

        document.body.appendChild(div);

        // Position it smartly below the element, ensuring it doesn't overflow
        const rect = element.getBoundingClientRect();
        let topPos = rect.bottom + window.scrollY + 10;
        let leftPos = rect.left;

        // Simple right edge boundary check
        if (leftPos + 300 > window.innerWidth) {
            leftPos = window.innerWidth - 320;
        }

        div.style.top = topPos + 'px';
        div.style.left = Math.max(10, leftPos) + 'px';

        this.tooltip = div;
    }

    // Called by main.js toggleLearningMode
    toggleIcons(enabled) {
        const terms = document.querySelectorAll('.biz-term');

        if (enabled) {
            // Add icon to all .biz-term elements if it doesn't already exist
            terms.forEach(term => {
                if (!term.querySelector('.glossary-icon')) {
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-info-circle glossary-icon ml-1.5 opacity-80 text-blue-400 cursor-pointer hover:text-blue-300 transition-colors pointer-events-none';
                    // pointer-events-none ensures the click event bubbles up to the parent .biz-term smoothly
                    term.appendChild(icon);
                    // Ensure the parent itself has cursor-pointer indicating it is clickable
                    term.classList.add('cursor-pointer');
                    term.classList.remove('cursor-help'); // remove old hover class
                }
            });
        } else {
            // Remove all injected icons
            terms.forEach(term => {
                const icon = term.querySelector('.glossary-icon');
                if (icon) icon.remove();
                term.classList.remove('cursor-pointer');
            });
            // If active, clear tooltip
            if (this.tooltip) {
                this.tooltip.remove();
                this.tooltip = null;
            }
        }
    }
}

window.glossaryEngine = new GlossaryEngine();
