<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityMetric extends Model
{
    protected $fillable = [
        'metric_name',
        'category',
        'current_value',
        'target_value',
        'benchmark_value',
        'trend_data',
        'improvement_areas',
        'assessment_date'
    ];

    protected $casts = [
        'trend_data' => 'array',
        'improvement_areas' => 'array',
        'assessment_date' => 'date'
    ];
}