<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
// Using simple variables
$host = "localhost";
$user = "root";
$pass = "";
$db = "hostel_db";

// Connect to mysql
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
