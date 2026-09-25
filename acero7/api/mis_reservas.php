<?php
/**
 * GET api/mis_reservas.php
 * Header: Authorization: Bearer <token>
 * ---------------------------------------------------------------
 * Devuelve las reservas del socio logueado, separadas en próximas
 * (confirmadas o en lista de espera, con fecha de hoy en adelante) e
 * historial (pasadas o canceladas).
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

$usuario = apiUsuarioActual($pdo);
apiExigirSocio($usuario);

$stmt = $pdo->prepare(
    "SELECT r.id_reserva, r.fecha_clase, r.estado, r.fecha_reserva,
            c.id_clase, c.nombre, c.hora_inicio, c.hora_fin, c.sala
     FROM Reservas r
     JOIN Clases c ON c.id_clase = r.Fk_id_clase
     WHERE r.Fk_id_cliente = :cliente
     ORDER BY r.fecha_clase DESC, c.hora_inicio DESC"
);
$stmt->execute([':cliente' => $usuario['id_clientes']]);
$filas = $stmt->fetchAll();

$hoy = (new DateTime('today'))->format('Y-m-d');
$proximas = [];
$historial = [];

foreach ($filas as $r) {
    $item = [
        'id_reserva'  => (int)$r['id_reserva'],
        'clase'       => $r['nombre'],
        'fecha_clase' => $r['fecha_clase'],
        'hora_inicio' => substr($r['hora_inicio'], 0, 5),
        'hora_fin'    => substr($r['hora_fin'], 0, 5),
        'sala'        => $r['sala'],
        'estado'      => $r['estado'],
    ];

    $esFutura = $r['fecha_clase'] >= $hoy;
    $activa   = in_array($r['estado'], ['confirmada', 'lista_espera'], true);

    if ($esFutura && $activa) {
        $proximas[] = $item;
    } else {
        $historial[] = $item;
    }
}

apiOk(['proximas' => $proximas, 'historial' => $historial]);
