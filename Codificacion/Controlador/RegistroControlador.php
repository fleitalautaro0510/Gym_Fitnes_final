<?php
// Agrega '/../' para subir a la carpeta raíz 'Codificacion' y luego entrar a 'Modelo'
require_once __DIR__ . '/../Modelo/ClienteModel.php';
require_once __DIR__ . '/../Modelo/UsuarioModel.php';

class RegistroControlador {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function mostrarPaso1() {
        require_once __DIR__ . '/../Vistas/registro_paso1.php';
    }

    public function guardarPaso1() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteModel = new ClienteModel($this->conn);
            $id_cliente = $clienteModel->registrarCliente(
                $_POST['nombre'], $_POST['apellido'], $_POST['dni'], 
                $_POST['altura'], $_POST['peso'], $_POST['genero']
            );

            if ($id_cliente) {
                $_SESSION['temp_id_cliente'] = $id_cliente;
                header("Location: Index.php?action=paso2");
                exit;
            }
        }
    }

    public function mostrarPaso2() {
        if (!isset($_SESSION['temp_id_cliente'])) {
            header("Location: Index.php?action=registro");
            exit;
        }
        require_once __DIR__ . '/../Vistas/registro_paso2.php';
    }

    public function guardarPaso2() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['temp_id_cliente'])) {
            $usuarioModel = new UsuarioModel($this->conn);
            $exito = $usuarioModel->registrarUsuarioConsumidor(
                $_POST['nombre_usuario'], 
                $_POST['email'], 
                $_POST['clave'], 
                $_SESSION['temp_id_cliente']
            );

            if ($exito) {
                unset($_SESSION['temp_id_cliente']);
                header("Location: Index.php?action=login&registrado=1");
                exit;
            }
        }
    }
}
?>