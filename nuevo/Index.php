<?php
session_start();

// Conexión
require_once __DIR__ . '/conexion.php';

// Controladores
require_once __DIR__ . '/Controlador/RegistroControlador.php';
require_once __DIR__ . '/Controlador/AuthControlador.php';
require_once __DIR__ . '/Controlador/AdministradorControlador.php';
require_once __DIR__ . '/Controlador/EmpleadoControlador.php';

// Acción solicitada
$action = $_GET['action'] ?? 'login';

// Instanciar controladores
$registroCtrl = new RegistroControlador($conn);
$authCtrl     = new AuthControlador($conn);
$adminCtrl    = new AdministradorControlador($conn);
$empleadoCtrl = new EmpleadoControlador();

// Rutas
switch ($action) {

    // Registro
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

    // Login
    case 'login':
        $authCtrl->mostrarLogin();
        break;

    case 'procesar_login':
        $authCtrl->procesarLogin();
        break;

    // Paneles principales
    case 'administrador':
        $adminCtrl->Index();
        break;

    case 'empleado':
        $empleadoCtrl->Index();
        break;

    case 'cliente':
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 2) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        require_once __DIR__ . '/Vistas/cliente.php';
        break;

    // Acciones de Administrador
    case 'admin_usuarios':
        $adminCtrl->gestionarUsuarios();
        break;

    case 'admin_clientes':
        $adminCtrl->gestionarClientes();
        break;

    case 'admin_empleados':
        $adminCtrl->gestionarEmpleados();
        break;

    case 'admin_membresias':
        $adminCtrl->gestionarMembresias();
        break;

    case 'admin_productos':
        $adminCtrl->gestionarProductos();
        break;

    case 'admin_reportes':
        $adminCtrl->verReportes();
        break;

    case 'admin_pdf_reportes':
        $adminCtrl->generarPDFReportes();
        break;

    // Acciones de Empleado
    case 'empleado_clientes':
        $empleadoCtrl->verClientes();
        break;

    case 'empleado_membresias':
        $empleadoCtrl->gestionarMembresias();
        break;

    case 'empleado_productos':
        $empleadoCtrl->verProductos();
        break;

    case 'empleado_reportes':
        $empleadoCtrl->verReportes();
        break;

    // Acciones de Cliente
    case 'cliente_membresia':
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 2) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Mi Membresía</h1>";
        echo '<a href="Index.php?action=cliente">Volver</a>';
        break;

    case 'cliente_perfil':
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 2) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Mi Perfil</h1>";
        echo '<a href="Index.php?action=cliente">Volver</a>';
        break;

    // Salir
    case 'logout':
        session_destroy();
        header("Location: Index.php?action=login");
        exit();

    case 'acceso_denegado':
        echo "<h1>Acceso denegado</h1>";
        echo "<p>No tenes permiso para acceder a esta página.</p>";
        echo '<a href="Index.php?action=login">Volver al inicio</a>';
        break;

    default:
        $authCtrl->mostrarLogin();
        break;
}
?>