<?php

namespace App\Controllers;

use App\Models\StatusPageModel;
use App\Models\ComponentModel;
use App\Models\IncidentModel;
use App\Models\MonitorModel;
use App\Models\MonitorResultModel;
use App\Models\SubscriberModel;

class StatusController extends BaseController
{
    protected $statusPageModel;
    protected $componentModel;
    protected $incidentModel;

    public function __construct()
    {
        $this->statusPageModel = new StatusPageModel();
        $this->componentModel = new ComponentModel();
        $this->incidentModel = new IncidentModel();
    }

    /**
     * Display status page (public)
     */
    public function index($slug = 'default')
    {
        $statusPage = $this->statusPageModel->getBySlug($slug);

        if (!$statusPage) {
            return $this->response->setStatusCode(404)->setBody('Status page not found');
        }

        // Get overall status
        $overallStatus = $this->statusPageModel->getOverallStatus($statusPage['id']);

        // Get components grouped
        $components = $this->componentModel->getGroupedByStatusPage($statusPage['id']);
        
        // Default period for initial load
        $period = 90;
        
        // Add uptime data for each component
        $monitorModel = new MonitorModel();
        $resultModel = new MonitorResultModel();
        
        foreach ($components as $groupName => &$groupComponents) {
            foreach ($groupComponents as &$component) {
                // Get monitors for this component
                $monitors = $monitorModel->where('component_id', $component['id'])
                    ->where('is_active', 1)
                    ->findAll();
                
                if (!empty($monitors)) {
                    $monitor = $monitors[0]; // Use first monitor for uptime calculation
                    
                    // Get uptime percentage for the period
                    $component['uptime_percentage'] = $resultModel->getUptimePercentage($monitor['id'], $period);
                    
                    // Get uptime data with fixed 90 bars
                    $component['uptime_data'] = $this->getDailyUptimeData($monitor['id'], $period);
                }
            }
        }

        // Get active incidents
        $activeIncidents = $this->incidentModel->getActiveIncidents($statusPage['id']);

        // Get recent incidents (last 30 days)
        $recentIncidents = $this->incidentModel->where('status_page_id', $statusPage['id'])
            ->where('is_visible', 1)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get scheduled maintenance
        $scheduledMaintenance = $this->incidentModel->getScheduledMaintenance($statusPage['id']);

        $data = [
            'statusPage'           => $statusPage,
            'overallStatus'        => $overallStatus,
            'components'           => $components,
            'activeIncidents'      => $activeIncidents,
            'recentIncidents'      => $recentIncidents,
            'scheduledMaintenance' => $scheduledMaintenance,
        ];

        return view('status/index', $data);
    }
    
    /**
     * Get uptime data for visualization with fixed 60 bars
     * Each bar represents different time windows based on period
     */
    private function getDailyUptimeData($monitorId, $period = 90)
    {
        $resultModel = new MonitorResultModel();
        $db = \Config\Database::connect();
        
        $uptimeData = [];
        $barsCount = 60; // Fixed number of bars
        
        // Calculate time window for each bar based on period
        $timeWindows = [
            6 => ['hours' => 6, 'minutes_per_bar' => 6],           // 6 hours / 60 = 6 minutes per bar
            12 => ['hours' => 12, 'minutes_per_bar' => 12],        // 12 hours / 60 = 12 minutes per bar
            24 => ['hours' => 24, 'minutes_per_bar' => 24],        // 24 hours / 60 = 24 minutes per bar
            2 => ['hours' => 48, 'minutes_per_bar' => 48],         // 2 days (48h) / 60 = 48 minutes per bar
            5 => ['hours' => 120, 'minutes_per_bar' => 120],       // 5 days (120h) / 60 = 2 hours per bar
            7 => ['hours' => 168, 'minutes_per_bar' => 168],       // 7 days (168h) / 60 = 2.8 hours per bar
            30 => ['hours' => 720, 'minutes_per_bar' => 720],      // 30 days (720h) / 60 = 12 hours per bar
            90 => ['hours' => 2160, 'minutes_per_bar' => 2160],    // 90 days (2160h) / 60 = 36 hours per bar
        ];
        
        // Default to 90 days if period not found
        if (!isset($timeWindows[$period])) {
            $period = 90;
        }
        
        $minutesPerBar = $timeWindows[$period]['minutes_per_bar'];
        $totalMinutes = $timeWindows[$period]['hours'] * 60;
        
        // Start from current time and go backwards
        $endTime = time();
        
        for ($i = $barsCount - 1; $i >= 0; $i--) {
            // Calculate time range for this bar
            $barEndTime = $endTime - ($i * $minutesPerBar * 60);
            $barStartTime = $barEndTime - ($minutesPerBar * 60);
            
            $startDate = date('Y-m-d H:i:s', $barStartTime);
            $endDate = date('Y-m-d H:i:s', $barEndTime);
            
            // Get all checks within this time window
            $query = $db->table('monitor_results')
                ->where('monitor_id', $monitorId)
                ->where('checked_at >=', $startDate)
                ->where('checked_at <', $endDate)
                ->get();
            
            $results = $query->getResultArray();
            
            if (empty($results)) {
                // No data for this time window
                $uptimeData[] = [
                    'date' => $this->formatBarLabel($barStartTime, $period),
                    'uptime' => 100,
                    'status_color' => 'bg-gray-700',
                ];
                continue;
            }
            
            // Calculate uptime percentage for this time window
            $totalChecks = count($results);
            $successfulChecks = 0;
            
            foreach ($results as $result) {
                if ($result['status'] === 'up') {
                    $successfulChecks++;
                }
            }
            
            $uptimePercent = ($successfulChecks / $totalChecks) * 100;
            
            // Determine color based on uptime (dark theme colors)
            $statusColor = match(true) {
                $uptimePercent >= 99.5 => 'bg-green-600',
                $uptimePercent >= 95 => 'bg-yellow-600',
                $uptimePercent >= 80 => 'bg-orange-600',
                default => 'bg-red-600'
            };
            
            $uptimeData[] = [
                'date' => $this->formatBarLabel($barStartTime, $period),
                'uptime' => round($uptimePercent, 1),
                'status_color' => $statusColor,
            ];
        }
        
        return $uptimeData;
    }
    
