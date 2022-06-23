<?php

namespace App\DataTables;

use App\Models\Admin\Absence;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AbsenceDataTable extends DataTable
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
            ->editColumn('created_at', function ($item) {
                return date('Y-m-d', strtotime($item->created_at));
            })
            ->editColumn('status', function ($item) {
                if($item->status == 1){return 'حاضر';}else{return 'غائب';}
            })
            ->editColumn('lessons_group_id', function ($item) {
                return !$item->group ? ('يوم: ' . $item->join_at) : (empty($item->group->group_name) ? 'مجموعة بلا إسم' : $item->group->group_name);
            })
            ->addColumn('action', function ($item) {
                $class = $item->status == 1 ? 'text-success' : 'text-warning';
                $itemData = json_encode([
                    'id' => $item->id,
                    'lesson' => $item->student->name
                ]);

                return "<i onclick='toggleItem(" . $item->id . ")' title='عكس حاضر وغائب' class='fas cursor-pointer fa-history " . $class . "'></i> &nbsp;<i onclick='deleteItem(" . $itemData . ")' class='fas cursor-pointer fa-trash text-danger' title='حذف التسجيل'></i>";
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Absence $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Absence $model)
    {
        return $model->newQuery()->with('student', 'student.level');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('absence-table')
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
            Column::make('student.name')
            ->orderable(false)
            ->title('الطالب'),
            Column::make('student.level.name_ar')->title('المرحلة'),
            Column::make('join_at')->title('الوقت'),
            Column::make('lessons_group_id')
            ->searchable(false)
            ->sortable(false)
            ->title('تم خلال'),
            Column::make('status')
            ->searchable(false)
            ->sortable(false)
            ->title('حـ / غـ'),
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
        return 'Absence_' . date('YmdHis');
    }
}
