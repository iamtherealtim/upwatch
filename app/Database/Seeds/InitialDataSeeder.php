<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Generate secure random password for admin user
        $adminPassword = $this->generateSecurePassword();
        
        // Create default admin user
        $userModel = new \App\Models\UserModel();
        $userModel->insert([
            'email'    => getenv('ADMIN_EMAIL') ?: 'admin@upwatch.local',
            'password' => $adminPassword,
            'name'     => 'Administrator',
            'role'     => 'admin',
            'is_active' => 1,
        ]);

        // Create default status page
        $statusPageModel = new \App\Models\StatusPageModel();
        $statusPageId = $statusPageModel->insert([
            'name'          => 'UpWatch Status',
            'slug'          => 'default',
            'primary_color' => '#3b82f6',
            'timezone'      => 'UTC',
            'is_public'     => 1,
            'is_active'     => 1,
        ]);

        // Create demo components
        $componentModel = new \App\Models\ComponentModel();
        
        $websiteId = $componentModel->insert([
            'status_page_id' => $statusPageId,
            'name'           => 'Website',
            'description'    => 'Main website frontend',
            'status'         => 'operational',
            'order'          => 1,
            'group_name'     => 'Core Services',
            'is_visible'     => 1,
        ]);

        $apiId = $componentModel->insert([
            'status_page_id' => $statusPageId,
            'name'           => 'API',
            'description'    => 'REST API backend',
            'status'         => 'operational',
            'order'          => 2,
            'group_name'     => 'Core Services',
            'is_visible'     => 1,
        ]);

        $databaseId = $componentModel->insert([
            'status_page_id' => $statusPageId,
            'name'           => 'Database',
            'description'    => 'Primary database cluster',
            'status'         => 'operational',
            'order'          => 3,
            'group_name'     => 'Infrastructure',
            'is_visible'     => 1,
        ]);

        // Create demo monitors
        $monitorModel = new \App\Models\MonitorModel();
        
        $monitorModel->insert([
            'component_id'         => $websiteId,
            'name'                 => 'Website Health Check',
            'type'                 => 'https',
            'target'               => 'https://example.com',
            'interval'             => 300, // 5 minutes
            'timeout'              => 10,
            'method'               => 'GET',
            'expected_status_code' => 200,
            'retry_count'          => 3,
            'failure_threshold'    => 3,
            'is_active'            => 1,
        ]);

        $monitorModel->insert([
            'component_id'         => $apiId,
            'name'                 => 'API Health Check',
            'type'                 => 'https',
            'target'               => 'https://api.example.com/health',
            'interval'             => 300,
            'timeout'              => 10,
            'method'               => 'GET',
            'expected_status_code' => 200,
            'keyword_match'        => 'ok',
            'retry_count'          => 3,
            'failure_threshold'    => 3,
            'is_active'            => 1,
        ]);

        // Create a sample resolved incident
        $incidentModel = new \App\Models\IncidentModel();
        $incidentId = $incidentModel->insert([
            'status_page_id' => $statusPageId,
            'title'          => 'Database Performance Degradation',
            'status'         => 'resolved',
            'impact'         => 'minor',
            'resolved_at'    => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'is_visible'     => 1,
            'created_at'     => date('Y-m-d H:i:s', strtotime('-4 hours')),
        ]);

        // Add incident updates
        $updateModel = new \App\Models\IncidentUpdateModel();
        $updateModel->insert([
            'incident_id' => $incidentId,
            'status'      => 'investigating',
            'message'     => 'We are investigating reports of slow database queries.',
            'created_at'  => date('Y-m-d H:i:s', strtotime('-4 hours')),
        ]);

        $updateModel->insert([
            'incident_id' => $incidentId,
            'status'      => 'identified',
            'message'     => 'We have identified the issue as a query optimization problem and are working on a fix.',
            'created_at'  => date('Y-m-d H:i:s', strtotime('-3 hours')),
        ]);

        $updateModel->insert([
            'incident_id' => $incidentId,
            'status'      => 'resolved',
            'message'     => 'The issue has been resolved. Database performance is back to normal.',
            'created_at'  => date('Y-m-d H:i:s', strtotime('-2 hours')),
        ]);

        // Link incident to database component
        $db = \Config\Database::connect();
        $db->table('incident_components')->insert([
            'incident_id'  => $incidentId,
            'component_id' => $databaseId,
        ]);

        echo "✓ Initial data seeded successfully!\n";
        echo "\n";
        echo "==================================================\n";
        echo "🔑 ADMIN CREDENTIALS - SAVE THESE IMMEDIATELY!\n";
        echo "==================================================\n";
        echo "Email:    " . (getenv('ADMIN_EMAIL') ?: 'admin@upwatch.local') . "\n";
        echo "Password: " . $adminPassword . "\n";
        echo "==================================================\n";
        echo "⚠️  This password will NOT be shown again!\n";
        echo "⚠️  Login at /admin/login and change it immediately!\n";
        echo "==================================================\n";
        echo "\n";
    }

    /**
     * Generate a secure random password that meets complexity requirements
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+[]{}|;:,.<>?';
        
        // Ensure at least one of each character type
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fill the rest randomly (total 16 characters)
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < 16; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle to avoid predictable pattern
        $password = str_shuffle($password);
        
        return $password;
    }
}
