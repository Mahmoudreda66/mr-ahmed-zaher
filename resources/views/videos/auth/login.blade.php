@extends('videos.layouts.app')

@section('content')
<div class="content">
	<div class="bg-white p-3">
		<div class="row login-area">
			<div class="col-lg-8 col-md-6 col-12 mb-3 border-start">
				<div class="text-center py-5">
					<h1 class="fw-bold">{{ cache('app_name') }}</h1>
					<span>
						<span class="fw-bold" style="font-size: 20px;">چوكر</span>
						<span>(الرياضيات والMaths)</span>
					</span>
					<img
					src="/{{ \App\Models\Admin\Settings::where('name', 'center_logo')->first()['value'] }}"
					alt="logo" width="200" class="d-block mx-auto">
				</div>
			</div>
			<div class="col-lg-4 col-md-6 col-12">
				<form action="{{ route('videos.attempt_login') }}" method="post">
					@csrf
					<div class="mb-3">
						<label for="code" class="form-label">كود الطالب</label>
						<input type="number" autofocus name="code" id="code"
						class="form-control @error('code') is-invalid @enderror"
						placeholder="كود الطالب...">
						@error('code')
						<small class="invalid-feedback">{{ $message }}</small>
						@enderror
					</div>
					<div class="d-grid gap-2">
						<button class="btn btn-success">
							<i class="fas fa-key"></i>
							تسجيل الدخول
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection