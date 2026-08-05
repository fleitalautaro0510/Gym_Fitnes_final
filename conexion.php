<?php
$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "mydb";

$conn = new mysqli($host, $usuario, $password, $basedatos);

if ($conn->connect_error) {
    die("Error de conexion: ". $conn->connect_error);
}
?>