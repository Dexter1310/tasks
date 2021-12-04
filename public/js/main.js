$(document).ready(function () {
    /**
     * TODO VALIDATIONS FORMS
     */
    //Todo Validate pass innew user
    $('.passUser ,#rememberPass').keyup(function () {
        if (($('.passUser').val() === $('#rememberPass').val()) && $('.passUser').val().length >= 7 && $('#rememberPass').val().length >= 7) {
            $('#btn-save-user').prop("disabled", false);
            $('#rememberPass').css('border', ' 2px solid #A8EE35');
            $('.passUser').css('border', ' 2px solid #A8EE35');
            $('#info-user').empty();
        } else if ($('.passUser').val() === "" || $('#rememberPass').val() === "") {
            $('#rememberPass').attr('placeholder', 'Repita contraseña');
            $('.passUser').attr('placeholder', 'Contraseña');
            $('#rememberPass').css('border', ' 1px solid transparent');
            $('.passUser').css('border', ' 1px solid transparent');
        } else if ($('.passUser').val().length < 7 || $('#rememberPass').val().length < 7) {
            $('#rememberPass').css('border', ' 2px solid red');
            $('.passUser').css('border', ' 2px solid red');
            $('#info-user').html('<br><h6 class="mt-3" style="color:orange"> Mínimo 8 caracteres alfanuméricos....</h6>');
        } else {
            $('#rememberPass').css('border', ' 2px solid red');
            $('.passUser').css('border', ' 2px solid red');
            $('#btn-save-user').prop("disabled", true);
        }
    });

    //Todo: Validate form of service
    $('#service_name').keyup(function () {
        if ($('#service_name').val().length >= 2) {
            $('#btn-save-service').prop("disabled", false);
        }
    });


    //TODO: new user added
    $('form[name="user"]').submit(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var formSerialize = form.serialize();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.user'),
            data: formSerialize,
            async: true,
            success: function (data, status, object) {
                location.href="/user";
            },
            error: function (data, status, object) {
                $('#info-admin').html('<scan style="color:blue">No se ha podido guardar el usuario por que ya existe en base de datos.</scan>')
            }
        });
    });
    //TODO: edit User
    $('form[name="editUser"]').submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('edituser'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                $('#info-admin').html("<span style='color: green'>Actualizada el usuario : " + data + "</span>");
                location.href="/user";
            },
            error: function () {
            }
        });
    });


    //TODO: new service added
    $('form[name="service"]').submit(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var formSerialize = form.serialize();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.new.service'),
            data: formSerialize,
            async: true,
            success: function (data, status, object) {
                location.href="/service";
            },
            error: function (data, status, object) {
            }
        });
    });

    //TODO: edit Service
    $('form[name="editService"]').submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('editservice'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                $('#info-admin').html("<span style='color: green'>Actualizada el servicio : " + data + "</span>");
                location.href="/service";
            },
            error: function () {
            }
        });
    });


    //TODO: new TASK added
    $('form[name="newTask"]').submit(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var formSerialize = form.serialize();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.new.task'),
            data: formSerialize,
            async: true,
            success: function (data, status, object) {
                 location.href="/user";
            },
            error: function (data, status, object) {
            }
        });
    });


});



