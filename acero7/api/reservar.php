<?php
/**
 * POST api/reservar.php
 * Header: Authorization: Bearer <token>
 * Body JSON: { "id_clase": 3, "fecha_clase": "2026-09-28" }
 * ---------------------------------------------------------------
 * Reserva un lugar en la clase para esa fecha. Si el cupo ya está lleno,
 * anota al socio en lista de espera en lugar de rechazar el pedido.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

apiRequierePost();
$usuario = apiUsuarioActual($pdo);
apiExigirSocio($usuario);

$body = apiBody();
$idClase = (int)($body['id_clase'] ?? 0);
$fecha   = (string)($body['fecha_clase'] ?? '');

if ($idClase <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    apiError('Faltan datos: id_clase y fecha_clase (AAAA-MM-DD).', 422);
}

$stmtClase = $pdo->prepare('SELECT id_clase, nombre, cupo_maximo FROM Clases WHERE id_clase = :id AND activa = 1');
$stmtClase->execute([':id' => $idClase]);
$clase = $stmtClase->fetch();
if (!$clase) {
    apiError('La clase no existe o ya no está activa.', 404);
}

// ¿El socio ya tiene una reserva activa para esta clase y fecha?
$stmtExiste = $pdo->prepare(
    "SELECT id_reserva FROM Reservas
     WHERE Fk_id_cliente = :cliente AND Fk_id_clase = :clase AND fecha_clase = :fecha
     AND estado IN ('confirmada','lista_espera')"
);
$stmtExiste->execute([':cliente' => $usuario['id_clientes'], ':clase' => $idClase, ':fecha' => $fecha]);
if ($stmtExiste->fetch()) {
    apiError('Ya tenés una reserva para esta clase en esa fecha.', 409);
}

$stmtOcupados = $pdo->prepare(
    "SELECT COUNT(*) FROM Reservas WHERE Fk_id_clase = :clase AND fecha_clase = :fecha AND estado = 'confirmada'"
);
$stmtOcupados->execute([':clase' => $idClase, ':fecha' => $fecha]);
$ocupados = (int)$stmtOcupados->fetchColumn();

$estado = $ocupados < (int)$clase['cupo_maximo'] ? 'confirmada' : 'lista_espera';

try {
    $pdo->prepare(
        "INSERT INTO Reservas (Fk_id_cliente, Fk_id_clase, fecha_clase, estado) VALUES (:cliente, :clase, :fecha, :estado)"
    )->execute([
        ':cliente' => $usuario['id_clientes'],
        ':clase'   => $idClase,
        ':fecha'   => $fecha,
        ':estado'  => $estado,
    ]);
} catch (PDOException $e) {
    apiError('No se pudo registrar la reserva (' . $e->getMessage() . ').', 500);
}

apiOk([
    'estado'  => $estado,
    'mensaje' => $estado === 'confirmada'
        ? 'Reserva confirmada para ' . $clase['nombre'] . ' el ' . $fecha . '.'
        : 'La clase está completa: quedaste en lista de espera para ' . $clase['nombre'] . '.',
]);
