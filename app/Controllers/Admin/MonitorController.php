<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MonitorModel;
use App\Models\ComponentModel;
use App\Models\MonitorResultModel;
use App\Libraries\MonitoringEngine;

class MonitorController extends BaseController
{
    protected $monitorModel;
    protected $componentModel;

    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }

        $this->monitorModel = new MonitorModel();
        $this->componentModel = new ComponentModel();
    }

    /**
     * List all monitors
     */
    public function index()
    {
        $monitors = $this->monitorModel->findAll();

        // Get component names
        foreach ($monitors as &$monitor) {
            $component = $this->componentModel->find($monitor['component_id']);
            $monitor['component_name'] = $component['name'] ?? 'Unknown';
        }

        $data = [
            'user'     => $this->getCurrentUser(),
            'monitors' => $monitors,
        ];

        return view('admin/monitors/index', $data);
    }

    /**
     * Create new monitor
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'component_id'         => $this->request->getPost('component_id'),
                'name'                 => $this->request->getPost('name'),
                'type'                 => $this->request->getPost('type'),
                'target'               => $this->request->getPost('target'),
                'interval'             => $this->request->getPost('interval') ?? 300,
                'timeout'              => $this->request->getPost('timeout') ?? 10,
                'method'               => $this->request->getPost('method') ?? 'GET',
                'expected_status_code' => $this->request->getPost('expected_status_code') ?? 200,
                'keyword_match'        => $this->request->getPost('keyword_match'),
                'retry_count'          => $this->request->getPost('retry_count') ?? 3,
                'failure_threshold'    => $this->request->getPost('failure_threshold') ?? 3,
                'is_active'            => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($this->monitorModel->insert($data)) {
                return redirect()->to('/admin/monitors')->with('success', 'Monitor created successfully');
            }

            $errors = $this->monitorModel->errors();
            return redirect()->back()->withInput()->with('error', 'Failed to create monitor: ' . implode(', ', $errors));
        }

        $statusPageModel = new \App\Models\StatusPageModel();
        $statusPage = $statusPageModel->first();
        $components = $this->componentModel->getByStatusPage($statusPage['id'], false);

        $data = [
            'user'       => $this->getCurrentUser(),
            'components' => $components,
        ];

        return view('admin/monitors/create', $data);
    }

    /**
     * Edit monitor
     */
    public function edit($id)
    {
        $monitor = $this->monitorModel->find($id);

        if (!$monitor) {
            return redirect()->to('/admin/monitors')->with('error', 'Monitor not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'component_id'         => $this->request->getPost('component_id'),
                'name'                 => $this->request->getPost('name'),
                'type'                 => $this->request->getPost('type'),
                'target'               => $this->request->getPost('target'),
                'interval'             => $this->request->getPost('interval') ?? 300,
                'timeout'              => $this->request->getPost('timeout') ?? 10,
                'method'               => $this->request->getPost('method') ?? 'GET',
                'expected_status_code' => $this->request->getPost('expected_status_code') ?? 200,
                'keyword_match'        => $this->request->getPost('keyword_match'),
                'retry_count'          => $this->request->getPost('retry_count') ?? 3,
                'failure_threshold'    => $this->request->getPost('failure_threshold') ?? 3,
                'is_active'            => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($this->monitorModel->update($id, $data)) {
                return redirect()->to('/admin/monitors')->with('success', 'Monitor updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update monitor');
        }

        $statusPageModel = new \App\Models\StatusPageModel();
        $statusPage = $statusPageModel->first();
        $components = $this->componentModel->getByStatusPage($statusPage['id'], false);

        $data = [
            'user'       => $this->getCurrentUser(),
            'monitor'    => $monitor,
            'components' => $components,
        ];

        return view('admin/monitors/edit', $data);
    }

    /**
     * Delete monitor
     */
    public function delete($id)
    {
        if ($this->monitorModel->delete($id)) {
            return redirect()->to('/admin/monitors')->with('success', 'Monitor deleted successfully');
        }

        return redirect()->to('/admin/monitors')->with('error', 'Failed to delete monitor');
    }

    /**
     * Test monitor immediately
     */
    public function test($id)
    {
        $monitor = $this->monitorModel->find($id);

        if (!$monitor) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Monitor not found'
            ]);
        }

        $engine = new MonitoringEngine();
        $result = $engine->checkMonitor($monitor);

        return $this->response->setJSON([
            'success' => true,
            'result'  => $result
        ]);
    }

    /**
     * View monitor results
     */
    public function results($id)
    {
        $monitor = $this->monitorModel->find($id);

        if (!$monitor) {
            return redirect()->to('/admin/monitors')->with('error', 'Monitor not found');
        }

        $resultModel = new MonitorResultModel();
        $results = $resultModel->getRecentResults($id, 168); // Last 7 days

        $stats = [
            'avg_response_24h' => $resultModel->getAverageResponseTime($id, 24),
            'uptime_30d'       => $resultModel->getUptimePercentage($id, 30),
            'uptime_90d'       => $resultModel->getUptimePercentage($id, 90),
        ];

        $data = [
            'user'    => $this->getCurrentUser(),
            'monitor' => $monitor,
            'results' => $results,
            'stats'   => $stats,
        ];

        return view('admin/monitors/results', $data);
    }
}
