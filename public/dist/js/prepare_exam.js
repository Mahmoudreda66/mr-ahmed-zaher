// global variables
let pageLoader              = document.getElementById('allpage-loader'),
    submitFormBtn           = document.getElementById('submitform-btn'),
    addSectionBtn           = document.getElementById('add-section-btn'),
    addQuestionModal        = new bootstrap.Modal(document.getElementById('addQuestionModal')),
    addQuestionForm         = document.getElementById('add-question-form'),
    questionTypeSelect      = addQuestionForm.querySelector('select#question-type'),
    questionBodyArea        = addQuestionForm.querySelector('#question-body-area');

function loaderStatus (status) {
    display = 'none';
    if(status){
        display = 'flex';
    }

    pageLoader.style.display = display;
}

addSectionBtn.onclick = function () {
    sectionModal.show();
}

function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    let breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function editSection () {
    let editSectionBtn          = document.querySelectorAll('.edit-section'),
        editSectionsModal       = new bootstrap.Modal(document.getElementById('editSectionModal')),
        editSectionsForm        = document.getElementById('edit-section-form');
    for(let i = 0; i < editSectionBtn.length; i++){
        editSectionBtn[i].onclick = function () {
            editSectionsForm.querySelector('input#id').value = editSectionBtn[i].dataset.id;
            editSectionsForm.querySelector('input#title').value = editSectionBtn[i].dataset.name;
            editSectionsForm.querySelector('textarea#description').value = editSectionBtn[i].dataset.description !== 'null' ? editSectionBtn[i].dataset.description : '';
            editSectionsForm.querySelector('input[type="radio"][value="' + editSectionBtn[i].dataset.direction + '"]').checked = true;
            editSectionsModal.show();
        }
    }       
}

function deleteSection () {
    let deleteSectionBtn        = document.querySelectorAll('.delete-section'),
        deleteSectionsModal     = new bootstrap.Modal(document.getElementById('deleteSectionModal')),
        deleteSectionsForm      = document.getElementById('delete-section-form');

    for(let i = 0; i < deleteSectionBtn.length; i++){
        deleteSectionBtn[i].onclick = function () {
            deleteSectionsForm.querySelector('input#id').value = deleteSectionBtn[i].dataset.id;
            deleteSectionsForm.querySelector('input#name').value = deleteSectionBtn[i].dataset.name;
            deleteSectionsModal.show();
        }
    }
}

