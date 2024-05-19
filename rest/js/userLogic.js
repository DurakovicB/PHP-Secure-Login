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
            if (response.status == 'success') {
                window.location.href = '/PHP-Secure-Login/dashboard';
              } else {
                alert(response.message);
              }
        },
        error: function(xhr) {
            }
    });
}