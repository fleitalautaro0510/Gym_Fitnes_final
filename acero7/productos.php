<?php
require_once __DIR__ . '/Controlador/conexion.php';
require_once __DIR__ . '/Modelo/funciones_productos.php';

$filtros = [
    'categoria' => $_GET['categoria'] ?? '',
    'marca' => $_GET['marca'] ?? '',
    'busqueda' => trim($_GET['busqueda'] ?? ''),
    'precio_min' => $_GET['precio_min'] ?? '',
    'precio_max' => $_GET['precio_max'] ?? '',
    'disponible' => 1,
    'orden' => $_GET['orden'] ?? 'nombre_asc',
];

$categorias = obtenerCategorias($pdo);
$marcas = obtenerMarcas($pdo);
$productos = obtenerProductos($pdo, $filtros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tienda | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>
<?php $paginaActiva = 'productos'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<section class="section section--products page-section" id="productos">
  <div class="container">
    <div class="section__head section__head--center">
      <p class="eyebrow"><span class="eyebrow__dash"></span>Tienda Acero</p>
      <h1 class="h2">Todo para tu entrenamiento</h1>
      <p class="section__intro">Indumentaria, calzado y accesorios para acompañar cada entrenamiento.</p>
    </div>

    <form class="shop-filters" method="get">
      <input type="text" name="busqueda" placeholder="Buscar producto..." value="<?= htmlspecialchars($filtros['busqueda']) ?>">
      <select name="categoria"><option value="">Todas las categorías</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat['id_categoria'] ?>" <?= (string)$filtros['categoria']===(string)$cat['id_categoria']?'selected':'' ?>><?= htmlspecialchars($cat['nombre_categoria']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="marca"><option value="">Todas las marcas</option>
        <?php foreach ($marcas as $m): ?>
          <option value="<?= $m['id_Marca'] ?>" <?= (string)$filtros['marca']===(string)$m['id_Marca']?'selected':'' ?>><?= htmlspecialchars($m['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="orden">
        <option value="nombre_asc">Nombre A-Z</option>
        <option value="precio_asc" <?= $filtros['orden']==='precio_asc'?'selected':'' ?>>Precio menor a mayor</option>
        <option value="precio_desc" <?= $filtros['orden']==='precio_desc'?'selected':'' ?>>Precio mayor a menor</option>
        <option value="nuevos" <?= $filtros['orden']==='nuevos'?'selected':'' ?>>Más nuevos</option>
      </select>
      <button class="btn btn--solid" type="submit">Filtrar</button>
    </form>

    <?php if (($_GET['error'] ?? '') === 'sin_stock'): ?>
      <div class="cart-error">Ese producto ya no tiene stock disponible.</div>
    <?php endif; ?>

    <p class="shop-result"><?= count($productos) ?> producto(s) disponible(s)</p>

    <?php if (!$productos): ?>
      <div class="empty-shop"><h3>No hay productos disponibles</h3><p>Probá cambiar los filtros o cargá productos desde el panel.</p></div>
    <?php else: ?>
      <div class="shop-grid">
      <?php foreach ($productos as $producto):
        $imagenUrl = resolverImagenProducto($producto);
        $caracteristicas = array_values(array_filter(array_map('trim', explode("\n", (string)($producto['caracteristicas'] ?? '')))));
      ?>
        <article class="shop-card" tabindex="0"
                 data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                 data-categoria="<?= htmlspecialchars($producto['nombre_categoria'] ?? 'Producto') ?>"
                 data-marca="<?= htmlspecialchars($producto['nombre_marca'] ?? '') ?>"
                 data-precio="<?= htmlspecialchars(number_format((float)$producto['precio_venta'], 0, ',', '.')) ?>"
                 data-stock="<?= (int)$producto['stock_actual'] ?>"
                 data-descripcion="<?= htmlspecialchars($producto['descripcion'] ?? '') ?>"
                 data-caracteristicas="<?= htmlspecialchars(implode('|', $caracteristicas)) ?>"
                 data-imagen="<?= htmlspecialchars($imagenUrl ?? '') ?>"
                 data-id="<?= (int)$producto['id_producto'] ?>">
          <div class="shop-card__image">
            <?php if ($imagenUrl): ?>
              <img src="<?= htmlspecialchars($imagenUrl) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
            <?php else: ?>
              <div class="shop-no-image">ACERO</div>
            <?php endif; ?>
          </div>
          <div class="shop-card__body">
            <span class="shop-card__category"><?= htmlspecialchars($producto['nombre_categoria'] ?? 'Producto') ?></span>
            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
            <?php if (!empty($producto['nombre_marca'])): ?><p class="shop-card__brand"><?= htmlspecialchars($producto['nombre_marca']) ?></p><?php endif; ?>
            <?php if (!empty($producto['descripcion'])): ?><p class="shop-card__desc"><?= htmlspecialchars($producto['descripcion']) ?></p><?php endif; ?>
            <small>Stock disponible: <?= (int)$producto['stock_actual'] ?></small>
            <div class="shop-card__bottom">
              <strong>$<?= number_format((float)$producto['precio_venta'], 0, ',', '.') ?></strong>
              <form action="Controlador/carrito.php" method="post" class="shop-card__form">
                <input type="hidden" name="accion" value="agregar_producto">
                <input type="hidden" name="id_producto" value="<?= (int)$producto['id_producto'] ?>">
                <button class="btn btn--solid" type="submit">Agregar al carrito</button>
              </form>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Modal de detalle de producto -->
<div class="product-modal" id="productModal" hidden>
  <div class="product-modal__overlay" data-close-modal></div>
  <div class="product-modal__box" role="dialog" aria-modal="true" aria-labelledby="productModalTitle">
    <button type="button" class="product-modal__close" data-close-modal aria-label="Cerrar">&times;</button>
    <div class="product-modal__image" id="productModalImage"><div class="shop-no-image">ACERO</div></div>
    <div class="product-modal__info">
      <span class="shop-card__category" id="productModalCategoria"></span>
      <h2 id="productModalTitle"></h2>
      <p class="product-modal__brand" id="productModalMarca"></p>
      <p class="product-modal__desc" id="productModalDesc"></p>
      <ul class="product-modal__features" id="productModalFeatures"></ul>
      <p class="product-modal__stock" id="productModalStock"></p>
      <div class="product-modal__bottom">
        <strong id="productModalPrecio"></strong>
        <form action="Controlador/carrito.php" method="post" id="productModalForm">
          <input type="hidden" name="accion" value="agregar_producto">
          <input type="hidden" name="id_producto" id="productModalId" value="">
          <button class="btn btn--solid" type="submit">Agregar al carrito</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
<script src="productos.js"></script>
</body>
</html>
