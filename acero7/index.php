<?php
session_start();

// Rutas absolutas para evitar fallos de inclusión
require_once __DIR__ . '/Controlador/conexion.php';
require_once __DIR__ . '/Controlador/RegistroControlador.php';
require_once __DIR__ . '/Controlador/AuthControlador.php';
require_once __DIR__ . '/Controlador/AdminControlador.php';

$action = $_GET['action'] ?? 'inicio';

// conexion.php crea la variable $pdo (PDO)
$registroCtrl = new RegistroControlador($pdo);
$authCtrl     = new AuthControlador($pdo);
$adminCtrl    = new AdminControlador($pdo);

switch ($action) {
    /* ---------- Sitio público ---------- */
    case 'inicio':
        require __DIR__ . '/index.html';
        break;

    /* ---------- Registro ---------- */
    case 'registro':
        $registroCtrl->mostrarPaso1();
        break;
    case 'guardar_paso1':
        $registroCtrl->guardarPaso1();
        break;
    case 'paso2':
        $registroCtrl->mostrarPaso2();
        break;
    case 'guardar_paso2':
        $registroCtrl->guardarPaso2();
        break;

    /* ---------- Sesión ---------- */
    case 'login':
        $authCtrl->mostrarLogin();
        break;
    case 'procesar_login':
        $authCtrl->procesarLogin();
        break;
    case 'cliente_inicio':
        $authCtrl->mostrarInicioCliente();
        break;
    case 'cerrar_sesion':
        $authCtrl->cerrarSesion();
        break;

    /* ---------- Panel de administración (rol 1) ---------- */
    case 'admin_dashboard':
        $adminCtrl->panel();
        break;
    case 'admin_listar':
        $adminCtrl->listar();
        break;
    case 'admin_nuevo':
        $adminCtrl->nuevo();
        break;
    case 'admin_editar':
        $adminCtrl->editar();
        break;
    case 'admin_guardar':
        $adminCtrl->guardar();
        break;
    case 'admin_eliminar':
        $adminCtrl->eliminar();
        break;
    case 'acceso_denegado':
        $adminCtrl->accesoDenegado();
        break;

    /* ---------- Empleados (rol 3): por ahora ven la tienda ---------- */
    case 'empleado_dashboard':
        header('Location: productos.php');
        exit;

    default:
        $authCtrl->mostrarLogin();
        break;
}
