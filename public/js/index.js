$('#logout').on('click', function () {
    $.post("/api/userLogout", {'request': 'request'}, function (data) {

        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            console.log('success');
            window.location.replace("/");
        } else {
            console.log('failed');
        }
    })
});