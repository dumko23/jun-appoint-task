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
    step(1, 2);
})
$("#step-2-complete").on('click', function () {
    step(2, 3);
})
$("#step-3-complete").on('click', function () {
    step(3, 4);
})