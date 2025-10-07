<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Rate Limiting Filter
 * 
 * Prevents brute force attacks by limiting login attempts
 */
class RateLimitFilter implements FilterInterface
{
    /**
     * Maximum attempts allowed
     */
    private int $maxAttempts = 5;

    /**
     * Time window in seconds (15 minutes)
     */
    private int $timeWindow = 900;

    /**
     * Lockout duration in seconds (30 minutes)
     */
    private int $lockoutDuration = 1800;

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $cache = \Config\Services::cache();
        $ipAddress = $request->getIPAddress();
        $route = $request->getUri()->getPath();
        
        // Create a unique key for this IP + route combination
        $key = 'rate_limit_' . md5($ipAddress . $route);
        $lockKey = 'rate_limit_lock_' . md5($ipAddress . $route);
        
        // Check if IP is locked out
        if ($cache->get($lockKey)) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON([
                    'error' => 'Too many attempts. Please try again later.',
                    'retry_after' => $cache->get($lockKey)
                ]);
        }
        
        // Get current attempt count
        $attempts = $cache->get($key);
        
        if ($attempts === null) {
            // First attempt
            $cache->save($key, 1, $this->timeWindow);
        } else {
            // Increment attempts
            $attempts++;
            
            if ($attempts > $this->maxAttempts) {
                // Lock out the IP
                $cache->save($lockKey, time() + $this->lockoutDuration, $this->lockoutDuration);
                $cache->delete($key);
                
                // Log the lockout
                log_message('warning', 'Rate limit exceeded for IP: ' . $ipAddress . ' on route: ' . $route);
                
                return service('response')
                    ->setStatusCode(429)
                    ->setJSON([
                        'error' => 'Too many attempts. Your IP has been temporarily blocked.',
                        'retry_after' => $this->lockoutDuration
                    ]);
            }
            
            $cache->save($key, $attempts, $this->timeWindow);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // If login was successful (redirect to admin), clear the rate limit
        if ($response->getStatusCode() === 302) {
            $location = $response->getHeaderLine('Location');
            if (strpos($location, '/admin') !== false && strpos($location, '/login') === false) {
                $cache = \Config\Services::cache();
                $ipAddress = $request->getIPAddress();
                $route = $request->getUri()->getPath();
                $key = 'rate_limit_' . md5($ipAddress . $route);
                
                // Clear successful login attempts
                $cache->delete($key);
            }
        }
    }
}
