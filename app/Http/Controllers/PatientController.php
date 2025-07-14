<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::with('medicalRecords')
            ->orderByRaw('CAST(patient_id AS UNSIGNED) ASC')
            ->get();

        if ($request->expectsJson()) {
            return response()->json($patients);
        }

        return view('patients.index', compact('patients'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $patients = Patient::where('patient_id', 'like', "%{$query}%")
            ->orWhere('age', 'like', "%{$query}%")
            ->orWhere('gender', 'like', "%{$query}%")
            ->orWhere('condition', 'like', "%{$query}%")
            ->orWhere('procedure', 'like', "%{$query}%")
            ->orWhere('cost', 'like', "%{$query}%")
            ->orWhere('length_of_stay', 'like', "%{$query}%")
            ->orWhere('readmission', 'like', "%{$query}%")
            ->orWhere('outcome', 'like', "%{$query}%")
            ->orWhere('satisfaction', 'like', "%{$query}%")
            ->orderByRaw('CAST(patient_id AS UNSIGNED) ASC')
            ->get();

        return response()->json($patients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'age' => 'nullable|integer|min:0|max:120',
            'gender' => 'nullable|in:male,female,other',
            'condition' => 'nullable|string|max:255',
            'procedure' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'length_of_stay' => 'nullable|integer|min:0',
            'readmission' => 'nullable|boolean',
            'outcome' => 'nullable|string|max:255',
            'satisfaction' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Generate unique patient ID
        $request->merge(['patient_id' => 'PAT' . str_pad(Patient::count() + 1, 6, '0', STR_PAD_LEFT)]);

        $patient = Patient::create($request->all());

        return response()->json($patient, 201);
    }

    public function show(Patient $patient)
    {
        $patient->load(['medicalRecords' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        if (request()->expectsJson()) {
            return response()->json($patient);
        }

        return view('patients.show', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'age' => 'nullable|integer|min:0|max:120',
            'gender' => 'nullable|in:male,female,other',
            'condition' => 'nullable|string|max:255',
            'procedure' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'length_of_stay' => 'nullable|integer|min:0',
            'readmission' => 'nullable|boolean',
            'outcome' => 'nullable|string|max:255',
            'satisfaction' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient->update($request->all());

        return response()->json($patient);
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->json(null, 204);
    }

    public function medicalHistory(Patient $patient)
    {
        $medicalHistory = $patient->medicalRecords()
            ->with(['provider'])
            ->orderBy('visit_date', 'desc')
            ->get();

        return response()->json([
            'patient' => $patient,
            'medical_history' => $medicalHistory
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'gender_distribution' => Patient::selectRaw('gender, COUNT(*) as count')
                ->groupBy('gender')
                ->get(),
            'age_distribution' => Patient::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN "Under 18"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 30 THEN "18-30"
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50 THEN "31-50"
                    ELSE "Over 50"
                END as age_group,
                COUNT(*) as count
            ')->groupBy('age_group')->get(),
            'recent_registrations' => Patient::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get()
        ];

        return response()->json($stats);
    }
}
