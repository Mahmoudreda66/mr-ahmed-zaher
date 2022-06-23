<!DOCTYPE html>
<html lang="ar">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>طباعة بيانات الطالب {{ $student->name }}</title>
	<link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
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

<body>
	<div class="top-nav overflow-hidden">
		<div class="float-end">
			<h1>{{ cache()->get('app_name', 'سمارت سنتر') }}</h1>
			<small class="d-block fst-italic">إستمارة بيانات الطالب</small>
			<br>
			<small>{{ $student->created_at }}</small>
		</div>
		<div class="float-start">
            {!! DNS1D::getBarcodeSVG($student->id, 'C128'); !!}
		</div>
	</div>
	<hr>
	<div class="body">
		<h4 class="text-center fw-bold">
			تفاصيل عامة
		</h4>
		<div class="container mt-3 mb-4 text-center">
			<div class="row">
				<div class="col-6">
					الإسم: {{ $student->name }}
				</div>
				<div class="col-6">
					المرحلة: {{ $student->level->name_ar }}
				</div>
			</div>
			<div class="row mt-2">
				<div class="col-6">
					رقم الهاتف: {{ $student->mobile ?? 'لا يوجد' }}
				</div>
				<div class="col-6">
					الجنس: {{ $student->gender ? 'أنثى' : 'ذكر' }}
				</div>
			</div>
			<div class="row mt-2">
				<div class="col-6">
					دفع عند الحجز: {{ $student->given_money ?? 0 }} ج
				</div>
				<div class="col-6">
					المصروفات المقررة: {{ json_decode(\App\Models\Admin\Settings::where('name', 'expenses')->select('value')->first()['value'], true)[$student->level->name_en] }} ج
				</div>
			</div>
		</div>
		<hr>
		@if($student->subjects)
		<table class="table table-striped text-center mb-4">
			<thead>
				<tr class="text-center">
					<td colspan="2">المُعلمين والمواد</td>
				</tr>
			</thead>
			<tbody>
				@if($student->subjects)
				@forelse($student->subjects->teachers as $key => $value)
				<tr>
					<td>ال{{ \App\Models\Admin\Subject::where('name_en', $key)->select('name_ar')->first()['name_ar'] ?? 'فير معروف' }}</td>
					@php
					$teacherName = \App\Models\Admin\Teacher::with('profile')->where('id', $value)->first()->profile->name ?? '';
					@endphp
					<td>{{ !empty($teacherName) ? ('أ/ ' . $teacherName) : 'غير معروف' }}</td>
				</tr>
				@empty
				<tr>
					<td colspan="2">
						<div class="text-center alert alert-info mb-0">لا توجد بيانات عن المواد</div>
					</td>
				</tr>
				@endforelse
				@else
				<tr>
					<td colspan="2">
						<div class="text-center alert alert-info mb-0">لا توجد بيانات عن المواد</div>
					</td>
				</tr>
				@endif
			</tbody>
		</table>
		@endif
		<hr>
		<div class="container mb-4">
			<div class="row">
				<div class="col-6">كود الطالب: {{ $student->code }}</div>
			</div>
			<div class="form-text">
				<p class="mb-0">
					قم بإستخدام الكود للدخول لبوابة أولياء الأمور لمتابعة مستوى الطالب من الهاتف عن طريق الرابط: 
				</p>
				<span class="text-start d-block">
					{{ route('parents.home') }}
				</span>
			</div>
		</div>
		<hr>
		<div class="container">
			<div class="row">
				<div class="col-6">
					تم بواسطة: {{ $student->user->name ?? 'غير معروف' }}
				</div>
				<div class="col-6">رقم التواصل: {{ \App\Models\Admin\Settings::where('name', 'center_phone1')->select('value')->first()['value'] }}</div>
			</div>
		</div>
		@php
		$description = \App\Models\Admin\Settings::where('name', 'student_paper_text')->select('value')->first()['value'];
		@endphp
		@if(!empty($description))
		<hr>
		<div class="container">
			<div class="col-12">
				<div style="white-space: pre-line;">
					{!! $description !!}
				</div>
			</div>
		</div>
		@endif
	</div>
	<script>
		window.print();
	</script>
</body>

</html>