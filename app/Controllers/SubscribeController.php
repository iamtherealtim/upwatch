<?php

namespace App\Controllers;

use App\Models\SubscriberModel;
use App\Models\StatusPageModel;
use App\Libraries\NotificationService;

class SubscribeController extends BaseController
{
    protected $subscriberModel;
    protected $statusPageModel;
    protected $notificationService;

    public function __construct()
    {
        $this->subscriberModel = new SubscriberModel();
        $this->statusPageModel = new StatusPageModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * Subscribe to notifications
     */
    public function subscribe()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/');
        }

        // Rate limiting - max 5 subscriptions per hour per IP
        $cache = \Config\Services::cache();
        $ipAddress = $this->request->getIPAddress();
        $rateLimitKey = 'subscribe_rate_' . md5($ipAddress);
        $subscriptionCount = $cache->get($rateLimitKey) ?? 0;

        if ($subscriptionCount >= 5) {
            log_message('warning', 'Subscription rate limit exceeded for IP: ' . $ipAddress);
            return redirect()->back()->with('error', 'Too many subscription attempts. Please try again later.');
        }

        $email = $this->request->getPost('email');

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email address');
        }

        // Check if already subscribed
        $existing = $this->subscriberModel->where('email', $email)->first();
        
        if ($existing) {
            if ($existing['is_verified']) {
                return redirect()->back()->with('info', 'This email is already subscribed');
            } else {
                // Resend verification
                try {
                    $this->notificationService->sendVerificationEmail($existing);
                    return redirect()->back()->with('success', 'Verification email resent. Please check your inbox.');
                } catch (\Exception $e) {
                    log_message('error', 'Failed to resend verification: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to send verification email');
                }
            }
        }

        // Create new subscriber
        $statusPage = $this->statusPageModel->first();
        
        $data = [
            'status_page_id'      => $statusPage['id'],
            'email'               => $email,
            'verification_token'  => bin2hex(random_bytes(32)),
            'unsubscribe_token'   => bin2hex(random_bytes(32)),
            'is_verified'         => 0,
            'is_active'           => 1,
        ];

        $subscriberId = $this->subscriberModel->insert($data);

        if ($subscriberId) {
            // Increment rate limit counter (1 hour expiration)
            $cache->save($rateLimitKey, $subscriptionCount + 1, 3600);
            
            // Send verification email
            try {
                $subscriber = $this->subscriberModel->find($subscriberId);
                $this->notificationService->sendVerificationEmail($subscriber);
                
                return redirect()->back()->with('success', 'Please check your email to verify your subscription');
            } catch (\Exception $e) {
                log_message('error', 'Failed to send verification email: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Subscription created but verification email failed to send');
            }
        }

        return redirect()->back()->with('error', 'Failed to subscribe. Please try again.');
    }

    /**
     * Verify subscription
     */
    public function verify($token)
    {
        $subscriber = $this->subscriberModel->where('verification_token', $token)->first();

        if (!$subscriber) {
            return redirect()->to('/')->with('error', 'Invalid verification link');
        }

        if ($subscriber['is_verified']) {
            return redirect()->to('/')->with('info', 'Email already verified');
        }

        // Verify subscriber
        $this->subscriberModel->update($subscriber['id'], [
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/')->with('success', 'Email verified! You will now receive status updates.');
    }

    /**
     * Unsubscribe from notifications
     */
    public function unsubscribe($token)
    {
        $subscriber = $this->subscriberModel->where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return redirect()->to('/')->with('error', 'Invalid unsubscribe link');
        }

        // Deactivate subscriber
        $this->subscriberModel->update($subscriber['id'], [
            'is_active' => 0,
        ]);

        $data = [
            'message' => 'You have been unsubscribed from status updates.',
            'email'   => $subscriber['email'],
        ];

        echo view('subscribe/unsubscribed', $data);
    }

    /**
     * Manage subscription (future feature)
     */
    public function manage()
    {
        // This could be expanded to allow subscribers to manage their preferences
        return redirect()->to('/')->with('info', 'Subscription management coming soon');
    }
}
