<?php
/**
 * migrar_app_socio.php — script de UN SOLO USO
 * ---------------------------------------------------------------
 * Prepara la base de datos para la App Socio:
 *   - Le agrega a "clientes" un código de acceso único (para el QR).
 *   - Le agrega a "Clases" las columnas de horario, instructor, cupo y sala
 *     (si la tabla ya existe con otro formato, sólo agrega lo que falte).
 *   - Crea la tabla "Reservas" (una fila por socio + clase + fecha).
 *   - Crea la tabla "tokens_api" (sesiones de la app móvil).
 *
 * Es seguro ejecutarlo más de una vez: antes de crear o modificar algo,
 * revisa si ya existe.
 *
 * CÓMO USARLO:
 *   1. Copiá este archivo dentro de la carpeta del proyecto (al lado de index.php).
 *   2. Abrí en el navegador: http://localhost/acero4/migrar_app_socio.php
 *   3. Revisá que todos los pasos digan "OK".
 *   4. IMPORTANTE: borrá este archivo del servidor cuando termines.
 */

require_once __DIR__ . '/Controlador/conexion.php';

header('Content-Type: text/html; charset=utf-8');

function columnaExiste(PDO $pdo, string $tabla, string $columna): bool
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$tabla` LIKE :c");
    $stmt->execute([':c' => $columna]);
    return (bool)$stmt->fetch();
}

function tablaExiste(PDO $pdo, string $tabla): bool
{
    $stmt = $pdo->prepare("SHOW TABLES LIKE :t");
    $stmt->execute([':t' => $tabla]);
    return (bool)$stmt->fetch();
}

$pasos = [];

function paso(string $texto, callable $fn, array &$pasos): void
{
    try {
        $resultado = $fn();
        $pasos[] = ['ok' => true, 'texto' => $texto . ($resultado ? ' — ' . $resultado : '')];
    } catch (Throwable $e) {
        $pasos[] = ['ok' => false, 'texto' => $texto . ' — ERROR: ' . $e->getMessage()];
    }
}

/* 1. Código de acceso único por cliente (para generar el QR) */
paso('Columna clientes.codigo_acceso', function () use ($pdo) {
    if (columnaExiste($pdo, 'clientes', 'codigo_acceso')) {
        return 'ya existía';
    }
    $pdo->exec("ALTER TABLE clientes ADD COLUMN codigo_acceso VARCHAR(40) NULL UNIQUE AFTER id_clientes");
    return 'agregada';
}, $pasos);

paso('Generar códigos para clientes que no tienen', function () use ($pdo) {
    $stmt = $pdo->query("SELECT id_clientes FROM clientes WHERE codigo_acceso IS NULL OR codigo_acceso = ''");
    $filas = $stmt->fetchAll();
    $upd = $pdo->prepare("UPDATE clientes SET codigo_acceso = :cod WHERE id_clientes = :id");
    foreach ($filas as $fila) {
        $cod = 'ACERO-' . strtoupper(bin2hex(random_bytes(6)));
        $upd->execute([':cod' => $cod, ':id' => $fila['id_clientes']]);
    }
    return count($filas) . ' generado(s)';
}, $pasos);

/* 2. Tabla Clases con horario, instructor, cupo y sala */
paso('Tabla Clases', function () use ($pdo) {
    if (!tablaExiste($pdo, 'Clases')) {
        $pdo->exec("
            CREATE TABLE Clases (
                id_clase INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(60) NOT NULL,
                Fk_id_instructor INT NULL,
                dia_semana ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo') NOT NULL,
                hora_inicio TIME NOT NULL,
                hora_fin TIME NOT NULL,
                cupo_maximo INT NOT NULL DEFAULT 15,
                sala VARCHAR(45) NULL,
                activa TINYINT(1) NOT NULL DEFAULT 1,
                FOREIGN KEY (Fk_id_instructor) REFERENCES empleados(id_empleado)
            )
        ");
        return 'creada';
    }

    // Si la tabla ya existía (de una versión anterior más simple), se le agrega lo que falte.
    $agregadas = [];
    $columnas = [
        'nombre'           => "VARCHAR(60) NULL",
        'Fk_id_instructor' => "INT NULL",
        'dia_semana'       => "ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo') NULL",
        'hora_inicio'      => "TIME NULL",
        'hora_fin'         => "TIME NULL",
        'cupo_maximo'      => "INT NOT NULL DEFAULT 15",
        'sala'             => "VARCHAR(45) NULL",
        'activa'           => "TINYINT(1) NOT NULL DEFAULT 1",
    ];
    foreach ($columnas as $col => $tipo) {
        if (!columnaExiste($pdo, 'Clases', $col)) {
            $pdo->exec("ALTER TABLE Clases ADD COLUMN `$col` $tipo");
            $agregadas[] = $col;
        }
    }
    // Si la tabla vieja tenía una columna "clase" (nombre) y "nombre" quedó vacía, se copia.
    if (columnaExiste($pdo, 'Clases', 'clase') && columnaExiste($pdo, 'Clases', 'nombre')) {
        $pdo->exec("UPDATE Clases SET nombre = clase WHERE (nombre IS NULL OR nombre = '') AND clase IS NOT NULL");
    }
    return $agregadas ? ('agregadas: ' . implode(', ', $agregadas)) : 'ya estaba completa';
}, $pasos);

/* 3. Clases de ejemplo si la tabla quedó vacía (para poder probar la app ya mismo) */
paso('Clases de ejemplo', function () use ($pdo) {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM Clases")->fetchColumn();
    if ($total > 0) {
        return 'ya hay ' . $total . ' cargada(s), no se agregó nada';
    }
    $ejemplo = [
        ['HIIT',       'Lunes',     '18:00:00', '18:45:00', 20, 'Sala 1'],
        ['Musculación','Lunes',     '09:00:00', '10:00:00', 30, 'Sala 2'],
        ['Funcional',  'Miercoles', '19:00:00', '19:50:00', 15, 'Sala 1'],
        ['Yoga',       'Viernes',   '08:00:00', '08:50:00', 18, 'Sala 3'],
    ];
    $ins = $pdo->prepare(
        "INSERT INTO Clases (nombre, dia_semana, hora_inicio, hora_fin, cupo_maximo, sala) VALUES (?,?,?,?,?,?)"
    );
    foreach ($ejemplo as $fila) {
        $ins->execute($fila);
    }
    return count($ejemplo) . ' cargadas';
}, $pasos);

/* 4. Tabla Reservas */
paso('Tabla Reservas', function () use ($pdo) {
    if (tablaExiste($pdo, 'Reservas')) {
        return 'ya existía';
    }
    $pdo->exec("
        CREATE TABLE Reservas (
            id_reserva INT AUTO_INCREMENT PRIMARY KEY,
            Fk_id_cliente INT NOT NULL,
            Fk_id_clase INT NOT NULL,
            fecha_clase DATE NOT NULL,
            estado ENUM('confirmada','cancelada','lista_espera') NOT NULL DEFAULT 'confirmada',
            fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unica_reserva_activa (Fk_id_cliente, Fk_id_clase, fecha_clase),
            FOREIGN KEY (Fk_id_cliente) REFERENCES clientes(id_clientes),
            FOREIGN KEY (Fk_id_clase) REFERENCES Clases(id_clase)
        )
    ");
    return 'creada';
}, $pasos);

/* 5. Tabla de tokens de sesión para la app móvil */
paso('Tabla tokens_api', function () use ($pdo) {
    if (tablaExiste($pdo, 'tokens_api')) {
        return 'ya existía';
    }
    $pdo->exec("
        CREATE TABLE tokens_api (
            token VARCHAR(64) PRIMARY KEY,
            Fk_id_usuario INT NOT NULL,
            creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expira DATETIME NOT NULL,
            FOREIGN KEY (Fk_id_usuario) REFERENCES usuarios(id_usuarios)
        )
    ");
    return 'creada';
}, $pasos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Migración App Socio | ACERO</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
<div style="max-width:640px;margin:8vh auto;padding:0 24px;">
  <p class="eyebrow"><span class="eyebrow__dash"></span>Instalación</p>
  <h1 class="admin-title">Migración <em>App Socio</em></h1>
  <p class="admin-subtitle">Preparando la base de datos para la app móvil de socios.</p>

  <?php foreach ($pasos as $p): ?>
    <div class="admin-alert admin-alert--<?= $p['ok'] ? 'ok' : 'error' ?>">
      <?= $p['ok'] ? '✔ ' : '✘ ' ?><?= htmlspecialchars($p['texto']) ?>
    </div>
  <?php endforeach; ?>

  <div class="admin-alert admin-alert--error">
    <strong>Último paso:</strong> borrá <code>migrar_app_socio.php</code> del servidor.
  </div>
  <p><a class="btn btn--solid" href="index.php?action=admin_dashboard">Ir al panel de administración</a></p>
</div>
</body>
</html>
