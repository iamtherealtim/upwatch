<?= view('admin/partials/header', ['title' => 'Dashboard - UpWatch Admin', 'includeChartJs' => true]) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'dashboard']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            <?= session('success') ?>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-gray-400 text-sm font-medium">Components</div>
                <div class="text-3xl font-bold text-white mt-2"><?= $stats['total_components'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-gray-400 text-sm font-medium">Monitors</div>
                <div class="text-3xl font-bold text-white mt-2"><?= $stats['total_monitors'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-gray-400 text-sm font-medium">Active Incidents</div>
                <div class="text-3xl font-bold text-red-400 mt-2"><?= $stats['active_incidents'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-gray-400 text-sm font-medium">Subscribers</div>
                <div class="text-3xl font-bold text-white mt-2"><?= $stats['total_subscribers'] ?></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Components -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-white">Components</h2>
                    <a href="/admin/components/create" class="text-indigo-400 hover:text-indigo-300">+ Add New</a>
                </div>
                <?php if (!empty($components)): ?>
                    <?php foreach ($components as $component): ?>
                    <div class="flex justify-between items-center py-3 border-b border-white/10 last:border-b-0">
                        <span class="font-medium text-gray-200"><?= esc($component['name']) ?></span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            <?= $component['status'] === 'operational' ? 'bg-green-900/50 text-green-300 border border-green-700' : 'bg-red-900/50 text-red-300 border border-red-700' ?>">
                            <?= ucfirst($component['status']) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400">No components yet. <a href="/admin/components/create" class="text-indigo-400 hover:text-indigo-300">Create one</a></p>
                <?php endif; ?>
            </div>

            <!-- Recent Incidents -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-white">Recent Incidents</h2>
                    <a href="/admin/incidents/create" class="text-indigo-400 hover:text-indigo-300">+ Create</a>
                </div>
                <?php if (!empty($recentIncidents)): ?>
                    <?php foreach ($recentIncidents as $incident): ?>
                    <div class="py-3 border-b border-white/10 last:border-b-0">
                        <div class="font-medium text-gray-200"><?= esc($incident['title']) ?></div>
                        <div class="text-sm text-gray-400 mt-1">
                            <?= ucfirst($incident['status']) ?> • 
                            <?= date('M j, g:i A', strtotime($incident['created_at'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400">No incidents. Everything is running smoothly! ✓</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-indigo-900/20 border border-indigo-700/50 rounded-lg p-6">
            <h3 class="font-semibold text-indigo-300 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="/admin/components/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition">
                    Add Component
                </a>
                <a href="/admin/monitors/create" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition">
                    Create Monitor
                </a>
                <a href="/admin/incidents/create" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-500 transition">
                    Report Incident
                </a>
                <a href="/" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>Status Page</span>
                </a>
            </div>
        </div>
    </main>

<?= view('admin/partials/footer') ?>
