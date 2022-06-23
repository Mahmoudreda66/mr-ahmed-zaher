<?php

namespace App\Exports;

use App\Models\Admin\LessonsGroups;
use Maatwebsite\Excel\Concerns\FromCollection;

class LessonsGroupsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LessonsGroups::all();
    }
}
