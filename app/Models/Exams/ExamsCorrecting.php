<?php

namespace App\Models\Exams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamsCorrecting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class, 'exams_question_id');
    }

    protected $casts = [
        'body' => 'json'
    ];
}
