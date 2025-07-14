<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceAllocation extends Model
{
    protected $fillable = [
        'resource_type',
        'department',
        'current_capacity',
        'optimal_capacity',
        'utilization_metrics',
        'recommendations',
        'assessment_date'
    ];

    protected $casts = [
        'utilization_metrics' => 'array',
        'recommendations' => 'array',
        'assessment_date' => 'date'
    ];
}