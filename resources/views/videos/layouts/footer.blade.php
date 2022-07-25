	<footer class="main-footer bg-white p-3 text-center">
		<div class="row">
			<div class="col-md-4 col-sm-6 col-12 mb-3 mb-md-0">
				<div class="mt-5 text-center">
					<h1>{{ cache('app_name') }}</h1>
					<span>مدرس الرياضيات للثانوية العامة</span>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-12 mb-3 mb-md-0">
				<h4 class="fw-bold">روابط مختصرة</h4>
				<ul>
					<li>
						<a href="{{ route('videos.index') }}">
							الرئيسية
						</a>
					</li>
					<li>
						<a target="_blank" href="{{ route('studentsApplication.home') }}">
							سجل معنا
						</a>
					</li>
					<li>
						@guest('videos')
						<a>
							تسجيل الدخول
						</a>
						@endguest

						@auth('videos')
						<a onclick="document.getElementById('logout-form').submit()" class="cursor-pointer">
							تسجيل الخروج
						</a>
						@endauth
					</li>
					<li>
						<a target="_blank" href="{{ route('parents.home') }}">
							بوابة أولياء الأمور
						</a>
					</li>
				</ul>
			</div>
			<div class="col-md-4 col-sm-6 col-12 mb-3 mb-md-0">
				<h4 class="fw-bold">تواصل معنا</h4>
				<div class="icons-container text-center mt-5">
					<a href="https://www.facebook.com/profile.php?id=100066707950468" target="_blank">
						<i class="fab fa-facebook text-primary"></i>
					</a>
					<a href="https://wa.me/+201000384903" target="_blank">
						<i class="fab fa-whatsapp text-success"></i>
					</a>
					<a href="tel:+201000384903" target="_blank">
						<i class="fas fa-phone-alt text-info"></i>
					</a>
					<a href="">
						<i class="fas fa-envelope text-warning"></i>
					</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<small>© <a href="https://facebook.com/smartcentersystem" class="text-primary">سمارت سنتر لإدارة الدروس الخصوصية</a> 2022</small>
			</div>
		</div>
	</footer>
	@stack('js')
	<script src="{{ asset('dist/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('dist/js/videos/app.js') }}"></script>
</body>
</html>