<?php

namespace App\Validation;

/**
 * Custom Validation Rules for Security
 */
class SecurityRules
{
    /**
     * Validate URL to prevent SSRF attacks
     * 
     * Blocks:
     * - Internal IP addresses (127.0.0.1, 10.x.x.x, 192.168.x.x, 172.16-31.x.x)
     * - localhost
     * - Private/reserved IP ranges
     * - Non-HTTP(S) schemes (file://, ftp://, etc.)
     * - Cloud metadata endpoints
     * 
     * @param string $str The URL to validate
     * @param string|null $error Error message reference
     * @return bool
     */
    public function valid_url_strict(string $str, ?string &$error = null): bool
    {
        // Must be valid URL format
        if (!filter_var($str, FILTER_VALIDATE_URL)) {
            $error = 'Invalid URL format';
            return false;
        }

        $parsed = parse_url($str);
        
        if (!$parsed || !isset($parsed['host'])) {
            $error = 'Invalid URL - missing host';
            return false;
        }

        $host = strtolower($parsed['host']);
        $scheme = strtolower($parsed['scheme'] ?? '');

        // Only allow HTTP and HTTPS
        if (!in_array($scheme, ['http', 'https'])) {
            $error = 'Only HTTP and HTTPS protocols are allowed';
            return false;
        }

        // Block localhost variations
        $localhostPatterns = [
            'localhost',
            'localhost.localdomain',
            '127.0.0.1',
            '::1',
            '0.0.0.0',
            '0000:0000:0000:0000:0000:0000:0000:0001',
        ];

        if (in_array($host, $localhostPatterns)) {
            $error = 'Cannot monitor localhost addresses';
            return false;
        }

        // Block internal domain names
        $internalDomains = [
            '.local',
            '.internal',
            '.lan',
            '.corp',
            '.home',
            '.localdomain',
        ];

        foreach ($internalDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                $error = 'Cannot monitor internal domain names';
                return false;
            }
        }

        // Block cloud metadata endpoints
        $metadataHosts = [
            '169.254.169.254',  // AWS, Azure, Google Cloud
            'metadata.google.internal',
            'metadata.azure.com',
        ];

        if (in_array($host, $metadataHosts)) {
            $error = 'Cannot monitor cloud metadata endpoints';
            return false;
        }

        // Resolve hostname to IP and check if it's private/reserved
        $ip = gethostbyname($host);
        
        // If hostname didn't resolve, gethostbyname returns the hostname
        if ($ip !== $host && filter_var($ip, FILTER_VALIDATE_IP)) {
            // Block private and reserved IP ranges
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $error = 'Cannot monitor private or reserved IP addresses';
                return false;
            }
        }

        // Additional check: if host is an IP address directly
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $error = 'Cannot monitor private or reserved IP addresses';
                return false;
            }
        }

        return true;
    }

    /**
     * Validate password complexity
     * 
     * Requires:
     * - At least 12 characters
     * - 1 uppercase letter
     * - 1 lowercase letter
     * - 1 number
     * - 1 special character
     * - Not in common passwords list
     * 
     * @param string $str The password to validate
     * @param string|null $error Error message reference
     * @return bool
     */
    public function password_complexity(string $str, ?string &$error = null): bool
    {
        // Minimum length
        if (strlen($str) < 12) {
            $error = 'Password must be at least 12 characters long';
            return false;
        }

        // Require uppercase letter
        if (!preg_match('/[A-Z]/', $str)) {
            $error = 'Password must contain at least one uppercase letter';
            return false;
        }

        // Require lowercase letter
        if (!preg_match('/[a-z]/', $str)) {
            $error = 'Password must contain at least one lowercase letter';
            return false;
        }

        // Require number
        if (!preg_match('/[0-9]/', $str)) {
            $error = 'Password must contain at least one number';
            return false;
        }

        // Require special character
        if (!preg_match('/[^A-Za-z0-9]/', $str)) {
            $error = 'Password must contain at least one special character (!@#$%^&* etc.)';
            return false;
        }

        // Check against common passwords
        $commonPasswords = [
            'password',
            'password123',
            'admin123',
            'administrator',
            '12345678',
            '123456789',
            '1234567890',
            'qwerty',
            'qwertyuiop',
            'letmein',
            'welcome',
            'monkey',
            'dragon',
            'master',
            'sunshine',
            'princess',
            'football',
            'baseball',
            'abc123',
            'passw0rd',
            'p@ssw0rd',
            'p@ssword',
        ];

        if (in_array(strtolower($str), $commonPasswords)) {
            $error = 'This password is too common. Please choose a stronger password';
            return false;
        }

        // Check for sequential characters
        if (preg_match('/(?:012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $str)) {
            $error = 'Password cannot contain sequential characters';
            return false;
        }

        // Check for repeated characters (aaa, 111, etc.)
        if (preg_match('/(.)\1{2,}/', $str)) {
            $error = 'Password cannot contain repeated characters (aaa, 111, etc.)';
            return false;
        }

        return true;
    }
}