function addSection () {
    let examId                  = sectionForm.querySelector('input#exam').value,
        sectionTitle            = sectionForm.querySelector('input#title').value,
        sectionDescription      = sectionForm.querySelector('textarea#description').value,
        sectionDirection        = sectionForm.querySelector('input[name="dir"]:checked').value,
        addSection              = new XMLHttpRequest();

    addSection.open('POST', '/admin/exams-sections');
    addSection.onload = function () {
        if(this.status === 200 && this.readyState === 4){ // success request

            let responseData = JSON.parse(this.responseText);

            if(responseData.status){ // true

                // prepare elements
                let parentEl      = document.createElement('div'),
                    childEl       = document.createElement('div'),
                    smallChildEl  = document.createElement('div'),
                    titleEl       = document.createElement('div'),
                    descriptionEl = document.createElement('small'),
                    optionsEl     = document.createElement('div'),
                    iEditEl       = document.createElement('i'),
                    iDeleteEl     = document.createElement('i'),
                    qParent       = document.createElement('div'),
                    iQEl          = document.createElement('i'),
                    addQTe        = document.createTextNode(' إضافة سؤال '),
                    questionArea  = document.createElement('div');

                parentEl.classList.add('mb-3', 'border-bottom', 'pb-2', 'pt-2', 'parent-section', 'dir-' + responseData.data[0].dir);
                if(responseData.data[0].dir == 'ltr'){
                    parentEl.classList.add('text-left');
                }else{
                    parentEl.classList.add('text-right');
                }
                parentEl.setAttribute('data-id', responseData.data[0].id);
                childEl.classList.add('tad', 'font-weight-bold', 'overflow-hidden', 'border-bottom', 'pb-2', 'd-inline-block');
                smallChildEl.classList.add('float-right');
                titleEl.textContent = responseData.data[1] + '- ' + responseData.data[0].title;
                descriptionEl.innerHTML = nl2br(responseData.data[0].description);
                optionsEl.classList.add('float-right');
                iEditEl.classList.add('fas', 'fa-edit', 'text-success', 'edit-section');
                iEditEl.setAttribute('data-id', responseData.data[0].id);
                iEditEl.setAttribute('data-name', responseData.data[0].title);
                iEditEl.setAttribute('data-description', responseData.data[0].description ? '' : responseData.data[0].description);
                iEditEl.setAttribute('data-direction', responseData.data[0].dir);
                iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger', 'delete-section');
                iDeleteEl.setAttribute('data-id', responseData.data[0].id);
                iDeleteEl.setAttribute('data-name', responseData.data[0].title);
                qParent.classList.add('as-a', 'mt-3', 'add-question-btn');
                qParent.id = 'add-question-btn';
                qParent.style.paddingRight = '15px';
                qParent.setAttribute('data-section', responseData.data[0].id);
                iQEl.classList.add('fas', 'fa-plus');
                questionArea.classList.add('mt-2');
                questionArea.id = 'question-area';
                qParent.appendChild(iQEl);
                qParent.appendChild(addQTe);
                smallChildEl.appendChild(titleEl);
                smallChildEl.appendChild(descriptionEl);
                childEl.appendChild(smallChildEl);
                optionsEl.appendChild(iEditEl);
                optionsEl.appendChild(iDeleteEl);
                childEl.appendChild(optionsEl);
                parentEl.appendChild(childEl);
                parentEl.appendChild(questionArea);
                parentEl.appendChild(qParent);
                document.getElementById('content-area').appendChild(parentEl);

                sectionModal.hide();

                for(let i = 0; i < document.querySelectorAll('.add-question-btn').length; i++){
                    document.querySelectorAll('.add-question-btn')[i].onclick = function () {
                        addQuestionForm.querySelector('input#section').value = document.querySelectorAll('.add-question-btn')[i].dataset.section;
                        addQuestionModal.show();
                    }    
                }

                editSection();

                deleteSection();

            }else{
                responseData.data.forEach(el => {
                   $.notify(el, 'error');     
                });
            }

        }else if(this.status === 404){
            location.reload();
        }else{ // failed request
            $.notify('لقد حدث خطأ غير متوقع', 'error');
        }
    }
    addSection.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    addSection.send('_token=' + document.querySelector('meta[name="_token"]').content + '&exam=' + examId + '&title=' + encodeURIComponent(sectionTitle) + '&description=' + encodeURIComponent(sectionDescription) + '&dir=' + sectionDirection);
}

editSection();

deleteSection();

submitFormBtn.onclick = function () {
    addSection();
}

sectionForm.onsubmit = function (e) {
    e.preventDefault();

    addSection();
}

for(let i = 0; i < document.querySelectorAll('.add-question-btn').length; i++){
    document.querySelectorAll('.add-question-btn')[i].onclick = function () {
        addQuestionForm.querySelector('input#section').value = document.querySelectorAll('.add-question-btn')[i].dataset.section;
        addQuestionModal.show();
    }    
}

let qCount = 0;

function addRadioButtonToSelectQuestion () {
    let parentEl    = document.createElement('div'),
        radioEl     = document.createElement('input'),
        qInputEl    = document.createElement('input'),
        removeBtnEl = document.createElement('small');

        qCount++;

    if(qCount <= 5){
        parentEl.classList.add('mb-3', 'overflow-hidden', 'position-relative');
        radioEl.setAttribute('type', 'radio');
        radioEl.setAttribute('name', 'optionQRadio');
        radioEl.classList.add('mt-3', 'q-option');
        qInputEl.setAttribute('type', 'text');
        qInputEl.classList.add('form-control', 'd-inline', 'float-left', 'q-text');
        qInputEl.setAttribute('placeholder', 'نص الإختيار');
        qInputEl.style.width = '91%';
        removeBtnEl.classList.add('fas', 'fa-times', 'close-btn-in-input');
        parentEl.appendChild(radioEl);
        parentEl.appendChild(qInputEl);
        parentEl.appendChild(removeBtnEl);
        questionBodyArea.appendChild(parentEl);

        let closeBtns = document.querySelectorAll('small.close-btn-in-input');
        for(let i = 0; i < closeBtns.length; i++){
            if(i != 0){
                qInputEl.focus();
            }
            closeBtns[i].onclick = function () {
                closeBtns[i].parentElement.remove();
                qCount--;
            }
        }

    }else{
        qCount = 5;
        $.notify('مسموح ب 5 إختيارات فقط للسؤال الواحد', 'error');
    }
}

