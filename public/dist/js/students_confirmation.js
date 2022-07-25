function confirmStudent (id) {
	let confirmationForm = document.getElementById('confirmationForm');

	confirmationForm.action = '/admin/students/update-confirm-application/' + id;

	confirmationForm.submit();
}

function deleteStudent (id) {
	let deleteForm = document.getElementById('deleteForm'),
		idInput = deleteForm.querySelector('input[name="id"]');

	idInput.value = id;

	deleteForm.submit();
}