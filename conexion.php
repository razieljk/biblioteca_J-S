<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "mysql_6f2f9fbc-3439-482c-b468-e8b70ada324f";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
    die("Error conexión: " . $conn->connect_error);
}
?>
