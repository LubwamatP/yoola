<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create categories table if not exists
        if (!Schema::hasTable('power_calculator_categories')) {
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
        }

        // Create appliances table if not exists
        if (!Schema::hasTable('power_calculator_appliances')) {
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
        }

        // Create settings table if not exists
        if (!Schema::hasTable('power_calculator_settings')) {
            if (!Schema::hasTable('power_calculator_settings')) {
        Schema::create('power_calculator_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('power_calculator_appliances');
        Schema::dropIfExists('power_calculator_categories');
        Schema::dropIfExists('power_calculator_settings');
    }
};
