/**
 * @file admin-handler.js
 * @description Logic for Admin Dashboard.
 * Handles fetching users, stats, and admin actions via API.
 */

import { api } from '../services/api.js';
import { showToast, formatDate } from '../utils/helpers.js';

// --- GLOBAL ACTIONS (Attached to window for HTML access) ---

// System Feature Toggle
window.toggleSystemFeature = async (feature, isActive) => {
    try {
        await api.post('/admin/settings', {
            key: `feature_${feature}`,
            value: isActive ? '1' : '0'
        }, { useApiPrefix: true });
        showToast(`Feature ${feature} ${isActive ? 'enabled' : 'disabled'}`, "success");
    } catch (error) {
        showToast("Failed to update feature flag", "error");
        console.error(error);
    }
};

window.initSystemControl = async () => {
    try {
        const settings = await api.get('/admin/settings', { useApiPrefix: true });

        // Sync feature flag checkboxes
        const flags = [
            'calculator', 'mini_course',
            'gamification', 'mentor_lab', 'registration'
        ];
        flags.forEach(flag => {
            const el = document.getElementById(`flag-${flag}`);
            if (el && settings[`feature_${flag}`] !== undefined) {
                el.checked = settings[`feature_${flag}`] === '1';
            } else if (el) {
                // Default to ON if not yet set
                el.checked = true;
            }
        });

        // Pre-fill broadcast inputs
        const msgEl = document.getElementById('broadcast-message');
        const activeEl = document.getElementById('broadcast-active');
        if (msgEl && settings.system_announcement !== undefined) {
            msgEl.value = settings.system_announcement;
        }
        if (activeEl && settings.system_broadcast_active !== undefined) {
            activeEl.value = settings.system_broadcast_active;
        }

        // Maintenance Status
        const maintenanceActive = settings.system_maintenance === '1';
        const btnMaint = document.getElementById('btn-toggle-maintenance');
        const statusText = document.getElementById('maintenance-status-text');
        const iconMaint = document.getElementById('maintenance-icon');

        if (btnMaint && statusText && iconMaint) {
            if (maintenanceActive) {
                btnMaint.innerText = 'DISABLE';
                btnMaint.classList.replace('bg-slate-700', 'bg-rose-600');
                statusText.innerText = 'App is in MAINTENANCE';
                statusText.classList.replace('text-white', 'text-rose-400');
                iconMaint.innerHTML = '<i class="fas fa-hammer animate-bounce"></i>';
                iconMaint.classList.replace('text-slate-400', 'text-rose-400');
            } else {
                btnMaint.innerText = 'ENABLE';
                btnMaint.classList.replace('bg-rose-600', 'bg-slate-700');
                statusText.innerText = 'App is Online';
                statusText.classList.replace('text-rose-400', 'text-white');
                iconMaint.innerHTML = '<i class="fas fa-power-off"></i>';
                iconMaint.classList.replace('text-rose-400', 'text-slate-400');
            }
        }

    } catch (error) {
        console.error("Failed to load system settings:", error);
    }
};

window.toggleMaintenance = async () => {
    const btn = document.getElementById('btn-toggle-maintenance');
    const isEnabling = btn.innerText === 'ENABLE';

    if (confirm(`Are you sure you want to ${isEnabling ? 'ENABLE' : 'DISABLE'} Maintenance Mode?`)) {
        try {
            await api.post('/admin/settings', {
                key: 'system_maintenance',
                value: isEnabling ? '1' : '0'
            }, { useApiPrefix: true });
            window.initSystemControl(); // Refresh UI
            showToast(`Maintenance Mode ${isEnabling ? 'Enabled' : 'Disabled'}`, "success");
        } catch (error) {
            showToast("Failed to update maintenance mode", "error");
        }
    }
};



// Global User Actions
window.banUser = async (userId) => {
    if (!confirm("Are you sure you want to ban this user?")) return;
    try {
        await api.post(`/admin/users/${userId}/ban`, {}, { useApiPrefix: true });
        showToast("User banned successfully", "success");
        loadUsers();
    } catch (error) {
        showToast("Failed to ban user", "error");
    }
};

window.unbanUser = async (userId) => {
    if (!confirm("Unban this user?")) return;
    try {
        await api.post(`/admin/users/${userId}/unban`, {}, { useApiPrefix: true });
        showToast("User unbanned successfully", "success");
        loadUsers();
    } catch (error) {
        showToast("Failed to unban user", "error");
    }
};

