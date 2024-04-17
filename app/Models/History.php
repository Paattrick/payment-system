<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'collector_id',
        'name',
        'file',
        'meta',
        'status',
        'reference',
        'note',
        'mode_of_payment',
        'school_year_id'
    ];

    protected $casts = [
        'meta' => 'json',
    ];
}