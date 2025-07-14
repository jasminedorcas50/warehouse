<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        return MedicalRecord::with('patient')->paginate(15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'diagnosis_code' => 'required|string',
            'prescription' => 'nullable|array',
            'vitals' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        return MedicalRecord::create($validated);
    }

    public function show(MedicalRecord $medicalRecord)
    {
        return $medicalRecord->load('patient');
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'diagnosis_code' => 'sometimes|string',
            'prescription' => 'nullable|array',
            'vitals' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $medicalRecord->update($validated);
        return $medicalRecord;
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return response()->noContent();
    }

    public function statistics()
    {
        return [
            'total_records' => MedicalRecord::count(),
            'records_by_month' => MedicalRecord::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->get(),
            'common_diagnoses' => MedicalRecord::selectRaw('diagnosis_code, COUNT(*) as count')
                ->groupBy('diagnosis_code')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
        ];
    }
}
