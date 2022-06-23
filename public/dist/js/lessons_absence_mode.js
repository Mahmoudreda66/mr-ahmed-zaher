let code = document.getElementById('code'),
	level = document.getElementById('s_level'),
	form = document.getElementById('code-form'),
	lessonsSelect = document.getElementById('lesson'),
	groupsSelect = document.getElementById('group'),
	lessonGroupForm = document.getElementById('absence-form'),
	urlData = new URLSearchParams(location.search);

// get lessons of the level
function getLessons(level) {
	lessonsSelect.setAttribute('disabled', '');
	groupsSelect.setAttribute('disabled', '');
	if (level) {
		let getLessons = new XMLHttpRequest();
		getLessons.open('GET', '/admin/get-lessons/' + level);
		getLessons.onload = function () {
			if (this.status === 200 && this.readyState === 4) {
				let data = JSON.parse(this.responseText);
				lessonsSelect.innerHTML = '<option value="NULL" disabled selected>إختر الحصة</option>';

				if (Object.keys(data).length > 0 && data) {
					lessonsSelect.removeAttribute('disabled');
				} else {
					$.notify('لم يتم العثور على حصص لهذه المرحلة', 'error');
				}

				data.forEach(element => {
					let teacher_name = element[1],
						subject_name = element[0].subject.name_ar,
						option = document.createElement('option');
					option.textContent = 'حصة ال' + subject_name + ' - أ/ ' + teacher_name;
					option.value = element[0].id;
					if (urlData.has('lesson') && urlData.get('lesson') == element[0].id) {
						option.setAttribute('selected', '');
					}
					lessonsSelect.appendChild(option);
				});
			} else {
				$.notify('لقد حدث خطأ غير متوقع', 'warn');
			}
		}
		getLessons.send();
	}
}

function getGroups(lesson) {
	if (lesson && lesson !== "NULL") {
		groupsSelect.setAttribute('disabled', '');
		let getGroups = new XMLHttpRequest();
		getGroups.open('GET', '/admin/lessons-groups/get-groups-by-lesson/' + lesson);
		getGroups.onload = function () {
			if (this.readyState === 4 && this.status === 200) {
				let responseData = JSON.parse(this.responseText);
				if (Object.keys(responseData).length != 0) {
					groupsSelect.innerHTML = '<option value="NULL" disabled selected>إختر المجموعة</option>';
					responseData.forEach(el => {
						let optionEl = document.createElement('option');
						optionEl.setAttribute('value', el.id);
						optionEl.textContent = el.group_name.length == 0 ? 'لا يوجد إسم' : el.group_name;

						if (urlData.has('group') && urlData.get('group') == el.id) {
							optionEl.setAttribute('selected', '');
						}

						groupsSelect.appendChild(optionEl);
					});
					groupsSelect.removeAttribute('disabled');
				} else {
					$.notify('لا توجد مجموعات لهذه الحصة', 'error');
				}
			} else if (status === 404) {
				$.notify('لم يتم العثور على الحصة', 'error');
			} else {
				$.notify('لقد حدث خطأ غير متوقع', 'error');
			}
		}
		getGroups.send();
	}
}

lessonsSelect.onchange = function () {
	getGroups(this.value);
}

level.onchange = function () {
	getLessons(this.value);
}

if (urlData.has('level')) {
	getLessons(urlData.get('level'));
}

if (urlData.has('lesson')) {
	getGroups(urlData.get('lesson'));
}

