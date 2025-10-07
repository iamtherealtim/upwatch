<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SettingModel;
use App\Helpers\EncryptionHelper;

/**
 * Encrypt Settings Command
 * 
 * Encrypts all sensitive settings in the database
 */
class EncryptSettings extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'settings:encrypt';
    protected $description = 'Encrypt all sensitive settings in the database';

    public function run(array $params)
    {
        CLI::write('Starting settings encryption...', 'yellow');
        
        $settingModel = new SettingModel();
        
        // Get sensitive keys
        $encryptedKeys = [
            'smtp_password',
            'sendgrid_api_key',
            'mailgun_secret',
            'mailgun_password',
            'postmark_api_key',
            'ses_secret_key',
            'sparkpost_api_key',
        ];
        
        $encrypted = 0;
        $skipped = 0;
        
        foreach ($encryptedKeys as $key) {
            $setting = $settingModel->where('key', $key)->first();
            
            if (!$setting) {
                CLI::write("  - {$key}: Not found (skipped)", 'yellow');
                $skipped++;
                continue;
            }
            
            $value = $setting['value'];
            
            if (empty($value)) {
                CLI::write("  - {$key}: Empty (skipped)", 'yellow');
                $skipped++;
                continue;
            }
            
            // Check if already encrypted
            if (EncryptionHelper::isEncrypted($value)) {
                CLI::write("  - {$key}: Already encrypted (skipped)", 'blue');
                $skipped++;
                continue;
            }
            
            // Encrypt it
            try {
                $encryptedValue = EncryptionHelper::encrypt($value);
                $settingModel->update($setting['id'], ['value' => $encryptedValue]);
                CLI::write("  ✓ {$key}: Encrypted successfully", 'green');
                $encrypted++;
            } catch (\Exception $e) {
                CLI::write("  ✗ {$key}: Failed to encrypt - " . $e->getMessage(), 'red');
            }
        }
        
        CLI::newLine();
        CLI::write("Encryption complete!", 'green');
        CLI::write("  Encrypted: {$encrypted}", 'green');
        CLI::write("  Skipped: {$skipped}", 'yellow');
    }
}
