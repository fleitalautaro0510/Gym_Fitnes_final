<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro — Paso 1 | Acero Gym</title>
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
      <p class="auth-card__eyebrow">Paso 1 de 2</p>
      <h1 class="auth-card__title">Datos<br>Físicos</h1>

      <form action="index.php?action=guardar_paso1" method="POST">
        <div class="field">
          <input class="field__input" type="text" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="field">
          <input class="field__input" type="text" name="apellido" placeholder="Apellido" required>
        </div>
        <div class="field">
          <input class="field__input" type="number" name="dni" placeholder="DNI" required>
        </div>
        <div class="field">
          <input class="field__input" type="number" name="altura" placeholder="Altura (cm)" required>
        </div>
        <div class="field">
          <input class="field__input" type="number" step="0.01" name="peso" placeholder="Peso (kg)" required>
        </div>
        <div class="field">
          <select class="field__input" name="genero" required>
            <option value="">Seleccioná género</option>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
          </select>
        </div>
        <button type="submit" class="auth-btn">Siguiente: crear cuenta</button>
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