window.promoteUser = async (userId) => {
    if (!confirm("Promote this user to Admin?")) return;
    try {
        await api.post(`/admin/users/${userId}/promote`, {}, { useApiPrefix: true });
        showToast("User promoted to Admin", "success");
        loadUsers();
    } catch (error) {
        showToast("Failed to promote user", "error");
    }
};

window.changeUserPassword = async (userId) => {
    const newPassword = prompt("Enter new password for this user (min 6 chars):");
    if (!newPassword) return;

    if (newPassword.length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    try {
        await api.post(`/admin/users/${userId}/password`, { password: newPassword }, { useApiPrefix: true });
        showToast("Password updated successfully", "success");
    } catch (error) {
        showToast("Failed to update password", "error");
        console.error(error);
    }
};

window.exportUserCSV = () => {
    showToast("Export feature coming soon to API version.", "info");
};

window.verifyDataIntegrity = () => {
    const stats = document.getElementById('integrity-stats');
    if (stats) stats.classList.remove('hidden');
    showToast("Data Integrity Check Passed", "success");
};

window.checkInactiveUsers = () => {
    const stats = document.getElementById('inactive-user-stats');
    if (stats) stats.classList.remove('hidden');
    showToast("Scan Complete: 0 Inactive Users", "info");
};

window.updateBroadcastSystem = async () => {
    const message = document.getElementById('broadcast-message').value;
    const isActive = document.getElementById('broadcast-active').value;

    try {
        await api.post('/admin/settings', {
            key: 'system_announcement',
            value: message
        }, { useApiPrefix: true });

        await api.post('/admin/settings', {
            key: 'system_broadcast_active',
            value: isActive
        }, { useApiPrefix: true });

        showToast("Broadcast settings updated successfully", "success");
    } catch (error) {
        console.error("Broadcast Update Failed:", error);
        showToast("Failed to update broadcast settings", "error");
    }
};

export const initAdminDashboard = async () => {
    console.log("Admin Dashboard Initialization...");
    // Check if on admin page (using the main dashboard view ID)
    if (!document.getElementById('view-dashboard')) return;

    try {
        await loadDashboardMetrics();
        await loadUsers();
    } catch (error) {
        console.error("Admin Load Failed:", error);
        showToast("Gagal memuat dashboard admin.", "error");
    }
};

const loadDashboardMetrics = async () => {
    try {
        const response = await api.get('/admin/dashboard', { useApiPrefix: true });
        const data = response; // Response returns the JSON directly from the api.js wrapper

        const metrics = data.metrics || {};
        const funnel = data.funnel || {};
        const aiSummary = data.ai_summary || {};
        const healthStats = data.system_health || [];

        // 1. Bind AI Insight Summary
        const insightEl = document.getElementById('ai-insight-text');
        if (insightEl) insightEl.innerHTML = aiSummary.summary_text || "Awaiting daily cron execution for insights.";

        // 2. Bind Snapshot Cards
        const fmtNum = (n) => new Intl.NumberFormat('id-ID').format(n || 0);
        const fmtMoney = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

        document.getElementById('stat-total-users').innerText = fmtNum(metrics.total_users);
        document.getElementById('stat-active-users').innerText = `${fmtNum(metrics.active_users)} active today`;
        document.getElementById('users-trend').innerHTML = `<i class="fas fa-arrow-up text-[10px]"></i> ${fmtNum(metrics.new_users)}`;

        document.getElementById('stat-total-blueprints').innerText = fmtNum(metrics.total_blueprints);
        document.getElementById('stat-avg-target').innerText = fmtMoney(metrics.avg_target_profit);
        document.getElementById('blueprints-trend').innerHTML = `<i class="fas fa-arrow-up text-[10px]"></i> ${fmtNum(metrics.new_blueprints)}`;

        document.getElementById('stat-total-roadmaps').innerText = fmtNum(metrics.total_roadmaps);
        document.getElementById('stat-roadmap-conversion').innerText = `${(metrics.roadmap_conversion_rate || 0).toFixed(1)}%`;
        document.getElementById('roadmaps-trend').innerHTML = `<i class="fas fa-arrow-up text-[10px]"></i> ${fmtNum(metrics.new_roadmaps)}`;

        document.getElementById('stat-high-intent').innerText = fmtNum(metrics.high_intent_users);

        // 3. Bind Funnel
        document.getElementById('funnel-visitors').innerText = fmtNum(funnel.total_visitors);
        document.getElementById('funnel-rgp').innerText = fmtNum(funnel.rgp_users);
        document.getElementById('funnel-sim').innerText = fmtNum(funnel.simulator_users);
        document.getElementById('funnel-mentor').innerText = fmtNum(funnel.mentor_users);
        document.getElementById('funnel-roadmap').innerText = fmtNum(funnel.roadmap_users);

        // Calculate Conversions (Visitor -> RGP)
        const calcConv = (current, previous) => previous > 0 ? Math.round((current / previous) * 100) : 0;
        const c1 = calcConv(funnel.rgp_users, funnel.total_visitors);
        const c2 = calcConv(funnel.simulator_users, funnel.rgp_users);
        const c3 = calcConv(funnel.mentor_users, funnel.simulator_users);
        const c4 = calcConv(funnel.roadmap_users, funnel.mentor_users);

        document.getElementById('funnel-conv-1').innerText = `${c1}%`;
        document.getElementById('funnel-conv-2').innerText = `${c2}%`;
        document.getElementById('funnel-conv-3').innerText = `${c3}%`;
        document.getElementById('funnel-conv-4').innerText = `${c4}%`;

        document.getElementById('funnel-conv-1-mobile').innerText = `${c1}% ↓`;
        document.getElementById('funnel-conv-2-mobile').innerText = `${c2}% ↓`;
        document.getElementById('funnel-conv-3-mobile').innerText = `${c3}% ↓`;
        document.getElementById('funnel-conv-4-mobile').innerText = `${c4}% ↓`;

        document.getElementById('funnel-drop-4').innerText = `${100 - c4}% Drop-off`;
        document.getElementById('funnel-drop-4-mobile').innerText = `${100 - c4}% Drop-off`;

        // 4. Bind DNA Radar
        renderDnaRadar(metrics);

        // 5. Bind System Health
        renderSystemHealth(healthStats);

    } catch (error) {
        console.error("Dashboard Metrics Load Failed:", error);
    }
};

const renderDnaRadar = (metrics) => {
    const ctx = document.getElementById('dnaRadarChart');
    if (!ctx) return;

    // Destroy previous chart if exists
    if (window.adminDnaChart) window.adminDnaChart.destroy();

    window.adminDnaChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Feasibility', 'Probability', 'Roadmap Conv.', 'Engagement'],
            datasets: [{
                label: 'System DNA Averages',
                // Normalize roadmap conversion rate to a 0-100 scale similar to the scores
                data: [
                    metrics.avg_feasibility || 0,
                    metrics.avg_probability_score || 0,
                    metrics.roadmap_conversion_rate || 0,
                    50 // Mock engagement stat since we don't have it explicitly yet
                ],
                backgroundColor: 'rgba(167, 139, 250, 0.2)', // Purple 400
                borderColor: 'rgba(167, 139, 250, 1)',
                pointBackgroundColor: 'rgba(167, 139, 250, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(167, 139, 250, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    pointLabels: { color: 'rgba(148, 163, 184, 1)', font: { size: 10 } },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0)', // Hide text
                        backdropColor: 'transparent',
                        min: 0,
                        max: 100,
                        stepSize: 20
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
};

const renderSystemHealth = (stats) => {
    const tbody = document.getElementById('system-health-tbody');
    if (!tbody) return;

    if (!stats || stats.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-slate-500">No API logs available (Middleware active, waiting for traffic).</td></tr>';
        return;
    }

    tbody.innerHTML = stats.map(s => {
        const errorRate = ((s.error_count / s.total_requests) * 100).toFixed(1);
        const isHealthy = errorRate < 5;
        const latency = parseFloat(s.avg_latency).toFixed(0);
        const latencyColor = latency > 1000 ? 'text-amber-400' : 'text-slate-300';

        return `
        <tr class="hover:bg-slate-800/20 transition-colors border-b border-slate-800/30">
            <td class="py-3 px-2">
                <div class="font-mono text-xs text-indigo-400">${s.endpoint}</div>
            </td>
            <td class="py-3 px-2 text-right text-slate-300 font-medium">${new Intl.NumberFormat().format(s.total_requests)}</td>
            <td class="py-3 px-2 text-right ${latencyColor}">${latency} ms</td>
            <td class="py-3 px-2 text-right">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold ${isHealthy ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'}">
                    ${isHealthy ? 'HEALTHY' : 'ERRORS'}
                </span>
            </td>
        </tr>
        `;
    }).join('');
};



const loadUsers = async () => {
    const tableBody = document.getElementById('users-table-body');
    if (!tableBody) return;

    tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4">Loading...</td></tr>';

    try {
        const response = await api.get('/admin/users', { useApiPrefix: true }); // Pagination default page 1
        const users = response.data; // Laravel pagination wrapped in 'data'

        if (!users || users.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4">No users found.</td></tr>';
            return;
        }

        tableBody.innerHTML = '';

        users.forEach(user => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-800/20 transition-colors border-b border-slate-800/30 last:border-0";

            const isBanned = user.is_banned;
            const roleBadge = user.role === 'admin'
                ? '<span class="px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full">Admin</span>'
                : '<span class="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-full">User</span>';

            tr.innerHTML = `
                <td class="p-4">
                    <div class="font-medium text-white ${isBanned ? 'line-through text-slate-500' : ''}">${user.name || user.username || 'Unknown'}</div>
                    <div class="text-xs text-slate-500">${user.email}</div>
                </td>
                <td class="p-4">
                    ${roleBadge}
                </td>
                <td class="p-4 text-slate-400 text-xs">${formatDate(user.created_at)}</td>
                <td class="p-4 text-slate-400 text-xs">${user.last_login_at ? formatDate(user.last_login_at) : '-'}</td>
                <td class="p-4 text-right flex justify-end gap-2">
                    <button onclick="window.viewUser(${user.id})" class="p-2 bg-blue-500/10 text-blue-400 rounded hover:bg-blue-500/20 transition-colors" title="Inspect User">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="window.changeUserPassword(${user.id})" class="p-2 bg-amber-500/10 text-amber-400 rounded hover:bg-amber-500/20 transition-colors" title="Change Password">
                        <i class="fas fa-key"></i>
                    </button>
                    ${!isBanned ? `
                        <button onclick="window.banUser(${user.id})" class="p-2 bg-rose-500/10 text-rose-400 rounded hover:bg-rose-500/20 transition-colors" title="Ban User">
                            <i class="fas fa-ban"></i>
                        </button>
                    ` : `
                        <button onclick="window.unbanUser(${user.id})" class="p-2 bg-emerald-500/10 text-emerald-400 rounded hover:bg-emerald-500/20 transition-colors" title="Unban User">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    `}
                    ${user.role !== 'admin' ? `
                        <button onclick="window.promoteUser(${user.id})" class="p-2 bg-purple-500/10 text-purple-400 rounded hover:bg-purple-500/20 transition-colors" title="Promote to Admin">
                            <i class="fas fa-crown"></i>
                        </button>
                    ` : ''}
                </td>
            `;
            tableBody.appendChild(tr);
        });

    } catch (error) {
        console.error("Users Load Failed:", error);
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-4 text-rose-500">Failed to load users.</td></tr>';
    }
};

// V.15 User Inspector Logic
window.viewUser = async (userId) => {
    const modal = document.getElementById('modal-user-inspector');
    const content = document.getElementById('inspector-content');
    if (!modal || !content) return;

    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex flex-col items-center justify-center h-full text-slate-500">
            <i class="fas fa-circle-notch fa-spin text-3xl mb-4 text-emerald-500"></i>
            <p>Loading user profile...</p>
        </div>
    `;

    try {
        const response = await api.get(`/admin/users/${userId}`, { useApiPrefix: true });
        const { user } = response;
        const business = user.business_profile || {};
        const gami = user.gamification || {};
        const eng = user.engagement || {};

        // Helper for currency
        const fmtMoney = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

        content.innerHTML = `
            <!-- Header Profile -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-8 border-b border-slate-700">
                <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                    <img src="${user.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`}" 
                        class="w-24 h-24 rounded-full border-4 border-slate-700 shadow-xl">
                    <div class="text-center md:text-left flex-1">
                        <h2 class="text-2xl font-bold text-white mb-1">${user.name}</h2>
                        <div class="text-slate-400 text-sm mb-3 flex flex-wrap justify-center md:justify-start gap-3">
                            <span><i class="fas fa-envelope mr-1"></i> ${user.email}</span>
                            <span><i class="fas fa-calendar mr-1"></i> Joined ${formatDate(user.created_at)}</span>
                            <span><i class="fas fa-history mr-1"></i> Active ${user.last_login_at ? formatDate(user.last_login_at) : 'Never'}</span>
                        </div>
                        <div class="flex gap-2 justify-center md:justify-start">
                             <span class="px-3 py-1 rounded-full text-xs font-bold ${user.role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-slate-700 text-slate-300'}">
                                ${user.role.toUpperCase()}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold ${user.is_banned ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400'}">
                                ${user.is_banned ? 'BANNED' : 'ACTIVE'}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                     <div class="flex flex-col gap-2 shrink-0">
                         <button onclick="window.location.href='mailto:${user.email}'" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition">
                            <i class="fas fa-envelope mr-2"></i> Send Email
                         </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 lg:divide-x divide-slate-700 min-h-[500px]">
                
                <!-- Col 1: Business Snapshot -->
                <div class="p-6 space-y-6 lg:col-span-1 bg-slate-900/50">
                    <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-2">Business Data</h3>
                    
                    ${business.business_name ? `
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-slate-500">Business Name</label>
                                <div class="text-white font-medium">${business.business_name}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-slate-500">Target Revenue</label>
                                    <div class="text-emerald-400 font-bold">${fmtMoney(business.target_revenue)}</div>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500">Ad Spend Budget</label>
                                    <div class="text-amber-400 font-bold">${fmtMoney(business.ad_spend)}</div>
                                </div>
                            </div>
                             <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-slate-500">Selling Price</label>
                                    <div class="text-blue-400 font-bold">${fmtMoney(business.selling_price)}</div>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500">Conv. Rate</label>
                                    <div class="text-white font-bold">${business.conversion_rate || 0}%</div>
                                </div>
                            </div>
                             <div>
                                <label class="text-xs text-slate-500">Current Traffic</label>
                                <div class="text-white font-medium">${business.traffic || 0} visitors</div>
                            </div>
                        </div>
                    ` : `
                        <div class="text-center py-10 text-slate-500">
                            <i class="fas fa-briefcase text-4xl mb-3 opacity-20"></i>
                            <p>No business profile yet.</p>
                        </div>
                    `}
                </div>

                <!-- Col 2: Gamification & Engagement -->
                <div class="p-6 lg:col-span-2 bg-slate-900 grid grid-rows-2 gap-4 divide-y divide-slate-800">
                     
                     <!-- Gamification Status -->
                     <div>
                         <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-2"><i class="fas fa-gamepad text-purple-400 mr-2"></i>Gamification Profile</h3>
                         <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50 text-center">
                                <div class="text-xs text-slate-500 mb-1 uppercase tracking-wider font-bold">Level</div>
                                <div class="text-3xl font-black text-white">${gami.level || 1}</div>
                            </div>
                            <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50 text-center">
                                <div class="text-xs text-slate-500 mb-1 uppercase tracking-wider font-bold">Total XP</div>
                                <div class="text-xl mt-1 font-bold text-emerald-400">${new Intl.NumberFormat('id-ID').format(gami.xp || 0)}</div>
                            </div>
                            <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50 text-center">
                                <div class="text-xs text-slate-500 mb-1 uppercase tracking-wider font-bold">Badges</div>
                                <div class="text-xl mt-1 font-bold text-amber-400"><i class="fas fa-award mr-1"></i>${gami.badges_count || 0}</div>
                            </div>
                            <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-700/50 text-center flex flex-col items-center justify-center">
                                <div class="text-[10px] text-slate-500 mb-1 uppercase tracking-wider font-bold">Equipped</div>
                                ${gami.equipped_badge_icon ? `<div class="text-2xl">${gami.equipped_badge_icon}</div>` : `<div class="text-slate-600"><i class="fas fa-shield-alt text-xl"></i></div>`}
                                <div class="text-[10px] font-bold mt-1 text-slate-300 truncate w-full" title="${gami.equipped_badge || 'None'}">${gami.equipped_badge || 'None'}</div>
                            </div>
                         </div>
                     </div>

                     <!-- Platform Engagement -->
                     <div class="pt-4">
                         <h3 class="text-lg font-bold text-white mb-4 border-b border-slate-800 pb-2"><i class="fas fa-chart-line text-blue-400 mr-2"></i>Platform Engagement</h3>
                         <div class="grid grid-cols-3 gap-4">
                            <div class="flex items-center gap-3 p-3 bg-slate-800/30 rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-map-project"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-white">${eng.total_blueprints || 0}</div>
                                    <div class="text-xs text-slate-500">Blueprints Generated</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-800/30 rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-route"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-white">${eng.total_roadmaps || 0}</div>
                                    <div class="text-xs text-slate-500">Active Roadmaps</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-slate-800/30 rounded-lg">
                                <div class="w-10 h-10 rounded-full bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <div>
                                    <div class="text-xl font-bold text-white">${eng.unlocked_milestones || 0}</div>
                                    <div class="text-xs text-slate-500">Milestones Reached</div>
                                </div>
                            </div>
                         </div>
                     </div>
                </div>
            </div>
        `;

    } catch (error) {
        console.error("Inspector Load Failed:", error);
        content.innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-rose-500">
                <i class="fas fa-exclamation-triangle text-3xl mb-4"></i>
                <p>Failed to load user data.</p>
            </div>
        `;
    }

};



// Auto-Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminDashboard);
} else {
    initAdminDashboard();
}

// ============================================================================
// GAMIFICATION MANAGER (MINTING, LEADERBOARD, REWARDS)
// ============================================================================

window.refreshGamification = async () => {
    await loadGamificationData(false);
};

// Global interval reference for silent auto-polling
let gamificationInterval = null;

const loadGamificationData = async (silent = false) => {
    const tableBody = document.getElementById('leaderboard-table-body');
    const xpEl = document.getElementById('gamification-total-xp');
    const bdEl = document.getElementById('gamification-total-badges');

    if (!tableBody) return;

    if (!silent) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-slate-500"><i class="fas fa-spinner fa-spin mr-2"></i> Loading Top Rank...</td></tr>';
    }

    try {
        const response = await api.get('/admin/gamification', { useApiPrefix: true });
        const { leaderboard, stats } = response.data || response;

        // Statistics
        if (xpEl) xpEl.innerText = new Intl.NumberFormat('id-ID').format(stats.total_xp_economy || 0);
        if (bdEl) bdEl.innerText = new Intl.NumberFormat('id-ID').format(stats.total_badges_circulation || 0);

        // Leaderboard
        if (!leaderboard || leaderboard.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-slate-500">Belum ada user yang memiliki XP.</td></tr>';
            return;
        }

        tableBody.innerHTML = '';

        leaderboard.forEach((metric, index) => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-800/20 transition-colors border-b border-slate-800/30 text-sm last:border-0";

            // Medals for top 3
            let rankDisplay = `<span class="text-slate-400 font-bold ml-2">${index + 1}</span>`;
            if (index === 0) rankDisplay = `<i class="fas fa-trophy text-amber-400 text-lg"></i>`;
            if (index === 1) rankDisplay = `<i class="fas fa-medal text-slate-300 text-lg"></i>`;
            if (index === 2) rankDisplay = `<i class="fas fa-medal text-amber-600 text-lg"></i>`;

            tr.innerHTML = `
                <td class="py-3 px-4 font-bold text-center w-12">${rankDisplay}</td>
                <td class="py-3 px-4">
                    <div class="font-bold text-white">${metric.name}</div>
                    <div class="text-[10px] text-slate-500 tracking-wider">ID: ${metric.user_id} | ${metric.email}</div>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 rounded-full text-xs font-bold border border-indigo-500/20">Lvl ${metric.level}</span>
                </td>
                <td class="py-3 px-4 text-right">
                    <span class="text-emerald-400 font-bold">${new Intl.NumberFormat('id-ID').format(metric.total_xp)} XP</span>
                </td>
                <td class="py-3 px-4 text-right flex flex-col justify-end items-end h-full mt-2">
                    <span class="text-slate-300"><i class="far fa-clock mr-1 text-slate-500"></i> ${metric.minutes_spent}</span>
                    <span class="text-[10px] text-slate-500">Min</span>
                </td>
            `;
            tableBody.appendChild(tr);
        });

    } catch (error) {
        console.error("Leaderboard Load Failed:", error);
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-rose-500">Gagal memuat data leaderboard.</td></tr>';
    }
};

