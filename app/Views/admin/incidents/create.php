<?= view('admin/partials/header', ['title' => 'create - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'incidents']) ?>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/incidents" class="text-indigo-400 hover:text-indigo-300">← Back to Incidents</a>
        </div>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Report New Incident</h1>

            <form method="post" action="/admin/incidents/create" x-data="{ isScheduled: false }">
                <?= csrf_field() ?>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Incident Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               required
                               class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="e.g., API Service Degradation">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    required
                                    x-model="isScheduled"
                                    class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                <option value="investigating">🔍 Investigating</option>
                                <option value="identified">🎯 Identified</option>
                                <option value="monitoring">👁️ Monitoring</option>
                                <option value="scheduled">📅 Scheduled Maintenance</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Impact <span class="text-red-500">*</span>
                            </label>
                            <select name="impact" 
                                    required
                                    class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                <option value="none">None</option>
                                <option value="minor">Minor</option>
                                <option value="major">Major</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <!-- Scheduled maintenance fields -->
                    <div x-show="isScheduled === 'scheduled'" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Start Time
                            </label>
                            <input type="datetime-local" 
                                   name="scheduled_start" 
                                   class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                End Time
                            </label>
                            <input type="datetime-local" 
                                   name="scheduled_end" 
                                   class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Initial Update Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" 
                                  required
                                  rows="4"
                                  class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                                  placeholder="Describe what's happening and what you're doing about it..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">
                            Affected Components
                        </label>
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-white/10 rounded-lg p-4">
                            <?php if (!empty($components)): ?>
                                <?php foreach ($components as $component): ?>
                                <label class="flex items-center py-2 hover:bg-gray-900/50 px-2 rounded">
                                    <input type="checkbox" 
                                           name="component_ids[]" 
                                           value="<?= $component['id'] ?>"
                                           class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                                    <span class="ml-3 text-sm text-gray-300"><?= esc($component['name']) ?></span>
                                </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-400">No components available</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="notify_subscribers" 
                                   value="1"
                                   checked
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Notify subscribers</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_visible" 
                                   value="1"
                                   checked
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Visible on status page</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition font-medium">
                        Create Incident
                    </button>
                    <a href="/admin/incidents" 
                       class="flex-1 bg-gray-800 text-gray-300 px-6 py-3 rounded-lg hover:bg-gray-700 border border-white/10 transition font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
<?= view('admin/partials/footer') ?>