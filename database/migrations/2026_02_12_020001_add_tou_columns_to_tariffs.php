<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
    
            Schema::table('power_calculator_tariffs', function (Blueprint $table) {
                if (!Schema::hasColumn('power_calculator_tariffs', 'tariff_type')) {
                    $table->string('tariff_type', 20)->default('domestic')->after('code');
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'rate_peak')) {
                    $table->decimal('rate_peak', 10, 2)->nullable()->after('rate_tier3');
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'rate_shoulder')) {
                    $table->decimal('rate_shoulder', 10, 2)->nullable()->after('rate_peak');
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'rate_off_peak')) {
                    $table->decimal('rate_off_peak', 10, 2)->nullable()->after('rate_shoulder');
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'rate_average')) {
                    $table->decimal('rate_average', 10, 2)->nullable()->after('rate_off_peak');
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'voltage')) {
                    $table->string('voltage', 50)->nullable();
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'max_demand')) {
                    $table->string('max_demand', 100)->nullable();
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('power_calculator_tariffs', 'is_default')) {
                    $table->boolean('is_default')->default(false);
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Column or table already exists - skip
            if (!in_array($e->errorInfo[1] ?? 0, [1060, 1050, 1061])) throw $e;
        }
    }

    public function down(): void
    {
        // Not dropping columns to preserve data
    }
};
