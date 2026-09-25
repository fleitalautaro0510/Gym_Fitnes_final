<?php
/**
 * Listado genérico de una entidad, con búsqueda, paginado y acciones
 * de editar / eliminar.
 *
 * Variables esperadas: $cfg, $resultado, $columnas, $campos, $pk,
 *                      $busqueda, $entidades, $flash, $token, $pdo
 */
$tituloPagina  = $cfg['titulo'];
$entidadActiva = $cfg['clave'];
require __DIR__ . '/parciales/header_admin.php';

/** Muestra el valor de una celda de forma legible (resuelve relaciones y fechas). */
function adminMostrarValor(array $campos, string $columna, $valor, array $cfg): string
{
    if ($valor === null || $valor === '') {
        return '<span class="admin-muted">—</span>';
    }

    // Si la columna es una clave foránea, se muestra el nombre y no el ID.
    if (!empty($campos[$columna]['fk']['opciones'][(string)$valor])) {
        return htmlspecialchars($campos[$columna]['fk']['opciones'][(string)$valor]);
    }

    if (in_array($columna, $cfg['password'] ?? [], true)) {
        return '<span class="admin-muted">••••••</span>';
    }

    $tipo = $campos[$columna]['tipo'] ?? 'texto';
    if ($tipo === 'decimal') {
        return '$' . number_format((float)$valor, 2, ',', '.');
    }

    return htmlspecialchars(adminRecortar((string)$valor, 60));
}

$urlBase = 'index.php?action=admin_listar&ent=' . urlencode($cfg['clave']);
?>

<p class="eyebrow"><span class="eyebrow__dash"></span>Gestión</p>

<div class="admin-head">
  <div>
    <h1 class="admin-title"><?= htmlspecialchars($cfg['titulo']) ?></h1>
    <p class="admin-subtitle">
      <?= htmlspecialchars($cfg['desc'] ?? '') ?>
      <?php if (!empty($resultado['total'])): ?>
        <strong><?= (int)$resultado['total'] ?></strong> registro<?= $resultado['total'] == 1 ? '' : 's' ?>.
      <?php endif; ?>
    </p>
  </div>

  <?php if (empty($cfg['sin_crear'])): ?>
    <a class="btn btn--solid" href="index.php?action=admin_nuevo&amp;ent=<?= urlencode($cfg['clave']) ?>">
      + Nuevo <?= htmlspecialchars($cfg['singular']) ?>
    </a>
  <?php endif; ?>
</div>

<?php if ($flash): ?>
  <div class="admin-alert admin-alert--<?= $flash['tipo'] === 'ok' ? 'ok' : 'error' ?>">
    <?= htmlspecialchars($flash['texto']) ?>
  </div>
<?php endif; ?>

<?php if (!empty($resultado['falta_tabla'])): ?>
  <div class="admin-alert admin-alert--error">
    La tabla <code><?= htmlspecialchars($cfg['tabla']) ?></code> no existe en la base de datos
    <code>mydb</code>. Revisá el nombre de la tabla o importá el script SQL del proyecto.
  </div>
<?php elseif ($pk === null): ?>
  <div class="admin-alert admin-alert--error">
    La tabla <code><?= htmlspecialchars($cfg['tabla']) ?></code> no tiene clave primaria definida,
    así que no se pueden editar ni eliminar sus registros de forma segura.
  </div>
<?php else: ?>

<form class="admin-buscador" method="get" action="index.php">
  <input type="hidden" name="action" value="admin_listar">
  <input type="hidden" name="ent" value="<?= htmlspecialchars($cfg['clave']) ?>">
  <input type="search" name="q" placeholder="Buscar en <?= htmlspecialchars(strtolower($cfg['titulo'])) ?>…"
         value="<?= htmlspecialchars($busqueda) ?>">
  <button class="btn btn--outline" type="submit">Buscar</button>
  <?php if ($busqueda !== ''): ?>
    <a class="btn btn--ghost" href="<?= $urlBase ?>">Limpiar</a>
  <?php endif; ?>
</form>

<?php if (!$resultado['filas']): ?>
  <p class="admin-empty">
    <?= $busqueda !== ''
        ? 'No se encontraron resultados para esa búsqueda.'
        : 'Todavía no hay registros cargados en esta sección.' ?>
  </p>
<?php else: ?>

<div class="admin-tabla-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <?php foreach ($columnas as $columna): ?>
          <th><?= htmlspecialchars($campos[$columna]['label'] ?? adminEtiquetaBonita($columna)) ?></th>
        <?php endforeach; ?>
        <th class="admin-table__acciones">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($resultado['filas'] as $fila): ?>
        <tr>
          <?php foreach ($columnas as $columna): ?>
            <td><?= adminMostrarValor($campos, $columna, $fila[$columna] ?? null, $cfg) ?></td>
          <?php endforeach; ?>
          <td class="admin-table__acciones">
            <?php if (empty($cfg['sin_editar'])): ?>
              <a class="btn btn--outline btn--mini"
                 href="index.php?action=admin_editar&amp;ent=<?= urlencode($cfg['clave']) ?>&amp;id=<?= urlencode((string)$fila[$pk]) ?>">
                Editar
              </a>
            <?php endif; ?>

            <form method="post" action="index.php?action=admin_eliminar" class="admin-inline-form"
                  data-confirmar="¿Seguro que querés eliminar este registro? Esta acción no se puede deshacer.">
              <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
              <input type="hidden" name="ent" value="<?= htmlspecialchars($cfg['clave']) ?>">
              <input type="hidden" name="__id" value="<?= htmlspecialchars((string)$fila[$pk]) ?>">
              <button class="btn btn--peligro btn--mini" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php if ($resultado['paginas'] > 1): ?>
  <nav class="admin-paginado">
    <?php for ($i = 1; $i <= $resultado['paginas']; $i++): ?>
      <a class="admin-paginado__link<?= $i === $resultado['pagina'] ? ' is-active' : '' ?>"
         href="<?= $urlBase ?>&amp;pagina=<?= $i ?><?= $busqueda !== '' ? '&amp;q=' . urlencode($busqueda) : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>
  </nav>
<?php endif; ?>

<?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/parciales/footer_admin.php'; ?>
