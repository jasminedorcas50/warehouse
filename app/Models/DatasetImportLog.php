<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatasetImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_dataset_id',
        'status',
        'message',
        'records_processed',
        'records_succeeded',
        'records_failed',
        'error_details'
    ];

    protected $casts = [
        'error_details' => 'array'
    ];

    public function dataset()
    {
        return $this->belongsTo(HospitalDataset::class, 'hospital_dataset_id');
    }
}
