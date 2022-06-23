<?php

namespace App\DataTables;

use App\Models\Admin\Teacher;
use App\Models\Admin\Level;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TeacherDataTable extends DataTable
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
            ->editColumn('levels', function ($item) {
                $html = '';
                foreach (json_decode($item->levels, true) as $i => $level){
                    $html .= Level::find($level)->name_ar;
                    $html .= $i < count(json_decode($item->levels, true)) - 1 ? ' - ' : ' ';

                }                    
            
                return $html;
            })
            ->editColumn('subject.name_ar', function ($item) {
                return 'ال' . $item->subject->name_ar;
            })
            ->addColumn('lessons_count', function ($item) {
                return count($item->lessons);
            })
            ->addColumn('action', function ($item) {
                $html = '';

                if(auth()->user()->hasPermission('edit-teacher')){
                    $html .= '<a href="' . route('teachers.edit', $item->id) . '"><i class="fas fa-edit text-success"></i></a>&nbsp;';
                }

                if(auth()->user()->hasPermission('delete-teacher')){
                    $itemData = json_encode([
                        'id' => $item->id,
                        'name' => $item->profile->name
                    ]);

                    $html .= "&nbsp; <i onclick='deleteItem(" . $itemData . ")' class='fas fa-trash text-danger cursor-pointer'></i>";
                }

                $html .= '&nbsp; <a href="' . route('teachers.show', $item->id) . '"><i class="fas fa-eye text-success"></i></a>';

                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Teacher $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Teacher $model)
    {
        return $model->newQuery()->with('subject', 'profile');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('teacher-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->language([
                        'url' => route('dataTableTranslation')
                    ])
                    ->lengthMenu([50])
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
            Column::make('profile_id')
            ->orderable(false)
            ->data('profile.name')
            ->title('الإسم'),
            Column::make('subject_id')
            ->orderable(false)
            ->data('subject.name_ar')
            ->title('المادة'),
            Column::make('profile_id')
            ->orderable(false)
            ->data('profile.phone')
            ->title('رقم الهاتف'),
            Column::make('levels')
            ->orderable(false)
            ->title('المراحل'),
            Column::make('lessons_count')
            ->orderable(false)
            ->title('عدد  الحصص بالسنتر'),
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
        return 'Teacher_' . date('YmdHis');
    }
}
