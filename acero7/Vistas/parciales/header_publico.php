<?php
/**
 * Header público reutilizable para todas las páginas del sitio (fuera del panel admin).
 *
 * Antes de incluir este archivo se puede definir:
 *   $paginaActiva = 'productos'; // resalta el link correspondiente del menú
 *
 * Se encarga de:
 *   - Iniciar la sesión de forma segura (si todavía no estaba iniciada).
 *   - Calcular la cantidad de artículos en el carrito para mostrarla en el botón.
 *   - Mostrar "Iniciar sesión" o el estado logueado (nombre + cerrar sesión) según corresponda.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$paginaActiva = $paginaActiva ?? '';

$cartCount = 0;
foreach (($_SESSION['carrito'] ?? []) as $item) {
    $cartCount += (int)($item['cantidad'] ?? 0);
}

$estaLogueado  = isset($_SESSION['user_id']);
$rolUsuario    = $_SESSION['user_rol'] ?? null;
$nombreUsuario = $_SESSION['user_nombre'] ?? '';

function claseActiva(string $pagina, string $paginaActiva): string
{
    return $pagina === $paginaActiva ? ' is-active' : '';
}
?>
<header class="header is-scrolled" id="header">
  <div class="container header__row">
    <a href="index.php" class="logo">
      <span class="logo__mark">A</span>
      <span class="logo__text">ACERO<span class="logo__sub">GYM</span></span>
    </a>

    <nav class="nav" id="nav">
      <a href="index.php" class="nav__link<?= claseActiva('inicio', $paginaActiva) ?>">Inicio</a>
      <a href="nosotros.php" class="nav__link<?= claseActiva('nosotros', $paginaActiva) ?>">Nosotros</a>
      <a href="servicios.php" class="nav__link<?= claseActiva('servicios', $paginaActiva) ?>">Servicios</a>
      <a href="clases.php" class="nav__link<?= claseActiva('clases', $paginaActiva) ?>">Clases</a>
      <a href="planes.php" class="nav__link<?= claseActiva('planes', $paginaActiva) ?>">Planes</a>
      <a href="contacto.php" class="nav__link<?= claseActiva('contacto', $paginaActiva) ?>">Contacto</a>
      <a href="productos.php" class="nav__link<?= claseActiva('productos', $paginaActiva) ?>">Productos</a>
    </nav>

    <a href="planes.php" class="btn btn--ghost header__cta">Sumate ahora</a>

    <?php if ($estaLogueado): ?>
        <?php if ((int)$rolUsuario === 1): ?>
            <a href="index.php?action=admin_dashboard" class="btn btn--solid header__cta">Panel admin</a>
        <?php endif; ?>
        <?php if ((int)$rolUsuario === 2): ?>
            <a href="mis_compras.php" class="btn btn--outline header__cta">Hola, <?= htmlspecialchars($nombreUsuario) ?></a>
        <?php else: ?>
            <span class="btn btn--outline header__cta" style="cursor:default;">Hola, <?= htmlspecialchars($nombreUsuario) ?></span>
        <?php endif; ?>
        <a href="index.php?action=cerrar_sesion" class="btn btn--ghost header__cta">Cerrar sesión</a>
    <?php else: ?>
        <a href="index.php?action=login" class="btn btn--outline header__cta">Iniciar sesión</a>
    <?php endif; ?>

    <a href="carrito.php" class="btn btn--ghost header__cta">CARRITO (<?= $cartCount ?>)</a>

    <button class="burger" id="burger" aria-label="Abrir menú">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
