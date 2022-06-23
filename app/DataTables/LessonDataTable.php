<?php

namespace App\DataTables;

use App\Models\Admin\Lesson;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class LessonDataTable extends DataTable
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
            ->editColumn('subject.name_ar', function ($item) {
                return 'ال' . $item->subject->name_ar;
            })
            ->addColumn('action', function ($item) {
                $html = '';

                if(auth()->user()->hasPermission('delete-lesson')){
                    $lessonData = json_encode([
                        'id' => $item->id,
                        'name' => 'ال' . $item->subject->name_ar . ' ' . $item->level->name_ar,
                    ]);

                    $html .= "<i onclick='deleteItem(" . $lessonData . ")' class='fas fa-trash text-danger cursor-pointer'></i> &nbsp;";
                }

                $html .= '<a href="' . route('lessons.show', $item->id) . '"><i class="fas fa-eye text-success"></i></a>';

                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Lesson $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Lesson $model)
    {
        return $model->newQuery()->with('teacher.profile', 'subject', 'level');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('lesson-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->language([
                        'url' => route('dataTableTranslation')
                    ])
                    ->lengthMenu([50])
                    ->orderBy(0)
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
            Column::make('level_id')
            ->data('level.name_ar')
            ->title('الصف'),
            Column::make('teacher.profile.name')
            ->orderable(false)
            ->title('المعلم'),
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
        return 'Lesson_' . date('YmdHis');
    }
}
