function formLogin() {
    return {
        'email': $('#email').val(),
        'password': $('#password').val()
    }
}

function formRegister() {
    return {
        'email': $('#emailRegister').val(),
        'name': $('#nameRegister').val(),
        'password': $('#passwordRegister').val(),
    }
}

function login() {
    let request = formLogin();
    $.post("/api/userLogin", {'request': request}, function (data) {
        console.log('login..');
    }).always(function(jqXHR){
        if (jqXHR.status === 200) {
            window.location.replace("/");
        } else if (jqXHR.status === 206) {
            $('#error-email-login').text(data.error.email);
        }
    });
}

$('#confirmLogin').on('click', function () {
    login();
});

$('#registerConfirm').on('click', function () {



    if (validation()) {
        return false;
    }
    let request = formRegister();
    $.post("/api/userRegister", {'request': request}, function (data) {
        console.log('register..');
        console.log(JSON.parse(data))
    }).always(function(jqXHR){
        if (jqXHR.status === 200) {
            $('#login').tab('show');
            $('#register').removeClass("active");
            $('#register-tab').removeClass("active");
            $('#login-tab').addClass("active");
        } else if (jqXHR.status === 206) {
            $('#error-email').text(data.error.email);
            $('#error-name').text(data.error.name);
            $('#error-password').text(data.error.password);
        }
    })
})

function validation(){
    let error = false;
    if (!($('#passwordRegister').val() === $('#passwordRegisterConfirm').val())) {
        $('#error-password-confirm').text("Password doesn't match");
        error = true;
    }
    if ($('#nameRegister').val() === '') {
        $('#error-name').text("Input is empty");
        error = true;
    }
    if ($('#emailRegister').val() === '') {
        $('#error-email').text("Input is empty");
        error = true;
    }
    if($('#passwordRegister').val() === ''){
        $('#error-password').text("Input is empty");
        error = true;
    }
    return error;
}

$('#emailRegister').on('focus', function () {
    $('#error-email').text('');
})

$('#nameRegister').on('focus', function () {
    $('#error-name').text('');
})
$('#passwordRegister').on('focus', function () {
    $('#error-password').text('');
})

$('#passwordRegisterConfirm').on('focus', function () {
    $('#error-password-confirm').text('');
})

$('#email').on('focus', function () {
    $('#error-email-login').text('');
})

$('#password').on('focus', function () {
    $('#error-email-login').text('');
})