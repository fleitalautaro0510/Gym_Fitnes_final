<?php

session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = trim($_POST['usuario']);
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM Usuarios
            WHERE nombre_usuario='$usuario'
            OR email='$usuario'";

    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) == 1) {

        $fila = mysqli_fetch_assoc($resultado);

        if (password_verify($clave, $fila['clave'])) {

            $_SESSION['id'] = $fila['id_usuarios'];
            $_SESSION['usuario'] = $fila['nombre_usuario'];
            $_SESSION['rol'] = $fila['FK_id_rol'];

            switch ($fila['FK_id_rol']) {

                case 1:
                    header("Location: administrador.php");
                    break;

                case 2:
                    header("Location: socio.php");
                    break;

                case 3:
                    header("Location: empleado.php");
                    break;

                default:
                    echo "Rol no válido.";
                    session_destroy();
                    exit();
            }

            exit();

        } else {

            echo "Contraseña incorrecta.";

        }

    } else {

        echo "Usuario no encontrado.";

    }

} else {

    header("Location: login.php");
    exit();

}

?>