<?php
/**
 * Formulario genérico de alta / edición.
 * Los campos se arman solos según la estructura real de la tabla.
 *
 * Variables esperadas: $cfg, $campos, $registro, $errores, $esEdicion,
 *                      $entidades, $token, $pdo
 */
$tituloPagina  = ($esEdicion ? 'Editar ' : 'Nuevo ') . $cfg['singular'];
$entidadActiva = $cfg['clave'];
require __DIR__ . '/parciales/header_admin.php';

$pk     = adminClavePrimaria($pdo, $cfg);
$idReg  = $esEdicion ? ($registro[$pk] ?? '') : '';
$volver = 'index.php?action=admin_listar&ent=' . urlencode($cfg['clave']);
?>

<p class="eyebrow"><span class="eyebrow__dash"></span><?= htmlspecialchars($cfg['titulo']) ?></p>

<div class="admin-head">
  <div>
    <h1 class="admin-title">
      <?= $esEdicion ? 'Editar' : 'Nuevo' ?> <em><?= htmlspecialchars($cfg['singular']) ?></em>
    </h1>
    <p class="admin-subtitle">
      <?= $esEdicion
          ? 'Modificá los datos y guardá los cambios. Registro #' . htmlspecialchars((string)$idReg) . '.'
          : 'Completá los datos para dar de alta un nuevo registro.' ?>
    </p>
  </div>
  <a class="btn btn--outline" href="<?= $volver ?>">Volver al listado</a>
</div>

<?php if (!empty($errores)): ?>
  <div class="admin-alert admin-alert--error">
    <strong>Revisá lo siguiente:</strong>
    <ul>
      <?php foreach ($errores as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form class="admin-form" method="post" action="index.php?action=admin_guardar" enctype="multipart/form-data">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <input type="hidden" name="ent" value="<?= htmlspecialchars($cfg['clave']) ?>">
  <input type="hidden" name="__id" value="<?= htmlspecialchars((string)$idReg) ?>">

  <div class="admin-form__grid">
  <?php foreach ($campos as $nombre => $campo):
      $valor  = $registro[$nombre] ?? '';
      $ancho  = in_array($campo['tipo'], ['texto_largo'], true) ? ' admin-field--ancho' : '';
      $req    = $campo['requerido'] ? ' required' : '';
  ?>
    <div class="admin-field<?= $ancho ?>">
      <label for="campo_<?= htmlspecialchars($nombre) ?>">
        <?= htmlspecialchars($campo['label']) ?>
        <?php if ($campo['requerido']): ?><span class="admin-req">*</span><?php endif; ?>
      </label>

      <?php if ($campo['fk']): ?>
        <select id="campo_<?= htmlspecialchars($nombre) ?>" name="<?= htmlspecialchars($nombre) ?>"<?= $req ?>>
          <option value="">— Sin asignar —</option>
          <?php foreach ($campo['fk']['opciones'] as $idOpcion => $texto): ?>
            <option value="<?= htmlspecialchars((string)$idOpcion) ?>"
              <?= (string)$valor === (string)$idOpcion ? 'selected' : '' ?>>
              <?= htmlspecialchars($texto) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <small>Relacionado con la tabla <?= htmlspecialchars($campo['fk']['tabla']) ?>.</small>

      <?php elseif ($campo['tipo'] === 'enum'): ?>
        <select id="campo_<?= htmlspecialchars($nombre) ?>" name="<?= htmlspecialchars($nombre) ?>"<?= $req ?>>
          <option value="">— Elegir —</option>
          <?php foreach ($campo['opciones'] as $opcion): ?>
            <option value="<?= htmlspecialchars($opcion) ?>" <?= (string)$valor === $opcion ? 'selected' : '' ?>>
              <?= htmlspecialchars($opcion) ?>
            </option>
          <?php endforeach; ?>
        </select>

      <?php elseif ($campo['tipo'] === 'texto_largo'): ?>
        <textarea id="campo_<?= htmlspecialchars($nombre) ?>" name="<?= htmlspecialchars($nombre) ?>"
                  rows="4"<?= $req ?>><?= htmlspecialchars((string)$valor) ?></textarea>

      <?php elseif ($campo['tipo'] === 'archivo'): ?>
        <input type="file" id="campo_<?= htmlspecialchars($nombre) ?>" name="__imagen"
               accept="image/jpeg,image/png,image/webp">
        <?php if (!empty($valor)): ?>
          <small>Imagen actual: <?= htmlspecialchars((string)$valor) ?></small>
        <?php else: ?>
          <small>JPG, PNG o WEBP (hasta 3 MB). Se guarda como <code><?= htmlspecialchars((string)($idReg ?: 'ID')) ?>.jpg</code> en uploads/productos/.</small>
        <?php endif; ?>

      <?php elseif ($campo['password']): ?>
        <input type="password" id="campo_<?= htmlspecialchars($nombre) ?>" name="<?= htmlspecialchars($nombre) ?>"
               autocomplete="new-password" <?= $esEdicion ? '' : 'required' ?>>
        <small><?= $esEdicion
            ? 'Dejalo vacío para mantener la contraseña actual.'
            : 'Mínimo 4 caracteres. Se guarda encriptada.' ?></small>

      <?php else: ?>
        <?php
          $tipoInput = 'text';
          $extra     = '';
          switch ($campo['tipo']) {
              case 'entero':    $tipoInput = 'number'; break;
              case 'decimal':   $tipoInput = 'number'; $extra = ' step="0.01"'; break;
              case 'fecha':     $tipoInput = 'date'; break;
              case 'fechahora': $tipoInput = 'datetime-local'; $valor = str_replace(' ', 'T', (string)$valor); break;
              case 'hora':      $tipoInput = 'time'; break;
          }
          if (strcasecmp($nombre, 'email') === 0) { $tipoInput = 'email'; }
          if ($campo['maxlargo'] && $tipoInput === 'text') { $extra .= ' maxlength="' . $campo['maxlargo'] . '"'; }
        ?>
        <input type="<?= $tipoInput ?>" id="campo_<?= htmlspecialchars($nombre) ?>"
               name="<?= htmlspecialchars($nombre) ?>"
               value="<?= htmlspecialchars((string)$valor) ?>"<?= $extra . $req ?>>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  </div>

  <div class="admin-form__acciones">
    <button class="btn btn--solid" type="submit">
      <?= $esEdicion ? 'Guardar cambios' : 'Crear ' . htmlspecialchars($cfg['singular']) ?>
    </button>
    <a class="btn btn--ghost" href="<?= $volver ?>">Cancelar</a>
  </div>
</form>

<?php if ($esEdicion && empty($cfg['sin_editar'])): ?>
  <section class="admin-panel admin-panel--peligro">
    <h2 class="admin-panel__title">Eliminar registro</h2>
    <p>Esta acción borra el registro de forma permanente. Si está relacionado con otras tablas
       (por ejemplo, un producto que ya fue vendido), la base de datos no va a permitir borrarlo.</p>
    <form method="post" action="index.php?action=admin_eliminar"
          data-confirmar="¿Eliminar definitivamente este registro?">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="hidden" name="ent" value="<?= htmlspecialchars($cfg['clave']) ?>">
      <input type="hidden" name="__id" value="<?= htmlspecialchars((string)$idReg) ?>">
      <button class="btn btn--peligro" type="submit">Eliminar <?= htmlspecialchars($cfg['singular']) ?></button>
    </form>
  </section>
<?php endif; ?>

<?php require __DIR__ . '/parciales/footer_admin.php'; ?>
