-- ============================================================
-- SCRIPT DE ACTUALIZACIÓN PARA tienda_online
-- Ejecutar en phpMyAdmin → seleccionar tienda_online → SQL
-- ============================================================

USE tienda_online;

-- 1. Agregar columna id_transaccion a tabla pago (si no existe)
ALTER TABLE pago 
    ADD COLUMN IF NOT EXISTS id_transaccion VARCHAR(100) NULL;

-- 2. Verificar y corregir estructura de metodo_pago
--    Primero limpiamos y reinsertamos los métodos correctos
TRUNCATE TABLE metodo_pago;

INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES 
(1, 'TARJETA',       'tarjeta'),
(2, 'YAPE',          'monedero'),
(3, 'PLIN',          'monedero'),
(4, 'TRANSFERENCIA', 'transferencia'),
(5, 'ENTREGA',       'entrega');

-- 3. Agregar sucursales si la tabla está vacía
INSERT IGNORE INTO sucursal (nombre, direccion, ciudad, departamento) VALUES
('Sede 1', 'Av. Lima 123',      'Lima', 'Lima'),
('Sede 2', 'Jr. Cusco 456',     'Lima', 'Lima'),
('Sede 3', 'Calle Piura 789',   'Lima', 'Lima');

-- 4. Verificar que la tabla venta tenga las columnas necesarias
ALTER TABLE venta 
    MODIFY COLUMN tipo_entrega ENUM('recojo en tienda','domicilio','tienda') NOT NULL;

-- Mensaje de confirmación
SELECT 'Base de datos tienda_online actualizada correctamente' AS resultado;
