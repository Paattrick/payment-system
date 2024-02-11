<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'suffix_name',
        'lrn',
        'birthday',
        'age',
        'contact_number',
        'gender',
        'grade',
        'section',
        'province',
        'municipality',
        'barangay',
        'password',
        'id_number',
    ];
}
