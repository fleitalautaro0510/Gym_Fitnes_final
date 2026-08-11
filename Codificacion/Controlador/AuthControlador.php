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
                $_SESSION['user_id']     = $user['id_usuarios'];
                $_SESSION['user_nombre'] = $user['nombre_usuario'];
                $_SESSION['user_rol']    = $user['FK_id_rol'];

                if ($user['FK_id_rol'] == 1) {
                    header("Location: Index.php?action=admin_dashboard");
                } elseif ($user['FK_id_rol'] == 3) {
                    header("Location: Index.php?action=empleado_dashboard");
                } else {
                    header("Location: Index.php?action=cliente_dashboard");
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