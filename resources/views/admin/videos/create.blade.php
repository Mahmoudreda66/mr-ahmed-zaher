@extends('admin.layouts.app', ['activePage' => 'videos.create', 'titlePage' => "إضافة فيديو"])

@section('title')
إضافة فيديو
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="content">

	<!-- Upload Video Modal -->
	<div class="modal fade" id="videoModal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="modalTitle">رفع الفيديو</h5>
	            </div>
	            <div class="modal-body">
	            	<div id="uploading-area">
	            		<label>رفع الفيديو</label>
		            	<input type="file" name="video" id="video" class="d-none" accept="video/*">
		            	<label for="video" class="fake-file-input rounded shadow">
		            		إضغط هنا لتحميل الفيديو
		            		<br>
		            		<i class="fas fa-video"></i>
		            	</label>
		            	<small>لا يمكنك إعادة إختيار الفيديو في المستقبل</small><br>
		            	<small>السعة التحميلية القصوى هي 512MB</small>
	            	</div>
	            	<div id="preview-area" style="display: none;">
	            		<video style="width: 100%;" controls id="videoPreview">
  							<source type="video/mp4">
  						</video>
  						<label class="mt-1 btn btn-sm btn-secondary" for="video">
  							إعادة إختيار
  						</label>
	            	</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" onclick="location.href = '/admin';">إغلاق</button>
	                <button type="button" class="btn btn-primary" onclick="uploadVideo()">التالي</button>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="allpage-loader">
		<span></span>
	</div>

	<div class="progress" style="display: none; height: 22px;" title="يتم رفع الفيديو">
	  <div
	  id="progress-bar"
	  class="progress-bar progress-bar-striped progress-bar-animated"
	  style="width: 0%;"></div>
	</div>
    <div class="bg-white p-3 rounded shadow" id="video-data" style="display: none;">
		<div class="row">
			<input type="hidden" name="video_url">
			<div class="col-md-6 col-12 mb-3">
				<label for="title">عنوان الفيديو</label>
				<input type="text" name="title" id="title" class="form-control">
			</div>
			<div class="col-md-6 col-12 mb-3">
				<label for="level_id">المرحلة</label>
				<select name="level_id" id="level_id" class="form-control">
					<option value="NULL" disabled selected>إختر المرحلة</option>
					@foreach($levels as $level)
					<option value="{{ $level->id }}">
						{{ $level->name_ar }}
					</option>
					@endforeach
				</select>
			</div>
		</div>
    	<div class="row">
			<div class="col-12 mb-3">
				<label for="description">وصف الفيديو</label>
				<textarea name="description" id="description" class="form-control"></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-12 mb-3">
				<label>الصورة المُصغرة</label>
				<input type="file" name="thumbnail" id="thumbnail" accept="image/jpeg, image/png, image/jpg" class="d-none">
				<label for="thumbnail" class="fake-file-input">
					إضغط هنا لتحميل الصورة المُصغرة
					<br>
					<i class="fas fa-image"></i>
				</label>
				<small>مقاسات الصورة المُرجحة هي: 1280×720px</small>
			</div>
			<div class="col-md-6 col-12 mb-3 preview-thumnail-area" style="display: none;">
				<label>مُعاينة الصورة</label>
				<div class="img-thumbnail rounded-0">
					<img style="width: 100%;">
				</div>
			</div>
		</div>
    	<button class="btn btn-block btn-success mb-0" disabled id="submit-btn">حفظ الفيديو</button>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="{{ asset('dist/js/axios.min.js') }}"></script>
<script src="{{ asset('dist/js/create_video.js') }}"></script>
@endsection