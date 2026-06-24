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
                // Add tariff type and code
                $table->string('code', 20)->nullable()->after('name');  // e.g., "10.1", "20.1"
                $table->enum('tariff_type', ['domestic', 'commercial', 'industrial'])->default('domestic')->after('code');
                
                // Time-of-Use rates for commercial/industrial
                $table->decimal('rate_peak', 10, 2)->nullable()->after('rate_tier_3');
                $table->decimal('rate_shoulder', 10, 2)->nullable()->after('rate_peak');
                $table->decimal('rate_off_peak', 10, 2)->nullable()->after('rate_shoulder');
                $table->decimal('rate_average', 10, 2)->nullable()->after('rate_off_peak');
                
                // Additional info
                $table->string('voltage', 50)->nullable()->after('rate_average');  // e.g., "240V", "415V", "11kV"
                $table->string('max_demand', 100)->nullable()->after('voltage');   // e.g., "up to 500kVA"
                $table->text('description')->nullable()->after('max_demand');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Column or table already exists - skip
            if (!in_array($e->errorInfo[1] ?? 0, [1060, 1050, 1061])) throw $e;
        }
    }

    public function down(): void
    {
        Schema::table('power_calculator_tariffs', function (Blueprint $table) {
            $table->dropColumn([
                'code', 'tariff_type', 
                'rate_peak', 'rate_shoulder', 'rate_off_peak', 'rate_average',
                'voltage', 'max_demand', 'description'
            ]);
        });
    }
};
