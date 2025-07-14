<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hospital_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('source_name'); // Hospital or healthcare facility name
            $table->string('data_type'); // e.g., 'patient_records', 'billing', 'lab_results', etc.
            $table->date('data_period_start');
            $table->date('data_period_end');
            $table->string('format'); // e.g., 'csv', 'json', 'xml'
            $table->string('status')->default('pending'); // pending, processing, imported, failed
            $table->json('metadata')->nullable(); // Additional dataset information
            $table->string('file_path')->nullable(); // Path to stored dataset file
            $table->integer('record_count')->nullable();
            $table->text('import_notes')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create a table for tracking data import logs
        Schema::create('dataset_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_dataset_id')->constrained()->onDelete('cascade');
            $table->string('status'); // success, error, warning
            $table->text('message');
            $table->integer('records_processed')->default(0);
            $table->integer('records_succeeded')->default(0);
            $table->integer('records_failed')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dataset_import_logs');
        Schema::dropIfExists('hospital_datasets');
    }
};
