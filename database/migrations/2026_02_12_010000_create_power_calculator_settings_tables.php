<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Electricity tariffs table
        if (!Schema::hasTable('power_calculator_tariffs')) {
        Schema::create('power_calculator_tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // e.g., "Domestic", "Commercial"
            $table->decimal('rate_lifeline', 10, 2)->default(250.0);  // 0-15 kWh
            $table->decimal('rate_tier_1', 10, 2)->default(756.2);    // 16-80 kWh
            $table->decimal('rate_tier_2', 10, 2)->default(412.0);    // 81-150 kWh
            $table->decimal('rate_tier_3', 10, 2)->default(756.2);    // Above 150 kWh
            $table->integer('limit_lifeline')->default(15);
            $table->integer('limit_tier_1')->default(80);
            $table->integer('limit_tier_2')->default(150);
            $table->integer('limit_eligibility')->default(100);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        }

        // Appliance categories
        if (!Schema::hasTable('power_calculator_categories')) {
        Schema::create('power_calculator_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        }

        // Appliances
        if (!Schema::hasTable('power_calculator_appliances')) {
        Schema::create('power_calculator_appliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('power_calculator_categories')->onDelete('cascade');
            $table->string('name');
            $table->integer('wattage');
            $table->decimal('typical_hours', 4, 2)->default(1.0);
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        }

        // System settings
        if (!Schema::hasTable('power_calculator_settings')) {
        Schema::create('power_calculator_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('power_calculator_settings');
        Schema::dropIfExists('power_calculator_appliances');
        Schema::dropIfExists('power_calculator_categories');
        Schema::dropIfExists('power_calculator_tariffs');
    }
};
