<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class Install extends BaseCommand
{
    protected $group       = 'UpWatch';
    protected $name        = 'upwatch:install';
    protected $description = 'Interactive installer for UpWatch - sets up database, runs migrations, and creates admin user';

    public function run(array $params)
    {
        CLI::newLine();
        CLI::write('╔════════════════════════════════════════════════════════════╗', 'cyan');
        CLI::write('║                                                            ║', 'cyan');
        CLI::write('║              UpWatch Installation Wizard                   ║', 'cyan');
        CLI::write('║                                                            ║', 'cyan');
        CLI::write('╚════════════════════════════════════════════════════════════╝', 'cyan');
        CLI::newLine();

        // Check if .env exists
        if (!file_exists(ROOTPATH . '.env')) {
            CLI::error('ERROR: .env file not found!');
            CLI::write('Please copy the env file to .env and configure it first:', 'yellow');
            CLI::write('  cp env .env', 'white');
            CLI::newLine();
            return;
        }

        // Step 1: Check database connection
        CLI::write('Step 1/6: Checking database connection...', 'yellow');
        CLI::newLine();

        try {
            $db = Database::connect();
            $db->query('SELECT 1');
            CLI::write('✓ Database connection successful!', 'green');
        } catch (\Exception $e) {
            CLI::error('✗ Database connection failed!');
            CLI::newLine();
            CLI::write('Error: ' . $e->getMessage(), 'red');
            CLI::newLine();
            CLI::write('Please check your .env database settings:', 'yellow');
            CLI::write('  database.default.hostname', 'white');
            CLI::write('  database.default.database', 'white');
            CLI::write('  database.default.username', 'white');
            CLI::write('  database.default.password', 'white');
            CLI::newLine();
            CLI::write('Make sure the database exists and the user has proper permissions.', 'yellow');
            CLI::newLine();
            
            // Offer to show database creation commands
            if (CLI::prompt('Show database setup commands?', ['y', 'n']) === 'y') {
                CLI::newLine();
                CLI::write('Run these commands in MySQL/MariaDB:', 'cyan');
                CLI::write('----------------------------------------', 'cyan');
                CLI::write("CREATE DATABASE upwatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;", 'white');
                CLI::write("CREATE USER 'upwatch_user'@'localhost' IDENTIFIED BY 'your_secure_password';", 'white');
                CLI::write("GRANT ALL PRIVILEGES ON upwatch.* TO 'upwatch_user'@'localhost';", 'white');
                CLI::write("FLUSH PRIVILEGES;", 'white');
                CLI::write('----------------------------------------', 'cyan');
                CLI::newLine();
            }
            
            return;
        }
        CLI::newLine();

        // Step 2: Check encryption key
        CLI::write('Step 2/6: Checking encryption key...', 'yellow');
        CLI::newLine();

        $encryptionKey = env('encryption.key', '');
        if (empty($encryptionKey)) {
            CLI::write('⚠ Encryption key not set in .env', 'red');
            CLI::newLine();
            
            if (CLI::prompt('Generate encryption key now?', ['y', 'n']) === 'y') {
                // Generate key
                $result = shell_exec('php spark key:generate --force --show 2>&1');
                if ($result) {
                    CLI::write('✓ Encryption key generated!', 'green');
                    CLI::write('Please restart this installer to continue.', 'yellow');
                    CLI::newLine();
                    return;
                } else {
                    CLI::error('Failed to generate encryption key.');
                    CLI::write('Run manually: php spark key:generate', 'yellow');
                    CLI::newLine();
                    return;
                }
            } else {
                CLI::write('Run: php spark key:generate', 'yellow');
                CLI::newLine();
                return;
            }
        } else {
            CLI::write('✓ Encryption key is set', 'green');
        }
        CLI::newLine();

        // Step 3: Run migrations
        CLI::write('Step 3/6: Running database migrations...', 'yellow');
        CLI::newLine();

        try {
            // Check if migrations have already been run
            $migrations = \Config\Services::migrations();
            $currentVersion = $migrations->getVersion();
            
            if ($currentVersion > 0) {
                CLI::write('Database already has migrations applied (version: ' . $currentVersion . ')', 'yellow');
                
                if (CLI::prompt('Run migrations anyway?', ['y', 'n']) !== 'y') {
                    CLI::write('Skipping migrations.', 'yellow');
                } else {
                    $result = $migrations->latest();
                    if ($result === true) {
                        CLI::write('✓ Migrations completed successfully!', 'green');
                    } else {
                        CLI::error('✗ Migration failed!');
                        return;
                    }
                }
            } else {
                $result = $migrations->latest();
                if ($result === true) {
                    CLI::write('✓ Migrations completed successfully!', 'green');
                    CLI::write('  - Created all required tables', 'white');
                } else {
                    CLI::error('✗ Migration failed!');
                    return;
                }
            }
        } catch (\Exception $e) {
            CLI::error('✗ Migration error: ' . $e->getMessage());
            return;
        }
        CLI::newLine();

        // Step 4: Check if admin user exists
        CLI::write('Step 4/6: Checking for admin user...', 'yellow');
        CLI::newLine();

        $userModel = model('UserModel');
        $existingUser = $userModel->where('email', 'admin@upwatch.local')->first();

        if ($existingUser) {
            CLI::write('⚠ Admin user already exists (admin@upwatch.local)', 'yellow');
            CLI::newLine();
            
            if (CLI::prompt('Create a new admin user anyway?', ['y', 'n']) !== 'y') {
                CLI::write('Skipping user creation.', 'yellow');
                CLI::newLine();
                CLI::write('Step 5/6: Seeding sample data...', 'yellow');
                CLI::newLine();
                
                if (CLI::prompt('Run database seeder?', ['y', 'n']) === 'y') {
                    $this->call('db:seed', ['InitialDataSeeder']);
                    CLI::newLine();
                } else {
                    CLI::write('Skipping seeder.', 'yellow');
                }
                
                $this->showCompletionMessage(false);
                return;
            }
        }

        // Step 5: Run seeder (which creates admin user)
        CLI::write('Step 5/6: Creating admin user and seeding sample data...', 'yellow');
        CLI::newLine();

        try {
            // Run the seeder
            $seeder = \Config\Database::seeder();
            $seeder->call('InitialDataSeeder');
            
            CLI::newLine();
            CLI::write('✓ Database seeded successfully!', 'green');
            CLI::write('  - Admin user created', 'white');
            CLI::write('  - Sample status page created', 'white');
            CLI::write('  - Sample components added', 'white');
            CLI::write('  - Demo monitors configured', 'white');
        } catch (\Exception $e) {
            CLI::error('✗ Seeding error: ' . $e->getMessage());
            return;
        }

        CLI::newLine();
        
        // Step 6: Build CSS assets
        CLI::write('Step 6/6: Building CSS assets...', 'yellow');
        CLI::newLine();
        
        // Check if node_modules exists
        if (!is_dir(ROOTPATH . 'node_modules')) {
            CLI::write('⚠ Node modules not installed', 'yellow');
            CLI::write('Run: npm install', 'white');
            CLI::newLine();
            
            if (CLI::prompt('Install npm dependencies now?', ['y', 'n']) === 'y') {
                CLI::write('Running npm install...', 'white');
                $output = shell_exec('npm install 2>&1');
                if ($output) {
                    CLI::write($output, 'white');
                    CLI::write('✓ npm dependencies installed!', 'green');
                } else {
                    CLI::error('Failed to install npm dependencies');
                    CLI::write('Please run manually: npm install', 'yellow');
                }
            } else {
                CLI::write('Skipping npm install.', 'yellow');
                CLI::write('You will need to run: npm install && npm run build', 'yellow');
            }
        } else {
            CLI::write('✓ Node modules found', 'green');
        }
        
        // Build CSS
        if (is_dir(ROOTPATH . 'node_modules')) {
            CLI::newLine();
            if (CLI::prompt('Build Tailwind CSS now?', ['y', 'n']) === 'y') {
                CLI::write('Building CSS assets...', 'white');
                $output = shell_exec('npm run build 2>&1');
                if ($output) {
                    CLI::write($output, 'white');
                    CLI::write('✓ CSS assets built successfully!', 'green');
                    CLI::write('  - Created: public/assets/css/output.css', 'white');
                } else {
                    CLI::error('Failed to build CSS');
                    CLI::write('Please run manually: npm run build', 'yellow');
                }
            } else {
                CLI::write('Skipping CSS build.', 'yellow');
                CLI::write('Run later: npm run build', 'yellow');
            }
        }
        
        CLI::newLine();
        $this->showCompletionMessage(true);
    }

    private function showCompletionMessage(bool $newUserCreated)
    {
        CLI::write('╔════════════════════════════════════════════════════════════╗', 'green');
        CLI::write('║                                                            ║', 'green');
        CLI::write('║              Installation Complete! 🎉                     ║', 'green');
        CLI::write('║                                                            ║', 'green');
        CLI::write('╚════════════════════════════════════════════════════════════╝', 'green');
        CLI::newLine();

        if ($newUserCreated) {
            CLI::write('⚠️  IMPORTANT: Check the output above for your admin password!', 'yellow');
            CLI::write('    The password was randomly generated and shown during seeding.', 'yellow');
            CLI::newLine();
        }

        CLI::write('Next steps:', 'cyan');
        CLI::write('  1. Start the development server:', 'white');
        CLI::write('     php spark serve', 'green');
        CLI::newLine();
        CLI::write('  2. Access UpWatch:', 'white');
        CLI::write('     Public Page: http://localhost:8080', 'green');
        CLI::write('     Admin Panel: http://localhost:8080/admin', 'green');
        CLI::newLine();
        CLI::write('  3. Login with:', 'white');
        CLI::write('     Email: admin@upwatch.local', 'green');
        CLI::write('     Password: (shown in seeder output above)', 'yellow');
        CLI::newLine();
        CLI::write('  4. Set up cron job for monitoring:', 'white');
        CLI::write('     * * * * * cd /path/to/upwatch && php spark monitor:run', 'green');
        CLI::newLine();
        CLI::write('For more information, see UPWATCH_README.md', 'cyan');
        CLI::newLine();
    }
}
