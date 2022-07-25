@extends('admin.layouts.app', ['activePage' => 'videos.index', 'titlePage' => "عرض الفيديو"])

@section('title')
عرض الفيديو
@endsection

@section('css')
<link href="{{ asset('dist/css/video-js.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="content">

	<div class="allpage-loader">
		<span></span>
	</div>

	@if(auth()->user()->hasPermission('edit-video'))
	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
	    <div class="modal-dialog" style="max-width: 900px;">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="editModalLabel">تعديل بيانات الفيديو</h5>
	            </div>
	            <div class="modal-body">
	                <div id="edit-form">
	                    <div class="row">
	                    	<div class="col-md-6 col-12 mb-3">
	                    		<label for="title">عنوان الفيديو</label>
	                    		<input
	                    		type="text"
	                    		name="title"
	                    		id="title"
	                    		class="form-control"
	                    		value="{{ $video->title }}">
	                    	</div>
	                    	<div class="col-md-6 col-12 mb-3">
	                    		<label for="level_id">المرحلة</label>
	                    		<select name="level_id" id="level_id" class="form-control">
	                    			<option value="NULL" disabled selected>إختر المرحلة</option>
	                    			@foreach($levels as $level)
	                    			<option
	                    			{{ $video->level_id == $level->id ? 'selected' : '' }}
	                    			value="{{ $level->id }}">
	                    				{{ $level->name_ar }}
	                    			</option>
	                    			@endforeach
	                    		</select>
	                    		@error('level')
	                    		<small class="invalid-feedback d-block">{{ $message }}</small>
	                    		@enderror
	                    	</div>
	                    </div>
	                    <div class="row">
	                    	<div class="col-12 mb-3">
	                    		<label for="description">وصف الفيديو</label>
	                    		<textarea name="description" id="description" class="form-control">{!! $video->description !!}</textarea>
	                    	</div>
	                    </div>
	                    <div class="row">
							<div class="col-md-6 col-12 mb-3 mb-md-0">
								<label>الصورة المُصغرة</label>
								<input
								type="file"
								name="thumbnail"
								id="thumbnail"
								accept="image/jpeg, image/png, image/jpg"
								class="d-none">
								<label for="thumbnail" class="fake-file-input">
									إضغط هنا لتحميل الصورة المُصغرة
									<br>
									<i class="fas fa-image"></i>
								</label>
								<small>مقاسات الصورة المُرجحة هي: 1280×720px</small>
							</div>
							<div class="col-md-6 col-12 mb-3 mb-md-0 preview-thumnail-area" style="display: none;">
								<label>مُعاينة الصورة</label>
								<div class="img-thumbnail rounded-0">
									<img style="width: 100%;">
								</div>
							</div>
						</div>
	                    <input type="submit" value="" class="d-none">
	                </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-primary" id="submit-btn">حفظ</button>
	            </div>
	        </div>
	    </div>
	</div>
	@endif

	@if(auth()->user()->hasPermission('delete-video'))
	<!-- Delete Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="deleteModalLabel">حذف الفيديو</h5>
	            </div>
	            <div class="modal-body">
	                <form action="{{ route('videos-management.destroy', $video->id) }}" class="mb-0" method="post" id="delete-form">
	                    @csrf
	                    {{ method_field('delete') }}
	                    <div style="font-size: 15px;">هل تريد بالفعل حذف الفيديو؟ لن يمكنك إستعادته مرة أخرى</div>
	                </form>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
	                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').submit();">حذف</button>
	            </div>
	        </div>
	    </div>
	</div>
	@endif

    <div class="bg-white p-3 rounded shadow">
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-md-7 col-12 mb-3">
    				<div class="video-container">
						<video
							style="width: 100%;"
							height="380"
						    id="my-video"
						    class="video-js"
						    controls
						    preload="auto"
						    poster="/{{ $video->thumbnail }}"
						    data-setup="{}">
						    <source src="/{{ $video->video }}" type="video/mp4" />
						    <p class="vjs-no-js">
						      قم بتفعيل الجافاسكربت في المتصفح الخاص بك أو قم بتحديث المتصفح
						    </p>
						</video>
						<p class="mt-3 mb-0" style="font-size: 19px;">{{ $video->title }}</p>
						<hr>
						<p class="mb-0">
							{!! $video->description !!}
						</p>
					</div>
    			</div>
    			<div class="col-md-5 col-12">
    				<div class="img-thumbnail rounded-0 mb-3">
    					<img src="/{{ $video->thumbnail }}" style="width: 100%;">
    				</div>
    				<div class="buttons-container d-flex justify-content-center">
    					@if(auth()->user()->hasPermission('edit-video'))
						<button class="btn btn-success mb-0 mr-2" data-bs-toggle="modal" data-bs-target="#editModal">
							<i class="fas fa-edit"></i>
							تعديل الفيديو
						</button>
    					@endif
    					@if(auth()->user()->hasPermission('delete-video'))
						<button class="btn btn-danger mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal">
							<i class="fas fa-trash"></i>
							حذف الفيديو
						</button>
    					@endif
					</div>
    			</div>
    		</div>
    	</div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('dist/js/video-js.js') }}"></script>
<script src="{{ asset('dist/js/axios.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="{{ asset('dist/js/edit_video.js') }}" data-video-id="{{ $video->id }}"></script>

@if(isset($_GET['edit']))
<script>
	let editModal = new bootstrap.Modal(document.getElementById('editModal'));

	editModal.show();
</script>
@endif

@if(isset($_GET['delete']))
<script>
	let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

	deleteModal.show();
</script>
@endif

@endsection