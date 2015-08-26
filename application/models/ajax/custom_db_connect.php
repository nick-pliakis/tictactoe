<?php

//Custom database connector. Instantiate a new PDO object that allows connection
//to the database, for our custom insertion in AJAX
$pdodb_username = 'tictactoe_admin';    // Database user name goes here
$pdodb_password = 'tictac123!';      // Database user password goes here
$dsn = 'mysql:host=localhost;dbname=tictactoe';

try {
    $pdo = new PDO($dsn,
                    $pdodb_username,
                    $pdodb_password,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

?>