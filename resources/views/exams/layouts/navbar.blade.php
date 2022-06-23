<nav class="navbar navbar-expand-lg navbar-dark {{ $nav_transparent ? '' : 'bg-primary' }}">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            {{ cache('app_name', 'سمارت سنتر') }}
            <small style="font-size: 10px; color: #eee;">الإختبارات</small>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0">
                @auth('students')
                <li class="nav-item">
                  <a class="nav-link {{ request()->is('students/exams') ? 'active' : '' }}" aria-current="page" href="{{ route('students.exams.index') }}">الرئيسية</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ request()->is('students/exams/results/*') || request()->is('students/exams/results') ? 'active' : '' }}" href="{{ route('students.results.index') }}">
                      الإختبارات السابقة
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('parents.home') }}" target="_blank">
                      بوابة أولياء الأمور
                  </a>
                </li>
                @endauth
            </ul>
            @guest('students')
            <ul class="navbar-nav mb-2 mb-lg-0 me-auto">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="">
                        تسجيل الدخول
                    </a>
                </li>
            </ul>
            @endguest
            @auth('students')
            <ul class="navbar-nav mb-2 mb-lg-0 me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth('students')->user()->name }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <form action="{{ route('students.logout') }}" method="post" id="logout-form">
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