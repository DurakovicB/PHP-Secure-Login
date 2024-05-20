var userLogic = {

    login: function() {
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
                    localStorage.setItem('user_id', response.id);
                    window.location.href = '/PHP-Secure-Login/dashboard';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                }
        });
    },
    populateDashboard: function() {
        var id = localStorage.getItem('user_id');
        $.ajax({
            url: 'userinfo/'+id,
            type: 'GET',
            success: function(response) {
                $('#welcome-message').text("Hello, "+response.username +"!");
                $('#email-message').text("Your current e-mail adress is "+response.email+".");
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log('AJAX error: ' + textStatus + ' : ' + errorThrown);
            }
        });
    },
    logout: function() {
        localStorage.clear();
        window.location.href = '/PHP-Secure-Login';
    }

}