@extends('admin.layouts.app', ['activePage' => 'dashboard', 'titlePage' => "لوحة التحكم"])

@section('title')
لوحة التحكم
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('/dist/css/chartist.css') }}">
<script src="{{ asset('/dist/js/chartist.js') }}"></script>
@endsection

@php
function getCountByLevels($level)
{
    return \App\Models\Admin\Student::where('level_id', $level)->count();
}

function getLevelsIds()
{
    $levels = \App\Models\Admin\Level::select('id')->get();
    $ids = [];
    foreach($levels as $level){
        $ids[] = $level->id;
    }
    return json_encode($ids);
}

function getExpensesByLevel($level)
{
    return \App\Models\Admin\Expenses::whereHas('student', function ($q) use($level) {
        $q->where('level_id', $level);
    })->sum('money');
}

function getStudentsGenderCount($gender)
{
    return \App\Models\Admin\Student::where('gender', $gender)->count();
}

function getCustomEncome($type)
{
    if($type === 'month'){
        return \App\Models\Admin\Expenses::where('month', date('m'))
        ->sum('money');
    }

    return \App\Models\Admin\Expenses::whereDay('created_at', date('d'))
        ->sum('money');
}
@endphp

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="card-category">عدد الطلاب</p>
                        <h3 class="card-title">
                            {{ \App\Models\Admin\Student::count() }}
                            <small>طالب</small>
                        </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <a href="{{ route('students.index') }}">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <p class="card-category">الوافد اليومي</p>
                        <h3 class="card-title">
                            {{ getCustomEncome('day') }}
                            <small>جـ</small>
                        </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <a href="{{ route('expenses.index') }}">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-danger card-header-icon">
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <p class="card-category">الوافد الشهري</p>
                        <h3 class="card-title">
                            {{ getCustomEncome('month') }}
                            <small>ج</small>
                        </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <a href="{{ route('statistics.index') }}">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-info card-header-icon">
                        <div class="card-icon">
                            <i class="fa fa-chalkboard"></i>
                        </div>
                        <p class="card-category">عدد المجموعات</p>
                        <h3 class="card-title">
                            {{ \App\Models\Admin\LessonsGroups::count() }}
                            <small>مجـ</small>
                        </h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <a href="{{ route('lessons.index') }}">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-chart">
                    <div class="card-header card-header-success">
                        <div class="ct-chart ct-minor-sixth" id="levels-chart"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">المراحل</h4>
                        <p class="card-category">
                            <span>مخطط المراحل الدراسية</span>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            رسم تخطيطي لأعداد الطلاب بكل مرحلة
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-chart">
                    <div class="card-header card-header-warning">
                        <div class="ct-chart ct-minor-sixth" id="gender-chart"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">الجنس</h4>
                        <p class="card-category">مخطط جنس الطلاب</p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            رسم تخطيطي لتقسيم الطلاب بالجنس
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-chart">
                    <div class="card-header card-header-danger">
                        <div class="ct-chart ct-minor-sixth" id="encomeChart"></div>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">الدخل</h4>
                        <p class="card-category">مخطط الدخل من الطلاب</p>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            رسم تخطيطي لعرض حالة الدخل حسب المرحلة 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let levels = {!! getLevelsIds() !!};

    new Chartist.Line('#levels-chart', {
      labels: [...levels],
      series: [
        [
            {{ getCountByLevels(1) }},
            {{ getCountByLevels(2) }},
            {{ getCountByLevels(3) }},
            {{ getCountByLevels(4) }},
            {{ getCountByLevels(5) }},
            {{ getCountByLevels(6) }},
        ]
      ]
    });

    new Chartist.Bar('#gender-chart', {
      labels: ['الذكور', 'الإناث'],
      series: [
        [{{ getStudentsGenderCount(0) }}, {{ getStudentsGenderCount(1) }}],
      ]
    }, {
      stackBars: true,
      axisY: {
        labelInterpolationFnc: function(value) {
          return value;
        }
      }
    }).on('draw', function(data) {
      if(data.type === 'bar') {
        data.element.attr({
          style: 'stroke-width: 30px'
        });
      }
    });    

    new Chartist.Line('#encomeChart', {
      labels: [...levels],
      series: [
        [
            {{ getExpensesByLevel(1) }},
            {{ getExpensesByLevel(2) }},
            {{ getExpensesByLevel(3) }},
            {{ getExpensesByLevel(4) }},
            {{ getExpensesByLevel(5) }},
            {{ getExpensesByLevel(6) }},
        ]
      ]
    });
</script>
@endpush