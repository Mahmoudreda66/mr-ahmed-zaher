<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة باركود {{ $level->name_ar }}</title>
    <link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('material/css/fontawesome.css') }}">
    <style>
        .container .row .st-card *{
            margin: 0px !important;
            overflow: hidden;
            white-space: nowrap;
            font-family: sans-serif;
        }
    </style>
</head>

<body>
    <div class="container my-3">
        <div class="row">
            @forelse($students as $i => $student)
            <div class="col-lg-4 col-4 mb-5 mt-3 overflow-visible st-card">
                <center style="margin-top: 3px !important;">
                    <b>{{ cache()->get('app_name') }}</b> <br>
                    {!! DNS1D::getBarcodeSVG($student->id, 'C128', 2, 30, 'black', true); !!} <br>
                    <span style="background-color: #000; color: #fff;">{{ $student->name }}</span> <br>
                    <span>الكود: {{ $student->code }}</span>
                </center>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center mt-5">
                    لا يوجد طلاب حتى الآن
                </div>
                <a href="{{ URL::previous() }}">
                    <button class="btn btn-info mx-auto d-block mt-3">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </a>
            </div>
            @endforelse
        </div>
    </div>
    <script>window.print();</script>
</body>

</html>