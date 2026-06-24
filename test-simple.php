<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$count = Product::active()->where('current_stock', '>', 0)->where('unit_price', '<=', 2000000)->count();
echo "Products under 2M: {$count}\n";

$samples = Product::active()
    ->where('current_stock', '>', 0)
    ->where('unit_price', '<=', 2000000)
    ->orderBy('unit_price', 'desc')
    ->limit(3)
    ->get(['id', 'name', 'unit_price']);

foreach ($samples as $p) {
    echo "- {$p->name}: UGX " . number_format($p->unit_price) . "\n";
}
echo "DONE\n";
