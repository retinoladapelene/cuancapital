/**
 * CuanCapital Experience OS: Gamification Configuration
 * Defines XP rewards, Levels, and Achievements.
 */

window.GAMIFICATION_CONFIG = {

    // Event-based XP Mapping
    xpRewards: {
        reverse_calculate: 50,    // User calculates target in RGP
        feasibility_green: 100,   // RGP Feasibility is High/Green
        save_blueprint: 150,      // Saving any blueprint successfully
        profit_over_10m: 200,     // Profit simulator hits > 10jt net profit
        mentor_evaluate: 200,     // Running the Business Mentor evaluation
        generate_roadmap: 250,    // Creating a new execution roadmap
        roadmap_toggle: 20        // Checking off an item in the roadmap
    },

    // Level progression titles (Total XP required)
    // Formula: Level = floor(totalXP / 500) + 1
    // (So Level 1 corresponds to 0-499 XP, Level 2 is 500-999, etc.)
    levelTitles: {
        1: "Rookie Planner",
        5: "Smart Executor",
        10: "Growth Hacker",
        20: "Strategic Builder",
        50: "Cuan Architect",
        100: "Business Titan"
    }

};
