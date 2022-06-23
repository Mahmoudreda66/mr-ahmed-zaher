<?php

namespace App\Exports;

use App\Models\Admin\LessonsGroupsStudent;
use Maatwebsite\Excel\Concerns\FromCollection;

class LessonsGroupsStudentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LessonsGroupsStudent::all();
    }
}
