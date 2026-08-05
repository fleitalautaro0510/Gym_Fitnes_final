<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Cliente</title>
</head>
<body>
    <h2>Registro</h2>

    <form action="guardar_cliente.php" method="post">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br><br>

        <label>DNI:</label><br>
        <input type="number" name="dni" required><br><br>

        <label>Altura:</label><br>
        <input type="number" name="altura" required><br><br>

        <label>Peso:</label><br>
        <input type="number" name="peso" required><br><br>

        <label>Genero:</label><br>
        <select name="genero" requiered>
            <option value="">Seleccionar genero:</option>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
            <option value="otro">Otro</option>
        
        </select><br><br>
        <button type="submit">Guardar</button><br><br>
    </form>
    <button><a href="login.php">Volver</a></button>
</body>
</html>