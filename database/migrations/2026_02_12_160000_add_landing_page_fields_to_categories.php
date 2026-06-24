<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add landing page fields to categories for enhanced SEO and conversion
 * Inspired by Amazon/Hormozi approach - value-first, trust-building pages
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
    
            Schema::table('categories', function (Blueprint $table) {
                // Hero Section
                $table->string('hero_title')->nullable()->after('icon');
                $table->string('hero_subtitle')->nullable();
                $table->string('hero_image')->nullable();
                $table->string('hero_image_storage_type')->nullable();
                $table->string('hero_cta_text')->nullable()->default('Shop Now');
                $table->string('hero_cta_link')->nullable();
                
                // Value Proposition (Hormozi-style)
                $table->string('value_prop_headline')->nullable();
                $table->text('value_props')->nullable(); // JSON array of value propositions
                
                // Trust Signals
                $table->text('trust_badges')->nullable(); // JSON array: warranty, delivery, support, etc.
                
                // Custom Content Blocks
                $table->text('content_top')->nullable(); // HTML/Markdown above products
                $table->text('content_bottom')->nullable(); // HTML/Markdown below products
                
                // FAQ Section (SEO gold)
                $table->text('faqs')->nullable(); // JSON array of Q&A pairs
                
                // Buying Guide
                $table->text('buying_guide')->nullable(); // Markdown content
                $table->string('buying_guide_title')->nullable();
                
                // Social Proof
                $table->string('review_highlight')->nullable();
                $table->integer('review_count_display')->nullable();
                $table->decimal('avg_rating_display', 2, 1)->nullable();
                
                // Conversion Elements
                $table->string('urgency_text')->nullable(); // e.g., "Limited stock!"
                $table->string('promo_banner_text')->nullable();
                $table->string('promo_banner_color')->nullable()->default('#dc3545');
                
                // WhatsApp CTA
                $table->string('whatsapp_message')->nullable(); // Pre-filled message
                $table->boolean('show_whatsapp_float')->default(true);
                
                // Layout Options
                $table->string('layout_type')->default('standard'); // standard, landing, minimal
                $table->integer('products_per_page')->default(20);
                $table->string('default_sort')->default('popularity');
                
                // Analytics
                $table->integer('landing_view_count')->default(0);
                
                // Toggle for enhanced landing page
                $table->boolean('use_landing_page')->default(false);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Column or table already exists - skip
            if (!in_array($e->errorInfo[1] ?? 0, [1060, 1050, 1061])) throw $e;
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title', 'hero_subtitle', 'hero_image', 'hero_image_storage_type',
                'hero_cta_text', 'hero_cta_link',
                'value_prop_headline', 'value_props',
                'trust_badges',
                'content_top', 'content_bottom',
                'faqs',
                'buying_guide', 'buying_guide_title',
                'review_highlight', 'review_count_display', 'avg_rating_display',
                'urgency_text', 'promo_banner_text', 'promo_banner_color',
                'whatsapp_message', 'show_whatsapp_float',
                'layout_type', 'products_per_page', 'default_sort',
                'landing_view_count',
                'use_landing_page'
            ]);
        });
    }
};
