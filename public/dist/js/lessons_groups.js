let deleteGroupsBtns = document.querySelectorAll('.delete-group'),
	deleteGroupModal = new bootstrap.Modal(document.getElementById('deleteGroupModal'));

for(let i = 0; i < deleteGroupsBtns.length; i++){
	deleteGroupsBtns[i].onclick = function () {
		deleteGroupForm.action = '/admin/lessons-groups/' + this.dataset.id;
		deleteGroupModal.show();
	}
}