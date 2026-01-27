<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application from Git and refresh all dependencies and caches.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commands = [
            'Discarding local changes...' => 'git reset --hard',
            'Pulling latest changes from Git...' => 'git pull origin main',
            'Installing Composer dependencies...' => 'composer install --optimize-autoloader --no-dev',
            'Installing NPM dependencies...' => 'npm install',
            'Building assets...' => 'npm run build',
            'Clearing caches...' => 'php artisan optimize:clear',
            'Optimizing application...' => 'php artisan optimize',
        ];

        foreach ($commands as $description => $command) {
            $this->info($description);

            $process = proc_open($command, [
                0 => STDIN,
                1 => STDOUT,
                2 => STDERR,
            ], $pipes);

            if (is_resource($process)) {
                $status = proc_close($process);

                if ($status !== 0) {
                    $this->error("Command failed with status: {$status}");

                    return 1;
                }
            }
        }

        $this->info('Application updated successfully!');

        return 0;
    }
}
