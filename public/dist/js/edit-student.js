let changeSecondaryDataBtn = document.getElementById('edit_secondary_data');

function toggleLoader(status) {
    let loader = document.getElementById('secondary-data-container');
    if (status) {
        loader.style.display = 'block';
    } else {
        loader.style.display = 'none';
    }
}

changeSecondaryDataBtn.onchange = function () {
    if (this.checked) {
        toggleLoader(true);

        let levelValue = levelSelectBox.value;

        if(typeof filterTeachersByWorkingLevels !== "undefined"){
            filterTeachersByWorkingLevels(levelValue);
        }

        if (levelValue == 1 || levelValue == 2 || levelValue == 3) { // prepratory
            if(typeof teachersContainerStatus !== "undefined"){
                teachersContainerStatus(true);
                showGlobalTeachersStatus();
                prepratoryTeachersStatus(true);
                secondaryTeachersStatus(false);
                filterTeachersByDivision(false);
                filterTeachersBySubLanguage(false);
                divisionSelectBoxStatus(false);
            }
            subLanguageSelectBoxStatus(false);
        } else if (levelValue == 4 || levelValue == 5 || levelValue == 6) { // secondary
            if(typeof teachersContainerStatus !== "undefined"){
                teachersContainerStatus(true);
                showGlobalTeachersStatus();
                secondaryTeachersStatus(true);
                prepratoryTeachersStatus(false);
            }
            subLanguageSelectBoxStatus(true);

            if(typeof subLanguageSelectBox !== "undefined"){
                if(subLanguageSelectBox.value != ""){
                    filterTeachersBySubLanguage(true, subLanguageSelectBox.value);
                }else{
                    filterTeachersBySubLanguage(false);
                }
            }
        
            if(typeof subLanguageSelectBox !== "undefined"){
                subLanguageSelectBox.onchange = function () {
                    if(subLanguageSelectBox.value != ""){
                        filterTeachersBySubLanguage(true, this.value)
                    }else{
                        filterTeachersBySubLanguage(false)
                    }
                }
            }

            if (levelValue == 5 || levelValue == 6) {
                divisionSelectBoxStatus(true);

                if(typeof divisionSelectBox !== "undefined"){
                    if(divisionSelectBox.value != ""){
                        if(divisionSelectBox.value == 0){
                            scinceTeachersStatus(true);
                            literaryTeachersStatus(false);
                        }else if(divisionSelectBox.value == 1){
                            scinceTeachersStatus(false);
                            literaryTeachersStatus(true);
                        }
                    }

                    divisionSelectBox.onchange = function () {
                        filterTeachersByDivision(true, this.value);
                    }
                }
            }else{
                divisionSelectBoxStatus(false);
            }
        }
    } else {
        toggleLoader(false);
    }
}