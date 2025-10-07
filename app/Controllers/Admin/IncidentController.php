<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\IncidentModel;
use App\Models\IncidentUpdateModel;
use App\Models\StatusPageModel;
use App\Models\ComponentModel;
use App\Libraries\NotificationService;

class IncidentController extends BaseController
{
    protected $incidentModel;
    protected $updateModel;
    protected $statusPageModel;
    protected $componentModel;
    protected $notificationService;

    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }

        $this->incidentModel = new IncidentModel();
        $this->updateModel = new IncidentUpdateModel();
        $this->statusPageModel = new StatusPageModel();
        $this->componentModel = new ComponentModel();
        $this->notificationService = new NotificationService();
    }

    /**
     * List all incidents
     */
    public function index()
    {
        $statusPage = $this->statusPageModel->first();
        $incidents = $this->incidentModel->where('status_page_id', $statusPage['id'])
                                        ->orderBy('created_at', 'DESC')
                                        ->findAll();

        $data = [
            'user'       => $this->getCurrentUser(),
            'statusPage' => $statusPage,
            'incidents'  => $incidents,
        ];

        echo view('admin/incidents/index', $data);
    }

    /**
     * Create new incident
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $statusPage = $this->statusPageModel->first();

            $data = [
                'status_page_id' => $statusPage['id'],
                'title'          => $this->request->getPost('title'),
                'status'         => $this->request->getPost('status'),
                'impact'         => $this->request->getPost('impact'),
                'is_visible'     => $this->request->getPost('is_visible') ? 1 : 0,
            ];

            // Handle scheduled maintenance
            if ($data['status'] === 'scheduled') {
                $data['scheduled_start'] = $this->request->getPost('scheduled_start');
                $data['scheduled_end'] = $this->request->getPost('scheduled_end');
            }

            $incidentId = $this->incidentModel->insert($data);

            if ($incidentId) {
                // Create initial update
                $notifySubscribers = $this->request->getPost('notify_subscribers') ? 1 : 0;
                
                $this->updateModel->insert([
                    'incident_id'        => $incidentId,
                    'status'             => $data['status'],
                    'message'            => $this->request->getPost('message'),
                    'notify_subscribers' => $notifySubscribers,
                ]);

                // Link affected components
                $componentIds = $this->request->getPost('component_ids') ?? [];
                $affectedComponents = [];
                
                if (!empty($componentIds)) {
                    $db = \Config\Database::connect();
                    foreach ($componentIds as $componentId) {
                        $db->table('incident_components')->insert([
                            'incident_id'  => $incidentId,
                            'component_id' => $componentId,
                        ]);
                        
                        // Get component details for notification
                        $component = $this->componentModel->find($componentId);
                        if ($component) {
                            $affectedComponents[] = $component;
                        }
                    }
                }

                // Send notification if requested
                if ($notifySubscribers) {
                    try {
                        $incident = $this->incidentModel->find($incidentId);
                        $this->notificationService->notifyIncidentCreated($incident, $affectedComponents);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to send incident notification: ' . $e->getMessage());
                    }
                }

                return redirect()->to('/admin/incidents')->with('success', 'Incident created successfully');
            }

            return redirect()->back()->with('error', 'Failed to create incident');
        }

        $statusPage = $this->statusPageModel->first();
        $components = $this->componentModel->getByStatusPage($statusPage['id'], false);

        $data = [
            'user'       => $this->getCurrentUser(),
            'components' => $components,
        ];

        echo view('admin/incidents/create', $data);
    }

    /**
     * View incident details
     */
    public function view($id)
    {
        $incident = $this->incidentModel->getWithUpdates($id);

        if (!$incident) {
            return redirect()->to('/admin/incidents')->with('error', 'Incident not found');
        }

        $data = [
            'user'     => $this->getCurrentUser(),
            'incident' => $incident,
        ];

        echo view('admin/incidents/view', $data);
    }

    /**
     * Add update to incident
     */
    public function addUpdate($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $notifySubscribers = $this->request->getPost('notify_subscribers') ? 1 : 0;
            
            $data = [
                'incident_id'        => $id,
                'status'             => $this->request->getPost('status'),
                'message'            => $this->request->getPost('message'),
                'notify_subscribers' => $notifySubscribers,
            ];

            if ($this->updateModel->insert($data)) {
                // Send notification if requested
                if ($notifySubscribers) {
                    try {
                        $incident = $this->incidentModel->find($id);
                        $update = $this->updateModel->orderBy('created_at', 'DESC')->first();
                        
                        // Get affected components
                        $db = \Config\Database::connect();
                        $componentIds = $db->table('incident_components')
                            ->where('incident_id', $id)
                            ->get()
                            ->getResultArray();
                        
                        $affectedComponents = [];
                        foreach ($componentIds as $row) {
                            $component = $this->componentModel->find($row['component_id']);
                            if ($component) {
                                $affectedComponents[] = $component;
                            }
                        }
                        
                        $this->notificationService->notifyIncidentUpdated($incident, $update, $affectedComponents);
                    } catch (\Exception $e) {
                        log_message('error', 'Failed to send update notification: ' . $e->getMessage());
                    }
                }
                
                return redirect()->to("/admin/incidents/view/{$id}")->with('success', 'Update added successfully');
            }

            return redirect()->back()->with('error', 'Failed to add update');
        }
    }

    /**
     * Resolve incident
     */
    public function resolve($id)
    {
        if ($this->incidentModel->resolveIncident($id)) {
            // Add resolved update
            $this->updateModel->insert([
                'incident_id'        => $id,
                'status'             => 'resolved',
                'message'            => $this->request->getPost('message') ?? 'This incident has been resolved.',
                'notify_subscribers' => 1,
            ]);

            // Send resolution notification
            try {
                $incident = $this->incidentModel->find($id);
                
                // Get affected components
                $db = \Config\Database::connect();
                $componentIds = $db->table('incident_components')
                    ->where('incident_id', $id)
                    ->get()
                    ->getResultArray();
                
                $affectedComponents = [];
                foreach ($componentIds as $row) {
                    $component = $this->componentModel->find($row['component_id']);
                    if ($component) {
                        $affectedComponents[] = $component;
                    }
                }
                
                $this->notificationService->notifyIncidentResolved($incident, $affectedComponents);
            } catch (\Exception $e) {
                log_message('error', 'Failed to send resolution notification: ' . $e->getMessage());
            }

            return redirect()->to('/admin/incidents')->with('success', 'Incident resolved');
        }

        return redirect()->back()->with('error', 'Failed to resolve incident');
    }

    /**
     * Delete incident
     */
    public function delete($id)
    {
        if ($this->incidentModel->delete($id)) {
            return redirect()->to('/admin/incidents')->with('success', 'Incident deleted successfully');
        }

        return redirect()->to('/admin/incidents')->with('error', 'Failed to delete incident');
    }
}
