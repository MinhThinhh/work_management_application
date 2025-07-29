<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiMetric;

class KpiMetricsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metrics = [
            [
                'name' => 'Task Completion Rate',
                'description' => 'Percentage of tasks completed on time',
                'metric_type' => 'task_completion',
                'weight' => 25.00,
                'unit' => 'percentage',
                'min_value' => 0.00,
                'max_value' => 100.00,
                'is_active' => true
            ],
            [
                'name' => 'Quality Score',
                'description' => 'Average quality rating of completed tasks',
                'metric_type' => 'quality_score',
                'weight' => 30.00,
                'unit' => 'score',
                'min_value' => 0.00,
                'max_value' => 5.00,
                'is_active' => true
            ],
            [
                'name' => 'Deadline Adherence',
                'description' => 'Percentage of tasks completed before deadline',
                'metric_type' => 'deadline_adherence',
                'weight' => 25.00,
                'unit' => 'percentage',
                'min_value' => 0.00,
                'max_value' => 100.00,
                'is_active' => true
            ],
            [
                'name' => 'Collaboration Score',
                'description' => 'Rating based on teamwork and communication',
                'metric_type' => 'collaboration',
                'weight' => 20.00,
                'unit' => 'score',
                'min_value' => 0.00,
                'max_value' => 5.00,
                'is_active' => true
            ],
            [
                'name' => 'Innovation Index',
                'description' => 'Custom metric for innovative solutions and ideas',
                'metric_type' => 'custom',
                'weight' => 10.00,
                'unit' => 'score',
                'min_value' => 0.00,
                'max_value' => 10.00,
                'is_active' => true
            ],
            [
                'name' => 'Customer Satisfaction',
                'description' => 'Customer feedback and satisfaction rating',
                'metric_type' => 'custom',
                'weight' => 15.00,
                'unit' => 'score',
                'min_value' => 0.00,
                'max_value' => 5.00,
                'is_active' => true
            ]
        ];

        foreach ($metrics as $metric) {
            KpiMetric::create($metric);
        }
    }
}
