CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`Roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Roles` (
  `id_rol` INT NOT NULL AUTO_INCREMENT,
  `Rol` VARCHAR(65) NULL,
  PRIMARY KEY (`id_rol`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Empleados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Empleados` (
  `id_empleado` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(105) NULL,
  `apellido` VARCHAR(105) NULL,
  `DNI` INT NULL,
  `Domicilio` VARCHAR(105) NULL,
  `telefono` INT NULL,
  `sueldo` INT NULL,
  PRIMARY KEY (`id_empleado`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Clientes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Clientes` (
  `id_clientes` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(105) NULL,
  `apellido` VARCHAR(75) NULL,
  `DNI` INT NULL,
  `altura` INT NULL,
  `peso` DECIMAL NULL,
  `genero` VARCHAR(45) NULL,
  PRIMARY KEY (`id_clientes`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Usuarios` (
  `id_usuarios` INT NOT NULL AUTO_INCREMENT,
  `nombre_usuario` VARCHAR(85) NULL,
  `email` VARCHAR(105) NULL,
  `clave` VARCHAR(225) NULL,
  `FK_id_rol` INT NULL,
  `Fk_id_cliente` INT NULL,
  `Fk_id_empleado` INT NULL,
  PRIMARY KEY (`id_usuarios`),
  INDEX `Fk_id_rol_idx` (`FK_id_rol` ASC),
  INDEX `Fk_id_cliente_idx` (`Fk_id_cliente` ASC),
  INDEX `Fk_id_empleado_idx` (`Fk_id_empleado` ASC),
  CONSTRAINT `Fk_id_rol`
    FOREIGN KEY (`FK_id_rol`)
    REFERENCES `mydb`.`Roles` (`id_rol`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_empleado`
    FOREIGN KEY (`Fk_id_empleado`)
    REFERENCES `mydb`.`Empleados` (`id_empleado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_cliente`
    FOREIGN KEY (`Fk_id_cliente`)
    REFERENCES `mydb`.`Clientes` (`id_clientes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Trabajos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Trabajos` (
  `id_trabajo` INT NOT NULL AUTO_INCREMENT,
  `trabajo` VARCHAR(45) NULL,
  `descripcion` TEXT NULL,
  PRIMARY KEY (`id_trabajo`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Turno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Turno` (
  `id_turno` INT NOT NULL AUTO_INCREMENT,
  `turno` VARCHAR(45) NULL,
  PRIMARY KEY (`id_turno`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Clases`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Clases` (
  `id_clase` INT NOT NULL AUTO_INCREMENT,
  `clase` VARCHAR(45) NULL,
  `Fk_id_empleado` INT NULL,
  `precio` DECIMAL NULL,
  PRIMARY KEY (`id_clase`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Membresias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Membresias` (
  `id_membresia` INT NOT NULL AUTO_INCREMENT,
  `membresia` VARCHAR(45) NULL,
  `duracion` VARCHAR(45) NULL,
  `precio` DECIMAL NULL,
  `Fk_id_clase` INT NULL,
  PRIMARY KEY (`id_membresia`),
  INDEX `Fk_id_clase_idx` (`Fk_id_clase` ASC),
  CONSTRAINT `Fk_id_clase`
    FOREIGN KEY (`Fk_id_clase`)
    REFERENCES `mydb`.`Clases` (`id_clase`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Inscripcion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Inscripcion` (
  `id_inscripcion` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_usuario` INT NULL,
  `fecha_inscripcion` DATE NULL,
  `hora_inscripcion` TIME NULL,
  `total` DECIMAL NULL,
  PRIMARY KEY (`id_inscripcion`),
  INDEX `Fk_id_usuario_idx` (`Fk_id_usuario` ASC),
  CONSTRAINT `Fk_id_usuario`
    FOREIGN KEY (`Fk_id_usuario`)
    REFERENCES `mydb`.`Usuarios` (`id_usuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Detalle_inscripcion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Detalle_inscripcion` (
  `id_detalle_inscripcion` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_inscripcion` INT NOT NULL,
  `Fk_id_membresia` INT NOT NULL,
  `fecha_inicio` DATE NULL,
  `fecha_fin` DATE NULL,
  `sub_total` DECIMAL NULL,
  PRIMARY KEY (`id_detalle_inscripcion`),
  INDEX `Fk_id_membresia_idx` (`Fk_id_membresia` ASC),
  INDEX `Fk_id_inscripcion_idx` (`Fk_id_inscripcion` ASC),
  CONSTRAINT `Fk_id_membresia`
    FOREIGN KEY (`Fk_id_membresia`)
    REFERENCES `mydb`.`Membresias` (`id_membresia`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_inscripcion`
    FOREIGN KEY (`Fk_id_inscripcion`)
    REFERENCES `mydb`.`Inscripcion` (`id_inscripcion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Detalle_turnos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Detalle_turnos` (
  `id_detalle_turnos` INT NOT NULL AUTO_INCREMENT,
  `FK_id_turnos` INT NULL,
  `Fk_id_empleados` INT NULL,
  PRIMARY KEY (`id_detalle_turnos`),
  INDEX `Fk_id_empleados_idx` (`Fk_id_empleados` ASC),
  INDEX `Fk_id_turnos_idx` (`FK_id_turnos` ASC),
  CONSTRAINT `Fk_id_empleados`
    FOREIGN KEY (`Fk_id_empleados`)
    REFERENCES `mydb`.`Empleados` (`id_empleado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_turnos`
    FOREIGN KEY (`FK_id_turnos`)
    REFERENCES `mydb`.`Turno` (`id_turno`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Medio_de_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Medio_de_pago` (
  `id_medio_de_pago` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(105) NULL,
  PRIMARY KEY (`id_medio_de_pago`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Estado_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Estado_pago` (
  `id_estado_pago` INT NOT NULL AUTO_INCREMENT,
  `estado_de_pago` VARCHAR(65) NULL,
  PRIMARY KEY (`id_estado_pago`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Pagos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Pagos` (
  `id_Pagos` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_inscripcion` INT NULL,
  `Fk_id_medio_pago` INT NULL,
  `Monto` DECIMAL NULL,
  `fecha_pago` DATETIME NULL,
  `Fk_id_estado_pago` INT NULL,
  PRIMARY KEY (`id_Pagos`),
  INDEX `Fk_id_inscripcion_idx` (`Fk_id_inscripcion` ASC),
  INDEX `Fk_id_medio_pago_idx` (`Fk_id_medio_pago` ASC),
  INDEX `Fk_id_estado_pago_idx` (`Fk_id_estado_pago` ASC),
  CONSTRAINT `Fk_id_inscripcion_pago`
    FOREIGN KEY (`Fk_id_inscripcion`)
    REFERENCES `mydb`.`Inscripcion` (`id_inscripcion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_medio_pago`
    FOREIGN KEY (`Fk_id_medio_pago`)
    REFERENCES `mydb`.`Medio_de_pago` (`id_medio_de_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_estado_pago`
    FOREIGN KEY (`Fk_id_estado_pago`)
    REFERENCES `mydb`.`Estado_pago` (`id_estado_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`ventas_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ventas_productos` (
  `id_venta_producto` INT NOT NULL AUTO_INCREMENT,
  `fecha_venta` DATETIME NULL,
  `Fk_id_usuario` INT NULL,
  PRIMARY KEY (`id_venta_producto`),
  INDEX `Fk_id_usuario_idx` (`Fk_id_usuario` ASC),
  CONSTRAINT `Fk_id_usuario_venta`
    FOREIGN KEY (`Fk_id_usuario`)
    REFERENCES `mydb`.`Usuarios` (`id_usuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Marca`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Marca` (
  `id_Marca` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NULL,
  `Fk_id_pais` INT NULL,
  PRIMARY KEY (`id_Marca`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Categoria` (
  `id_categoria` INT NOT NULL AUTO_INCREMENT,
  `nombre_categoria` VARCHAR(45) NULL,
  `Descripcion` TEXT NULL,
  PRIMARY KEY (`id_categoria`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Proveedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Proveedor` (
  `id_Proveedor` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NULL,
  `telefono` INT NULL,
  `Proveedorcol` VARCHAR(45) NULL,
  PRIMARY KEY (`id_Proveedor`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Productos` (
  `id_producto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(85) NULL,
  `Fk_id_categoria` INT NULL,
  `Fk_id_marca` INT NULL,
  `Fk_id_proveedor` INT NULL,
  `precio` DECIMAL NULL,
  `Stock_min` INT NULL,
  `stock_actual` INT NULL,
  `stock_mac` INT NULL,
  `precio_compra` DECIMAL NULL,
  `precio_venta` DECIMAL NULL,
  PRIMARY KEY (`id_producto`),
  INDEX `Fk_id_marca_idx` (`Fk_id_marca` ASC),
  INDEX `Fk_id_categoria_idx` (`Fk_id_categoria` ASC),
  INDEX `Fk_id_proveedor_idx` (`Fk_id_proveedor` ASC),
  CONSTRAINT `Fk_id_marca`
    FOREIGN KEY (`Fk_id_marca`)
    REFERENCES `mydb`.`Marca` (`id_Marca`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_categoria`
    FOREIGN KEY (`Fk_id_categoria`)
    REFERENCES `mydb`.`Categoria` (`id_categoria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_proveedor`
    FOREIGN KEY (`Fk_id_proveedor`)
    REFERENCES `mydb`.`Proveedor` (`id_Proveedor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Detalle_venta_producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Detalle_venta_producto` (
  `id_detalle_venta_producto` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_usuario` INT NULL,
  `Fk_id_ventas_producto` INT NULL,
  `fk_id_producto` INT NULL,
  `amount` INT NULL, -- Cambiado nombre técnico interno si es requerido, o mantenido como cantidad
  `cantidad` INT NULL,
  `precio` DECIMAL NULL,
  `sub_total` DECIMAL NULL,
  PRIMARY KEY (`id_detalle_venta_producto`),
  INDEX `Fk_id_venta_productos_idx` (`Fk_id_ventas_producto` ASC),
  INDEX `Fk_id_productos_idx` (`fk_id_producto` ASC),
  CONSTRAINT `Fk_id_venta_productos`
    FOREIGN KEY (`Fk_id_ventas_producto`)
    REFERENCES `mydb`.`ventas_productos` (`id_venta_producto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_productos`
    FOREIGN KEY (`fk_id_producto`)
    REFERENCES `mydb`.`Productos` (`id_producto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Pagos_productos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Pagos_productos` (
  `id_Pagos_productos` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_venta_producto` INT NULL,
  `Fk_id_medio_pago` INT NULL,
  `fecha_pago` DATETIME NULL,
  `total` VARCHAR(45) NULL,
  `Fk_id_estado_pago` INT NULL,
  `Pagos_productoscol` VARCHAR(45) NULL,
  PRIMARY KEY (`id_Pagos_productos`),
  INDEX `Fk_id_venta_producto_idx` (`Fk_id_venta_producto` ASC),
  INDEX `Fk_id_medio_pago_idx` (`Fk_id_medio_pago` ASC),
  INDEX `Fk_id_estado_pago_idx` (`Fk_id_estado_pago` ASC),
  CONSTRAINT `Fk_id_venta_producto`
    FOREIGN KEY (`Fk_id_venta_producto`)
    REFERENCES `mydb`.`ventas_productos` (`id_venta_producto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_medio_pago_prod`
    FOREIGN KEY (`Fk_id_medio_pago`)
    REFERENCES `mydb`.`Medio_de_pago` (`id_medio_de_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_estado_pago_prod`
    FOREIGN KEY (`Fk_id_estado_pago`)
    REFERENCES `mydb`.`Estado_pago` (`id_estado_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Ejercicios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Ejercicios` (
  `id_Ejercicios` INT NOT NULL AUTO_INCREMENT,
  `Nombre_ejercicio` VARCHAR(80) NULL,
  `grupo_muscular` VARCHAR(45) NULL,
  `descripcion` TEXT NULL,
  PRIMARY KEY (`id_Ejercicios`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Rutinas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Rutinas` (
  `id_rutinas` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_usuario` INT NULL,
  `nombre_rutina` VARCHAR(105) NULL,
  `fecha_creacion` DATE NULL,
  PRIMARY KEY (`id_rutinas`),
  INDEX `Fk_id_usuario_idx` (`Fk_id_usuario` ASC),
  CONSTRAINT `Fk_id_usuario_rutina`
    FOREIGN KEY (`Fk_id_usuario`)
    REFERENCES `mydb`.`Usuarios` (`id_usuarios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Detalle_rutinas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Detalle_rutinas` (
  `id_detalle_rutinas` INT NOT NULL AUTO_INCREMENT,
  `FK_id_rutina` INT NULL,
  `repeticiones` INT NULL,
  `series` INT NULL,
  `dia_semana` DATE NULL,
  `descancso` VARCHAR(45) NULL,
  `Fk_id_ejercicio` INT NULL,
  `Detalle_rutinascol` VARCHAR(45) NULL,
  PRIMARY KEY (`id_detalle_rutinas`),
  INDEX `Fk_id_ejercicio_idx` (`Fk_id_ejercicio` ASC),
  INDEX `Fk_id_rutina_idx` (`FK_id_rutina` ASC),
  CONSTRAINT `Fk_id_ejercicio`
    FOREIGN KEY (`Fk_id_ejercicio`)
    REFERENCES `mydb`.`Ejercicios` (`id_Ejercicios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_rutina`
    FOREIGN KEY (`FK_id_rutina`)
    REFERENCES `mydb`.`Rutinas` (`id_rutinas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Detalle_trabajo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Detalle_trabajo` (
  `id_detalle_trabajo` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_empleado` INT NULL,
  `Fk_id_trabajo` INT NULL,
  PRIMARY KEY (`id_detalle_trabajo`),
  INDEX `Fk_id_trabajo_idx` (`Fk_id_trabajo` ASC),
  INDEX `Fk_id_empleado_idx` (`Fk_id_empleado` ASC),
  CONSTRAINT `Fk_id_trabajo`
    FOREIGN KEY (`Fk_id_trabajo`)
    REFERENCES `mydb`.`Trabajos` (`id_trabajo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_empleado_trabajo`
    FOREIGN KEY (`Fk_id_empleado`)
    REFERENCES `mydb`.`Empleados` (`id_empleado`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Maquinas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Maquinas` (
  `id_maquinas` INT NOT NULL AUTO_INCREMENT,
  `Nombre_maquina` VARCHAR(65) NULL,
  `descripcion` TEXT NULL,
  PRIMARY KEY (`id_maquinas`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Tecnicos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Tecnicos` (
  `id_Tecnicos` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(145) NULL,
  `Apellido` VARCHAR(45) NULL,
  `Telefono` INT NULL,
  PRIMARY KEY (`id_Tecnicos`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Mantenimiento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Mantenimiento` (
  `id_Mantenimiento` INT NOT NULL AUTO_INCREMENT,
  `Fecha_ultimo_mantenimiento` DATETIME NULL,
  `fecha_proximo_mantenimiento` DATETIME NULL,
  `Costo_mantenimiento` DECIMAL NULL,
  `Fk_id_maquina` INT NULL,
  `Fk_id_tecnico` INT NULL,
  PRIMARY KEY (`id_Mantenimiento`),
  INDEX `Fk_id_tecnico_idx` (`Fk_id_tecnico` ASC),
  INDEX `Fk_id_maquina_idx` (`Fk_id_maquina` ASC),
  CONSTRAINT `Fk_id_tecnico`
    FOREIGN KEY (`Fk_id_tecnico`)
    REFERENCES `mydb`.`Tecnicos` (`id_Tecnicos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_maquina`
    FOREIGN KEY (`Fk_id_maquina`)
    REFERENCES `mydb`.`Maquinas` (`id_maquinas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Caja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Caja` (
  `id_Caja` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_empleado_apertura` INT NULL,
  `Fk_id_empleado_cierre` INT NULL,
  `fecha_apertura` DATETIME NULL,
  `fecha_cierre` DATETIME NULL,
  `Monto_inicial` DECIMAL NULL,
  `monto_final_sistema` DECIMAL NULL,
  `monto_final_real` DECIMAL NULL,
  `observaiones` TEXT NULL,
  PRIMARY KEY (`id_Caja`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Historial_caja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Historial_caja` (
  `id_Historial_caja` INT NOT NULL AUTO_INCREMENT,
  `Fk_id_caja` INT NULL,
  `Tipo` VARCHAR(45) NULL,
  `Monto` DECIMAL NULL,
  `fk_id_metodo_pago` INT NULL,
  `Fk_id_pagos` INT NULL,
  `Fk_id_pagos_producto` INT NULL,
  `fecha_hora` DATETIME NULL,
  PRIMARY KEY (`id_Historial_caja`),
  INDEX `Fk_id_caja_idx` (`Fk_id_caja` ASC),
  INDEX `Fk_id_metodo_pago_idx` (`fk_id_metodo_pago` ASC),
  INDEX `Fk_id_pago_idx` (`Fk_id_pagos` ASC),
  INDEX `Fk_id_pago_producto_idx` (`Fk_id_pagos_producto` ASC),
  CONSTRAINT `Fk_id_caja`
    FOREIGN KEY (`Fk_id_caja`)
    REFERENCES `mydb`.`Caja` (`id_Caja`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_metodo_pago`
    FOREIGN KEY (`fk_id_metodo_pago`)
    REFERENCES `mydb`.`Medio_de_pago` (`id_medio_de_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_pago`
    FOREIGN KEY (`Fk_id_pagos`)
    REFERENCES `mydb`.`Pagos` (`id_Pagos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Fk_id_pago_producto`
    FOREIGN KEY (`Fk_id_pagos_producto`)
    REFERENCES `mydb`.`Pagos_productos` (`id_Pagos_productos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;