<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'bio',
        'experience_years',
        'specialization',
        'dob',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
