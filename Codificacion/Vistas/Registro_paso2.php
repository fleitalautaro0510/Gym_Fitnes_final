<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paso 2: Crear Usuario</title>
</head>
<body>
    <h2>Registro de Usuario - Paso 2</h2>
    <form action="index.php?action=guardar_paso2" method="POST">
        <input type="text" name="nombre_usuario" placeholder="Nombre de Usuario" required><br>
        <input type="email" name="email" placeholder="Correo Electrónico" required><br>
        <input type="password" name="clave" placeholder="Contraseña" required><br>
        <button type="submit">Finalizar Registro</button>
    </form>
</body>
</html>