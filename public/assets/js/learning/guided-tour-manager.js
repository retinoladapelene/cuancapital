/**
 * Guided Tour Manager - Experience OS Phase 2
 * Handles initialization, auto-starting, and manual triggers for guided tours
 */

class GuidedTourManager {
    init() {
        // Stop if user is a guest — check if the guest overlay is actually VISIBLE
        const guestOverlay = document.querySelector('.guest-only');
        if (guestOverlay) {
            const style = window.getComputedStyle(guestOverlay);
            if (style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0') return;
        }

        // Only auto-start tour for brand-new accounts (flag set by registration handler)
        const isNewUser = localStorage.getItem('tour_trigger_new_user');
        const onboardDone = localStorage.getItem('tour_mainOnboarding_done');
        if (isNewUser && !onboardDone) {
            // Clear the trigger flag immediately so it won't fire again
            localStorage.removeItem('tour_trigger_new_user');
            if (window.learningMode && !window.learningMode.enabled) {
                window.learningMode.enable();
            }
            this.autoStart('mainOnboarding');
        }

        // Only trigger other feature tours (if defined later) if Learning Mode is explicitly ON
        if (!window.learningMode || !window.learningMode.enabled) return;

        // Auto start Profit Simulator tour (if applicable, e.g. after moving to that view)
        // Typically, you might listen to an event when navigating to that step.
        // For now, let's bind the manual buttons first.
        this.injectManualButtons();
    }

    autoStart(tourName) {
        // Check local storage if the user has already seen this tour
        const done = localStorage.getItem(`tour_${tourName}_done`);
        if (!done) {
            // Delay start so UI has time to render and user can settle
            setTimeout(() => {
                if (window.learningMode && window.learningMode.enabled) {
                    if (window.guidedTour) {
                        window.guidedTour.start(tourName);
                    }
                }
            }, 1000); // 1-second delay
        }
    }

    injectManualButtons() {
        // Bind to any button with data-start-tour attribute
        document.querySelectorAll('[data-start-tour]').forEach(btn => {
            // Remove old listener to avoid duplicates if re-injected
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);

            newBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const tourName = newBtn.dataset.startTour;

                // If learning mode is off, turn it on temporarily for the tour or block it?
                // It's cleaner to turn it on if they explicitly request a tour.
                if (window.learningMode && !window.learningMode.enabled) {
                    window.learningMode.toggle(); // Auto-enable learning mode
                }

                if (window.guidedTour) {
                    // Reset completion state so it plays again
                    localStorage.removeItem(`tour_${tourName}_done`);
                    window.guidedTour.start(tourName);
                }
            });
        });
    }
}

window.guidedTourManager = new GuidedTourManager();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Wait for learningMode to be fully initialized
    setTimeout(() => {
        if (window.guidedTourManager) {
            window.guidedTourManager.init();
        }
    }, 500);
});
