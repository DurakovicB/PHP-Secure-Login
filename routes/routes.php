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


Flight::start();