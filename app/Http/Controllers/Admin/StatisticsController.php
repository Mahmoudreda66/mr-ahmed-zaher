<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\OutMoney;
use App\Models\Admin\Settings;
use App\Models\Admin\Expenses;
use App\Models\Admin\Student;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\DataTables\OutMoneyDataTable;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:money');
        $this->middleware('permission:show-statistics')->only('statistics_index');
        $this->middleware('permission:show-out-money')->only('index');
        $this->middleware('permission:add-out-money')->only('store');
        $this->middleware('permission:edit-out-money')->only('update');
        $this->middleware('permission:delete-out-money')->only('destroy');
    }

    public function index(Request $request, OutMoneyDataTable $table)
    {
        $allUsers = User::select('id', 'name')->get();
        $users = [];
        foreach ($allUsers as $user) {
            if (!$user->hasRole('teacher')) {
                $users[] = $user;
            }
        }

        $arguments = [
            'users'
        ];

        if ($request->has('user') && $request->has('from') && $request->has('to')) {
            $validation = Validator::make($request->all(), [
                'from' => 'date|nullable',
                'to' => 'date|nullable'
            ]);

            if ($validation->fails()) {
                abort(404);
            }

            $request->user !== '*' ? ($user = User::findOrFail($request->user)) : ($user = null);

            if ($user) {
                $getReport = OutMoney::where('user_id', $user->id)
                    ->whereBetween('at', [$request->from, $request->to])
                    ->get();

                $total = OutMoney::where('user_id', $user->id)
                    ->whereBetween('at', [$request->from, $request->to])
                    ->sum('money');
            } else {
                $getReport = OutMoney::whereBetween('at', [$request->from ?? date('Y-m-d'), $request->to ?? date('Y-m-d')])
                    ->get();

                $total = OutMoney::whereBetween('at', [$request->from ?? date('Y-m-d'), $request->to ?? date('Y-m-d')])
                    ->sum('money');
            }

            $showReport = true;

            array_push($arguments, 'getReport', 'showReport', 'total');
        }

        return $table->render('admin.statistics.out_money', compact(...$arguments));
    }

    public function statistics_index(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'month' => 'required|numeric|max:12|min:1',
            'year' => 'required|numeric|max:' . date('Y')
        ]);

        if ($validation->fails()) {
            return redirect()->to(route('statistics.index', ['month' => date('m'), 'year' => date('Y')]));
        }

        function getIncomeByLevel($level, $month, $year)
        {
            return Expenses::where('month', $month)
                ->whereYear('created_at', $year)
                ->whereHas('student', function ($q) use ($level) {
                    $q->where('level_id', $level);
                })->sum('money');
        }

        function getStudentsCount($level)
        {
            return Student::where('level_id', $level)->count();
        }

        $paid_students = Expenses::where('month', $request->month)
        ->whereYear('created_at', $request->year)
        ->with('user', 'student.level')
        ->get();

        $unpaid_students = Student::select('id', 'name', 'level_id')
        ->with('level')
        ->whereNotExists(function ($q) use ($request) {
            return $q->select()
            ->from('expenses')
            ->where('month', $request->month)
            ->whereYear('created_at', $request->year)
            ->whereColumn('expenses.student_id', 'students.id');
        })->get();

        $expenses = json_decode(Settings::where('name', 'expenses')->first()['value'], true);

        $incomes = [
            'prep1Incom'        => getIncomeByLevel(1, $request->month, $request->year),
            'prep2Incom'        => getIncomeByLevel(2, $request->month, $request->year),
            'prep3Incom'        => getIncomeByLevel(3, $request->month, $request->year),
            'sec1Incom'         => getIncomeByLevel(4, $request->month, $request->year),
            'sec2Incom'         => getIncomeByLevel(5, $request->month, $request->year),
            'sec3Incom'         => getIncomeByLevel(6, $request->month, $request->year),
        ];

        $counts = [
            'prep1Count'        => getStudentsCount(1),
            'prep2Count'        => getStudentsCount(2),
            'prep3Count'        => getStudentsCount(3),
            'sec1Count'         => getStudentsCount(4),
            'sec2Count'         => getStudentsCount(5),
            'sec3Count'         => getStudentsCount(6),
        ];

        $arguments = [
            'incomes',
            'counts',
            'unpaid_students',
            'paid_students',
            'expenses'
        ];

        return view('admin.statistics.index', compact(...$arguments));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'money' => 'required|numeric|max:99999|min:1',
            'reason' => 'required|min:2',
            'at' => 'required|date',
        ], [
            'money.required' => 'الأموال مطلوبة',
            'money.numeric' => 'يجب أن تتكون الأموال من أرقام',
            'money.max' => 'أقصى مبلغ يمكن دفعه هو 99999 جنيه',
            'money.min' => 'أقل مبلغ يمكن دفعه هو 1 جنيه',
            'reason.required' => 'سبب الدفع مطلوب',
            'reason.min' => 'يجب أن يتكون سبب الدفع من 2 حرف على الأقل',
            'at.required' => 'التاريخ مطلوب',
            'at.date' => 'صيغة التاريخ غير صحيحة',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation);
        }

        OutMoney::create([
            'user_id' => auth()->user()->id,
            'money' => $request->money,
            'at' => $request->at,
            'reason' => $request->reason
        ]);

        return redirect()->back()->with(['success' => 'تم الإضافة بنجاح']);
    }

    public function update($id, Request $request)
    {
        $item = OutMoney::findOrFail($id);

        $validation = Validator::make($request->all(), [
            'money' => 'required|numeric|max:99999|min:1',
            'reason' => 'required|min:2',
            'at' => 'required|date',
        ], [
            'money.required' => 'الأموال مطلوبة',
            'money.numeric' => 'يجب أن تتكون الأموال من أرقام',
            'money.max' => 'أقصى مبلغ يمكن دفعه هو 99999 جنيه',
            'money.min' => 'أقل مبلغ يمكن دفعه هو 1 جنيه',
            'reason.required' => 'سبب الدفع مطلوب',
            'reason.min' => 'يجب أن يتكون سبب الدفع من 2 حرف على الأقل',
            'at.required' => 'التاريخ مطلوب',
            'at.date' => 'صيغة التاريخ غير صحيحة',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->withInput()->withErrors($validation)->with(['open_edit_modal' => $item->id]);
        }

        $item->update($request->except('_token', '_method'));

        return redirect()->back()->with(['success' => 'تم التعديل بنجاح']);
    }

    public function destroy($id)
    {
        OutMoney::findOrFail($id)->delete();

        return redirect()->back()->with(['success' => 'تم حذف العنصر بنجاح']);
    }
}
