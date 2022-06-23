<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة كشف الغياب - {{ $level }}</title>
    <link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('material/css/fontawesome.css') }}">
    <style>
        body {
            font-family: cairo;
            direction: rtl;
            font-size: 12px;
        }

        table td{
            padding: .2rem .5rem !important;
            text-align: center;
        }

        table td.name{
            text-align: right;
            font-size: 10px;
        }
    </style>
    <script>
        // window.print();
    </script>
</head>

<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <td colspan="35">
                    <div class="row">
                        <div class="col-6 py-1">
                            الشهر: ......
                        </div>
                        <div class="col-6 py-1">
                            المرحلة: {{ $level }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>الإســــــــــــــــــــــــــــــــــــــــــــم</td>
                <td>شـ</td>
                <td>عـ</td>
                @for($i = 1; $i < 32; $i++)
                <td>{{ $i }}{!! $i < 10 ? '&nbsp;' : '' !!}</td>
                @endfor
                <td>المصروفات</td>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $index => $student)
            <tr>
                <td class="name">
                    <small>{{ $index + 1 }}- {{ $student->name }}</small>
                </td>
                <td>
                    @if($student->division === 0)
                    ع
                    @elseif($student->division === 1)
                    أ
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if($student->edu_type === 0)
                    ع
                    @elseif($student->edu_type === 1)
                    ل
                    @else
                    -
                    @endif
                </td>
                @for($i = 1; $i < 32; $i++)
                <td></td>
                @endfor
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="35">
                    <div class="alert alert-info text-center">
                        لا يوجد طلاب حتى الآن
                    </div>
                    <a href="{{ URL::previous() }}">
                        <button class="btn btn-info mx-auto d-block btn-sm">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="35" class="text-center py-1">
                    سمارت سنتر لإدارة الدروس الخصوصية
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>