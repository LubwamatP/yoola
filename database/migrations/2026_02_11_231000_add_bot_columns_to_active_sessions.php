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
        Schema::table('active_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('active_sessions', 'is_bot')) {
                $table->boolean('is_bot')->default(false)->index()->after('session_started');
            }
            if (!Schema::hasColumn('active_sessions', 'bot_name')) {
                $table->string('bot_name', 100)->nullable()->after('is_bot');
            }
            if (!Schema::hasColumn('active_sessions', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('bot_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('active_sessions', function (Blueprint $table) {
            $table->dropColumn(['is_bot', 'bot_name', 'user_agent']);
        });
    }
};
