<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Subject;
use App\Models\Admin\Level;
use App\Models\Admin\Teacher;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sections()
    {
        return $this->hasMany(ExamSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function attemps()
    {
        return $this->hasMany(ExamsEnterAttemps::class);
    }
}
