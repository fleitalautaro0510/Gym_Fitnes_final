<?php
/**
 * Verificación de permisos para las páginas sueltas de administración
 * (las que no pasan por index.php, como Vistas/productos.php).
 *
 * Se incluye al principio del archivo a proteger:
 *     require_once __DIR__ . '/../Controlador/verificar_admin.php';
 *
 * Si el usuario no está logueado, lo manda al login.
 * Si está logueado pero no es administrador (rol 1), muestra "acceso denegado".
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ruta relativa hacia la raíz del proyecto, según desde dónde se incluya.
$raizProyecto = defined('ADMIN_RAIZ') ? ADMIN_RAIZ : '';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . $raizProyecto . 'index.php?action=login');
    exit;
}

if ((int)($_SESSION['user_rol'] ?? 0) !== 1) {
    header('Location: ' . $raizProyecto . 'index.php?action=acceso_denegado');
    exit;
}
