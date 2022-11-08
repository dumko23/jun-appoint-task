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
                }
            });
    }
}

$('#confirmLogin').on('click', login);