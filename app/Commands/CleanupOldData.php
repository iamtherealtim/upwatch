<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MonitorResultModel;

class CleanupOldData extends BaseCommand
{
    protected $group       = 'UpWatch';
    protected $name        = 'monitor:cleanup';
    protected $description = 'Clean up old monitor result data';

    public function run(array $params)
    {
        $days = $params[0] ?? 90;
        
        CLI::write("Cleaning up monitor results older than {$days} days...", 'yellow');
        
        $resultModel = new MonitorResultModel();
        $deleted = $resultModel->cleanupOldData($days);

        if ($deleted) {
            CLI::write("Deleted {$deleted} old record(s).", 'green');
        } else {
            CLI::write('No old records to delete.', 'cyan');
        }
    }
}
