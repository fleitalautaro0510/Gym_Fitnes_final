<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class AdministradorControlador
{
    private $conn;

    public function __construct($conexion = null) {
        $this->conn = $conexion;
    }

    public function Index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $totalUsuarios = $this->contarUsuarios();
        $totalClientes = $this->contarClientes();
        $totalEmpleados = $this->contarEmpleados();
        $totalMembresias = $this->contarMembresias();
        $totalProductos = $this->contarProductos();

        require_once __DIR__ . '/../Vistas/admin/administrador.php';
    }

    public function gestionarUsuarios()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $usuarios = $this->obtenerTodosUsuarios();
        require_once __DIR__ . '/../Vistas/admin/usuarios.php';
    }

    public function gestionarClientes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $clientes = $this->obtenerTodosClientes();
        require_once __DIR__ . '/../Vistas/admin/clientes.php';
    }

    public function gestionarEmpleados()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $empleados = $this->obtenerTodosEmpleados();
        require_once __DIR__ . '/../Vistas/admin/empleados.php';
    }

    public function gestionarMembresias()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $membresias = $this->obtenerTodasMembresias();
        require_once __DIR__ . '/../Vistas/admin/membresias.php';
    }

    public function gestionarProductos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $productos = $this->obtenerTodosProductos();
        require_once __DIR__ . '/../Vistas/admin/productos.php';
    }

    public function verReportes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        $totalUsuarios = $this->contarUsuarios();
        $totalClientes = $this->contarClientes();
        $totalEmpleados = $this->contarEmpleados();
        $totalMembresias = $this->contarMembresias();
        $totalProductos = $this->contarProductos();
        $totalPagos = $this->contarPagos();
        $totalVentas = $this->contarVentas();
        $montoTotalPagos = $this->sumarPagos();
        $montoTotalVentas = $this->sumarVentas();

        require_once __DIR__ . '/../Vistas/admin/reportes.php';
    }

    public function generarPDFReportes()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 1) {
            header("Location: Index.php?action=acceso_denegado");
            exit();
        }

        require_once __DIR__ . '/../fpdf/fpdf.php';

        $totalUsuarios = $this->contarUsuarios();
        $totalClientes = $this->contarClientes();
        $totalEmpleados = $this->contarEmpleados();
        $totalMembresias = $this->contarMembresias();
        $totalProductos = $this->contarProductos();
        $totalPagos = $this->contarPagos();
        $totalVentas = $this->contarVentas();
        $montoTotalPagos = $this->sumarPagos();
        $montoTotalVentas = $this->sumarVentas();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'REPORTE GENERAL DEL SISTEMA', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $pdf->Ln(10);

        // Resumen
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'RESUMEN GENERAL', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(80, 8, 'Total Usuarios:', 0, 0);
        $pdf->Cell(0, 8, $totalUsuarios, 0, 1);
        $pdf->Cell(80, 8, 'Total Clientes:', 0, 0);
        $pdf->Cell(0, 8, $totalClientes, 0, 1);
        $pdf->Cell(80, 8, 'Total Empleados:', 0, 0);
        $pdf->Cell(0, 8, $totalEmpleados, 0, 1);
        $pdf->Cell(80, 8, 'Total Membresias:', 0, 0);
        $pdf->Cell(0, 8, $totalMembresias, 0, 1);
        $pdf->Cell(80, 8, 'Total Productos:', 0, 0);
        $pdf->Cell(0, 8, $totalProductos, 0, 1);
        $pdf->Ln(5);

        // Pagos
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'REPORTE DE PAGOS', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(80, 8, 'Total Pagos:', 0, 0);
        $pdf->Cell(0, 8, $totalPagos, 0, 1);
        $pdf->Cell(80, 8, 'Monto Total:', 0, 0);
        $pdf->Cell(0, 8, '$ ' . number_format($montoTotalPagos, 2), 0, 1);
        $pdf->Ln(5);

        // Ventas
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'REPORTE DE VENTAS', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(80, 8, 'Total Ventas:', 0, 0);
        $pdf->Cell(0, 8, $totalVentas, 0, 1);
        $pdf->Cell(80, 8, 'Monto Total:', 0, 0);
        $pdf->Cell(0, 8, '$ ' . number_format($montoTotalVentas, 2), 0, 1);
        $pdf->Ln(5);

        // Total Ingresos
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'TOTAL INGRESOS', 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 12);
        $totalIngresos = $montoTotalPagos + $montoTotalVentas;
        $pdf->Cell(80, 8, 'Ingresos Totales:', 0, 0);
        $pdf->Cell(0, 8, '$ ' . number_format($totalIngresos, 2), 0, 1);

        $pdf->Output('D', 'reporte_general_' . date('Y-m-d') . '.pdf');
        exit();
    }

    // Métodos para obtener datos
    private function obtenerTodosUsuarios()
    {
        $sql = "SELECT u.*, r.Rol as nombre_rol 
                FROM Usuarios u 
                LEFT JOIN Roles r ON u.FK_id_rol = r.id_rol 
                ORDER BY u.id_usuarios DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function obtenerTodosClientes()
    {
        $sql = "SELECT c.*, u.nombre_usuario, u.email 
                FROM Clientes c 
                LEFT JOIN Usuarios u ON c.id_clientes = u.Fk_id_cliente 
                ORDER BY c.id_clientes DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function obtenerTodosEmpleados()
    {
        $sql = "SELECT e.*, u.nombre_usuario, u.email 
                FROM Empleados e 
                LEFT JOIN Usuarios u ON e.id_empleado = u.Fk_id_empleado 
                ORDER BY e.id_empleado DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function obtenerTodasMembresias()
    {
        $sql = "SELECT m.*, c.clase, cl.Nombre as cliente_nombre, cl.apellido as cliente_apellido 
                FROM Membresias m 
                LEFT JOIN Clases c ON m.Fk_id_clase = c.id_clase
                LEFT JOIN Clientes cl ON m.id_membresia = cl.id_clientes
                ORDER BY m.id_membresia DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function obtenerTodosProductos()
    {
        $sql = "SELECT p.*, cat.nombre_categoria, m.nombre as marca_nombre, prov.nombre as proveedor_nombre
                FROM Productos p 
                LEFT JOIN Categoria cat ON p.Fk_id_categoria = cat.id_categoria
                LEFT JOIN Marca m ON p.Fk_id_marca = m.id_Marca
                LEFT JOIN Proveedor prov ON p.Fk_id_proveedor = prov.id_Proveedor
                ORDER BY p.id_producto DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function contarUsuarios()
    {
        $sql = "SELECT COUNT(*) as total FROM Usuarios";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarClientes()
    {
        $sql = "SELECT COUNT(*) as total FROM Clientes";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarEmpleados()
    {
        $sql = "SELECT COUNT(*) as total FROM Empleados";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarMembresias()
    {
        $sql = "SELECT COUNT(*) as total FROM Membresias";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarProductos()
    {
        $sql = "SELECT COUNT(*) as total FROM Productos";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarPagos()
    {
        $sql = "SELECT COUNT(*) as total FROM Pagos";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function contarVentas()
    {
        $sql = "SELECT COUNT(*) as total FROM ventas_productos";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    private function sumarPagos()
    {
        $sql = "SELECT SUM(Monto) as total FROM Pagos";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    private function sumarVentas()
    {
        $sql = "SELECT SUM(total) as total FROM Pagos_productos";
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
?>