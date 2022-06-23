<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة كشف الغياب</title>
    <link rel="stylesheet" href="{{ asset('material/css/fontawesome.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            direction: rtl;
            font-family: cairo, 'sans-serif';
            color: #161616;
            font-size: 10px;
        }

        .app-name{
            font-size: 20px;
        }

        .border{
            border-color: #0000 !important;
        }

        @media print {
		  .pagination {
		    display: none !important;
		  }
		}
    </style>
</head>
<body>
	<div class="bg-white p-3" id="main-content">
		<div>
			<table class="table table-bordered table-striped">
		        <thead>
		            <tr>
		                <th colspan="34">
		                    <div class="row">
		                        <div class="col-6 py-1">
		                            الشهر: {{ $_GET['month'] * 1 . ' - ' . $_GET['year'] }}
		                        </div>
		                        <div class="col-6 py-1">
		                            المرحلة: {{ $levelData->name_ar }}
		                        </div>
		                    </div>
		                </th>
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
		            @forelse ($result as $index => $student)
		            <tr>
		                <td class="name">
		                	<small>
		                		{{ $index + 1 }}- {{ $student['student']['name'] }}
		                	</small>
		                </td>
		                <td>
		                    @if($student['student']['division'] === 0)
		                    ع
		                    @elseif($student['student']['division'] === 1)
		                    أ
		                    @else
		                    -
		                    @endif
		                </td>
		                <td>
		                    @if($student['student']['edu_type'] === 0)
		                    ع
		                    @elseif($student['student']['edu_type'] === 1)
		                    ل
		                    @else
		                    -
		                    @endif
		                </td>
		                @for($i = 1; $i < 32; $i++)
		                @php
		                $date = $_GET['year'] . '-' . $_GET['month'] . '-' . ($i < 10 ? '0' . $i : $i);
		                @endphp
		                <td class="text-center">
		                	@foreach($student['absenceList'] as $item)
		                	@if($item['join_at'] == $date)
			                	@if($item['status'] == 0)
			                	<i class="fas fa-times text-danger"></i>
			                	@elseif($item['status'] == 1)
			                	<i class="fas fa-check text-success"></i>
			                	@endif
		                	@endif
		                	@endforeach
		                </td>
		                @endfor
		                <td class="text-center">
		                	@if($student['expensesStatus'])
		                	<i class="fas fa-check text-success"></i>
		                	@else
		                	<i class="fas fa-times text-danger"></i>
		                	@endif
		                </td>
		            </tr>
		            @empty
		            <tr>
		                <td colspan="34">
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
		                <td colspan="34" class="text-center">
		                    سمارت سنتر لإدارة الدروس الخصوصية
		                </td>
		            </tr>
		        </tfoot>
		    </table>
		</div>
	</div>
	@if(isset($_GET['print']))
	<script>
		window.print();
	</script>
	@endif
</body>
</head>
</html>