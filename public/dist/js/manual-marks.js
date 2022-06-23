let globalFullMarkEl = document.getElementById('full-mark-number'),
	allFullMarksEls = document.getElementsByClassName('full-mark-input'),
	saveMarksBtn = document.getElementById('saveMarksBtn'),
	submitMarksForm = document.getElementById('submit-marks'),
	groupSelect	    	= document.getElementById('group'),
	urlData = new URLSearchParams(location.search);

customExamSelect.onchange = function () {
	groupSelect.setAttribute('disabled', '');
	groupSelect.innerHTML = '<option value="NULL" disabled selected>إختر المجموعة</option>';
	let getGroups = new XMLHttpRequest();
	getGroups.open('GET', '/admin/lessons-groups/get-group-by-exam/' + this.value);
	getGroups.onload = function () {
		if(this.readyState === 4 && this.status === 200){
			let responseData = JSON.parse(this.responseText);
			if(Object.keys(responseData).length > 0){
				responseData.forEach(el => {
					let optionEl = document.createElement('option');
					optionEl.textContent = el.group_name;
					optionEl.value = el.id;
					groupSelect.appendChild(optionEl);
				});
				groupSelect.removeAttribute('disabled');
			}else{
				groupSelect.setAttribute('disabled', '');
				$.notify('لم يتم العثور على مجموعات  بالبيانات الحالية', 'error');
			}
		}
	}
	getGroups.send();
}

if(urlData.has('exam') && urlData.has('group')){
	globalFullMarkEl.onkeyup = function (e) {
		let markValue = this.value;

		if(!isNaN(markValue)){
			for(let i = 0; i < allFullMarksEls.length; i++){
				allFullMarksEls[i].value = this.value;
			}
		}else{
			$.notify('يجب أن تتكون الدرجة من أرقام فقط');
		}
	}

	saveMarksBtn.onclick = function () {
		disableAllInputs(true);

		let studentsCards 	= document.getElementsByClassName('student-card'),
			marksContainer 	= [],
			errors 			= [];

		function pushToMarksContainer (id, mark, fullmark) {
			marksContainer.push({
				id: id,
				mark: mark,
				fullmark: fullmark
			});
		}

		function disableAllInputs (status) {
			let inputs1 = document.querySelectorAll('input.mark-input'),
				inputs2 = document.querySelectorAll('input.full-mark-input');
			if(status){
				for(let i = 0; i < inputs1.length; i++){
					inputs1[i].setAttribute('disabled', '');
					inputs2[i].setAttribute('disabled', '');
				}
			}else{
				for(let i = 0; i < inputs1.length; i++){
					inputs1[i].removeAttribute('disabled');
					inputs2[i].removeAttribute('disabled');
				}
			}
		}

		for(let i = 0; i < studentsCards.length; i++){
			let card = studentsCards[i],
				studentId = card.querySelector('input#id').value,
				mark = card.querySelector('input.mark-input').value,
				fullMark = card.querySelector('input.full-mark-input').value;

			if(
				!isNaN(studentId) &&
				!isNaN(mark) &&
				!isNaN(fullMark) &&
				studentId !== "" &&
				fullMark !== "" &&
				mark !== ""
				){
				if(mark <= fullMark){
					pushToMarksContainer(studentId, mark, fullMark);
				}else{
					errors.push('يوجد درجة أكبر من الدرجة النهائية');
				}
			}
		}

		if(errors.length == 0){
			if(marksContainer.length !== 0){
				submitMarksForm.querySelector('input[name="marksContainer"]').value = JSON.stringify(marksContainer);
				submitMarksForm.submit();
			}else{
				disableAllInputs(false);
			}
		}else{
			errors.forEach(el => {
				$.notify(el, 'error');
			})
			disableAllInputs(false);
		}
	}
}