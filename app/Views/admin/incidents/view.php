<?= view('admin/partials/header', ['title' => 'view - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'incidents']) ?>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/incidents" class="text-indigo-400 hover:text-indigo-300">← Back to Incidents</a>
        </div>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            ✓ <?= session('success') ?>
        </div>
        <?php endif; ?>

        <!-- Incident Header -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-2xl font-bold text-white"><?= esc($incident['title']) ?></h1>
                <?php if ($incident['status'] !== 'resolved'): ?>
                <form method="post" action="/admin/incidents/resolve/<?= $incident['id'] ?>" class="inline">
                    <?= csrf_field() ?>
                    <button type="submit" 
                            onclick="return confirm('Mark as resolved?')"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                        Mark as Resolved
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <div class="flex gap-4 mb-4">
                <?php
                $statusColors = [
                    'investigating' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-700',
                    'identified' => 'bg-orange-900/50 text-orange-300 border border-orange-700',
                    'monitoring' => 'bg-blue-900/50 text-blue-300 border border-blue-700',
                    'resolved' => 'bg-green-900/50 text-green-300 border border-green-700',
                    'scheduled' => 'bg-purple-900/50 text-purple-300 border border-purple-700',
                ];
                $statusColor = $statusColors[$incident['status']] ?? 'bg-gray-900 text-gray-200';
                ?>
                <span class="px-4 py-2 text-sm font-semibold rounded-full <?= $statusColor ?>">
                    <?= ucfirst($incident['status']) ?>
                </span>

                <?php
                $impactColors = [
                    'none' => 'bg-gray-900 text-gray-400',
                    'minor' => 'bg-blue-100 text-indigo-400',
                    'major' => 'bg-orange-900/50 text-orange-300 border border-orange-700',
                    'critical' => 'bg-red-100 text-red-400',
                ];
                $impactColor = $impactColors[$incident['impact']] ?? 'bg-gray-900 text-gray-400';
                ?>
                <span class="px-4 py-2 text-sm font-semibold rounded <?= $impactColor ?>">
                    <?= ucfirst($incident['impact']) ?> Impact
                </span>
            </div>

            <div class="text-sm text-gray-400 space-y-1">
                <div>Created: <?= date('F j, Y g:i A', strtotime($incident['created_at'])) ?></div>
                <?php if ($incident['resolved_at']): ?>
                <div>Resolved: <?= date('F j, Y g:i A', strtotime($incident['resolved_at'])) ?></div>
                <?php endif; ?>
            </div>

            <?php if (!empty($incident['components'])): ?>
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-2">Affected Components:</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($incident['components'] as $component): ?>
                    <span class="px-3 py-1 bg-gray-900 text-gray-300 rounded-full text-sm">
                        <?= esc($component['name']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Update Timeline -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-4">Timeline</h2>

            <?php if (!empty($incident['updates'])): ?>
            <div class="space-y-6">
                <?php foreach ($incident['updates'] as $update): ?>
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <?php
                        $updateStatusColors = [
                            'investigating' => 'bg-yellow-500',
                            'identified' => 'bg-orange-500',
                            'monitoring' => 'bg-blue-500',
                            'resolved' => 'bg-green-500',
                            'scheduled' => 'bg-purple-500',
                            'in_progress' => 'bg-indigo-500',
                            'completed' => 'bg-green-600',
                        ];
                        $updateColor = $updateStatusColors[$update['status']] ?? 'bg-gray-900/500';
                        ?>
                        <div class="w-10 h-10 rounded-full <?= $updateColor ?> flex items-center justify-center text-white font-bold">
                            <?= strtoupper(substr($update['status'], 0, 1)) ?>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-semibold text-white capitalize">
                                <?= str_replace('_', ' ', $update['status']) ?>
                            </span>
                            <span class="text-sm text-gray-400">
                                <?= date('M j, g:i A', strtotime($update['created_at'])) ?>
                            </span>
                        </div>
                        <p class="text-gray-300"><?= nl2br(esc($update['message'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400">No updates yet.</p>
            <?php endif; ?>
        </div>

        <!-- Add Update Form -->
        <?php if ($incident['status'] !== 'resolved'): ?>
        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h2 class="text-xl font-bold text-white mb-4">Add Update</h2>

            <form method="post" action="/admin/incidents/update/<?= $incident['id'] ?>">
                <?= csrf_field() ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                        <select name="status" 
                                required
                                class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="investigating">🔍 Investigating</option>
                            <option value="identified">🎯 Identified</option>
                            <option value="monitoring">👁️ Monitoring</option>
                            <option value="resolved">✅ Resolved</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Update Message</label>
                        <textarea name="message" 
                                  required
                                  rows="4"
                                  class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                                  placeholder="What's the latest on this incident?"></textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="notify_subscribers" 
                                   value="1"
                                   checked
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Notify subscribers</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                        Post Update
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </main>
<?= view('admin/partials/footer') ?>