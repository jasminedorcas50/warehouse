<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Make previously required fields nullable
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            // Revert changes if needed
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->enum('gender', ['male', 'female', 'other'])->nullable(false)->change();
        });
    }
};
