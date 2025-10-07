<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StatusPageModel;
use App\Models\ComponentModel;
use App\Models\IncidentModel;
use App\Models\MonitorModel;
use App\Models\SubscriberModel;

class DashboardController extends BaseController
{
    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }
    }

    public function index()
    {
        $statusPageModel = new StatusPageModel();
        $componentModel = new ComponentModel();
        $incidentModel = new IncidentModel();
        $monitorModel = new MonitorModel();
        $subscriberModel = new SubscriberModel();

        // Get default status page (or first one)
        $statusPage = $statusPageModel->first();

        if (!$statusPage) {
            return view('admin/dashboard/empty');
        }

        // Get statistics
        $stats = [
            'total_components' => $componentModel->where('status_page_id', $statusPage['id'])->countAllResults(),
            'total_monitors'   => $monitorModel->countAllResults(),
            'active_incidents' => $incidentModel->where('status_page_id', $statusPage['id'])
                                                ->whereIn('status', ['investigating', 'identified', 'monitoring'])
                                                ->countAllResults(),
            'total_subscribers' => $subscriberModel->where('status_page_id', $statusPage['id'])
                                                   ->where('is_verified', 1)
                                                   ->countAllResults(),
        ];

        // Get overall status
        $overallStatus = $statusPageModel->getOverallStatus($statusPage['id']);

        // Get recent incidents
        $recentIncidents = $incidentModel->where('status_page_id', $statusPage['id'])
                                        ->orderBy('created_at', 'DESC')
                                        ->limit(5)
                                        ->findAll();

        // Get components with status
        $components = $componentModel->getByStatusPage($statusPage['id'], false);

        $data = [
            'user'            => $this->getCurrentUser(),
            'statusPage'      => $statusPage,
            'stats'           => $stats,
            'overallStatus'   => $overallStatus,
            'recentIncidents' => $recentIncidents,
            'components'      => $components,
        ];

        return view('admin/dashboard/index', $data);
    }
}
