<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$estaLogueado = isset($_SESSION['user_id']);
?>
<footer class="footer">
  <div class="container footer__row">
    <a href="index.php" class="logo logo--footer">
      <span class="logo__mark">A</span>
      <span class="logo__text">ACERO<span class="logo__sub">GYM</span></span>
    </a>

    <nav class="footer__nav">
      <a href="index.php">Inicio</a>
      <a href="nosotros.php">Nosotros</a>
      <a href="servicios.php">Servicios</a>
      <a href="clases.php">Clases</a>
      <a href="planes.php">Planes</a>
      <a href="contacto.php">Contacto</a>
      <a href="productos.php">Productos</a>
      <a href="carrito.php">Carrito</a>
      <?php if ($estaLogueado): ?>
        <a href="mis_compras.php">Mis compras</a>
        <a href="index.php?action=cerrar_sesion">Cerrar sesión</a>
      <?php else: ?>
        <a href="index.php?action=login">Iniciar sesión</a>
      <?php endif; ?>
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
