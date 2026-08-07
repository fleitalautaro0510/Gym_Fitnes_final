<?php
class ClienteModel {
    private $conn;

    public function __construct($conexion) {
        $this->conn = $conexion;
    }

    public function registrarCliente($nombre, $apellido, $dni, $altura, $peso, $genero) {
        $stmt = $this->conn->prepare("INSERT INTO clientes (Nombre, apellido, DNI, altura, peso, genero) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiids", $nombre, $apellido, $dni, $altura, $peso, $genero);
        
        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return $id;
        }
        $stmt->close();
        return false;
    }
}
?>