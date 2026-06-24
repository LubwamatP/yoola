<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add image_alt field for SEO image optimization
 * Allows admin to set custom alt text for product images
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
    
            Schema::table('products', function (Blueprint $table) {
                $table->string('image_alt', 255)->nullable()->after('meta_description');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Column or table already exists - skip
            if (!in_array($e->errorInfo[1] ?? 0, [1060, 1050, 1061])) throw $e;
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_alt');
        });
    }
};
