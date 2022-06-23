let printInvoiceBtn = document.querySelectorAll('.print-expenses-invoice-btn'),
	printBarcodeBtn = document.getElementById('print-barcode'),
	payExpensesModal = new bootstrap.Modal(document.getElementById('expensesModal')),
	urlData = new URLSearchParams(location.search);

for(let i = 0; i < printInvoiceBtn.length; i++){
	printInvoiceBtn[i].onclick = function () {
		window.open('/admin/expenses/print-invoice/' + this.dataset.id, 'طباعة بيانات الطالب', 'fullscreen=no,height=450,left=0,resizable=no,status=no,width=400,titlebar=yes,menubar=no');
	}
}

printBarcodeBtn.onclick = function () {
	window.open('/admin/students/print-card/' + this.dataset.id, 'طباعة الباركود', 'fullscreen=no,height=350,left=0,resizable=no,status=no,width=200,titlebar=yes,menubar=no');
}

if(urlData.has('pay')){
	payExpensesModal.show();
}