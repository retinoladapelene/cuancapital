class LearningEngine {
    constructor() {
        // Will be used for evaluation events in the future
    }

    evaluate(state) {
        if (!window.learningMode || !window.learningMode.enabled) return;

        // Concept code for Phase 2:
        /*
        if (state.margin < 0.25) {
            this.showInsight("Margin kamu tipis. Ideal scaling > 35%");
        }
        */
    }

    showInsight(message) {
        if (window.learningUI) {
            window.learningUI.displayInsight(message);
        }
    }
}

window.learningEngine = new LearningEngine();
