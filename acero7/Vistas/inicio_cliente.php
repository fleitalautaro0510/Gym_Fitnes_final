<?php
$cartCount = 0;
foreach (($_SESSION['carrito'] ?? []) as $item) {
    $cartCount += (int)($item['cantidad'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ACERO | Mi espacio - Cliente</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="header" id="header">
  <div class="container header__row">
    <a href="#inicio" class="logo">
      <span class="logo__mark">A</span>
      <span class="logo__text">ACERO<span class="logo__sub">GYM</span></span>
    </a>

    <nav class="nav" id="nav">
      <a href="#inicio" class="nav__link is-active">Inicio</a>
      <a href="#nosotros" class="nav__link">Nosotros</a>
      <a href="#servicios" class="nav__link">Servicios</a>
      <a href="#clases" class="nav__link">Clases</a>
      <a href="#planes" class="nav__link">Planes</a>
      <a href="#contacto" class="nav__link">Contacto</a>
      <a href="productos.php" class="nav__link">Productos</a>
    </nav>

    <a href="#planes" class="btn btn--ghost header__cta">Sumate ahora</a>
    <a href="mis_compras.php" class="btn btn--outline header__cta">Mis compras</a>
    <a href="index.php?action=cerrar_sesion" class="btn btn--ghost header__cta">Cerrar sesión</a>
    <a href="carrito.php" class="btn btn--ghost header__cta">CARRITO (<?= $cartCount ?>)</a>

    <button class="burger" id="burger" aria-label="Abrir menú">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- ===== HERO ===== -->
<section class="hero" id="inicio">
  <div class="hero__bg-photo" aria-hidden="true"><img src="img/hero-fondo.jpg" alt=""></div>
  <div class="hero__bg" aria-hidden="true"></div>
  <div class="hero__grain" aria-hidden="true"></div>

  <div class="container hero__grid">
    <div class="hero__content">
      <p class="eyebrow"><span class="eyebrow__dash"></span>Mi espacio de cliente</p>
      <h1 class="hero__title">
        FORJÁ<br>
        <span class="hero__title--outline">TU LÍMITE</span>
      </h1>
      <p class="hero__text">
        En Acero no contamos repeticiones, forjamos resultados. Entrená con
        acceso 24/7 por QR, huella o tarjeta, reservá tus clases desde el
        celular y seguí tu progreso sin filas ni esperas.
      </p>
      <div class="hero__actions">
        <a href="#planes" class="btn btn--solid">Ver planes</a>
        <a href="#nosotros" class="btn btn--outline">Conocé Acero</a>
      </div>

      <div class="hero__stats">
        <div class="stat">
          <span class="stat__num" data-count="500">0</span>
          <span class="stat__label">Socios activos</span>
        </div>
        <div class="stat">
          <span class="stat__num" data-count="24">0</span>
          <span class="stat__label">Horas de acceso</span>
        </div>
        <div class="stat">
          <span class="stat__num" data-count="12">0</span>
          <span class="stat__label">Clases grupales</span>
        </div>
      </div>
    </div>

    <div class="hero__brand" aria-label="Acero Gym">
      <div class="hero__brand-glow"></div>
      <img src="logo-acero.png" alt="Logo Acero Gym" class="hero__brand-logo">
      <p class="hero__brand-caption">FUERZA · DISCIPLINA · RENDIMIENTO</p>
    </div>
  </div>

  <a href="#nosotros" class="scroll-cue" aria-label="Bajar">
    <span></span>
  </a>
</section>

<!-- ===== NOSOTROS / OVERVIEW ===== -->
<section class="section" id="nosotros">
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
      <a href="#servicios" class="link-arrow">Descubrí cómo funciona →</a>
    </div>
  </div>
</section>

<!-- ===== SERVICIOS / POR QUÉ ACERO ===== -->
<section class="section section--dark" id="servicios">
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
  </div>
</section>

<!-- ===== CLASES ===== -->
<section class="section" id="clases">
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
      <a href="#planes" class="btn btn--solid">Ver todas las clases</a>
    </div>
  </div>
</section>

<!-- ===== PLANES ===== -->
<section class="section section--dark" id="planes">
  <div class="container">
    <div class="section__head section__head--center">
      <p class="eyebrow eyebrow--light"><span class="eyebrow__dash"></span>Membresías</p>
      <h2 class="h2 h2--light">Un plan para cada objetivo</h2>
    </div>

    <div class="plan-grid">
      <article class="plan-card">
        <h3 class="plan-card__name">Mensual</h3>
        <p class="plan-card__price"><span>$</span>30.000<small>/mes</small></p>
        <ul class="plan-card__list">
          <li>Acceso QR, tarjeta o huella</li>
          <li>Uso libre de sala de musculación</li>
          <li>1 clase grupal por día</li>
          <li>App de reservas y pagos</li>
        </ul>
        <a href="#contacto" class="btn btn--outline plan-card__btn">Elegir plan</a>
      </article>

      <article class="plan-card plan-card--featured">
        <span class="plan-card__tag">Más elegido</span>
        <h3 class="plan-card__name">Trimestral</h3>
        <p class="plan-card__price"><span>$</span>70.000<small>/trim.</small></p>
        <ul class="plan-card__list">
          <li>Todo lo del plan Mensual</li>
          <li>Clases grupales ilimitadas</li>
          <li>Congelamiento de membresía</li>
          <li>Descuento en tienda Acero</li>
        </ul>
        <a href="#contacto" class="btn btn--solid plan-card__btn">Elegir plan</a>
      </article>

      <article class="plan-card">
        <h3 class="plan-card__name">Anual</h3>
        <p class="plan-card__price"><span>$</span>200.000<small>/año</small></p>
        <ul class="plan-card__list">
          <li>Todo lo del plan Trimestral</li>
          <li>Evaluación física trimestral</li>
          <li>Invitados sin cargo (2 al mes)</li>
          <li>Precio bloqueado todo el año</li>
        </ul>
        <a href="#contacto" class="btn btn--outline plan-card__btn">Elegir plan</a>
      </article>
    </div>
  </div>
</section>

<!-- ===== CONTACTO ===== -->
<section class="section" id="contacto">
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

<!-- ===== FOOTER ===== -->
<footer class="footer">
  <div class="container footer__row">
    <a href="#inicio" class="logo logo--footer">
      <span class="logo__mark">A</span>
      <span class="logo__text">ACERO<span class="logo__sub">GYM</span></span>
    </a>

    <nav class="footer__nav">
      <a href="#inicio">Inicio</a>
      <a href="#nosotros">Nosotros</a>
      <a href="#servicios">Servicios</a>
      <a href="#clases">Clases</a>
      <a href="#planes">Planes</a>
      <a href="#contacto">Contacto</a>
      <a href="index.php?action=cerrar_sesion">Cerrar Sesión</a>
      
    </nav>

    <div class="footer__social">
      <a href="#" aria-label="Instagram">IG</a>
      <a href="#" aria-label="Facebook">FB</a>
      <a href="#" aria-label="TikTok">TT</a>
    </div>
  </div>
  <div class="container footer__bottom">
    <p>© 2026 Acero Gym. Todos los derechos reservados.</p>
  </div>
</footer>

<script src="script.js"></script>
</body>
</html>