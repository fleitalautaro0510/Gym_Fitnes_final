<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
</head>
<body>
    <h1>Gestión de Productos</h1>
    <hr>
    <a href="Index.php?action=administrador">← Volver</a>
    <hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Stock</th>
                <th>Precio Venta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($productos)): ?>
                <tr>
                    <td colspan="7">No hay productos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($productos as $producto): 
                    $stock = $producto['stock_actual'] ?? 0;
                    $estadoStock = 'Alto';
                    if ($stock <= 5) { $estadoStock = 'BAJO'; }
                    elseif ($stock <= 15) { $estadoStock = 'Medio'; }
                ?>
                    <tr>
                        <td><?php echo $producto['id_producto']; ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                        <td><?php echo htmlspecialchars($producto['marca_nombre'] ?? 'Sin marca'); ?></td>
                        <td>
                            <?php echo $stock; ?> 
                            (<?php echo $estadoStock; ?>)
                        </td>
                        <td>$<?php echo number_format($producto['precio_venta'] ?? 0, 2); ?></td>
                        <td>
                            <a href="#">Ver</a> |
                            <a href="#">Editar</a> |
                            <a href="#">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>