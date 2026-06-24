<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This table stores search analytics data for AI-powered search monitoring
     * and continuous improvement of the Gemini search integration.
     */
    public function up(): void
    {
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();

            // Search query data
            $table->string('query', 500)->index();
            $table->integer('results_count')->default(0);
            $table->boolean('ai_powered')->default(false)->index();

            // AI Intent data (JSON)
            $table->json('intent')->nullable();
            $table->decimal('confidence', 3, 2)->nullable()->index();

            // Performance metrics
            $table->integer('response_time_ms')->nullable();

            // User tracking (optional)
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();

            // Filters applied
            $table->json('filters')->nullable();

            // User interaction (can be updated later)
            $table->integer('clicks')->default(0);
            $table->boolean('converted')->default(false);
            $table->unsignedBigInteger('clicked_product_id')->nullable();

            // Feedback
            $table->tinyInteger('user_rating')->nullable(); // 1-5 rating
            $table->text('feedback')->nullable();

            $table->timestamps();

            // Indexes for analytics queries
            $table->index(['created_at', 'ai_powered']);
            $table->index(['created_at', 'results_count']);
        });

        // Create table for tracking search-to-purchase conversions
        Schema::create('search_conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('search_analytics_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('order_amount', 12, 2);
            $table->timestamps();

            $table->foreign('search_analytics_id')
                ->references('id')
                ->on('search_analytics')
                ->onDelete('cascade');

            $table->index(['created_at']);
        });

        // Create table for popular searches (aggregated)
        Schema::create('popular_searches', function (Blueprint $table) {
            $table->id();
            $table->string('query', 500)->unique();
            $table->string('normalized_query', 500)->index();
            $table->integer('search_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->decimal('avg_results', 8, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->date('last_searched')->index();
            $table->timestamps();

            $table->index(['search_count', 'last_searched']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_conversions');
        Schema::dropIfExists('popular_searches');
        Schema::dropIfExists('search_analytics');
    }
};
