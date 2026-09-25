<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nosotros | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>

<?php $paginaActiva = 'nosotros'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<!-- ===== NOSOTROS / OVERVIEW ===== -->
<section class="section page-section" id="nosotros">
  <div class="container split">
    <div class="split__media">
      <div class="media-frame">
        <img src="img/nosotros.jpg" alt="Socios entrenando en Acero Gym" class="media-frame__photo">
        <div class="media-frame__plate">01</div>
      </div>
    </div>

    <div class="split__content">
      <p class="eyebrow"><span class="eyebrow__dash"></span>Quiénes somos</p>
      <h2 class="h2">Un gimnasio pensado<br>para que <em>nunca esperes</em></h2>
      <p class="p-lead">
        Acero nació para romper con la recepción saturada, la fila hasta la
        vereda y el mostrador que traba todo. Reemplazamos el papel y el
        teclado por un sistema que atiende a cientos de socios al mismo
        tiempo.
      </p>
      <ul class="checklist">
        <li>Ingreso autónomo con QR, tarjeta o huella digital</li>
        <li>Reservas y cancelaciones de clases desde la app</li>
        <li>Pagos, facturas y vencimientos automatizados</li>
      </ul>
      <a href="servicios.php" class="link-arrow">Descubrí cómo funciona →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body>
</html>
