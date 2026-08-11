<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesion</h2>

    <form action="validar.php" method="POST">
      <label>Usuario</label><br>
      <input type="text" name="usuario" required><br><br>

      <label>Contraseña</label><br>
      <input type="password" name="clave" required><br><br>

      <button type="submit">Ingresar</button>
    </form>

    <p>¿No tienes una cuenta?<a href="cliente.php">Registrarse</a></p>
</body>
</html>