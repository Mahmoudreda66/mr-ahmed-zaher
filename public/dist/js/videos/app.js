function _(selector, all = false) { // alias function to get element
	if(all){
		return document.querySelectorAll(selector);
	}

	return document.querySelector(selector);
}
