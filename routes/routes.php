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

Flight::start();