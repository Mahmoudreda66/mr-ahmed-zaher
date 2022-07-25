@extends('videos.layouts.app')

@push('css')
<link href="https://vjs.zencdn.net/7.20.1/video-js.css" rel="stylesheet">
@endpush

@section('content')
<div class="content">
	<div class="bg-white p-3">
		<div class="row">
			<div class="col-md-7 col-12 mb-3 border-start video-area">
				<div class="video-container">
					<video
					    id="my-video"
					    class="video-js"
					    controls
					    preload="auto"
					    poster="{{ asset($video->thumbnail) }}"
					    data-setup="{}">
					    <source src="{{ asset($video->video) }}" type="video/mp4" />
					    <p class="vjs-no-js">
					      قم بتفعيل الجافاسكربت في المتصفح الخاص بك أو قم بتحديث المتصفح
					    </p>
					</video>
				</div>
				<div class="accordion mt-3" id="video-description">
				  <div class="accordion-item">
				    <h2 class="accordion-header" id="video-title">
				      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
				        <p class="video-title ps-2">{{ $video->title }}</p>
				      </button>
				    </h2>
				    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="video-title" data-bs-parent="#video-description">
				      <div class="accordion-body">
				        <p class="video-description">
				        	{!! $video->description !!}
						</p>
				      </div>
				    </div>
				  </div>
				</div>
			</div>
			<div class="col-md-5 col-12 suggestions-area">
				<div class="main-heading mb-3">
					<h1>
						فيديوهات مُقترحة
					</h1>
					<span></span>
				</div>
				<div class="row">
					@foreach($suggests_videos as $key => $item)
						@if($video->id != $item->id)
						<div class="col-md-6 col-12 mb-3">
							<a href="{{ $item->id }}">
								<div class="video-card">
									<div class="thumbnail">
										<span class="rounded"></span>
										<img src="{{ asset($item->thumbnail) }}" class="rounded" alt="صورة الفيديو">
									</div>
									<div class="video-title my-1">
										<span>
											{{ strlen($item->title) > 100 ? substr($item->title, 0, 99) . '...' : $item->title }}
										</span>
									</div>
								</div>
							</a>
						</div>
						@else
							@if(count($suggests_videos) < 2)
							<div class="col-12">
								<div class="alert alert-info mb-0 text-center">
									لا يوجد فيديوهات حتى الآن.
								</div>
							</div>
							@endif
						@endif
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('js')
<script src="https://vjs.zencdn.net/7.20.1/video.min.js"></script>
@endpush