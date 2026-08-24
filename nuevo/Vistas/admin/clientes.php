<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
</head>
<body>
    <h1>Gestión de Clientes</h1>
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
                <th>Altura</th>
                <th>Peso</th>
                <th>Género</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr>
                    <td colspan="9">No hay clientes registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?php echo $cliente['id_clientes']; ?></td>
                        <td><?php echo htmlspecialchars($cliente['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['apellido']); ?></td>
                        <td><?php echo $cliente['DNI']; ?></td>
                        <td><?php echo $cliente['altura']; ?> cm</td>
                        <td><?php echo $cliente['peso']; ?> kg</td>
                        <td><?php echo htmlspecialchars($cliente['genero']); ?></td>
                        <td><?php echo htmlspecialchars($cliente['nombre_usuario'] ?? 'Sin usuario'); ?></td>
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