<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'current_school_year'
    ];

    protected $casts = [
        'current_school_year' => 'bool'
    ];
}