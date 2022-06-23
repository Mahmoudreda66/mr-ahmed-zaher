let tokenForm = document.getElementById('student-token-form'),
	userCanPay = document.currentScript.dataset.userCanPay,
	modalEl = document.getElementById('expensesModal');

function deleteAlert () {
	let el = document.getElementById('emptyAlert');
	if(el) el.parentElement.parentElement.remove();
}

let toggleButtons = document.querySelectorAll('.toggle-buttons');
for(let i = 0; i < toggleButtons.length; i++){
	toggleButtons[i].onclick = function () {
		toggleRecord(this.dataset.id);
	}
}

function toggleRecord (id) {
	let toggleRecord = new XMLHttpRequest();

	toggleRecord.open('PUT', '/admin/absences/toggle/' + id);
	toggleRecord.onload = function () {
		if(this.status === 200 && this.readyState === 4){ // success
			if(this.responseText){
				$.notify('تم تغيير حالة الحضور بنجاح', 'success');

				let element = document.querySelector('i.toggle-buttons[data-id="' + id + '"]')
				.parentElement
				.parentElement
				.querySelector('td:nth-of-type(4)'),
					elementText = element.textContent.trim();
					console.log(elementText)

				if(elementText == 'غائب'){
					element.textContent = 'حاضر';
				}else if(elementText == 'حاضر'){
					element.textContent = 'غائب';
				}
			}
		}else if(this.status === 404){
			$.notify('لم يتم العثور على العنصر', 'warn');
		}else{
			$.notify('لقد حدث خطأ غير متوقع', 'warn');
		}
	}
	toggleRecord.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	toggleRecord.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + id);
}