function removeSelectBtn () {
    let el = document.getElementById('questions-area').querySelector('#addOptionBtn');
    if(el){
        el.remove();
    }
}

questionTypeSelect.onchange = function () {
    let questionTypeValue = this.value;
    questionBodyArea.innerHTML = '';

    if(questionTypeValue && questionTypeValue !== 'NULL'){

        if(questionTypeValue == 0){ // choose question

            let questionInputEl = document.createElement('input');

            questionInputEl.setAttribute('type', 'text');
            questionInputEl.setAttribute('placeholder', 'نص السؤال');
            questionInputEl.id = 'chooseQuestionInput';
            questionInputEl.classList.add('form-control', 'mb-3');
            questionBodyArea.appendChild(questionInputEl);

            qCount = 0;

            addRadioButtonToSelectQuestion();

            let addOptionBtn = document.createElement('small');

            addOptionBtn.innerHTML = '<i class="fas fa-plus"></i> إضافة إختيار';
            addOptionBtn.classList.add('as-a', 'mt-3');
            addOptionBtn.id = 'addOptionBtn';
            document.getElementById('questions-area').appendChild(addOptionBtn);

            document.getElementById('addOptionBtn').onclick = function () {
                addRadioButtonToSelectQuestion();
            }

        }else if(questionTypeValue == 1){ // long answer question
            
            let inputEl         = document.createElement('input'),
                labelCheckEl    = document.createElement('label'),
                checkEl         = document.createElement('input'),
                checkParent     = document.createElement('div');

            removeSelectBtn();

            inputEl.classList.add('form-control', 'mb-3');
            inputEl.id = 'longTextAreaBody';
            inputEl.setAttribute('type', 'text');
            inputEl.setAttribute('placeholder', 'نص السؤال');
            labelCheckEl.setAttribute('for', 'add_editor_to_answer');
            labelCheckEl.textContent = 'إضافة محرر نصوص للإجابة';
            labelCheckEl.classList.add('position-relative');
            labelCheckEl.style.right = '6px';
            checkEl.setAttribute('type', 'checkbox');
            checkEl.setAttribute('checked', '');
            checkEl.id = 'add_editor_to_answer';
            checkParent.appendChild(checkEl);
            checkParent.appendChild(labelCheckEl);

            questionBodyArea.appendChild(inputEl);
            questionBodyArea.appendChild(checkParent);

        }else if(questionTypeValue == 2){ // short answer question
            
            let inputEl = document.createElement('input');

            removeSelectBtn();

            inputEl.classList.add('form-control', 'mb-3');
            inputEl.id = 'shortTextAreaBody';
            inputEl.setAttribute('type', 'text');
            inputEl.setAttribute('placeholder', 'نص السؤال');

            questionBodyArea.appendChild(inputEl);

        }else if(questionTypeValue == 3){ // t&f question
            
            let inputEl             = document.createElement('input'),
                radiosParentEl      = document.createElement('div'),
                trueParentEl        = document.createElement('span'),
                falseParentEl       = document.createElement('span'),
                trueRadioEl         = document.createElement('input'),
                falseRadioEl        = document.createElement('input'),
                trueRadioLabelEl    = document.createElement('label'),
                falseRadioLabelEl   = document.createElement('label');

            removeSelectBtn();

            inputEl.classList.add('form-control', 'mb-3');
            inputEl.id = 't_f_question';
            inputEl.setAttribute('type', 'text');
            inputEl.setAttribute('placeholder', 'نص السؤال');
            trueRadioEl.setAttribute('type', 'radio');
            trueRadioEl.value = '1';
            trueRadioEl.id = 'trueRadioButton';
            trueRadioEl.name = 't_f_radio';
            falseRadioEl.setAttribute('type', 'radio');
            falseRadioEl.value = '0';
            falseRadioEl.id = 'falseRadioButton';
            falseRadioEl.name = 't_f_radio';
            trueRadioLabelEl.setAttribute('for', 'trueRadioButton');
            trueRadioLabelEl.textContent = 'صواب';
            trueRadioLabelEl.classList.add('ml-1');
            falseRadioLabelEl.setAttribute('for', 'falseRadioButton');
            falseRadioLabelEl.textContent = 'خطأ';
            falseRadioLabelEl.classList.add('ml-1');
            falseParentEl.classList.add('ml-2');

            trueParentEl.appendChild(trueRadioEl);
            trueParentEl.appendChild(trueRadioLabelEl);
            falseParentEl.appendChild(falseRadioEl);
            falseParentEl.appendChild(falseRadioLabelEl);
            radiosParentEl.appendChild(trueParentEl);
            radiosParentEl.appendChild(falseParentEl);
            questionBodyArea.appendChild(inputEl);
            questionBodyArea.appendChild(radiosParentEl);

        }else{
            $.notify('قم بإختيار نوع السؤال بشكل صحيح', 'error');
        }

    }
}

