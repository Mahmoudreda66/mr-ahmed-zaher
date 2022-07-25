let levelSelect = document.getElementById('level'),
	divisionContainer = document.getElementById('divisionSelect');

levelSelect.onchange = function () {
	if(this.value === "5" || this.value === "6"){
		divisionContainer.style.display = 'block';
	}else{
		divisionContainer.style.display = 'none';
	}
}