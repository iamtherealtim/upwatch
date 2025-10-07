<?= view('admin/partials/header', ['title' => 'edit - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'monitors']) ?>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/monitors" class="text-indigo-400 hover:text-indigo-300">← Back to Monitors</a>
        </div>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Edit Monitor</h1>

            <form method="post" action="/admin/monitors/edit/<?= $monitor['id'] ?>" x-data="monitorForm('<?= $monitor['type'] ?>')">
                <?= csrf_field() ?>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Component</label>
                        <select name="component_id" 
                                required
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <?php foreach ($components as $component): ?>
                            <option value="<?= $component['id'] ?>" <?= $component['id'] == $monitor['component_id'] ? 'selected' : '' ?>>
                                <?= esc($component['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Monitor Name</label>
                        <input type="text" 
                               name="name" 
                               required
                               value="<?= esc($monitor['name']) ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Monitor Type</label>
                        <select name="type" 
                                required
                                x-model="type"
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="https" <?= $monitor['type'] === 'https' ? 'selected' : '' ?>>HTTPS</option>
                            <option value="http" <?= $monitor['type'] === 'http' ? 'selected' : '' ?>>HTTP</option>
                            <option value="tcp" <?= $monitor['type'] === 'tcp' ? 'selected' : '' ?>>TCP</option>
                            <option value="ssl" <?= $monitor['type'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            <option value="ping" <?= $monitor['type'] === 'ping' ? 'selected' : '' ?>>Ping</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Target</label>
                        <input type="text" 
                               name="target" 
                               required
                               value="<?= esc($monitor['target']) ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Interval (seconds)</label>
                            <select name="interval" 
                                    class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                <option value="60" <?= $monitor['interval'] == 60 ? 'selected' : '' ?>>1 minute</option>
                                <option value="300" <?= $monitor['interval'] == 300 ? 'selected' : '' ?>>5 minutes</option>
                                <option value="900" <?= $monitor['interval'] == 900 ? 'selected' : '' ?>>15 minutes</option>
                                <option value="1800" <?= $monitor['interval'] == 1800 ? 'selected' : '' ?>>30 minutes</option>
                                <option value="3600" <?= $monitor['interval'] == 3600 ? 'selected' : '' ?>>1 hour</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Timeout (seconds)</label>
                            <input type="number" 
                                   name="timeout" 
                                   value="<?= $monitor['timeout'] ?>"
                                   min="1"
                                   max="60"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div x-show="['http', 'https'].includes(type)">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">HTTP Method</label>
                                <select name="method" 
                                        class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                    <option value="GET" <?= $monitor['method'] === 'GET' ? 'selected' : '' ?>>GET</option>
                                    <option value="POST" <?= $monitor['method'] === 'POST' ? 'selected' : '' ?>>POST</option>
                                    <option value="HEAD" <?= $monitor['method'] === 'HEAD' ? 'selected' : '' ?>>HEAD</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Expected Status Code</label>
                                <input type="number" 
                                       name="expected_status_code" 
                                       value="<?= $monitor['expected_status_code'] ?>"
                                       class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Keyword Match</label>
                            <input type="text" 
                                   name="keyword_match" 
                                   value="<?= esc($monitor['keyword_match'] ?? '') ?>"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Retry Count</label>
                            <input type="number" 
                                   name="retry_count" 
                                   value="<?= $monitor['retry_count'] ?>"
                                   min="0"
                                   max="10"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Failure Threshold</label>
                            <input type="number" 
                                   name="failure_threshold" 
                                   value="<?= $monitor['failure_threshold'] ?>"
                                   min="1"
                                   max="10"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   <?= $monitor['is_active'] ? 'checked' : '' ?>
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                        Update Monitor
                    </button>
                    <a href="/admin/monitors" 
                       class="flex-1 bg-gray-800 text-gray-300 px-6 py-3 rounded-lg hover:bg-gray-700 border border-white/10 transition font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function monitorForm(initialType) {
            return {
                type: initialType
            }
        }
    </script>
<?= view('admin/partials/footer') ?>