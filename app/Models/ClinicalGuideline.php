<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalGuideline extends Model
{
    protected $fillable = [
        'condition_code',
        'condition_name',
        'description',
        'recommended_tests',
        'treatment_options',
        'risk_factors',
        'contraindications',
        'source',
        'last_updated'
    ];

    protected $casts = [
        'recommended_tests' => 'array',
        'treatment_options' => 'array',
        'risk_factors' => 'array',
        'contraindications' => 'array',
        'last_updated' => 'date'
    ];

    public function recommendations()
    {
        return $this->hasMany(TreatmentRecommendation::class, 'condition_code', 'condition_code');
    }
}
