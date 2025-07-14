<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analytics_data', function (Blueprint $table) {
            $table->id();
            $table->date('data_date');
            $table->string('metric_type');
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->decimal('value', 10, 2);
            $table->json('dimensions')->nullable();
            $table->string('source');
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['data_date', 'metric_type']);
            $table->index(['category', 'subcategory']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics_data');
    }
};
