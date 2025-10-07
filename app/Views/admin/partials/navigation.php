<nav class="relative bg-gray-800/50 after:pointer-events-none after:absolute after:inset-x-0 after:bottom-0 after:h-px after:bg-white/10">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div class="relative flex h-16 items-center justify-between">
            <!-- Mobile menu button -->
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <button type="button" 
                        onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
                    <span class="sr-only">Open main menu</span>
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Logo and Desktop Navigation -->
            <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex shrink-0 items-center">
                    <a href="/admin" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-500 flex items-center justify-center">
                            <i data-lucide="chart-no-axes-combined" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-white">UpWatch</span>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:block">
                    <div class="flex space-x-4">
                        <a href="/admin" 
                           class="<?= ($active ?? '') === 'dashboard' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'dashboard' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="home" class="w-4 h-4"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="/admin/components" 
                           class="<?= ($active ?? '') === 'components' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'components' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="layout-grid" class="w-4 h-4"></i>
                            <span>Components</span>
                        </a>
                        <a href="/admin/incidents" 
                           class="<?= ($active ?? '') === 'incidents' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'incidents' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                            <span>Incidents</span>
                        </a>
                        <a href="/admin/monitors" 
                           class="<?= ($active ?? '') === 'monitors' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'monitors' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="activity" class="w-4 h-4"></i>
                            <span>Monitors</span>
                        </a>
                        <a href="/admin/subscribers" 
                           class="<?= ($active ?? '') === 'subscribers' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'subscribers' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Subscribers</span>
                        </a>
                        <a href="/admin/settings" 
                           class="<?= ($active ?? '') === 'settings' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
                           <?= ($active ?? '') === 'settings' ? 'aria-current="page"' : '' ?>>
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right side: Status Page link and User Menu -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <a href="/" target="_blank" class="mr-4 inline-flex items-center gap-1.5 text-sm text-gray-300 hover:text-white hidden sm:flex">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>Status Page</span>
                </a>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            type="button" 
                            class="relative flex items-center space-x-2 rounded-full bg-gray-800 p-1 pr-3 text-gray-400 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-medium text-white">
                            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                        </span>
                        <span class="text-sm text-gray-300"><?= esc($user['name'] ?? 'User') ?></span>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-gray-800 py-1 shadow-lg ring-1 ring-white/10 focus:outline-none">
                        <a href="/admin/settings" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Settings</span>
                        </a>
                        <form method="POST" action="/admin/logout" class="m-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-white/5 w-full text-left">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Sign out</span>
                            </button>
                        </form>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2">
            <a href="/admin" 
               class="<?= ($active ?? '') === 'dashboard' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'dashboard' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="home" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/components" 
               class="<?= ($active ?? '') === 'components' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'components' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span>Components</span>
            </a>
            <a href="/admin/incidents" 
               class="<?= ($active ?? '') === 'incidents' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'incidents' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                <span>Incidents</span>
            </a>
            <a href="/admin/monitors" 
               class="<?= ($active ?? '') === 'monitors' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'monitors' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="activity" class="w-5 h-5"></i>
                <span>Monitors</span>
            </a>
            <a href="/admin/subscribers" 
               class="<?= ($active ?? '') === 'subscribers' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'subscribers' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Subscribers</span>
            </a>
            <a href="/admin/settings" 
               class="<?= ($active ?? '') === 'settings' ? 'flex items-center gap-2 rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white' : 'flex items-center gap-2 rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white' ?>"
               <?= ($active ?? '') === 'settings' ? 'aria-current="page"' : '' ?>>
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>
</nav>
