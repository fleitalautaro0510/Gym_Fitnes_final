<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../Modelo/funciones_productos.php';

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

function volverCarrito(): void { header('Location: ../carrito.php'); exit; }

if ($accion === 'agregar_producto') {
    $id = (int)($_POST['id_producto'] ?? 0);
    if ($id <= 0) volverCarrito();

    $producto = obtenerProductoPorId($pdo, $id);
    if (!$producto || (int)$producto['stock_actual'] <= 0) {
        header('Location: ../productos.php?error=sin_stock'); exit;
    }

    $clave = 'producto_' . $id;
    $actual = (int)($_SESSION['carrito'][$clave]['cantidad'] ?? 0);
    $cantidad = min($actual + 1, (int)$producto['stock_actual']);

    $_SESSION['carrito'][$clave] = [
        'clave'=>$clave, 'tipo'=>'producto', 'id'=>$id,
        'nombre'=>$producto['nombre'],
        'precio'=>(float)($producto['precio_venta'] ?: $producto['precio']),
        'cantidad'=>$cantidad, 'stock'=>(int)$producto['stock_actual'],
        'imagen'=>resolverImagenProducto($producto)
    ];
    volverCarrito();
}

if ($accion === 'agregar_plan') {
    $id = (int)($_POST['id_membresia'] ?? 0);
    if ($id <= 0) volverCarrito();
    $stmt = $pdo->prepare('SELECT id_membresia, membresia, duracion, precio FROM Membresias WHERE id_membresia = :id');
    $stmt->execute([':id'=>$id]);
    $plan = $stmt->fetch();
    if (!$plan) volverCarrito();

    $clave = 'plan_' . $id;
    $_SESSION['carrito'][$clave] = [
        'clave'=>$clave, 'tipo'=>'plan', 'id'=>(int)$plan['id_membresia'],
        'nombre'=>$plan['membresia'], 'precio'=>(float)$plan['precio'],
        'duracion'=>$plan['duracion'], 'cantidad'=>1, 'stock'=>1
    ];
    volverCarrito();
}

if ($accion === 'actualizar') {
    foreach ($_POST['cantidades'] ?? [] as $clave=>$cantidad) {
        if (!isset($_SESSION['carrito'][$clave])) continue;
        $cantidad = max(0, (int)$cantidad);
        if ($_SESSION['carrito'][$clave]['tipo'] === 'producto') {
            $cantidad = min($cantidad, (int)$_SESSION['carrito'][$clave]['stock']);
        } else $cantidad = min($cantidad, 1);
        if ($cantidad === 0) unset($_SESSION['carrito'][$clave]);
        else $_SESSION['carrito'][$clave]['cantidad'] = $cantidad;
    }
    volverCarrito();
}

if ($accion === 'eliminar') {
    unset($_SESSION['carrito'][$_GET['clave'] ?? '']);
    volverCarrito();
}
if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    volverCarrito();
}
if ($accion === 'confirmar') {
    if (empty($_SESSION['carrito'])) volverCarrito();
    header('Location: ../checkout.php'); exit;
}
volverCarrito();
