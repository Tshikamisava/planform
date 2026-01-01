<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dcr extends Model
{
    protected $fillable = [
        'dcr_id',
        'author_id',
        'recipient_id',
        'request_type',
        'reason_for_change',
        'due_date',
        'status',
        'impact',
        'impact_rating',
        'impact_summary',
        'recommendations',
        'decision_maker_id',
        'attachments',
        'auto_escalated',
        'escalated_at',
        'escalated_to',
    ];

    protected $attributes = [
        'status' => 'Pending',
        'impact_rating' => 'Low',
        'auto_escalated' => false,
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'escalated_at' => 'datetime',
        'auto_escalated' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function decisionMaker()
    {
        return $this->belongsTo(User::class, 'decision_maker_id');
    }
}
