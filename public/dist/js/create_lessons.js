// global variables
let form = document.getElementById('add-lesson'), // the container form
	teacherSelect = form.querySelector('select#teacher'), // teacher select box
	levelSelect = form.querySelector('select#level'), // lesson select box
	subjectInput = form.querySelector('input#subject'), // subject input
	subjectId = form.querySelector('input#subject-id'), // subject id input
	addGroupBtn = form.querySelector('button#add-group-btn'), // add time button
	timesContainer = form.querySelector('div#times-container'), // times container element
	loader = document.getElementById('allpage-loader'),
	addGroupModal = new bootstrap.Modal(document.getElementById('groupsModal')),
	addGroupForm = document.getElementById('add-group-form'),
	addTimeInGroupBtn = addGroupForm.querySelector('button#add-time'),
	allTimes = [],
	tableContainer = document.querySelector('#groups-table');

teacherSelect.onchange = function () {
	teacherId = this.value;

	if (teacherId !== 'NULL' && teacherId) {
		levelSelect.setAttribute('disabled', '');

		let getLevels = new XMLHttpRequest();
		getLevels.open('GET', '/admin/teacher-levels-subject/' + teacherId);
		getLevels.onload = function () {
			let teacherData = JSON.parse(this.responseText);
			if (this.readyState === 4 && this.status === 200) { // success request

				let teachersLevels = JSON.parse(this.responseText),
					getLevelname = new XMLHttpRequest();
				getLevelname.open('GET', '/admin/levels/get-ids/' + teacherData.levels);
				getLevelname.onload = function () {

					levelSelect.innerHTML = '<option value="NULL" disabled selected>إختر الصف</option>';

					subjectInput.value = 'ال' + teacherData.subject.name_ar;
					subjectId.value = teacherData.subject.id;

					if (this.status === 200 && this.readyState === 4) { // success request
						let levelsData = JSON.parse(this.responseText);
						levelsData.forEach(el => {
							let optionEl = document.createElement('option'),
								optionTe = document.createTextNode(el.name_ar);

							optionEl.appendChild(optionTe);
							optionEl.value = el.id;
							levelSelect.appendChild(optionEl);
							levelSelect.removeAttribute('disabled');
						});
					} else { // failed request
						$.notify('لقد حدث خطأ غير متوقع', 'error');
					}
				}
				getLevelname.send();

			} else if (this.status === 404) { // 404 not found
				$.notify('لم يتم العثور على المعلم', 'error');
			} else {
				$.notify('لقد حدث خطأ غير متوقع', 'error')
			}
		}
		getLevels.send();

	}
}

addGroupBtn.onclick = function () {
	addGroupModal.show();
}

function removeTimeInGroup() {
	let removeBtns = addGroupForm.querySelectorAll('i.fa-times.position-absolute');

	for (let i = 0; i < removeBtns.length; i++) {
		removeBtns[i].onclick = function () {
			this.parentElement.parentElement.remove();
		}
	}
}

removeTimeInGroup();

let elementsCounter = 1;
addTimeInGroupBtn.onclick = function () {
	elementsCounter++;
	let weekDays = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
		timesContainer = addGroupForm.querySelector('#times-container'),
		parentEl = document.createElement('div'),
		rowEl = document.createElement('div'),
		col_6_1 = document.createElement('div'),
		col_6_2 = document.createElement('div'),
		dayLabelEl = document.createElement('label'),
		daySelectEl = document.createElement('select'),
		timeLabelEl = document.createElement('label'),
		timeInputEl = document.createElement('input'),
		closeBtnEl = document.createElement('i');

	parentEl.classList.add('mb-3');
	rowEl.classList.add('row', 'position-relative');
	col_6_1.classList.add('col-6');
	col_6_2.classList.add('col-6');
	dayLabelEl.setAttribute('for', 'day_' + elementsCounter);
	dayLabelEl.textContent = 'اليوم';
	daySelectEl.classList.add('form-control');
	daySelectEl.setAttribute('name', 'day_' + elementsCounter);
	daySelectEl.setAttribute('id', 'day_' + elementsCounter);
	for (let i = 0; i < weekDays.length; i++) {
		let optionEl = document.createElement('option');
		optionEl.setAttribute('value', weekDays[i]);
		optionEl.textContent = weekDays[i];

		daySelectEl.appendChild(optionEl);
	}
	timeLabelEl.setAttribute('for', 'time_' + elementsCounter);
	timeLabelEl.textContent = 'الوقت';
	timeInputEl.classList.add('form-control');
	timeInputEl.setAttribute('type', 'time');
	timeInputEl.setAttribute('name', 'time_' + elementsCounter);
	timeInputEl.setAttribute('id', 'time_' + elementsCounter);
	closeBtnEl.classList.add('fas', 'fa-times', 'position-absolute', 'cursor-pointer');
	closeBtnEl.style.bottom = '0px';
	closeBtnEl.style.right = '49%';

	col_6_1.appendChild(dayLabelEl);
	col_6_1.appendChild(daySelectEl);
	col_6_2.appendChild(timeLabelEl);
	col_6_2.appendChild(timeInputEl);
	rowEl.appendChild(col_6_1);
	rowEl.appendChild(closeBtnEl);
	rowEl.appendChild(col_6_2);
	parentEl.appendChild(rowEl);
	timesContainer.appendChild(parentEl);

	removeTimeInGroup();
}

