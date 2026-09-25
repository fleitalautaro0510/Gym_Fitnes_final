-- ============================================================
-- ACERO GYM — Recreación completa de la base de datos "mydb"
-- ------------------------------------------------------------
-- Reconstruido a partir del código PHP real del proyecto
-- (ClienteModel, UsuarioModel, funciones_productos, ventas.php,
-- AdminModel, la API de la app socio, etc.)
--
-- CÓMO USARLO (después de reinstalar XAMPP):
--   1. Entrá a http://localhost/phpmyadmin
--   2. Pestaña "SQL" (arriba)
--   3. Pegá TODO este archivo y tocá "Continuar" / "Go"
--   Esto crea la base "mydb" con todas las tablas vacías, lista
--   para usar. Después corré crear_admin.php y migrar_app_socio.php
--   como ya hiciste antes.
-- ============================================================

CREATE DATABASE IF NOT EXISTS mydb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mydb;

-- ---------- Roles y personas ----------

CREATE TABLE IF NOT EXISTS Roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    Rol VARCHAR(45) NOT NULL
);
INSERT INTO Roles (id_rol, Rol) VALUES
    (1, 'Administrador'),
    (2, 'Consumidor'),
    (3, 'Empleado');

CREATE TABLE IF NOT EXISTS clientes (
    id_clientes INT AUTO_INCREMENT PRIMARY KEY,
    codigo_acceso VARCHAR(40) NULL UNIQUE,
    Nombre VARCHAR(45) NOT NULL,
    apellido VARCHAR(45) NOT NULL,
    DNI VARCHAR(20) NOT NULL UNIQUE,
    altura DECIMAL(4,2) NULL,
    peso DECIMAL(5,2) NULL,
    genero ENUM('Masculino','Femenino','Otro') NULL
);

CREATE TABLE IF NOT EXISTS empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45) NOT NULL,
    apellido VARCHAR(45) NOT NULL,
    DNI VARCHAR(20) NULL,
    telefono VARCHAR(30) NULL,
    fecha_ingreso DATE NULL
);

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuarios INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(45) NOT NULL UNIQUE,
    email VARCHAR(90) NOT NULL,
    clave VARCHAR(255) NOT NULL,
    FK_id_rol INT NOT NULL,
    Fk_id_cliente INT NULL,
    Fk_id_empleado INT NULL,
    FOREIGN KEY (FK_id_rol) REFERENCES Roles(id_rol),
    FOREIGN KEY (Fk_id_cliente) REFERENCES clientes(id_clientes),
    FOREIGN KEY (Fk_id_empleado) REFERENCES empleados(id_empleado)
);

-- ---------- Catálogo de la tienda ----------

CREATE TABLE IF NOT EXISTS Categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(45) NOT NULL
);

CREATE TABLE IF NOT EXISTS Marca (
    id_Marca INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45) NOT NULL
);

CREATE TABLE IF NOT EXISTS Proveedor (
    id_Proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45) NOT NULL,
    telefono VARCHAR(30) NULL
);

CREATE TABLE IF NOT EXISTS Productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    Fk_id_categoria INT NULL,
    Fk_id_marca INT NULL,
    Fk_id_proveedor INT NULL,
    precio DECIMAL(10,2) NULL,
    precio_compra DECIMAL(10,2) NULL,
    precio_venta DECIMAL(10,2) NULL,
    Stock_min INT NOT NULL DEFAULT 0,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_mac INT NOT NULL DEFAULT 0,
    descripcion TEXT NULL,
    caracteristicas TEXT NULL,
    imagen VARCHAR(255) NULL,
    FOREIGN KEY (Fk_id_categoria) REFERENCES Categoria(id_categoria),
    FOREIGN KEY (Fk_id_marca) REFERENCES Marca(id_Marca),
    FOREIGN KEY (Fk_id_proveedor) REFERENCES Proveedor(id_Proveedor)
);

-- ---------- Clases y membresías del gimnasio ----------

