<?php
/**
 * POST api/login.php
 * ---------------------------------------------------------------
 * Body JSON: { "usuario": "ana", "clave": "1234" }
 * "usuario" puede ser el nombre de usuario o el email.
 *
 * Respuesta OK:
 * {
 *   "ok": true,
 *   "token": "...",
 *   "socio": { "id_clientes":1, "nombre":"Ana", "apellido":"Gomez", ... }
 * }
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_middleware.php';

apiRequierePost();
$body = apiBody();

$identificador = trim((string)($body['usuario'] ?? ''));
$clave         = (string)($body['clave'] ?? '');

if ($identificador === '' || $clave === '') {
    apiError('Ingresá tu usuario/email y tu contraseña.', 422);
}

$stmt = $pdo->prepare(
    'SELECT id_usuarios, nombre_usuario, email, clave, FK_id_rol, Fk_id_cliente
     FROM usuarios
     WHERE nombre_usuario = :id OR email = :id
     LIMIT 1'
);
$stmt->execute([':id' => $identificador]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($clave, $usuario['clave'])) {
    apiError('Usuario o contraseña incorrectos.', 401);
}

if ((int)$usuario['FK_id_rol'] !== 2) {
    apiError('Esta app es solo para socios. Tu cuenta no tiene ese rol.', 403);
}
if (empty($usuario['Fk_id_cliente'])) {
    apiError('Tu usuario no tiene un perfil de socio asociado. Consultá en recepción.', 403);
}

$token = apiCrearToken($pdo, (int)$usuario['id_usuarios']);

$stmtCliente = $pdo->prepare(
    'SELECT id_clientes, codigo_acceso, Nombre, apellido, DNI, genero
     FROM clientes WHERE id_clientes = :id'
);
$stmtCliente->execute([':id' => $usuario['Fk_id_cliente']]);
$cliente = $stmtCliente->fetch();

if (empty($cliente['codigo_acceso'])) {
    $clienteConId = ['id_clientes' => $cliente['id_clientes'], 'codigo_acceso' => null];
    $cliente['codigo_acceso'] = apiAsegurarCodigoAcceso($pdo, $clienteConId);
}

apiOk([
    'token' => $token,
    'socio' => [
        'id_clientes'   => (int)$cliente['id_clientes'],
        'nombre'        => $cliente['Nombre'],
        'apellido'      => $cliente['apellido'],
        'dni'           => $cliente['DNI'],
        'codigo_acceso' => $cliente['codigo_acceso'],
        'nombre_usuario'=> $usuario['nombre_usuario'],
        'email'         => $usuario['email'],
    ],
]);
