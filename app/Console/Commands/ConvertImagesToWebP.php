<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ConvertImagesToWebP extends Command
{
    protected $signature = 'images:convert-webp 
                            {--path=storage/app/public/product : Path to convert}
                            {--quality=80 : WebP quality (1-100)}
                            {--dry-run : Show what would be converted without converting}';
    
    protected $description = 'Convert existing images to WebP format for better page speed';

    private int $converted = 0;
    private int $skipped = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $path = base_path($this->option('path'));
        $quality = (int) $this->option('quality');
        $dryRun = $this->option('dry-run');

        if (!File::isDirectory($path)) {
            $this->error("Directory not found: {$path}");
            return 1;
        }

        $this->info("Scanning for images in: {$path}");
        $this->info("Quality: {$quality}");
        
        if ($dryRun) {
            $this->warn("DRY RUN - No files will be converted");
        }

        $this->convertDirectory($path, $quality, $dryRun);

        $this->newLine();
        $this->info("=== Conversion Complete ===");
        $this->info("Converted: {$this->converted}");
        $this->info("Skipped (WebP exists): {$this->skipped}");
        $this->info("Failed: {$this->failed}");

        return 0;
    }

    private function convertDirectory(string $directory, int $quality, bool $dryRun): void
    {
        $files = File::allFiles($directory);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        $imageFiles = collect($files)->filter(function ($file) use ($imageExtensions) {
            return in_array(strtolower($file->getExtension()), $imageExtensions);
        });

        $this->info("Found {$imageFiles->count()} images to process");

        $bar = $this->output->createProgressBar($imageFiles->count());
        $bar->start();

        foreach ($imageFiles as $file) {
            $this->convertImage($file->getPathname(), $quality, $dryRun);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function convertImage(string $imagePath, int $quality, bool $dryRun): void
    {
        try {
            $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $imagePath);

            // Skip if WebP already exists
            if (File::exists($webpPath)) {
                $this->skipped++;
                return;
            }

            if ($dryRun) {
                $this->line("Would convert: {$imagePath}");
                $this->converted++;
                return;
            }

            // Use Intervention Image to convert
            $manager = new ImageManager(new Driver());
            $image = $manager->read($imagePath);
            
            // Encode to WebP
            $encoded = $image->toWebp($quality);
            $encoded->save($webpPath);

            $this->converted++;

            // Log size savings
            $originalSize = File::size($imagePath);
            $webpSize = File::size($webpPath);
            $savings = round((1 - ($webpSize / $originalSize)) * 100, 1);
            
            if ($this->getOutput()->isVerbose()) {
                $this->line("Converted: {$imagePath} (saved {$savings}%)");
            }

        } catch (\Exception $e) {
            $this->failed++;
            if ($this->getOutput()->isVerbose()) {
                $this->error("Failed: {$imagePath} - {$e->getMessage()}");
            }
        }
    }
}
