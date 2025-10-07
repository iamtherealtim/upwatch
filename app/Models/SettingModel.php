<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Helpers\EncryptionHelper;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'key',
        'value',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    /**
     * Settings that should be encrypted
     */
    protected $encryptedKeys = [
        'smtp_password',
        'sendgrid_api_key',
        'mailgun_secret',
        'mailgun_password',
        'postmark_api_key',
        'ses_secret_key',
        'sparkpost_api_key',
    ];

    // Validation
    protected $validationRules      = [
        'key' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get a single setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        $value = $setting['value'];
        
        // Decrypt if this is an encrypted key
        if (in_array($key, $this->encryptedKeys) && !empty($value)) {
            try {
                // Check if already encrypted (migration scenario)
                if (EncryptionHelper::isEncrypted($value)) {
                    $value = EncryptionHelper::decrypt($value);
                }
            } catch (\Exception $e) {
                log_message('error', "Failed to decrypt setting: {$key}");
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * Set a setting value
     */
    public function setSetting(string $key, $value)
    {
        // Encrypt if this is a sensitive key
        if (in_array($key, $this->encryptedKeys) && !empty($value)) {
            try {
                // Only encrypt if not already encrypted
                if (!EncryptionHelper::isEncrypted($value)) {
                    $value = EncryptionHelper::encrypt($value);
                }
            } catch (\Exception $e) {
                log_message('error', "Failed to encrypt setting: {$key}");
                throw $e;
            }
        }
        
        $existing = $this->where('key', $key)->first();
        
        if ($existing) {
            return $this->update($existing['id'], ['value' => $value]);
        }
        
        return $this->insert(['key' => $key, 'value' => $value]);
    }

    /**
     * Get all settings as key-value array
     */
    public function getAllAsArray()
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $key = $setting['key'];
            $value = $setting['value'];
            
            // Decrypt if this is an encrypted key
            if (in_array($key, $this->encryptedKeys) && !empty($value)) {
                try {
                    if (EncryptionHelper::isEncrypted($value)) {
                        $value = EncryptionHelper::decrypt($value);
                    }
                } catch (\Exception $e) {
                    log_message('error', "Failed to decrypt setting: {$key}");
                    $value = '';
                }
            }
            
            $result[$key] = $value;
        }
        
        return $result;
    }
}
