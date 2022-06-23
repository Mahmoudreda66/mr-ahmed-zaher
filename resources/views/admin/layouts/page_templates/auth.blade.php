<div class="wrapper ">
  @include('admin.layouts.navbars.sidebar')
  <div class="main-panel" style="overflow: inherit !important;">
    @include('admin.layouts.navbars.navs.auth')
    @yield('content')
    @include('admin.layouts.footers.auth')
  </div>
</div>