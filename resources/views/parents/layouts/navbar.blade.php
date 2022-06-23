<nav class="navbar navbar-expand-lg navbar-dark {{ $nav_transparent ? '' : 'bg-info' }}">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
    	{{ cache('app_name', 'سمارت سنتر') }}
    	<small style="font-size: 10px; color: #eee;">أولياء الأمور</small>
	</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mb-2 mb-lg-0">
      	@auth('parents')
      	<li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#home">الرئيسية</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#expenses">المصروفات</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#absences">الغياب</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#marks">الدرجات</a>
        </li>
      	@endauth
      </ul>

      	@guest('parents')
      	<ul class="navbar-nav mb-2 mb-lg-0 me-auto">
	      	<li class="nav-item">
	      		<a href="{{ route('parents.login') }}" class="nav-link active">
	      			تسجيل الدخول
	      		</a>
	      	</li>
	    </ul>
      	@endguest

      	@auth('parents')
      	<ul class="navbar-nav mb-2 mb-lg-0 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth('parents')->user()->name }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <form action="{{ route('parents.logout') }}" method="post" id="logout-form">
                                @csrf
                            </form>
                            <a
                            onclick="document.getElementById('logout-form').submit();"
                            class="dropdown-item text-end"
                            href="javascript:void(0)">تسجيل الخروج</a>
                        </li>
                    </ul>
                </li>
            </ul>
      	@endauth
    </div>
  </div>
</nav>