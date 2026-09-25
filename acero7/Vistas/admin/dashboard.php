<?php
/**
 * Tablero principal del administrador.
 * Variables esperadas: $stats, $entidades, $stockBajo, $ultimasVentas, $flash, $pdo
 */
$tituloPagina  = 'Panel de administración';
$entidadActiva = '';
require __DIR__ . '/parciales/header_admin.php';

$tarjetas = [
    ['titulo' => 'Usuarios',   'valor' => $stats['usuarios'],   'ent' => 'usuarios'],
    ['titulo' => 'Clientes',   'valor' => $stats['clientes'],   'ent' => 'clientes'],
    ['titulo' => 'Empleados',  'valor' => $stats['empleados'],  'ent' => 'empleados'],
    ['titulo' => 'Productos',  'valor' => $stats['productos'],  'ent' => 'productos'],
    ['titulo' => 'Membresías', 'valor' => $stats['membresias'], 'ent' => 'membresias'],
    ['titulo' => 'Ventas',     'valor' => $stats['ventas'],     'ent' => 'ventas'],
];
?>

<p class="eyebrow"><span class="eyebrow__dash"></span>Administración</p>
<h1 class="admin-title">Hola, <em><?= htmlspecialchars($_SESSION['user_nombre'] ?? 'admin') ?></em></h1>
<p class="admin-subtitle">Desde acá podés dar de alta, editar y eliminar la información de todo el sistema.</p>

<?php if ($flash): ?>
  <div class="admin-alert admin-alert--<?= $flash['tipo'] === 'ok' ? 'ok' : 'error' ?>">
    <?= htmlspecialchars($flash['texto']) ?>
  </div>
<?php endif; ?>

<div class="admin-cards">
  <?php foreach ($tarjetas as $tarjeta): ?>
    <a class="admin-card" href="index.php?action=admin_listar&amp;ent=<?= urlencode($tarjeta['ent']) ?>">
      <span class="admin-card__label"><?= htmlspecialchars($tarjeta['titulo']) ?></span>
      <span class="admin-card__value">
        <?= $tarjeta['valor'] === null ? '—' : number_format((int)$tarjeta['valor'], 0, ',', '.') ?>
      </span>
      <span class="admin-card__link">Gestionar →</span>
    </a>
  <?php endforeach; ?>

  <div class="admin-card admin-card--dest">
    <span class="admin-card__label">Ingresos totales</span>
    <span class="admin-card__value">$<?= number_format((float)$stats['ingresos'], 2, ',', '.') ?></span>
    <span class="admin-card__link">Pagos + ventas de la tienda</span>
  </div>
</div>

<div class="admin-grid2">

  <section class="admin-panel">
    <h2 class="admin-panel__title">Stock bajo</h2>
    <?php if ($stockBajo): ?>
      <table class="admin-table">
        <thead><tr><th>Producto</th><th>Stock</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($stockBajo as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><span class="admin-badge admin-badge--alerta"><?= (int)$p['stock_actual'] ?></span></td>
            <td class="admin-table__acciones">
              <a class="btn btn--outline btn--mini"
                 href="index.php?action=admin_editar&amp;ent=productos&amp;id=<?= (int)$p['id_producto'] ?>">Editar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="admin-empty">No hay productos con stock por debajo del mínimo.</p>
    <?php endif; ?>
  </section>

  <section class="admin-panel">
    <h2 class="admin-panel__title">Últimas ventas</h2>
    <?php if ($ultimasVentas): ?>
      <table class="admin-table">
        <thead><tr><th>#</th><th>Cliente</th><th>Fecha</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($ultimasVentas as $v): ?>
          <tr>
            <td>#<?= (int)$v['id_venta_producto'] ?></td>
            <td><?= htmlspecialchars($v['nombre_usuario'] ?? 'Sin usuario') ?></td>
            <td><?= htmlspecialchars((string)$v['fecha_venta']) ?></td>
            <td>$<?= number_format((float)$v['total'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="admin-empty">Todavía no se registraron ventas.</p>
    <?php endif; ?>
  </section>

</div>

<section class="admin-panel">
  <h2 class="admin-panel__title">Accesos rápidos</h2>
  <div class="admin-quick">
    <?php foreach ($entidades as $clave => $ent): ?>
      <div class="admin-quick__item">
        <h3><?= htmlspecialchars($ent['titulo']) ?></h3>
        <p><?= htmlspecialchars($ent['desc'] ?? '') ?></p>
        <div class="admin-quick__botones">
          <a class="btn btn--outline btn--mini"
             href="index.php?action=admin_listar&amp;ent=<?= urlencode($clave) ?>">Ver todos</a>
          <?php if (empty($ent['sin_crear'])): ?>
            <a class="btn btn--solid btn--mini"
               href="index.php?action=admin_nuevo&amp;ent=<?= urlencode($clave) ?>">+ Nuevo</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/parciales/footer_admin.php'; ?>
