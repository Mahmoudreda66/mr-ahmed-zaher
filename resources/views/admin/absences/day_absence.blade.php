@extends('admin.layouts.app', ['activePage' => 'day-absence-mode', 'titlePage' => "وضع غياب اليوم"])

@section('title')
وضع غياب اليوم
@endsection

@section('content')

<!-- Absence Modal -->
<div class="modal fade" id="absenceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">تغييب الباقي</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('absences.day.end_day') }}" class="mb-0" method="post" id="absence-form" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label for="level" class="form-label">المرحلة</label>
                        <select name="level" id="level" class="form-control @error('level')
                        is-invalid
                        @enderror">
                            <option value="NULL" disabled selected>إختر المرحلة</option>
                            @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name_ar }}</option>
                            @endforeach
                        </select>
                        @error('level')
                        <small class="d-block invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label for="date" class="form-label">التاريخ</label>
                        <input type="date" name="date" id="date" class="form-control @error('date')
                        is-invalid
                        @enderror" value="{{ date('Y-m-d') }}">
                        @error('date')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('absence-form').submit();">تغييب</button>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->hasPermission('add-student-expenses'))
<!-- Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">دفع المصروفات</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="expenses-form" autocomplete="off" onsubmit="sendPay(); return false;">
                    <input type="hidden" name="id" id="id">
                    <div class="mb-3">
                        <label for="student" class="form-label">الطالب</label>
                        <input type="text" name="student" id="student" readonly class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="expenses" class="form-label">المصروفات</label>
                        <input type="number" name="expenses" id="expenses" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="month" class="form-label">الشهر</label>
                        <input type="number" name="month" id="month" class="form-control" value="{{ date('m') * 1 }}">
                    </div>
                    <input type="submit" value="" class="d-none">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="submitExpenses" onclick="sendPay()">حفظ</button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="content">

    @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div class="bg-white container-fluid p-4">
    	<form id="student-token-form" class="mb-3" autocomplete="off">
    		<div class="row">
                <div class="col-8">
                    <div class="mb-0">
                        <label for="token">رمز الإستجابة السريع</label>
                        <input type="text" name="token" placeholder="قم بلصق رمز الإستجابة السريع هنا" id="token" class="form-control">
                    </div>
                </div>
                <div class="col-2">
                    <label for="day_date">التاريخ</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" id="day_date" class="form-control">
                </div>
                <div class="col-2">
                    <label for="absence_count_alert">الإشعار بالغياب أكثر من</label>
                    <input type="number" name="date" value="2" id="absence_count_alert" class="form-control">
                </div>
            </div>
            <input type="submit" class="d-none">
    	</form>
    	<div class="table-responsive">
    		<table class="table table-hover text-center mb-0" id="parent-table">
                <thead class="text-primary">
                    <th class="text-center" style="direction: ltr;">
                        #ID
                    </th>
                    <th class="text-center">
                        الطالب
                    </th>
                    <th class="text-center">
                        المرحلة
                    </th>
                    <th class="text-center">
                        الحالة
                    </th>
                    <th class="text-center">
                        حالة المصروفات
                    </th>
                    <th class="text-center">
                        التاريخ
                    </th>
                    <th class="text-center">
                        خيارات
                    </th>
                </thead>
                <tbody>
                    @forelse ($dayAbsences as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->student->name }}</td>
                        <td>{{ $item->student->level->name_ar }}</td>
                        <td>{{ $item->status == 1 ? 'حاضر' : 'غائب' }}</td>
                        <td>
                            @php
                            $studentData = json_encode([
                                'name' => $item->student->name,
                                'id' => $item->student->id,
                                'level_id' => $item->student->level->name_en
                            ]);
                            @endphp

                            @if(isset($item->student->expenses[0]))
                                @if(auth()->user()->hasPermission('add-student-expenses'))
                                    <span
                                    data-student="{{ $studentData }}"
                                    data-id="{{ $item->student->id }}"
                                    class='badge user-select-none cursor-pointer bg-success text-white'>
                                        تم الدفع
                                    </span>
                                @else
                                    <span
                                    class='badge bg-success text-white'>
                                    تم الدفع
                                    </span>
                                @endif
                            @else
                                @if(auth()->user()->hasPermission('add-student-expenses'))
                                    <span
                                    data-student="{{ $studentData }}"
                                    data-id="{{ $item->student->id }}"
                                    class='cursor-pointer badge user-select-none bg-warning'>لم يتم الدفع</span>
                                @else
                                    <span class='badge bg-warning'>لم يتم الدفع</span>
                                @endif
                            @endif
                        </td>
                        <td title="{{ $item->created_at->diffForHumans() }}">{{ $item->join_at }}</td>
                        <td>
                            <i class="fas fa-history text-info toggle-buttons cursor-pointer" data-id="{{ $item->id }}"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="alert alert-info text-center mb-0" id="emptyAlert">
                            	لا يوجد تسجيلات حتى الآن
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
    	</div>
    	<div class="pagination text-center d-flex mt-3 justify-content-center">
    		{!! $dayAbsences->links('pagination::bootstrap-4') !!}
    	</div>
    </div>
    <button class="btn btn-sm my-3 btn-success" onclick="new bootstrap.Modal(document.getElementById('absenceModal')).show()">تغييب الباقي</button>
</div>
@endsection

@section('js')
@if(Session::has('open_end_modal'))
<script>
    new bootstrap.Modal(document.getElementById('absenceModal')).show();
</script>
@endif
<script src="{{ asset('/dist/js/day_absence_mode.js') }}" data-user-can-pay="{{ auth()->user()->hasPermission('add-student-expenses') }}"></script>
@endsection