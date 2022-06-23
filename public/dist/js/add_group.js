let addGroupModal = new bootstrap.Modal(document.getElementById('addGroupModal')),
	addGroupForm = document.getElementById('add-group-form'),
	addTimeInGroupBtn = addGroupForm.querySelector('button#add-time'),
	groupInfo = null;

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

addGroupForm.onsubmit = function (e) {
	submitGroupForm();
}
function submitGroupForm () {
	let groupTimesEl = addGroupForm.querySelector('input#group_times'),
		daysSelects = addGroupForm.querySelectorAll('select'),
		timesInputs = addGroupForm.querySelectorAll('input[type="time"]'),
		daysValues = [],
		timesValues = [],
		errors = false;

	for (let i = 0; i < daysSelects.length; i++) {
		if (daysSelects[i].value === "") {
			daysSelects[i].focus();
			$.notify('قم بإختيار اليوم', 'error');
			errors = true;
		}

	}

	for (let i = 0; i < timesInputs.length; i++) {
		if (timesInputs[i].value === "") {
			timesInputs[i].focus();
			$.notify('قم بإختيار الوقت', 'error');
			errors = true;
		}
	}

	if(!errors){
		for(let i = 0; i < daysSelects.length; i++){
			daysValues.push(daysSelects[i].value);
			daysSelects[i].setAttribute('disabled', '');
		}

		for(let i = 0; i < timesInputs.length; i++){
			timesValues.push(timesInputs[i].value);
			timesInputs[i].setAttribute('disabled', '');
		}

		groupInfo = {
			days: daysValues,
			times: timesValues
		};

		groupTimesEl.value = JSON.stringify(groupInfo);
		addGroupForm.submit();
	}
}