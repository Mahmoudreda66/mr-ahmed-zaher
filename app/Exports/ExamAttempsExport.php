<?php

namespace App\Exports;

use App\Models\Exams\ExamsEnterAttemps;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExamAttempsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ExamsEnterAttemps::all();
    }
}
