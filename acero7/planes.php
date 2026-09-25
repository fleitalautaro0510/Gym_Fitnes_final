<?php
session_start();
require_once __DIR__.'/Controlador/conexion.php';
$planes=$pdo->query("SELECT id_membresia,membresia,duracion,precio FROM Membresias WHERE LOWER(membresia) IN ('mensual','trimestral','anual','membresía mensual','membresía trimestral','membresía anual') ORDER BY FIELD(LOWER(membresia),'mensual','membresía mensual','trimestral','membresía trimestral','anual','membresía anual')")->fetchAll();
$map=[];foreach($planes as $p){$key=strtolower($p['membresia']);if(str_contains($key,'mensual'))$map['mensual']=$p;elseif(str_contains($key,'trimestral'))$map['trimestral']=$p;elseif(str_contains($key,'anual'))$map['anual']=$p;}
$beneficios=[
 'mensual'=>['Acceso QR, tarjeta o huella','Uso libre de sala de musculación','1 clase grupal por día','App de reservas y pagos'],
 'trimestral'=>['Todo lo del plan Mensual','Clases grupales ilimitadas','Congelamiento de membresía','Descuento en tienda Acero'],
 'anual'=>['Todo lo del plan Trimestral','Evaluación física trimestral','2 invitados por mes','Precio bloqueado durante el año']
];
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Planes | Acero Gym</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="index.css"></head><body>
<?php $paginaActiva = 'planes'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>
<section class="section section--dark page-section"><div class="container"><div class="section__head section__head--center"><p class="eyebrow eyebrow--light"><span class="eyebrow__dash"></span>Membresías</p><h2 class="h2 h2--light">Un plan para cada objetivo</h2></div><div class="plan-grid">
<?php foreach(['mensual','trimestral','anual'] as $tipo): $p=$map[$tipo]??null;if(!$p)continue; ?><article class="plan-card <?= $tipo==='trimestral'?'plan-card--featured':'' ?>"><?php if($tipo==='trimestral'):?><span class="plan-card__tag">Más elegido</span><?php endif; ?><h3 class="plan-card__name"><?= htmlspecialchars($p['membresia']) ?></h3><p class="plan-card__price"><span>$</span><?= number_format((float)$p['precio'],0,',','.') ?><small>/<?= htmlspecialchars($p['duracion']) ?></small></p><ul class="plan-card__list"><?php foreach($beneficios[$tipo] as $b):?><li><?= htmlspecialchars($b) ?></li><?php endforeach;?></ul><form action="Controlador/carrito.php" method="post" class="plan-cart-form"><input type="hidden" name="accion" value="agregar_plan"><input type="hidden" name="id_membresia" value="<?= (int)$p['id_membresia'] ?>"><button type="submit" class="btn <?= $tipo==='trimestral'?'btn--solid':'btn--outline' ?> plan-card__btn">Agregar al carrito</button></form></article><?php endforeach; ?></div></div></section>
<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body></html>
