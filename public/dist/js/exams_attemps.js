let deleteBtns 				= document.querySelectorAll('i.delete-attemp'),
	loader 					= document.getElementById('allpage-loader'),
	customResultModal 		= document.getElementById('customResultModal');

for(let i = 0; i < deleteBtns.length; i++){
	deleteBtns[i].onclick = function () {
		if(confirm('سيتم السماح للطالب بدخول الإختبار مرة أخرى. لن يتم قبول الإجابات الحالية منه.')){
			loader.style.display = 'flex';
			let deleteAttemp = new XMLHttpRequest();
			deleteAttemp.open('DELETE', '/admin/exams-attemps/' + this.dataset.id);
			deleteAttemp.onload = function () {
				if(this.status === 200 && this.readyState === 4){
					if(JSON.parse(this.responseText).status == true){
						deleteBtns[i].parentElement.parentElement.remove();
						$.notify('تم حذف العملية بنجاح', 'success');
					}
				}else if(this.status === 404){
					$.notify('ربما تم حذف هذه العملية من قبل', 'error');
				}
				loader.style.display = 'none';
			}
			deleteAttemp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
			deleteAttemp.send('_token=' + document.querySelector('meta[name="_token"]').content);
		}
	}
}