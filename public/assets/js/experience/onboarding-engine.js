export const onboardingEngine = {
    state: {
        tutorialEnabled: false
    },

    init() {
        // Hydrate state from localStorage
        const savedState = localStorage.getItem('cuan_tutorial_mode');
        this.state.tutorialEnabled = savedState !== 'disabled'; // Default is enabled until explicitly disabled

        // Bind toggle logic
        const toggleBtn = document.getElementById('toggle-tutorial-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleTutorial(toggleBtn));
            this.updateToggleUI(toggleBtn);
        }

        if (this.state.tutorialEnabled) {
            this.scanAndHighlight();
        }
    },

    toggleTutorial(btn) {
        this.state.tutorialEnabled = !this.state.tutorialEnabled;
        localStorage.setItem('cuan_tutorial_mode', this.state.tutorialEnabled ? 'enabled' : 'disabled');
        this.updateToggleUI(btn);

        if (this.state.tutorialEnabled) {
            this.scanAndHighlight();
        } else {
            this.clearHighlights();
        }
    },

    updateToggleUI(btn) {
        const knob = document.getElementById('tutorial-toggle-knob');
        if (!knob) return;

        if (this.state.tutorialEnabled) {
            btn.querySelector('span:nth-child(2)').classList.remove('bg-slate-700');
            btn.querySelector('span:nth-child(2)').classList.add('bg-emerald-500');
            knob.classList.add('translate-x-4');
            knob.classList.remove('translate-x-0');
        } else {
            btn.querySelector('span:nth-child(2)').classList.remove('bg-emerald-500');
            btn.querySelector('span:nth-child(2)').classList.add('bg-slate-700');
            knob.classList.remove('translate-x-4');
            knob.classList.add('translate-x-0');
        }
    },

    /**
     * Non-intrusive DOM scanning: Applies glowing rings to elements flagged with `data-onboard-step`
     */
    scanAndHighlight() {
        const onboardableItems = document.querySelectorAll('[data-onboard-step]');
        onboardableItems.forEach(item => {
            // Apply highlight styling class globally
            item.classList.add('ring-2', 'ring-emerald-400', 'ring-offset-2', 'ring-offset-slate-950', 'animate-pulse');

            // Optional: Inject brief label
            if (item.getAttribute('data-onboard-label') && !item.querySelector('.onboard-label')) {
                const label = document.createElement('span');
                label.className = 'onboard-label absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-emerald-400 text-[10px] font-bold px-2 py-1 rounded shadow-lg whitespace-nowrap pointer-events-none z-50';
                label.innerText = item.getAttribute('data-onboard-label');
                item.style.position = item.style.position === 'static' ? 'relative' : item.style.position;
                item.appendChild(label);
            }
        });
    },

    clearHighlights() {
        const onboardableItems = document.querySelectorAll('[data-onboard-step]');
        onboardableItems.forEach(item => {
            item.classList.remove('ring-2', 'ring-emerald-400', 'ring-offset-2', 'ring-offset-slate-950', 'animate-pulse');
            const label = item.querySelector('.onboard-label');
            if (label) label.remove();
        });
    }
};
