<?php
/**
 * POST api/cancelar_reserva.php
 * Header: Authorization: Bearer <token>
 * Body JSON: { "id_reserva": 12 }
 * ---------------------------------------------------------------
 * Cancela una reserva del socio logueado. Si la reserva cancelada estaba
 * "confirmada" y hay gente en lista de espera para esa misma clase y fecha,
 * asciende automáticamente a la primera de la lista.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

apiRequierePost();
$usuario = apiUsuarioActual($pdo);
apiExigirSocio($usuario);

$body = apiBody();
$idReserva = (int)($body['id_reserva'] ?? 0);
if ($idReserva <= 0) {
    apiError('Falta el id_reserva.', 422);
}

$stmt = $pdo->prepare(
    "SELECT id_reserva, Fk_id_clase, fecha_clase, estado FROM Reservas
     WHERE id_reserva = :id AND Fk_id_cliente = :cliente"
);
$stmt->execute([':id' => $idReserva, ':cliente' => $usuario['id_clientes']]);
$reserva = $stmt->fetch();

if (!$reserva) {
    apiError('No se encontró esa reserva, o no te pertenece.', 404);
}
if ($reserva['estado'] === 'cancelada') {
    apiError('Esa reserva ya estaba cancelada.', 409);
}

$pdo->beginTransaction();
try {
    $pdo->prepare("UPDATE Reservas SET estado = 'cancelada' WHERE id_reserva = :id")
        ->execute([':id' => $idReserva]);

    if ($reserva['estado'] === 'confirmada') {
        // Busca al primero en lista de espera para esa clase y fecha, y lo confirma.
        $stmtEspera = $pdo->prepare(
            "SELECT id_reserva FROM Reservas
             WHERE Fk_id_clase = :clase AND fecha_clase = :fecha AND estado = 'lista_espera'
             ORDER BY fecha_reserva ASC LIMIT 1"
        );
        $stmtEspera->execute([':clase' => $reserva['Fk_id_clase'], ':fecha' => $reserva['fecha_clase']]);
        $siguiente = $stmtEspera->fetch();

        if ($siguiente) {
            $pdo->prepare("UPDATE Reservas SET estado = 'confirmada' WHERE id_reserva = :id")
                ->execute([':id' => $siguiente['id_reserva']]);
        }
    }

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    apiError('No se pudo cancelar la reserva (' . $e->getMessage() . ').', 500);
}

apiOk(['mensaje' => 'Reserva cancelada correctamente.']);
