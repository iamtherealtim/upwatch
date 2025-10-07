<?php

namespace App\Libraries;

use App\Models\SubscriberModel;
use App\Models\SettingModel;

/**
 * Notification Service
 * Handles all email notifications for incidents and subscriber management
 */
class NotificationService
{
    protected $emailProvider;
    protected $subscriberModel;
    protected $settingModel;
    
    public function __construct()
    {
        $this->emailProvider = new EmailProvider();
        $this->subscriberModel = new SubscriberModel();
        $this->settingModel = new SettingModel();
    }
    
    /**
     * Send notification for new incident
     */
    public function notifyIncidentCreated($incident, $components = [])
    {
        if (!$this->areNotificationsEnabled()) {
            return false;
        }
        
        $subscribers = $this->getVerifiedSubscribers();
        if (empty($subscribers)) {
            return true;
        }
        
        $email = $this->emailProvider->getEmailInstance();
        $fromEmail = $this->settingModel->getSetting('from_email', 'noreply@upwatch.local');
        $fromName = $this->settingModel->getSetting('site_name', 'UpWatch');
        
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject("[{$incident->severity}] New Incident: {$incident->title}");
        
        $componentNames = implode(', ', array_column($components, 'name'));
        
        $message = view('emails/incident_created', [
            'incident' => $incident,
            'components' => $componentNames,
            'site_name' => $fromName,
        ]);
        
        $email->setMessage($message);
        
        $success = true;
        foreach ($subscribers as $subscriber) {
            $email->setTo($subscriber->email);
            if (!$email->send()) {
                log_message('error', 'Failed to send incident notification to: ' . $subscriber->email);
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send notification for incident update
     */
    public function notifyIncidentUpdated($incident, $update, $components = [])
    {
        if (!$this->areNotificationsEnabled()) {
            return false;
        }
        
        $subscribers = $this->getVerifiedSubscribers();
        if (empty($subscribers)) {
            return true;
        }
        
        $email = $this->emailProvider->getEmailInstance();
        $fromEmail = $this->settingModel->getSetting('from_email', 'noreply@upwatch.local');
        $fromName = $this->settingModel->getSetting('site_name', 'UpWatch');
        
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject("[Update] {$incident->title}");
        
        $componentNames = implode(', ', array_column($components, 'name'));
        
        $message = view('emails/incident_updated', [
            'incident' => $incident,
            'update' => $update,
            'components' => $componentNames,
            'site_name' => $fromName,
        ]);
        
        $email->setMessage($message);
        
        $success = true;
        foreach ($subscribers as $subscriber) {
            $email->setTo($subscriber->email);
            if (!$email->send()) {
                log_message('error', 'Failed to send incident update to: ' . $subscriber->email);
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send notification for resolved incident
     */
    public function notifyIncidentResolved($incident, $components = [])
    {
        if (!$this->areNotificationsEnabled()) {
            return false;
        }
        
        $subscribers = $this->getVerifiedSubscribers();
        if (empty($subscribers)) {
            return true;
        }
        
        $email = $this->emailProvider->getEmailInstance();
        $fromEmail = $this->settingModel->getSetting('from_email', 'noreply@upwatch.local');
        $fromName = $this->settingModel->getSetting('site_name', 'UpWatch');
        
        $email->setFrom($fromEmail, $fromName);
        $email->setSubject("[Resolved] {$incident->title}");
        
        $componentNames = implode(', ', array_column($components, 'name'));
        
        $message = view('emails/incident_resolved', [
            'incident' => $incident,
            'components' => $componentNames,
            'site_name' => $fromName,
        ]);
        
        $email->setMessage($message);
        
        $success = true;
        foreach ($subscribers as $subscriber) {
            $email->setTo($subscriber->email);
            if (!$email->send()) {
                log_message('error', 'Failed to send resolution notification to: ' . $subscriber->email);
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send verification email to new subscriber
     */
    public function sendVerificationEmail($subscriber)
    {
        $email = $this->emailProvider->getEmailInstance();
        $fromEmail = $this->settingModel->getSetting('from_email', 'noreply@upwatch.local');
        $fromName = $this->settingModel->getSetting('site_name', 'UpWatch');
        
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($subscriber->email);
        $email->setSubject("Please verify your subscription to {$fromName}");
        
        $verifyUrl = site_url("subscribe/verify/{$subscriber->verification_token}");
        $unsubscribeUrl = site_url("subscribe/unsubscribe/{$subscriber->unsubscribe_token}");
        
        $message = view('emails/verify_subscription', [
            'verify_url' => $verifyUrl,
            'unsubscribe_url' => $unsubscribeUrl,
            'site_name' => $fromName,
        ]);
        
        $email->setMessage($message);
        
        if (!$email->send()) {
            log_message('error', 'Failed to send verification email to: ' . $subscriber->email);
            return false;
        }
        
        return true;
    }
    
    /**
     * Send test email
     */
    public function sendTestEmail($toEmail)
    {
        $email = $this->emailProvider->getEmailInstance();
        $fromEmail = $this->settingModel->getSetting('from_email', 'noreply@upwatch.local');
        $fromName = $this->settingModel->getSetting('site_name', 'UpWatch');
        
        $email->setFrom($fromEmail, $fromName);
        $email->setTo($toEmail);
        $email->setSubject('Test Email from ' . $fromName);
        
        $message = view('emails/test', [
            'site_name' => $fromName,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
        
        $email->setMessage($message);
        
        if (!$email->send()) {
            log_message('error', 'Failed to send test email to: ' . $toEmail);
            log_message('error', 'Email debug: ' . $email->printDebugger());
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if notifications are enabled
     */
    private function areNotificationsEnabled()
    {
        return $this->settingModel->getSetting('notifications_enabled', '1') === '1';
    }
    
    /**
     * Get all verified subscribers
     */
    private function getVerifiedSubscribers()
    {
        return $this->subscriberModel
            ->where('is_verified', 1)
            ->where('is_active', 1)
            ->findAll();
    }
}
