<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Model;

class AnalysisResult extends Model
{
    protected $fillable = [
        'input_data',
        'output_data',
        'confidence_score',
        'model_version'
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array'
    ];
}
