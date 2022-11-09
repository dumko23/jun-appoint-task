function formlogin() {
    return {
        'email': $('#email').val(),
        'password': $('#password').val()
    }
}

function login() {
    let request = formlogin();
    if (request['email'] !== '' && request['password'] !== '') {
        $.post("/admin/adminLogin", {'request': request}, function (data) {
            console.log('login success');
            console.log(JSON.parse(data))
        })
            .always(function (jqXHR) {
                console.log(jqXHR.status);
                if(jqXHR.status === 200){
                    window.location.replace("/admin/users");
                } else if (jqXHR.status === 206) {
                    $('#error-email-login').text('Failed to login');
                }
            });
    }
}

$('#password, #email').on('focus', function(){
    $('#error-email-login').text('');
})

$('#confirmLogin').on('click', login);

$('#logout').on('click', function () {
    $.post("/admin/adminLogout", {'request': 'request'}, function (data) {

        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            console.log('success');
            window.location.replace("/admin");
        } else {
            console.log('failed');
        }
    })
});