if (urlData.has('lesson') && urlData.has('level')) { // absence mode

	let diffrentGroupModal = new bootstrap.Modal(document.getElementById('diffrentGroupModal')),
		diffrentGroupForm = document.getElementById('diffrent-group-form'),
		payExpensesForm = document.getElementById('expensesModal'),
		payExpensesModal = new bootstrap.Modal(payExpensesForm);

	// end lesson
	document.getElementById('end-lesson').onclick = function () {
		if(confirm('هل أنت متأكد من إغلاق الحصة ')){
			document.getElementById('allpage-loader').style.display = 'flex';
			let students = document.querySelectorAll('.student-token'),
				tokens = [];
			for (let i = 0; i < students.length; i++) {
				if (students[i].classList.contains('not')) {
					tokens.push(students[i].dataset.token);
				}
			}

			let endLesson = new XMLHttpRequest();

			endLesson.open('POST', '/admin/absences/end-lesson');
			endLesson.onload = function () {
				if (this.status === 200 && this.readyState === 4) { // success
					let data = JSON.parse(this.responseText);

					if (data.status) {
						document.getElementById('allpage-loader').style.display = 'none';
						if (data.data > 0) {
							$.notify('تم قفل الحصة بنجاح ولم يتم العثور على ' + data.data + ' طالب', 'warn');
						} else {
							$.notify('تم قفل الحصة بنجاح', 'success');
							$.notify('سيتم إعادة تحميل الصفحة', 'info');
						}

						setTimeout(function () {
							location.reload();
						}, 1800);
					} else {
						if (data.message == 'validation') {
							let errAudio = new Audio('/dist/sound/error.aac');
							errAudio.play();
							data.data.forEach(el => {
								$.notify(el, 'error')
							});
						}
					}
				} else {
					$.notify('لقد حدث خطأ غير متوقع', 'warn')
				}
			}
			endLesson.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			endLesson.send('_token=' + document.querySelector('meta[name="_token"]').content + '&tokens=' + JSON.stringify(tokens) + '&group=' + urlData.get('group') + '&date=' + urlData.get('date'));
		}
	}

	// show settings button
	window.onload = function () {
		setTimeout(function () {
			document.querySelector('.options-cog-btn').style.opacity = '1';
		}, 2000);
	}

	// function to send token
	function sendToken(token, route = '/admin/lessons-absence-mode', date = null) {
		let setAbsence = new XMLHttpRequest();
		setAbsence.open('POST', route);

		setAbsence.onload = function () {
			if (this.readyState === 4 && this.status === 200) { // success
				let data = JSON.parse(this.responseText);
				if (data.status) { // success message
					$.notify('تم تسجيل حضور الطالب ' + data.data.name, 'success');
					let successAudio = new Audio('/dist/sound/success.aac');
					successAudio.play();

					diffrentGroupModal.hide();
				} else { // error message
					if (data.message === 'validation') {
						let errAudio = new Audio('/dist/sound/error.aac');
						errAudio.play();
						data.data.forEach(el => {
							$.notify(el, 'error');
						});
					}
				}
			} else if (this.status === 404) {
				let errAudio = new Audio('/dist/sound/error.aac');
				errAudio.play();
				$.notify('لم يتم العثور على الطالب', 'warn')
			} else { // faild
				let errAudio = new Audio('/dist/sound/error.aac');
				errAudio.play();
				$.notify('لقد حدث خطأ غير متوقع', 'warn');
			}
		}

		if(date === null){
			date = urlData.get('date');
		}

		setAbsence.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		let httpString = '_token=' + document.querySelector('meta[name="_token"]').content + '&token=' + token + '&group=' + document.getElementById('group-id').value + '&date=' + date;
		setAbsence.send(httpString);
	}

	function submitDiffrenttGroup() {
		sendToken(codeInput.value, '/admin/another-group-lessons-absence-mode', diffrentGroupForm.querySelector('input#date').value);
	}

	// function to submit student token
	function submitToken() {
		let tokens = document.querySelectorAll('.student-token'),
			studentToken = document.querySelector('.student-token[data-token="' + code.value + '"]');

		if (code.value !== 'null') {
			if (studentToken) {
				for (let i = 0; i < tokens.length; i++) {
					let token = tokens[i].dataset.token;
					if (code.value == token && code.value !== 'null') {
						tokens[i].classList.remove('badge-warning');
						tokens[i].classList.remove('badge-info');
						tokens[i].classList.remove('badge-danger');
						tokens[i].classList.add('badge-success');
						tokens[i].classList.remove('not');

						sendToken(token);
					}
				}
			} else {
				let findStudent = new XMLHttpRequest();
				findStudent.open('GET', '/admin/students/find-by-id-group/' + codeInput.value + '/' + urlData.get('group'));
				findStudent.onload = function () {
					if (this.status === 200 && this.readyState === 4) {
						let responseData = JSON.parse(this.responseText);

						let errAudio = new Audio('/dist/sound/error.aac');
						errAudio.play();

						if(responseData.group){
							diffrentGroupForm.querySelector('input#group_name').value = responseData.group.group.group_name.length == 0 ? 'لا يوجد إسم' : responseData.group.group.group_name;
							diffrentGroupModal.show();
							$.notify('هذه ليست المجموعة الخاصة  بالطالب', 'error');

							diffrentGroupForm.onsubmit = function (e) {
								e.preventDefault();
								submitDiffrenttGroup();
							}
						}else{
							$.notify('الطالب غير مقيد بأي مجموعة', 'error');
						}
					} else if (this.status === 404) {
						let errAudio = new Audio('/dist/sound/error.aac');
						errAudio.play();
						$.notify('لم يتم العثور على الطالب', 'error');
					}
				}
				findStudent.send();
			}
		} else if (code.value === 'null') {
			let errAudio = new Audio('/dist/sound/error.aac');
			errAudio.play();
			$.notify('الطالب لا يمتلك شيفرة حتى الآن', 'error');
		} else {
			let errAudio = new Audio('/dist/sound/error.aac');
			errAudio.play();
			$.notify('لا يوجد طالب بهذا الرمز', 'error');
		}

		code.select();
	}

	// submit paying expenses
	function payExpenses (id, money, month) {
		let sendExpenses = new XMLHttpRequest();

		sendExpenses.open('POST', '/admin/expenses');
		sendExpenses.onload = function () {
			if(this.readyState === 4 && this.status === 200){
				let responseData = JSON.parse(this.responseText),
					dateObject = new Date();
				if(responseData.status){
					payExpensesModal.hide();
					$.notify('تم دفع المصروفات بنجاح', 'success');

					if(month == dateObject.getMonth() + 1){
						let element = document.querySelector('div[data-token="' + id + '"] i');

						if(element){
							element.classList.remove('fa-ban');
							element.classList.add('fa-check');
						}
					}
				}else{
					if(responseData.message === 'validation'){
						responseData.data.forEach(el => {
							$.notify(el, 'error');
						});
					}
				}
			}
		}

		sendExpenses.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		sendExpenses.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		sendExpenses.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + id + '&expenses=' + money + '&month=' + month);
	}

	payExpensesForm.onsubmit = function (e) {
		e.preventDefault();
		payExpenses(this.querySelector('input#id').value, this.querySelector('input#expenses').value, this.querySelector('input#month').value);
	}

	document.getElementById('submitExpenses').onclick = function () {
		payExpenses(document.querySelector('form#expenses-form input#id').value, document.querySelector('form#expenses-form input#expenses').value, document.querySelector('form#expenses-form input#month').value);
	}

	// recieve student qr code
	form.onsubmit = function (e) {
		e.preventDefault();
		submitToken();
	}

	// show absence model
	let openModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
	openModal.show();

	document.getElementById('close-modal').onclick = function () {
		openModal.hide();
	}

	let codeInput = document.getElementById('code');
	
	// xhr object for getting all students of level
	let getStudents = new XMLHttpRequest();

	getStudents.open('GET', '/admin/lessons-groups/get-students-by-group/' + urlData.get('group'));
	getStudents.onload = function () {
		if (this.status === 200 && this.readyState === 4) { // success
			let data = JSON.parse(this.responseText),
				today = urlData.get('date'),
				dateObg = new Date();

			if (data[0].students.length > 0) {
				document.getElementById('students-absence-loader').style.display = 'none';
				data[0].students.forEach(element => {
					let divParentElement = document.createElement('div'),
						divChildElement = document.createElement('div'),
						studentName = document.createTextNode(element.name),
						iEl = document.createElement('i'),
						expensesList = element.expenses;

					divParentElement.classList.add('col-sm-4', 'col-6', 'mb-3');
					divChildElement.classList.add('badge', 'student-token', 'not');
					iEl.classList.add('fas', 'text-warning', 'pl-1');
					divChildElement.setAttribute('data-token', element.id);
					divChildElement.setAttribute('data-name', element.name);
					divChildElement.setAttribute('data-level', element.level.name_en);
					divChildElement.appendChild(studentName);
					divChildElement.appendChild(iEl);
					divParentElement.appendChild(divChildElement);
					document.getElementById('students-container').appendChild(divParentElement);

					if(expensesList.length == 0){
						iEl.classList.add('fa-ban');
					}else{
						let paidMonths = [];

						for(let i = 0; i < expensesList.length; i++){
							paidMonths.push(expensesList[i].month);
						}

						if(paidMonths.includes(dateObg.getMonth() + 1)){
							iEl.classList.add('fa-check');
						}else{
							iEl.classList.add('fa-ban');
						}
					}

					// preparing absences style
					if (element.absence_list.length !== 0) {
						element.absence_list.forEach(absence => {
							if (absence.lessons_group_id == urlData.get('group')) {
								if (absence.join_at == today && absence.status == 1) {
									divChildElement.classList.add('badge-success');
									divChildElement.classList.remove('not');
								} else if (absence.join_at == today && absence.status == 0) {
									divChildElement.classList.remove('not');
									divChildElement.classList.add('badge-danger');
								}
							}
						});
					}

					let studentsDivs = document.querySelectorAll('.student-token');

					for (let i = 0; i < studentsDivs.length; i++) {
						studentsDivs[i].ondblclick = function () {
							code.value = studentsDivs[i].dataset.token;
							submitToken();
						}

						studentsDivs[i].querySelector('i').onclick = function () {
							payExpensesModal.show();
							let studentData = this.parentElement.dataset,
								expenses = document.querySelector('form#expenses-form input#expenses'),
								getExpenses = new XMLHttpRequest();

							document.querySelector('form#expenses-form input#id').value = studentData.token;
							document.querySelector('form#expenses-form input#student').value = studentData.name;


						    expenses.setAttribute('readonly', '');

						    getExpenses.open('GET', '/admin/settings/expenses/' + studentData.level);
						    getExpenses.onload = function () {
						        expenses.removeAttribute('readonly');
						        if (this.readyState == 4 && this.status == 200) {
						            expenses.value = this.responseText;
						        } else if (this.status == 404) {
						            alert('لقد حدثت مشكلة في جلب القيمة الإفتراضية لمصاريف الطالب.. راجع إعدادات المصروفات وأعد المحاولة');
						        }
						    }
						    getExpenses.send();
						}
					}
				});

				for (let i = 0; i < document.querySelectorAll('.student-token').length; i++) {
					document.querySelectorAll('.student-token')[i].click();
				}
			} else {
				$.notify('لا يوجد طلاب مربوطين بهذه المجموعة', 'error');
				document.getElementById('students-absence-loader').style.display = 'none';
			}
		} else {
			$.notify('لقد حدث خطأ غير متوقع', 'warn');
		}
	}
	getStudents.send();
}

lessonGroupForm.onsubmit = function (e) {
	if (level.value === "NULL" || !level.value) {
		e.preventDefault();
		level.classList.add('is-invalid');
		this.querySelector('small.level').textContent = 'قم بإختيار المرحلة';
	} else {
		level.classList.remove('is-invalid');
		this.querySelector('small.level').textContent = '';

		if (lessonsSelect.value === "NULL" || !lessonsSelect.value) {
			e.preventDefault();
			lessonsSelect.classList.add('is-invalid');
			this.querySelector('small.lesson').textContent = 'قم بإختيار المجموعة';
		} else {
			lessonsSelect.classList.remove('is-invalid');
			this.querySelector('small.lesson').textContent = '';

			if (groupsSelect.value === "NULL" || !groupsSelect.value) {
				e.preventDefault();
				groupsSelect.classList.add('is-invalid');
				this.querySelector('small.group').textContent = 'قم بإختيار المجموعة';
			} else {
				groupsSelect.classList.remove('is-invalid');
				this.querySelector('small.group').textContent = '';
			}
		}
	}
}
