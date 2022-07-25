@extends('videos.layouts.app')

@section('content')
<div class="content">
	<div class="bg-white p-3">
		<div class="main-heading mb-3">
			<h1>
				<span>الفيديوهات</span>
				<span> / </span>
				<span>
					{{ auth('videos')->user()->level->name_ar }}
				</span>
			</h1>
		</div>
		<div class="row videos-container">
			@forelse($videos as $video)
			<div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
				<a href="{{ route('videos.show', $video->id) }}">
					<div class="video-card">
						<div class="thumbnail">
							<span class="rounded"></span>
							<img src="{{ asset($video->thumbnail) }}" class="rounded" alt="صورة الفيديو">
						</div>
						<div class="video-title my-1">
							<span>
								{{ $video->title }}
							</span>
						</div>
					</div>
				</a>
			</div>
			@empty
			<div class="col-md-8 col-12 mx-auto">
				<div class="alert alert-info mb-0 text-center">
					لا يوجد فيديوهات حتى الآن.
				</div>
			</div>
			@endforelse
		</div>
		<div class="d-flex justify-content-center">
			{!! $videos->links("pagination::bootstrap-4") !!}
		</div>
		<div class="more-spinner-loader d-none">
			<span></span>
		</div>
	</div>
</div>
@endsection