<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Expenses;
use Illuminate\Http\Request;
use App\Models\Admin\Settings;
use Illuminate\Support\Facades\Validator;
use App\DataTables\ExpensesDataTable;

class ExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:students-expenses-list')->only('index');
        $this->middleware('permission:add-student-expenses')->only('store');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ExpensesDataTable $table)
    {
        if(!empty($request->month) && is_numeric($request->month) && !empty($request->year) && is_numeric($request->year)){
            if($request->month > 12 || $request->month < 0 || $request->year > date('Y') || $request->year < (date('Y') - 10)){
                abort(404);
            }else{
                $table->setDate($request->month, $request->year);
            }
        }else{
            return redirect()->to(route('expenses.index', ['month' => date('m'), 'year' => date('Y')]));
        }

        return $table->render('admin.expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'expenses' => 'required|numeric',
            'month' => 'required|numeric|max:12|min:1'
        ], [
            'id.required' => 'خطأ في الID',
            'id.numeric' => 'خطأ في الID',
            'expenses.required' => 'حقل المصروفات مطلوب',
            'expenses.numeric' => 'يجب أن تتألف المصروفات من أرقام',
            'month.required' => 'حقل الشهر مطلوب',
            'month.numeric' => 'يجب أن يتكون الشهر من أرقام',
            'month.max' => 'قم بإدخال تاريخ صالح',
            'month.min' => 'قم بإدخال تاريخ صالح',
        ]);

        if ($validation->fails()) {
            if($request->ajax()){
                return json_encode(['status' => false, 'message' => 'validation', 'data' => $validation->errors()->all()]);
            }else{
                return redirect()->back()->withInput()->withErrors($validation)->with(['show_expenses_modal' => true]);
            }
        }

        $expenses = Expenses::create([
            'student_id' => $request->id,
            'user_id' => auth()->user()->id,
            'money' => $request->expenses,
            'month' => $request->month
        ]);

        $print_invoice = Settings::where('name', 'always_print_invoice_billing')->select('value')->first()['value'];

        if($request->ajax()){
            return json_encode(['status' => true, 'message' => 'success', 'data' => []]);
        }else{
            if ($print_invoice == 1) {
                return redirect()->back()->with([
                    'success' => 'تم الدفع بنجاح',
                    'print_invoice' => true,
                    'invoice_id' => $expenses->id,    
                ]);
            }

            return redirect()->back()->with(['success' => 'تم الدفع بنجاح']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Expenses::findOrFail($id)->delete();
        return redirect()->back()->with(['success' => 'تم حذف العملية بنجاح']);
    }

    public function force_delete($id)
    {
        Expenses::withTrashed()->findOrFail($id)->forceDelete();

        return redirect()->back()->with(['success' => 'تم حذف العملية بنجاح']);
    }

    public function restore($id)
    {
        $item = Expenses::withTrashed()->findOrFail($id);
        if ($item->trashed($id)) {
            $item->restore();
        }

        return redirect()->back()->with(['success' => 'تم إستعادة  العملية بنجاح']);
    }

    public function print_invoice($id)
    {
        $expenses = Expenses::findOrFail($id);

        // $connector = new WindowsPrintConnector(env('PRINTER_NAME', ''));
        // $printer = new Printer($connector);
        // $printer->text("Hello World!\n");
        // $printer->cut();
        // $printer->close();

        return view('admin.expenses.print_invoice', compact('expenses'));
    }

    public function search(Request $request)
    {
        if (!empty($request->id)) {
            $expenses = Expenses::where('id', $request->id)->get();
            $title = 'نتيجة البحث';
            $pagination = false;

            return view('admin.expenses.index', compact('expenses', 'title', 'pagination'));
        }

        $expenses = Expenses::withTrashed()->latest()->paginate(50);
        $title = 'مصروفات الطلاب';
        $pagination = true;
        return view('admin.expenses.index', compact('expenses', 'title', 'pagination'));
    }
}
