<?php

include("conexion.php");

$nombre = $_POST['nombre_usuario'];
$email = $_POST['email'];
$clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);

$sql = "SELECT * FROM Usuarios WHERE email='$email'";
$resultado = mysqli_query($conn, $sql);

if (mysqli_num_rows($resultado) > 0) {
    die("El correo ya está registrado.");
}

$sqlCliente = "INSERT INTO Clientes() VALUES()";

if (!mysqli_query($conn, $sqlCliente)) {
    die(mysqli_error($conn));
}

$id_cliente = mysqli_insert_id($conn);

$sql = "INSERT INTO Usuarios
(nombre_usuario,email,clave,FK_id_rol,Fk_id_cliente)
VALUES
('$nombre','$email','$clave',2,$id_cliente)";

if (mysqli_query($conn, $sql)) {
    header("Location: login.php");
    exit();
} else {
    echo mysqli_error($conn);
}