<?php
# Utiliza variables para asignarles los valores para ingresar a la base de datos MySQL
$host = "localhost";
$user = "root";
$password = "";
$database = "mydb";

# Inicia sesión
$conn = new mysqli($host, $user, $password, $database);

# Verifica si el inicio de sesión se ha realizado correctamente
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
#echo "Conexión exitosa";