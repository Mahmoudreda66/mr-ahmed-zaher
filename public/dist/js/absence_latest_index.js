let modal = new bootstrap.Modal(document.getElementById('deleteModal')); // get submit delete modal 

function deleteItem (item) {
	modal.show(); // shows the submit modal

	// preparing modal data
	let form = document.getElementById('d-record-form');
	form.querySelector('input#id').value = item.id;
	form.querySelector('input#lesson').value = item.lesson;
}

function toggleItem (item) {
	// prepare xhr
	let toggleRecord = new XMLHttpRequest(),
		id = item;

	toggleRecord.open('PUT', '/admin/absences/toggle/' + id);
	toggleRecord.onload = function () {
		if(this.status === 200 && this.readyState === 4){ // success
			if(this.responseText){
				$.notify('تم تغيير حالة الحضور بنجاح', 'success');
			}
		}else if(this.status === 404){
			$.notify('لم يتم العثور على العنصر', 'warn');
		}else{
			$.notify('لقد حدث خطأ غير متوقع', 'warn');
		}
	}
	toggleRecord.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	toggleRecord.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + id);
}
