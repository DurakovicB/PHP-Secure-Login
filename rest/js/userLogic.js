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
                    localStorage.setItem('username', response.username);
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
        var username = localStorage.getItem('username');
        $.ajax({
            url: 'userinfo/'+username,
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
    },

    register: function() {
        var username = $('#username').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();
        var phone_number = $('#phone_number').val();

        if(password.length < 8){
            alert('Password must be at least 8 characters long');
            return;
        }
        
        if (password != confirmPassword) {
            alert('Passwords do not match');
            return;
        }

        $.ajax({
            url: 'register',
            type: 'POST',
            data: {
                username: username,
                email: email,
                password: md5(password),
                phone_number: phone_number
            },
            success: function(response) {
                if (response.status == 'success') {
                    alert('User registered successfully');
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                console.log('AJAX error: ' + xhr);
            }
        });
    }
}
