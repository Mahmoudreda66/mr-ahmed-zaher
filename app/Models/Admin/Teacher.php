<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\Exams\Exam;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Teacher extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "subject_id",
        "levels",
        "profile_id"
    ];

    public function profile()
    {
        return $this->belongsTo(User::class, 'profile_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function absences()
    {
        return $this->hasMany(TeachersAbsence::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    protected $casts = [
        'levels' => 'array'
    ];
}
