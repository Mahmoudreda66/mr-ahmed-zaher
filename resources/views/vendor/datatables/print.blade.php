<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <title>طباعة الجدول</title>
        <meta charset="UTF-8">
        <meta name=description content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="{{ asset('/dist/css/bootstrap3.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('/material/css/fontawesome.css') }}">
        <style>
            body {margin: 20px}
            *{
                text-align: center;
            }
            th{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <table class="table table-bordered table-condensed table-striped">
            @foreach($data as $row)
                @if ($loop->first)
                    <tr>
                        @foreach($row as $key => $value)
                            <th>{!! $key !!}</th>
                        @endforeach
                    </tr>
                @endif
                <tr>
                    @foreach($row as $key => $value)
                        @if(is_string($value) || is_numeric($value))
                            <td>{!! $value !!}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
    </body>
</html>
