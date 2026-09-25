<?php
/**
 * api/config.php
 * ---------------------------------------------------------------
 * Se incluye al principio de CADA endpoint de la API. Se encarga de:
 *   - Habilitar CORS (para que la app React Native pueda llamar a esta API
 *     desde otro origen: el celular/emulador).
 *   - Fijar la respuesta como JSON.
 *   - Conectar a la base de datos ($pdo, ya definida en Controlador/conexion.php).
 *   - Ofrecer helpers para responder éxito/error de forma consistente.
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// El navegador (o RN en algunos casos) manda un OPTIONS antes del POST real.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../Controlador/conexion.php';

/** Responde con éxito y termina la ejecución. */
function apiOk($datos = [], int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => true] + (is_array($datos) ? $datos : ['datos' => $datos]), JSON_UNESCAPED_UNICODE);
    exit;
}

/** Responde con error y termina la ejecución. */
function apiError(string $mensaje, int $codigo = 400): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

/** Lee el body JSON del request (para POST). */
function apiBody(): array
{
    $crudo = file_get_contents('php://input');
    $datos = json_decode($crudo, true);
    return is_array($datos) ? $datos : [];
}

/** Exige que el request sea POST. */
function apiRequierePost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        apiError('Este endpoint requiere POST.', 405);
    }
}
