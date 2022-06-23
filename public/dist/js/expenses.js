let filterModal = new bootstrap.Modal(document.getElementById('filterModal')),
	urlData = new URLSearchParams(location.search),
	actionsForm = document.querySelector('form#records-actions'),
	methodInput = actionsForm.querySelector('input[name="_method"]'),
	showMonthBtn = document.getElementById('show-month');

showMonthBtn.onclick = function () {
	filterModal.show();
}

function deleteRecord (item) {
	methodInput.value = 'DELETE';
	actionsForm.action = '/admin/expenses/' + item;
	actionsForm.submit();
}

function forceDeleteRecord (item) {
	methodInput.value = 'DELETE';
	actionsForm.action = '/admin/expenses/force-delete/' + item;
	actionsForm.submit();
}

function restoreRecord (item) {
	methodInput.value = 'PUT';
	actionsForm.action = '/admin/expenses/restore/' + item;
	actionsForm.submit();
}

function printInvoice (item) {
	window.open('/admin/expenses/print-invoice/' + item, 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no');
}