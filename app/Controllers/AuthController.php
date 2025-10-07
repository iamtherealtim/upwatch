<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    /**
     * Show login page
     */
    public function login()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    /**
     * Process login
     */
    public function loginPost()
    {
        $userModel = new UserModel();
        
        // Regenerate session BEFORE authentication to prevent fixation
        $session = session();
        $session->regenerate(true);

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->verifyPassword($email, $password);

        if ($user) {
            // Regenerate session AGAIN after successful authentication
            $session->regenerate(true);
            
            $session->set([
                'user_id'      => $user['id'],
                'user_name'    => $user['name'],
                'user_role'    => $user['role'],
                'logged_in'    => true,
                'login_time'   => time(),
                'ip_address'   => $this->request->getIPAddress(),
                'user_agent'   => $this->request->getUserAgent()->getAgentString(),
            ]);
            
            // Log successful login
            log_message('info', 'User logged in: ' . $user['email'] . ' (ID: ' . $user['id'] . ')');
            
            // Check for redirect URL
            $redirectUrl = $session->get('redirect_url');
            if ($redirectUrl) {
                $session->remove('redirect_url');
                return redirect()->to($redirectUrl);
            }

            return redirect()->to('/admin/dashboard');
        }
        
        // Log failed login attempt
        log_message('warning', 'Failed login attempt for email: ' . $email . ' from IP: ' . $this->request->getIPAddress());

        return redirect()->back()->with('error', 'Invalid email or password');
    }

    /**
     * Logout (requires POST to prevent CSRF)
     */
    public function logout()
    {
        // Require POST method to prevent CSRF attacks
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/dashboard')->with('error', 'Invalid logout request');
        }
        
        $session = session();
        
        // Log logout
        $userId = $session->get('user_id');
        if ($userId) {
            log_message('info', 'User logged out: ID ' . $userId . ' from IP: ' . $this->request->getIPAddress());
        }
        
        // Destroy session and regenerate ID
        $session->destroy();

        return redirect()->to('/admin/login')->with('success', 'You have been logged out');
    }
}
