export const progressEngine = {
    weights: {
        rgp: 20,
        simulator: 20,
        mentor: 25,
        roadmap: 20,
        actionPercent: 15 // Up to 15 points based on completion
    },

    recalculate(milestones, actionPercentComplete = 0) {
        let total = 0;

        if (milestones.rgp) total += this.weights.rgp;
        if (milestones.simulator) total += this.weights.simulator;
        if (milestones.mentor) total += this.weights.mentor;
        if (milestones.roadmap) total += this.weights.roadmap;

        // Calculate fraction of the 15 points allocated for action execution
        const actionFraction = (actionPercentComplete / 100) * this.weights.actionPercent;
        total += actionFraction;

        // Ensure ceiling
        total = Math.min(100, Math.round(total));

        this.updateVisually(total);
    },

    updateVisually(percentage) {
        const bar = document.getElementById('journey-progress-bar');
        const text = document.getElementById('journey-percent-text');
        const wrapper = document.getElementById('founder-journey-wrapper');

        if (bar && text) {
            bar.style.width = `${percentage}%`;
            text.innerText = `${percentage}%`;

            // Only show the top bar if there is actual progress
            if (percentage > 0 && wrapper) {
                wrapper.classList.remove('hidden');
                // Allow browser to render 'hidden' removal before transitioning
                setTimeout(() => {
                    wrapper.classList.remove('-translate-y-full');
                }, 50);
            }
        }
    }
};
