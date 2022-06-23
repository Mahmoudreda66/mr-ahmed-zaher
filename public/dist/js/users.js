let editBtns = document.querySelectorAll('i.edit-user'),
	editUserModal = new bootstrap.Modal(document.getElementById('editUserModal')),
	editUserForm = document.getElementById('edit-user-form'),
	deleteBtns = document.querySelectorAll('i.delete-user'),
	deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal')),
	deleteUserForm = document.getElementById('delete-user-form'),
	toggleUserbtns = document.querySelectorAll('i.toggle-user-activity'),
	toggleUserForm = document.getElementById('toggle-form');

function setRolesOnEdit (roles) {
	// check user roles in edit form
	let userRoles = JSON.parse(roles),
		roleItems = editUserForm.querySelectorAll('label.role-item'),
		rolesContainer = [];

	for(let i = 0; i < roleItems.length; i++){
		let roleItem = roleItems[i].querySelector('input[type="checkbox"]');

		rolesContainer.push(parseInt(roleItem.value));
		roleItem.checked = false;
	}

	for(let i = 0; i < userRoles.length; i++){
		if(rolesContainer.includes(userRoles[i])){
			editUserForm.querySelector('input[type="checkbox"][value="' + userRoles[i] + '"]').checked = true;
		}
	}
}

for(let i = 0; i < editBtns.length; i++){
	editBtns[i].onclick = function () {
		editUserForm.action = '/admin/users/' + this.dataset.id;
		editUserForm.querySelector('input#user_name').value = this.dataset.name;
		editUserForm.querySelector('input#phone').value = this.dataset.phone;

		setRolesOnEdit(this.dataset.roles);

		editUserModal.show();
	}
}

for(let i = 0; i < deleteBtns.length; i++){
	deleteBtns[i].onclick = function () {
		deleteUserForm.querySelector('input#user_name').value = this.dataset.name;
        deleteUserForm.action = '/admin/users/' + this.dataset.id;

		deleteUserModal.show();
	}
}

for(let i = 0; i < toggleUserbtns.length; i++){
	toggleUserbtns[i].onclick = function () {
		toggleUserForm.action = '/admin/users/' + this.dataset.id + '/toggle-activity';

		toggleUserForm.submit();
	}
}