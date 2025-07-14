<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentRecommendation extends Model
{
    protected $fillable = [
        'patient_id',
        'medical_record_id',
        'condition_code',
        'recommended_treatments',
        'alternative_treatments',
        'risk_assessment',
        'rationale',
        'status'
    ];

    protected $casts = [
        'recommended_treatments' => 'array',
        'alternative_treatments' => 'array',
        'risk_assessment' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function guideline()
    {
        return $this->belongsTo(ClinicalGuideline::class, 'condition_code', 'condition_code');
    }
}