function deleteQuestion () {
    let deleteQuestionElements = document.querySelectorAll('.question i.fa-trash');

    for(let i = 0; i < deleteQuestionElements.length; i++){
        deleteQuestionElements[i].onclick = function () {
            if(confirm('هل أنت متأكد من رغبتك في حذف السؤال؟ ')){
                let deleteQuestion = new XMLHttpRequest(),
                    element = this.parentElement.parentElement;

                deleteQuestion.open('DELETE', '/admin/exams-questions/' + element.dataset.id);
                element.style.display = 'none';
                deleteQuestion.onload = function () {
                    if(this.status === 404){
                        $.notify('لم يتم العثور على السؤال', 'error');
                    }else if(this.status !== 200){
                        $.notify('لقد حدث خطأ غير متوقع', 'error');
                    }
                }
                deleteQuestion.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                deleteQuestion.send('_token=' + document.querySelector('meta[name="_token"]').content);
            }
        }
    }
}

function editQuestion () {
    let editQuestionElements = document.querySelectorAll('.question div i.fa-edit');

    for(let i = 0; i < editQuestionElements.length; i++){
        editQuestionElements[i].onclick = function () {
            let element = this.parentElement.parentElement,
                newContent = prompt('نص السؤال الجديد', element.querySelector('span:first-child').textContent.trim());

            if(newContent){
                element.querySelector('span:first-child').textContent = newContent;

                let editQuestion = new XMLHttpRequest();

                editQuestion.open('PUT', '/admin/exams-questions/1');
                editQuestion.onload = function () {
                    if(this.readyState === 4){
                        let responseData = JSON.parse(this.responseText);
                        if(this.status === 200){
                            if(!responseData.status){
                                element.querySelector('span:first-child').textContent = responseData.data.old_text;

                                responseData.data.messages.forEach(el => {
                                    $.notify(el, 'error');
                                });
                            }
                        }else if(this.status === 404){
                            $.notify("لم يتم العثور على السؤال", "error");
                        }else if(this.status === 500){
                            $.notify("لقد حدث خطأ غير متوقع", "error");
                        }
                    }
                }
                editQuestion.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                editQuestion.send('_token=' + document.querySelector('meta[name="_token"]').content + '&question_id=' + element.dataset.id + '&new_text=' + encodeURIComponent(newContent));
            }
        }
    }
}

