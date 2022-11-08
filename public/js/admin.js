let button = `<div class="d-flex justify-content-end my-2">
        <button id="deleteButton" class="btn btn-outline-primary" disabled>Delete selected</button>
    </div>`

$(document).ready(function () {
    createTable();


    $('#deleteButton').on('click', function () {

        let ids = [];
        $('.selected').each(function () {
            ids.push($(this).attr('id').match(/(\d+)/)[0])
        })
        console.log('deleting users by id: ' + ids.join(', '));
        deleteUsers(ids);
        $('#deleteButton').prop("disabled", true);
    })
});

function createTable() {
    $('#table_id').DataTable({
        select: true,
        rowReorder: true

    });

    $("#table_id_wrapper .row:first-child").after(button);
}


$('#logout').on('click', function () {
    $.post("/admin/adminLogout", {'request': 'request'}, function (data) {

        console.log(JSON.parse(data))
    })
})

$('button').on('click', function (event) {
    event.stopPropagation();

    let id = [$(this).attr('id').match(/(\d+)/)[0]];
    console.log(`delete user: ${id[0]}`)

    deleteUsers(id);
});

$('tbody tr').on('click', function () {
    setTimeout(function () {
        if ($('.selected').length > 0) {
            $('#deleteButton').prop("disabled", false);
        } else {
            $('#deleteButton').prop("disabled", true);
        }
    }, 0);
})

function deleteUsers(id) {
    $.post("/admin/deleteUser", {'request': id}, function (data) {
        console.log(JSON.parse(data))
    }).always(function (jqXHR) {
        console.log(jqXHR.status);
        if (jqXHR.status === 200) {
            id.forEach((elem) => {
                $('#table_id').DataTable()
                    .row($(`#user${elem}`))
                    .remove()
                    .draw();
                console.log(`removed: user${elem}`)
            })
            console.log('success')
        } else {
            console.log('failed');
        }
    })
}