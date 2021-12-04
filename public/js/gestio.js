$(document).ready(function () {

    var valueselect=$('#user_type').val();
    if(valueselect=='operator'){
        $('#select-specilialized').show();
    }else{
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


    $("select[name='service']").change(function(){
        var select= this.value;
        console.log(select);
    });


});