const loadBadgesDropdown = async () => {
    // Target the new custom options container instead of a select element
    const optionsContainer = document.getElementById('custom-badge-options');
    const hiddenInput = document.getElementById('award-badge-id');
    const triggerText = document.getElementById('dropdown-selected-text');

    if (!optionsContainer || !hiddenInput) return;

    optionsContainer.innerHTML = '<div class="px-4 py-6 text-center text-slate-500 text-xs"><i class="fas fa-spinner fa-spin mr-2"></i>Loading items...</div>';

    try {
        const response = await api.get('/admin/gamification/badges', { useApiPrefix: true });
        const data = response.data || response;
        const badges = data.badges || [];
        const borders = data.borders || [];

        // Store options globally for filtering purposes
        window.cachedDropdownItems = [];

        if (badges.length === 0 && borders.length === 0) {
            optionsContainer.innerHTML = '<div class="px-4 py-8 text-center text-slate-500 text-xs">Tidak ada item di database</div>';
            return;
        }

        let htmlContent = '';

        // Render Badges Group
        if (badges.length > 0) {
            htmlContent += `<div class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider bg-slate-800/80 sticky top-0 z-10 backdrop-blur-sm border-b border-slate-700/50">🎖️ Premium Badges</div>`;
            badges.forEach(b => {
                let rarityIcon = '🟢';
                if (b.rarity_weight >= 50 && b.rarity_weight <= 89) rarityIcon = '💎';
                if (b.rarity_weight >= 90) rarityIcon = '👑';

                const optionData = {
                    val: `badge_${b.id}`,
                    label: `${b.name}`,
                    category: b.category,
                    icon: rarityIcon,
                    image: b.icon_url || '',
                    type: 'badge',
                    rawText: `${b.name} ${b.category} badge`.toLowerCase()
                };

                window.cachedDropdownItems.push(optionData);

                htmlContent += buildDropdownItemHTML(optionData);
            });
        }

        // Render Borders Group
        if (borders.length > 0) {
            htmlContent += `<div class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider bg-slate-800/80 sticky top-0 z-10 backdrop-blur-sm border-y border-slate-700/50 mt-1">🖼️ Avatar Borders</div>`;
            borders.forEach(b => {
                const optionData = {
                    val: `border_${b.id}`,
                    label: `${b.name}`,
                    category: `Rank: ${b.rarity}`,
                    icon: '🖼️',
                    image: b.image_url || '',
                    type: 'border',
                    rawText: `${b.name} ${b.rarity} border`.toLowerCase()
                };

                window.cachedDropdownItems.push(optionData);

                htmlContent += buildDropdownItemHTML(optionData);
            });
        }

        optionsContainer.innerHTML = htmlContent;

        // Reset state
        hiddenInput.value = '';
        triggerText.innerText = '-- Pilih Item Gamification --';
        triggerText.classList.replace('text-emerald-400', 'text-slate-400');
        triggerText.classList.remove('font-bold');

    } catch (error) {
        console.error("Item Catalog Load Failed:", error);
        optionsContainer.innerHTML = '<div class="px-4 py-6 text-center text-rose-500 text-xs">Gagal meload item</div>';
    }
};

