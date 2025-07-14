<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_date',
        'metric_type',
        'category',
        'subcategory',
        'value',
        'dimensions',
        'source',
        'description'
    ];

    protected $casts = [
        'data_date' => 'date',
        'value' => 'decimal:2',
        'dimensions' => 'array'
    ];

    // Scope for filtering by date range
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('data_date', [$startDate, $endDate]);
    }

    // Scope for filtering by metric type
    public function scopeMetricType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    // Scope for filtering by category
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Method to get aggregated data
    public static function getAggregatedData($metricType, $category, $startDate, $endDate, $groupBy = 'data_date')
    {
        return self::where('metric_type', $metricType)
            ->where('category', $category)
            ->whereBetween('data_date', [$startDate, $endDate])
            ->groupBy($groupBy)
            ->selectRaw("$groupBy, SUM(value) as total_value, AVG(value) as avg_value")
            ->get();
    }
}
