<?php

namespace App\DataTables;

use App\Models\Admin\Student;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StudentsConfirmationDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->editColumn('division', function ($item) {
            if($item->division === 0){
                return 'علمي';
            }else if($item->division === 1){
                return 'أدبي';
            }
            
            return 'شعبة عامة';
        })
        ->editColumn('edu_type', function ($item) {
            return $item->edu_type === 0 ? 'عربي' : ($item->edu_type == 1 ? 'لغات' : 'غير معروف');
        })
        ->editColumn('mobile', function ($item) {
            return $item->mobile ?? 'لا يوجد';
        })
        ->addColumn('action', function ($student) {
            $html = '';

            if(auth()->user()->hasPermission('show-student')){
                $html .= "<i class='fa fa-check cursor-pointer text-info' onclick='confirmStudent(" . $student->id . ")'></i>&nbsp;&nbsp;";
            }

            if(auth()->user()->hasPermission('delete-student')){
                $html .= "<i class='fa fa-trash cursor-pointer text-danger' onclick='deleteStudent(" . $student->id . ")'></i>";
            }

            return $html;
        })
        ->rawColumns(['hasPaid', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\StudentsConfirmation $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Student $model)
    {
        return $model->with('level')->onlyTrashed()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('student-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->language(['url' => route('dataTableTranslation')])
                    ->lengthMenu([50])
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> إضافة طالب'),
                        Button::make('excel')->text('<i class="fas fa-download"></i> تصدير EXCEL'),
                        Button::make('print')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id')->data('id')->title('#ID'),
            Column::make('name')->title('الإسم'),
            Column::make('level_id')
            ->data('level.name_ar')
            ->title('المرحلة'),
            Column::make('division')->title('الشعبة'),
            Column::make('edu_type')->title('نوع التعليم'),
            Column::make('mobile')->title('رقم الهاتف'),
            Column::make('code')->title('كود الطالب'),
            Column::computed('action')
                ->title('خيارات')
                ->exportable(false)
                ->printable(false)
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'StudentsConfirmation_' . date('YmdHis');
    }
}
