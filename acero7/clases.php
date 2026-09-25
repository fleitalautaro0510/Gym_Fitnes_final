<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clases | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>

<?php $paginaActiva = 'clases'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<!-- ===== CLASES ===== -->
<section class="section page-section" id="clases">
  <div class="container">
    <div class="section__head section__head--center">
      <p class="eyebrow"><span class="eyebrow__dash"></span>Entrenamiento grupal</p>
      <h2 class="h2">Elegí tu disciplina</h2>
    </div>

    <div class="class-grid">
      <article class="class-card">
        <div class="class-card__img class-card__img--hiit"><img src="img/clase-hiit.jpg" alt="Clase de HIIT en Acero Gym"></div>
        <div class="class-card__body">
          <h3>HIIT</h3>
          <p>Series de alta intensidad para quemar al máximo en poco tiempo.</p>
          <span class="class-card__meta">45 min · Cupo 20</span>
        </div>
      </article>

      <article class="class-card">
        <div class="class-card__img class-card__img--musc"><img src="img/clase-musculacion.jpg" alt="Clase de musculación en Acero Gym"></div>
        <div class="class-card__body">
          <h3>Musculación</h3>
          <p>Trabajo de fuerza con seguimiento de rutinas y progresión de cargas.</p>
          <span class="class-card__meta">60 min · Libre</span>
        </div>
      </article>

      <article class="class-card">
        <div class="class-card__img class-card__img--func"><img src="img/clase-funcional.jpg" alt="Clase funcional en Acero Gym"></div>
        <div class="class-card__body">
          <h3>Funcional</h3>
          <p>Movimientos multiarticulares que mejoran fuerza, movilidad y resistencia.</p>
          <span class="class-card__meta">50 min · Cupo 15</span>
        </div>
      </article>

      <article class="class-card">
        <div class="class-card__img class-card__img--yoga"><img src="img/clase-yoga.jpg" alt="Clase de yoga y movilidad en Acero Gym"></div>
        <div class="class-card__body">
          <h3>Yoga &amp; Movilidad</h3>
          <p>Elongación, respiración y recuperación activa para cerrar la semana.</p>
          <span class="class-card__meta">50 min · Cupo 18</span>
        </div>
      </article>
    </div>

    <div class="center-cta">
      <a href="planes.php" class="btn btn--solid">Ver todos los planes</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body>
</html>
