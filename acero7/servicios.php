<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Servicios | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>

<?php $paginaActiva = 'servicios'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<!-- ===== SERVICIOS / POR QUÉ ACERO ===== -->
<section class="section section--dark page-section" id="servicios">
  <div class="container">
    <div class="section__head">
      <p class="eyebrow eyebrow--light"><span class="eyebrow__dash"></span>Por qué elegirnos</p>
      <h2 class="h2 h2--light">Tecnología que se banca<br>tu volumen de entrenamiento</h2>
    </div>

    <div class="feature-grid">
      <article class="feature-card">
        <div class="feature-card__num">01</div>
        <h3>Acceso sin filas</h3>
        <p>Molinetes conectados al sistema: si tu membresía está al día, la
        puerta se abre sola. Sin depender de recepción.</p>
      </article>

      <article class="feature-card">
        <div class="feature-card__num">02</div>
        <h3>Reservas online</h3>
        <p>Anotate a tus clases desde el celular, con lista de espera
        automática si no queda cupo.</p>
      </article>

      <article class="feature-card">
        <div class="feature-card__num">03</div>
        <h3>Pagos sin vueltas</h3>
        <p>Cuotas, recordatorios y facturación automáticos. Pagá con
        tarjeta, transferencia o efectivo.</p>
      </article>

      <article class="feature-card">
        <div class="feature-card__num">04</div>
        <h3>Panel en vivo</h3>
        <p>Seguimiento de tu historial de accesos, clases y pagos, todo
        centralizado en un solo lugar.</p>
      </article>
    </div>

    <div class="center-cta">
      <a href="clases.php" class="btn btn--solid">Ver clases</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body>
</html>
