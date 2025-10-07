<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriberModel extends Model
{
    protected $table            = 'subscribers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status_page_id',
        'email',
        'verification_token',
        'is_verified',
        'verified_at',
        'unsubscribe_token',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'status_page_id' => 'required|integer',
        'email'          => 'required|valid_email',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['generateTokens'];

    protected function generateTokens(array $data)
    {
        $data['data']['verification_token'] = bin2hex(random_bytes(32));
        $data['data']['unsubscribe_token'] = bin2hex(random_bytes(32));
        return $data;
    }

    public function getVerifiedSubscribers(int $statusPageId)
    {
        return $this->where('status_page_id', $statusPageId)
                    ->where('is_verified', 1)
                    ->where('is_active', 1)
                    ->findAll();
    }

    public function verifyByToken(string $token)
    {
        $subscriber = $this->where('verification_token', $token)->first();
        
        if ($subscriber) {
            return $this->update($subscriber['id'], [
                'is_verified'        => 1,
                'verified_at'        => date('Y-m-d H:i:s'),
                'verification_token' => null,
            ]);
        }
        
        return false;
    }

    public function unsubscribeByToken(string $token)
    {
        $subscriber = $this->where('unsubscribe_token', $token)->first();
        
        if ($subscriber) {
            return $this->update($subscriber['id'], [
                'is_active' => 0,
            ]);
        }
        
        return false;
    }
}
