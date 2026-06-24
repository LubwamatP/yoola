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
        if (!Schema::hasTable('active_sessions')) {
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('current_product_id')->nullable()->index();
            $table->string('current_page', 50)->nullable()->index();
            $table->string('device_type', 20)->nullable(); // mobile, tablet, desktop
            $table->string('browser', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('referrer')->nullable();
            $table->timestamp('last_activity')->nullable()->index();
            $table->timestamp('session_started')->nullable();
            
            // Bot detection fields
            $table->boolean('is_bot')->default(false)->index();
            $table->string('bot_name', 100)->nullable();
            $table->string('user_agent', 500)->nullable();
            
            $table->timestamps();

            // Foreign keys (optional - uncomment if needed)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('current_product_id')->references('id')->on('products')->onDelete('set null');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_sessions');
    }
};
