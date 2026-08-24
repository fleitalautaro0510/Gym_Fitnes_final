<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Membresías</title>
</head>
<body>
    <h1>Gestión de Membresías</h1>
    <hr>
    <a href="Index.php?action=administrador">← Volver</a>
    <hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Membresía</th>
                <th>Duración</th>
                <th>Precio</th>
                <th>Clase</th>
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($membresias)): ?>
                <tr>
                    <td colspan="7">No hay membresías registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($membresias as $membresia): ?>
                    <tr>
                        <td><?php echo $membresia['id_membresia']; ?></td>
                        <td><?php echo htmlspecialchars($membresia['membresia']); ?></td>
                        <td><?php echo htmlspecialchars($membresia['duracion']); ?></td>
                        <td>$<?php echo number_format($membresia['precio'] ?? 0, 2); ?></td>
                        <td><?php echo htmlspecialchars($membresia['clase'] ?? 'Sin clase'); ?></td>
                        <td>
                            <?php 
                            echo htmlspecialchars($membresia['cliente_nombre'] ?? '') . ' ' . 
                                 htmlspecialchars($membresia['cliente_apellido'] ?? ''); 
                            ?>
                        </td>
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