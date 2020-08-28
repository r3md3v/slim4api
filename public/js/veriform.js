function verifForm(arrayFields, msg) {
    // Alert and color if fields marked with asterisk are empty
    var nbVerif = 0;
    var warning = false;
    arrayFields.forEach(function (item) {
        if (item.value == '') {
            item.style.backgroundColor = '#FF5555';
            warning = true;
        } else {
            item.style.backgroundColor = '';
            nbVerif++;
        }
    });

    if (nbVerif < arrayFields.length || warning) {
        alert(msg);
        return false;
    }
    return true;
}

function verifFormSpace(arrayFields, msg) {
    // Alert and color if fields should not contain space
    var nbVerif = 0;
    arrayFields.forEach(function (item) {
        if (itemt.value.indexOf(" ") != -1) {
            item.style.backgroundColor = '#FFAAAA';
        } else {
            item.style.backgroundColor = '';
            nbVerif++;
        }
    });
    if (nbVerif < arrayFields.length) {
        alert(msg);
        return false;
    }
}

function validateEmail(email) {
    console.log('email:' + email.value);
    const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var res = re.test(email.value);
    if (!res) {
        email.style.backgroundColor = '#FFAAAA';
        alert("Incorrect email format")
    }
    return res;
}
