<?php
session_start();require_once __DIR__.'/Controlador/conexion.php';require_once __DIR__.'/Modelo/ventas.php';
if(!isset($_SESSION['user_id'])){header('Location: index.php?action=login');exit;}
$uid=(int)$_SESSION['user_id'];$ventas=obtenerVentasUsuario($pdo,$uid);$membresias=obtenerInscripcionesUsuario($pdo,$uid);$detalle=null;$ventaSel=null;
if(!empty($_GET['id'])){foreach($ventas as $v)if((int)$v['id_venta']===(int)$_GET['id']){$ventaSel=$v;break;}if($ventaSel)$detalle=obtenerDetalleVenta($pdo,(int)$_GET['id']);}
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mis compras | Acero Gym</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="index.css"></head><body>
<?php $paginaActiva = ''; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>
<section class="section page-section"><div class="container"><p class="eyebrow"><span class="eyebrow__dash"></span>Socio</p><h1 class="h2">Mis compras</h1>
<h2>Productos</h2><?php if(!$ventas):?><p>Aún no tenés compras de productos.</p><?php else:foreach($ventas as $v):?><article class="purchase-card"><div><small>Venta #<?= (int)$v['id_venta'] ?></small><h3><?= date('d/m/Y H:i',strtotime($v['fecha_venta'])) ?></h3><p><?= htmlspecialchars($v['metodo_pago']) ?></p></div><strong>$<?= number_format((float)$v['total'],0,',','.') ?></strong><a class="btn btn--outline" href="mis_compras.php?id=<?= (int)$v['id_venta'] ?>">Ver detalle</a></article><?php endforeach;endif;?>
<?php if($ventaSel):?><div class="purchase-detail"><h2>Detalle de venta</h2><?php foreach($detalle as $d):?><p><?= htmlspecialchars($d['producto_nombre']??'Producto eliminado') ?> × <?= (int)$d['cantidad'] ?><strong>$<?= number_format((float)$d['sub_total'],0,',','.') ?></strong></p><?php endforeach;?><hr><p class="cart-total">Total: $<?= number_format((float)$ventaSel['total'],0,',','.') ?></p></div><?php endif;?>
<h2 style="margin-top:3rem">Membresías adquiridas</h2><?php if(!$membresias):?><p>Aún no tenés membresías registradas.</p><?php else:foreach($membresias as $m):?><article class="purchase-card"><div><h3><?= htmlspecialchars($m['membresia']) ?></h3><p><?= htmlspecialchars($m['duracion']) ?> · Desde <?= htmlspecialchars($m['fecha_inicio']) ?> hasta <?= htmlspecialchars($m['fecha_fin']) ?></p></div><strong>$<?= number_format((float)$m['sub_total'],0,',','.') ?></strong></article><?php endforeach;endif;?>
</div></section>
<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body></html>
