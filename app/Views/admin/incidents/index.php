<?= view('admin/partials/header', ['title' => 'Incidents - UpWatch Admin']) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'incidents']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Incidents</h1>
            <a href="/admin/incidents/create" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-500 transition">
                + Report Incident
            </a>
        </div>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            ✓ <?= session('success') ?>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 rounded-lg border border-white/10">
            <?php if (!empty($incidents)): ?>
            <div class="divide-y divide-white/10">
                <?php foreach ($incidents as $incident): ?>
                <div class="p-6 hover:bg-gray-800/50/5 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-white">
                                    <?= esc($incident['title']) ?>
                                </h3>
                                <?php
                                $statusColors = [
                                    'investigating' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-700',
                                    'identified' => 'bg-orange-900/50 text-orange-300 border border-orange-700',
                                    'monitoring' => 'bg-blue-900/50 text-blue-300 border border-blue-700',
                                    'resolved' => 'bg-green-900/50 text-green-300 border border-green-700',
                                    'scheduled' => 'bg-purple-900/50 text-purple-300 border border-purple-700',
                                ];
                                $statusColor = $statusColors[$incident['status']] ?? 'bg-gray-800 text-gray-300 border border-gray-700';
                                ?>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $statusColor ?>">
                                    <?= ucfirst($incident['status']) ?>
                                </span>

                                <?php
                                $impactColors = [
                                    'none' => 'bg-gray-800 text-gray-400 border border-gray-700',
                                    'minor' => 'bg-blue-900/50 text-blue-300 border border-blue-700',
                                    'major' => 'bg-orange-900/50 text-orange-300 border border-orange-700',
                                    'critical' => 'bg-red-900/50 text-red-300 border border-red-700',
                                ];
                                $impactColor = $impactColors[$incident['impact']] ?? 'bg-gray-800 text-gray-400 border border-gray-700';
                                ?>
                                <span class="px-2 py-1 text-xs font-medium rounded <?= $impactColor ?>">
                                    <?= ucfirst($incident['impact']) ?> Impact
                                </span>
                            </div>

                            <div class="text-sm text-gray-400 space-y-1">
                                <div>
                                    Created: <?= date('M j, Y g:i A', strtotime($incident['created_at'])) ?>
                                </div>
                                <?php if ($incident['resolved_at']): ?>
                                <div>
                                    Resolved: <?= date('M j, Y g:i A', strtotime($incident['resolved_at'])) ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($incident['scheduled_start']): ?>
                                <div>
                                    Scheduled: <?= date('M j, Y g:i A', strtotime($incident['scheduled_start'])) ?> - 
                                    <?= date('M j, Y g:i A', strtotime($incident['scheduled_end'])) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/admin/incidents/view/<?= $incident['id'] ?>" 
                               class="px-4 py-2 text-sm text-indigo-400 hover:bg-gray-800/50/5 rounded-lg transition">
                                View Details
                            </a>
                            <?php if ($incident['status'] !== 'resolved'): ?>
                            <form method="post" action="/admin/incidents/resolve/<?= $incident['id'] ?>" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" 
                                        onclick="return confirm('Mark this incident as resolved?')"
                                        class="px-4 py-2 text-sm text-green-400 hover:bg-gray-800/50/5 rounded-lg transition">
                                    Resolve
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-8 text-center text-gray-400">
                <p class="text-2xl mb-2">✓</p>
                <p class="mb-4">No incidents reported. Everything is running smoothly!</p>
                <a href="/admin/incidents/create" class="text-indigo-400 hover:text-indigo-300">Report an incident →</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

<?= view('admin/partials/footer') ?>
