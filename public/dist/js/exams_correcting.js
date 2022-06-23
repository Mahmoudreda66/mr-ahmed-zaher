let modalQuestionId 				= document.querySelector('input#question_id'),
	noticeBtns 						= document.querySelectorAll('.add-notice-btn'),
	questionAnswerStatusBtns 		= document.querySelectorAll('.sho-bb-0 .question-answer-status'),
	studentsSelect					= document.querySelector('select#students'),
	finalMarkForm					= document.getElementById('final-mark-form'),
	markInputCount 					= finalMarkForm.querySelector('input#mark'),
	fullMarkInputCount 				= finalMarkForm.querySelector('input#full_mark'),
	studentId 						= document.getElementById('students').value,
	examId  						= document.getElementById('exam-info').dataset.id,
	finalResult						= [];

function sendQuestion (questionId, questionValue, studentId, comment = null) {
	let sendAnswer = new XMLHttpRequest();

	sendAnswer.open('POST', '/admin/exams-correcting');
	sendAnswer.onload = function () {
		if (this.status === 200 && this.readyState === 4) {
			let responseData = JSON.parse(this.responseText);

			if (!responseData.status){
				if (responseData.message === 'validation') {
					responseData.data.forEach(el => {
						$.notify(el, 'error');
					});
				} else {
					$.notify('لقد حدث خطأ غير متوقع', 'error');
				}
			}
		} else {
			$.notify('لقد حدث خطأ غير متوقع', 'error');
		}
	}

	let queryString = '_token=' + document.querySelector('meta[name="_token"]').content + '&question_id=' + questionId + '&question_answer=' + questionValue + '&student=' + studentId;

	if(comment){
		queryString += '&comment=' + comment;
	}

	sendAnswer.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	sendAnswer.send(queryString);
}

function setMark (value, element) {
	if(value == 1){ // correct answer
		if(!finalResult.includes(element.parentElement.parentElement)){
			finalResult.push(element.parentElement.parentElement);
		}
	}else if(value == 0){
		if(finalResult.includes(element.parentElement.parentElement)){
			finalResult.remove(element.parentElement.parentElement);
		}
	}

	markInputCount.value = finalResult.length;
}

Array.prototype.remove = function(val) {
  for (let i = 0; i < this.length; i++) {
    if (this[i] === val) {
      this.splice(i, 1);
      i--;
    }
  }
  return true;
}

for (let i = 0; i < questionAnswerStatusBtns.length; i++) {
	questionAnswerStatusBtns[i].onclick = function () {
		let statusBtn 		= this,
			btnSiblings 	= statusBtn.parentElement.children,
			questionId 		= statusBtn.parentElement.dataset.id,
			questionValue 	= statusBtn.dataset.value,
			studentId 		= document.getElementById('students').value;

		for(let i = 0; i < btnSiblings.length; i++){
			if(btnSiblings[i] == statusBtn){
				btnSiblings[i].classList.add('active');
			}else{
				btnSiblings[i].classList.remove('active');
			}
		}

		setMark(questionValue, statusBtn);

		statusBtn.parentElement.parentElement.classList.remove('correct', 'wrong');
		statusBtn.parentElement.parentElement.classList.add('corrected-question', questionValue == 1 ? 'correct' : 'wrong');

		sendQuestion(questionId, questionValue, studentId);
	}
}

studentsSelect.onchange = function () {
	let urlData = new URLSearchParams(location.search);
	location.href = '/admin/exams-correcting?level=' + urlData.get('level') + '&exam=' + urlData.get('exam') + '&student=' + this.value;
}

window.onload = function () {
	fullMarkInputCount.value = document.querySelectorAll('.sho-bb-0').length;
	markInputCount.value = document.querySelectorAll('.corrected-question.correct').length;
}

function loaderStatus (status) {
	let display = status ? 'flex' : 'none';
	document.getElementById('allpage-loader').style.display = display;
}

function sendMark () {
	let sendMark = new XMLHttpRequest();

	sendMark.open('POST', '/admin/exams/exams-marks/single');
	sendMark.onload = function () {
		console.log(this.responseText);
		if(this.status === 200 && this.readyState === 4){
			let responseData = JSON.parse(this.responseText);
			if(responseData.status){
				$.notify('تم إرسال الدرجة بنجاح', 'success');
				window.scrollTo(0, 0);
			}else{
				responseData.data.forEach(el => {
					$.notify(el, 'error');
				});
			}
		}else{
			$.notify('لقد حدث خطأ غير متوقع', 'error');
		}
	}
	sendMark.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	sendMark.send('_token=' + document.querySelector('meta[name="_token"]').content + '&student=' + studentId + '&exam=' + examId + '&full_mark=' + fullMarkInputCount.value + '&mark=' + markInputCount.value + '&enter_type=0');
}

finalMarkForm.onsubmit = function (e) {
	e.preventDefault();

	let uncorrectedQuestions = 0,
		questions = document.querySelectorAll('.sho-bb-0');
	for(let i = 0; i < questions.length; i++){
		if(!questions[i].classList.contains('corrected-question')){
			uncorrectedQuestions++;
		}
	}

	if(uncorrectedQuestions > 0){
		if(confirm('يوجد أسئلة لم يتم تصحيحها بعد. هل ترغب بإرسال الدرجة؟ ')){
			sendMark();
		}
	}else{
		sendMark();
	}
}