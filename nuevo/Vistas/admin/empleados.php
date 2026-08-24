<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados</title>
</head>
<body>
    <h1>Gestión de Empleados</h1>
    <hr>
    <a href="Index.php?action=administrador">← Volver</a>
    <hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>
                <th>Domicilio</th>
                <th>Teléfono</th>
                <th>Sueldo</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($empleados)): ?>
                <tr>
                    <td colspan="9">No hay empleados registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($empleados as $empleado): ?>
                    <tr>
                        <td><?php echo $empleado['id_empleado']; ?></td>
                        <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($empleado['apellido']); ?></td>
                        <td><?php echo $empleado['DNI']; ?></td>
                        <td><?php echo htmlspecialchars($empleado['Domicilio'] ?? 'N/A'); ?></td>
                        <td><?php echo $empleado['telefono'] ?? 'N/A'; ?></td>
                        <td>$<?php echo number_format($empleado['sueldo'] ?? 0, 2); ?></td>
                        <td><?php echo htmlspecialchars($empleado['nombre_usuario'] ?? 'Sin usuario'); ?></td>
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