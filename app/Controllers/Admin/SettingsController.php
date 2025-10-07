<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Libraries\EmailProvider;
use App\Libraries\NotificationService;

class SettingsController extends BaseController
{
    protected $settingModel;
    protected $emailProvider;

    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }

        $this->settingModel = new SettingModel();
        $this->emailProvider = new EmailProvider();
    }

    /**
     * Settings page
     */
    public function index()
    {
        if ($this->request->getMethod() === 'POST') {
            return $this->save();
        }

        // Get all settings as key-value array
        $settingsArray = $this->settingModel->findAll();
        $settings = [];
        foreach ($settingsArray as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        $data = [
            'user'      => $this->getCurrentUser(),
            'settings'  => $settings,
            'providers' => $this->emailProvider->getProviders(),
        ];

        echo view('admin/settings/index', $data);
    }

    /**
     * Save settings
     */
    private function save()
    {
        $postData = $this->request->getPost();
        
        // Remove CSRF token from post data
        unset($postData['csrf_test_name']);

        foreach ($postData as $key => $value) {
            // Skip empty password fields
            if ($key === 'smtp_password' && empty($value)) {
                continue;
            }

            // Convert checkboxes to 1/0
            if (in_array($key, ['notify_on_incident', 'notify_on_recovery', 'notify_on_maintenance'])) {
                $value = $value ? '1' : '0';
            }

            // Update or insert setting
            $existing = $this->settingModel->where('key', $key)->first();
            
            if ($existing) {
                $this->settingModel->update($existing['id'], ['value' => $value]);
            } else {
                $this->settingModel->insert(['key' => $key, 'value' => $value]);
            }
        }

        return redirect()->to('/admin/settings')->with('success', 'Settings saved successfully');
    }

    /**
     * Test email configuration
     */
    public function testEmail()
    {
        try {
            $notificationService = new NotificationService();
            $userEmail = $this->getCurrentUser()['email'];
            
            if ($notificationService->sendTestEmail($userEmail)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Test email sent successfully to ' . $userEmail
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send test email. Check your email configuration and logs.'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
