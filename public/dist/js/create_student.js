let createStudentForm = document.getElementById('create-form'),
    levelSelectBox = createStudentForm.querySelector('select#level'),
    teachersContainer = createStudentForm.querySelector('#teachers-container'),
    subLanguageContainer = createStudentForm.querySelector('#sub_language-container'),
    divisionContainer = createStudentForm.querySelector('#division-container');

function resetInputs() {
    for (let i = 0; i < teachersSelectBox.length; i++) {
        teachersSelectBox[i].removeAttribute('disabled');
    }
}

function subLanguageSelectBoxStatus (status) {
    let display = status ? 'block' : 'none';
    subLanguageContainer.style.display = display;
}

function divisionSelectBoxStatus (status) {
    let display = status ? 'block' : 'none';
    divisionContainer.style.display = display;
}

levelSelectBox.onchange = function () {
    let levelValue = this.value;

    if(levelValue == 1 || levelValue == 2 || levelValue == 3){ // prepratory
        divisionSelectBoxStatus(false);
        subLanguageSelectBoxStatus(false);
    }else if(levelValue == 4 || levelValue == 5 || levelValue == 6){ // secondary
        subLanguageSelectBoxStatus(true);

        if(levelValue == 5 || levelValue == 6){
            divisionSelectBoxStatus(true);
        }else{
            divisionSelectBoxStatus(false);
        }
    }else{
        alert('قم بإختيار المرحلة بشكل صحيح');
    }
}