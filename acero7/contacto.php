<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contacto | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>

<?php $paginaActiva = 'contacto'; require __DIR__ . '/Vistas/parciales/header_publico.php'; ?>

<!-- ===== CONTACTO ===== -->
<section class="section page-section" id="contacto">
  <div class="container contact">
    <div class="contact__info">
      <p class="eyebrow"><span class="eyebrow__dash"></span>Visitanos</p>
      <h2 class="h2">Empezá hoy tu<br>entrenamiento</h2>
      <ul class="contact__list">
        <li><strong>Dirección:</strong> Av. Principal 1234, tu ciudad</li>
        <li><strong>Horario:</strong> Lun. a Sáb. 06:00–23:00</li>
        <li><strong>Teléfono:</strong> +54 9 11 0000-0000</li>
        <li><strong>Email:</strong> info@acerogym.com</li>
      </ul>
    </div>

    <form class="contact__form" id="contactForm">
      <div class="form-row">
        <input type="text" placeholder="Nombre" required>
        <input type="email" placeholder="Email" required>
      </div>
      <input type="tel" placeholder="Teléfono">
      <textarea rows="4" placeholder="Contanos qué estás buscando..."></textarea>
      <button type="submit" class="btn btn--solid">Enviar mensaje</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/Vistas/parciales/footer_publico.php'; ?>
</body>
</html>
