<?php
class UsuarioModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarUsuarioConsumidor($nombre_usuario, $email, $clave, $id_cliente) {
        $clave_hash = password_hash($clave, PASSWORD_BCRYPT);
        $id_rol = 2; // Rol 2: Consumidor / Socio

        $sql = "INSERT INTO usuarios (nombre_usuario, email, clave, FK_id_rol, Fk_id_cliente, Fk_id_empleado)
                VALUES (:nombre_usuario, :email, :clave, :id_rol, :id_cliente, NULL)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre_usuario' => $nombre_usuario,
            ':email'          => $email,
            ':clave'          => $clave_hash,
            ':id_rol'         => $id_rol,
            ':id_cliente'     => $id_cliente,
        ]);
    }

    // Valida credenciales: busca por usuario o email
    public function obtenerPorUsuarioOEmail($identificador) {
        $sql = "SELECT id_usuarios, nombre_usuario, clave, FK_id_rol
                FROM usuarios
                WHERE nombre_usuario = :identificador OR email = :identificador";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':identificador' => $identificador]);
        $user = $stmt->fetch();

        return $user ?: null;
    }
}