if(userCanPay == 1){
	let payModal = new bootstrap.Modal(modalEl);
	
	function requestPay (element) {
		let studentData = JSON.parse(element.dataset.student),
			expensesInput = modalEl.querySelector('form#expenses-form input#expenses'),
			getExpenses = new XMLHttpRequest();

		payModal.show();

		modalEl.querySelector('form#expenses-form input#id').value = studentData.id;
		modalEl.querySelector('form#expenses-form input#student').value = studentData.name;
		expensesInput.setAttribute('disabled', '');
		expensesInput.value = '';

		getExpenses.open('GET', '/admin/settings/expenses/' + studentData.level_id);
	    getExpenses.onload = function () {
	        expenses.removeAttribute('readonly');
	        if (this.readyState == 4 && this.status == 200) {
	            expensesInput.value = this.responseText;
	        } else if (this.status == 404) {
	            alert('لقد حدثت مشكلة في جلب القيمة الإفتراضية لمصاريف الطالب.. راجع إعدادات المصروفات وأعد المحاولة');
	        }

	        expensesInput.removeAttribute('disabled');
	    }
	    getExpenses.send();
	}

	function sendPay () {
		let studentId = modalEl.querySelector('form#expenses-form input#id').value,
			expenses = modalEl.querySelector('form#expenses-form input#expenses').value,
			month = modalEl.querySelector('form#expenses-form input#month').value;

		let sendExpenses = new XMLHttpRequest();

		sendExpenses.open('POST', '/admin/expenses');
		sendExpenses.onload = function () {
			if(this.readyState === 4 && this.status === 200){
				let responseData = JSON.parse(this.responseText),
					dateObject = new Date();
				if(responseData.status){
					payModal.hide();
					$.notify('تم دفع المصروفات بنجاح', 'success');

					if(month == dateObject.getMonth() + 1){
						let element = document.querySelector('span[data-id="' + studentId + '"]');

						if(element){
							element.textContent = 'تم الدفع';
							element.classList.remove('bg-warning');
							element.classList.add('bg-success', 'text-white');
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
		sendExpenses.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + studentId + '&expenses=' + expenses + '&month=' + month);
	}

	let payButtons = document.querySelectorAll('table tbody tr td span.badge');
	for(let i = 0; i < payButtons.length; i++){
		payButtons[i].ondblclick = function () {
			requestPay(this);
		}
	}
}

tokenForm.onsubmit = function (e) {
	e.preventDefault();
	let tokenInput = tokenForm.querySelector('input#token');

	if(tokenInput.value && tokenInput.value !== ''){
		let sendToken = new XMLHttpRequest();
		sendToken.open('POST', '/admin/day-absence-mode');
		sendToken.onload = function () {
			if(this.status === 200 && this.readyState === 4){ // success request
				let responseData = JSON.parse(this.responseText);

				if(responseData.status){
					let tableParentEl = document.querySelector('table#parent-table tbody'),
						trParentEl = document.createElement('tr'),
						tdForId = document.createElement('td'),
						tdForStudent = document.createElement('td'),
						tdForLevel = document.createElement('td'),
						tdForStatus = document.createElement('td'),
						tdForExpenses = document.createElement('td'),
						spanForExpenses = document.createElement('span'),
						tdForDate = document.createElement('td'),
						tdForOptions = document.createElement('td'),
						iForToggle = document.createElement('i');

					tdForId.textContent = responseData.data.item.id;
					tdForStudent.textContent = responseData.data.student.name;
					tdForLevel.textContent = responseData.data.student.level.name_ar;
					tdForStatus.textContent = 'حاضر';
					tdForDate.textContent = responseData.data.item.join_at;
					iForToggle.classList.add('fas', 'fa-history', 'text-info', 'toggle-buttons', 'cursor-pointer');
					iForToggle.setAttribute('data-id', responseData.data.item.id);

					let expensesSpanText = '';

					if(responseData.data.hasPaid){
						expensesSpanText = 'تم الدفع';

						if(userCanPay == 1){
							spanForExpenses.classList.add('badge', 'user-select-none', 'cursor-pointer', 'bg-success', 'text-white');
						}else{
							spanForExpenses.classList.add('badge', 'bg-success', 'text-white');
						}
					}else{
						expensesSpanText = 'لم يتم الدفع';

						if(userCanPay == 1){
							spanForExpenses.classList.add('cursor-pointer', 'badge', 'user-select-none', 'bg-warning');
						}else{
							spanForExpenses.classList.add('badge', 'bg-warning');
						}
					}

					spanForExpenses.textContent = expensesSpanText;

					let studentData = responseData.data.student;

					if(userCanPay == 1){
						let studentDataObject = {
							'name': studentData.name,
							'id': studentData.id,
							'level_id': studentData.level.name_en
						};

						studentDataObject = JSON.stringify(studentDataObject);

						spanForExpenses.setAttribute('data-student', studentDataObject);
						spanForExpenses.setAttribute('data-id', studentData.id);
						
						spanForExpenses.ondblclick = function () {
							requestPay(this);
						}

						iForToggle.onclick = function () {
							toggleRecord(this.dataset.id);
						}
					}

					tdForExpenses.appendChild(spanForExpenses);
					tdForOptions.appendChild(iForToggle);

					trParentEl.appendChild(tdForId);
					trParentEl.appendChild(tdForStudent);
					trParentEl.appendChild(tdForLevel);
					trParentEl.appendChild(tdForStatus);
					trParentEl.appendChild(tdForExpenses);
					trParentEl.appendChild(tdForDate);
					trParentEl.appendChild(tdForOptions);

					tableParentEl.prepend(trParentEl);

					$.notify('تم تسجيل حضور الطالب ' + responseData.data.student.name, 'success');
					let successAudio = new Audio('/dist/sound/success.aac');
					successAudio.play();

					deleteAlert();
				}else{
					if(responseData.message === 'validation'){
						responseData.data.forEach(el => {
							$.notify(el, 'error');
						});
						let errAudio = new Audio('/dist/sound/error.aac');
						errAudio.play();
					}
				}

				let absenceCountAlert = document.getElementById('absence_count_alert').value;
				if(responseData.data.absenceDays > absenceCountAlert){
					alert('لقد غاب الطالب ' + responseData.data.absenceDays + ' يوم هذا الشهر');
				}

			}else{
				$.notify('لقد حدث خطأ غير متوقع');
			}
		}

		tokenInput.select();
		tokenInput.focus();
		sendToken.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		sendToken.send('_token=' + document.querySelector('meta[name="_token"]').content + '&token=' + encodeURIComponent(tokenInput.value) + '&date=' + document.getElementById('day_date').value);
	}else{
		$.notify('قم بمسح رمز الإستجابة السريع', 'error');
	}
}

