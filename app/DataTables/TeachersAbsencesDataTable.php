<?php

namespace App\DataTables;

use App\Models\Admin\TeachersAbsence;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TeachersAbsencesDataTable extends DataTable
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
        ->editColumn('status', function ($item) {
            if($item->status == 0){
                return '<small class="badge badge-danger">غائب</small>';
            }

            return '<small class="badge badge-success">حاضر</small>';
        })
        ->editColumn('group.group_name', function ($item) {
            $groupName = empty($item->group->group_name) ? 'مجموعة بلا إسم' : $item->group->group_name;
            return 'حصة ' . $item->group->lesson->level->name_ar . ' - ' . $groupName;
        })
        ->addColumn('action', function ($item) {
            return '<i
                    onclick="toggleItem(this,' . $item->id . ')"
                    class="fas toggle-record fa-history text-warning cursor-pointer" title="تغيير حالة الحضور"></i>
                    &nbsp;
                    <i
                    onclick="deleteItem(' . $item->id . ')"
                    class="fas fa-trash delete-record text-danger cursor-pointer" title="حذف"></i>';
        })
        ->editColumn('teacher.subject.name_ar', function ($item) {
            return 'ال' . $item->teacher->subject->name_ar;
        })
        ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\TeachersAbsence $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TeachersAbsence $model)
    {
        return $model
        ->with('teacher.profile', 'teacher.subject', 'group')
        ->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('teachers-absences-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->language(['url' => route('dataTableTranslation')])
                    ->lengthMenu([50])
                    ->buttons(
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
            Column::make('id')
                ->title('#'),
            Column::make('teacher.profile.name')
                ->orderable(false)
                ->title('المُعلم'),
            Column::make('teacher.subject.name_ar')
                ->orderable(false)
                ->title('مُعلم مادة'),
            Column::make('status')
                ->orderable(false)
                ->title('الحالة'),
            Column::make('group.group_name')
                ->orderable(false)
                ->title('تم خلال'),
            Column::make('join_at')
                ->orderable(false)
                ->title('التاريخ'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->title('خيارات')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'TeachersAbsences_' . date('YmdHis');
    }
}
