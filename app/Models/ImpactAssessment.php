<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_request_id',
        'assessor_id',
        'impact_rating',
        'business_impact',
        'technical_impact',
        'risk_level',
        'impact_summary',
        'affected_systems',
        'affected_users',
        'downtime_estimate',
        'rollback_plan',
        'testing_requirements',
        'recommendations',
        'conditions_for_approval',
        'required_resources',
        'assessment_date',
        'next_review_date',
        'confidence_level',
    ];

    protected $casts = [
        'affected_systems' => 'array',
        'affected_users' => 'array',
        'required_resources' => 'array',
        'assessment_date' => 'date',
        'next_review_date' => 'date',
        'confidence_level' => 'string',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(Dcr::class, 'change_request_id');
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function getOverallRiskAttribute()
    {
        $risks = [$this->impact_rating, $this->business_impact, $this->technical_impact, $this->risk_level];
        $riskLevels = ['Low' => 1, 'Medium' => 2, 'High' => 3, 'Critical' => 4];
        
        $maxRisk = max(array_map(fn($risk) => $riskLevels[$risk] ?? 1, $risks));
        
        return array_search($maxRisk, $riskLevels);
    }
}
