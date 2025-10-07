<?= view('admin/partials/header', ['title' => 'Monitors - UpWatch Admin']) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'monitors']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Monitors</h1>
            <a href="/admin/monitors/create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition">
                + Add Monitor
            </a>
        </div>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            ✓ <?= session('success') ?>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 overflow-hidden">
            <?php if (!empty($monitors)): ?>
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Monitor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Component</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Last Check</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Interval</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    <?php foreach ($monitors as $monitor): ?>
                    <tr class="hover:bg-gray-800/50/5">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-200"><?= esc($monitor['name']) ?></div>
                            <div class="text-sm text-gray-400"><?= esc($monitor['target']) ?></div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            <?= esc($monitor['component_name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-700 text-gray-300 border border-gray-600">
                                <?= strtoupper($monitor['type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColor = match($monitor['last_status']) {
                                'up' => 'bg-green-900/50 text-green-300 border border-green-700',
                                'down' => 'bg-red-900/50 text-red-300 border border-red-700',
                                default => 'bg-gray-800 text-gray-300 border border-gray-700'
                            };
                            ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColor ?>">
                                <?= ucfirst($monitor['last_status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            <?= $monitor['last_check_at'] ? date('M j, g:i A', strtotime($monitor['last_check_at'])) : 'Never' ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">
                            <?= round($monitor['interval'] / 60) ?> min
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end gap-2">
                                <button hx-post="/admin/monitors/test/<?= $monitor['id'] ?>"
                                        hx-swap="none"
                                        hx-headers='{"X-CSRF-TOKEN": "<?= csrf_hash() ?>"}'
                                        hx-on::after-request="handleTestResponse(event)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-900/30 text-green-400 rounded-lg hover:bg-green-900/50 transition border border-green-700/50">
                                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                                    <span>Test</span>
                                </button>
                                <a href="/admin/monitors/results/<?= $monitor['id'] ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-900/30 text-purple-400 rounded-lg hover:bg-purple-900/50 transition border border-purple-700/50">
                                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5"></i>
                                    <span>Results</span>
                                </a>
                                <a href="/admin/monitors/edit/<?= $monitor['id'] ?>" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-900/30 text-indigo-400 rounded-lg hover:bg-indigo-900/50 transition border border-indigo-700/50">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="/admin/monitors/delete/<?= $monitor['id'] ?>" 
                                   onclick="return confirm('Delete this monitor?')"
                                   title="Delete"
                                   class="inline-flex items-center justify-center w-8 h-8 bg-red-900/30 text-red-400 rounded-lg hover:bg-red-900/50 transition border border-red-700/50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-8 text-center text-gray-400">
                <p class="mb-4">No monitors configured yet.</p>
                <a href="/admin/monitors/create" class="text-indigo-400 hover:text-indigo-300">Create your first monitor →</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-900/50 border-green-700 text-green-300' : 'bg-red-900/50 border-red-700 text-red-300';
            
            toast.className = `${bgColor} border px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] animate-slide-in`;
            toast.innerHTML = `
                <i data-lucide="${type === 'success' ? 'check-circle' : 'x-circle'}" class="w-5 h-5"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="hover:opacity-70">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            `;
            
            container.appendChild(toast);
            lucide.createIcons();
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        function handleTestResponse(event) {
            const response = JSON.parse(event.detail.xhr.responseText);
            
            if (response.success && response.result) {
                const result = response.result;
                if (result.status === 'up') {
                    showToast(`✓ Monitor test successful! Response time: ${result.response_time}ms`, 'success');
                } else {
                    showToast(`✗ Monitor test failed: ${result.error_message || 'Unknown error'}`, 'error');
                }
            } else {
                showToast('Monitor test failed', 'error');
            }
        }
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>

<?= view('admin/partials/footer') ?>
