let startBtn = document.getElementById('exam-start'),
    csrfToken = document.querySelector('meta[name="_token"]').content;

function loaderStatus(status) {
    let display = null;
    if (status) {
        display = 'flex';
    } else {
        display = 'none';
    }
    document.getElementById('allpage-loader').style.display = display;
}

function timerStatus(status) {
    let display = null;
    if (status) {
        display = 'flex';
    } else {
        display = 'none';
    }
    document.getElementById('time-counter').style.display = display;
}

function closeExam() {
    loaderStatus(true);

    document.getElementById('exam-form').submit();
}

startBtn.onclick = function () {
    loaderStatus(true);

    let setStudentEnter = new XMLHttpRequest();
    setStudentEnter.open('POST', '/students/exams/' + this.dataset.exam + '/enter');
    setStudentEnter.onload = function () {
        if (this.status === 200 && this.readyState === 4) { // success

            let responseData = JSON.parse(this.responseText);
            if (responseData.status) {

                loaderStatus(false);
                document.querySelector('body').style.overflow = 'auto';
                document.getElementById('exam-ready-loader').remove();

                // prepare timer
                let examTime = document.getElementById('exam-duration').textContent,
                    time = examTime * 60,
                    examMainTimer = setInterval(setTimer, 1000),
                    timerElement = document.getElementById('time-counter');

                function setTimer() {
                    let minuets = Math.floor(time / 60),
                        seconds = time % 60;

                    if (minuets == 0 && seconds == 0) { // close exam

                        clearInterval(examMainTimer);
                        timerElement.classList.remove('timer-alert-animation');
                        timerElement.classList.add('bg-danger');

                        closeExam();

                    }

                    if (minuets < 3) {
                        timerElement.classList.add('timer-alert-animation');
                    }

                    minuets = minuets < 10 ? '0' + minuets : minuets;
                    seconds = seconds < 10 ? '0' + seconds : seconds;


                    timerElement.textContent = `${minuets}:${seconds}`;

                    time--;
                }

            } else {
                $.notify(responseData.message, 'error');
                loaderStatus(false);
            }
        } else if (this.status === 404) {
            $.notify('لم يتم العثور على الإختبار أو ربما تم قفله', 'error');
            loaderStatus(false);
        }else if(this.status === 419) {
            location.href = 'students/exams';
            loaderStatus(false);
        }else{
            loaderStatus(false);
        }
    }
    setStudentEnter.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    setStudentEnter.send('_token=' + csrfToken);
}

let toggleStatus = document.getElementById('toggle-status'),
    status = true,
    endExam = document.getElementById('end-exam');

toggleStatus.onclick = function () {
    if (status) {
        timerStatus(false);
        status = false;
        toggleStatus.textContent = 'إظهار العداد';
    } else {
        timerStatus(true);
        status = true;
        toggleStatus.textContent = 'إخفاء العداد';
    }
}

endExam.onclick = function () {
    closeExam();
}