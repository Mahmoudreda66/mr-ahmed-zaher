let mainForm 				= document.getElementById('students-search-form'),
	addStudentsModal		= new bootstrap.Modal(document.getElementById('studentGroupModal'));
	editGroupModal			= new bootstrap.Modal(document.getElementById('editGroupModal'));

function containerStatus (status) {
	let display = status ? 'block' : 'none';
	ulContainerEl.style.display = display;
	if(status){ulContainerEl.innerHTML = '';}
}