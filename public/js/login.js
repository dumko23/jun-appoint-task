function formLogin()
{
    return {
        'email': $('#email').val(),
        'password': $('#password').val()
    }
}

function formRegister()
{
    return {
        'email': $('#emailRegister').val(),
        'name': $('#nameRegister').val(),
        'password': $('#passwordRegister').val(),
    }
}

function login()
{
    let request = formLogin();
    $.post("/api/userLogin", {'request': request}, function (data) {
        console.log('login success');
        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            window.location.replace("/");
        }
    });
}

$('#confirmLogin').on('click', function () {
    login()
})

$('#registerConfirm').on('click', function () {
    if ($('#passwordRegister').val() === $('#passwordRegisterConfirm').val()) {
        let request = formRegister();
        $.post("/api/userRegister", {'request': request}, function (data) {
            console.log('login success');
            console.log(JSON.parse(data))
        }).always(function (jqXHR) {
            console.log(jqXHR.status);
            if (jqXHR.status === 200) {
                $('#login').tab('show');
                $('#register').removeClass("active");
                $('#register-tab').removeClass("active");
                $('#login-tab').addClass("active");
            }
        });
    } else {
        console.log("password doesn't match");
        return false;
    }
})
