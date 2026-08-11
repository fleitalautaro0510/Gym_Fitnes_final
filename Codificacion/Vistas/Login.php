<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Gym Fitness</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    
    <?php if (isset($_GET['registrado'])): ?>
        <p style="color:green;">¡Registro completado! Ahora puedes iniciar sesión.</p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="index.php?action=procesar_login" method="POST">
        <input type="text" name="usuario_o_email" placeholder="Usuario o Email" required><br>
        <input type="password" name="clave" placeholder="Contraseña" required><br>
        <button type="submit">Ingresar</button>
    </form>
    <br>
    <a href="index.php?action=registro">¿No tienes cuenta? Regístrate aquí</a>
</body>
</html>