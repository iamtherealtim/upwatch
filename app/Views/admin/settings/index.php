<?= view('admin/partials/header', ['title' => 'Settings - UpWatch Admin']) ?>
    
    <?= view('admin/partials/navigation', ['user' => $user, 'active' => 'settings']) ?>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-white mb-8">Settings</h1>

        <?php if (session()->has('success')): ?>
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
            <?= session('success') ?>
        </div>
        <?php endif; ?>

        <form method="post" action="/admin/settings" x-data="settingsForm()">
            <?= csrf_field() ?>

            <!-- General Settings -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">General Settings</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Site Name</label>
                        <input type="text" 
                               name="site_name" 
                               value="<?= esc($settings['site_name'] ?? 'UpWatch') ?>"
                               class="w-full px-4 py-2 bg-gray-900 border border-white/10 text-white rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Site URL</label>
                        <input type="url" 
                               name="site_url" 
                               value="<?= esc($settings['site_url'] ?? '') ?>"
                               placeholder="https://status.example.com"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Support Email</label>
                        <input type="email" 
                               name="support_email" 
                               value="<?= esc($settings['support_email'] ?? '') ?>"
                               placeholder="support@example.com"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Time Zone</label>
                        <select name="timezone" 
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <?php
                            $timezones = [
                                'UTC' => 'UTC',
                                'America/New_York' => 'Eastern Time (ET)',
                                'America/Chicago' => 'Central Time (CT)',
                                'America/Denver' => 'Mountain Time (MT)',
                                'America/Los_Angeles' => 'Pacific Time (PT)',
                                'Europe/London' => 'London (GMT)',
                                'Europe/Paris' => 'Paris (CET)',
                                'Asia/Tokyo' => 'Tokyo (JST)',
                                'Australia/Sydney' => 'Sydney (AEDT)',
                            ];
                            $current = $settings['timezone'] ?? 'UTC';
                            foreach ($timezones as $value => $label):
                            ?>
                            <option value="<?= $value ?>" <?= $value === $current ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Email Settings</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email Provider</label>
                        <select name="email_provider" 
                                x-model="provider"
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <?php foreach ($providers as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($settings['email_provider'] ?? 'smtp') === $key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-sm text-gray-400 mt-1">Choose your email service provider</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">From Email</label>
                        <input type="email" 
                               name="from_email" 
                               value="<?= esc($settings['from_email'] ?? '') ?>"
                               placeholder="noreply@example.com"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">From Name</label>
                        <input type="text" 
                               name="from_name" 
                               value="<?= esc($settings['from_name'] ?? 'UpWatch') ?>"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <!-- Generic SMTP -->
                    <div x-show="provider === 'smtp'" class="border-t pt-4 space-y-4">
                        <h3 class="font-medium text-white">SMTP Configuration</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Host</label>
                            <input type="text" 
                                   name="smtp_host" 
                                   value="<?= esc($settings['smtp_host'] ?? '') ?>"
                                   placeholder="smtp.example.com"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Port</label>
                                <input type="number" 
                                       name="smtp_port" 
                                       value="<?= esc($settings['smtp_port'] ?? '587') ?>"
                                       class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Encryption</label>
                                <select name="smtp_encryption" 
                                        class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                                    <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="" <?= empty($settings['smtp_encryption']) ? 'selected' : '' ?>>None</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Username</label>
                            <input type="text" 
                                   name="smtp_username" 
                                   value="<?= esc($settings['smtp_username'] ?? '') ?>"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Password</label>
                            <input type="password" 
                                   name="smtp_password" 
                                   value="<?= esc($settings['smtp_password'] ?? '') ?>"
                                   placeholder="Leave blank to keep current"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Gmail / Outlook / Office365 -->
                    <div x-show="['gmail', 'outlook', 'office365'].includes(provider)" class="border-t pt-4 space-y-4">
                        <h3 class="font-medium text-white">
                            <span x-show="provider === 'gmail'">Gmail Configuration</span>
                            <span x-show="provider === 'outlook'">Outlook Configuration</span>
                            <span x-show="provider === 'office365'">Microsoft 365 Configuration</span>
                        </h3>
                        
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
                            <strong>Note:</strong> 
                            <span x-show="provider === 'gmail'">
                                You need to enable 2FA and generate an App Password in your Google Account settings.
                            </span>
                            <span x-show="provider === 'outlook'">
                                Use your full Outlook.com email address and password (or App Password if 2FA enabled).
                            </span>
                            <span x-show="provider === 'office365'">
                                Use your Microsoft 365 email address and password (or App Password).
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                            <input type="text" 
                                   name="smtp_username" 
                                   value="<?= esc($settings['smtp_username'] ?? '') ?>"
                                   placeholder="your-email@domain.com"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Password / App Password</label>
                            <input type="password" 
                                   name="smtp_password" 
                                   value="<?= esc($settings['smtp_password'] ?? '') ?>"
                                   placeholder="Leave blank to keep current"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- SendGrid -->
                    <div x-show="provider === 'sendgrid'" class="border-t pt-4 space-y-4">
                        <h3 class="font-medium text-white">SendGrid Configuration</h3>
                        
                        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded text-sm">
                            <strong>Note:</strong> Create an API key in your SendGrid account settings.
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SendGrid API Key</label>
                            <input type="password" 
                                   name="sendgrid_api_key" 
                                   value="<?= esc($settings['sendgrid_api_key'] ?? '') ?>"
                                   placeholder="SG.xxxxxxxxxxxxxxxxxxxxx"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Mailgun -->
                    <div x-show="provider === 'mailgun'" class="border-t pt-4 space-y-4">
                        <h3 class="font-medium text-white">Mailgun Configuration</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Mailgun Username</label>
                            <input type="text" 
                                   name="mailgun_username" 
                                   value="<?= esc($settings['mailgun_username'] ?? '') ?>"
                                   placeholder="postmaster@your-domain.mailgun.org"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Mailgun Password</label>
                            <input type="password" 
                                   name="mailgun_password" 
                                   value="<?= esc($settings['mailgun_password'] ?? '') ?>"
                                   placeholder="Leave blank to keep current"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Amazon SES -->
                    <div x-show="provider === 'ses'" class="border-t pt-4 space-y-4">
                        <h3 class="font-medium text-white">Amazon SES Configuration</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SES SMTP Host</label>
                            <input type="text" 
                                   name="ses_host" 
                                   value="<?= esc($settings['ses_host'] ?? 'email-smtp.us-east-1.amazonaws.com') ?>"
                                   placeholder="email-smtp.region.amazonaws.com"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Username</label>
                            <input type="text" 
                                   name="ses_username" 
                                   value="<?= esc($settings['ses_username'] ?? '') ?>"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">SMTP Password</label>
                            <input type="password" 
                                   name="ses_password" 
                                   value="<?= esc($settings['ses_password'] ?? '') ?>"
                                   placeholder="Leave blank to keep current"
                                   class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Test Email -->
                    <div class="border-t border-white/10 pt-4">
                        <button type="button" 
                                @click="testEmail()"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 transition">
                            Test Email Configuration
                        </button>

                        <div x-show="emailTestResult" 
                             x-text="emailTestResult"
                             :class="emailTestSuccess ? 'text-green-400' : 'text-red-400'"
                             class="text-sm font-medium mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Notification Settings</h2>

                <div class="space-y-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="notify_on_incident" 
                                   value="1"
                                   <?= !empty($settings['notify_on_incident']) ? 'checked' : '' ?>
                                   class="rounded bg-gray-900 border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Send email on new incidents</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="notify_on_recovery" 
                                   value="1"
                                   <?= !empty($settings['notify_on_recovery']) ? 'checked' : '' ?>
                                   class="rounded bg-gray-900 border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Send email on recovery</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="notify_on_maintenance" 
                                   value="1"
                                   <?= !empty($settings['notify_on_maintenance']) ? 'checked' : '' ?>
                                   class="rounded bg-gray-900 border-white/10 accent-indigo-600 hover:accent-indigo-500 focus:outline-none focus:border-indigo-500">
                            <span class="ml-2 text-sm font-medium text-gray-300">Send email for scheduled maintenance</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Monitor Settings -->
            <div class="bg-gray-800/50 rounded-lg border border-white/10 p-6 mb-6">
                <h2 class="text-xl font-bold text-white mb-4">Monitor Settings</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Data Retention (days)</label>
                        <input type="number" 
                               name="monitor_data_retention" 
                               value="<?= esc($settings['monitor_data_retention'] ?? '90') ?>"
                               min="7"
                               max="365"
                               class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">How long to keep monitor check results</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Default Check Interval (seconds)</label>
                        <select name="default_check_interval" 
                                class="w-full px-4 py-2 bg-gray-900 text-white border border-white/10 rounded-lg focus:outline-none focus:border-indigo-500">
                            <?php
                            $intervals = [60 => '1 minute', 300 => '5 minutes', 900 => '15 minutes', 1800 => '30 minutes', 3600 => '1 hour'];
                            $current = $settings['default_check_interval'] ?? 300;
                            foreach ($intervals as $value => $label):
                            ?>
                            <option value="<?= $value ?>" <?= $value == $current ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-500 transition font-medium">
                    Save Settings
                </button>
            </div>
        </form>
    </main>

    <script>
        function settingsForm() {
            return {
                provider: '<?= esc($settings['email_provider'] ?? 'smtp') ?>',
                emailTestResult: '',
                emailTestSuccess: false,

                async testEmail() {
                    this.emailTestResult = 'Sending test email...';
                    this.emailTestSuccess = false;
                    
                    try {
                        const response = await fetch('/admin/settings/test-email', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.emailTestResult = '✓ Test email sent successfully! Check your inbox.';
                            this.emailTestSuccess = true;
                        } else {
                            this.emailTestResult = '✗ Failed: ' + data.message;
                            this.emailTestSuccess = false;
                        }
                    } catch (error) {
                        this.emailTestResult = '✗ Error sending test email: ' + error.message;
                        this.emailTestSuccess = false;
                    }
                }
            }
        }
    </script>
<?= view('admin/partials/footer') ?>
