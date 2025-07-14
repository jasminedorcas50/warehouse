<?php

namespace App\Http\Controllers;

use App\Models\HospitalDataset;
use App\Models\DatasetImportLog;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Jobs\ImportDatasetJob;

class DatasetController extends Controller
{
    protected $dataset;

    public function index()
    {
        $datasets = HospitalDataset::with('importLogs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('datasets.index', compact('datasets'));
    }

    public function create()
    {
        return view('datasets.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'source_name' => 'required|string|max:255',
            'data_type' => 'required|string|in:patient_records,billing,lab_results,pharmacy,imaging',
            'data_period_start' => 'required|date',
            'data_period_end' => 'required|date|after_or_equal:data_period_start',
            'format' => 'required|string|in:csv,json,xml',
            'dataset_file' => 'required|file|mimes:csv,json,xml|max:10240', // 10MB max
            'metadata' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('dataset_file');
        $path = $file->store('datasets');

        $this->dataset = HospitalDataset::create([
            'name' => $request->name,
            'source_name' => $request->source_name,
            'data_type' => $request->data_type,
            'data_period_start' => $request->data_period_start,
            'data_period_end' => $request->data_period_end,
            'format' => $request->format,
            'file_path' => $path,
            'metadata' => $request->metadata,
            'status' => 'processing'
        ]);

        // Create initial import log
        $log = new DatasetImportLog([
            'status' => 'processing',
            'message' => 'Starting import process',
            'records_processed' => 0,
            'records_succeeded' => 0,
            'records_failed' => 0
        ]);
        $this->dataset->importLogs()->save($log);

        // Dispatch the import job
        ImportDatasetJob::dispatch($this->dataset);

        return redirect()->route('datasets.show', $this->dataset)
            ->with('success', 'Dataset uploaded and queued for import.');
    }

    public function show(HospitalDataset $dataset)
    {
        $dataset->load('importLogs');
        return view('datasets.show', compact('dataset'));
    }

    public function import(HospitalDataset $dataset)
    {
        if ($dataset->status === 'processing') {
            return back()->with('error', 'Dataset is already being processed.');
        }

        $this->dataset = $dataset;
        $this->dataset->update(['status' => 'processing']);

        // Create import log
        $log = new DatasetImportLog([
            'status' => 'processing',
            'message' => 'Starting import process',
            'records_processed' => 0,
            'records_succeeded' => 0,
            'records_failed' => 0
        ]);
        $this->dataset->importLogs()->save($log);

        // Dispatch the import job
        ImportDatasetJob::dispatch($this->dataset);

        return back()->with('success', 'Dataset import has been queued.');
    }

    public function destroy(HospitalDataset $dataset)
    {
        if ($dataset->file_path) {
            Storage::delete($dataset->file_path);
        }

        $dataset->delete();
        return redirect()->route('datasets.index')
            ->with('success', 'Dataset deleted successfully.');
    }

    public function download(HospitalDataset $dataset)
    {
        if (!$dataset->file_path || !Storage::exists($dataset->file_path)) {
            return back()->with('error', 'Dataset file not found.');
        }

        return Storage::download($dataset->file_path, $dataset->name . '.' . $dataset->format);
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataset_file' => 'required|file|mimes:csv,json,xml|max:10240', // 10MB max
            'format' => 'required|string|in:csv,json,xml'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('dataset_file');
        $content = file_get_contents($file->getPathname());
        $preview = [];
        $errors = [];

        try {
            switch ($request->format) {
                case 'csv':
                    $preview = $this->previewCsv($content, $errors);
                    break;
                case 'json':
                    $preview = $this->previewJson($content, $errors);
                    break;
                case 'xml':
                    $preview = $this->previewXml($content, $errors);
                    break;
            }

            return response()->json([
                'preview' => $preview,
                'errors' => $errors,
                'headers' => $request->format === 'csv' ? $preview['headers'] ?? [] : null,
                'sample_data' => $request->format === 'csv' ? $preview['sample_data'] ?? [] : $preview
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    protected function importRecord($data, DatasetImportLog $log)
    {
        if ($this->dataset->data_type === 'patient_records') {
            // Generate a unique patient ID if not provided
            if (!isset($data['patient_id'])) {
                $data['patient_id'] = 'PAT' . str_pad(Patient::count() + 1, 6, '0', STR_PAD_LEFT);
            }

            // Parse date of birth if provided
            $dateOfBirth = null;
            if (!empty($data['date_of_birth'])) {
                try {
                    $dateOfBirth = Carbon::parse($data['date_of_birth'])->toDateString();
                } catch (\Exception $e) {
                    // Log the date parsing error but continue with import
                    $currentErrors = $log->error_details ?? [];
                    $currentErrors[] = [
                        'record' => $data,
                        'error' => 'Invalid date format for date_of_birth'
                    ];

                    $log->update([
                        'error_details' => $currentErrors
                    ]);
                }
            }

            // Map the incoming data to our patient model fields
            $patientData = [
                'patient_id' => $data['patient_id'],
                'first_name' => $data['first_name'] ?? null,
                'last_name' => $data['last_name'] ?? null,
                'date_of_birth' => $dateOfBirth,
                'gender' => $data['gender'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'blood_type' => $data['blood_type'] ?? null,
                'medical_history' => isset($data['medical_history']) ? (is_array($data['medical_history']) ? $data['medical_history'] : [$data['medical_history']]) : [],
                'allergies' => isset($data['allergies']) ? (is_array($data['allergies']) ? $data['allergies'] : [$data['allergies']]) : []
            ];

            try {
                // Create or update the patient record
                Patient::updateOrCreate(
                    ['patient_id' => $patientData['patient_id']],
                    $patientData
                );

                // Update records_succeeded using safe pattern
                $log->records_succeeded = $log->records_succeeded + 1;
                $log->save();
            } catch (\Exception $e) {
                // Update records_failed using safe pattern
                $log->records_failed = $log->records_failed + 1;

                // Handle error_details using safe pattern
                $errors = $log->error_details ?? [];
                $errors[] = [
                    'record' => $data,
                    'error' => 'Database error: ' . $e->getMessage()
                ];
                $log->error_details = $errors;
                $log->save();
            }
        }
    }

    protected function processCsv($content, DatasetImportLog $log)
    {
        $rows = array_map('str_getcsv', explode("\n", $content));
        $headers = array_map('trim', array_map('strtolower', array_shift($rows)));

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if (count($row) !== count($headers)) {
                    // Update records_failed using safe pattern
                    $log->records_failed = $log->records_failed + 1;

                    // Handle error_details using safe pattern
                    $errors = $log->error_details ?? [];
                    $errors[] = [
                        'row' => $index + 2,
                        'error' => 'Invalid number of columns'
                    ];
                    $log->error_details = $errors;
                    $log->save();

                    continue;
                }

                $data = array_combine($headers, $row);
                $this->importRecord($data, $log);

                // Update records_processed using safe pattern
                $log->records_processed = $log->records_processed + 1;
                $log->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function processJson($content, DatasetImportLog $log)
    {
        $records = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON format: ' . json_last_error_msg());
        }

        if (!is_array($records)) {
            throw new \Exception('JSON data must be an array of records');
        }

        DB::beginTransaction();
        try {
            foreach ($records as $index => $data) {
                if (!is_array($data)) {
                    $log->records_failed++;
                    $log->error_details = array_merge($log->error_details ?? [], [
                        [
                            'record' => $index,
                            'error' => 'Invalid record format'
                        ]
                    ]);
                    $log->save();
                    continue;
                }

                $this->importRecord($data, $log);
                $log->records_processed++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function processXml($content, DatasetImportLog $log)
    {
        $xml = simplexml_load_string($content);
        if (!$xml) {
            throw new \Exception('Invalid XML format: ' . libxml_get_last_error()->message);
        }

        DB::beginTransaction();
        try {
            foreach ($xml->children() as $index => $record) {
                $data = json_decode(json_encode($record), true);
                if (!is_array($data)) {
                    $log->records_failed++;
                    $log->error_details = array_merge($log->error_details ?? [], [
                        [
                            'record' => $index,
                            'error' => 'Invalid record format'
                        ]
                    ]);
                    $log->save();
                    continue;
                }

                $this->importRecord($data, $log);
                $log->records_processed++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function previewCsv($content, &$errors)
    {
        $rows = array_map('str_getcsv', explode("\n", $content));
        $headers = array_map('trim', array_map('strtolower', array_shift($rows)));

        // Get sample data (first 5 rows)
        $sampleData = [];
        $rowCount = 0;
        foreach ($rows as $row) {
            if (empty($row[0])) continue; // Skip empty rows
            if ($rowCount >= 5) break; // Only get 5 rows

            if (count($row) !== count($headers)) {
                $errors[] = "Row " . ($rowCount + 2) . " has incorrect number of columns";
                continue;
            }

            $sampleData[] = array_combine($headers, $row);
            $rowCount++;
        }

        return [
            'headers' => $headers,
            'sample_data' => $sampleData,
            'total_rows' => count(array_filter($rows))
        ];
    }

    protected function previewJson($content, &$errors)
    {
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = 'Invalid JSON format: ' . json_last_error_msg();
            return [];
        }

        if (!is_array($data)) {
            $errors[] = 'JSON data must be an array of records';
            return [];
        }

        // Get sample data (first 5 records)
        $sampleData = array_slice($data, 0, 5);

        return [
            'sample_data' => $sampleData,
            'total_records' => count($data)
        ];
    }

    protected function previewXml($content, &$errors)
    {
        $xml = simplexml_load_string($content);
        if (!$xml) {
            $errors[] = 'Invalid XML format: ' . libxml_get_last_error()->message;
            return [];
        }

        // Get sample data (first 5 records)
        $sampleData = [];
        $count = 0;
        foreach ($xml->children() as $record) {
            if ($count >= 5) break;
            $sampleData[] = json_decode(json_encode($record), true);
            $count++;
        }

        return [
            'sample_data' => $sampleData,
            'total_records' => count($xml->children())
        ];
    }
}
