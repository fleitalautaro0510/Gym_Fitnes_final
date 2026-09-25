<?php
define('ADMIN_RAIZ', '../');
require_once __DIR__ . '/../Controlador/verificar_admin.php';
require_once __DIR__ . '/../Controlador/conexion.php';
require_once __DIR__ . '/../Modelo/funciones_productos.php';
$categorias = obtenerCategorias($pdo);
$marcas = obtenerMarcas($pdo);
$proveedores = obtenerProveedores($pdo);
$idProducto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$producto = $idProducto ? obtenerProductoPorId($pdo, $idProducto) : null;
$esEdicion = (bool)$producto;
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title><?= $esEdicion?'Editar':'Nuevo' ?> producto | Acero Gym</title><link rel="stylesheet" href="../index.css"></head><body>
<header class="header is-scrolled"><div class="container header__row"><a href="../index.php" class="logo"><span class="logo__mark">A</span><span class="logo__text">ACERO<span class="logo__sub">GYM</span></span></a><a href="productos.php" class="btn btn--ghost">Volver</a></div></header>
<section class="section page-section"><div class="container"><p class="eyebrow"><span class="eyebrow__dash"></span>Tienda</p><h1 class="h2"><?= $esEdicion?'Editar producto':'Cargar producto' ?></h1>
<p>Completá la descripción y características para que se muestren en la ficha del producto en la tienda. Para la imagen podés subirla acá abajo, o bien&mdash;si preferís&mdash; arrastrar directamente un archivo llamado <code><?= $idProducto ?: 'ID_DEL_PRODUCTO' ?>.jpg</code> (o .png/.webp) a la carpeta <code>uploads/productos/</code> desde Visual Studio Code; en ese caso se muestra automáticamente sin usar este formulario.</p>
<form action="../Controlador/guardar_producto.php" method="post" enctype="multipart/form-data" class="checkout-form">
<input type="hidden" name="id_producto" value="<?= $idProducto ?>">
<label>Nombre del producto<input type="text" name="nombre" required value="<?= htmlspecialchars($producto['nombre']??'') ?>"></label>
<label>Descripción corta<textarea name="descripcion" rows="3" placeholder="Ej: Proteína de suero concentrada ideal para la recuperación muscular."><?= htmlspecialchars($producto['descripcion']??'') ?></textarea></label>
<label>Características (una por línea)<textarea name="caracteristicas" rows="5" placeholder="24g de proteína por porción&#10;Sabor chocolate&#10;Rinde 33 porciones"><?= htmlspecialchars($producto['caracteristicas']??'') ?></textarea></label>
<label>Imagen del producto (opcional)<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
  <?php if (!empty($producto['imagen'])): ?><small>Imagen actual: <?= htmlspecialchars($producto['imagen']) ?></small><?php endif; ?>
</label>
<label>Categoría<select name="Fk_id_categoria" required><option value="">Seleccionar</option><?php foreach($categorias as $c): ?><option value="<?= $c['id_categoria'] ?>" <?= ((int)($producto['Fk_id_categoria']??0)===(int)$c['id_categoria'])?'selected':'' ?>><?= htmlspecialchars($c['nombre_categoria']) ?></option><?php endforeach; ?></select></label>
<label>Marca<select name="Fk_id_marca"><option value="">Sin marca</option><?php foreach($marcas as $m): ?><option value="<?= $m['id_Marca'] ?>" <?= ((int)($producto['Fk_id_marca']??0)===(int)$m['id_Marca'])?'selected':'' ?>><?= htmlspecialchars($m['nombre']) ?></option><?php endforeach; ?></select></label>
<label>Proveedor<select name="Fk_id_proveedor"><option value="">Sin proveedor</option><?php foreach($proveedores as $p): ?><option value="<?= $p['id_Proveedor'] ?>" <?= ((int)($producto['Fk_id_proveedor']??0)===(int)$p['id_Proveedor'])?'selected':'' ?>><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; ?></select></label>
<label>Precio de compra<input type="number" name="precio_compra" step="0.01" min="0" value="<?= htmlspecialchars($producto['precio_compra']??'') ?>"></label>
<label>Precio de venta<input type="number" name="precio_venta" step="0.01" min="0" required value="<?= htmlspecialchars($producto['precio_venta']??$producto['precio']??'') ?>"></label>
<label>Stock actual<input type="number" name="stock_actual" min="0" value="<?= htmlspecialchars($producto['stock_actual']??0) ?>"></label>
<label>Stock mínimo<input type="number" name="Stock_min" min="0" value="<?= htmlspecialchars($producto['Stock_min']??0) ?>"></label>
<label>Stock máximo<input type="number" name="stock_mac" min="0" value="<?= htmlspecialchars($producto['stock_mac']??0) ?>"></label>
<button class="btn btn--solid" type="submit"><?= $esEdicion?'Guardar cambios':'Crear producto' ?></button>
</form></div></section></body></html>
