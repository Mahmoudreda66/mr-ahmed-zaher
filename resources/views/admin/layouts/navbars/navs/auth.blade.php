<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <a class="navbar-brand mr-1" href="">{{ $titlePage }}</a>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
      <span class="sr-only">Toggle navigation</span>
      <span class="navbar-toggler-icon icon-bar"></span>
      <span class="navbar-toggler-icon icon-bar"></span>
      <span class="navbar-toggler-icon icon-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <a class="nav-link cursor-pointer">
          <i class="fas fa-print main-print-icon" onclick="window.print();" style="font-size: 17px;"></i>
        </a>
        <li class="nav-item dropdown">
          <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user" style="font-size: 17px;"></i>
            <p class="d-lg-none d-md-block">
              الملف الشخصي
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <a class="dropdown-item" href="{{ route('profile.edit') }}">تعديل الحساب</a>
            @if(auth()->user()->hasRole('teacher') && auth()->user()->teacher)
            <a class="dropdown-item" href="{{ route('teachers.show', auth()->user()->teacher->id) }}">عرض حساب المعلم</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="https://wa.me/+201274385491" target="_blank">تواصل مع المُطور</a>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">تسجيل الخروج</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>