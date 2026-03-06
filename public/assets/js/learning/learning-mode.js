class LearningMode {
    constructor() {
        this.enabled = localStorage.getItem('learning_mode') === 'true';
        this.navBtn = document.getElementById('nav-learning-btn');

        this.bindNavToggle();

        // Ensure body class is correct on load
        if (this.enabled) {
            document.body.classList.add('learning-mode');
            if (this.navBtn) this.navBtn.classList.add('active');

            // Wait for DOM to be ready before triggering glossary icons on initial load
            setTimeout(() => {
                if (window.glossaryEngine) window.glossaryEngine.toggleIcons(true);
            }, 100);
        }
    }

    enable() {
        this.enabled = true;
        localStorage.setItem('learning_mode', 'true');
        document.body.classList.add('learning-mode');

        if (this.navBtn) this.navBtn.classList.add('active');

        this.showActivatedBanner();

        // Trigger Guided Tour evaluation
        if (window.guidedTourManager) {
            window.guidedTourManager.init();
        }

        // Toggle Glossary UI
        if (window.glossaryEngine) window.glossaryEngine.toggleIcons(true);
    }

    disable() {
        this.enabled = false;
        localStorage.setItem('learning_mode', 'false');
        document.body.classList.remove('learning-mode');

        if (this.navBtn) this.navBtn.classList.remove('active');

        // Toggle Glossary UI
        if (window.glossaryEngine) window.glossaryEngine.toggleIcons(false);
    }

    toggle() {
        this.enabled ? this.disable() : this.enable();
    }

    bindNavToggle() {
        if (!this.navBtn) return;

        this.navBtn.addEventListener('click', () => {
            this.toggle();
        });
    }

    showActivatedBanner() {
        const banner = document.createElement('div');
        banner.className = 'learning-banner';
        banner.innerText = "🎓 Learning Mode Activated — Semua fitur akan dijelaskan";
        document.body.appendChild(banner);

        setTimeout(() => banner.remove(), 4000);
    }
}

window.learningMode = new LearningMode();
