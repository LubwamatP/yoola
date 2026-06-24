<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Programmatic SEO - Price Pages
     * Creates pages for "[Product] price in Uganda" searches
     */
    public function up(): void
    {
        if (!Schema::hasTable('price_pages')) {
        Schema::create('price_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->index();
            $table->string('title'); // SEO title
            $table->string('meta_description', 160);
            $table->string('h1');
            $table->text('intro_text'); // Unique intro paragraph
            $table->text('buying_guide')->nullable(); // Unique buying guide
            $table->json('faqs')->nullable(); // FAQ array for schema
            $table->json('related_slugs')->nullable(); // Related price pages
            
            // Product filtering
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('product_type')->nullable(); // e.g., 'tv', 'fridge', 'cooker'
            $table->string('size_filter')->nullable(); // e.g., '32-inch', '200-litre'
            $table->string('feature_filter')->nullable(); // e.g., 'smart', 'automatic'
            $table->string('brand_filter')->nullable(); // e.g., 'hisense', 'samsung' (text match)
            
            // Display
            $table->integer('min_price')->nullable();
            $table->integer('max_price')->nullable();
            $table->string('hero_image')->nullable();
            
            // SEO
            $table->boolean('is_active')->default(true);
            $table->boolean('is_indexed')->default(true);
            $table->integer('priority')->default(0); // For sitemap
            
            $table->timestamps();
            
            // Indexes
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_active');
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('price_pages');
    }
};
