function login() {
    var email = $('#email').val();
    var password = $('#password').val();

    $.ajax({
        url: 'login',
        type: 'POST',
        data: {
            email: email,
            password: md5(password)
        },
        success: function(response) {
            $('#message').html('<div class="alert alert-success">' + response.message + '</div>');
        },
        error: function(xhr) {
            }
    });
}