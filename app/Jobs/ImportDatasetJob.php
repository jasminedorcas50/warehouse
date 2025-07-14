<?php

namespace App\Jobs;

use App\Models\HospitalDataset;
use App\Models\DatasetImportLog;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImportDatasetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataset;

    public function __construct(HospitalDataset $dataset)
    {
        $this->dataset = $dataset;
    }

    public function handle()
    {
        $importLog = $this->dataset->importLogs()->latest()->first();

        if (!$importLog) {
            throw new \Exception('No import log found for this dataset');
        }

        DB::beginTransaction();
        try {
            $filePath = Storage::path($this->dataset->file_path);
            if (!file_exists($filePath)) {
                throw new \Exception('Dataset file not found at: ' . $filePath);
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                throw new \Exception('Failed to read dataset file');
            }

            $importLog->update([
                'status' => 'processing',
                'message' => 'Starting CSV import process',
                'error_details' => []
            ]);

            // Process the file based on format
            switch ($this->dataset->format) {
                case 'csv':
                    $this->processCsv($content, $importLog);
                    break;
                case 'json':
                    $this->processJson($content, $importLog);
                    break;
                case 'xml':
                    $this->processXml($content, $importLog);
                    break;
                default:
                    throw new \Exception('Unsupported file format: ' . $this->dataset->format);
            }

            // Update dataset and log on success
            $this->dataset->update([
                'status' => 'imported',
                'imported_at' => now(),
                'record_count' => $importLog->records_succeeded
            ]);

            $importLog->update([
                'status' => 'success',
                'message' => sprintf(
                    'Import completed successfully. Processed: %d, Succeeded: %d, Failed: %d',
                    $importLog->records_processed,
                    $importLog->records_succeeded,
                    $importLog->records_failed
                )
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dataset->update(['status' => 'failed']);

            $importLog->update([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage(),
                'error_details' => [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    protected function processCsv($content, DatasetImportLog $importLog)
    {
        $rows = array_map('str_getcsv', explode("\n", $content));
        $headers = array_map('trim', array_map('strtolower', array_shift($rows)));

        foreach ($rows as $index => $row) {
            // Skip empty rows
            if (empty($row[0])) {
                continue;
            }

            try {
                // Validate row length
                if (count($row) !== count($headers)) {
                    $this->logError($importLog, [
                        'row_number' => $index + 2,
                        'row_data' => $row,
                        'error' => 'Invalid number of columns. Expected ' . count($headers) . ', got ' . count($row)
                    ]);
                    continue;
                }

                // Combine headers with row data
                $data = array_combine($headers, $row);

                // Process the record
                $this->importRecord($data, $importLog);

                // Update processed count
                $this->updateLogCount($importLog, 'records_processed');
            } catch (\Exception $e) {
                $this->logError($importLog, [
                    'row_number' => $index + 2,
                    'row_data' => $row,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function processJson($content, DatasetImportLog $importLog)
    {
        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new \Exception('Invalid JSON format');
        }

        DB::beginTransaction();
        try {
            foreach ($data as $record) {
                $this->importRecord($record, $importLog);

                $importLog->records_processed++;
                $importLog->records_succeeded++;
                $importLog->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function processXml($content, DatasetImportLog $importLog)
    {
        $xml = simplexml_load_string($content);
        if (!$xml) {
            throw new \Exception('Invalid XML format');
        }

        DB::beginTransaction();
        try {
            foreach ($xml->children() as $record) {
                $data = json_decode(json_encode($record), true);
                $this->importRecord($data, $importLog);

                $importLog->records_processed++;
                $importLog->records_succeeded++;
                $importLog->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function logError(DatasetImportLog $importLog, array $error)
    {
        // Get current errors or initialize empty array
        $currentErrors = $importLog->error_details ?? [];

        // Add new error
        $currentErrors[] = $error;

        // Update using update() method
        $importLog->update([
            'records_failed' => $importLog->records_failed + 1,
            'error_details' => $currentErrors
        ]);
    }

    protected function updateLogCount(DatasetImportLog $importLog, string $field)
    {
        $importLog->update([
            $field => $importLog->$field + 1
        ]);
    }

    protected function importRecord($data, DatasetImportLog $importLog)
    {
        if ($this->dataset->data_type === 'patient_records') {
            try {
                // Clean and prepare the data
                $patientData = [
                    'patient_id' => $data['patient_id'] ?? 'PAT' . str_pad(Patient::count() + 1, 6, '0', STR_PAD_LEFT),
                    'age' => isset($data['age']) ? (int)trim($data['age']) : null,
                    'gender' => isset($data['gender']) ? trim($data['gender']) : null,
                    'condition' => isset($data['condition']) ? trim($data['condition']) : null,
                    'procedure' => isset($data['procedure']) ? trim($data['procedure']) : null,
                    'cost' => isset($data['cost']) ? (float)trim($data['cost']) : null,
                    'length_of_stay' => isset($data['length_of_stay']) ? (int)trim($data['length_of_stay']) : null,
                    'readmission' => isset($data['readmission']) ? filter_var($data['readmission'], FILTER_VALIDATE_BOOLEAN) : null,
                    'outcome' => isset($data['outcome']) ? trim($data['outcome']) : null,
                    'satisfaction' => isset($data['satisfaction']) ? (int)trim($data['satisfaction']) : null
                ];

                // Create or update patient record
                Patient::updateOrCreate(
                    ['patient_id' => $patientData['patient_id']],
                    $patientData
                );

                $importLog->update([
                    'records_succeeded' => $importLog->records_succeeded + 1
                ]);
            } catch (\Exception $e) {
                $importLog->update([
                    'records_failed' => $importLog->records_failed + 1,
                    'error_details' => array_merge($importLog->error_details ?? [], [[
                        'record' => $data,
                        'error' => 'Database error: ' . $e->getMessage()
                    ]])
                ]);
            }
        }
    }
}
