let prepratoryTeachersSelectBoxes 		= teachersContainer.querySelectorAll('select[data-level="0"]'),
	secondaryTeachersSelectBoxes 		= teachersContainer.querySelectorAll('select[data-level="1"]'),
	globalTeachersSelectBoxes 			= teachersContainer.querySelectorAll('select[data-level="2"]'),
	scinceTeachersSelectBoxes 			= teachersContainer.querySelectorAll('select[data-division="0"]'),
	literaryTeachersSelectBoxes 		= teachersContainer.querySelectorAll('select[data-division="1"]'),
	divisionSelectBox 					= createStudentForm.querySelector('select#division'),
	subLanguageSelectBox 				= createStudentForm.querySelector('select#sub_language'),
	frenchSelectBox 					= createStudentForm.querySelector('select#french_id'),
	germanySelectBox 					= createStudentForm.querySelector('select#germany_id');

function teachersContainerStatus (status) {
    let display = status ? 'flex' : 'none';
    teachersContainer.style.display = display;
}

function prepratoryTeachersStatus (status) {
	for(let i = 0; i < prepratoryTeachersSelectBoxes.length; i++){
		if(status){
			prepratoryTeachersSelectBoxes[i].removeAttribute('disabled');
		}else{
			prepratoryTeachersSelectBoxes[i].setAttribute('disabled', '');
		}
	}
}

function secondaryTeachersStatus (status) {
	for(let i = 0; i < secondaryTeachersSelectBoxes.length; i++){
		if(status){
			secondaryTeachersSelectBoxes[i].removeAttribute('disabled');
		}else{
			secondaryTeachersSelectBoxes[i].setAttribute('disabled', '');
		}
	}
}

function showGlobalTeachersStatus () {
	for(let i = 0; i < globalTeachersSelectBoxes.length; i++){
		globalTeachersSelectBoxes[i].removeAttribute('disabled');
	}
}

function scinceTeachersStatus (status) {
	for(let i = 0; i < scinceTeachersSelectBoxes.length; i++){
		if(status){
			scinceTeachersSelectBoxes[i].removeAttribute('disabled');
		}else{
			scinceTeachersSelectBoxes[i].setAttribute('disabled', '');
		}
	}
}

function literaryTeachersStatus (status) {
	for(let i = 0; i < literaryTeachersSelectBoxes.length; i++){
		if(status){
			literaryTeachersSelectBoxes[i].removeAttribute('disabled');
		}else{
			literaryTeachersSelectBoxes[i].setAttribute('disabled', '');
		}
	}
}

function filterTeachersByDivision (status, division = null) {
	if(status){
		if(division == 0){
			scinceTeachersStatus(true);
			literaryTeachersStatus(false);
		}else if(division == 1){
			literaryTeachersStatus(true);
			scinceTeachersStatus(false);
		}else{
			alert('قم بالإختيار من بين الخيارات المسموح بها فقط');
		}
	}else{
		literaryTeachersStatus(false);
		scinceTeachersStatus(false);
	}
}

function frenchStatus (status) {
	if(status){
		frenchSelectBox.removeAttribute('disabled');
	}else{
		frenchSelectBox.setAttribute('disabled', '');
	}
}

function germanyStatus (status) {
	if(status){
		germanySelectBox.removeAttribute('disabled');
	}else{
		germanySelectBox.setAttribute('disabled', '');
	}
}


function filterTeachersBySubLanguage (status, subLaguage = null) {
	if(status){
		if(subLaguage == 0){
			frenchStatus(true);
			germanyStatus(false);
		}else if(subLaguage == 1){
			germanyStatus(true);
			frenchStatus(false);
		}else{
			alert('قم بالإختيار من بين الخيارات المسموح بها فقط');
		}
	}else{
		frenchStatus(false);
		germanyStatus(false);
	}
}

function filterTeachersByWorkingLevels (levelValue) {
	let sOptions = document.querySelectorAll('.t-opt-s');
    for(let i = 0; i < sOptions.length; i++){
        let levelsIds = sOptions[i].dataset.levels.split(',');
        if(!levelsIds.includes(levelValue)){
            sOptions[i].classList.add('d-none');
            sOptions[i].classList.remove('d-block');
        }else{
            sOptions[i].classList.add('d-block');
            sOptions[i].classList.remove('d-none');
        }
    }
}

function disableSubjectByName (name, status) {
	let selectElement = document.querySelector('select[name="' + name + '"]');

	if(status){
		selectElement.setAttribute('disabled', '');
	}else{
		selectElement.removeAttribute('disabled');
	}
}

levelSelectBox.addEventListener('change', function () {
	let levelValue = this.value;

	filterTeachersByWorkingLevels(levelValue);

	if(levelValue == 1 || levelValue == 2 || levelValue == 3){ // prepratory
		teachersContainerStatus(true);
		showGlobalTeachersStatus();
		prepratoryTeachersStatus(true);
		secondaryTeachersStatus(false);
		filterTeachersByDivision(false);
		filterTeachersBySubLanguage(false);
	}else if(levelValue == 4 || levelValue == 5 || levelValue == 6){ // secondary
		teachersContainerStatus(true);
		showGlobalTeachersStatus();
		secondaryTeachersStatus(true);
		prepratoryTeachersStatus(false);

		subLanguageSelectBox.onchange = function () {
			if(subLanguageSelectBox.value != ""){
                filterTeachersBySubLanguage(true, this.value)
            }else{
                filterTeachersBySubLanguage(false)
            }
		}

		if(levelValue == 5 || levelValue == 6){
			divisionSelectBox.onchange = function () {
				filterTeachersByDivision(true, this.value);
			}
		}

		if(levelValue != 6){
			disableSubjectByName('geology', true);
			disableSubjectByName('psychology', true);
		}else{
			disableSubjectByName('geology', false);
			disableSubjectByName('psychology', false);
		}
	}
});
