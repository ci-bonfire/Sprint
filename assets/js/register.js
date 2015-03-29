/*
    Check and update our password strength with a call
    to the server.
 */
$(document).on('keyup', '#password', function(e) {

    var current_password = $(this).val();

    if (current_password == '')
    {
        $('.pass-strength').removeClass('alert-warning').removeClass('alert-success').html('');
        return false;
    }

    $.getJSON(
        'password_check/'+ current_password,
        function (data)
        {
            if (data.status == 'pass')
            {
                $('.pass-strength').removeClass('alert-warning').addClass('alert alert-success').html('Strong');
                $('#submit').prop('disabled', false);
            }
            else
            {
                $('.pass-strength').addClass('alert alert-warning').html('Too Weak');
                $('#submit').prop('disabled', true);
            }
        }
    );
});

$(document).on('keyup', '#pass-confirm', function(e) {

    var password = $('#password').val();
    var pass_confirm = $(this).val();

    if (pass_confirm == '')
    {
        $(this).css('border-color', 'inherit');
        return false;
    }

    if (password == pass_confirm)
    {
        $(this).css('border-color', 'green');
        $('#password').css('border-color', 'green');
    }
    else
    {
        $(this).css('border-color', 'red');
    }

});