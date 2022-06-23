<?php

namespace App\DataTables;

use App\Models\Admin\Student;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class StudentDataTable extends DataTable
{

    function getUser($id) {
        $user = \App\Models\User::find($id);

        if(!$user){
            return 'غير معروف';
        }else{
            return $user->name;
        }
    }

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
            ->editColumn('hasPaid', function ($item) {
                $expenses = collect(\Illuminate\Support\Facades\DB::select("SELECT
                id, created_at, user_id, money
                FROM
                expenses
                WHERE
                student_id = ? AND month = EXTRACT(MONTH FROM TIMESTAMP '" . now() . "') AND deleted_at IS NULL", [$item->id]))->first();

                $studentData = json_encode([
                    'name' => $item->name,
                    'id' => $item->id,
                    'level_id' => $item->level->name_en
                ]);

                if ($expenses) {
                    if(auth()->user()->hasPermission('add-student-expenses')){
                        return "<span
                        ondblclick='payExpenses(" . $studentData . ")'
                        class='badge user-select-none cursor-pointer bg-success'
                        title='تاريخ الدفع: " . date('Y-m-d', strtotime($expenses->created_at)) . "'>
                        تم دفع <span>" . $expenses->money . " جـ. </span>
                        <span>لـ  " . $this->getUser($expenses->user_id) . "</span>
                        </span>";
                    }

                    return "<span
                        class='badge bg-success'
                        title='تاريخ الدفع: " . date('Y-m-d', strtotime($expenses->created_at)) . "'>
                        تم دفع <span>" . $expenses->money . " جـ. </span>
                        <span>لـ  " . $this->getUser($expenses->user_id) . "</span>
                        </span>";
                } else {
                    if(auth()->user()->hasPermission('add-student-expenses')){
                        return "<span ondblclick='payExpenses(" . $studentData . ")' title='إضغط ضغطتين لتغيير حالة الدفع' class='cursor-pointer badge user-select-none bg-warning'>لم يتم الدفع</span>";
                    }
                    else{
                        return "<span class='badge bg-warning'>لم يتم الدفع</span>";
                    }
                }
            })
            ->addColumn('action', function ($student) {
                $studentData = json_encode([
                    'name' => $student->name,
                    'id' => $student->id,
                    'level_id' => $student->level->name_ar
                ]);

                $html = '<i class="fas fa-print cursor-pointer print-barcode" onclick="printBarcode(' . $student->id . ')"></i>&nbsp;';

                if(auth()->user()->hasPermission('edit-student')){
                    $html .= '<a href="' . route("students.edit", $student->id) . '"><i class="fas fa-edit text-success"></i></a>&nbsp;';
                }

                if(auth()->user()->hasPermission('delete-student')){
                    $html .= "<i class='fa fa-trash cursor-pointer text-danger' onclick='deleteStudent(" . $studentData . ")'></i>&nbsp;";
                }

                if(auth()->user()->hasPermission('show-student')){
                    $html .= '<a href="' . route('students.show', $student->id) . '"><i class="fa fa-eye text-success"></i></a>';
                }

                return $html;
            })
            ->rawColumns(['hasPaid', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Student $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Student $model)
    {
        return $model->newQuery()->with('level');
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
            Column::make('hasPaid')
            ->title('مصروفات الشهر الجاري')
            ->searchable(false)
            ->orderable(false),
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
        return 'Student_' . date('YmdHis');
    }
}
