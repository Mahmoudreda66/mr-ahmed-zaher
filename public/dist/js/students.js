let payExpensesEl = document.getElementById('expensesModal'),
    payExpensesModal = new bootstrap.Modal(payExpensesEl),
    deleteModal = document.getElementById('deleteModal'),
    deleteBsModal = new bootstrap.Modal(deleteModal);

function payExpenses (studentData) {
    payExpensesEl.querySelector('form#expenses-form input#student').value = studentData.name;
    payExpensesEl.querySelector('form#expenses-form input#id').value = studentData.id;

    payExpensesModal.show();

    let expenses = payExpensesEl.querySelector('form#expenses-form input#expenses'),
        getExpenses = new XMLHttpRequest();

    expenses.setAttribute('readonly', '');

    getExpenses.open('GET', '/admin/settings/expenses/' + studentData.level_id);
    getExpenses.onload = function () {
        expenses.removeAttribute('readonly');
        if (this.readyState == 4 && this.status == 200) {
            expenses.value = this.responseText;
        } else if (this.status == 404) {
            alert('لقد حدثت مشكلة في جلب القيمة الإفتراضية لمصاريف الطالب.. راجع إعدادات المصروفات وأعد المحاولة');
        }
    }
    getExpenses.send();
}

function printBarcode (id) {
    window.open('/admin/students/print-card/' + id, 'طباعة الباركود', 'fullscreen=no,height=350,left=0,resizable=no,status=no,width=200,titlebar=yes,menubar=no');
}

function deleteStudent (studentData) {
    deleteModal.querySelector('form#delete-form input#id').value = studentData.id;
    deleteModal.querySelector('form#delete-form input#student').value = studentData.name;
    deleteBsModal.show();
}