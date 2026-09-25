<?php
/**
 * crear_admin.php — script de UN SOLO USO
 * ---------------------------------------------------------------
 * Crea (o actualiza) un usuario administrador para poder entrar al panel.
 *
 * CÓMO USARLO:
 *   1. Copiá este archivo dentro de la carpeta del proyecto (al lado de index.php).
 *   2. Abrí en el navegador:  http://localhost/acero4/crear_admin.php
 *   3. Completá el formulario y apretá "Crear administrador".
 *   4. IMPORTANTE: cuando termines, BORRÁ este archivo del servidor.
 *      Mientras exista, cualquiera podría crearse un usuario administrador.
 */

require_once __DIR__ . '/Controlador/conexion.php';

$mensaje = null;
$tipo    = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $clave   = $_POST['clave'] ?? '';

    try {
        if ($usuario === '' || $email === '' || strlen($clave) < 4) {
            throw new RuntimeException('Completá usuario, email y una contraseña de al menos 4 caracteres.');
        }

        // 1. Asegurar que exista el rol 1 = Administrador.
        $rol = $pdo->query("SELECT id_rol FROM Roles WHERE id_rol = 1")->fetch();
        if (!$rol) {
            $pdo->exec("INSERT INTO Roles (id_rol, Rol) VALUES (1, 'Administrador')");
        }

        // 2. Crear el usuario, o actualizarlo si el nombre ya existe.
        $hash = password_hash($clave, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("SELECT id_usuarios FROM usuarios WHERE nombre_usuario = :u");
        $stmt->execute([':u' => $usuario]);
        $existente = $stmt->fetch();

        if ($existente) {
            $pdo->prepare(
                "UPDATE usuarios SET email = :e, clave = :c, FK_id_rol = 1 WHERE id_usuarios = :id"
            )->execute([':e' => $email, ':c' => $hash, ':id' => $existente['id_usuarios']]);

            $mensaje = 'El usuario "' . htmlspecialchars($usuario) . '" ya existía: se le puso la nueva contraseña y el rol de administrador.';
        } else {
            $pdo->prepare(
                "INSERT INTO usuarios (nombre_usuario, email, clave, FK_id_rol, Fk_id_cliente, Fk_id_empleado)
                 VALUES (:u, :e, :c, 1, NULL, NULL)"
            )->execute([':u' => $usuario, ':e' => $email, ':c' => $hash]);

            $mensaje = 'Administrador "' . htmlspecialchars($usuario) . '" creado correctamente.';
        }

        $tipo = 'ok';
    } catch (PDOException $e) {
        $mensaje = 'Error de base de datos: ' . htmlspecialchars($e->getMessage());
    } catch (RuntimeException $e) {
        $mensaje = htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear administrador | ACERO</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<div style="max-width:560px;margin:8vh auto;padding:0 24px;">
  <p class="eyebrow"><span class="eyebrow__dash"></span>Instalación</p>
  <h1 class="admin-title">Crear <em>administrador</em></h1>
  <p class="admin-subtitle">
    Este formulario da de alta un usuario con rol 1 para poder entrar al panel.
    Si el nombre de usuario ya existe, le cambia la contraseña y lo convierte en administrador.
  </p>

  <?php if ($mensaje): ?>
    <div class="admin-alert admin-alert--<?= $tipo ?>"><?= $mensaje ?></div>
    <?php if ($tipo === 'ok'): ?>
      <div class="admin-alert admin-alert--error">
        <strong>Último paso:</strong> borrá el archivo <code>crear_admin.php</code> del servidor.
        Mientras exista, cualquiera podría crearse un usuario administrador.
      </div>
      <p><a class="btn btn--solid" href="index.php?action=login">Ir al login</a></p>
    <?php endif; ?>
  <?php endif; ?>

  <form class="admin-form" method="post">
    <div class="admin-form__grid">
      <div class="admin-field">
        <label for="usuario">Nombre de usuario <span class="admin-req">*</span></label>
        <input type="text" id="usuario" name="usuario" value="admin" required>
      </div>
      <div class="admin-field">
        <label for="email">Email <span class="admin-req">*</span></label>
        <input type="email" id="email" name="email" value="admin@acerogym.com" required>
      </div>
      <div class="admin-field admin-field--ancho">
        <label for="clave">Contraseña <span class="admin-req">*</span></label>
        <input type="password" id="clave" name="clave" required>
        <small>Mínimo 4 caracteres. Se guarda encriptada con password_hash().</small>
      </div>
    </div>
    <div class="admin-form__acciones">
      <button class="btn btn--solid" type="submit">Crear administrador</button>
    </div>
  </form>
</div>

</body>
</html>
