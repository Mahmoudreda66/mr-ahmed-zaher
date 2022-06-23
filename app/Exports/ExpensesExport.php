<?php

namespace App\Exports;

use App\Models\Admin\Expenses;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExpensesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Expenses::all();
    }
}
