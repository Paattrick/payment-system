<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'middle_name',
        'last_name',
        'suffix_name',
        'lrn',
        'birthday',
        'contact_number',
        'gender',
        'grade_id',
        'section',
        'address',
        'password',
        'status',
        'student_fees',
        'active_school_year_id',
        'enrolled_school_years'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'student_fees' => 'json',
        'address' => 'array',
        'password' => 'hashed',
        'enrolled_school_years' => 'array'
    ];

    public function fee()
    {
        return $this->belongsToMany(User::class);
    }
}