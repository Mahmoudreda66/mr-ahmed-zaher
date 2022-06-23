<?php

namespace App\Exports;

use App\Models\Exams\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Exam::all();
    }
}
