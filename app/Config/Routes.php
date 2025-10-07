<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Status Page Routes
$routes->get('/', 'StatusController::index');
$routes->get('/status/(:any)', 'StatusController::index/$1');
$routes->get('/api/status/live', 'StatusController::liveStatus');
$routes->get('/api/status/live/(:any)', 'StatusController::liveStatus/$1');
$routes->get('/api/uptime/(:num)/(:any)', 'StatusController::uptimeData/$1/$2');

// Subscription Routes
$routes->post('/subscribe', 'SubscribeController::subscribe');
$routes->get('/subscribe/verify/(:any)', 'SubscribeController::verify/$1');
$routes->get('/subscribe/unsubscribe/(:any)', 'SubscribeController::unsubscribe/$1');
$routes->get('/subscribe/manage', 'SubscribeController::manage');

// Authentication Routes
$routes->get('/admin/login', 'AuthController::login');
$routes->post('/admin/login', 'AuthController::loginPost');
$routes->get('/admin/logout', 'AuthController::logout');

// Admin Routes (protected)
$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('/', 'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Components
    $routes->get('components', 'Admin\ComponentController::index');
    $routes->get('components/create', 'Admin\ComponentController::create');
    $routes->post('components/create', 'Admin\ComponentController::create');
    $routes->get('components/edit/(:num)', 'Admin\ComponentController::edit/$1');
    $routes->post('components/edit/(:num)', 'Admin\ComponentController::edit/$1');
    $routes->get('components/delete/(:num)', 'Admin\ComponentController::delete/$1');
    $routes->post('components/update-status/(:num)', 'Admin\ComponentController::updateStatus/$1');

    // Incidents
    $routes->get('incidents', 'Admin\IncidentController::index');
    $routes->get('incidents/create', 'Admin\IncidentController::create');
    $routes->post('incidents/create', 'Admin\IncidentController::create');
    $routes->get('incidents/view/(:num)', 'Admin\IncidentController::view/$1');
    $routes->post('incidents/update/(:num)', 'Admin\IncidentController::addUpdate/$1');
    $routes->post('incidents/resolve/(:num)', 'Admin\IncidentController::resolve/$1');
    $routes->get('incidents/delete/(:num)', 'Admin\IncidentController::delete/$1');

    // Monitors
    $routes->get('monitors', 'Admin\MonitorController::index');
    $routes->get('monitors/create', 'Admin\MonitorController::create');
    $routes->post('monitors/create', 'Admin\MonitorController::create');
    $routes->get('monitors/edit/(:num)', 'Admin\MonitorController::edit/$1');
    $routes->post('monitors/edit/(:num)', 'Admin\MonitorController::edit/$1');
    $routes->get('monitors/delete/(:num)', 'Admin\MonitorController::delete/$1');
    $routes->post('monitors/test/(:num)', 'Admin\MonitorController::test/$1');
    $routes->get('monitors/results/(:num)', 'Admin\MonitorController::results/$1');

    // Subscribers
    $routes->get('subscribers', 'Admin\SubscriberController::index');
    $routes->get('subscribers/create', 'Admin\SubscriberController::create');
    $routes->post('subscribers/create', 'Admin\SubscriberController::create');
    $routes->get('subscribers/verify/(:num)', 'Admin\SubscriberController::verify/$1');
    $routes->get('subscribers/resend/(:num)', 'Admin\SubscriberController::resendVerification/$1');
    $routes->get('subscribers/toggle/(:num)', 'Admin\SubscriberController::toggleStatus/$1');
    $routes->get('subscribers/delete/(:num)', 'Admin\SubscriberController::delete/$1');
    $routes->get('subscribers/export', 'Admin\SubscriberController::export');

    // Settings
    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings', 'Admin\SettingsController::index');
    $routes->post('settings/test-email', 'Admin\SettingsController::testEmail');
});
