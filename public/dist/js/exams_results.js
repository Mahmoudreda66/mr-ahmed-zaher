let editForm 		= document.getElementById('edit-mark-form'),
	deleteForm 		= document.getElementById('delete-mark-form'),
	editBtns 		= document.querySelectorAll('i.edit-result'),
	deleteBtns 		= document.querySelectorAll('i.delete-result'),
	fullMarkInput 	= editForm.querySelector('input#full_mark'),
	editMarkInput 	= editForm.querySelector('input#correct_answers');

for(let i = 0; i < editBtns.length; i++){
	editBtns[i].onclick = function () {
		fullMarkInput.value = this.dataset.fullmark;
		editMarkInput.value = this.dataset.mark;
		editForm.action = '/admin/exams-marks/' + this.dataset.id;
		new bootstrap.Modal(document.getElementById('editMarkModal')).show();
	}
}

for(let i = 0; i < deleteBtns.length; i++){
	deleteBtns[i].onclick = function () {
		deleteForm.querySelector('input#mark').value = `الطالب  ${this.dataset.student} - ${this.dataset.mark}`;
		deleteForm.action = '/admin/exams-marks/' + this.dataset.id;
		new bootstrap.Modal(document.getElementById('deleteMarkModal')).show();
	}
}