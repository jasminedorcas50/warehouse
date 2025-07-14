<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffingRecommendation extends Model
{
    protected $fillable = [
        'department',
        'date',
        'required_staff',
        'available_staff',
        'recommendations',
        'status'
    ];

    protected $casts = [
        'required_staff' => 'array',
        'available_staff' => 'array',
        'recommendations' => 'array',
        'date' => 'date'
    ];
}