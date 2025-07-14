<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HospitalDataset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'source_name',
        'data_type',
        'data_period_start',
        'data_period_end',
        'format',
        'status',
        'metadata',
        'file_path',
        'record_count',
        'import_notes',
        'imported_at'
    ];

    protected $casts = [
        'data_period_start' => 'date',
        'data_period_end' => 'date',
        'metadata' => 'array',
        'imported_at' => 'datetime'
    ];

    public function importLogs()
    {
        return $this->hasMany(DatasetImportLog::class);
    }

    public function getLatestImportLog()
    {
        return $this->importLogs()->latest()->first();
    }

    public function getImportStatusAttribute()
    {
        $latestLog = $this->getLatestImportLog();
        if (!$latestLog) {
            return 'No import attempts';
        }
        return $latestLog->status;
    }

    public function getSuccessRateAttribute()
    {
        $latestLog = $this->getLatestImportLog();
        if (!$latestLog || $latestLog->records_processed === 0) {
            return 0;
        }
        return round(($latestLog->records_succeeded / $latestLog->records_processed) * 100, 2);
    }
}
