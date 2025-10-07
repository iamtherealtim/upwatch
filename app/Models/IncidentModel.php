<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentModel extends Model
{
    protected $table            = 'incidents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status_page_id',
        'title',
        'status',
        'impact',
        'scheduled_start',
        'scheduled_end',
        'resolved_at',
        'is_visible',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'status_page_id' => 'required|integer',
        'title'          => 'required|min_length[3]|max_length[255]',
        'status'         => 'in_list[investigating,identified,monitoring,resolved,scheduled]',
        'impact'         => 'in_list[none,minor,major,critical]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getByStatusPage(int $statusPageId, int $limit = 10)
    {
        return $this->where('status_page_id', $statusPageId)
                    ->where('is_visible', 1)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getActiveIncidents(int $statusPageId)
    {
        return $this->where('status_page_id', $statusPageId)
                    ->where('is_visible', 1)
                    ->whereIn('status', ['investigating', 'identified', 'monitoring'])
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getScheduledMaintenance(int $statusPageId)
    {
        return $this->where('status_page_id', $statusPageId)
                    ->where('is_visible', 1)
                    ->where('status', 'scheduled')
                    ->where('scheduled_start >=', date('Y-m-d H:i:s'))
                    ->orderBy('scheduled_start', 'ASC')
                    ->findAll();
    }

    public function getWithUpdates(int $id)
    {
        $incident = $this->find($id);
        
        if ($incident) {
            $updateModel = new IncidentUpdateModel();
            $incident['updates'] = $updateModel->where('incident_id', $id)
                                               ->orderBy('created_at', 'DESC')
                                               ->findAll();
            
            // Get affected components
            $db = \Config\Database::connect();
            $builder = $db->table('incident_components ic');
            $builder->select('c.*');
            $builder->join('components c', 'c.id = ic.component_id');
            $builder->where('ic.incident_id', $id);
            $incident['components'] = $builder->get()->getResultArray();
        }
        
        return $incident;
    }

    public function resolveIncident(int $id)
    {
        return $this->update($id, [
            'status'      => 'resolved',
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
