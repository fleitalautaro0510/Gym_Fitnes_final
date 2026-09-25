<?php
session_start();
$carrito = $_SESSION['carrito'] ?? [];
$total = 0; $cantidadTotal = 0;
foreach ($carrito as $item) { $total += $item['precio'] * $item['cantidad']; $cantidadTotal += $item['cantidad']; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carrito | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>
<?php $paginaActiva = ''; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<section class="section page-section"><div class="container cart-page">
<div class="section__head"><p class="eyebrow"><span class="eyebrow__dash"></span>Compra</p><h1 class="h2">Tu carrito</h1></div>

<?php if (isset($_GET['pedido']) && $_GET['pedido']==='ok'): ?>
<div class="cart-success"><h3>¡Pedido confirmado!</h3><p>La compra fue registrada correctamente. Se guardó la venta y se descontó el stock de los productos.</p></div>
<?php endif; ?>

<?php if (!$carrito): ?>
<div class="empty-shop"><h3>Tu carrito está vacío</h3><p>Agregá una membresía o un producto de la tienda.</p><a class="btn btn--solid" href="productos.php">Ir a productos</a></div>
<?php else: ?>
<form action="Controlador/carrito.php" method="post">
<input type="hidden" name="accion" value="actualizar">
<div class="cart-layout">
<div class="cart-items">
<?php foreach ($carrito as $item): ?>
<div class="cart-item">
<div class="cart-item__image"><?php if (!empty($item['imagen'])): ?><img src="<?= htmlspecialchars($item['imagen']) ?>" alt=""><?php else: ?><span><?= $item['tipo']==='plan'?'PLAN':'A' ?></span><?php endif; ?></div>
<div class="cart-item__info"><span><?= $item['tipo']==='plan'?'MEMBRESÍA':'PRODUCTO' ?></span><h3><?= htmlspecialchars($item['nombre']) ?></h3><p>$<?= number_format($item['precio'],0,',','.') ?> c/u</p></div>
<div><label>Cantidad</label><input class="cart-qty" type="number" name="cantidades[<?= htmlspecialchars($item['clave']) ?>]" value="<?= (int)$item['cantidad'] ?>" min="0" max="<?= (int)$item['stock'] ?>"></div>
<strong>$<?= number_format($item['precio']*$item['cantidad'],0,',','.') ?></strong>
<a href="Controlador/carrito.php?accion=eliminar&clave=<?= urlencode($item['clave']) ?>" onclick="return confirm('¿Quitar este artículo del carrito?');">Eliminar</a>
</div>
<?php endforeach; ?>
</div>
<aside class="cart-summary"><h2>Resumen</h2><p>Artículos: <strong><?= $cantidadTotal ?></strong></p><p class="cart-total">Total: $<?= number_format($total,0,',','.') ?></p>
<button class="btn btn--outline" type="submit">Actualizar carrito</button>
<a class="btn btn--solid" href="Controlador/carrito.php?accion=confirmar">Continuar al pago</a>
<a class="cart-clear" href="Controlador/carrito.php?accion=vaciar" onclick="return confirm('¿Vaciar todo el carrito?');">Vaciar carrito</a></aside>
</div></form>
<?php endif; ?>
</div></section>
<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body></html>
