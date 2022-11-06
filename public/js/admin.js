$('#logout').on('click', function(){
    $.post("/api/adminLogout", {'request': 'request'}, function (data) {
        console.log('logout success');
        console.log(JSON.parse(data))
    })
        // .always(function (jqXHR) {
        //     console.log(jqXHR.status);
        //     if(jqXHR.status === 200){
        //         window.location.replace("/admin/users");
        //     }
        // });
})