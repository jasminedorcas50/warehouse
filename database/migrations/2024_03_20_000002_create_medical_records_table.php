<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->date('visit_date');
            $table->string('visit_type');
            $table->text('chief_complaint');
            $table->text('diagnosis');
            $table->text('treatment_plan');
            $table->text('prescription')->nullable();
            $table->text('notes')->nullable();
            $table->json('vitals')->nullable();
            $table->json('lab_results')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_records');
    }
};
