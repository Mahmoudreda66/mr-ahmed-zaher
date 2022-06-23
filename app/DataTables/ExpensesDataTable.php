<?php

namespace App\DataTables;

use App\Models\Admin\Expenses;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ExpensesDataTable extends DataTable
{
    private $monthData;
    private $yearData;

    public function setDate ($month, $year)
    {
        $this->monthData = $month;
        $this->yearData = $year;
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
            ->editColumn('created_at', function ($item) {
                return date('Y-m-d H:i', strtotime($item->created_at));
            })
            ->editColumn('user_id', function ($item) {
                return $item->user->name;
            })
            ->addColumn('action', function ($item) {
                if($item->trashed()){
                    $html = '<i title="حذف نهائياً" class="fas fa-trash text-danger cursor-pointer" onclick="forceDeleteRecord(' . $item->id . ')"></i> &nbsp;<i title="إستعادة" class="fas fa-trash-restore cursor-pointer text-success" onclick="restoreRecord(' . $item->id . ')"></i>';
                }else{
                    $html = '<i title="حذف" class="fas fa-trash text-danger cursor-pointer" onclick="deleteRecord(' . $item->id . ')"></i>&nbsp;<i class="cursor-pointer fas fa-print text-primary" onclick="printInvoice(' . $item->id . ')"></i>';
                }

                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Expense $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Expenses $model)
    {
        if($this->monthData && $this->yearData){
            return $model
            ->query()
            ->where('month', $this->monthData)
            ->whereYear('created_at', $this->yearData)
            ->with('student', 'student.level')
            ->withTrashed();
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
                    ->setTableId('expenses-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0)
                    ->lengthMenu([50])
                    ->language(['url' => route('dataTableTranslation')])
                    ->buttons(
                        Button::make('excel')->text('<i class="fas fa-download"></i> طباعة EXCEL'),
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
            Column::make('id')
            ->title('#ID'),
            Column::make('student.name')
            ->orderable(false)
            ->title('الطالب'),
            Column::make('student.level.name_ar')
            ->orderable(false)
            ->title('المرحلة'),
            Column::make('month')->title('الشهر'),
            Column::make('money')->title('المبلغ'),
            Column::make('created_at')->title('تاريخ العملية'),
            Column::make('user_id')->title('المستلم'),
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
        return 'Expenses_' . date('YmdHis');
    }
}
