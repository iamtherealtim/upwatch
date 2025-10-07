<?= view('admin/partials/header', ['title' => 'edit - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'components']) ?>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/components" class="text-indigo-400 hover:text-indigo-300">← Back to Components</a>
        </div>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Edit Component</h1>

            <form method="post" action="/admin/components/edit/<?= $component['id'] ?>">
                <?= csrf_field() ?>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Component Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               required
                               value="<?= esc($component['name']) ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  rows="3"
                                  class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"><?= esc($component['description'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                required
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="operational" <?= $component['status'] === 'operational' ? 'selected' : '' ?>>🟢 Operational</option>
                            <option value="degraded" <?= $component['status'] === 'degraded' ? 'selected' : '' ?>>🟡 Degraded Performance</option>
                            <option value="partial_outage" <?= $component['status'] === 'partial_outage' ? 'selected' : '' ?>>🟠 Partial Outage</option>
                            <option value="major_outage" <?= $component['status'] === 'major_outage' ? 'selected' : '' ?>>🔴 Major Outage</option>
                            <option value="maintenance" <?= $component['status'] === 'maintenance' ? 'selected' : '' ?>>🔵 Under Maintenance</option>
                        </select>
                        <p class="text-sm text-gray-400 mt-1">This is the current status displayed to users on your status page. Update this manually when the component's operational state changes.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Group Name
                        </label>
                        <input type="text" 
                               name="group_name" 
                               value="<?= esc($component['group_name'] ?? '') ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Display Order
                        </label>
                        <input type="number" 
                               name="order" 
                               value="<?= $component['order'] ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_visible" 
                                   value="1"
                                   <?= $component['is_visible'] ? 'checked' : '' ?>
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Visible on status page</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                        Update Component
                    </button>
                    <a href="/admin/components" 
                       class="flex-1 bg-gray-800 text-gray-300 px-6 py-3 rounded-lg hover:bg-gray-700 border border-white/10 transition font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
<?= view('admin/partials/footer') ?>