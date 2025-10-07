<?php

namespace App\Helpers;

use Config\Services;

/**
 * Encryption Helper
 * 
 * Provides encryption/decryption for sensitive data
 */
class EncryptionHelper
{
    /**
     * Encrypt a value
     * 
     * @param string $value The value to encrypt
     * @return string The encrypted value
     */
    public static function encrypt(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        
        try {
            $encrypter = Services::encrypter();
            return base64_encode($encrypter->encrypt($value));
        } catch (\Exception $e) {
            log_message('error', 'Encryption failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to encrypt data');
        }
    }
    
    /**
     * Decrypt a value
     * 
     * @param string $value The encrypted value
     * @return string The decrypted value
     */
    public static function decrypt(string $value): string
    {
        if (empty($value)) {
            return '';
        }
        
        try {
            $encrypter = Services::encrypter();
            return $encrypter->decrypt(base64_decode($value));
        } catch (\Exception $e) {
            log_message('error', 'Decryption failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to decrypt data');
        }
    }
    
    /**
     * Check if a value is encrypted
     * 
     * @param string $value The value to check
     * @return bool True if encrypted, false otherwise
     */
    public static function isEncrypted(string $value): bool
    {
        // Basic check - encrypted values will be base64 encoded
        // and much longer than typical plaintext
        if (empty($value) || strlen($value) < 40) {
            return false;
        }
        
        // Try to base64 decode
        $decoded = base64_decode($value, true);
        if ($decoded === false) {
            return false;
        }
        
        return true;
    }
}
