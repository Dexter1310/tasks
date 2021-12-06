$(document).ready(function () {

    var valueselect = $('#user_type').val();
    if (valueselect == 'operator') {
        $('#select-specilialized').show();
    } else {
        $('#select-specilialized').hide();
    }
    $('#user_type').change(function () {
        var value = this.value;
        if (value == "operator") {
            $('#select-specilialized').fadeIn();
        } else {
            $('#select-specilialized').fadeOut();
        }
    })

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
                $('#label-operator').html('Operador/es de ' + service);
                $('select[name="operator"]').empty();
                $.each(operator, function (index, value) {
                    $('select[name="operator"]').append("<option value=" + value.id + ">" + value.username + "</option>");
                });
            },
            error: function (data, status, object) {
            }
        });
    });

//    Todo recover pass for user since Admin
    $('#admin-recover-pass-user').click(function () {
        $('#block-recover-pass').fadeIn();
    });

});