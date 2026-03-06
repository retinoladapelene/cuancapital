import { gamificationEngine } from './gamification-engine.js';
import { onboardingEngine } from './onboarding-engine.js';
import { learningHud } from './learning-hud.js';

/**
 * 🚀 CuanCapital Experience OS
 * The central orchestrator that non-intrusively binds to global events
 * and coordinates Gamification, Progress, and Onboarding layers.
 */
export const experienceEngine = {
    init() {
        console.log("🚀 Experience OS: Initializing...");

        // 1. Initialize Sub-Engines
        learningHud.init();
        onboardingEngine.init();

        // 2. Fetch Initial State from Server
        gamificationEngine.loadState();

        // 3. Bind Global Passive Observers
        this.bindObservers();
    },

    /**
     * Listen to global window/document events that the existing app fires.
     * ZERO modifications to the core app required.
     * 
     * [Phase 16 Update]: Polling observers have been removed because 
     * Gamification.refresh() is now explicitly injected into API fetch responses 
     * across mentor-wizard.js, reverse-goal-planner.js, and roadmap-handler.js 
     * to eliminate DOM polling overhead.
     */
    bindObservers() {
        // Observers are decommissioned. 
        // Core features invoke window.Gamification.refresh() directly.
        // E.g. when checking off a roadmap item, or generating a planner.
    }
};
