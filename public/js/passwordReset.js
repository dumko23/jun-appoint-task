function validateEmail(email) {
    return email.match(
        /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
}

function validation() {
    if (validateEmail($('#email').val())) {
        $(`#step-1-complete`).prop("disabled", false);
    } else {
        $(`#step-1-complete`).prop("disabled", true);
    }
}

$('#email').on('input', validation);

$('#code').on('input', function () {
    if ($(this).val().length > 0) {
        $(`#step-2-complete`).prop("disabled", false);
    } else {
        $(`#step-2-complete`).prop("disabled", true);
    }
})

$('#passwordNewConfirm').on('input', function () {
    if ($(this).val().length > 0 && $('#passwordNew').val().length > 0) {
        $(`#step-3-complete`).prop("disabled", false);
    } else {
        $(`#step-3-complete`).prop("disabled", true);
    }
})

function step(prev, next) {
    $(`#step-${prev}-tab`).prop("disabled", true).removeClass("active");
    $(`#step-${next}-tab`).prop("disabled", false).addClass("active");

    $(`#step-${next}`).tab('show');
    $(`#step-${prev}`).removeClass("active");
}

$("#step-1-complete").on('click', function () {
    sendMail();

})
$("#step-2-complete").on('click', function () {
    checkCode();
})
$("#step-3-complete").on('click', function () {
    resetPass();
})


function sendMail() {
    let email = {
        'email': $('#email').val()
    }
    timeToResend();

    $.post("/api/sendMail", {'request': email}, function (data) {
        console.log('login success');
        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            console.log('mail sent');
            step(1, 2);
        }
    });
}

function timeToResend() {
    let sec = 30;
    let timer = setInterval(function () {
        $('#seconds-left').text(sec);
        sec--;
        if (sec < 0) {
            clearInterval(timer);
            $('#resend-btn').prop("disabled", false);
        }
    }, 1000);
}

$('#resend-btn').on('click', function () {
    sendMail();
    $('#resend-btn').prop("disabled", true);
})

function checkCode() {
    let code = {
        'email': $('#email').val(),
        'code': $('#code').val()
    }
    $.post("/api/acceptCode", {'request': code}, function (data) {
        console.log('login success');
        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            console.log('code approved');
            step(2, 3);
        }
    });
}

function resetPass() {

    if ($('#passwordNew').val() === $('#passwordNewConfirm').val()) {
        let request = {
            'email': $('#email').val(),
            'code': $('#code').val(),
            'password': $('#passwordNew').val(),
        }
        $.post("/api/resetPass", {'request': request}, function (data) {
            console.log('password reset');
            console.log(JSON.parse(data))
        }).always(function (jqXHR) {
            console.log(jqXHR.status);
            if (jqXHR.status === 200) {
                window.location.replace("/auth");
            }
        });
    } else {
        console.log("password doesn't match");
        return false;
    }
}