function addChoice () {
    let addBtns = document.querySelectorAll('.question div.d-inline:first-of-type i.fa-plus');

    for(let i = 0; i < addBtns.length; i++){
        addBtns[i].onclick = function () {
            let element = this.parentElement.parentElement,
                questionId = element.dataset.id,
                newChoice = prompt('قم بكتابة الإختيار الجديد');

            if(newChoice){
                let containerElement = element.querySelector('table tbody tr'),
                    tdEl = document.createElement('td'),
                    spanForIndex = document.createElement('span'),
                    spanForText = document.createElement('span'),
                    smallForOptions = document.createElement('small'),
                    iEditEl = document.createElement('i'),
                    iDeleteEl = document.createElement('i');

                spanForIndex.textContent = (containerElement.children.length + 1) + '- ';
                spanForText.textContent = newChoice;
                tdEl.setAttribute('data-index', containerElement.children.length);
                tdEl.style.border = '0px';
                smallForOptions.classList.add('d-inline');
                iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                iEditEl.style.fontSize = '10px';
                iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                iDeleteEl.style.fontSize = '10px';
                smallForOptions.appendChild(iEditEl);
                smallForOptions.appendChild(iDeleteEl);

                tdEl.appendChild(spanForIndex);
                tdEl.appendChild(spanForText);
                tdEl.appendChild(smallForOptions);
                containerElement.appendChild(tdEl);

                choiceOptions();

                let sendNewChoice = new XMLHttpRequest();
                sendNewChoice.open('POST', '/admin/exams-questions/add-choice');
                sendNewChoice.onload = function () {
                    if(this.readyState === 4){
                        let responseData = JSON.parse(this.responseText);

                        if(!responseData.status){
                            if(responseData.message === 'validation'){
                                responseData.data.messages.forEach(el => {
                                    $.notify(el, 'error');
                                })
                            }

                            tdEl.remove();
                        }
                    }
                }
                sendNewChoice.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                sendNewChoice.send('_token=' + document.querySelector('meta[name="_token"]').content + '&new_choice=' + encodeURIComponent(newChoice) + '&question_id=' + questionId);
            }
        }
    }
}

function choiceOptions() {
    let editBtns = document.querySelectorAll('.question table td i.fa-edit'),
        deleteBtns = document.querySelectorAll('.question table td i.fa-trash');

    for(let i = 0; i < editBtns.length; i++){
        editBtns[i].onclick = function () {
            let element = this.parentElement.parentElement,
                questionId = element.parentElement.parentElement.parentElement.parentElement.dataset.id,
                elementIndex = element.dataset.index,
                oldTextElement = element.querySelector('span:nth-of-type(2)'),
                newText = prompt("قم بكتابة النص الجديد", oldTextElement.textContent.trim());

            if(newText){
                oldTextElement.textContent = newText;

                let sendEdit = new XMLHttpRequest();
                sendEdit.open('PUT', '/admin/exams-questions/edit-choice');
                sendEdit.onload = function () {
                    if(this.status === 200 && this.readyState === 4){
                        let responseData = JSON.parse(this.responseText);

                        if(!responseData.status){
                            if(responseData.message === 'validation'){
                                oldTextElement.textContent = responseData.data.old_text;
                                responseData.data.messages.forEach(el => {
                                    $.notify(el, 'error');
                                });
                            }
                        }
                    }
                }
                sendEdit.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                sendEdit.send('_token=' + document.querySelector('meta[name="_token"]').content + '&question_id=' + questionId + '&index=' + elementIndex + '&new_text=' + encodeURIComponent(newText));
            }
        }

        deleteBtns[i].onclick = function () {
            let element = this.parentElement.parentElement,
                questionId = element.parentElement.parentElement.parentElement.parentElement.dataset.id,
                elementIndex = element.dataset.index,
                oldTextElement = element.querySelector('span:nth-of-type(2)'),
                deleteChoice = confirm("هل تريد بالتأكيد حذف الخيار: " + oldTextElement.textContent.trim());

            if(deleteChoice){
                element.style.display = 'none';

                let deleteChoiceObject = new XMLHttpRequest();

                deleteChoiceObject.open('DELETE', '/admin/exams-questions/delete-choice');
                deleteChoiceObject.onload = function () {
                    if(this.readyState === 4){
                        if(this.status === 200){
                            let responseData = JSON.parse(this.responseText);
                            
                            if(!responseData.status){
                                if(responseData.message === 'validation'){
                                    responseData.data.forEach(el => {
                                        $.notify(el, 'error');
                                    });
                                }
                                element.style.display = 'block';
                            }else{
                                element.remove();
                            }
                        }else if(this.status === 404){
                            $.notify('لم يتم العثور على السؤال', 'error');
                        }else{
                            $.notify('لقد حدث خطأ غير متوقع', 'error');
                        }
                    }
                }
                deleteChoiceObject.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                deleteChoiceObject.send('_token=' + document.querySelector('meta[name="_token"]').content + '&question_id=' + questionId + '&index=' + elementIndex);
            }
        }
    }
}

