<?= view('admin/partials/header', ['title' => 'results - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'monitors']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/monitors" class="text-indigo-400 hover:text-indigo-300">← Back to Monitors</a>
        </div>

        <!-- Monitor Info -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-white mb-2"><?= esc($monitor['name']) ?></h1>
                    <div class="text-gray-400">
                        <div class="mb-1">
                            <span class="font-medium">Target:</span> <?= esc($monitor['target']) ?>
                        </div>
                        <div class="mb-1">
                            <span class="font-medium">Type:</span> <?= strtoupper($monitor['type']) ?>
                        </div>
                        <div>
                            <span class="font-medium">Interval:</span> <?= round($monitor['interval'] / 60) ?> minutes
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                    $statusColor = match($monitor['last_status']) {
                        'up' => 'bg-green-900/50 text-green-300 border border-green-700',
                        'down' => 'bg-red-900/50 text-red-300 border border-red-700',
                        default => 'bg-gray-900 text-gray-200'
                    };
                    ?>
                    <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?= $statusColor ?>">
                        <?= ucfirst($monitor['last_status']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 font-medium mb-1">Avg Response (24h)</div>
                <div class="text-3xl font-bold text-white">
                    <?= $stats['avg_response_24h'] ? $stats['avg_response_24h'] . 'ms' : 'N/A' ?>
                </div>
            </div>

            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 font-medium mb-1">Uptime (30 days)</div>
                <div class="text-3xl font-bold text-green-400">
                    <?= $stats['uptime_30d'] ?>%
                </div>
            </div>

            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 font-medium mb-1">Uptime (90 days)</div>
                <div class="text-3xl font-bold text-green-400">
                    <?= $stats['uptime_90d'] ?>%
                </div>
            </div>
        </div>

        <!-- Response Time Chart -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-4">Response Time (Last 7 Days)</h2>
            <canvas id="responseTimeChart" height="80"></canvas>
        </div>

        <!-- Recent Checks -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h2 class="text-xl font-bold text-white mb-4">Recent Checks</h2>

            <?php if (!empty($results)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Response Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Error</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800/50 divide-y divide-white/10">
                        <?php foreach (array_slice($results, 0, 50) as $result): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                <?= date('M j, g:i A', strtotime($result['checked_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $color = $result['status'] === 'up' ? 'text-green-400' : 'text-red-400';
                                $icon = $result['status'] === 'up' ? '✓' : '✗';
                                ?>
                                <span class="<?= $color ?> font-semibold"><?= $icon ?> <?= ucfirst($result['status']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                <?= $result['response_time'] ? $result['response_time'] . ' ms' : '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                <?= $result['status_code'] ?? '-' ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 max-w-md truncate">
                                <?= $result['error_message'] ? esc($result['error_message']) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-gray-400">No check results yet.</p>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Prepare chart data
        const results = <?= json_encode($results) ?>;
        const labels = results.map(r => new Date(r.checked_at).toLocaleTimeString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        }));
        const data = results.map(r => r.response_time || 0);

        // Create chart
        const ctx = document.getElementById('responseTimeChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Response Time (ms)',
                    data: data,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Response Time (ms)'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    </script>
<?= view('admin/partials/footer') ?>