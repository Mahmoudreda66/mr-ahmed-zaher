<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('videos.index') }}">
      <img
      src="/{{ \App\Models\Admin\Settings::where('name', 'center_logo')->first()['value'] }}"
      alt="logo" width="120" class="d-inline-block align-text-top">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-0 me-lg-5 mb-2 mb-lg-0">
        <li class="nav-item me-0 me-lg-5">
          <a class="nav-link {{ !request()->is('videos/login') ? 'active' : '' }}" href="{{ route('videos.index') }}">
              <i class="fas fa-play"></i>
              <span>الفيديوهات</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="{{ route('studentsApplication.home') }}" target="_blank">
              <i class="fas fa-user"></i>
              <span>سجل معنا</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link cursor-pointer {{ request()->is('videos/login') ? 'active' : '' }}">
              @guest('videos')
              <i class="fas fa-lock"></i>
              <span>تسجيل الدخول</span>
              @endguest

              @auth('videos')
              <form action="{{ route('videos.logout') }}" method="post" id="logout-form">
                @csrf
              </form>
              <span onclick="document.getElementById('logout-form').submit()">
                <i class="fas fa-unlock"></i>
                <span>تسجيل الخروج</span>
              </span>
              @endauth
          </a>
        </li>
      </ul>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="https://www.facebook.com/profile.php?id=100066707950468" target="_blank">
              <i class="fab fa-facebook text-primary"></i>
              <span>فيسبوك</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://wa.me/+201000384903" target="_blank">
              <i class="fab fa-whatsapp text-success"></i>
              <span>واتساب</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tel:+2{{ \App\Models\Admin\Settings::where('name', 'center_phone1')->first()['value'] }}" target="_blank">
              <i class="fas fa-phone-alt text-info"></i>
              <span>إتصل بنا</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>