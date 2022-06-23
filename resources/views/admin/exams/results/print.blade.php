<!DOCTYPE html>
<html lang="ar">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>طباعة الدرجات</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
	<link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
	<style>
		body {
			direction: rtl;
			font-family: cairo;
			color: #161616;
			font-size: 13px;
		}

		h1 {
			font-weight: bolder;
			font-size: 25px;
		}

		ul {
			list-style: square;
		}

		ul li {
			margin-bottom: 4px;
		}

		.footer ul li{
			font-size: 12px !important;
		}

		.footer h1,
		.footer h2,
		.footer h3,
		.footer h4,
		.footer h5,
		.footer h6 {
			font-size: 16px;
			font-weight: bold;
		}

		.footer ul {
			margin-bottom: 0px;
		}
	</style>
</head>

@php
function getResult ($student, $attemp)
{

	return App\Models\Exams\ExamsResults::where([
		['student_id', $student],
		['exams_enter_attemps_id', $attemp]
	])
	->select('mark')
	->first();
}
@endphp

<body>
	<table class="table table-bordered table-striped text-center">
		<thead>
			<tr>
				<td>#</td>
				<td>الطالب</td>
				<td>الكود</td>
				<td>الدرجة</td>
				<td>التاريخ</td>
			</tr>
		</thead>
		<tbody>
			@foreach($attemps as $i => $attemp)
			<tr>
				<td>{{ $i + 1 }}</td>
				<td>{{ $attemp->student->name }}</td>
				<td>{{ $attemp->student->code }}</td>
				<td>
					@php
					$result = getResult($attemp->student->id, $attemp->id);

					$result ? ($result = $result['mark']['full_mark'] . ' / ' . $result['mark']['correct_answers']) : ($result = 'لا يوجد');
					@endphp

					{{ $result }}
				</td>
				<td>{{ $attemp->created_at }}</td>
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">سمارت سنتر لإدارة الدروس الخصوصية</td>
			</tr>
		</tfoot>
	</table>
	<script>
		window.print();
	</script>
</body>

</html>