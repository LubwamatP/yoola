<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tables for product view tracking and recommendation system.
     */
    public function up(): void
    {
        // Product views - tracks individual product page views
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->index();
            $table->timestamp('viewed_at')->index();
            $table->integer('duration_seconds')->nullable();
            $table->string('device_type', 20)->nullable(); // mobile, desktop, tablet
            $table->string('referrer', 500)->nullable();
            $table->string('source', 50)->nullable(); // homepage, search, category, direct
            $table->boolean('added_to_cart')->default(false);
            $table->boolean('purchased')->default(false);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Composite indexes for common queries
            $table->index(['product_id', 'viewed_at']);
            $table->index(['session_id', 'viewed_at']);
            $table->index(['user_id', 'viewed_at']);
        });

        // Product stats - aggregated statistics for recommendations
        Schema::create('product_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->primary();
            $table->integer('views_1h')->default(0);
            $table->integer('views_24h')->default(0);
            $table->integer('views_7d')->default(0);
            $table->integer('views_30d')->default(0);
            $table->integer('unique_visitors_24h')->default(0);
            $table->integer('unique_visitors_7d')->default(0);
            $table->integer('cart_adds_7d')->default(0);
            $table->integer('purchases_7d')->default(0);
            $table->integer('purchases_30d')->default(0);
            $table->decimal('conversion_rate', 5, 4)->default(0); // views to purchase
            $table->decimal('cart_rate', 5, 4)->default(0); // views to cart
            $table->decimal('trending_score', 10, 4)->default(0)->index();
            $table->decimal('popularity_score', 10, 4)->default(0)->index();
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Indexes for recommendation queries
            $table->index(['trending_score', 'views_24h']);
            $table->index(['popularity_score', 'views_7d']);
        });

        // User preferences - for personalized recommendations
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->json('category_affinity')->nullable(); // {category_id: score, ...}
            $table->json('brand_affinity')->nullable(); // {brand_id: score, ...}
            $table->decimal('price_range_min', 12, 2)->nullable();
            $table->decimal('price_range_max', 12, 2)->nullable();
            $table->decimal('avg_viewed_price', 12, 2)->nullable();
            $table->integer('total_views')->default(0);
            $table->integer('total_purchases')->default(0);
            $table->json('recently_viewed')->nullable(); // [product_ids...]
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index('last_activity_at');
        });

        // Recommendation cache - stores pre-computed recommendations
        Schema::create('recommendation_cache', function (Blueprint $table) {
            $table->string('cache_key', 100)->primary();
            $table->string('placement_zone', 50)->index(); // hero, trending, just_for_you, etc.
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->json('product_ids');
            $table->json('metadata')->nullable(); // scores, reasons, etc.
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });

        // Product co-views - for "customers also viewed" recommendations
        Schema::create('product_co_views', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('co_viewed_product_id');
            $table->integer('co_view_count')->default(0);
            $table->decimal('affinity_score', 8, 4)->default(0);
            $table->timestamp('last_updated_at')->nullable();

            $table->primary(['product_id', 'co_viewed_product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('co_viewed_product_id')->references('id')->on('products')->onDelete('cascade');

            $table->index(['product_id', 'affinity_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_co_views');
        Schema::dropIfExists('recommendation_cache');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('product_stats');
        Schema::dropIfExists('product_views');
    }
};
