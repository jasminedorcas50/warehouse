<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clinical_guidelines', function (Blueprint $table) {
            $table->id();
            $table->string('condition_name');
            $table->string('condition_code')->unique();
            $table->json('treatment_options');
            $table->json('alternative_treatments');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_guidelines');
    }
};
