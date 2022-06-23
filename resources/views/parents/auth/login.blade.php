@extends('parents.layouts.app', ['nav_transparent' => true])

@section('css')
<style>
	body{
		background-image: url('{{ asset("dist/images/login bg.jpg");  }}');
		background-size: cover;
		background-repeat: no-repeat;
	}

	img.logo{
		width: 100%;
	}
</style>
@endsection

@section('title')
أولياء الأمور - تسجيل الدخول
@endsection

@section('content')
<div class="container mt-5">
	<div class="row">
		<div class="col-sm-7 col-12 mx-auto">
			<div class="bg-white rounded border shadow p-3">
				<div class="row">
					<div class="col-6">
						<div class="text-center mb-3 mt-4 border-start">
							<img
							src="/{{ $logo }}"
							alt="Center Logo"
							class="logo"
							onerror="this.src = '/dist/images/smart center logo.png';">
						</div>
					</div>
					<div class="col-6">
						<form action="{{ route('parents.check_login') }}" method="post"
						id="login-form" autocomplete="off" class="mb-3">
							@csrf
							<div class="mb-3">
								<label for="code" class="form-label"><small>كود الطالب</small></label>
								<input type="number" name="code" id="code" 
								class="form-control form-control-sm @error('code')
								is-invalid
								@enderror @if(Session::has('error')) is-invalid @endif"
								autofocus>
								@error('code')
									<small class="invalid-feedback d-block">{{ $message }}</small>
								@enderror
								@if(Session::has('error'))
								<small class="invalid-feedback d-block">{{ Session::get('error') }}</small>
								@enderror
							</div>
							<div class="d-grid">
								<button class="btn btn-sm btn-primary" type="submit">
									دخول
								</button>
							</div>
						</form>
						<div class="text-center">
							<p>قم بتسجيل الدخول إلى البوابة الإلكترونية ل{{ cache('app_name') }} وتابع مستوى الطالب في أي وقت ومن أي مكان.</p>
							<p>©<a href="https://facebook.com/smartcentersystem">سمارت سنتر لإدارة الدروس الخصوصية</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
	function resizeWindow () {
		let bodyEl = document.querySelector('body');
		bodyEl.style.height = window.innerHeight + 'px';
	}

	resizeWindow();
	
	window.onresize = function () {
		resizeWindow();
	}
</script>
@endsection