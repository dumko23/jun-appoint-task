function formlogin() {
    return {
        'email': $('#email').val(),
        'password': $('#password').val()
    }
}

function login() {
    let request = formlogin();
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

$('#confirmLogin').on('click', function(){
    login()
})
