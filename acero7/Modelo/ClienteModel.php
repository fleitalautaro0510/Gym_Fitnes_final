<?php
class ClienteModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrarCliente($nombre, $apellido, $dni, $altura, $peso, $genero) {
        $sql = "INSERT INTO clientes (Nombre, apellido, DNI, altura, peso, genero)
                VALUES (:nombre, :apellido, :dni, :altura, :peso, :genero)";

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':nombre'   => $nombre,
            ':apellido' => $apellido,
            ':dni'      => $dni,
            ':altura'   => $altura,
            ':peso'     => $peso,
            ':genero'   => $genero,
        ]);

        return $ok ? $this->pdo->lastInsertId() : false;
    }
}
