<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

echo "Starting sitemap generation...\n";

try {
    // Generate sitemap by crawling the site
    SitemapGenerator::create('https://yoola.ug')
        ->writeToFile(public_path('sitemap.xml'));
    
    echo "Sitemap generated successfully!\n";
    
    // Count URLs
    $content = file_get_contents(public_path('sitemap.xml'));
    $count = substr_count($content, '<loc>');
    echo "Total URLs: " . $count . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
