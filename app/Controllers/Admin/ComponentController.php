<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComponentModel;
use App\Models\StatusPageModel;

class ComponentController extends BaseController
{
    protected $componentModel;
    protected $statusPageModel;

    public function __construct()
    {
        if (!$this->isLoggedIn()) {
            redirect()->to('/admin/login')->with('error', 'Please login to continue')->send();
            exit;
        }

        $this->componentModel = new ComponentModel();
        $this->statusPageModel = new StatusPageModel();
    }

    /**
     * List all components
     */
    public function index()
    {
        $statusPage = $this->statusPageModel->first();
        $components = $this->componentModel->getByStatusPage($statusPage['id'], false);

        $data = [
            'user'       => $this->getCurrentUser(),
            'statusPage' => $statusPage,
            'components' => $components,
        ];

        return view('admin/components/index', $data);
    }

    /**
     * Create new component
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $statusPage = $this->statusPageModel->first();

            $data = [
                'status_page_id' => $statusPage['id'],
                'name'           => $this->request->getPost('name'),
                'description'    => $this->request->getPost('description'),
                'status'         => $this->request->getPost('status'),
                'group_name'     => $this->request->getPost('group_name'),
                'order'          => $this->request->getPost('order') ?? 0,
                'is_visible'     => $this->request->getPost('is_visible') ? 1 : 0,
            ];

            if ($this->componentModel->insert($data)) {
                return redirect()->to('/admin/components')->with('success', 'Component created successfully');
            }

            return redirect()->back()->with('error', 'Failed to create component');
        }

        $data = [
            'user' => $this->getCurrentUser(),
        ];

        return view('admin/components/create', $data);
    }

    /**
     * Edit component
     */
    public function edit($id)
    {
        $component = $this->componentModel->find($id);

        if (!$component) {
            return redirect()->to('/admin/components')->with('error', 'Component not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name'        => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'status'      => $this->request->getPost('status'),
                'group_name'  => $this->request->getPost('group_name'),
                'order'       => $this->request->getPost('order') ?? 0,
                'is_visible'  => $this->request->getPost('is_visible') ? 1 : 0,
            ];

            if ($this->componentModel->update($id, $data)) {
                return redirect()->to('/admin/components')->with('success', 'Component updated successfully');
            }

            return redirect()->back()->with('error', 'Failed to update component');
        }

        $data = [
            'user'      => $this->getCurrentUser(),
            'component' => $component,
        ];

        return view('admin/components/edit', $data);
    }

    /**
     * Delete component
     */
    public function delete($id)
    {
        if ($this->componentModel->delete($id)) {
            return redirect()->to('/admin/components')->with('success', 'Component deleted successfully');
        }

        return redirect()->to('/admin/components')->with('error', 'Failed to delete component');
    }

    /**
     * Update component status (HTMX endpoint)
     */
    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');

        if ($this->componentModel->updateStatus($id, $status)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }
}
