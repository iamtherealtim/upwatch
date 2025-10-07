<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SubscriberModel;
use App\Models\StatusPageModel;
use App\Libraries\NotificationService;

class SubscriberController extends BaseController
{
    protected $subscriberModel;
    protected $statusPageModel;
    protected $notificationService;

    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }

        $this->subscriberModel = new SubscriberModel();
        $this->statusPageModel = new StatusPageModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * List all subscribers
     */
    public function index()
    {
        $statusPage = $this->statusPageModel->first();
        $subscribers = $this->subscriberModel
            ->where('status_page_id', $statusPage['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Calculate stats
        $stats = [
            'total'      => count($subscribers),
            'verified'   => count(array_filter($subscribers, fn($s) => $s['is_verified'] == 1)),
            'active'     => count(array_filter($subscribers, fn($s) => $s['is_active'] == 1)),
            'unverified' => count(array_filter($subscribers, fn($s) => $s['is_verified'] == 0)),
        ];

        $data = [
            'user'        => $this->getCurrentUser(),
            'subscribers' => $subscribers,
            'stats'       => $stats,
        ];

        echo view('admin/subscribers/index', $data);
    }

    /**
     * Create new subscriber (manual)
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = $this->request->getPost('email');

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Invalid email address');
            }

            // Check if already exists
            $existing = $this->subscriberModel->where('email', $email)->first();
            if ($existing) {
                return redirect()->back()->with('error', 'Subscriber already exists');
            }

            $statusPage = $this->statusPageModel->first();
            
            $data = [
                'status_page_id'      => $statusPage['id'],
                'email'               => $email,
                'verification_token'  => bin2hex(random_bytes(32)),
                'unsubscribe_token'   => bin2hex(random_bytes(32)),
                'is_verified'         => $this->request->getPost('auto_verify') ? 1 : 0,
                'verified_at'         => $this->request->getPost('auto_verify') ? date('Y-m-d H:i:s') : null,
                'is_active'           => 1,
            ];

            $subscriberId = $this->subscriberModel->insert($data);

            if ($subscriberId) {
                // Send verification email if not auto-verified
                if (!$this->request->getPost('auto_verify')) {
                    try {
                        $subscriber = $this->subscriberModel->find($subscriberId);
                        $this->notificationService->sendVerificationEmail($subscriber);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to send verification email: ' . $e->getMessage());
                    }
                }

                return redirect()->to('/admin/subscribers')->with('success', 'Subscriber added successfully');
            }

            return redirect()->back()->with('error', 'Failed to add subscriber');
        }

        $data = [
            'user' => $this->getCurrentUser(),
        ];

        echo view('admin/subscribers/create', $data);
    }

    /**
     * Resend verification email
     */
    public function resendVerification($id)
    {
        $subscriber = $this->subscriberModel->find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'Subscriber not found');
        }

        if ($subscriber['is_verified']) {
            return redirect()->back()->with('info', 'Subscriber is already verified');
        }

        try {
            $this->notificationService->sendVerificationEmail($subscriber);
            return redirect()->back()->with('success', 'Verification email sent');
        } catch (\Exception $e) {
            log_message('error', 'Failed to resend verification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send verification email');
        }
    }

    /**
     * Manually verify subscriber
     */
    public function verify($id)
    {
        $subscriber = $this->subscriberModel->find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'Subscriber not found');
        }

        if ($subscriber['is_verified']) {
            return redirect()->back()->with('info', 'Subscriber is already verified');
        }

        $this->subscriberModel->update($id, [
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Subscriber verified');
    }

    /**
     * Toggle subscriber status
     */
    public function toggleStatus($id)
    {
        $subscriber = $this->subscriberModel->find($id);

        if (!$subscriber) {
            return redirect()->back()->with('error', 'Subscriber not found');
        }

        $newStatus = $subscriber['is_active'] ? 0 : 1;
        $this->subscriberModel->update($id, ['is_active' => $newStatus]);

        $message = $newStatus ? 'Subscriber activated' : 'Subscriber deactivated';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete subscriber
     */
    public function delete($id)
    {
        if ($this->subscriberModel->delete($id)) {
            return redirect()->to('/admin/subscribers')->with('success', 'Subscriber deleted');
        }

        return redirect()->back()->with('error', 'Failed to delete subscriber');
    }

    /**
     * Export subscribers as CSV
     */
    public function export()
    {
        $statusPage = $this->statusPageModel->first();
        $subscribers = $this->subscriberModel
            ->where('status_page_id', $statusPage['id'])
            ->findAll();

        $filename = 'subscribers_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Email', 'Status', 'Verified', 'Verified At', 'Created At']);

        // CSV data
        foreach ($subscribers as $subscriber) {
            fputcsv($output, [
                $subscriber['email'],
                $subscriber['is_active'] ? 'Active' : 'Inactive',
                $subscriber['is_verified'] ? 'Yes' : 'No',
                $subscriber['verified_at'] ?? 'N/A',
                $subscriber['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}