deleteQuestion();
editQuestion();
addChoice();
choiceOptions();

function sendQuestion () {
    let questionType = questionTypeSelect.value,
        sendQuestion = new XMLHttpRequest(),
        examID       = addQuestionForm.querySelector('input#exam').value,
        sectionId    = addQuestionForm.querySelector('input#section').value,
        queryString  = '_token=' + document.querySelector('meta[name="_token"]').content + '&exam=' + examID + '&section=' + sectionId + '&type=' + questionType;

    sendQuestion.open('POST', '/admin/exams-questions');

    sendQuestion.onload = function () {
        if(this.status === 200 && this.readyState === 4){ // success request

            let responseData = JSON.parse(this.responseText);
            if(responseData.status){ // valid
                
                let questionData    = JSON.parse(this.responseText).data,
                    ulEl            = document.createElement('ul');

                ulEl.classList.add('list-style-square', 'my-0');

                if(questionData.type == 0){ // choose
                    
                    let liEl                = document.createElement('li'),
                        spanEl              = document.createElement('span'),
                        qTe                 = document.createTextNode(questionData.body.question),
                        optionsContainer    = document.createElement('div'),
                        iAddEl              = document.createElement('i'),
                        iEditEl             = document.createElement('i'),
                        iDeleteEl           = document.createElement('i'),
                        tbodyEl             = document.createElement('tbody'),
                        tableEl             = document.createElement('table'),
                        trEl                = document.createElement('tr'),
                        options             = questionData.body.options,
                        sections            = document.querySelectorAll('.parent-section');

                    liEl.classList.add('exam-question-c', 'question');
                    liEl.setAttribute('data-id', questionData.id);
                    spanEl.appendChild(qTe);
                    iAddEl.classList.add('fas', 'fa-plus', 'text-info');
                    iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                    iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                    optionsContainer.appendChild(iAddEl);
                    optionsContainer.appendChild(iEditEl);
                    optionsContainer.appendChild(iDeleteEl);
                    optionsContainer.classList.add('d-inline');
                    liEl.appendChild(spanEl);
                    liEl.appendChild(optionsContainer);
                    tableEl.classList.add('table', 'font-weight-normal');
                    options.forEach(function (el, i) {
                        let spanForIndex = document.createElement('span'),
                            spanForText = document.createElement('span'),
                            optionsContainer = document.createElement('small'),
                            iEditEl = document.createElement('i'),
                            iDeleteEl = document.createElement('i'),
                            tdEl = document.createElement('td');

                        tdEl.style.border = '0px';
                        tdEl.setAttribute('data-index', i);
                        iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                        iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                        iEditEl.style.fontSize = '10px';
                        iDeleteEl.style.fontSize = '10px';
                        optionsContainer.appendChild(iEditEl);
                        optionsContainer.appendChild(iDeleteEl);
                        spanForIndex.textContent = (i + 1 ) + '- ';
                        spanForText.textContent = el;
                        tdEl.appendChild(spanForIndex);
                        tdEl.appendChild(spanForText);
                        tdEl.appendChild(optionsContainer);
                        trEl.appendChild(tdEl);
                        if(questionData.answer == i){
                            tdEl.classList.add('text-success', 'font-weight-bold');
                        }
                    });
                    
                    tbodyEl.appendChild(trEl);
                    tableEl.appendChild(tbodyEl);
                    liEl.appendChild(tableEl);
                    ulEl.appendChild(liEl);

                    for(let i = 0; i < sections.length; i++){
                        if(sections[i].dataset.id == questionData.exam_section_id){
                            sections[i].querySelector('#question-area').appendChild(ulEl);
                        }
                    }

                    addQuestionModal.hide();

                }else if(questionData.type == 1){ // long answer
                    
                    let liEl                = document.createElement('li'),
                        textEl              = document.createElement('span'),
                        qTe                 = document.createTextNode(questionData.body.question),
                        optionsContainer    = document.createElement('div'),
                        iEditEl             = document.createElement('i'),
                        iDeleteEl           = document.createElement('i'),
                        sections            = document.querySelectorAll('.parent-section');

                    liEl.classList.add('exam-question-c', 'question');
                    optionsContainer.classList.add('d-inline');
                    iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                    iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                    optionsContainer.appendChild(iEditEl);
                    optionsContainer.appendChild(iDeleteEl);
                    liEl.setAttribute('data-id', questionData.id);
                    textEl.appendChild(qTe);
                    liEl.appendChild(textEl);
                    if(questionData.body.addEditor == 1){
                        let iEl = document.createElement('i');
                        iEl.classList.add('fas', 'fa-keyboard', 'text-info');
                        iEl.style.fontSize = '10px';
                        liEl.appendChild(iEl);
                    }
                    liEl.appendChild(optionsContainer);
                    ulEl.appendChild(liEl);

                    for(let i = 0; i < sections.length; i++){
                        if(sections[i].dataset.id == questionData.exam_section_id){
                            sections[i].querySelector('#question-area').appendChild(ulEl);
                        }
                    }

                    addQuestionModal.hide();

                }else if(questionData.type == 2){ // short answer
                    
                    let liEl        = document.createElement('li'),
                        textEl              = document.createElement('span'),
                        qTe         = document.createTextNode(questionData.body.question),
                        optionsContainer    = document.createElement('div'),
                        iEditEl             = document.createElement('i'),
                        iDeleteEl           = document.createElement('i'),
                        sections    = document.querySelectorAll('.parent-section');

                    liEl.classList.add('exam-question-c', 'question');
                    liEl.setAttribute('data-id', questionData.id);
                    optionsContainer.classList.add('d-inline');
                    iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                    iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                    optionsContainer.appendChild(iEditEl);
                    optionsContainer.appendChild(iDeleteEl);
                    textEl.appendChild(qTe);
                    liEl.appendChild(textEl);
                    liEl.appendChild(optionsContainer);
                    ulEl.appendChild(liEl);

                    for(let i = 0; i < sections.length; i++){
                        if(sections[i].dataset.id == questionData.exam_section_id){
                            sections[i].querySelector('#question-area').appendChild(ulEl);
                        }
                    }
                    
                    addQuestionModal.hide();

                }else if(questionData.type == 3){ // t&f
                    
                    let liEl        = document.createElement('li'),
                        textEl              = document.createElement('span'),
                        qTe         = document.createTextNode(questionData.body.question),
                        optionsContainer    = document.createElement('div'),
                        iEditEl             = document.createElement('i'),
                        iDeleteEl           = document.createElement('i'),
                        sections    = document.querySelectorAll('.parent-section');

                    liEl.classList.add('exam-question-c', 'question');
                    liEl.setAttribute('data-id', questionData.id);
                    optionsContainer.classList.add('d-inline');
                    iEditEl.classList.add('fas', 'fa-edit', 'text-success');
                    iDeleteEl.classList.add('fas', 'fa-trash', 'text-danger');
                    optionsContainer.appendChild(iEditEl);
                    optionsContainer.appendChild(iDeleteEl);
                    textEl.appendChild(qTe);
                    liEl.appendChild(textEl);
                    liEl.appendChild(optionsContainer);
                    ulEl.appendChild(liEl);

                    if(questionData.answer == 1){
                        liEl.classList.add('text-success');
                    }else{
                        liEl.classList.add('text-danger');
                    }

                    for(let i = 0; i < sections.length; i++){
                        if(sections[i].dataset.id == questionData.exam_section_id){
                            sections[i].querySelector('#question-area').appendChild(ulEl);
                        }
                    }

                    addQuestionModal.hide();

                }

            }else{ // invalid
                if(responseData.message == 'validation'){
                    responseData.data.forEach(el => {
                        $.notify(el, 'error'); 
                    });
                }
            }

        }else{ // failed request
            $.notify('لقد حدث خطأ غير متوقع', 'error');
        }

        deleteQuestion();
        editQuestion();
        addChoice();
        choiceOptions();
    }

    if(questionType == 0){ // choose question

        let optionInputs        = questionBodyArea.querySelectorAll('input[type="text"].q-text'),
            optionRadios        = questionBodyArea.querySelectorAll('input[type="radio"].q-option'),
            questionText        = questionBodyArea.querySelector('input#chooseQuestionInput').value,
            questions           = [],
            answer              = null;

        for(let i = 0; i < optionInputs.length; i++){
            questions.push(optionInputs[i].value);
        }

        for(let i = 0; i < optionRadios.length; i++){
            if(optionRadios[i].checked){
                answer = i;
            }
        }

        queryString += '&question=' + encodeURIComponent(questionText) + '&body=' + encodeURIComponent(JSON.stringify(questions)) + '&answer=' + encodeURIComponent(answer);

    }else if(questionType == 1){ // long answer question
        
        let question    = document.getElementById('longTextAreaBody').value,
            addEditor   = document.getElementById('add_editor_to_answer').checked,
            adVal       = null;

            if(addEditor){adVal = 1;}else{adVal = 0;}

        queryString += '&body=' + encodeURIComponent(question) + '&addEditor=' + adVal;

    }else if(questionType == 2){ // short answer question
        
        let question = document.getElementById('shortTextAreaBody').value;

        queryString += '&body=' + encodeURIComponent(question);

    }else if(questionType == 3){ // t&f question
        
        let question    = document.getElementById('t_f_question').value,
            radios      = document.querySelectorAll('input[type="radio"][name="t_f_radio"]'),
            qStatus     = null;

        for(let i = 0; i < radios.length; i++){
            if(radios[i].checked){
                qStatus = radios[i].value;
            }
        }

        queryString += '&body=' + encodeURIComponent(question) + '&answer=' + qStatus;

    }else{
        $.notify('قم بإختيار نوع السؤال بشكل صحيح', 'error');
    }

    sendQuestion.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    sendQuestion.send(queryString);
}

