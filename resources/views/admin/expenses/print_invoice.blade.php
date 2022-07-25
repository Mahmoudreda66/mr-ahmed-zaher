<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>طباعة إيصال المصروفات</title>
	<link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
	<style>
		body {
			direction: rtl;
			font-family: cairo;
			color: #161616;
			font-size: 12px;
		}

		h1 {
			font-weight: bolder;
			font-size: 25px;
		}

		ul {
			list-style: square;
		}
	</style>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="mx-auto bg-white col-12 p-3">
				<div class="text-center brand-name fw-bold mb-3">
					<img src="/{{ \App\Models\Admin\Settings::where('name', 'center_logo')->first()['value'] }}" width="180px">
				</div>
				<div>
					<div class="invoice-info">
						<div class="mb-1">
							<span>تمت العملية في:</span>
							<span>
								{{ date('Y-m-d H:i', strtotime($expenses->created_at)) }}
							</span>
						</div>
						<div class="mb-2">
							<span>تمت بواسطة: </span>
							<span>
								{{ $expenses->user->name }}.
							</span>
						</div>
						<div class="mb-2">
							<table class="table mb-0">
								<thead>
									<tr>
										<td>الطالب</td>
										<td>المبلغ</td>
										<td>الشهر</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{ $expenses->student->name }}</td>
										<td>
											{{ $expenses->money }} ج
										</td>
										<td>شهر {{ $expenses->month }}</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p class="mb-2">قم بالإحتفاظ بهذا الإيصال لحين الحاجة إليه.</p>
						<p class="mb-0">
							<span>رقم الهاتف: </span>
							<span>{{ \App\Models\Admin\Settings::where('name', 'center_phone1')->select('value')->first()['value'] }}.</span>
						</p>
						<hr>
						<div class="d-flex justify-content-center">
							{!! DNS1D::getBarcodeSVG($expenses->id, 'C128'); !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>window.print();</script>
</body>

</html>