let thumbnailFileInput = document.querySelector('input#thumbnail'),
	previewThumbnailArea = document.querySelector('.preview-thumnail-area'),
	submitBtn = document.querySelector('button#submit-btn'),
	videoFormData = document.getElementById('edit-form'),
	videoId = document.currentScript.dataset.videoId;

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

submitBtn.onclick = function () {
	let videoTitle = videoFormData.querySelector('input[name="title"]').value,
		videoLevel = videoFormData.querySelector('select[name="level_id"]').value,
		videoDescription = videoFormData.querySelector('textarea[name="description"]').value,
		videoThumbnail = videoFormData.querySelector('input[name="thumbnail"]').files[0],
		formData = new FormData(),
		errors = [];

	if(videoTitle == ""){
		errors.push('قم بكتابة عنوان الفيديو');
	}

	if(videoLevel == "" | videoLevel == "NULL"){
		errors.push('قم بإختيار المرحلة الخاصة بالفيديو');
	}

	if(errors.length > 0){
		errors.forEach(el => {
			$.notify(el);
		});
	}else{
		document.querySelector('.allpage-loader').style.display = 'flex';

		formData.append('videoTitle', videoTitle);
		formData.append('videoLevel', videoLevel);
		formData.append('videoDescription', videoDescription);
		formData.append('videoThumbanil', videoThumbnail);
		formData.append('_token', document.querySelector('meta[name="_token"]').content);
		formData.append('_method', 'PUT');

		axios({
			url: '/admin/videos-management/' + videoId,
			method: 'post',
			data: formData,
			headers: {
				'Content-type': 'multipart/form-data'
			}
		}).then(response => {
			if(response.data.status){
				location.href = '/admin/videos-management/' + videoId;
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
    height: 300,
    placeholder: "وصف الفيديو...",
    focus: false
});

$('#description').on('summernote.change', function (data, content) {
    $('#description').val(content);
});