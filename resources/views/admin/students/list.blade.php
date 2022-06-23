<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة كشف الأسماء - {{ $level }}</title>
    <link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('material/css/fontawesome.css') }}">
    <style>
        body {
            font-family: cairo;
            direction: rtl;
            font-size: 12px;
        }
    </style>
    <script>
        window.print();
    </script>
</head>

<body>
    <table border="1px" style="border-color: #000;" class="table">
        <thead>
            <tr>
                <td colspan="3" class="text-center">كشف أسماء {{ $level }}</td>
            </tr>
        </thead>
        <tbody>
            <?php $i = 3; ?>
            <tr>
                @forelse ($students as $index => $student)
                
                @if($index % $i == 0)
                </tr>
                @endif

                <td class="border-end border-dark">
                    {{ $index + 1}} - {{ $student->name }} // {{ $student->code }}
                </td>

                @empty
                <tr>
                    <td colspan="3">
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
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-center">
                    سمارت سنتر لإدارة الدروس الخصوصية
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>