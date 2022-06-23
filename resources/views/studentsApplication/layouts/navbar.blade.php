<nav class="navbar navbar-expand-lg navbar-dark" style="{{ $nav_transparent ? '' : 'background-color: #5896cd;' }}">
  <div class="container-fluid">
	<a class="navbar-brand" href="#">
		{{ cache('app_name', 'سمارت سنتر') }}
		<small style="font-size: 10px; color: #eee;">التقديم الإلكتروني</small>
	</a>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	  <span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarNav">
	<ul class="navbar-nav mb-2 mb-lg-0 me-auto">
		<li class="nav-item">
			<a href="{{ route('parents.home') }}" target="_blank" class="nav-link active">
				بوابة أولياء الأمور
			</a>
		</li>
	</ul>
	</div>
  </div>
</nav>