function verifForm(arrayFields,msg){
	// Alert and color if fields marked with asterisk are empty
	var nbVerif = 0;
	for (var i = 0, len = arrayFields.length ; i < len ; i++){
		if(arrayFields[i].value == ''){
			arrayFields[i].style.backgroundColor = '#FF5555';
		}else{
			arrayFields[i].style.backgroundColor = '';
			nbVerif++;
		}
	}
	if(nbVerif < arrayFields.length){
		alert(msg);
		return false;
	}
	return true;
}

function verifFormSpace(arrayFields,msg){
	// Alert and color if fields should not contain space
	var nbVerif = 0;
	for (var i = 0, len = arrayFields.length ; i < len ; i++){
		if(arrayFields[i].value.indexOf(" ") != -1){
			arrayFields[i].style.backgroundColor = '#FFAAAA';
		}else{
			arrayFields[i].style.backgroundColor = '';
			nbVerif++;
		}
	}
	if(nbVerif < arrayFields.length){
		alert(msg);
		return false;
	}
	return true;
}

function validateEmail(email,msg) {
	const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if(re.test(email.value)){
		email.style.backgroundColor = '';
		return true;
	}else{
		email.style.backgroundColor = '#FF5555';
		alert(msg);
		return false;
	}
}
