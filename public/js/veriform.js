function verifForm(arrayFields, msg) {
    // Alert and color if fields marked with asterisk are empty
    var nbVerif = 0;
    var elt;
    arrayFields.forEach(function (item) {
        console.log('item: ' + item);
        elt = document.getElementsByName(item)[0]
        if (elt.value == '') {
            elt.style.backgroundColor = '#FF5555';
        } else {
            elt.style.backgroundColor = '';
            nbVerif++;
        }
    });

    if (nbVerif < arrayFields.length) {
        alert(msg);
        return false;
    }
    return true;
}

function verifFormSpace(arrayFields, msg) {
    // Alert and color if fields should not contain space
    var nbVerif = 0;
    var elt;
    arrayFields.forEach(function (item) {
        elt = document.getElementsByName(item)[0]
        if (elt.value.indexOf(" ") != -1) {
            elt.style.backgroundColor = '#FFAAAA';
        } else {
            elt.style.backgroundColor = '';
            nbVerif++;
        }
    });
    if (nbVerif < arrayFields.length) {
        alert(msg);
        return false;
    }
}

function validateEmail(emailField) {
    var email = document.getElementsByName(emailField)[0].value
    console.log('email:' + email);
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