addQuestionForm.onsubmit = function (e) {
    e.preventDefault();
    sendQuestion();
}

document.getElementById('submit-add-q-form-btn').onclick = function () {
    sendQuestion();
}

let editSectionsForm = document.getElementById('edit-section-form');

function updateSection () {
    loaderStatus(true);

    let updateSection           = new XMLHttpRequest(),
        sectionId               = editSectionsForm.querySelector('input#id').value,
        sectionTitle            = editSectionsForm.querySelector('input#title').value,
        sectionDescription      = editSectionsForm.querySelector('textarea#description').value,
        sectionDirection        = editSectionsForm.querySelector('input[name="dir"]:checked').value;

    updateSection.open('PUT', '/admin/exams-sections/' + sectionId);
    updateSection.onload = function () {
        loaderStatus(false);
        if(this.status === 200 && this.readyState === 4){ // success request

            let responseData = JSON.parse(this.responseText);

            if(responseData.status){
                location.reload();
            }

        }else if(this.status === 404) {
            $.notify('لم يتم العثور على القسم', 'error');
        }else{ // failed request
            $.notify('لقد حدث خطأ غير متوقع', 'error');
        }
    }
    updateSection.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    updateSection.send('_token=' + document.querySelector('meta[name="_token"]').content + '&id=' + sectionId + '&title=' + sectionTitle + '&description=' + sectionDescription + '&dir=' + sectionDirection);
}

editSectionsForm.onsubmit = function (e) {
    e.preventDefault();
    updateSection();
}

document.getElementById('update-section-btn').onclick = function () {
    updateSection();
}