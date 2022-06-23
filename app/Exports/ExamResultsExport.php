<?php

namespace App\Exports;

use App\Models\Exams\ExamsResults;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamResultsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ExamsResults::all();
    }
}
