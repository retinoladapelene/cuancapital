import { api } from '../services/api.js';
import { select, showToast } from '../utils/helpers.js';

class RoadmapHandler {
    constructor() {
        this.init();
    }

    init() {
        const btnGenerate = select('#btn-generate-roadmap');
        if (btnGenerate) {
            btnGenerate.addEventListener('click', () => this.generateRoadmap());
        }
    }

    async generateRoadmap() {
        const btn = select('#btn-generate-roadmap');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.disabled = true;

        try {
            const response = await api.post('/mentor/roadmap/generate', null, { useApiPrefix: true });

            if (!response.success || !response.data || !response.data.job_id) {
                showToast(response.message || 'Failed to start generation.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
                return;
            }

            const jobId = response.data.job_id;
            let attempts = 0;
            const maxAttempts = 20;
            let currentDelay = 2000;

            const pollStatus = async () => {
                attempts++;

                try {
                    const statusRes = await fetch(`/api/jobs/${jobId}/status`, {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token') || ''}`
                        }
                    });
                    const statusData = await statusRes.json();

                    if (statusData.status === 'completed') {
                        // Fetch the latest V1 roadmap payload
                        const finalRes = await api.get('/mentor/roadmap', { useApiPrefix: true });
                        if (finalRes.success && finalRes.data?.roadmap) {
                            this.renderRoadmap(finalRes.data.roadmap);
                            showToast('Roadmap generated successfully!', 'success');
                            select('#roadmap-container').scrollIntoView({ behavior: 'smooth' });
                            if (window.Gamification) window.Gamification.refresh();
                        } else {
                            showToast('Failed to load roadmap data.', 'error');
                        }
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        return;
                    }

                    if (statusData.status === 'failed') {
                        showToast(statusData.error_message || 'Background generation failed.', 'error');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        return;
                    }

                    if (attempts >= maxAttempts) {
                        showToast('Request timeout. Try again later.', 'error');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        return;
                    }

                    currentDelay = Math.min(currentDelay + 1000, 5000);
                    setTimeout(pollStatus, currentDelay);

                } catch (e) {
                    console.error('Polling error:', e);
                    setTimeout(pollStatus, currentDelay);
                }
            };

            setTimeout(pollStatus, currentDelay);

        } catch (e) {
            console.error(e);
            showToast(e.message || 'Failed to generate roadmap', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    renderRoadmap(roadmap) {
        const container = select('#roadmap-container');
        const stepsContainer = select('#roadmap-steps');
        container.classList.remove('hidden');

        let html = `
            <!-- Vertical Connector Line (Absolute) -->
            <div class="absolute left-8 md:left-1/2 top-4 bottom-4 w-0.5 bg-slate-200 dark:bg-slate-700 -ml-px hidden md:block"></div>
        `;

        roadmap.steps.forEach((step, index) => {
            const isLeft = index % 2 === 0;
            const isCompleted = step.status === 'completed';
            const isLocked = step.status === 'locked';

            // Status Colors
            let statusColor = isCompleted ? 'bg-emerald-500 border-emerald-500 text-white' :
                (isLocked ? 'bg-slate-200 border-slate-300 text-slate-400 dark:bg-slate-800 dark:border-slate-700' : 'bg-white border-blue-500 text-blue-600 dark:bg-slate-800 dark:text-white shadow-lg shadow-blue-500/10');

            let icon = isCompleted ? 'fa-check' : (isLocked ? 'fa-lock' : 'fa-flag');

            html += `
            <div class="relative flex items-center justify-between md:justify-normal md:${isLeft ? 'flex-row-reverse' : 'flex-row'} mb-8 group" data-step-id="${step.id}">
                
                <!-- 1. The Empty Space (For ZigZag) -->
                <div class="hidden md:block w-5/12"></div>
                
                <!-- 2. The Center Connector Dot -->
                <div class="absolute left-8 md:left-1/2 -ml-3 md:-ml-3 w-6 h-6 rounded-full border-4 ${isCompleted ? 'border-emerald-500 bg-white' : 'border-slate-200 bg-slate-100'} z-10 transition-colors duration-500"></div>

                <!-- 3. The Content Card -->
                <div class="w-full pl-16 md:pl-0 md:w-5/12 ${isLeft ? 'md:mr-auto md:pr-8 md:text-right' : 'md:ml-auto md:pl-8 md:text-left'}">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border ${isCompleted ? 'border-emerald-200' : 'border-slate-200'} dark:border-slate-700 shadow-sm transition-all hover:shadow-md relative overflow-hidden group-hover:-translate-y-1 duration-300">
                        
                        <!-- Status Badge -->
                        <div class="text-[10px] font-bold uppercase tracking-wider mb-2 ${isCompleted ? 'text-emerald-500' : 'text-slate-400'}">
                            Step ${step.order} • ${step.strategy_tag}
                        </div>

                        <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-2">${step.title}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">${step.description}</p>

                        <!-- Actions Checklist -->
                        <div class="space-y-2 text-left">
                            ${step.actions.map(action => `
                                <label class="flex items-start gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-900/50 cursor-pointer transition-colors">
                                    <div class="relative flex items-center mt-0.5">
                                        <input type="checkbox" 
                                            class="peer h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 cursor-pointer roadmap-action-checkbox"
                                            ${action.is_completed ? 'checked' : ''}
                                            ${isLocked ? 'disabled' : ''}
                                            data-action-id="${action.id}"
                                        >
                                    </div>
                                    <span class="text-sm text-slate-600 dark:text-slate-300 ${action.is_completed ? 'text-emerald-600 line-through' : ''} peer-checked:text-emerald-600 peer-checked:line-through transition-colors select-none">
                                        ${action.action_text}
                                    </span>
                                </label>
                            `).join('')}
                        </div>

                        ${isLocked ? `
                        <div class="absolute inset-0 bg-slate-100/50 dark:bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center z-20">
                            <div class="bg-white dark:bg-slate-800 px-4 py-2 rounded-full shadow-lg flex items-center gap-2 text-slate-500 text-xs font-bold border border-slate-200">
                                <i class="fas fa-lock"></i> Locked
                            </div>
                        </div>
                        ` : ''}

                    </div>
                </div>
            </div>
            `;
        });

        stepsContainer.innerHTML = html;

        // Re-bind checkboxes
        this.bindCheckboxes();
    }

    bindCheckboxes() {
        const checkboxes = document.querySelectorAll('.roadmap-action-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', (e) => this.toggleAction(e.target));
        });
    }

    async toggleAction(checkbox) {
        const actionId = checkbox.dataset.actionId;
        const isChecked = checkbox.checked;
        const label = checkbox.closest('label').querySelector('span');

        // Optimistic UI Update
        if (isChecked) {
            label.classList.add('text-emerald-600', 'line-through');
        } else {
            label.classList.remove('text-emerald-600', 'line-through');
        }

        try {
            const response = await api.post(`/mentor/roadmap/action/${actionId}/toggle`, null, { useApiPrefix: true });

            if (response.success) {
                if (response.step_completed) {
                    showToast('Step Completed! Next step unlocked.', 'success');
                    this.refreshRoadmap(); // Refresh to see unlocked steps
                }

                // --- PHASE 16: Emotional Retention Loop Update ---
                if (window.Gamification) window.Gamification.refresh();
            }
        } catch (e) {
            console.error(e);
            // Revert UI
            checkbox.checked = !isChecked;
            if (label) {
                if (isChecked) label.classList.remove('text-emerald-600', 'line-through');
                else label.classList.add('text-emerald-600', 'line-through');
            }
            showToast('Failed to update action', 'error');
        }
    }

    async refreshRoadmap() {
        try {
            const response = await api.get('/mentor/roadmap', { useApiPrefix: true });
            if (response.success && response.data?.roadmap) {
                this.renderRoadmap(response.data.roadmap);
            }
        } catch (e) {
            console.error(e);
        }
    }
}

export const roadmapHandler = new RoadmapHandler();
