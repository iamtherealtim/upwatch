<?= view('admin/partials/header', ['title' => 'Subscribers - UpWatch Admin']) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'subscribers']) ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">Subscribers</h1>
            <div class="flex gap-3">
                <a href="/admin/subscribers/export" 
                   class="inline-flex items-center gap-2 bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Export CSV</span>
                </a>
                <a href="/admin/subscribers/create" 
                   class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition">
                    + Add Subscriber
                </a>
            </div>
        </div>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            <?= session('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
        <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6">
            <?= session('error') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->has('info')): ?>
        <div class="bg-blue-900/50 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg mb-6">
            <?= session('info') ?>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 mb-1">Total Subscribers</div>
                <div class="text-3xl font-bold text-white"><?= $stats['total'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 mb-1">Verified</div>
                <div class="text-3xl font-bold text-green-400"><?= $stats['verified'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 mb-1">Active</div>
                <div class="text-3xl font-bold text-blue-400"><?= $stats['active'] ?></div>
            </div>
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
                <div class="text-sm text-gray-400 mb-1">Pending Verification</div>
                <div class="text-3xl font-bold text-yellow-400"><?= $stats['unverified'] ?></div>
            </div>
        </div>

        <!-- Subscribers Table -->
        <div class="bg-gray-800/50 rounded-lg border border-white/10 overflow-hidden">
            <?php if (empty($subscribers)): ?>
            <div class="p-8 text-center text-gray-400">
                <p class="mb-4">No subscribers yet</p>
                <a href="/admin/subscribers/create" class="text-indigo-400 hover:text-indigo-300">
                    Add your first subscriber
                </a>
            </div>
            <?php else: ?>
            <table class="w-full">
                <thead class="bg-gray-900/50 border-b border-white/10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Verified
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Subscribed
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    <?php foreach ($subscribers as $subscriber): ?>
                    <tr class="hover:bg-gray-800/50/5">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-900/50 rounded-full flex items-center justify-center mr-3 border border-indigo-700">
                                    <span class="text-indigo-400 font-semibold text-sm">
                                        <?= strtoupper(substr($subscriber['email'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-200">
                                        <?= esc($subscriber['email']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($subscriber['is_active']): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-900/50 text-green-300 border border-green-700">
                                Active
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-900 text-gray-200">
                                Inactive
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($subscriber['is_verified']): ?>
                            <span class="flex items-center text-sm text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                            <?php else: ?>
                            <span class="flex items-center text-sm text-yellow-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Pending
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            <?= date('M j, Y', strtotime($subscriber['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <?php if (!$subscriber['is_verified']): ?>
                                <a href="/admin/subscribers/verify/<?= $subscriber['id'] ?>" 
                                   class="text-green-400 hover:text-green-300"
                                   title="Verify">
                                    ✓
                                </a>
                                <a href="/admin/subscribers/resend/<?= $subscriber['id'] ?>" 
                                   class="text-indigo-400 hover:text-indigo-300"
                                   title="Resend Verification">
                                    📧
                                </a>
                                <?php endif; ?>
                                <a href="/admin/subscribers/toggle/<?= $subscriber['id'] ?>" 
                                   class="text-gray-400 hover:text-white"
                                   title="Toggle Status">
                                    <?= $subscriber['is_active'] ? '⏸' : '▶' ?>
                                </a>
                                <a href="/admin/subscribers/delete/<?= $subscriber['id'] ?>" 
                                   class="text-red-400 hover:text-red-300"
                                   onclick="return confirm('Are you sure you want to delete this subscriber?')"
                                   title="Delete">
                                    🗑
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
<?= view('admin/partials/footer') ?>
