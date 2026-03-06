<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strategic Intelligence - CuanCapital Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-100 h-screen w-full flex overflow-hidden">

    <!-- Sidebar (Simplified Replica) -->
    <aside class="w-64 h-full glass-panel border-r border-slate-800 flex flex-col z-30 hidden md:flex">
        <div class="p-6 border-b border-slate-800/50 flex items-center h-16 shrink-0">
            <h1 class="text-xl font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">
                <i class="fas fa-shield-alt mr-2"></i>Admin
            </h1>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin') }}" class="flex items-center space-x-3 px-4 py-3 text-slate-400 hover:bg-slate-800/50 hover:text-white rounded-xl transition-all w-full">
                <i class="fas fa-chart-line w-5"></i>
                <span>Back to Operations</span>
            </a>
            
            <div class="px-3 pt-4 pb-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Intelligence</span>
            </div>
            
            <button class="flex items-center space-x-3 px-4 py-3 bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 rounded-xl transition-all w-full">
                <i class="fas fa-brain w-5"></i>
                <span>Strategic Intel</span>
            </button>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-full min-w-0 bg-slate-900/50 relative overflow-hidden">
        <!-- Header -->
        <header class="h-16 glass-panel border-b border-slate-800/50 flex items-center justify-between px-8 shrink-0">
            <h2 class="text-lg font-medium text-slate-200">Strategic Intelligence Dashboard</h2>
            <div class="flex items-center gap-4">
                <div class="text-xs text-slate-500">Live Data</div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8">
            
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Analyses -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl"></div>
                    <div class="text-slate-400 text-sm mb-1">Total Analyses</div>
                    <div class="text-3xl font-bold text-white mb-2">{{ number_format($data['kpi']['total_analyses']) }}</div>
                    <div class="text-xs text-blue-400"><i class="fas fa-database mr-1"></i> Data Points</div>
                </div>

                <!-- Unrealistic % -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl"></div>
                    <div class="text-slate-400 text-sm mb-1">Unrealistic Rate</div>
                    <div class="text-3xl font-bold text-white mb-2">{{ $data['kpi']['unrealistic_percentage'] }}%</div>
                    <div class="text-xs text-rose-400"><i class="fas fa-exclamation-triangle mr-1"></i> Market Reality Check</div>
                </div>

                <!-- Avg Risk Score -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl"></div>
                    <div class="text-slate-400 text-sm mb-1">Avg Risk Appetite</div>
                    <div class="text-3xl font-bold text-white mb-2">{{ $data['kpi']['avg_risk_score'] }}</div>
                    <div class="text-xs text-amber-400"><i class="fas fa-fire mr-1"></i> (0-100 Scale)</div>
                </div>

                <!-- Dominant Strategy -->
                <div class="glass-panel rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
                    <div class="text-slate-400 text-sm mb-1">Dominant Strategy</div>
                    <div class="text-xl font-bold text-white mb-2 truncate" title="{{ $data['kpi']['most_common_strategy'] }}">
                        {{ Str::limit($data['kpi']['most_common_strategy'], 15) }}
                    </div>
                    <div class="text-xs text-emerald-400"><i class="fas fa-crown mr-1"></i> Most Popular</div>
                </div>
            </div>

            <!-- Charts Row 1: Distribution & Radar -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <!-- Strategy Distribution (Pie) -->
                <div class="glass-panel rounded-2xl p-6 lg:col-span-1">
                    <h3 class="text-lg font-medium text-white mb-4">Strategy Distribution</h3>
                    <div class="relative h-64">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>

                <!-- Trend Over Time (Line) -->
                <div class="glass-panel rounded-2xl p-6 lg:col-span-2">
                    <h3 class="text-lg font-medium text-white mb-4">Adoption Trend (Daily)</h3>
                    <div class="relative h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2: Radar & Insights -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-12">
                <!-- Average Scores (Radar) -->
                <div class="glass-panel rounded-2xl p-6">
                    <h3 class="text-lg font-medium text-white mb-4">Market Competency Profile</h3>
                    <div class="relative h-72 flex justify-center">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                <!-- Contextual Hint -->
                <div class="glass-panel rounded-2xl p-6 flex flex-col justify-center">
                    <h3 class="text-lg font-medium text-white mb-4">Strategic Insights</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-slate-800/50 rounded-xl border-l-4 border-emerald-500">
                            <h4 class="text-emerald-400 font-bold text-sm">Feasibility vs Risk</h4>
                            <p class="text-slate-400 text-sm mt-1">
                                High Feasibility + High Risk indicates experienced founders taking calculated bets. 
                                Low Feasibility + High Risk indicates dangerous gambling behavior (Unrealistic).
                            </p>
                        </div>
                        <div class="p-4 bg-slate-800/50 rounded-xl border-l-4 border-blue-500">
                            <h4 class="text-blue-400 font-bold text-sm">Efficiency Score</h4>
                            <p class="text-slate-400 text-sm mt-1">
                                Monitors how well capital is being utilized. A low average suggests users rely too heavily on brute-force spending rather than organic optimization.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Chart Configuration -->
    <script>
        // Data Injection from Controller
        const distributionData = @json($data['distribution']);
        const trendData = @json($data['trend']);
        const averageData = @json($data['averages']);

        // 1. Distribution Chart
        new Chart(document.getElementById('distributionChart'), {
            type: 'doughnut',
            data: {
                labels: distributionData.map(d => d.label),
                datasets: [{
                    data: distributionData.map(d => d.count),
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8' } }
                }
            }
        });

        // 2. Trend Chart
        // Process trend data into datasets
        const dates = Object.keys(trendData);
        // We need to extract all unique labels for datasets
        const allLabels = [...new Set(Object.values(trendData).flat().map(i => i.strategy_label))];
        
        const datasets = allLabels.map((label, index) => {
            const colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'];
            return {
                label: label,
                data: dates.map(date => {
                    const found = trendData[date].find(i => i.strategy_label === label);
                    return found ? found.total : 0;
                }),
                borderColor: colors[index % colors.length],
                tension: 0.4,
                fill: false
            };
        });

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: '#1e293b' }, ticks: { color: '#64748b' } },
                    x: { grid: { display: false }, ticks: { color: '#64748b' } }
                },
                plugins: {
                    legend: { labels: { color: '#94a3b8' } }
                }
            }
        });

        // 3. Radar Chart
        new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: {
                labels: ['Feasibility', 'Risk', 'Profit', 'Efficiency'],
                datasets: [{
                    label: 'Market Average',
                    data: [
                        averageData.feasibility,
                        averageData.risk,
                        averageData.profit,
                        averageData.efficiency
                    ],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: '#334155' },
                        grid: { color: '#334155' },
                        pointLabels: { color: '#cbd5e1', font: { size: 12 } },
                        ticks: { display: false, backdropColor: 'transparent' },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>
