<?php

include("conexion.php");


$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$altura = $_POST['altura'];
$peso = $_POST['peso'];
$genero = $_POST['genero'];


$sql = "INSERT INTO Clientes
(Nombre, apellido, DNI, altura, peso, genero)
VALUES
('$nombre','$apellido','$dni','$altura','$peso','$genero')";


if(mysqli_query($conn,$sql)){

    echo "Cliente registrado correctamente.";
    echo "<br>";
    echo "<a href='registrar_usuario.php'>Crear usuario ahora</a>";

}else{

    echo "Error: ".mysqli_error($conn);

}


?>