<?php
session_start();

// Rutas absolutas para evitar fallos de inclusión
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/Controlador/RegistroControlador.php';
require_once __DIR__ . '/Controlador/AuthControlador.php';

$action = $_GET['action'] ?? 'login';

$registroCtrl = new RegistroControlador($conn);
$authCtrl     = new AuthControlador($conn);

switch ($action) {
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
    case 'login':
        $authCtrl->mostrarLogin();
        break;
    case 'procesar_login':
        $authCtrl->procesarLogin();
        break;
    default:
        $authCtrl->mostrarLogin();
        break;
}
?>