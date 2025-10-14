<?php
$host = "bknkqxzvwrlkivc4nl9r-mysql.services.clever-cloud.com";
$user = "uaizhveabf76vn4n";                    
$pass = "lZ1zU9ETaANVVtVI6Va9";           
$db   = "bknkqxzvwrlkivc4nl9r";                 
$port = 3306;                             

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>