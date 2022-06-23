@extends('admin.layouts.app', ['activePage' => 'expenses.index', 'titlePage' => 'المصروفات'])

@section('title')
المصروفات
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatables.css') }}">
<link rel="stylesheet" href="{{ asset('dist/css/bootstrap4_datatablesButtons.css') }}">
@endsection

@section('content')
<div class="content">

	@if (Session::has('success'))
	<div class="alert alert-success">{{ Session::get('success') }}</div>
	@endif

	@if(Session::has('error'))
	<div class="alert alert-danger">{{ Session::get('error') }}</div>
	@endif

	<form method="post" id="records-actions">
		@csrf
		<input type="hidden" name="_method">
	</form>

	<!-- Filter Modal -->
	<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">الشهر</h5>
				</div>
				<div class="modal-body">
					<form action="{{ route('expenses.index') }}" method="get" id="filter-form" autocomplete="off">
						<div class="row">
	                        <div class="col-6">
	                            <label for="month" class="form-label">الشهر</label>
	                            <input type="number" min="1" max="12" name="month" id="month" autofocus class="form-control" value="{{ $_GET['month'] }}">
	                        </div>
	                        <div class="col-6">
	                            <label for="year" class="form-label">السنة</label>
	                            <select name="year" id="year" class="form-control">
	                                @for($i = (date('Y') - 10); $i <= date('Y'); $i++)
	                                <option {{ isset($_GET['year']) ? ($_GET['year'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : '') }} value="{{ $i }}">{{ $i }}</option>
	                                @endfor
	                            </select>
	                        </div>
	                    </div>
						<input type="submit" value="" class="d-none">
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
					<button type="button" class="btn btn-primary" onclick="document.getElementById('filter-form').submit();">عرض</button>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card mb-2">
					<div class="card-body">
						<div class="table-responsive">
							{{ $dataTable->table(['class' => 'table table-hover text-center']) }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<button class="btn btn-primary" id="show-month">
			<i class="fas fa-eye"></i>
			عرض شهر معين
		</button>
	</div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/jquery_datatables.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap4_datatables.js') }}"></script>
<script src="{{ asset('dist/js/datatables_buttons.js') }}"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
<script src="{{ asset('/dist/js/expenses.js') }}"></script>
{!! $dataTable->scripts() !!}
@endsection