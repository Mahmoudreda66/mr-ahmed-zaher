@extends('admin.layouts.app', ['activePage' => 'absence-report', 'titlePage' => "تقارير الغياب"])

@section('title')
تقارير الغياب
@endsection

@section('content')
<div class="content">
    <div class="bg-white p-4 container-fluid">
    	<form action="{{ route('absences.reports') }}" method="get" id="report-form" autocomplete="off" class="mb-0">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="level">المرحلة</label>
                    <select name="level" id="level" class="form-control">
                        <option value="NULL" disabled selected>إختر المرحلة</option>
                        @foreach($levels as $level)
                        <option
                        {{ $level->id == $_GET['level'] ? 'selected' : '' }}
                        value="{{ $level->id }}">
                            {{ $level->name_ar }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-12 mb-3">
                    <div class="row">
                        <div class="col-6">
                            <label for="from">من</label>
                            <input type="date" name="from" id="from" class="form-control" value="{{ $_GET['from'] ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label for="to">إلى</label>
                            <input type="date" name="to" id="to" class="form-control" value="{{ $_GET['to'] ?? date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                @if(isset($students))
                <div class="col-md-6 col-12 mb-3">
                    <label for="student">الطلاب</label>
                    <select name="student" id="student" class="form-control">
                        <option
                        {{ empty($_GET['student']) || $_GET['student'] === '*' ? 'selected' : '' }}
                        value="*">كل الطلاب</option>
                        @foreach($students as $student)
                        <option
                        {{ !empty($_GET['student']) && $_GET['student'] == $student->id ? 'selected' : '' }}
                        value="{{ $student->id }}">
                            {{ $student->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <button class="btn btn-success btn-sm">عرض</button>
        </form>
        @if(isset($absences))
        <hr>
        <table class="table table-hover text-center mb-0">
            <thead>
                <tr>
                    <th class="text-center text-primary">#</th>
                    <th class="text-center text-primary">الطالب</th>
                    <th class="text-center text-primary">المرحلة</th>
                    <th class="text-center text-primary">تم خلال</th>
                    <th class="text-center text-primary">الحالة</th>
                    <th class="text-center text-primary">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absences as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->student->name }}</td>
                    <td>{{ $item->student->level->name_ar }}</td>
                    <td>
                        {{ $item->group ? $item->group->group_name : $item->created_at }}
                    </td>
                    <td>{!! $item->status == 1 ? '<small class="badge badge-success">حاضر</small>' : '<small class="badge badge-danger">غائب</small>' !!}</td>
                    <td>{{ $item->join_at }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="alert alert-info mb-0 text-center">
                            لا توجد بيانات بعد
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection