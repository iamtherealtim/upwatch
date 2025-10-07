<?= view('admin/partials/header', ['title' => 'create - UpWatch Admin']) ?>
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'subscribers']) ?>

    <main class="max-w-2xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="/admin/subscribers" class="text-indigo-400 hover:underline">← Back to Subscribers</a>
        </div>

        <div class="bg-gray-800/50 rounded-lg border border-white/10-md p-8">
            <h1 class="text-2xl font-bold text-white mb-6">Add Subscriber</h1>

            <?php if (session()->has('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session('error') ?>
            </div>
            <?php endif; ?>

            <form method="post" action="/admin/subscribers/create">
                <?= csrf_field() ?>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address *
                    </label>
                    <input type="email" 
                           name="email" 
                           required
                           placeholder="subscriber@example.com"
                           class="w-full px-4 py-2 bg-gray-900 text-white 4 py-2 border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    <p class="text-sm text-gray-400 mt-1">
                        Enter the subscriber's email address
                    </p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="auto_verify" 
                               value="1"
                               class="rounded border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                        <span class="ml-2 text-sm font-medium text-gray-300">
                            Auto-verify (skip email verification)
                        </span>
                    </label>
                    <p class="text-sm text-gray-400 mt-1 ml-6">
                        If checked, the subscriber will be immediately verified without email confirmation
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                        Add Subscriber
                    </button>
                    <a href="/admin/subscribers" 
                       class="px-6 py-3 border border-white/10 rounded-lg hover:bg-gray-900/50 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">💡 Tip</h3>
            <p class="text-sm text-blue-800">
                Manually added subscribers can be auto-verified to give them immediate access to notifications.
                Alternatively, leave it unchecked to send them a verification email.
            </p>
        </div>
    </main>
<?= view('admin/partials/footer') ?>