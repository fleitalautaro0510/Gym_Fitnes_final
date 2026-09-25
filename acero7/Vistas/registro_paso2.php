<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro — Paso 2 | Acero Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="auth.css">
</head>
<body>

<div class="auth-page">
  <a href="index.php" class="auth-back">&larr; Volver al inicio</a>

  <div class="auth-card">
    <div class="auth-card__form">
      <p class="auth-card__eyebrow">Paso 2 de 2</p>
      <h1 class="auth-card__title">Crear<br>Cuenta</h1>

      <form action="index.php?action=guardar_paso2" method="POST">
        <div class="field">
          <input class="field__input" type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
          <span class="field__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" stroke="currentColor" stroke-width="2"/><path d="M3 21c1.8-4 5.2-6 9-6s7.2 2 9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </span>
        </div>
        <div class="field">
          <input class="field__input" type="email" name="email" placeholder="Correo electrónico" required>
          <span class="field__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="m3 7 9 6 9-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
        </div>
        <div class="field">
          <input class="field__input" type="password" name="clave" placeholder="Contraseña" required>
          <span class="field__icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="4" y="11" width="16" height="9" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 1 1 8 0v4" stroke="currentColor" stroke-width="2"/></svg>
          </span>
        </div>
        <button type="submit" class="auth-btn">Finalizar registro</button>
      </form>

      <p class="auth-card__foot">¿Ya tenés cuenta? <a href="index.php?action=login">Iniciá sesión</a></p>
    </div>

    <div class="auth-card__brand">
      <div class="auth-card__brand-window">
        <img src="logo-acero.png" alt="Logo Acero Gym" class="auth-card__brand-logo">
      </div>
      <p class="auth-card__brand-caption">FUERZA · DISCIPLINA · RENDIMIENTO</p>
    </div>
  </div>
</div>

</body>
</html>
