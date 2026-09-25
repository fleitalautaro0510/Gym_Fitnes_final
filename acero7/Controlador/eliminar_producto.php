<?php
define('ADMIN_RAIZ', '../');
require_once __DIR__ . '/verificar_admin.php';
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Vistas/productos.php');
    exit;
}

$id = (int)($_POST['id_producto'] ?? 0);
if ($id <= 0) {
    header('Location: ../Vistas/productos.php?error=id_invalido');
    exit;
}

try {
    // La tabla Productos no tiene baja lógica/activo. Se elimina el registro.
    $stmt = $pdo->prepare('DELETE FROM Productos WHERE id_producto = :id');
    $stmt->execute([':id' => $id]);
    header('Location: ../Vistas/productos.php?eliminado=1');
    exit;
} catch (PDOException $e) {
    echo '<p>No se puede eliminar el producto. Si ya fue utilizado en una venta, primero revisá sus relaciones.</p>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
