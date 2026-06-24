<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add AI processing columns to chattings table
     */
    public function up(): void
    {
        // Add AI processing column to chattings
        if (Schema::hasTable('chattings') && !Schema::hasColumn('chattings', 'ai_processed_at')) {
            Schema::table('chattings', function (Blueprint $table) {
                $table->timestamp('ai_processed_at')->nullable()->after('updated_at');
            });
        }

        // Create AI escalations tracking table
        if (!Schema::hasTable('ai_escalations')) {
            Schema::create('ai_escalations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chatting_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('reason', 50);
                $table->text('original_message')->nullable();
                $table->boolean('resolved')->default(false);
                $table->unsignedBigInteger('resolved_by')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'created_at']);
                $table->index('resolved');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('chattings', 'ai_processed_at')) {
            Schema::table('chattings', function (Blueprint $table) {
                $table->dropColumn('ai_processed_at');
            });
        }

        Schema::dropIfExists('ai_escalations');
    }
};
