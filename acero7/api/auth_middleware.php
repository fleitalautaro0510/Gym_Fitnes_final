<?php
/**
 * api/auth_middleware.php
 * ---------------------------------------------------------------
 * Valida el token Bearer que manda la app en cada request protegido
 * y devuelve los datos del usuario logueado (incluyendo su cliente).
 *
 * Uso en un endpoint:
 *   require_once __DIR__ . '/auth_middleware.php';
 *   $usuario = apiUsuarioActual($pdo); // corta con 401 si el token es inválido
 */

const API_DURACION_TOKEN_HORAS = 24 * 30; // 30 días

/** Devuelve el token Bearer del header Authorization, o null si no vino. */
function apiTokenDelHeader(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? '';

    if (preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
        return $m[1];
    }
    return null;
}

/** Crea un token nuevo para el usuario y lo guarda en tokens_api. */
function apiCrearToken(PDO $pdo, int $idUsuario): string
{
    $token  = bin2hex(random_bytes(32));
    $expira = (new DateTime())->modify('+' . API_DURACION_TOKEN_HORAS . ' hours')->format('Y-m-d H:i:s');

    $pdo->prepare('INSERT INTO tokens_api (token, Fk_id_usuario, expira) VALUES (:t, :u, :e)')
        ->execute([':t' => $token, ':u' => $idUsuario, ':e' => $expira]);

    return $token;
}

/**
 * Valida el token del request y devuelve los datos del usuario + su cliente.
 * Si el token falta, es inválido o expiró, corta la ejecución con 401.
 */
function apiUsuarioActual(PDO $pdo): array
{
    $token = apiTokenDelHeader();
    if (!$token) {
        apiError('Falta el token de sesión (header Authorization: Bearer <token>).', 401);
    }

    $stmt = $pdo->prepare(
        'SELECT u.id_usuarios, u.nombre_usuario, u.email, u.FK_id_rol, u.Fk_id_cliente,
                c.id_clientes, c.Nombre, c.apellido, c.DNI, c.codigo_acceso
         FROM tokens_api t
         JOIN usuarios u ON u.id_usuarios = t.Fk_id_usuario
         LEFT JOIN clientes c ON c.id_clientes = u.Fk_id_cliente
         WHERE t.token = :token AND t.expira > NOW()
         LIMIT 1'
    );
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        apiError('La sesión expiró o el token no es válido. Iniciá sesión de nuevo.', 401);
    }

    return $usuario;
}

/** Exige además que el usuario tenga un cliente asociado (o sea, que sea socio). */
function apiExigirSocio(array $usuario): void
{
    if (empty($usuario['id_clientes'])) {
        apiError('Esta cuenta no tiene un perfil de socio asociado.', 403);
    }
}

/**
 * Si el cliente todavía no tiene código de acceso (por ejemplo, porque se
 * registró después de correr la migración), le genera uno ahora mismo.
 * Devuelve el código ya garantizado.
 */
function apiAsegurarCodigoAcceso(PDO $pdo, array &$usuario): string
{
    if (!empty($usuario['codigo_acceso'])) {
        return $usuario['codigo_acceso'];
    }

    $codigo = 'ACERO-' . strtoupper(bin2hex(random_bytes(6)));
    $pdo->prepare('UPDATE clientes SET codigo_acceso = :cod WHERE id_clientes = :id')
        ->execute([':cod' => $codigo, ':id' => $usuario['id_clientes']]);

    $usuario['codigo_acceso'] = $codigo;
    return $codigo;
}
