<?php
class UsuarioModel {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function registrarUsuarioConsumidor($nombre_usuario, $email, $clave, $id_cliente) {
        $clave_hash = password_hash($clave, PASSWORD_BCRYPT);
        $id_rol = 2; // Rol 2: Consumidor

        $stmt = $this->conn->prepare("INSERT INTO usuarios (nombre_usuario, email, clave, FK_id_rol, Fk_id_cliente, Fk_id_empleado) VALUES (?, ?, ?, ?, ?, NULL)");
        
        // Corregido: 'sssii' para 5 parámetros (3 cadenas, 2 enteros)
        $stmt->bind_param("sssii", $nombre_usuario, $email, $clave_hash, $id_rol, $id_cliente);
        
        $exito = $stmt->execute();
        $stmt->close();
        return $exito;
    }

    public function obtenerPorUsuarioOEmail($identificador) {
        $stmt = $this->conn->prepare("SELECT id_usuarios, nombre_usuario, clave, FK_id_rol FROM usuarios WHERE nombre_usuario = ? OR email = ?");
        $stmt->bind_param("ss", $identificador, $identificador);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $user = $resultado->fetch_assoc();
        $stmt->close();
        return $user;
    }
}
?>