function checkOnSubmitButton() {

	let mainBtn = form.querySelector('button[type="submit"]');
	if (allTimes.length > 0) {
		mainBtn.removeAttribute('disabled');
		mainBtn.classList.remove('cursor-ban');
	} else {
		mainBtn.setAttribute('disabled', '');
		mainBtn.classList.add('cursor-ban');
	}
}

function deleteGroup() {
	let deleteBtns = tableContainer.querySelectorAll('i.delete-group');

	for (let i = 0; i < deleteBtns.length; i++) {
		deleteBtns[i].onclick = function () {
			allTimes.splice(allTimes.indexOf(this.dataset.info), 1);

			this.parentElement.parentElement.remove();

			if (tableContainer.querySelector('tbody').children.length == 0) {
				tableContainer.classList.remove('d-table');
				tableContainer.classList.add('d-none');
			}

			checkOnSubmitButton();
		}
	}

}

function submitGroupAdding() {
	let groupName = addGroupForm.querySelector('input#group_name'),
		groupDays = addGroupForm.querySelectorAll('select'),
		groupTimes = addGroupForm.querySelectorAll('input[type="time"]'),
		daysValues = [],
		timesValues = [],
		errors = false;

	for (let i = 0; i < groupDays.length; i++) {
		if (groupDays[i].value === "") {
			groupDays[i].focus();
			$.notify('قم بإختيار اليوم', 'error');
			errors = true;
		} else {
			daysValues.push(groupDays[i].value);
		}

	}

	for (let i = 0; i < groupTimes.length; i++) {
		if (groupTimes[i].value === "") {
			groupTimes[i].focus();
			$.notify('قم بإختيار الوقت', 'error');
			errors = true;
		} else {
			timesValues.push(groupTimes[i].value);
		}
	}

	if (!errors) {
		let trEl = document.createElement('tr'),
			tdForName = document.createElement('td'),
			tdForDays = document.createElement('td'),
			tdForTimes = document.createElement('td'),
			tdForOptions = document.createElement('td'),
			iDeleteEl = document.createElement('i'),
			groupInfo = {
				groupName: groupName.value,
				days: daysValues,
				times: timesValues
			};

		tdForName.textContent = groupName.value.length == 0 ? 'لا يوجد' : groupName.value;
		tdForDays.textContent = daysValues.join('، ');
		tdForTimes.textContent = timesValues.join(', ');
		iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger', 'cursor-pointer', 'delete-group');
		iDeleteEl.setAttribute('data-info', JSON.stringify(groupInfo));
		tdForOptions.appendChild(iDeleteEl);
		trEl.appendChild(tdForName);
		trEl.appendChild(tdForDays);
		trEl.appendChild(tdForTimes);
		trEl.appendChild(tdForOptions);
		tableContainer.classList.remove('d-none');
		tableContainer.classList.add('d-table');
		tableContainer.querySelector('tbody').appendChild(trEl);

		allTimes.push(groupInfo);
		deleteGroup();
		checkOnSubmitButton();
		addGroupModal.hide();
	}

}

addGroupForm.onsubmit = function (e) {
	e.preventDefault();
	submitGroupAdding();
}

form.onsubmit = function (e) {
	this.querySelector('#times').value = JSON.stringify(allTimes);
}