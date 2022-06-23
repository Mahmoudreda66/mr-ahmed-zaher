let studntsLoader = document.getElementById('students-loader'),
	studentsContainer = document.getElementById('students-container'),
	teacherId = document.querySelector('input[type=hidden]#id').value;

window.onload = function () {
	function loaderStatus (status) {
		if(status){
			studntsLoader.classList.remove('d-none');
			studntsLoader.classList.add('d-flex');
		}else{
			studntsLoader.classList.remove('d-flex');
			studntsLoader.classList.add('d-none');
		}
	}

	function printAlert (type, message) {
		let alertParent = document.createElement('div'),
			alertText = document.createTextNode(message);

		alertParent.classList.add('alert', 'mb-0', 'alert-' + type, 'text-center');
		alertParent.appendChild(alertText);

		loaderStatus(false);

		studentsContainer.appendChild(alertParent);
	}

	let getStudents = new XMLHttpRequest();

	getStudents.open('GET', '/admin/students-teacher/' + teacherId);
	getStudents.onload = function () {
		if(this.status === 200 && this.readyState === 4){
			let responseData = JSON.parse(this.responseText);

			if(responseData.students_count != 0){
				responseData.data.forEach((el, i) => {
					let aEl = document.createElement('a'),
						counterEl = document.createElement('span'),
						nameEl = document.createElement('span'),
						parentEl = document.createElement('span');

					aEl.setAttribute('href', '/admin/students/' + el.student.id);
					aEl.classList.add('d-block', 'mb-1');
					counterEl.textContent = i + 1 + '- ';
					nameEl.textContent = el.student.name + ' - ' + el.student.level.name_ar;
					parentEl.appendChild(counterEl);
					parentEl.appendChild(nameEl);
					aEl.appendChild(parentEl);
					studentsContainer.appendChild(aEl);
				});

				// prepare counts
				let parentTable = document.createElement('table'),
					countsEl = document.getElementById('counts');

				parentTable.classList.add('table');

				function translateLevel (level) {
					if(level == 1){
						return 'الصف الأول الإعدادي';
					}else if(level == 2){
						return 'الصف الثاني الإعدادي';
					}else if(level == 3){
						return 'الصف الثالث الإعدادي';
					}else if(level == 4){
						return 'الصف الأول الثانوي';
					}else if(level == 5){
						return 'الصف الثاني الثانوي';
					}else if(level == 6){
						return 'الصف الثالث الثانوي';
					}
				}

				for(let el in responseData.counts) {
					let trEl = document.createElement('tr'),
						tdForName = document.createElement('td'),
						tdForCount = document.createElement('td');

					tdForName.textContent = translateLevel(el);
					tdForCount.textContent = responseData.counts[el] + ' طالب';
					trEl.appendChild(tdForName);
					trEl.appendChild(tdForCount);

					parentTable.appendChild(trEl);
				};

				countsEl.appendChild(parentTable);

				document.getElementById('students-counter').textContent = '( ' + responseData.students_count + ' )';
				loaderStatus(false);
			}else{
				printAlert('info', 'لا يوجد طلاب حتى الآن');
			}


		}else if(this.status === 404){
			printAlert('danger', 'لم يتم العثور على المعلم');
		}
	}
	getStudents.send();
}