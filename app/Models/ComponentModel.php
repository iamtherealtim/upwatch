<?php

namespace App\Models;

use CodeIgniter\Model;

class ComponentModel extends Model
{
    protected $table            = 'components';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status_page_id',
        'name',
        'description',
        'status',
        'order',
        'group_name',
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
        'name'           => 'required|min_length[3]|max_length[255]',
        'status'         => 'in_list[operational,degraded,partial_outage,major_outage,maintenance]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getByStatusPage(int $statusPageId, bool $visibleOnly = true)
    {
        $builder = $this->where('status_page_id', $statusPageId);
        
        if ($visibleOnly) {
            $builder->where('is_visible', 1);
        }
        
        return $builder->orderBy('order', 'ASC')
                       ->orderBy('name', 'ASC')
                       ->findAll();
    }

    public function getGroupedByStatusPage(int $statusPageId)
    {
        $components = $this->getByStatusPage($statusPageId);
        
        $grouped = [];
        foreach ($components as $component) {
            $group = $component['group_name'] ?? 'Services';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $component;
        }
        
        return $grouped;
    }

    public function updateStatus(int $id, string $status)
    {
        return $this->update($id, [
            'status' => $status,
        ]);
    }
}
