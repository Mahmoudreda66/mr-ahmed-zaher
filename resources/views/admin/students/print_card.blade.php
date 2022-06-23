<head>
    <style>
        *{
            margin: 0px !important;
            overflow: hidden;
            white-space: nowrap;
        }
    </style>
</head>
<center style="margin-top: 3px !important;">
    <b>{{ cache()->get('app_name') }}</b> <br>
    {!! DNS1D::getBarcodeSVG($student->id, 'C128', 2, 30, 'black', false); !!} <br>
    <span style="background-color: #000; color: #fff;">{{ $student->name }}</span> <br>
    <span>الكود: {{ $student->code }}</span>
</center>
<script>window.print();</script>