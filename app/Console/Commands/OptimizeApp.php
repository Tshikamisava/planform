<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the application for production performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting application optimization...');
        
        // Clear all caches first
        $this->info('Clearing existing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        // Optimize configuration
        $this->info('Caching configuration...');
        Artisan::call('config:cache');
        
        // Optimize routes
        $this->info('Caching routes...');
        Artisan::call('route:cache');
        
        // Optimize views
        $this->info('Compiling views...');
        Artisan::call('view:cache');
        
        // Optimize events
        $this->info('Caching events...');
        Artisan::call('event:cache');
        
        // Optimize autoloader
        $this->info('Optimizing autoloader...');
        exec('composer dump-autoload -o');
        
        $this->info('✓ Application optimization complete!');
        $this->newLine();
        
        $this->table(
            ['Optimization', 'Status'],
            [
                ['Config Cache', '✓ Enabled'],
                ['Route Cache', '✓ Enabled'],
                ['View Cache', '✓ Enabled'],
                ['Event Cache', '✓ Enabled'],
                ['Autoloader', '✓ Optimized'],
            ]
        );
        
        return 0;
    }
}
