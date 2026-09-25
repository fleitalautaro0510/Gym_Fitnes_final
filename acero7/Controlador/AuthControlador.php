<?php
require_once __DIR__ . '/../Modelo/UsuarioModel.php';

class AuthControlador {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function mostrarLogin() {
        require __DIR__ . '/../Vistas/login.php';
    }

    public function mostrarInicioCliente() {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_rol'] ?? null) != 2) {
            header("Location: index.php?action=login");
            exit;
        }
        require __DIR__ . '/../Vistas/inicio_cliente.php';
    }

    public function cerrarSesion() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = new UsuarioModel($this->pdo);
            $user = $usuarioModel->obtenerPorUsuarioOEmail($_POST['usuario_o_email']);

            if ($user && password_verify($_POST['clave'], $user['clave'])) {
                $_SESSION['user_id']     = $user['id_usuarios'];
                $_SESSION['user_nombre'] = $user['nombre_usuario'];
                $_SESSION['user_rol']    = $user['FK_id_rol'];

                if ($user['FK_id_rol'] == 1) {
                    header("Location: index.php?action=admin_dashboard");
                } elseif ($user['FK_id_rol'] == 3) {
                    header("Location: index.php?action=empleado_dashboard");
                } else {
                    header("Location: index.php?action=cliente_inicio");
                }
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
                require __DIR__ . '/../Vistas/login.php';
            }
        }
    }
}
