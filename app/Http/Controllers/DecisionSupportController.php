<?php

namespace App\Http\Controllers;

use App\Services\ClinicalDecisionSupportService;
use App\Services\OperationalDecisionSupportService;
use App\Services\StrategicDecisionSupportService;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DecisionSupportController extends Controller
{
    protected $clinicalService;
    protected $operationalService;
    protected $strategicService;

    public function __construct(
        ClinicalDecisionSupportService $clinicalService,
        OperationalDecisionSupportService $operationalService,
        StrategicDecisionSupportService $strategicService
    ) {
        $this->clinicalService = $clinicalService;
        $this->operationalService = $operationalService;
        $this->strategicService = $strategicService;
    }

    // Clinical Decision Support Endpoints
    public function getTreatmentRecommendations(Request $request): JsonResponse
    {
        $patient = Patient::findOrFail($request->patient_id);
        $record = MedicalRecord::findOrFail($request->medical_record_id);

        $recommendation = $this->clinicalService->generateTreatmentRecommendations($patient, $record);

        return response()->json($recommendation);
    }

    // Operational Decision Support Endpoints
    public function optimizeResources(Request $request): JsonResponse
    {
        $allocation = $this->operationalService->optimizeResourceAllocation($request->department);

        return response()->json($allocation);
    }

    public function getStaffingRecommendations(Request $request): JsonResponse
    {
        $recommendation = $this->operationalService->generateStaffingRecommendations(
            $request->department,
            $request->date
        );

        return response()->json($recommendation);
    }

    // Strategic Decision Support Endpoints
    public function analyzeQuality(Request $request): JsonResponse
    {
        $metrics = $this->strategicService->analyzeQualityMetrics($request->category);

        return response()->json($metrics);
    }

    public function analyzeCosts(Request $request): JsonResponse
    {
        $analysis = $this->strategicService->analyzeCosts($request->department);

        return response()->json($analysis);
    }
}
