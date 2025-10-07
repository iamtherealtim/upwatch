<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;

/**
 * Email Provider Manager
 * Handles multiple email providers with configuration
 */
class EmailProvider
{
    protected $providers = [
        'smtp' => 'Generic SMTP',
        'gmail' => 'Gmail',
        'outlook' => 'Outlook.com',
        'office365' => 'Microsoft 365',
        'sendgrid' => 'SendGrid',
        'mailgun' => 'Mailgun',
        'ses' => 'Amazon SES',
    ];
    
    protected $settingModel;
    
    public function __construct()
    {
        $this->settingModel = new \App\Models\SettingModel();
    }
    
    /**
     * Get all available providers
     */
    public function getProviders()
    {
        return $this->providers;
    }
    
    /**
     * Get active provider
     */
    public function getActiveProvider()
    {
        return $this->settingModel->getSetting('email_provider', 'smtp');
    }
    
    /**
     * Get configured email instance
     */
    public function getEmailInstance()
    {
        $provider = $this->getActiveProvider();
        $config = $this->getProviderConfig($provider);
        
        $email = \Config\Services::email();
        $email->initialize($config);
        
        return $email;
    }
    
    /**
     * Get provider-specific configuration
     */
    private function getProviderConfig($provider)
    {
        $baseConfig = [
            'mailType'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n",
            'wordWrap'  => true,
        ];
        
        switch ($provider) {
            case 'gmail':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'smtp.gmail.com',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => $this->settingModel->getSetting('smtp_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('smtp_password'),
                ]);
                
            case 'outlook':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'smtp-mail.outlook.com',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => $this->settingModel->getSetting('smtp_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('smtp_password'),
                ]);
                
            case 'office365':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'smtp.office365.com',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => $this->settingModel->getSetting('smtp_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('smtp_password'),
                ]);
                
            case 'sendgrid':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'smtp.sendgrid.net',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => 'apikey',
                    'SMTPPass'   => $this->settingModel->getSetting('sendgrid_api_key'),
                ]);
                
            case 'mailgun':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => 'smtp.mailgun.org',
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => $this->settingModel->getSetting('mailgun_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('mailgun_password'),
                ]);
                
            case 'ses':
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => $this->settingModel->getSetting('ses_host', 'email-smtp.us-east-1.amazonaws.com'),
                    'SMTPPort'   => 587,
                    'SMTPCrypto' => 'tls',
                    'SMTPUser'   => $this->settingModel->getSetting('ses_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('ses_password'),
                ]);
                
            case 'smtp':
            default:
                return array_merge($baseConfig, [
                    'protocol'   => 'smtp',
                    'SMTPHost'   => $this->settingModel->getSetting('smtp_host'),
                    'SMTPPort'   => $this->settingModel->getSetting('smtp_port', 587),
                    'SMTPCrypto' => $this->settingModel->getSetting('smtp_encryption', 'tls'),
                    'SMTPUser'   => $this->settingModel->getSetting('smtp_username'),
                    'SMTPPass'   => $this->settingModel->getSetting('smtp_password'),
                ]);
        }
    }
    
    /**
     * Get provider-specific fields needed for configuration
     */
    public function getProviderFields($provider)
    {
        switch ($provider) {
            case 'gmail':
            case 'outlook':
            case 'office365':
                return ['username' => 'Email Address', 'password' => 'Password or App Password'];
                
            case 'sendgrid':
                return ['api_key' => 'SendGrid API Key'];
                
            case 'mailgun':
                return ['username' => 'Mailgun Username', 'password' => 'Mailgun Password'];
                
            case 'ses':
                return ['host' => 'SES SMTP Host', 'username' => 'SMTP Username', 'password' => 'SMTP Password'];
                
            case 'smtp':
            default:
                return [
                    'host' => 'SMTP Host',
                    'port' => 'SMTP Port',
                    'encryption' => 'Encryption (tls/ssl)',
                    'username' => 'SMTP Username',
                    'password' => 'SMTP Password'
                ];
        }
    }
}
