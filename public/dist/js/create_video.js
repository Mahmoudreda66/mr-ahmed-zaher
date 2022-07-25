let uploadVideoModal = new bootstrap.Modal(document.getElementById('videoModal')),
	uploadingArea = document.getElementById('uploading-area'),
	videoInput = document.getElementById('video'),
	progressBar = document.getElementById('progress-bar'),
	videoFormData = document.getElementById('video-data'),
	submitBtn = videoFormData.querySelector('button#submit-btn'),
	thumbnailFileInput = videoFormData.querySelector('input#thumbnail'),
	previewThumbnailArea = document.querySelector('.preview-thumnail-area');

uploadVideoModal.show();

videoInput.onchange = function () {
	let videoFile = this.files[0],
		videoExtension = videoFile.name.split('.').pop().toLowerCase(),
		allowedExtensions = ['mp4', 'ogm', 'wmv', 'mbg', 'webm',
		'ogv', 'mov', 'asx', 'mpeg', 'm4v', 'avi'],
		errors = [];

	// validation
	if(!allowedExtensions.includes(videoExtension)){
		errors.push('الإمتداد غير مسموح به. الإمتدادات المسموحة فقط هي: ' + allowedExtensions.join(', '));
	}

	if(errors.length > 0){
		errors.forEach(el => {
			$.notify(el)
		});
	}else{
		let temporaryVideoUrl = URL.createObjectURL(videoFile),
			previewArea = document.getElementById('preview-area');

		previewArea.querySelector('video#videoPreview source').src = temporaryVideoUrl;
		previewArea.style.display = 'block';
		uploadingArea.style.display = 'none';

		previewArea.querySelector('video#videoPreview').load();
	}
}

thumbnailFileInput.onchange = function () {
	let thumbnailFile = this.files[0],
		thumbnailExtension = thumbnailFile.name.split('.').pop().toLowerCase(),
		allowedExtensions = ['jpeg', 'png', 'jpg'],
		errors = [];

	// validation
	if(!allowedExtensions.includes(thumbnailExtension)){
		errors.push('إمتداد الصورة غير صالح. الإمتدادات المسموح بها هي: ' + allowedExtensions.join(', '));
	}

	if(errors.length > 0){
		errors.forEach(el => {
			$.notify(el);
		});
	}else{
		let temporaryThumbnailUrl = URL.createObjectURL(thumbnailFile);

		previewThumbnailArea.querySelector('img').src = temporaryThumbnailUrl;
		previewThumbnailArea.style.display = 'block';
	}
}

function uploadVideo () {
	uploadVideoModal.hide();
	videoFormData.style.display = 'block';
	$.notify('يتم رفع الفيديو الآن', 'success');

	let videoFile = videoInput.files[0],
		sendVideoObject = new XMLHttpRequest(),
		formData = new FormData();

	formData.append('_token', document.querySelector('meta[name="_token"]').content);
	formData.append('video', videoFile);

	axios({
		method: 'post',
		url: '/admin/videos-management/upload',
		data: formData,
		headers: {
			'Content-Type': 'multipart/form-data'
		},
		onUploadProgress (progressEvent) {
            let progress = Math.floor((progressEvent.loaded * 100) / progressEvent.total);

            progressBar.style.width = progressBar.textContent = (progress + '%');
            progressBar.parentElement.style.display = 'block';
        }
	}).then(response => {
		if(response.data.status){
			progressBar.classList.remove('progress-bar-animated');

			$.notify('تم رفع الفيديو بنجاح', 'success');

			submitBtn.removeAttribute('disabled');
			videoFormData.querySelector('input[name="video_url"]').value = response.data.data;
		}else{
			if(response.data.message == 'video not uploaded'){
				$.notify('لم يتم رفع الفيديو');
			}else{
				response.data.data.forEach(el => {
					$.notify(el);
				});
			}
		}
	}).catch(error => {
		alert('لقد حدث خطأ اثناء رفع الفيديو. يرجى العلم أن السعة التحميلية القصوى هي 512MB');
	});
}

submitBtn.onclick = function () {
	let videoPath = videoFormData.querySelector('input[name="video_url"]').value,
		videoTitle = videoFormData.querySelector('input[name="title"]').value,
		videoLevel = videoFormData.querySelector('select[name="level_id"]').value,
		videoDescription = videoFormData.querySelector('textarea[name="description"]').value,
		videoThumbnail = videoFormData.querySelector('input[name="thumbnail"]').files[0],
		formData = new FormData(),
		errors = [];

	if(videoPath == "" || videoPath == null){
		errors.push('لم يتم رفع الفيديو بشكل صحيح');
	}

	if(videoTitle == ""){
		errors.push('قم بكتابة عنوان الفيديو');
	}

	if(videoLevel == "" | videoLevel == "NULL"){
		errors.push('قم بإختيار المرحلة الخاصة بالفيديو');
	}

	if(videoThumbnail == "" | videoThumbnail == null){
		errors.push('قم بإختيار الصورة المُصغرة');
	}

	if(errors.length > 0){
		errors.forEach(el => {
			$.notify(el);
		});
	}else{
		document.querySelector('.allpage-loader').style.display = 'flex';

		formData.append('videoPath', videoPath);
		formData.append('videoTitle', videoTitle);
		formData.append('videoLevel', videoLevel);
		formData.append('videoDescription', videoDescription);
		formData.append('videoThumbanil', videoThumbnail);
		formData.append('_token', document.querySelector('meta[name="_token"]').content);

		axios({
			url: '/admin/videos-management',
			method: 'post',
			data: formData,
			headers: {
				'Content-type': 'multipart/form-data'
			}
		}).then(response => {
			if(response.data.status){
				location.href = '/admin/videos-management/' + response.data.data.id;
			}
		}).catch(error => {
			if(error.response.status == 422){
				for(item in error.response.data.errors){
					$.notify(error.response.data.errors[item][0]);
				}
			}

			document.querySelector('.allpage-loader').style.display = 'none';
		});
	}
}

$('#description').summernote({
    height: 200,
    placeholder: "وصف الفيديو...",
    focus: false
});

$('#description').on('summernote.change', function (data, content) {
    $('#description').val(content);
});