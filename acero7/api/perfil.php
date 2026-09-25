<?php
/**
 * GET api/perfil.php
 * Header: Authorization: Bearer <token>
 * ---------------------------------------------------------------
 * Devuelve los datos del socio logueado, su estado de membresía y el
 * código que se usa para generar el QR de acceso en la app.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

$usuario = apiUsuarioActual($pdo);
apiExigirSocio($usuario);
apiAsegurarCodigoAcceso($pdo, $usuario);

// Última membresía / pago del socio, si el proyecto ya tiene esas tablas cargadas.
$membresia = null;
try {
    $stmt = $pdo->prepare(
        "SELECT p.Monto, p.fecha, p.Fk_id_cliente
         FROM Pagos p
         WHERE p.Fk_id_cliente = :id
         ORDER BY p.fecha DESC LIMIT 1"
    );
    $stmt->execute([':id' => $usuario['id_clientes']]);
    $membresia = $stmt->fetch() ?: null;
} catch (PDOException $e) {
    $membresia = null; // si la tabla no existe todavía, seguimos sin romper la app
}

apiOk([
    'socio' => [
        'id_clientes'    => (int)$usuario['id_clientes'],
        'nombre'         => $usuario['Nombre'],
        'apellido'       => $usuario['apellido'],
        'dni'            => $usuario['DNI'],
        'codigo_acceso'  => $usuario['codigo_acceso'],
        'nombre_usuario' => $usuario['nombre_usuario'],
        'email'          => $usuario['email'],
    ],
    'ultimo_pago' => $membresia ? [
        'monto' => (float)$membresia['Monto'],
        'fecha' => $membresia['fecha'],
    ] : null,
]);
