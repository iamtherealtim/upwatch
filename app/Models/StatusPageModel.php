<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusPageModel extends Model
{
    protected $table            = 'status_pages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'domain',
        'logo_url',
        'primary_color',
        'support_email',
        'timezone',
        'is_public',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|alpha_dash|is_unique[status_pages.slug,id,{id}]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (empty($data['data']['slug']) && !empty($data['data']['name'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)
                    ->where('is_active', 1)
                    ->first();
    }

    public function getOverallStatus(int $statusPageId)
    {
        $componentModel = new ComponentModel();
        $components = $componentModel->where('status_page_id', $statusPageId)
                                     ->where('is_visible', 1)
                                     ->findAll();

        if (empty($components)) {
            return 'operational';
        }

        $statuses = array_column($components, 'status');

        if (in_array('major_outage', $statuses)) {
            return 'major_outage';
        }
        if (in_array('partial_outage', $statuses)) {
            return 'partial_outage';
        }
        if (in_array('degraded', $statuses)) {
            return 'degraded';
        }
        if (in_array('maintenance', $statuses)) {
            return 'maintenance';
        }

        return 'operational';
    }
}
