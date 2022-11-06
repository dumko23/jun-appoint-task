function formlogin() {
    return {
        'email': $('#email').val(),
        'password': $('#password').val()
    }
}

function login() {
    let request = formlogin();
    $.post("loginUser", {'request': request}, function (data) {
        console.log('login success');
        console.log(JSON.parse(data))
    })
}
