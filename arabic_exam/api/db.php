<?php
// api/db.php

$host     = "127.0.0.1";      // or "localhost"
$user     = "root";           // default XAMPP
$password = "";               // default is empty
$dbname   = "arabic_exam";    // MUST match the DB we created

$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>

