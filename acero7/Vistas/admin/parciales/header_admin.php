<?php
/**
 * Cabecera + menú lateral del panel de administración.
 *
 * Antes de incluirlo se puede definir:
 *   $tituloPagina  -> título del <head>
 *   $entidadActiva -> clave de la sección activa (para resaltarla en el menú)
 *   $entidades     -> array devuelto por adminEntidades()
 */
$tituloPagina  = $tituloPagina  ?? 'Panel de administración';
$entidadActiva = $entidadActiva ?? '';
$entidades     = $entidades     ?? adminEntidades();
$nombreAdmin   = $_SESSION['user_nombre'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ACERO | <?= htmlspecialchars($tituloPagina) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<header class="admin-topbar">
  <a href="index.php?action=admin_dashboard" class="logo">
    <span class="logo__mark">A</span>
    <span class="logo__text">ACERO<span class="logo__sub">ADMIN</span></span>
  </a>

  <button class="admin-burger" id="adminBurger" aria-label="Abrir menú">
    <span></span><span></span><span></span>
  </button>

  <div class="admin-topbar__right">
    <span class="admin-user">
      <span class="admin-user__dot"></span>
      <?= htmlspecialchars($nombreAdmin) ?>
    </span>
    <a href="index.php" class="btn btn--outline admin-topbar__btn">Ver sitio</a>
    <a href="index.php?action=cerrar_sesion" class="btn btn--ghost admin-topbar__btn">Cerrar sesión</a>
  </div>
</header>

<div class="admin-shell">

  <aside class="admin-sidebar" id="adminSidebar">
    <p class="admin-sidebar__label">General</p>
    <a href="index.php?action=admin_dashboard"
       class="admin-navlink<?= $entidadActiva === '' ? ' is-active' : '' ?>">Tablero</a>

    <p class="admin-sidebar__label">Gestión</p>
    <?php foreach ($entidades as $clave => $ent): ?>
      <a href="index.php?action=admin_listar&amp;ent=<?= urlencode($clave) ?>"
         class="admin-navlink<?= $entidadActiva === $clave ? ' is-active' : '' ?>">
        <?= htmlspecialchars($ent['titulo']) ?>
      </a>
    <?php endforeach; ?>
  </aside>

  <main class="admin-main">
