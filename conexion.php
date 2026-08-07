<?php
<<<<<<< HEAD
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
=======
$host = "localhost";
$usuario = "root";
$password = "";
$basedatos = "mydb";

$conn = new mysqli($host, $usuario, $password, $basedatos);

if ($conn->connect_error) {
    die("Error de conexion: ". $conn->connect_error);
}
?>
>>>>>>> 6e1443744914c6cb44fa67a2bef240e5b1464b6b
