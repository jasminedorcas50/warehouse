<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop existing columns that we don't need
            $table->dropColumn([
                'first_name',
                'last_name',
                'date_of_birth',
                'email',
                'phone',
                'address',
                'emergency_contact',
                'blood_type',
                'medical_history',
                'allergies'
            ]);

            // Add new columns for analytics
            $table->integer('age')->nullable();
            $table->string('condition')->nullable();
            $table->string('procedure')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('length_of_stay')->nullable(); // in days
            $table->boolean('readmission')->nullable();
            $table->string('outcome')->nullable();
            $table->integer('satisfaction')->nullable(); // 1-5 rating
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Restore original columns
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('blood_type')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();

            // Drop new columns
            $table->dropColumn([
                'age',
                'condition',
                'procedure',
                'cost',
                'length_of_stay',
                'readmission',
                'outcome',
                'satisfaction'
            ]);
        });
    }
};
