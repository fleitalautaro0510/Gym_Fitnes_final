<?php
define('ADMIN_RAIZ', '../');
require_once __DIR__ . '/verificar_admin.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../Modelo/funciones_productos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Vistas/productos.php');
    exit;
}

$idProducto   = !empty($_POST['id_producto']) ? (int)$_POST['id_producto'] : null;
$nombre       = trim($_POST['nombre'] ?? '');
$descripcion  = trim($_POST['descripcion'] ?? '');
$caracteristicas = trim($_POST['caracteristicas'] ?? '');
$idCategoria  = !empty($_POST['Fk_id_categoria']) ? (int)$_POST['Fk_id_categoria'] : null;
$idMarca      = !empty($_POST['Fk_id_marca']) ? (int)$_POST['Fk_id_marca'] : null;
$idProveedor  = !empty($_POST['Fk_id_proveedor']) ? (int)$_POST['Fk_id_proveedor'] : null;
$precioCompra = ($_POST['precio_compra'] ?? '') !== '' ? (float)$_POST['precio_compra'] : null;
$precioVenta  = (float)($_POST['precio_venta'] ?? 0);
$stockActual  = (int)($_POST['stock_actual'] ?? 0);
$stockMin     = (int)($_POST['Stock_min'] ?? 0);
$stockMax     = (int)($_POST['stock_mac'] ?? 0);

$errores = [];
if ($nombre === '') $errores[] = 'El nombre del producto es obligatorio.';
if (!$idCategoria) $errores[] = 'Debes seleccionar una categoría.';
if ($precioVenta <= 0) $errores[] = 'El precio de venta debe ser mayor a 0.';
if ($stockActual < 0 || $stockMin < 0 || $stockMax < 0) $errores[] = 'El stock no puede ser negativo.';

if ($stockMax > 0 && $stockMin > $stockMax) {
    $errores[] = 'El stock mínimo no puede superar al stock máximo.';
}

if ($errores) {
    echo '<p>Se encontraron errores:</p><ul>';
    foreach ($errores as $err) echo '<li>' . htmlspecialchars($err) . '</li>';
    echo '</ul><p><a href="javascript:history.back()">Volver</a></p>';
    exit;
}

try {
    if ($idProducto) {
        $sql = "UPDATE Productos SET
                    nombre = :nombre,
                    descripcion = :descripcion,
                    caracteristicas = :caracteristicas,
                    Fk_id_categoria = :categoria,
                    Fk_id_marca = :marca,
                    Fk_id_proveedor = :proveedor,
                    precio = :precio,
                    precio_compra = :precio_compra,
                    precio_venta = :precio_venta,
                    stock_actual = :stock_actual,
                    Stock_min = :stock_min,
                    stock_mac = :stock_max
                WHERE id_producto = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'=>$nombre, ':descripcion'=>$descripcion, ':caracteristicas'=>$caracteristicas,
            ':categoria'=>$idCategoria, ':marca'=>$idMarca,
            ':proveedor'=>$idProveedor, ':precio'=>$precioVenta,
            ':precio_compra'=>$precioCompra, ':precio_venta'=>$precioVenta,
            ':stock_actual'=>$stockActual, ':stock_min'=>$stockMin,
            ':stock_max'=>$stockMax, ':id'=>$idProducto
        ]);
    } else {
        $sql = "INSERT INTO Productos
                    (nombre, descripcion, caracteristicas, Fk_id_categoria, Fk_id_marca, Fk_id_proveedor,
                     precio, precio_compra, precio_venta, stock_actual, Stock_min, stock_mac)
                VALUES
                    (:nombre, :descripcion, :caracteristicas, :categoria, :marca, :proveedor,
                     :precio, :precio_compra, :precio_venta, :stock_actual, :stock_min, :stock_max)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'=>$nombre, ':descripcion'=>$descripcion, ':caracteristicas'=>$caracteristicas,
            ':categoria'=>$idCategoria, ':marca'=>$idMarca,
            ':proveedor'=>$idProveedor, ':precio'=>$precioVenta,
            ':precio_compra'=>$precioCompra, ':precio_venta'=>$precioVenta,
            ':stock_actual'=>$stockActual, ':stock_min'=>$stockMin,
            ':stock_max'=>$stockMax
        ]);
        $idProducto = (int)$pdo->lastInsertId();
    }

    // Si se subió una imagen, la guardamos como "{id_producto}.{extension}"
    // en uploads/productos/. Con ese nombre, resolverImagenProducto() la
    // detecta automáticamente (es la misma convención que usar VS Code
    // para arrastrar el archivo a mano).
    if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $extensionesPermitidas = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (isset($extensionesPermitidas[$ext])) {
            $carpetaDestino = __DIR__ . '/../uploads/productos/';
            if (!is_dir($carpetaDestino)) mkdir($carpetaDestino, 0775, true);
            $nombreArchivo = $idProducto . '.' . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $carpetaDestino . $nombreArchivo)) {
                $pdo->prepare('UPDATE Productos SET imagen = :imagen WHERE id_producto = :id')
                    ->execute([':imagen'=>$nombreArchivo, ':id'=>$idProducto]);
            }
        }
    }

    header('Location: ../Vistas/productos.php?guardado=1');
    exit;
} catch (PDOException $e) {
    echo '<p>Ocurrió un error al guardar el producto: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="javascript:history.back()">Volver</a></p>';
}
