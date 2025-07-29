<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'metric_type',
        'weight',
        'unit',
        'min_value',
        'max_value',
        'is_active'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get KPI targets for this metric
     */
    public function kpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class, 'metric_id');
    }

    /**
     * Scope for active metrics
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific metric type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Get metric types
     */
    public static function getMetricTypes(): array
    {
        return [
            'task_completion' => 'Task Completion Rate',
            'quality_score' => 'Quality Score',
            'deadline_adherence' => 'Deadline Adherence',
            'collaboration' => 'Collaboration Score',
            'custom' => 'Custom Metric'
        ];
    }

    /**
     * Get metric type label
     */
    public function getMetricTypeLabel(): string
    {
        return self::getMetricTypes()[$this->metric_type] ?? $this->metric_type;
    }
}
