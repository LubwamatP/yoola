<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('electricity_tariffs')) {
        Schema::create('electricity_tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Domestic", "Commercial", "Industrial"
            $table->string('code')->unique(); // e.g., "DOM", "COM", "IND"
            $table->decimal('rate_per_kwh', 10, 2); // Price per kWh in UGX
            $table->decimal('service_fee', 10, 2)->default(0); // Monthly service fee
            $table->decimal('vat_percentage', 5, 2)->default(18); // VAT %
            $table->integer('min_units')->default(0); // Minimum units for this tier
            $table->integer('max_units')->nullable(); // Max units (null = unlimited)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        }

        // Insert default Uganda electricity tariffs (UMEME rates)
        DB::table('electricity_tariffs')->insert([
            [
                'name' => 'Domestic (Lifeline)',
                'code' => 'DOM_LIFELINE',
                'rate_per_kwh' => 250.00,
                'service_fee' => 3360.00,
                'vat_percentage' => 18.00,
                'min_units' => 0,
                'max_units' => 15,
                'description' => 'For first 15 units per month (subsidized)',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Domestic (Standard)',
                'code' => 'DOM_STANDARD',
                'rate_per_kwh' => 803.30,
                'service_fee' => 3360.00,
                'vat_percentage' => 18.00,
                'min_units' => 16,
                'max_units' => null,
                'description' => 'For units above 15 per month',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Commercial',
                'code' => 'COMMERCIAL',
                'rate_per_kwh' => 705.50,
                'service_fee' => 10080.00,
                'vat_percentage' => 18.00,
                'min_units' => 0,
                'max_units' => null,
                'description' => 'For commercial/business premises',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Industrial',
                'code' => 'INDUSTRIAL',
                'rate_per_kwh' => 522.00,
                'service_fee' => 50400.00,
                'vat_percentage' => 18.00,
                'min_units' => 0,
                'max_units' => null,
                'description' => 'For industrial premises',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('electricity_tariffs');
    }
};
