<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $table = 'exams_questions';
    
    protected $guarded = [];

    public $casts = [
        'body' => 'json'
    ];

    public function section()
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id');
    }

    public function answerData()
    {
        return $this->hasOne(ExamsAnswers::class, 'exams_question_id');
    }
}
