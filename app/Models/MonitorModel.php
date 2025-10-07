<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitorModel extends Model
{
    protected $table            = 'monitors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'component_id',
        'name',
        'type',
        'target',
        'interval',
        'timeout',
        'method',
        'expected_status_code',
        'keyword_match',
        'retry_count',
        'failure_threshold',
        'consecutive_failures',
        'last_check_at',
        'last_status',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'component_id'         => 'required|integer',
        'name'                 => 'required|min_length[3]|max_length[255]',
        'type'                 => 'in_list[http,https,tcp,ping,ssl]',
        'target'               => 'required|max_length[500]|valid_url',
        'interval'             => 'required|integer|greater_than[0]',
        'timeout'              => 'required|integer|greater_than[0]',
        'expected_status_code' => 'integer',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getMonitorsDueForCheck()
    {
        $sql = "(last_check_at IS NULL OR 
                TIMESTAMPDIFF(SECOND, last_check_at, NOW()) >= `interval`)";
        
        return $this->where('is_active', 1)
                    ->where($sql, null, false)
                    ->findAll();
    }

    public function getByComponent(int $componentId)
    {
        return $this->where('component_id', $componentId)->findAll();
    }

    public function recordCheck(int $id, bool $success, ?int $responseTime = null, ?int $statusCode = null, ?string $errorMessage = null)
    {
        $monitor = $this->find($id);
        
        if (!$monitor) {
            return false;
        }

        $consecutiveFailures = $success ? 0 : ($monitor['consecutive_failures'] + 1);
        $lastStatus = $success ? 'up' : 'down';

        // Update monitor
        $this->update($id, [
            'last_check_at'         => date('Y-m-d H:i:s'),
            'last_status'           => $lastStatus,
            'consecutive_failures'  => $consecutiveFailures,
        ]);

        // Record result
        $resultModel = new MonitorResultModel();
        $resultModel->insert([
            'monitor_id'    => $id,
            'status'        => $lastStatus,
            'response_time' => $responseTime,
            'status_code'   => $statusCode,
            'error_message' => $errorMessage,
            'checked_at'    => date('Y-m-d H:i:s'),
        ]);

        // Update component status if threshold reached
        if ($consecutiveFailures >= $monitor['failure_threshold']) {
            $componentModel = new ComponentModel();
            $componentModel->updateStatus($monitor['component_id'], 'major_outage');
        } elseif ($consecutiveFailures === 0 && $monitor['last_status'] === 'down') {
            // Component is back up
            $componentModel = new ComponentModel();
            $componentModel->updateStatus($monitor['component_id'], 'operational');
        }

        return true;
    }
}
