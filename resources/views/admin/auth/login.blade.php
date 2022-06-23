@extends('admin.layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'login', 'title' => cache()->get('app_name')])

@section('title')
{{ cache()->get('app_name', 'سمارت سنتر') }}
@endsection

@section('content')
<div class="container" style="height: auto;">
    <div class="row align-items-center">
        <div class="col-md-9 ml-auto mr-auto mb-3 text-center">
            <h3>تسجيل الدخول للوحة تحكم {{ cache('app_name', 'سمارت سنتر') }}</h3>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
            <form class="form" method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                <div class="card card-login card-hidden mb-3">
                    <div class="card-header card-header-primary text-center">
                        <h4 class="card-title"><strong>تسجيل الدخول | الإدارة</strong></h4>
                    </div>
                    <div class="card-body">
                        <div class="bmd-form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                </div>
                                <input type="phone" autofocus placeholder="رقم الهاتف" name="phone" class="form-control" required>
                            </div>
                            @if ($errors->has('phone'))
                            <div id="phone-error" class="error text-danger pl-3" for="phone" style="display: block;">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="bmd-form-group{{ $errors->has('password') ? ' has-danger' : '' }} mt-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" placeholder="كلمة السر" name="password" id="password" class="form-control" required>
                            </div>
                            @if ($errors->has('password'))
                            <div id="password-error" class="error text-danger pl-3" for="password" style="display: block;">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="form-check mr-auto ml-0 mt-3 mb-0">
                            <label class="form-check-label mb-0">
                                تذكرني
                                <input class="mb-0" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            </label>
                        </div>
                    </div>
                    <div class="card-footer justify-content-center mt-0">
                        <button type="submit" class="btn btn-primary btn-link btn-lg mt-0">دخول</button>
                    </div>
                </div>
            </form>
            @if (Session::has('error'))
            <div class="alert alert-danger text-center mb-0">
                {{ Session::get('error') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection