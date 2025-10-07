<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MonitoringEngine;

class RunMonitors extends BaseCommand
{
    protected $group       = 'UpWatch';
    protected $name        = 'monitor:run';
    protected $description = 'Run all active monitors that are due for checking';

    public function run(array $params)
    {
        CLI::write('Starting monitor checks...', 'green');
        
        $engine = new MonitoringEngine();
        $results = $engine->runChecks();

        if (empty($results)) {
            CLI::write('No monitors due for checking.', 'yellow');
            return;
        }

        CLI::write('Checked ' . count($results) . ' monitor(s):', 'cyan');
        
        foreach ($results as $result) {
            $statusColor = $result['status'] === 'up' ? 'green' : 'red';
            $responseTime = $result['response_time'] ? " ({$result['response_time']}ms)" : '';
            
            CLI::write(
                "  [{$result['status']}] {$result['name']}{$responseTime}",
                $statusColor
            );
        }

        CLI::write('Monitor checks completed.', 'green');
    }
}
