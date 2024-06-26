<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

Flight::set('flight.views.path', 'rest/html');

Flight::set('flight.views.extension', '.html');

Flight::route('/', function(){
    Flight::redirect('/login');
});

Flight::route('GET /login', function(){
    Flight::render('login.html');
});

include('rest/config.class.php');

$servername = "localhost";
$dbname = "secure_login";

//$conn = new mysqli($servername, $mysql_username, $mysql_password, $dbname);
$conn = new mysqli(Config::DB_HOST(), Config::DB_USERNAME(), Config::DB_PASSWORD(), $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

Flight::route('GET /users', function(){
    global $conn;

    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User data found
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        Flight::json($users);
    } else {
        // No user data found
        Flight::json(array('message' => 'No user data found.'));
    }
});

Flight::route('GET /userinfo/@username', function($username) {
    global $conn;

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $result=$result->fetch_assoc();
            Flight::json($result);
        
        }
        else
            Flight::json(false);
});


Flight::route('GET /dashboard', function(){
    Flight::render('dashboard.html');
});

Flight::route('GET /register', function(){
    Flight::render('register.html');
});

Flight::route('POST /login', function(){
        global $conn;
    
        // Retrieve user inputs from the request
        $email = Flight::request()->data['email'];
        $password = Flight::request()->data['password'];
    
        // Retrieve the user data from the database
        $sql = "SELECT * FROM users WHERE email = ? ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc(); 
        $dbPassword = $row['password'];

        if ($row && ($password==$dbPassword)) {
            // User login successful
            Flight::json(array('username' => $row['username'], 'status' => 'success', 'message' => 'User logged in successfully.'));
        }
        else {
            // User login failed
            Flight::halt(401, json_encode(array('status' => 'error', 'message' => 'Invalid username or password')));
    
        }
    });

    Flight::route('POST /register', function(){
        global $conn;
    
        // Retrieve user inputs from the request
        $email = Flight::request()->data['email'];
        $password = Flight::request()->data['password'];
        $username = Flight::request()->data['username'];
        $phone_number = Flight::request()->data['phone_number'];

        // Check if username is already taken
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            Flight::json(array('status' => 'error', 'message' => 'Username already taken.'));
            return;
        }

        // Check if password is valid
        if (strlen($password) < 8) {
            Flight::json(array('status' => 'error', 'message' => 'Password must have at least 8 characters.'));
            return;
        }

        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Flight::json(array('status' => 'error', 'message' => 'Invalid email address.'));
            return;
        }

        //check for mx records for the domain
        $parts = explode('@', $email);
        $domain = isset($parts[1]) ? $parts[1] : '';
    
        $mxhosts=[];
        if (getmxrr($domain, $mxhosts)) {
    
        } else {
            Flight::json(array('status' => 'error', 'message' => "No domains for the e-mail adress domain."));
            return;
        }

        // Check if phone number is already taken
        $sql = "SELECT * FROM users WHERE phone_number = '$phone_number'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            Flight::json(array('status' => 'error', 'message' => 'Phone number already registered.'));
            return;
        }

        // Insert the user data into the database
        $sql = "INSERT INTO users (username, email, password, phone_number) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $username, $email, $password, $phone_number);
        $stmt->execute();   

        // User registration successful
        Flight::json(array('status' => 'success', 'message' => 'User registered successfully'));
        
    });
Flight::start();