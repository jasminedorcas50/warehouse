<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'provider_id',
        'visit_date',
        'visit_type',
        'chief_complaint',
        'diagnosis',
        'treatment_plan',
        'prescription',
        'notes',
        'vitals',
        'lab_results',
        'status'
    ];

    protected $casts = [
        'visit_date' => 'date',
        'vitals' => 'array',
        'lab_results' => 'array',
        'prescription' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function analytics()
    {
        return $this->hasMany(AnalyticsData::class, 'source_id')
            ->where('source_type', 'medical_record');
    }
}
