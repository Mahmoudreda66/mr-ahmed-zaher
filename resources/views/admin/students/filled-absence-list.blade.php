@extends('admin.layouts.app', ['activePage' => 'filled_absence_list', 'titlePage' => "كشف غياب مملوء"])

@section('title')
كشف غياب مملوء
@endsection

@section('content')
<div class="content">
    <!-- Filter Modal -->
	<div class="modal fade" id="filterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">الشهر</h5>
				</div>
				<div class="modal-body">
					<form action="{{ route('fill_absence_list') }}" method="get" id="filter-form" autocomplete="off">
						<div class="mb-3">
							<label for="month" class="form-label">المرحلة</label>
							<select name="level" id="level" class="form-control">
								<option value="NULL" disabled selected>إختر المرحلة</option>
								@foreach($levels as $level)
								<option
								{{ !empty($_GET['level']) ? ($_GET['level'] == $level->id ? 'selected' : '') : '' }}
								value="{{ $level->id }}">{{ $level->name_ar }}</option>
								@endforeach
							</select>
							@error('level')
							<small class="d-block invalid-feedback">{{ $message }}</small>
							@enderror
						</div>
						<div class="row mb-0">
							<div class="col-6">
								<label for="month" class="form-label">الشهر</label>
								<input type="number"
								name="month" id="month" class="form-control @error('month')
	                            is-invalid
	                        	@enderror" value="{{ $_GET['month'] ?? old('month', date('m')) }}">
								@error('month')
								<small class="d-block invalid-feedback">{{ $message }}</small>
								@enderror
							</div>
							<div class="col-6">
								<label for="year" class="form-label">السنة</label>
								<select name="year" id="year" class="form-control">
									@for($i = (date('Y') - 10); $i <= date('Y'); $i++)
									<option {{ $i == date('Y') ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
									@endfor
								</select>
								@error('year')
								<small class="d-block invalid-feedback">{{ $message }}</small>
								@enderror
							</div>
						</div>
						<input type="submit" value="" class="d-none">
					</form>
				</div>
				<div class="modal-footer">
					<a href="{{ route('home') }}">
						<button type="button" class="btn btn-secondary">إغلاق</button>
					</a>
					<button type="button" class="btn btn-primary" onclick="document.getElementById('filter-form').submit();">عرض</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
	let modalElement = document.getElementById('filterModal'),
		filterModal = new bootstrap.Modal(modalElement),
		urlData = new URLSearchParams(location.search);

	if(!urlData.has('month') || '{{ Session::has("open_filter_modal") }}'){
		filterModal.show();
	}
</script>
@endsection