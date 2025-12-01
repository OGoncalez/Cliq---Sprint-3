<?php 
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_Cliq";

$conn = mysqli_connect($host, $user, $pass, $db);
if($conn->connect_error){
        die("Falha na conexão". $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>