let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal')),
    editModal = new bootstrap.Modal(document.getElementById('editModal')),
    deleteForm = document.getElementById('delete-form'),
    editForm = document.getElementById('edit-form'),
    addModal = new bootstrap.Modal(document.getElementById('addModal')),
    reportsForm = new bootstrap.Modal(document.getElementById('reportsModal'));

function deleteItem (item) {
    deleteForm.action = '/admin/out-money/' + item.id;
    deleteForm.querySelector('input#item').value = item.name;
    deleteModal.show();
}

function editItem (item) {
    editForm.querySelector('input#money').value = item.money;
    editForm.querySelector('input#at').value = item.at;
    editForm.querySelector('textarea#reason').value = item.reason;
    editForm.action = '/admin/out-money/' + item.id;

    editModal.show();
}