<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'age',
        'gender',
        'condition',
        'procedure',
        'cost',
        'length_of_stay',
        'readmission',
        'outcome',
        'satisfaction'
    ];

    protected $casts = [
        'age' => 'integer',
        'cost' => 'decimal:2',
        'length_of_stay' => 'integer',
        'readmission' => 'boolean',
        'satisfaction' => 'integer'
    ];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        } elseif ($this->first_name) {
            return $this->first_name;
        } elseif ($this->last_name) {
            return $this->last_name;
        }
        return 'Unknown';
    }
}
