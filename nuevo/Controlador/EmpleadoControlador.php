<?php

class EmpleadoControlador
{
    public function Index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        require_once __DIR__ . '/../Vistas/empleado.php';
    }

    public function verClientes()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Clientes - Empleado</h1>";
        echo "<p>Aquí puedes ver la información de los clientes.</p>";
        echo '<a href="Index.php?action=empleado">Volver al panel</a>';
    }

    public function gestionarMembresias()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Gestión de Membresías - Empleado</h1>";
        echo "<p>Aquí puedes gestionar las membresías de los clientes.</p>";
        echo '<a href="Index.php?action=empleado">Volver al panel</a>';
    }

    public function verProductos()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Productos - Empleado</h1>";
        echo "<p>Aquí puedes ver los productos disponibles.</p>";
        echo '<a href="Index.php?action=empleado">Volver al panel</a>';
    }

    public function verReportes()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 3) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }
        echo "<h1>Reportes - Empleado</h1>";
        echo "<p>Aquí puedes generar y ver reportes.</p>";
        echo '<a href="Index.php?action=empleado">Volver al panel</a>';
    }
}
?>