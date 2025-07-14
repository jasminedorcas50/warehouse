<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\ClinicalGuideline;
use App\Models\TreatmentRecommendation;
use Illuminate\Support\Collection;

class ClinicalDecisionSupportService
{
    public function generateTreatmentRecommendations(Patient $patient, MedicalRecord $record): TreatmentRecommendation
    {
        // Get relevant clinical guidelines
        $guidelines = ClinicalGuideline::where('condition_code', $record->diagnosis_code)->first();

        if (!$guidelines) {
            throw new \Exception('No clinical guidelines found for this condition');
        }

        // Analyze patient history and current condition
        $riskFactors = $this->assessRiskFactors($patient, $record);
        $contraindications = $this->checkContraindications($patient, $guidelines);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($guidelines, $riskFactors, $contraindications);

        // Create and return treatment recommendation
        return TreatmentRecommendation::create([
            'patient_id' => $patient->id,
            'medical_record_id' => $record->id,
            'condition_code' => $record->diagnosis_code,
            'recommended_treatments' => $recommendations['recommended'],
            'alternative_treatments' => $recommendations['alternatives'],
            'risk_assessment' => $riskFactors,
            'rationale' => $recommendations['rationale'],
            'status' => 'pending'
        ]);
    }

    private function assessRiskFactors(Patient $patient, MedicalRecord $record): array
    {
        // Implement risk factor assessment logic
        return [
            'patient_age' => $patient->age,
            'comorbidities' => $patient->medical_history,
            'allergies' => $patient->allergies,
            'current_medications' => $record->prescription,
            'vitals' => $record->vitals
        ];
    }

    private function checkContraindications(Patient $patient, ClinicalGuideline $guideline): array
    {
        // Implement contraindication checking logic
        return [
            'allergies' => array_intersect($patient->allergies, $guideline->contraindications),
            'interactions' => $this->checkDrugInteractions($patient, $guideline)
        ];
    }

    private function checkDrugInteractions(Patient $patient, ClinicalGuideline $guideline): array
    {
        $currentMedications = $patient->medical_records->last()->prescription ?? [];
        $recommendedMedications = $guideline->treatment_options['medications'] ?? [];

        // Check for potential interactions between current and recommended medications
        return array_filter($recommendedMedications, function($med) use ($currentMedications) {
            return !empty(array_intersect($med['interactions'] ?? [], $currentMedications));
        });
    }

    private function filterRecommendedTreatments(array $treatmentOptions, array $contraindications): array
    {
        return array_filter($treatmentOptions, function($treatment) use ($contraindications) {
            return empty(array_intersect($treatment['contraindications'] ?? [], $contraindications['allergies'] ?? []));
        });
    }

    private function generateAlternativeTreatments(ClinicalGuideline $guideline, array $contraindications): array
    {
        $alternatives = $guideline->alternative_treatments ?? [];
        return array_filter($alternatives, function($treatment) use ($contraindications) {
            return empty(array_intersect($treatment['contraindications'] ?? [], $contraindications['allergies'] ?? []));
        });
    }

    private function generateRecommendations(ClinicalGuideline $guideline, array $riskFactors, array $contraindications): array
    {
        // Implement recommendation generation logic
        return [
            'recommended' => $this->filterRecommendedTreatments($guideline->treatment_options, $contraindications),
            'alternatives' => $this->generateAlternativeTreatments($guideline, $contraindications),
            'rationale' => $this->generateRationale($guideline, $riskFactors)
        ];
    }

    private function generateRationale(ClinicalGuideline $guideline, array $riskFactors): string
    {
        $rationale = "Based on clinical guidelines for {$guideline->condition_name}, ";
        $rationale .= "considering patient age ({$riskFactors['patient_age']}), ";
        $rationale .= "comorbidities: " . implode(', ', $riskFactors['comorbidities'] ?? []) . ", ";
        $rationale .= "and current medications: " . implode(', ', $riskFactors['current_medications'] ?? []);

        return $rationale;
    }
}