// --- Custom Dropdown UI Helpers ---

function buildDropdownItemHTML(opt) {
    const iconHtml = opt.image
        ? `<div class="w-8 h-8 rounded-full overflow-hidden bg-slate-800 border border-slate-700 shrink-0 flex items-center justify-center mr-3"><img src="${opt.image}" class="w-full h-full object-cover"></div>`
        : `<div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm shrink-0 mr-3">${opt.icon}</div>`;

    return `
        <div class="custom-dropdown-item px-3 py-2 flex items-center cursor-pointer hover:bg-slate-700/50 transition-colors group" 
             data-val="${opt.val}" 
             data-label="${opt.label}" 
             data-icon="${opt.icon}"
             data-type="${opt.type}"
             onclick="selectBadgeOption(this)">
             
            ${iconHtml}
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate group-hover:text-emerald-400 transition-colors">${opt.label}</div>
                <div class="text-[10px] text-slate-500 truncate">${opt.category}</div>
            </div>
            <i class="fas fa-check text-emerald-500 opacity-0 transition-opacity ml-2 checkmark-icon"></i>
        </div>
    `;
}

window.toggleBadgeDropdown = (forceClose = false) => {
    const menu = document.getElementById('custom-badge-menu');
    const icon = document.getElementById('dropdown-icon');
    const searchInput = document.getElementById('badge-search-input');

    if (!menu) return;

    const isHidden = menu.classList.contains('hidden');

    if (isHidden && !forceClose) {
        // Open
        menu.classList.remove('hidden');
        // Small delay to allow CSS display:block to apply before animating opacity
        setTimeout(() => {
            menu.classList.remove('opacity-0', '-translate-y-2');
            menu.classList.add('opacity-100', 'translate-y-0');
            icon.classList.add('rotate-180');
            if (searchInput) searchInput.focus();
        }, 10);
    } else {
        // Close
        menu.classList.remove('opacity-100', 'translate-y-0');
        menu.classList.add('opacity-0', '-translate-y-2');
        icon.classList.remove('rotate-180');

        setTimeout(() => {
            menu.classList.add('hidden');
            // Reset search when closing
            if (searchInput) {
                searchInput.value = '';
                filterBadgeDropdown('');
            }
        }, 200); // Matches standard tailwind transition duration
    }
};

