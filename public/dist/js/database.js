let truncationForm = document.getElementById('truncation-form'),
	submitTrancationBtn = truncationForm.querySelector('#submit-truncation-btn'),
	allCheckBoxes = truncationForm.querySelectorAll('input[type="checkbox"]'),
	selectAllTablesBtn = truncationForm.querySelector('#select-all-btn');

function disabledButtonStatus(button, status) {
	if (status) {
		button.removeAttribute('disabled');
	} else {
		button.setAttribute('disabled', '');
	}
}

function selectAllCheckBoxes(element, select) {
	for (let i = 0; i < allCheckBoxes.length; i++) {
		allCheckBoxes[i].checked = true;
	}

	disabledButtonStatus(submitTrancationBtn, true);
}

selectAllTablesBtn.onclick = function () {
	selectAllCheckBoxes(this);
}

for (let i = 0; i < allCheckBoxes.length; i++) {
	allCheckBoxes[i].onchange = function () {
		let checkedInputs = truncationForm.querySelectorAll('input[type="checkbox"]:checked');
		if (checkedInputs.length > 0) {
			disabledButtonStatus(submitTrancationBtn, true)
		} else {
			disabledButtonStatus(submitTrancationBtn, false)
		}
	}
}