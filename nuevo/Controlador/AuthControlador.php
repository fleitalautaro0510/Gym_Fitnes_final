<?php
require_once __DIR__ . '/../Modelo/UsuarioModel.php';

class AuthControlador {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function mostrarLogin() {
        require_once __DIR__ . '/../Vistas/login.php';
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = new UsuarioModel($this->conn);
            $user = $usuarioModel->obtenerPorUsuarioOEmail($_POST['usuario_o_email']);

            if ($user && password_verify($_POST['clave'], $user['clave'])) {
                $_SESSION['user_id'] = $user['id_usuarios'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['user_rol'] = $user['FK_id_rol'];

                // Redirigir según el rol
                switch ($user['FK_id_rol']) {
                    case 1: // Administrador
                        header("Location: Index.php?action=administrador");
                        break;
                    case 2: // Cliente
                        header("Location: Index.php?action=cliente");
                        break;
                    case 3: // Empleado
                        header("Location: Index.php?action=empleado");
                        break;
                    default:
                        header("Location: Index.php?action=login");
                        break;
                }
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
                require_once __DIR__ . '/../Vistas/login.php';
            }
        }
    }
}
?>