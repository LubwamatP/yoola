<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tables for AI-powered recommendations:
     * - product_views: Track individual product views
     * - product_stats: Aggregated stats for trending/popular calculations
     * - product_co_views: Co-viewing patterns (customers who viewed X also viewed Y)
     * - user_preferences: User behavior and preference tracking
     */
    public function up(): void
    {
        // Product Views - individual view tracking
        if (!Schema::hasTable('product_views')) {
            Schema::create('product_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('session_id', 100)->index();
                $table->timestamp('viewed_at')->index();
                $table->integer('duration_seconds')->nullable();
                $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
                $table->string('referrer', 500)->nullable();
                $table->enum('source', ['direct', 'search', 'category', 'homepage', 'shop', 'external', 'internal'])->default('direct');
                $table->boolean('added_to_cart')->default(false);
                $table->boolean('purchased')->default(false);
                $table->timestamps();

                // Indexes for common queries
                $table->index(['product_id', 'viewed_at']);
                $table->index(['user_id', 'viewed_at']);
                $table->index(['session_id', 'viewed_at']);
            });
        }

        // Product Stats - aggregated metrics for recommendations
        if (!Schema::hasTable('product_stats')) {
            Schema::create('product_stats', function (Blueprint $table) {
                $table->foreignId('product_id')->primary()->constrained()->onDelete('cascade');
                
                // View counts at different time windows
                $table->integer('views_1h')->default(0);
                $table->integer('views_24h')->default(0);
                $table->integer('views_7d')->default(0);
                $table->integer('views_30d')->default(0);
                
                // Unique visitor counts
                $table->integer('unique_visitors_24h')->default(0);
                $table->integer('unique_visitors_7d')->default(0);
                
                // Conversion metrics
                $table->integer('cart_adds_7d')->default(0);
                $table->integer('purchases_7d')->default(0);
                $table->integer('purchases_30d')->default(0);
                $table->decimal('conversion_rate', 8, 4)->default(0);
                $table->decimal('cart_rate', 8, 4)->default(0);
                
                // Calculated scores
                $table->decimal('trending_score', 12, 4)->default(0)->index();
                $table->decimal('popularity_score', 12, 4)->default(0)->index();
                
                // Timestamps
                $table->timestamp('last_viewed_at')->nullable();
                $table->timestamp('last_calculated_at')->nullable();
                $table->timestamps();
            });
        }

        // Product Co-Views - tracks products viewed together
        if (!Schema::hasTable('product_co_views')) {
            Schema::create('product_co_views', function (Blueprint $table) {
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('co_viewed_product_id')->constrained('products')->onDelete('cascade');
                $table->integer('co_view_count')->default(0);
                $table->decimal('affinity_score', 10, 4)->default(0)->index();
                $table->timestamp('last_updated_at')->nullable();

                // Composite primary key
                $table->primary(['product_id', 'co_viewed_product_id']);
                
                // Index for lookups
                $table->index(['product_id', 'affinity_score']);
            });
        }

        // User Preferences - behavior tracking for personalization
        if (!Schema::hasTable('user_preferences')) {
            Schema::create('user_preferences', function (Blueprint $table) {
                $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
                
                // Affinity scores (JSON maps category/brand ID to view count)
                $table->json('category_affinity')->nullable();
                $table->json('brand_affinity')->nullable();
                
                // Price preferences
                $table->decimal('price_range_min', 15, 2)->nullable();
                $table->decimal('price_range_max', 15, 2)->nullable();
                $table->decimal('avg_viewed_price', 15, 2)->nullable();
                
                // Activity counts
                $table->integer('total_views')->default(0);
                $table->integer('total_purchases')->default(0);
                
                // Recently viewed product IDs (JSON array)
                $table->json('recently_viewed')->nullable();
                
                // Timestamps
                $table->timestamp('last_activity_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('product_co_views');
        Schema::dropIfExists('product_stats');
        Schema::dropIfExists('product_views');
    }
};
