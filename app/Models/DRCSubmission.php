<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DRCSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'drc_id',
        'submission_date',
        'author',
        'recipient',
        'details',
    ];
}
