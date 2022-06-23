let mainForm 				= document.getElementById('students-search-form'),
	mainInput 				= mainForm.querySelector('input#student'),
	errorEl 				= mainForm.querySelector('small.error.main-input'),
	idInput 				= mainForm.querySelector('input#id'),
	ulContainerEl 			= mainForm.querySelector('ul.search-hints'),
	markInputEl 			= mainForm.querySelector('input#mark'),
	fullMarkInputEl 		= mainForm.querySelector('input#full_mark');

function containerStatus (status) {
	let display = status ? 'block' : 'none';
	ulContainerEl.style.display = display;
	if(status){ulContainerEl.innerHTML = '';}
}

function restartMainInput () {
	idInput.value = '';
	errorEl.textContent = '';
	markInputEl.setAttribute('readonly', '');
	fullMarkInputEl.setAttribute('readonly', '');
}

mainInput.oninput = function () {
	let textValue = this.value,
		getDataObject = new XMLHttpRequest();

	restartMainInput();

	if(textValue != ""){
		getDataObject.open('GET', '/admin/students/search-a?value=' + textValue + '&levelId=' + document.getElementById('levelId').dataset.id);
		getDataObject.onload = function () {
			if(this.readyState === 4 && this.status === 200){ // success request
				let responseData = JSON.parse(this.responseText);

				if(responseData.status){
					if(Object.keys(responseData.data).length > 0){
						// set data to dom
						containerStatus(true);
						responseData.data.forEach(el => {
							let parentEl = document.createElement('li'),
								textNode = document.createTextNode(el.name);
							parentEl.appendChild(textNode);
							parentEl.setAttribute('data-id', el.id);
							ulContainerEl.appendChild(parentEl);
						});

						// preparing data events
						let hintsElements = ulContainerEl.querySelectorAll('li');
						for(let i = 0; i < hintsElements.length; i++){
							hintsElements[i].onclick = function () {
								mainInput.value = this.innerHTML;
								idInput.value = this.dataset.id;
								containerStatus(false);
								markInputEl.removeAttribute('readonly');
								fullMarkInputEl.removeAttribute('readonly');
							}
						}
					}else{
						if(textValue !== ""){
							errorEl.textContent = 'لم يتم العثور على الطالب';
						}
						containerStatus(false);
					}
				}else{
					if(responseData.message === 'validation'){
						responseData.data.forEach(el => {
							$.notify(el, 'error');
						});
					}
				}
			}else{
				$.notify('لقد حدث خطأ غير متوقع', 'error');
			}
		}
		getDataObject.send();
	}else{
		containerStatus(false);
		restartMainInput();
	}
}