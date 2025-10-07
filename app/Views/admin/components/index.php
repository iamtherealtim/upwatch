<?= view('admin/partials/header', ['title' => 'Components - UpWatch Admin']) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'components']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-white">Components</h1>
            <a href="/admin/components/create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition">
                + Add Component
            </a>
        </div>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            ✓ <?= session('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
        <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6">
            ✗ <?= session('error') ?>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 overflow-hidden">
            <?php if (!empty($components)): ?>
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Visible</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    <?php foreach ($components as $component): ?>
                    <tr class="hover:bg-gray-800/50/5">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-200"><?= esc($component['name']) ?></div>
                            <?php if ($component['description']): ?>
                            <div class="text-sm text-gray-400"><?= esc($component['description']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            <?= esc($component['group_name'] ?? 'Default') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $statusColors = [
                                'operational' => 'bg-green-900/50 text-green-300 border border-green-700',
                                'degraded' => 'bg-yellow-900/50 text-yellow-300 border border-yellow-700',
                                'partial_outage' => 'bg-orange-900/50 text-orange-300 border border-orange-700',
                                'major_outage' => 'bg-red-900/50 text-red-300 border border-red-700',
                                'maintenance' => 'bg-blue-900/50 text-blue-300 border border-blue-700',
                            ];
                            $color = $statusColors[$component['status']] ?? 'bg-gray-800 text-gray-300 border border-gray-700';
                            ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                <?= ucfirst(str_replace('_', ' ', $component['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            <?= $component['order'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                            <?= $component['is_visible'] ? '✓ Yes' : '✗ No' ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/admin/components/edit/<?= $component['id'] ?>" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                            <a href="/admin/components/delete/<?= $component['id'] ?>" 
                               onclick="return confirm('Are you sure you want to delete this component?')"
                               class="text-red-400 hover:text-red-300">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="p-8 text-center text-gray-400">
                <p class="mb-4">No components yet.</p>
                <a href="/admin/components/create" class="text-indigo-400 hover:text-indigo-300">Create your first component →</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

<?= view('admin/partials/footer') ?>
