<?= view('admin/partials/header', ['title' => 'create - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'monitors']) ?>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/monitors" class="text-indigo-400 hover:text-indigo-300">← Back to Monitors</a>
        </div>

        <?php if (session()->has('error')): ?>
        <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6">
            <?= session('error') ?>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6">
            <h1 class="text-2xl font-bold text-white mb-6">Create New Monitor</h1>

            <form method="post" action="/admin/monitors/create" x-data="monitorForm()">
                <?= csrf_field() ?>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Component <span class="text-red-500">*</span>
                        </label>
                        <select name="component_id" 
                                required
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="">Select a component...</option>
                            <?php foreach ($components as $component): ?>
                            <option value="<?= $component['id'] ?>"><?= esc($component['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Monitor Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               required
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="e.g., Website Health Check">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Monitor Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                required
                                x-model="type"
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="https">HTTPS - Web endpoint check</option>
                            <option value="http">HTTP - Insecure web endpoint</option>
                            <option value="tcp">TCP - Port connectivity check</option>
                            <option value="ssl">SSL - Certificate expiration check</option>
                            <option value="ping">Ping - ICMP echo test</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Target <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="target" 
                               required
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                               :placeholder="targetPlaceholder">
                        <p class="text-sm text-gray-400 mt-1" x-text="targetHint"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Check Interval (seconds)
                            </label>
                            <select name="interval" 
                                    class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                <option value="60">1 minute</option>
                                <option value="300" selected>5 minutes</option>
                                <option value="900">15 minutes</option>
                                <option value="1800">30 minutes</option>
                                <option value="3600">1 hour</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Timeout (seconds)
                            </label>
                            <input type="number" 
                                   name="timeout" 
                                   value="10"
                                   min="1"
                                   max="60"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- HTTP-specific fields -->
                    <div x-show="['http', 'https'].includes(type)">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    HTTP Method
                                </label>
                                <select name="method" 
                                        class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="HEAD">HEAD</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    Expected Status Code
                                </label>
                                <input type="number" 
                                       name="expected_status_code" 
                                       value="200"
                                       class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Keyword Match (optional)
                            </label>
                            <input type="text" 
                                   name="keyword_match" 
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500"
                                   placeholder="Text to find in response">
                            <p class="text-sm text-gray-400 mt-1">Monitor will fail if this text is not found in the response</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Retry Count
                            </label>
                            <input type="number" 
                                   name="retry_count" 
                                   value="3"
                                   min="0"
                                   max="10"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Failure Threshold
                            </label>
                            <input type="number" 
                                   name="failure_threshold" 
                                   value="3"
                                   min="1"
                                   max="10"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <p class="text-sm text-gray-400 mt-1">Failed checks before marking component as down</p>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   checked
                                   class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Active (monitor will run automatically)</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                        Create Monitor
                    </button>
                    <a href="/admin/monitors" 
                       class="flex-1 bg-gray-800 text-gray-300 px-6 py-3 rounded-lg hover:bg-gray-700 border border-white/10 transition font-medium text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function monitorForm() {
            return {
                type: 'https',
                get targetPlaceholder() {
                    const placeholders = {
                        'http': 'http://example.com',
                        'https': 'https://example.com',
                        'tcp': 'example.com:3306',
                        'ssl': 'https://example.com',
                        'ping': 'example.com'
                    };
                    return placeholders[this.type] || '';
                },
                get targetHint() {
                    const hints = {
                        'http': 'Enter the full HTTP URL to monitor',
                        'https': 'Enter the full HTTPS URL to monitor',
                        'tcp': 'Enter hostname:port (e.g., db.example.com:3306)',
                        'ssl': 'Enter HTTPS URL to check certificate',
                        'ping': 'Enter hostname or IP address'
                    };
                    return hints[this.type] || '';
                }
            }
        }
    </script>
<?= view('admin/partials/footer') ?>