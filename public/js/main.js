$(document).ready(function () {

    //Todo options selectec repeats

    removeItemsOptions('#select-state');
    removeItemsOptions('#select-periodic');

    function removeItemsOptions(select) {
        var finishItems = {};
        $(select + " > option").each(function () {
            if (finishItems[this.text]) {
                $(this).remove();
            } else {
                finishItems[this.text] = this.value;
            }
        });
    }


    /**
     * TODO VALIDATIONS FORMS
     */
    //Todo Validate pass innew USER
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

    //Todo: Validate form of SERVICE
    $('#service_name').keyup(function () {
        if ($('#service_name').val().length >= 2) {
            $('#btn-save-service').prop("disabled", false);
        }
    });


    //TODO: new USER added
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
                location.href = "/user";
            },
            error: function (data, status, object) {
                $('#info-admin').html('<scan style="color:blue">No se ha podido guardar el usuario por que ya existe en base de datos.</scan>')
            }
        });
    });
    //TODO: edit USER
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
                alert('Se acaba de modificar el usuario');
                location.href = "/user";
            },
            error: function () {
            }
        });
    });


    //TODO: new SERVICE added
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
                location.href = "/service";
            },
            error: function (data, status, object) {
            }
        });
    });

    //TODO: edit SERVICE
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
                location.href = "/service";
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
                location.href = "/task";
            },
            error: function (data, status, object) {
            }
        });
    });

    //TODO :new TASK advanced

    $('form[name="newAdvancedTask"]').submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.new.advanced.task'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                location.href = "/task";
            },
            error: function (data, status, object) {
            }
        });

    });

    //TODO :new COMPANY advanced

    $('form[name="company"]').submit(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var formSerialize = form.serialize();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.new.company'),
            data: formSerialize,
            async: true,
            success: function (data, status, object) {
                location.href = "/company";
            },
            error: function (data, status, object) {
            }
        });
    });
    //TODO: edit COMPANY
    $('form[name="editCompany"]').submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('editcompany'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                location.href = "/company";
            },
            error: function () {
            }
        });
    });


    //TODO:edit TASK For OPERATOR

    $('form[name="editTaskOperator"]').submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.edit.task.operator'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                alert("Actualización correcta")
                location.reload();
            },
            error: function () {
            }
        });
    });


    //TODO :new REQUEST  CLIENT

    $('form[name="request"]').submit(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var formSerialize = form.serialize();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.new.request'),
            data: formSerialize,
            async: true,
            success: function (data, status, object) {
                location.href = "/";
            },
            error: function (data, status, object) {
            }
        });
    });



});