    /**
     * Format the label for each bar based on the time period
     */
    private function formatBarLabel($timestamp, $period)
    {
        switch ($period) {
            case 6:
            case 12:
            case 24:
                // Short periods: show time (e.g., "14:32")
                return date('H:i', $timestamp);
            case 2:
            case 5:
            case 7:
                // Medium periods: show day and time (e.g., "Mon 14:00")
                return date('D H:i', $timestamp);
            case 30:
                // 30 days: show date and time (e.g., "Oct 1 14:00")
                return date('M j H:i', $timestamp);
            case 90:
                // 90 days: show date (e.g., "Oct 1")
                return date('M j', $timestamp);
            default:
                return date('M j', $timestamp);
        }
    }

    /**
     * Subscribe to updates
     */
    public function subscribe($slug = 'default')
    {
        if ($this->request->getMethod() === 'POST') {
            $statusPage = $this->statusPageModel->getBySlug($slug);

            if (!$statusPage) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status page not found'
                ]);
            }

            $subscriberModel = new SubscriberModel();
            
            $data = [
                'status_page_id' => $statusPage['id'],
                'email'          => $this->request->getPost('email'),
            ];

            if ($subscriberModel->insert($data)) {
                // TODO: Send verification email
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Please check your email to confirm your subscription.'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again.'
            ]);
        }

        return view('status/subscribe');
    }

    /**
     * Verify subscription
     */
    public function verify($token)
    {
        $subscriberModel = new SubscriberModel();
        
        if ($subscriberModel->verifyByToken($token)) {
            return view('status/verified', ['success' => true]);
        }

        return view('status/verified', ['success' => false]);
    }

    /**
     * Unsubscribe from updates
     */
    public function unsubscribe($token)
    {
        $subscriberModel = new SubscriberModel();
        
        if ($subscriberModel->unsubscribeByToken($token)) {
            return view('status/unsubscribed', ['success' => true]);
        }

        return view('status/unsubscribed', ['success' => false]);
    }

    /**
     * Get live status updates (for HTMX)
     */
    public function liveStatus($slug = 'default')
    {
        $statusPage = $this->statusPageModel->getBySlug($slug);

        if (!$statusPage) {
            return $this->response->setStatusCode(404);
        }
        
        // Get period from request (default 90 days)
        $period = (int) ($this->request->getPost('period') ?? $this->request->getGet('period') ?? 90);
        
        // Validate period
        if (!in_array($period, [6, 12, 24, 2, 5, 7, 30, 90])) {
            $period = 90;
        }

        $overallStatus = $this->statusPageModel->getOverallStatus($statusPage['id']);
        $components = $this->componentModel->getGroupedByStatusPage($statusPage['id']);
        
        // Add uptime data for each component
        $monitorModel = new MonitorModel();
        $resultModel = new MonitorResultModel();
        
        foreach ($components as $groupName => &$groupComponents) {
            foreach ($groupComponents as &$component) {
                // Get monitors for this component
                $monitors = $monitorModel->where('component_id', $component['id'])
                    ->where('is_active', 1)
                    ->findAll();
                
                if (!empty($monitors)) {
                    $monitor = $monitors[0];
                    $component['uptime_percentage'] = $resultModel->getUptimePercentage($monitor['id'], $period);
                    $component['uptime_data'] = $this->getDailyUptimeData($monitor['id'], $period);
                }
            }
        }

        return view('status/partials/components', [
            'overallStatus' => $overallStatus,
            'components'    => $components,
            'period'        => $period,
        ]);
    }

    /**
     * Get component uptime data (for charts)
     */
    public function uptimeData($componentId, $period = '24h')
    {
        $monitorModel = new MonitorModel();
        $resultModel = new MonitorResultModel();

        $monitors = $monitorModel->getByComponent($componentId);

        if (empty($monitors)) {
            return $this->response->setJSON([]);
        }

        // Get data for first monitor (can be enhanced to aggregate multiple monitors)
        $monitor = $monitors[0];

        $hours = match($period) {
            '24h' => 24,
            '7d'  => 168,
            '30d' => 720,
            default => 24,
        };

        $results = $resultModel->getRecentResults($monitor['id'], $hours);

        // Format data for Chart.js
        $chartData = [
            'labels' => [],
            'data'   => [],
        ];

        foreach ($results as $result) {
            $chartData['labels'][] = date('H:i', strtotime($result['checked_at']));
            $chartData['data'][] = $result['response_time'] ?? 0;
        }

        return $this->response->setJSON($chartData);
    }
}
