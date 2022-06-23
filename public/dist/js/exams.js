let deleteModal = document.getElementById('deleteModal'),
    deleteBsModal = new bootstrap.Modal(deleteModal),
    toggleModal = document.getElementById('toggleModal'),
    toggleBsModal = new bootstrap.Modal(toggleModal),
    top10Modal = new bootstrap.Modal(document.getElementById('top10Modal'));

function deleteExam (exam) {
    deleteModal.querySelector('form#delete-form input#id').value = exam.id;
    deleteModal.querySelector('form#delete-form input#exam').value = exam.name;
    deleteBsModal.show();
}

function toggleBtns (exam) {
    toggleModal.querySelector('form#toggle-form input#id').value = exam.id;
    toggleModal.querySelector('form#toggle-form input#status').value = exam.status;
    toggleModal.querySelector('form#toggle-form input#exam').value = exam.name;
    toggleBsModal.show();
    if(exam.status == 0){
        document.getElementById('on').style.display = 'block';
        document.getElementById('off').style.display = 'none';
    }else{
        document.getElementById('off').style.display = 'block';
        document.getElementById('on').style.display = 'none';
    }
}

function loaderStatus (status) {
    let top10Loader = document.getElementById('loader');

    status ? (display = 'flex') : (display = 'none');
    top10Loader.style.display = display;
}

function theadStatus (status) {
    let thead = document.getElementById('top10_thead');

    status ? (display = 'table-row-group') : (display = 'none');
    thead.style.display = display;
}

function showTop10 (exam) {
    let top10Container = document.getElementById('top10_container');
    top10Container.innerHTML = '';
    loaderStatus(true);
    theadStatus(false);
    top10Modal.show();

    let getTop10 = new XMLHttpRequest();

    getTop10.open('GET', '/admin/exams/top_10/' + exam);
    getTop10.onload = function () {
        if(this.status === 200 && this.readyState === 4){
            let responseData = JSON.parse(this.responseText);
            if(Object.keys(responseData.data).length > 0){
                for (let el in responseData.data) {
                    let trEl       = document.createElement('tr'),
                        nameTdEl   = document.createElement('td'),
                        markTdEl     = document.createElement('td');

                    nameTdEl.textContent = el;
                    markTdEl.textContent = responseData.data[el];
                    trEl.appendChild(nameTdEl);
                    trEl.appendChild(markTdEl);

                    top10Container.appendChild(trEl);
                };

                theadStatus(true);
            }else{
                let colEl = document.createElement('div'),
                    alertEl = document.createElement('div');

                colEl.classList.add('col-12', 'mb-0');
                alertEl.classList.add('alert', 'alert-info', 'mb-0', 'text-center');
                alertEl.textContent = 'لا يوجد طلاب حتى الآن';
                colEl.appendChild(alertEl);
                top10Container.appendChild(colEl);

                theadStatus(false);
            }
            
            loaderStatus(false);
        }else if(this.status === 404){
            $.notify('لم يتم العثور على الإختبار', 'error');
            loaderStatus(false);
        }else{
            $.notify('لقد حدث خطأ غير متوقع', 'error');
            loaderStatus(false);
        }
    }
    getTop10.send();
}