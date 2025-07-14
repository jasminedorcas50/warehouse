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
        Schema::create('resource_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type');
            $table->string('department');
            $table->integer('current_capacity');
            $table->integer('optimal_capacity');
            $table->json('utilization_metrics');
            $table->json('recommendations');
            $table->date('assessment_date');
            $table->timestamps();

            $table->index(['resource_type', 'department']);
        });

        Schema::create('staffing_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->date('date');
            $table->json('required_staff');
            $table->json('available_staff');
            $table->json('recommendations');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['department', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffing_recommendations');
        Schema::dropIfExists('resource_allocations');
    }
};
