<?php
/**
 * GET api/clases.php
 * Header: Authorization: Bearer <token>
 * ---------------------------------------------------------------
 * Devuelve las clases activas junto con su PRÓXIMA fecha (según el día de
 * la semana configurado) y cuántos lugares quedan libres ese día.
 * También indica si el socio logueado ya tiene reserva para esa fecha.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

$usuario = apiUsuarioActual($pdo);
apiExigirSocio($usuario);

/** Calcula la próxima fecha (incluyendo hoy) para un día de la semana dado. */
function proximaFechaParaDia(string $diaSemana): string
{
    $dias = ['Lunes'=>1,'Martes'=>2,'Miercoles'=>3,'Jueves'=>4,'Viernes'=>5,'Sabado'=>6,'Domingo'=>7];
    $objetivo = $dias[$diaSemana] ?? 1;
    $hoy = new DateTime('today');
    $actual = (int)$hoy->format('N');
    $diff = ($objetivo - $actual + 7) % 7;
    $hoy->modify("+{$diff} days");
    return $hoy->format('Y-m-d');
}

$clases = $pdo->query(
    "SELECT c.id_clase, c.nombre, c.dia_semana, c.hora_inicio, c.hora_fin, c.cupo_maximo, c.sala,
            e.nombre AS instructor_nombre, e.apellido AS instructor_apellido
     FROM Clases c
     LEFT JOIN empleados e ON e.id_empleado = c.Fk_id_instructor
     WHERE c.activa = 1
     ORDER BY FIELD(c.dia_semana,'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo'), c.hora_inicio"
)->fetchAll();

if (!$clases) {
    apiOk(['clases' => []]);
}

$stmtOcupados = $pdo->prepare(
    "SELECT COUNT(*) FROM Reservas WHERE Fk_id_clase = :clase AND fecha_clase = :fecha AND estado = 'confirmada'"
);
$stmtMiReserva = $pdo->prepare(
    "SELECT id_reserva, estado FROM Reservas
     WHERE Fk_id_clase = :clase AND fecha_clase = :fecha AND Fk_id_cliente = :cliente
     AND estado IN ('confirmada','lista_espera') LIMIT 1"
);

$resultado = [];
foreach ($clases as $c) {
    $fecha = proximaFechaParaDia($c['dia_semana']);

    $stmtOcupados->execute([':clase' => $c['id_clase'], ':fecha' => $fecha]);
    $ocupados = (int)$stmtOcupados->fetchColumn();
    $disponibles = max(0, (int)$c['cupo_maximo'] - $ocupados);

    $stmtMiReserva->execute([':clase' => $c['id_clase'], ':fecha' => $fecha, ':cliente' => $usuario['id_clientes']]);
    $miReserva = $stmtMiReserva->fetch();

    $resultado[] = [
        'id_clase'      => (int)$c['id_clase'],
        'nombre'        => $c['nombre'],
        'dia_semana'    => $c['dia_semana'],
        'proxima_fecha' => $fecha,
        'hora_inicio'   => substr($c['hora_inicio'], 0, 5),
        'hora_fin'      => substr($c['hora_fin'], 0, 5),
        'sala'          => $c['sala'],
        'instructor'    => $c['instructor_nombre']
            ? trim($c['instructor_nombre'] . ' ' . $c['instructor_apellido'])
            : null,
        'cupo_maximo'   => (int)$c['cupo_maximo'],
        'cupos_disponibles' => $disponibles,
        'mi_reserva'    => $miReserva ? [
            'id_reserva' => (int)$miReserva['id_reserva'],
            'estado'     => $miReserva['estado'],
        ] : null,
    ];
}

apiOk(['clases' => $resultado]);
