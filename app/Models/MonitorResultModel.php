<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitorResultModel extends Model
{
    protected $table            = 'monitor_results';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'monitor_id',
        'status',
        'response_time',
        'status_code',
        'error_message',
        'checked_at',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    // Validation
    protected $validationRules      = [
        'monitor_id' => 'required|integer',
        'status'     => 'in_list[up,down]',
        'checked_at' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getRecentResults(int $monitorId, int $hours = 24)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        return $this->where('monitor_id', $monitorId)
                    ->where('checked_at >=', $since)
                    ->orderBy('checked_at', 'ASC')
                    ->findAll();
    }

    public function getAverageResponseTime(int $monitorId, int $hours = 24)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
        
        $result = $this->selectAvg('response_time', 'avg_response')
                       ->where('monitor_id', $monitorId)
                       ->where('checked_at >=', $since)
                       ->where('status', 'up')
                       ->first();
        
        return $result ? round($result['avg_response']) : null;
    }

    public function getUptimePercentage(int $monitorId, int $days = 30)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $total = $this->where('monitor_id', $monitorId)
                      ->where('checked_at >=', $since)
                      ->countAllResults();
        
        if ($total === 0) {
            return 100;
        }
        
        $up = $this->where('monitor_id', $monitorId)
                   ->where('checked_at >=', $since)
                   ->where('status', 'up')
                   ->countAllResults();
        
        return round(($up / $total) * 100, 2);
    }

    public function cleanupOldData(int $daysToKeep = 90)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        return $this->where('checked_at <', $date)->delete();
    }
}
