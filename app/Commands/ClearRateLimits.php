<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Clear Rate Limits Command
 * 
 * Clears all rate limiting data from cache
 */
class ClearRateLimits extends BaseCommand
{
    protected $group       = 'Cache';
    protected $name        = 'ratelimit:clear';
    protected $description = 'Clear all rate limiting data';

    public function run(array $params)
    {
        CLI::write('Clearing rate limit data...', 'yellow');
        
        $cache = \Config\Services::cache();
        
        // Get all cache items (this is cache-handler specific)
        // For file cache, we can clear by pattern
        
        // Try to clear common rate limit keys
        $cleared = 0;
        
        // We'll need to clear the entire cache since CodeIgniter doesn't
        // provide a way to delete by pattern
        if ($cache->clean()) {
            CLI::write('✓ Cache cleared successfully', 'green');
            CLI::write('All rate limits have been reset.', 'green');
        } else {
            CLI::write('✗ Failed to clear cache', 'red');
            CLI::write('Try manually: php spark cache:clear', 'yellow');
        }
    }
}
