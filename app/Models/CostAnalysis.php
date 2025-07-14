<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostAnalysis extends Model
{
    protected $fillable = [
        'department',
        'cost_category',
        'current_cost',
        'budgeted_cost',
        'cost_breakdown',
        'savings_opportunities',
        'analysis_date'
    ];

    protected $casts = [
        'cost_breakdown' => 'array',
        'savings_opportunities' => 'array',
        'analysis_date' => 'date'
    ];
}