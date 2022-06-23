<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonsGroups extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'times' => 'array'
    ];

    public function students ()
    {
        return $this->belongsToMany(Student::class, 'lessons_groups_student');
    }

    public function lesson ()
    {
        return $this->belongsTo(Lesson::class);
    }
}
