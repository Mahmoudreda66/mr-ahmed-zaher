<?php

namespace App\DataTables;

use App\Models\Exams\Exam;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExamDataTable extends DataTable
{
    public $isTeacher;

    public function isTeacher ($isTeacher) {
        $this->isTeacher = $isTeacher;
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
            ->editColumn('date', function ($item) {
                return date('Y-m-d H:m', strtotime($item->date));
            })
            ->editColumn('exam_type', function ($item) {
                if($item->exam_type == 0){
                    return 'إلكتروني';
                }else{
                    return 'ورقي';
                }
            })
            ->editColumn('subject.name_ar', function ($item) {
                return 'ال' . $item->subject->name_ar;
            })
            ->addColumn('attemps', function ($item) {
                return $item->attemps->count() . ' طالب';
            })
            ->addColumn('action', function ($item) {
                $html = '';

                if(auth()->user()->hasPermission('delete-exam')){
                    $examData = json_encode([
                        'id' => $item->id,
                        'name' => 'إختبار ال' . $item->subject->name_ar . ' ' . $item->level->name_ar,
                    ]);

                    $html .= "<i onclick='deleteExam(" . $examData . ")' class='fas fa-trash text-danger cursor-pointer'></i>";
                }

                if(auth()->user()->hasPermission('view-exam')){
                    $html .= '&nbsp; <a href="' . route('exams.show', $item->id) . '"><i class="fas fa-eye text-success"></i></a>';
                }

                if($item->exam_type == 0 && auth()->user()->hasPermission('toggle-exam')){
                    $html .= '&nbsp;';
                    $examData = json_encode([
                        'id' => $item->id,
                        'status' => $item->status,
                        'name' => 'إختبار ال' . $item->subject->name_ar . ' ' . $item->level->name_ar,
                    ]);

                    if($item->status){
                        $html .= "<i onclick='toggleBtns(" . $examData . ")' class='cursor-pointer fas fa-ban text-warning' title='إنهاء الإختبار'></i>";
                    }else{
                        $html .= "<i onclick='toggleBtns(" . $examData . ")' class='fas cursor-pointer fa-check text-info' title='بدء الإختبار'></i>";
                    }
                }

                $html .= '&nbsp; <div class="dropdown d-inline-block dropup" style="top: -1px;"> <a class="btn table m-0 p-0 bg-transparent shadow-0 border-0 dropdown-toggle" type="a" id="dropdownMenua1" data-bs-toggle="dropdown" aria-expanded="false"> <i title="بيانات الإختبار" class="fas fa-info text-primary"></i> </a> <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">';
                if(auth()->user()->hasPermission('exams-attemps')){
                    $html .= '<li><a class="dropdown-item" href="' . route('exams-attemps.index', ['level' => $item->level_id, 'exam' => $item->id]) . '">سجل الدخول </a> </li>';
                }

                if(auth()->user()->hasPermission('exams-marks')){
                    $html .= '<li> <a class="dropdown-item" href="' . route('exams-marks.index', ['level' => $item->level_id, 'exam' => $item->id]) . '">درجات الإختبار</a></li>';
                }

                $html .= '<li onclick="showTop10(' . $item->id . ')"><div class="dropdown-item cursor-pointer top10_btn">العشرة الأوائل</div></li></ul></div>';

                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Exam $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Exam $model)
    {
        if($this->isTeacher){
            return $model
            ->newQuery()
            ->where('teacher_id', auth()->user()->teacher->id)
            ->with('subject', 'level', 'teacher.profile', 'attemps');
        }else{
            return $model
            ->newQuery()
            ->with('subject', 'level', 'teacher.profile', 'attemps');
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('exam-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->lengthMenu([50])
                    ->language([
                        'url' => route('dataTableTranslation')
                    ])
                    ->buttons(
                        Button::make('excel')->text('<i class="fas fa-download"></i> تصدير EXCEL'),
                        Button::make('print'),
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
            Column::make('id')->title('#ID'),
            Column::make('subject_id')
            ->data('subject.name_ar')
            ->title('المادة'),
            Column::make('teacher.profile.name')
            ->orderable(false)
            ->title('المعلم'),
            Column::make('level_id')
            ->data('level.name_ar')
            ->title('الصف'),
            Column::make('date')->title('التوقيت'),
            Column::make('exam_type')->title('النوع'),
            Column::make('attemps')
            ->orderable(false)
            ->title('دخل الإختبار'),
            Column::make('duration')->title('المدة'),
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
        return 'Exam_' . date('YmdHis');
    }
}
