<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
</head>
<body>
    <h1>Panel de Administrador</h1>
    <hr>

    <p>
        Bienvenido: 
        <strong><?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></strong>
    </p>

    <hr>

    <h2>Estadísticas</h2>
    <ul>
        <li>Usuarios: <?php echo $totalUsuarios ?? 0; ?></li>
        <li>Clientes: <?php echo $totalClientes ?? 0; ?></li>
        <li>Empleados: <?php echo $totalEmpleados ?? 0; ?></li>
        <li>Membresías: <?php echo $totalMembresias ?? 0; ?></li>
        <li>Productos: <?php echo $totalProductos ?? 0; ?></li>
    </ul>

    <hr>

    <h2>Menú de Administración</h2>

    <h3>Usuarios</h3>
    <a href="Index.php?action=admin_usuarios">Gestionar Usuarios</a>
    <br><br>

    <h3>Clientes</h3>
    <a href="Index.php?action=admin_clientes">Gestionar Clientes</a>
    <br><br>

    <h3>Empleados</h3>
    <a href="Index.php?action=admin_empleados">Gestionar Empleados</a>
    <br><br>

    <h3>Membresías</h3>
    <a href="Index.php?action=admin_membresias">Gestionar Membresías</a>
    <br><br>

    <h3>Productos</h3>
    <a href="Index.php?action=admin_productos">Gestionar Productos</a>
    <br><br>

    <h3>Reportes</h3>
    <a href="Index.php?action=admin_reportes">Ver Reportes</a>
    <br><br>

    <hr>

    <h3>Sesión</h3>
    <a href="Index.php?action=logout">Cerrar Sesión</a>
</body>
</html>