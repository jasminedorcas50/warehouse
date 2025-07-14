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
        Schema::create('quality_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->string('category');
            $table->decimal('current_value', 10, 2);
            $table->decimal('target_value', 10, 2);
            $table->decimal('benchmark_value', 10, 2);
            $table->json('trend_data');
            $table->json('improvement_areas');
            $table->date('assessment_date');
            $table->timestamps();

            $table->index(['metric_name', 'category']);
        });

        Schema::create('cost_analysis', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('cost_category');
            $table->decimal('current_cost', 12, 2);
            $table->decimal('budgeted_cost', 12, 2);
            $table->json('cost_breakdown');
            $table->json('savings_opportunities');
            $table->date('analysis_date');
            $table->timestamps();

            $table->index(['department', 'cost_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_analysis');
        Schema::dropIfExists('quality_metrics');
    }
};
