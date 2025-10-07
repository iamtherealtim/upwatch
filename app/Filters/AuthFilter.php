<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Authentication Filter
 * 
 * Ensures user is logged in before accessing protected routes
 */
class AuthFilter implements FilterInterface
{
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
        $session = session();
        
        // Get the current URI path
        $path = $request->getUri()->getPath();
        
        // Remove index.php from path if present
        $path = str_replace('/index.php', '', $path);
        
        // DEBUG: Log what URI we're checking
        log_message('debug', "AuthFilter checking path: {$path} | Logged in: " . ($session->get('logged_in') ? 'yes' : 'no'));
        
        // Allow access to login, logout, and auth routes without authentication
        if (preg_match('#^/admin/(login|logout|auth)#', $path)) {
            log_message('debug', "AuthFilter: Allowing access to auth route: {$path}");
            return;
        }
        
        if (!$session->get('logged_in')) {
            // Store the intended URL to redirect after login
            $session->set('redirect_url', current_url());
            
            log_message('debug', "AuthFilter redirecting to /admin/login from: {$path}");
            return redirect()->to('/admin/login')->with('error', 'Please login to continue');
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
        // Nothing to do here
    }
}
