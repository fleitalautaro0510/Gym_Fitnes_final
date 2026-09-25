<?php
/**
 * Funciones de productos adaptadas a la estructura REAL de la base mydb.
 *
 * IMPORTANTE: se agregaron las columnas `descripcion`, `caracteristicas` e
 * `imagen` a la tabla Productos (ver Controlador/migrar_productos.php, que
 * las crea automáticamente si todavía no existen).
 */
function obtenerProductos(PDO $pdo, array $filtros = []): array
{
    $sql = "SELECT
                p.id_producto,
                p.nombre,
                p.Fk_id_categoria,
                p.Fk_id_marca,
                p.Fk_id_proveedor,
                p.precio,
                p.precio_compra,
                p.precio_venta,
                p.Stock_min,
                p.stock_actual,
                p.stock_mac,
                p.descripcion,
                p.caracteristicas,
                p.imagen,
                c.nombre_categoria,
                m.nombre AS nombre_marca
            FROM Productos p
            LEFT JOIN Categoria c ON c.id_categoria = p.Fk_id_categoria
            LEFT JOIN Marca m ON m.id_Marca = p.Fk_id_marca
            WHERE 1=1";

    $params = [];
    if (!empty($filtros['categoria'])) {
        $sql .= " AND p.Fk_id_categoria = :categoria";
        $params[':categoria'] = (int)$filtros['categoria'];
    }
    if (!empty($filtros['marca'])) {
        $sql .= " AND p.Fk_id_marca = :marca";
        $params[':marca'] = (int)$filtros['marca'];
    }
    if (!empty($filtros['busqueda'])) {
        $sql .= " AND p.nombre LIKE :busqueda";
        $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
    }
    if (isset($filtros['precio_min']) && $filtros['precio_min'] !== '') {
        $sql .= " AND p.precio_venta >= :precio_min";
        $params[':precio_min'] = (float)$filtros['precio_min'];
    }
    if (isset($filtros['precio_max']) && $filtros['precio_max'] !== '') {
        $sql .= " AND p.precio_venta <= :precio_max";
        $params[':precio_max'] = (float)$filtros['precio_max'];
    }
    if (!empty($filtros['disponible'])) {
        $sql .= " AND p.stock_actual > 0";
    }

    switch ($filtros['orden'] ?? '') {
        case 'nombre_desc': $sql .= " ORDER BY p.nombre DESC"; break;
        case 'precio_asc':  $sql .= " ORDER BY p.precio_venta ASC"; break;
        case 'precio_desc': $sql .= " ORDER BY p.precio_venta DESC"; break;
        default:            $sql .= " ORDER BY p.nombre ASC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function obtenerCategorias(PDO $pdo): array
{
    return $pdo->query("SELECT id_categoria, nombre_categoria FROM Categoria ORDER BY nombre_categoria ASC")->fetchAll();
}

function obtenerMarcas(PDO $pdo): array
{
    return $pdo->query("SELECT id_Marca, nombre FROM Marca ORDER BY nombre ASC")->fetchAll();
}

function obtenerProveedores(PDO $pdo): array
{
    return $pdo->query("SELECT id_Proveedor, nombre FROM Proveedor ORDER BY nombre ASC")->fetchAll();
}

function obtenerProductoPorId(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM Productos WHERE id_producto = :id");
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch();
    return $producto ?: null;
}

/**
 * Resuelve la imagen a mostrar para un producto.
 *
 * Funciona de dos formas (para que sea muy fácil poner imágenes desde VS Code):
 *
 *  1) Si el producto tiene algo cargado en la columna `imagen` (por ejemplo
 *     porque se subió un archivo desde el panel de administración), se usa
 *     ese archivo tal cual.
 *
 *  2) Si no, se busca automáticamente un archivo llamado igual que el
 *     ID del producto dentro de /uploads/productos, probando las
 *     extensiones más comunes (jpg, jpeg, png, webp). Es decir: para que el
 *     producto con id_producto = 15 tenga imagen, alcanza con arrastrar un
 *     archivo "15.jpg" (o .png / .webp) a la carpeta
 *     acero3/uploads/productos/ directamente desde el explorador de
 *     Visual Studio Code. No hace falta tocar la base de datos.
 *
 * Si no se encuentra ninguna imagen, devuelve null y la vista debe mostrar
 * el placeholder "ACERO".
 */
function resolverImagenProducto(array $producto): ?string
{
    $carpeta = __DIR__ . '/../uploads/productos/';
    $rutaPublica = 'uploads/productos/';

    if (!empty($producto['imagen']) && is_file($carpeta . $producto['imagen'])) {
        return $rutaPublica . $producto['imagen'];
    }

    $id = (int)($producto['id_producto'] ?? 0);
    if ($id > 0) {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            if (is_file($carpeta . $id . '.' . $ext)) {
                return $rutaPublica . $id . '.' . $ext;
            }
        }
    }

    return null;
}
