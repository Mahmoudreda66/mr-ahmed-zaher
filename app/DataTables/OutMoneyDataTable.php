<?php

namespace App\DataTables;

use App\Models\Admin\OutMoney;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OutMoneyDataTable extends DataTable
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
            ->addColumn('action', function ($item) {
                $html = '';

                if(auth()->user()->hasPermission('delete-out-money')){
                    $itemData = json_encode([
                        'id' => $item->id,
                        'name' => $item->money . ' ج - ' . $item->reason
                    ]);

                    $html .= "<i onclick='deleteItem(" . $itemData . ")' class='cursor-pointer fas fa-trash text-danger' title='حذف''></i>";
                }

                if(auth()->user()->hasPermission('edit-out-money')){
                    $itemData = json_encode([
                        'id' => $item->id,
                        'money' => $item->money,
                        'at' => $item->at,
                        'reason' => $item->reason,
                        'money_from' => $item->money_from,
                    ]);

                    $html .= "&nbsp; <i onclick='editItem(" . $itemData . ")' class='cursor-pointer fas fa-edit text-success' title='تعديل'></i>";
                }

                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\OutMoney $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(OutMoney $model)
    {
        return $model->newQuery()->with('user');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('outmoney-table')
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
            Column::make('id')->title('#'),
            Column::make('money')->title('المال'),
            Column::make('user_id')
            ->orderable(false)
            ->data('user.name')
            ->title('المستخدم'),
            Column::make('reason')->title('السبب'),
            Column::make('at')->title('التاريخ'),
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
        return 'OutMoney_' . date('YmdHis');
    }
}
