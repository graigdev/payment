<?php

namespace GraigDev\Payment\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublishModelsCommand extends Command
{
    protected $signature = 'payment:publish-models {--force : Overwrite existing models}';
    protected $description = 'Publish Payment models with proper namespace';

    public function handle()
    {
        $targetDir = app_path('Models/Payment');
        
        // Create directory if it doesn't exist
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        
        $sourceDir = __DIR__ . '/../../Models';
        $models = File::files($sourceDir);
        
        foreach ($models as $model) {
            $filename = $model->getFilename();
            $targetFile = $targetDir . '/' . $filename;
            
            // Check if file exists and force option is not used
            if (File::exists($targetFile) && !$this->option('force')) {
                if (!$this->confirm("The model [{$filename}] already exists. Do you want to overwrite it?")) {
                    continue;
                }
            }
            
            // Get content and replace namespace
            $content = File::get($model->getPathname());
            $content = str_replace(
                'namespace GraigDev\Payment\Models;',
                'namespace App\Models\Payment;',
                $content
            );
            
            // Update model relations
            $content = str_replace(
                ['use GraigDev\Payment\Models\\', 'use App\Models\Payment\\'],
                ['use App\Models\Payment\\', 'use App\Models\Payment\\'],
                $content
            );
            
            // Write file
            File::put($targetFile, $content);
            $this->info("Published model: {$filename}");
        }
        
        $this->info('Payment models published successfully!');
        $this->info('Note: Remember to update any references to these models in your code.');
    }
} 