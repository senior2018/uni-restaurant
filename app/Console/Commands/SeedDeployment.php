<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DeploymentSeeder;

class SeedDeployment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:deployment {--force : Force seeding even in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with comprehensive deployment data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('This command is not allowed in production environment.');
            $this->info('Use --force flag if you really want to run this in production.');
            return 1;
        }

        $this->info('🌱 Starting deployment seeding...');
        $this->info('This will populate your database with comprehensive sample data.');

        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Seeding cancelled.');
            return 0;
        }

        try {
            $seeder = new DeploymentSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->info('✅ Deployment seeding completed successfully!');
            $this->info('You can now test your application with realistic data.');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Seeding failed: ' . $e->getMessage());
            return 1;
        }
    }
}
