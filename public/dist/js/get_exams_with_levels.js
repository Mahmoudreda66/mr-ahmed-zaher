let customLevelSelect 		= customResultModal.querySelector('select#level'),
	customExamSelect 		= customResultModal.querySelector('select#exam'),
	customSearchForm		= document.querySelector('form#custom-result-form'),
	getSource				= document.currentScript.dataset.get;

function getExams (el) {
	let levelId = el.value,
		getExams = new XMLHttpRequest();

	customExamSelect.setAttribute('disabled', '');

	getExams.open('GET', (getSource ? getSource : '/admin/exams/get-manual-exams-by-level/') + levelId);
	getExams.onload = function () {
		if(this.readyState === 4 && this.status === 200){ // success request
			let examsData = JSON.parse(this.responseText);
			customExamSelect.setAttribute('disabled', '');
			customExamSelect.innerHTML = '<option value="NULL" disabled selected>إختر الإختبار</option>';

			if(Object.keys(examsData).length == 0){
				$.notify('لم يتم العثور على إختبارات للصف الحالي', 'error');
			}else{
				let urlData = new URLSearchParams(location.search),
					hasExam = false;

				if(urlData.has('exam')){
					hasExam = true;
				}

				examsData.forEach(el => {
					let optionEl = document.createElement('option'),
						textNode = document.createTextNode(`إختبار ال${el.exam.subject.name_ar} - ${el.exam.level.name_ar} - أ/ ${ el.teacher.name } - ${el.exam.date}`);

					optionEl.appendChild(textNode);
					optionEl.value = el.exam.id;
					
					if(hasExam){
						if(el.exam.id == urlData.get('exam')){
							optionEl.setAttribute('selected', '')
						}
					}

					customExamSelect.appendChild(optionEl);
				});
				customExamSelect.removeAttribute('disabled');
			}
		}else{ // failed request
			$.notify('لقد حدث خطأ غير متوقع', 'error');
		}
	}
	getExams.send();
}

customLevelSelect.onchange = function () {
	getExams(this);
}

window.onload = function () {
	if(customLevelSelect.value != 'NULL' || !customLevelSelect.value){
		getExams(customLevelSelect);
	}
}

function validateSearchForm () {
	if(customLevelSelect.value == 'NULL' || !customLevelSelect.value){
		customSearchForm.querySelector('small.text-danger.level').textContent = 'قم بإختيار المرحلة';
	}else if(customExamSelect.value == 'NULL' || !customExamSelect){
		customSearchForm.querySelector('small.text-danger.level').textContent = '';
		customSearchForm.querySelector('small.text-danger.exam').textContent = 'قم بإختيار الإختبار';
	}else{
		customSearchForm.querySelector('small.text-danger.exam').textContent = '';
		customSearchForm.submit();
	}
}

customSearchForm.onsubmit = function (e) {
	e.preventDefault();
	validateSearchForm();
}