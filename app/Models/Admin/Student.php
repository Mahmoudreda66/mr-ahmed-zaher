<?php

namespace App\Models\Admin;

use App\Models\User;
use App\Models\Exams\ExamsEnterAttemps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes;

    public function level ()
    {
        return $this->belongsTo(Level::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->hasOne(StudentTeachers::class);
    }

    public function absence_list()
    {
        return $this->hasMany(Absence::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expenses::class);
    }

    public function attemps()
    {
        return $this->hasMany(ExamsEnterAttemps::class);
    }

    public function groups()
    {
        return $this->hasMany(LessonsGroupsStudent::class);
    }

    protected $guarded = [];
}
