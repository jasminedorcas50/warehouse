<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     // Disable foreign key checks temporarily
    //     DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    //     // Truncate the patients table
    //     DB::table('patients')->truncate();

    //     // Re-enable foreign key checks
    //     DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    // }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     // No down migration needed as this is a data cleanup
    // }
};