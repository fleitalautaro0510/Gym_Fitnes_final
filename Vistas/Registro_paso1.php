<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paso 1: Datos Físicos Gym</title>
</head>
<body>
    <h2>Registro de Cliente - Paso 1</h2>
    <form action="index.php?action=guardar_paso1" method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="text" name="apellido" placeholder="Apellido" required><br>
        <input type="number" name="dni" placeholder="DNI" required><br>
        <input type="number" name="altura" placeholder="Altura (cm)" required><br>
        <input type="number" step="0.01" name="peso" placeholder="Peso (kg)" required><br>
        <select name="genero" required>
            <option value="">Seleccione Género</option>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
        </select><br>
        <button type="submit">Siguiente: Crear Cuenta</button>
    </form>
</body>
</html>