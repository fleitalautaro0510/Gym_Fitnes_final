<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes del Sistema</title>
</head>
<body>
    <h1>Reportes del Sistema</h1>
    <hr>

    <p>
        <a href="Index.php?action=administrador">← Volver al panel</a>
        &nbsp;|&nbsp;
        <a href="Index.php?action=admin_pdf_reportes" target="_blank">
         Exportar a PDF
        </a>
    </p>

    <hr>

    <h2>Resumen General</h2>
    <ul>
        <li><strong>Total Usuarios:</strong> <?php echo $totalUsuarios ?? 0; ?></li>
        <li><strong>Total Clientes:</strong> <?php echo $totalClientes ?? 0; ?></li>
        <li><strong>Total Empleados:</strong> <?php echo $totalEmpleados ?? 0; ?></li>
        <li><strong>Total Membresías:</strong> <?php echo $totalMembresias ?? 0; ?></li>
        <li><strong>Total Productos:</strong> <?php echo $totalProductos ?? 0; ?></li>
    </ul>

    <hr>

    <h2>Reporte de Pagos</h2>
    <ul>
        <li><strong>Total Pagos:</strong> <?php echo $totalPagos ?? 0; ?></li>
        <li><strong>Monto Total:</strong> $<?php echo number_format($montoTotalPagos ?? 0, 2); ?></li>
    </ul>

    <hr>

    <h2>Reporte de Ventas</h2>
    <ul>
        <li><strong>Total Ventas:</strong> <?php echo $totalVentas ?? 0; ?></li>
        <li><strong>Monto Total:</strong> $<?php echo number_format($montoTotalVentas ?? 0, 2); ?></li>
    </ul>

    <hr>

    <h2>Total Ingresos</h2>
    <ul>
        <li>
            <strong>Ingresos Totales:</strong> 
            $<?php echo number_format(($montoTotalPagos ?? 0) + ($montoTotalVentas ?? 0), 2); ?>
        </li>
        <li>
            <strong>Promedio por Pago:</strong> 
            $<?php echo number_format(($montoTotalPagos ?? 0) / max(1, ($totalPagos ?? 0)), 2); ?>
        </li>
        <li>
            <strong>Promedio por Venta:</strong> 
            $<?php echo number_format(($montoTotalVentas ?? 0) / max(1, ($totalVentas ?? 0)), 2); ?>
        </li>
    </ul>

    <hr>
    <p><small>Reporte generado el: <?php echo date('d/m/Y H:i:s'); ?></small></p>
</body>
</html>