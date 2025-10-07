<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($statusPage['name'] ?? 'UpWatch Status') ?></title>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .status-operational { color: #34d399; }
        .status-degraded { color: #fbbf24; }
        .status-partial_outage { color: #fb923c; }
        .status-major_outage { color: #f87171; }
        .status-maintenance { color: #818cf8; }
        
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gray: {
                            950: '#0a0a0a',
                        }
                    },
                    animation: {
                        'wiggle': 'wiggle 0.5s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 min-h-screen text-gray-100">
    <!-- Header -->
    <header class="bg-gray-800/50 border-b border-white/10">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center">
                        <i data-lucide="chart-no-axes-combined" class="w-7 h-7 text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white">UpWatch</h1>
                </div>
                <button 
                    @click="$dispatch('open-subscribe')"
                    class="group flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition"
                >
                    <i data-lucide="bell" class="w-4 h-4 group-hover:animate-[wiggle_0.5s_ease-in-out_infinite]"></i>
                    <span>Subscribe to Updates</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <!-- Flash Messages -->
        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
            <span><?= session('success') ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-300 hover:text-green-100">×</button>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
        <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
            <span><?= session('error') ?></span>
            <button onclick="this.parentElement.remove()" class="text-red-300 hover:text-red-100">×</button>
        </div>
        <?php endif; ?>

        <?php if (session()->has('info')): ?>
        <div class="bg-blue-900/50 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg mb-6 flex justify-between items-center">
            <span><?= session('info') ?></span>
            <button onclick="this.parentElement.remove()" class="text-blue-300 hover:text-blue-100">×</button>
        </div>
        <?php endif; ?>

        <!-- Overall Status -->
        <div class="bg-gray-800/50 border border-white/10 rounded-lg p-8 mb-6 text-center">
            <?php 
            $statusText = match($overallStatus ?? 'operational') {
                'operational' => 'All Systems Operational',
                'degraded' => 'Degraded Performance',
                'partial_outage' => 'Partial Outage',
                'major_outage' => 'Major Outage',
                'maintenance' => 'Under Maintenance',
                default => 'Unknown Status'
            };
            ?>
            <h2 class="text-4xl font-bold status-<?= $overallStatus ?> mb-2">
                <?= $statusText ?>
            </h2>
            <p class="text-gray-400">
                Last updated: <?= date('F j, Y, g:i a') ?>
            </p>
        </div>

        <!-- Active Incidents -->
        <?php if (!empty($activeIncidents)): ?>
        <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-yellow-300 mb-4">⚠️ Active Incidents</h3>
            <?php foreach ($activeIncidents as $incident): ?>
            <div class="bg-gray-800/50 border border-white/10 rounded p-4 mb-3 last:mb-0">
                <h4 class="font-semibold text-white"><?= esc($incident['title']) ?></h4>
                <p class="text-sm text-gray-400 mt-1">
                    Status: <span class="font-medium"><?= ucfirst($incident['status']) ?></span> • 
                    Impact: <span class="font-medium"><?= ucfirst($incident['impact']) ?></span>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Scheduled Maintenance -->
        <?php if (!empty($scheduledMaintenance)): ?>
        <div class="bg-blue-900/20 border border-blue-700/50 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-blue-300 mb-4">📅 Scheduled Maintenance</h3>
            <?php foreach ($scheduledMaintenance as $maintenance): ?>
            <div class="bg-gray-800/50 border border-white/10 rounded p-4 mb-3 last:mb-0">
                <h4 class="font-semibold text-white"><?= esc($maintenance['title']) ?></h4>
                <p class="text-sm text-gray-400 mt-1">
                    <?= date('M j, Y g:i A', strtotime($maintenance['scheduled_start'])) ?> - 
                    <?= date('M j, Y g:i A', strtotime($maintenance['scheduled_end'])) ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Components Status -->
        <div class="bg-gray-800/50 border border-white/10 rounded-lg  p-6 mb-6" x-data="{ uptimePeriod: 90 }">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-xl font-semibold">Services</h3>
                    <p class="text-sm text-gray-400 mt-1">
                        <span x-show="uptimePeriod === 6">Each bar represents 6 minutes</span>
                        <span x-show="uptimePeriod === 12">Each bar represents 12 minutes</span>
                        <span x-show="uptimePeriod === 24">Each bar represents 24 minutes</span>
                        <span x-show="uptimePeriod === 2">Each bar represents 48 minutes</span>
                        <span x-show="uptimePeriod === 5">Each bar represents 2 hours</span>
                        <span x-show="uptimePeriod === 7">Each bar represents ~3 hours</span>
                        <span x-show="uptimePeriod === 30">Each bar represents 12 hours</span>
                        <span x-show="uptimePeriod === 90">Each bar represents ~1.5 days</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="uptimePeriod = 6; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 6})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 6 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        6h
                    </button>
                    <button @click="uptimePeriod = 12; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 12})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 12 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        12h
                    </button>
                    <button @click="uptimePeriod = 24; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 24})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 24 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        24h
                    </button>
                    <button @click="uptimePeriod = 2; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 2})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 2 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        2d
                    </button>
                    <button @click="uptimePeriod = 5; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 5})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 5 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        5d
                    </button>
                    <button @click="uptimePeriod = 7; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 7})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 7 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        7d
                    </button>
                    <button @click="uptimePeriod = 30; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 30})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 30 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        30d
                    </button>
                    <button @click="uptimePeriod = 90; $refs.componentContainer.setAttribute('hx-vals', JSON.stringify({period: 90})); htmx.trigger($refs.componentContainer, 'loadWithPeriod')" 
                            :class="uptimePeriod === 90 ? 'bg-indigo-600 text-white' : 'bg-gray-800/50 text-gray-400 hover:text-gray-200 border border-white/10'"
                            class="w-12 px-3 py-2 rounded-lg text-sm font-medium transition">
                        90d
                    </button>
                </div>
            </div>
            
            <div x-ref="componentContainer"
                 hx-get="/api/status/live" 
                 hx-trigger="every 30s, loadWithPeriod"
                 hx-swap="innerHTML"
                 hx-vals='{"period": 90}'>
            
            <?php if (!empty($components)): ?>
                <?php foreach ($components as $groupName => $groupComponents): ?>
                <div class="mb-6 last:mb-0">
                    <h4 class="text-sm font-semibold text-gray-400 uppercase mb-3"><?= esc($groupName) ?></h4>
                    
                    <?php foreach ($groupComponents as $component): ?>
                    <div class="py-4 border-b last:border-b-0">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h5 class="font-medium text-white"><?= esc($component['name']) ?></h5>
                                <?php if ($component['description']): ?>
                                <p class="text-sm text-gray-400"><?= esc($component['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-4">
                                <?php if (isset($component['uptime_percentage'])): ?>
                                <span class="text-sm font-semibold text-gray-300">
                                    <?= number_format($component['uptime_percentage'], 2) ?>% uptime
                                </span>
                                <?php endif; ?>
                                <?php
                                $statusLabel = match($component['status']) {
                                    'operational' => 'Operational',
                                    'degraded' => 'Degraded',
                                    'partial_outage' => 'Partial Outage',
                                    'major_outage' => 'Major Outage',
                                    'maintenance' => 'Maintenance',
                                    default => 'Unknown'
                                };
                                $statusColor = match($component['status']) {
                                    'operational' => 'bg-green-900/50 text-green-300 border-2 border-green-700',
                                    'degraded' => 'bg-yellow-900/50 text-yellow-300 border-2 border-yellow-700',
                                    'partial_outage' => 'bg-orange-900/50 text-orange-300 border-2 border-orange-700',
                                    'major_outage' => 'bg-red-900/50 text-red-300 border-2 border-red-700',
                                    'maintenance' => 'bg-blue-900/50 text-blue-300 border-2 border-blue-700',
                                    default => 'bg-gray-800 text-gray-300 border-2 border-gray-700'
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusColor ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Uptime Visualization -->
                        <?php if (isset($component['uptime_data']) && !empty($component['uptime_data'])): ?>
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs text-gray-400" x-text="[6,12,24].includes(uptimePeriod) ? uptimePeriod + ' hours ago' : uptimePeriod + ' days ago'"></span>
                                <span class="text-xs text-gray-400">Now</span>
                            </div>
                            <div class="grid gap-[2px]" style="grid-template-columns: repeat(60, 1fr);" x-bind:title="(uptimePeriod === 6 ? '6 hour' : uptimePeriod + '-day') + ' uptime history (60 bars)'">
                                <?php foreach ($component['uptime_data'] as $day): ?>
                                <div class="h-10 rounded-sm <?= $day['status_color'] ?> hover:opacity-80 transition-opacity cursor-pointer" 
                                     title="<?= esc($day['date']) ?>: <?= $day['uptime'] ?>% uptime"></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 bg-green-600 rounded-sm"></div>
                                    <span>Operational (99.5%+)</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 bg-yellow-600 rounded-sm"></div>
                                    <span>Degraded (95-99%)</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 bg-orange-600 rounded-sm"></div>
                                    <span>Partial Outage (80-95%)</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 bg-red-600 rounded-sm"></div>
                                    <span>Major Outage (&lt;80%)</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                                    <span>No data</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-400">No components configured yet.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Incidents -->
        <?php if (!empty($recentIncidents)): ?>
        <div class="bg-gray-800/50 border border-white/10 rounded-lg  p-6">
            <h3 class="text-xl font-semibold mb-4">Recent Incidents</h3>
            <div class="space-y-4">
                <?php foreach ($recentIncidents as $incident): ?>
                <div class="border-l-4 border-white/20 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-white"><?= esc($incident['title']) ?></h4>
                            <p class="text-sm text-gray-400 mt-1">
                                <?= ucfirst($incident['status']) ?> • 
                                <?= date('M j, Y g:i A', strtotime($incident['created_at'])) ?>
                            </p>
                        </div>
                        <span class="text-sm font-medium text-gray-400"><?= ucfirst($incident['impact']) ?> Impact</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800/50 border border-white/10 border-t mt-12">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-400">
                    Powered by <strong class="text-indigo-400">UpWatch</strong> • Open-source status page monitoring
                </div>
                <a href="/admin" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-900/50 border border-white/5 hover:border-indigo-500/30 hover:bg-gray-900 transition-all">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-gray-500 group-hover:text-indigo-400 transition-colors"></i>
                    <span class="text-xs text-gray-500 group-hover:text-indigo-400 transition-colors">Admin</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- Subscribe Modal (Alpine.js) -->
    <div x-data="{ open: false }" 
         @open-subscribe.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/70" @click="open = false"></div>
            
            <div class="bg-gray-800 border border-white/10 rounded-lg shadow-xl p-6 max-w-md w-full relative z-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-white">Subscribe to Updates</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <p class="text-gray-400 mb-4">
                    Get notified when we create, update or resolve incidents.
                </p>
                
                <form action="/subscribe" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input type="email" 
                               name="email" 
                               placeholder="your@email.com"
                               class="w-full px-4 py-2 bg-gray-900 border border-white/10 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               required>
                        <p class="text-xs text-gray-400 mt-1">We'll send you a verification email</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" 
                                class="flex-1 bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                            Subscribe
                        </button>
                        <button type="button" 
                                @click="open = false" 
                                class="px-4 py-3 border border-white/10 rounded-lg hover:bg-white/5 transition text-gray-300">
                            Cancel
                        </button>
                    </div>
                </form>

                <div class="mt-4 pt-4 border-t text-xs text-gray-400">
                    <p>✓ No spam, only important updates</p>
                    <p>✓ Unsubscribe anytime</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
        
        // Re-initialize icons after HTMX swaps
        document.body.addEventListener('htmx:afterSwap', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
