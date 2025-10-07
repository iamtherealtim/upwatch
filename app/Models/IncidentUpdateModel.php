<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentUpdateModel extends Model
{
    protected $table            = 'incident_updates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'incident_id',
        'status',
        'message',
        'notify_subscribers',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'incident_id' => 'required|integer',
        'message'     => 'required|min_length[3]',
        'status'      => 'in_list[investigating,identified,monitoring,resolved,scheduled,in_progress,completed]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $afterInsert = ['updateIncidentStatus', 'sendNotifications'];

    protected function updateIncidentStatus(array $data)
    {
        if (isset($data['id']) && isset($data['data']['status'])) {
            $update = $this->find($data['id']);
            if ($update) {
                $incidentModel = new IncidentModel();
                $incidentModel->update($update['incident_id'], [
                    'status' => $data['data']['status'],
                ]);
            }
        }
        return $data;
    }

    protected function sendNotifications(array $data)
    {
        if (isset($data['id']) && isset($data['data']['notify_subscribers']) && $data['data']['notify_subscribers']) {
            // TODO: Implement notification sending via event or queue
            // Events::trigger('incident_update', $data['id']);
        }
        return $data;
    }
}
