<?php

namespace App\Exports;

use App\Models\Admin\Absence;
use Maatwebsite\Excel\Concerns\FromCollection;

class AbsenceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Absence::all();
    }
}
