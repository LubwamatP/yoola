<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Push notification logs for analytics
        if (!Schema::hasTable('push_notification_logs')) {
        Schema::create('push_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 50); // cart_abandonment, price_drop, back_in_stock, etc.
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->decimal('conversion_value', 15, 2)->nullable();
            $table->timestamp('sent_at');
            
            $table->index(['user_id', 'type']);
            $table->index(['type', 'sent_at']);
            $table->index('sent_at');
        });
        }

        // Product views for tracking interest
        if (!Schema::hasTable('product_views')) {
            if (!Schema::hasTable('product_views')) {
        Schema::create('product_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->string('session_id')->nullable();
                $table->boolean('was_out_of_stock')->default(false);
                $table->timestamps();
                
                $table->index(['product_id', 'user_id']);
                $table->index('user_id');
            });
        }
        }

        // Price history for price drop alerts
        if (!Schema::hasTable('product_price_history')) {
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->decimal('old_price', 15, 2);
            $table->decimal('new_price', 15, 2);
            $table->decimal('drop_percent', 5, 2)->nullable();
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('changed_at');
            
            $table->index('product_id');
            $table->index('changed_at');
        });
        }

        // AI Chat conversations
        if (!Schema::hasTable('ai_chat_conversations')) {
        Schema::create('ai_chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('channel', 20)->default('web'); // web, app
            $table->enum('status', ['ai_active', 'escalated', 'admin_active', 'seller_active', 'resolved'])->default('ai_active');
            $table->string('sentiment', 20)->nullable(); // positive, neutral, negative, frustrated
            $table->decimal('order_value', 15, 2)->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('escalation_reason')->nullable();
            $table->unsignedBigInteger('taken_over_by')->nullable();
            $table->timestamp('taken_over_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('seller_id');
        });
        }

        // AI Chat messages
        if (!Schema::hasTable('ai_chat_messages')) {
        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->enum('sender_type', ['user', 'ai', 'admin', 'seller']);
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('message');
            $table->json('ai_context')->nullable(); // Products mentioned, intent detected, etc.
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->boolean('admin_approved')->nullable();
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('ai_chat_conversations')->onDelete('cascade');
        });
        }

        // Power calculator leads
        if (!Schema::hasTable('power_calculator_leads')) {
        Schema::create('power_calculator_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->decimal('monthly_kwh', 10, 2)->nullable();
            $table->decimal('monthly_cost', 15, 2)->nullable();
            $table->integer('battery_ah')->nullable();
            $table->integer('inverter_va')->nullable();
            $table->integer('solar_panels')->nullable();
            $table->integer('total_wattage')->nullable();
            $table->json('appliances')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('contacted')->default(false);
            $table->timestamp('contacted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('phone');
            $table->index(['contacted', 'created_at']);
        });
        }

        // Add fcm_token to users table if not exists
        if (!Schema::hasColumn('users', 'fcm_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('fcm_token')->nullable()->after('remember_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_conversations');
        Schema::dropIfExists('product_price_history');
        Schema::dropIfExists('product_views');
        Schema::dropIfExists('push_notification_logs');
        Schema::dropIfExists('power_calculator_leads');

        if (Schema::hasColumn('users', 'fcm_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};
