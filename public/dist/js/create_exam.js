// global variables
let form = document.getElementById('add-exam'), // parent form
	teacherSelect = form.querySelector('select#teacher'), // teacher select box
	subjectInput = form.querySelector('input#subject'), // subject select box
	subjectId = form.querySelector('input#subject_id'), // subject id hidden input
	levelSelect = form.querySelector('select#level'); // level select box

teacherSelect.onchange = function () {

	let teacherId = this.value;
	if(teacherId && teacherId !== 'NULL'){

		let getSubject = new XMLHttpRequest();
		subjectInput.value = '';
		levelSelect.setAttribute('disabled', '');
		getSubject.open('GET', '/admin/get-subject_levels-by-teacher/' + teacherId);
		getSubject.onload = function () {
			if(this.status === 200 && this.readyState === 4){ // success request

				let subjectData = JSON.parse(this.responseText);
				subjectInput.value = 'ال' + subjectData.subject.name_ar;
				subjectId.value = subjectData.subject.id;

				let getLevels = new XMLHttpRequest();
				getLevels.open('GET', '/admin/levels/get-ids/' + subjectData.levels);
				getLevels.onload = function () {
					if(this.status === 200 && this.readyState === 4){ // success request

						levelSelect.removeAttribute('disabled');
						levelSelect.innerHTML = '<option value="NULL" disabled selected>إختر المرحلة</option>';
						let levelsData = JSON.parse(this.responseText);
						levelsData.forEach(element => {

							let optionEl = document.createElement('option');
							optionEl.textContent = element.name_ar;
							optionEl.value = element.id;
							levelSelect.appendChild(optionEl);

						});

					}else{ // failed request
						$.notify('لقد حدث خطأ غير متوقع', 'error');
					}
				}
				getLevels.send();

			}else if(this.status === 404) {
				location.reload();
			}else{ // failed request
				$.notify('لقد حدث خطأ غير متوقع', 'error');
			}
		}
		getSubject.send();
	}

}