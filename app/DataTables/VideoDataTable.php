<?php

namespace App\DataTables;

use App\Models\Videos\Video;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VideoDataTable extends DataTable
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
                return date('Y-m-d h:i A', strtotime($item->created_at));
            })
            ->addColumn('action', function ($item) {
                $html = '<a href="' . route('videos-management.show', [$item->id]) . '"><i class="fas fa-eye text-info"></i></a>&nbsp;';

                if(auth()->user()->hasPermission('edit-video')){
                    $html .= '<a href="' . route('videos-management.show', [$item->id, 'edit']) . '"><i class="fas fa-edit text-success"></i></a>&nbsp;';
                }

                if(auth()->user()->hasPermission('delete-video')){
                    $html .= '<a href="' . route('videos-management.show', [$item->id, 'delete']) . '"><i class="fas fa-trash text-danger"></i></a>';
                }

                return $html;
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Video $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Video $model)
    {
        return $model->with('level')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('videos-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->language(['url' => route('dataTableTranslation')])
                    ->lengthMenu([50])
                    ->buttons(
                        Button::make('create')->text('<i class="fas fa-plus"></i> إضافة فيديو'),
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
            ->title('#ID'),

            Column::make('title')
            ->title('عنوان الفيديو'),

            Column::make('level_id')
            ->data('level.name_ar')
            ->title('المرحلة'),

            Column::make('created_at')
            ->title('تاريخ النشر'),

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
        return 'Video_' . date('YmdHis');
    }
}