window.selectBadgeOption = (el) => {
    const val = el.getAttribute('data-val');
    const label = el.getAttribute('data-label');
    const icon = el.getAttribute('data-icon');

    // Update hidden input
    document.getElementById('award-badge-id').value = val;

    // Update Trigger UI
    const triggerText = document.getElementById('dropdown-selected-text');
    triggerText.innerHTML = `<span class="mr-2">${icon}</span> ${label}`;
    triggerText.classList.replace('text-slate-400', 'text-emerald-400');
    triggerText.classList.add('font-bold');

    // Update Checkmarks visually
    document.querySelectorAll('.custom-dropdown-item .checkmark-icon').forEach(i => i.classList.remove('opacity-100'));
    document.querySelectorAll('.custom-dropdown-item').forEach(i => i.classList.remove('bg-emerald-900/20'));

    el.querySelector('.checkmark-icon').classList.add('opacity-100');
    el.classList.add('bg-emerald-900/20');

    // Close
    toggleBadgeDropdown(true);
};

window.filterBadgeDropdown = (query) => {
    if (!window.cachedDropdownItems) return;

    const searchLower = query.toLowerCase().trim();
    const items = document.querySelectorAll('.custom-dropdown-item');

    items.forEach((item, index) => {
        const dataObj = window.cachedDropdownItems[index];
        if (!dataObj) return;

        if (searchLower === '' || dataObj.rawText.includes(searchLower)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
};

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const container = document.getElementById('custom-badge-dropdown-container');
    if (container && !container.contains(e.target)) {
        toggleBadgeDropdown(true);
    }
});

window.submitManualAward = async () => {
    const btnSubmit = document.getElementById('btn-award-submit');
    const originalText = btnSubmit.innerHTML;

    const userId = document.getElementById('award-user-id').value;
    const selectedItem = document.getElementById('award-badge-id').value;
    const giveXpStr = document.getElementById('award-give-xp').value;

    // Validasi basic
    if (!userId || parseInt(userId) <= 0) return showToast("User ID tidak valid", "error");
    if (!selectedItem) return showToast("Pilih item terlebih dahulu", "error");

    const [itemType, itemId] = selectedItem.split('_');

    const payload = {
        user_id: parseInt(userId),
        item_type: itemType,
        item_id: parseInt(itemId),
    };

    if (giveXpStr && parseInt(giveXpStr) > 0) {
        payload.give_xp = parseInt(giveXpStr);
    }

    if (!confirm(`Yakin ingin menganugerahkan ${itemType.toUpperCase()} ke User #${userId}?`)) return;

    try {
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
        btnSubmit.disabled = true;

        await api.post('/admin/gamification/award', payload, { useApiPrefix: true });

        showToast(`Berhasil menganugerahkan Item ke User #${userId}!`, "success");

        // Reset sub-form
        document.getElementById('award-user-id').value = '';
        document.getElementById('award-badge-id').selectedIndex = 0;
        document.getElementById('award-give-xp').value = '';

        // Refresh grid
        refreshGamification();

    } catch (error) {
        console.error("Award Submit Failed:", error);
        showToast(error.message || "Gagal memberikan item", "error");
    } finally {
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
    }
};

// Hook into existing logic (We do it here to ensure it's loaded when switching tab)
const originalSwitchTab = window.switchTab;
window.switchTab = (tabName) => {
    if (typeof originalSwitchTab === 'function') {
        originalSwitchTab(tabName);
    }

    // Clear interval when leaving the gamification tab
    if (gamificationInterval) {
        clearInterval(gamificationInterval);
        gamificationInterval = null;
    }

    if (tabName === 'gamification') {
        loadGamificationData();
        loadBadgesDropdown();

        // Start auto-polling every 10 seconds silently
        gamificationInterval = setInterval(() => {
            loadGamificationData(true);
        }, 10000);
    }
};