CREATE TABLE IF NOT EXISTS Clases (
    id_clase INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    Fk_id_instructor INT NULL,
    dia_semana ENUM('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    cupo_maximo INT NOT NULL DEFAULT 15,
    sala VARCHAR(45) NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (Fk_id_instructor) REFERENCES empleados(id_empleado)
);

CREATE TABLE IF NOT EXISTS Membresias (
    id_membresia INT AUTO_INCREMENT PRIMARY KEY,
    membresia VARCHAR(45) NOT NULL,
    duracion INT NULL,
    precio DECIMAL(10,2) NULL,
    Fk_id_clase INT NULL,
    FOREIGN KEY (Fk_id_clase) REFERENCES Clases(id_clase)
);

CREATE TABLE IF NOT EXISTS Reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    Fk_id_cliente INT NOT NULL,
    Fk_id_clase INT NOT NULL,
    fecha_clase DATE NOT NULL,
    estado ENUM('confirmada','cancelada','lista_espera') NOT NULL DEFAULT 'confirmada',
    fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unica_reserva_activa (Fk_id_cliente, Fk_id_clase, fecha_clase),
    FOREIGN KEY (Fk_id_cliente) REFERENCES clientes(id_clientes),
    FOREIGN KEY (Fk_id_clase) REFERENCES Clases(id_clase)
);

-- ---------- Ventas de la tienda online ----------

CREATE TABLE IF NOT EXISTS Medio_de_pago (
    id_medio_de_pago INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45) NOT NULL
);
INSERT INTO Medio_de_pago (nombre) VALUES
    ('Efectivo'), ('Tarjeta de débito'), ('Tarjeta de crédito'), ('Transferencia');

CREATE TABLE IF NOT EXISTS ventas_productos (
    id_venta_producto INT AUTO_INCREMENT PRIMARY KEY,
    fecha_venta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Fk_id_usuario INT NULL,
    FOREIGN KEY (Fk_id_usuario) REFERENCES usuarios(id_usuarios)
);

CREATE TABLE IF NOT EXISTS Detalle_venta_producto (
    id_detalle_venta_producto INT AUTO_INCREMENT PRIMARY KEY,
    Fk_id_ventas_producto INT NOT NULL,
    fk_id_producto INT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    FOREIGN KEY (Fk_id_ventas_producto) REFERENCES ventas_productos(id_venta_producto),
    FOREIGN KEY (fk_id_producto) REFERENCES Productos(id_producto)
);

CREATE TABLE IF NOT EXISTS Pagos_productos (
    id_Pagos_productos INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    Fk_id_venta_producto INT NOT NULL,
    Fk_id_medio_pago INT NULL,
    FOREIGN KEY (Fk_id_venta_producto) REFERENCES ventas_productos(id_venta_producto),
    FOREIGN KEY (Fk_id_medio_pago) REFERENCES Medio_de_pago(id_medio_de_pago)
);

-- ---------- Pagos de membresías ----------

CREATE TABLE IF NOT EXISTS Pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    Monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    Fk_id_cliente INT NULL,
    FOREIGN KEY (Fk_id_cliente) REFERENCES clientes(id_clientes)
);

-- ---------- Sesiones de la API (app socio) ----------

CREATE TABLE IF NOT EXISTS tokens_api (
    token VARCHAR(64) PRIMARY KEY,
    Fk_id_usuario INT NOT NULL,
    creado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expira DATETIME NOT NULL,
    FOREIGN KEY (Fk_id_usuario) REFERENCES usuarios(id_usuarios)
);

-- ---------- Datos de ejemplo para arrancar probando ----------

INSERT INTO Categoria (nombre_categoria) VALUES ('Suplementos'), ('Indumentaria'), ('Accesorios');
INSERT INTO Marca (nombre) VALUES ('ENA'), ('Star Nutrition'), ('Nike');
INSERT INTO Proveedor (nombre, telefono) VALUES ('Distribuidora Sur', '3704-555000');

INSERT INTO Clases (nombre, dia_semana, hora_inicio, hora_fin, cupo_maximo, sala) VALUES
    ('HIIT',        'Lunes',     '18:00:00', '18:45:00', 20, 'Sala 1'),
    ('Musculación', 'Lunes',     '09:00:00', '10:00:00', 30, 'Sala 2'),
    ('Funcional',   'Miercoles', '19:00:00', '19:50:00', 15, 'Sala 1'),
    ('Yoga',        'Viernes',   '08:00:00', '08:50:00', 18, 'Sala 3');

INSERT INTO Membresias (membresia, duracion, precio) VALUES
    ('Mensual', 30, 25000),
    ('Trimestral', 90, 65000);
