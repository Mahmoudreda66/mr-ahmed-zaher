@extends('admin.layouts.app', ['activePage' => 'users-management', 'titlePage' => "إدارة المستخدمين"])

@section('title')
إدارة المستخدمين
@endsection

@section('content')
<div class="content">

	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <!-- Edit Modal -->
	<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLabel">تعديل المستخدم</h5>
	            </div>
	            <div class="modal-body">
	                <form method="post" enctype="multipart/form-data" id="edit-user-form" autocomplete="off">
	                    @csrf
	                    {{ method_field('PUT') }}
	                    <div class="mb-3">
	                    	<label for="user_name">المستخدم</label>
	                    	<input type="text" name="user_name" id="user_name" class="form-control">
	                    	@error('user_name')
	                    	<small class="invalid-feedback d-block">{{ $message }}</small>
	                    	@enderror
	                    </div>
	                    <div class="mb-3">
	                    	<label for="phone">رقم الهاتف</label>
	                    	<input type="number" name="phone" id="phone" class="form-control">
	                    	@error('phone')
	                    	<small class="invalid-feedback d-block">{{ $message }}</small>
	                    	@enderror
	                    </div>
	                    <div class="mb-3">
	                    	<label class="d-block">المنصب</label>
	                    	@foreach($roles as $role)
	                    	<label class="mr-3 role-item">
	                    		<input type="checkbox" name="roles[]" id="{{ $role->name }}" value="{{ $role->id }}">
	                    		{{ $role->display_name }}
	                    	</label>
	                    	@endforeach
	                    	@error('roles')
	                    	<small class="invalid-feedback d-block">{{ $message }}</small>
	                    	@enderror
	                    </div>
	                    <div class="mb-3">
	                        <label for="old_password">كلمة السر القديمة</label>
	                        <input type="password" autocomplete="off" name="old_password" id="old_password" class="form-control">
	                        @error('old_password')
	                    	<small class="invalid-feedback d-block">{{ $message }}</small>
	                    	@enderror
	                    	@if(Session::has('password_check'))
	                    	<small class="invalid-feedback d-block">{{ Session::get('password_check') }}</small>
	                    	@endif
	                    </div>
	                    <div class="row">
	                        <div class="col-md-6 col-12 mb-3">
	                            <label for="new_password">كلمة السر الجديدة</label>
	                            <input type="password" autocomplete="off" name="new_password" id="new_password" class="form-control">
	                            @error('new_password')
                                <small class="invalid-feedback d-block">{{ $message }}</small>
                                @enderror
	                        </div>
	                        <div class="col-md-6 col-12 mb-3">
	                            <label for="password_confirmation">إعادة كلمة السر الجديدة</label>
                                <input type="password" autocomplete="off" name="password_confirmation" id="password_confirmation" class="form-control">
	                            @error('password_confirmation')
                                <small class="invalid-feedback d-block">{{ $message }}</small>
                                @enderror
	                        </div>
	                    </div>
	                    <input type="submit" value="" class="d-none">
	                </form>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-primary" onclick="document.getElementById('edit-user-form').submit();">حفظ</button>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Add Modal -->
	<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLabel">إضافة مستخدم</h5>
	            </div>
	            <div class="modal-body">
	                <form enctype="multipart/form-data" class="mb-0" action="{{ route('users.store') }}" method="post" id="add-user-form" autocomplete="off">
	                    @csrf
	                    <input type="hidden" id="id" name="id">
	                    <div class="row">
	                    	<div class="col-md-6 col-12 mb-3">
		                    	<label for="user_name">الإسم</label>
		                    	<input type="text" name="user_name" id="user_name" class="form-control" value="{{ old('user_name') }}">
		                    	@error('user_name')
		                    	<small class="invalid-feedback d-block">{{ $message }}</small>
		                    	@enderror
		                    </div>
		                    <div class="col-md-6 col-12 mb-3">
		                    	<label for="phone">رقم الهاتف</label>
		                    	<input type="number" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
		                    	@error('phone')
		                    	<small class="invalid-feedback d-block">{{ $message }}</small>
		                    	@enderror
		                    </div>
	                    </div>
	                    <div class="row">
	                    	<div class="col-md-6 col-12 mb-3">
	                    		<label for="password">كلمة السر</label>
	                    		<input type="password" name="password" id="password" class="form-control">
	                    		@error('password')
		                    	<small class="invalid-feedback d-block">{{ $message }}</small>
		                    	@enderror
	                    	</div>
	                    	<div class="col-md-6 col-12 mb-3">
	                    		<label for="password_confirmation">إعادة كتابة كلمة  السر</label>
	                    		<input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
	                    		@error('password_confirmation')
		                    	<small class="invalid-feedback d-block">{{ $message }}</small>
		                    	@enderror
	                    	</div>
	                    </div>
	                    <div class="mb-3">
	                    	<label class="d-block">المنصب</label>
	                    	@foreach($roles as $role)
	                    	<label class="mr-3 role-item">
	                    		<input type="checkbox" name="roles[]"
	                    		value="{{ $role->id }}" 
	                    		id="{{ $role->name }}">
	                    		{{ $role->display_name }}
	                    	</label>
	                    	@endforeach
	                    	@error('roles')
	                    	<small class="invalid-feedback d-block">{{ $message }}</small>
	                    	@enderror
	                    </div>
	                    <input type="submit" value="" class="d-none">
	                </form>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-primary" onclick="document.getElementById('add-user-form').submit();">حفظ</button>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Delete Modal -->
	<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="exampleModalLabel">حذف  مستخدم</h5>
	            </div>
	            <div class="modal-body">
	                <form method="post" id="delete-user-form" autocomplete="off">
	                    @csrf
	                    {{ method_field('DELETE') }}
	                    <div class="mb-0">
	                    	<label for="user_name">الإسم</label>
	                    	<input type="text" name="user_name" disabled id="user_name" class="form-control">
                    	</div>
	                    <input type="submit" value="" class="d-none">
	                </form>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-user-form').submit();">حذف</button>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Toggle Form -->
	<form method="post" id="toggle-form">
	    @csrf
	    {{ method_field('PUT') }}
	</form>

	<div class="container-fluid">
		<div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">جميع بيانات المستخدمين  المُسجلة</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center">
                                <thead class="text-primary">
                                    <th class="text-center" style="direction: ltr;">
                                        #ID
                                    </th>
                                    <th class="text-center">
                                        الإسم
                                    </th>
                                    <th class="text-center">
                                        رقم الهاتف
                                    </th>
                                    <th class="text-center">
                                        المنصب
                                    </th>
                                    <th class="text-center">
                                        حالة الحساب
                                    </th>
                                    <th class="text-center">
                                    	خيارات
                                    </th>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                    	<td>{{ $user->id }}</td>
                                    	<td>{{ $user->name }}</td>
                                    	<td>{{ $user->phone }}</td>
                                    	<td>
                                    		@php $userRoles = []; @endphp
                                    		@foreach($user->roles as $i => $role)
                                    		@php $userRoles[] = $role->id @endphp
                                    		{{ $role->display_name }} {{ $i < count($user->roles) - 1 ? '، ' : '' }}
                                    		@endforeach
                                    	</td>
                                    	<td>
                                    		@if($user->active == 1)
                                    		<small class="badge badge-success">
                                    			مفعل
                                    		</small>
                                    		@else
                                    		<small class="badge badge-danger">
                                    			غير مفعل
                                    		</small>
                                    		@endif
                                    	</td>
                                    	<td>
                                    		<i
                                    		data-id="{{ $user->id }}"
                                    		data-name="{{ $user->name }}"
                                    		class="delete-user cursor-pointer fas fa-trash text-danger"></i>
                                    		&nbsp;
                                    		<i
                                    		data-id="{{ $user->id }}"
                                    		data-name="{{ $user->name }}"
                                    		data-phone="{{ $user->phone }}"
                                    		data-roles="{{ json_encode($userRoles) }}"
                                    		class="edit-user cursor-pointer fas fa-edit text-success"></i>
                                    		&nbsp;
                                    		<i
                                    		data-id="{{ $user->id }}"
                                    		class="toggle-user-activity cursor-pointer fas fa-history text-info"></i>
                                    	</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="alert alert-info text-center mb-0">
                                            	لا يوجد مستخدمين حتى الآن
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pagination text-center mx-auto">
                        {{ $users->links("pagination::bootstrap-4") }}
                    </div>
                </div>
            </div>
        </div>
		<button class="btn btn-primary" onclick="new bootstrap.Modal(document.getElementById('addUserModal')).show();">
			<i class="fas fa-plus"></i>
			إضافة مستخدم
		</button>
	</div>
</div>
@endsection

@section('js')
<script src="{{ asset('/dist/js/users.js') }}"></script>
@if(Session::has('open_add_modal'))
<script>
	new bootstrap.Modal(document.getElementById('addUserModal')).show();
</script>
@endif

@if(Session::has('open_edit_modal'))
@foreach($errors->all() as $error)
<script>
	$.notify('{{ $error }}', 'error');
</script>
@endforeach
@endif
@endsection