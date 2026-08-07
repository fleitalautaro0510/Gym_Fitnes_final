<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
</head>
<body>
    <h2>Registrar Usuario</h2>

    <form action="guardar_usuario.php" method="post">

    <label>Nombre de Usuario</label><br>
    <input type="text" name="nombre_usuario" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña</label><br>
    <input type="password" name="clave" required><br><br>

    <input type="submit" value="Registrar"><br><br>

    </form>
    <a href="login.php"><input type="button" value="Volver"></a>
    
</body>
</html>