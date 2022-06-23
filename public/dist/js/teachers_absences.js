let form 			= document.getElementById('add-absence'),
	teacherSelect 	= form.querySelector('select#teacher'),
	lessonSelect 	= form.querySelector('select#lesson'),
	groupsSelect 	= form.querySelector('select#group'),
	presentBtn 		= form.querySelector('button[name="present"]'),
	absentBtn		= form.querySelector('button[name="absent"]'),
	atInput 		= form.querySelector('input#at'),
	deleteBtns 		= document.querySelectorAll('i.delete-record'),
	toggleBtns 		= document.querySelectorAll('i.toggle-record');

teacherSelect.onchange = function () {
	if(this.value !== 'NULL' && this.value){ // valid

		let teacherId = this.value,
			getLessons = new XMLHttpRequest();

		getLessons.open('GET', '/admin/get-lessons-teacher/' + teacherId);
		getLessons.onload = function () {
			lessonSelect.innerHTML = '<option value="NULL" disabled selected>إختر الحصة</option>';

			if(this.status === 200 && this.readyState === 4){ // success request

				let lessonsData = JSON.parse(this.responseText);
				if(lessonsData.length > 0){
					lessonsData.forEach(el => {

						let optionEl = document.createElement('option'),
							optionTe = document.createTextNode('حصة ال' + el.subject.name_ar + ' - ' + el.level.name_ar);

						optionEl.appendChild(optionTe);
						optionEl.value = el.id;
						lessonSelect.appendChild(optionEl);
						lessonSelect.removeAttribute('disabled');

					});
				}else{
					$.notify('لم يتم العثور على حصص لهذا المعلم', 'error');
					lessonSelect.setAttribute('disabled', '');
				}

			}else{ // faild request
				$.notify('لقد خدث خطأ غير متوقع', 'error');
				lessonSelect.setAttribute('disabled', '');
			}
		}
		getLessons.send();

	}
}

function getGroups (lesson) {
	let getGroups = new XMLHttpRequest();
	getGroups.open('GET', '/admin/lessons-groups/get-groups-by-lesson/' + lesson);
	getGroups.onload = function () {
		if(this.readyState === 4 && this.status === 200){
			groupsSelect.setAttribute('disabled', '');
			getGroups.innerHTML = '<option value="NULL" disabled selected>إختر المجموعة</option>';
			let responseData = JSON.parse(this.responseText);

			responseData.forEach(el => {
				let option = document.createElement('option');
				option.value = el.id;
				option.textContent = el.group_name.length == 0 ? 'لا يوجد إسم' : el.group_name;

				groupsSelect.appendChild(option);
			});
			groupsSelect.removeAttribute('disabled');
		}else if(this.status === 404){
			$.notify('لم يتم العثور على الحصة', 'error');
		}else{
			$.notify('لقد حدث خطأ غير متوقع', 'error');
		}
	}
	getGroups.send();
}

lessonSelect.onchange = function () {
	if(this.value && this.value !== "NULL"){
		getGroups(this.value);
	}else{
		$.notify('قم بإختيار الحصة');
	}
}

function deleteItem (id) {
	let deleteForm = document.getElementById('delete-record');

	deleteForm.action = '/admin/teachers-absences/' + id;

	deleteForm.submit();
}

function toggleItem (el, id) {
	el.classList.add('text-info');
	el.classList.remove('text-success');
	el.classList.remove('text-danger');

	// prepare xhr
	let toggleRecord = new XMLHttpRequest();

	toggleRecord.open('PUT', '/admin/teachers-absences/toggle/' + id);
	toggleRecord.onload = function () {
		if(this.status === 200 && this.readyState === 4){ // success
			if(this.responseText){
				$.notify('تم تغيير حالة الحضور بنجاح', 'success');
			}
		}else if(this.status === 404){
			$.notify('لم يت العثور على التسجيل', 'warn');
		}else{
			$.notify('لقد حدث خطأ غير متوقع', 'warn');
		}
	}
	toggleRecord.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	toggleRecord.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + id);
}

// for(let i = 0; i < toggleBtns.length; i++){
// 	toggleBtns[i].onclick = function () {
// 		toggleBtns[i].classList.add('text-info');
// 		toggleBtns[i].classList.remove('text-success');
// 		toggleBtns[i].classList.remove('text-danger');

// 		// prepare xhr
// 		let toggleRecord = new XMLHttpRequest(),
// 			id = toggleBtns[i].dataset.id;

// 		toggleRecord.open('PUT', '/admin/teachers-absences/toggle/' + id);
// 		toggleRecord.onload = function () {
// 			if(this.status === 200 && this.readyState === 4){ // success
// 				if(this.responseText){
// 					$.notify('تم تغيير حالة الحضور بنجاح', 'success');
// 				}
// 			}else if(this.status === 404){
// 				$.notify('لم يت العثور على التسجيل', 'warn');
// 			}else{
// 				$.notify('لقد حدث خطأ غير متوقع', 'warn');
// 			}
// 		}
// 		toggleRecord.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
// 		toggleRecord.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + id);
// 	}
// }