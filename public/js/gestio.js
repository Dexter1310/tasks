$(document).ready(function () {

    var valueselect = $('#user_type').val();
    if (valueselect == 'operator') {
        $('#select-specilialized').show();
    } else {
        $('#select-specilialized').hide();
    }
    if (valueselect == 'client') {
        $('#new-info-client').show();
    } else {
        $('#new-info-client').hide();
    }
    $('#user_type').change(function () {
        var value = this.value;
        if (value == "operator") {
            $('#select-specilialized').fadeIn();
        } else {
            $('#select-specilialized').fadeOut();
        }
        if (value == "client") {
            $('#new-info-client').fadeIn();
        } else {
            $('#new-info-client').fadeOut();
        }
    })

    //TODO: btn forn open form new task only for one operator
    $('#newTask').hide();
    $('#btn-task-admin').click(function () {
        $('#newTask').toggle();
    });

    //TODO selected company superAdmin

    if ($('select[name="company"]').length) {  // if exists element select company in the form
        $('select[name="service"]').empty();
        $('select[name="operator"]').empty();
        $('select[name="specialized"]').empty();
        $('select[name="client"]').empty();
        $('select[name="company"]').change(function () {
            $('select[name="service"]').empty();
            $('select[name="operator"]').empty();
            $('select[name="specialized"]').empty();
            $('select[name="client"]').empty();
            var idCompany = this.value;
            $.ajax({
                type: 'POST',
                url: Routing.generate('ajax.select.company'),
                data: {id: idCompany},
                async: true,
                success: function (data, status, object) {
                    var services = object.responseJSON;

                    if (services || $('select[name="specialized"]').length) {
                        $('select[name="service"],select[name="specialized"]').append("<option value='' disabled selected>SELECCIONA SERVICIO</option>");
                        $.each(services, function (index, value) {
                            $('select[name="service"],select[name="specialized"]').append("<option value=" + value.id + ">" + value.name + "</option>");
                        });
                    }
                },
                error: function (data, status, object) {
                }
            });
            $.ajax({
                type: 'POST',
                url: Routing.generate('ajax.select.client.admin'),
                data: {id: idCompany},
                async: true,
                success: function (data, status, object) {
                    var clients = object.responseJSON;
                    if (clients || $('select[name="client"]').length) {
                        $('select[name="client"]').append("<option value='' disabled selected>SELECCIONA ClIENTE</option>");
                        $.each(clients, function (index, value) {
                            $('select[name="client"]').append("<option value=" + value.id + ">" + value.name + " " + value.lastname + "</option>");
                        });
                    }
                },
                error: function (data, status, object) {
                }
            });

        });

    }


    //TODO selected type task
    $('select[name="type-task"]').change(function () {
        var select = $("select[name='service']").val();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.select.operators'),
            data: {id: select},
            async: true,
            success: function (data, status, object) {
                var operator = object.responseJSON;
                addcheckOperator($('select[name="type-task"]').val());
            },
            error: function (data, status, object) {
            }
        });
    });


    //TODO: select for operator depending on which service you select
    $("select[name='service']").change(function () {
        var select = this.value;
        var service = $('select[name="service"] option:selected').text();
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.select.operators'),
            data: {id: select},
            async: true,
            success: function (data, status, object) {
                var operator = object.responseJSON;
                $('#label-operator').html('Operador de ' + service);
                $('select[name="operator"]').empty();
                $.each(operator, function (index, value) {
                    $('select[name="operator"]').append("<option value=" + value.id + ">" + value.name + " " + value.lastname + "</option>");
                });
                addcheckOperator($('select[name="type-task"]').val());
            },
            error: function (data, status, object) {
            }
        });

    });


//    Todo recover pass for user since Admin
    $('#admin-recover-pass-user').click(function () {
        $('#block-recover-pass').fadeIn();
    });


    function addcheckOperator(typeselect) {
        if (typeselect == 1) {
            $('#label-operator').html('Selecciona los operarios destinados');
            $('select[name="operator"]').hide();
            $("#check").empty();
            $('select[name="operator"] option').each(function () {
                var val = $(this).text();
                $("#check").append('<div class="form-check">\n' +
                    '  <input class="form-check-input" name="operator[]" type="checkbox" value="' + $(this).val() + '" id="flexCheckIndeterminate">\n' +
                    '  <label class="form-check-label" for="flexCheckIndeterminate">\n' + val + '</label>' +
                    '</div>');
            })
        } else {
            $("#check").empty();
            $('select[name="operator"]').show();
        }
    }

    $('#edit-time-task').click(function () {
        $('#form-edit-time').toggle()
    })

    //TODO : Edit time task with admin or superadmin

    $("form[name='edit-time-task']").submit(function (e) {
        e.preventDefault();
        var data = new FormData(this);
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.edit.time.task'),
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (data, status, object) {
                location.href = "/admin/task/show/" + data.id;
                alert("Editado el tiempo");
            },
            error: function (data, status, object) {
            }
        });

    });


    //TODO images reactions
    (function () {
        var ok = false;
        var longpress = 1000;
        var start;
        $(".imgTask1").on('mousedown', function (e) {
            start = new Date().getTime();

        });
        $(".imgTask1").on('mouseleave', function (e) {
            start = 0;

        });
        $(".imgTask1").on('mouseup', function (e) {
            if (new Date().getTime() >= (start + longpress)) {
                //todo: delete image
                if (confirm('¿Quiere eliminar la imagen 1?')) {
                    $('.imgTask1').hide();
                    $('input[name="imgDelete"]').attr('value', 'delete');
                }
            } else {
                //todo: zoom foto
                if (ok) {
                    $(this).animate({'zoom': 1}, 600)
                    ok = false;
                } else {
                    $(this).animate({'zoom': 3.5}, 600);
                    ok = true;
                }

            }
        });
    }());

    $('.tr-client .show-info-task-client').click(function () {
        $(this).find('table').toggle();
        $(this).find('table').css({'position':'absolute','left':0,'background':'#F1EECF'});


    })


});

function confirState(id) {
    var bool = confirm("¿Seguro que quiere cambiar el estado de la empresa?");
    if (bool) {
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.state.company'),
            data: {id: id},
            async: true,
            success: function (data, status, object) {
                location.href = "/company";
            },
            error: function (data, status, object) {
            }
        });
    }
}

function confirStateService(id) {
    var bool = confirm("¿Seguro que quiere cambiar el estado del servicio?");
    if (bool) {
        $.ajax({
            type: 'POST',
            url: Routing.generate('ajax.state.service'),
            data: {id: id},
            async: true,
            success: function (data, status, object) {
                location.href = "/service";
            },
            error: function (data, status, object) {
            }
        });
    }
}


// function reloadPage(time){
//     setTimeout("location.reload(true);", time);
// }
// //Podemos ejecutar la función de este modo
// //La página se actualizará dentro de 10 segundos
// reloadPage(10000);
//
