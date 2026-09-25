<?php
session_start();
$carrito = $_SESSION['carrito'] ?? [];
if (!$carrito) { header('Location: carrito.php'); exit; }
$total = 0;
foreach ($carrito as $item) $total += (float)$item['precio'] * (int)$item['cantidad'];
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Finalizar compra | Acero Gym</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="index.css"></head>
<body>
<?php $paginaActiva = ''; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>
<section class="section page-section"><div class="container checkout-grid"><div><p class="eyebrow"><span class="eyebrow__dash"></span>Finalizar compra</p><h1 class="h2">Datos de la compra</h1>
<?php if ($error): ?><div class="cart-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if (!isset($_SESSION['user_id'])): ?><div class="cart-error">Necesitás <a href="index.php?action=login">iniciar sesión</a> para poder confirmar la compra.</div><?php endif; ?>
<form class="checkout-form" action="Controlador/venta.php" method="post"><label>Nombre completo<input type="text" name="cliente_nombre" value="<?= htmlspecialchars($_SESSION['user_nombre'] ?? '') ?>" required></label><label>Email<input type="email" name="cliente_email"></label><label>Medio de pago<select name="metodo_pago"><option value="efectivo">Efectivo en recepción</option><option value="transferencia">Transferencia bancaria</option><option value="tarjeta">Tarjeta</option></select></label><label>Observaciones<textarea name="observaciones" rows="4" placeholder="Información adicional de la compra"></textarea></label><button class="btn btn--solid" type="submit">Confirmar compra</button></form></div>
<aside class="cart-summary"><h2>Resumen</h2><?php foreach ($carrito as $item): ?><p><?= htmlspecialchars($item['nombre']) ?> × <?= (int)$item['cantidad'] ?><strong>$<?= number_format($item['precio']*$item['cantidad'],0,',','.') ?></strong></p><?php endforeach; ?><hr><p class="cart-total">Total: $<?= number_format($total,0,',','.') ?></p></aside></div></section>
<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body></html>
