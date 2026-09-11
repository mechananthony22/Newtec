DROP DATABASE IF EXISTS tienda_online;

CREATE DATABASE IF NOT EXISTS tienda_online;

-- Usar la base de datos
USE tienda_online;
-- 1. Roles -- nueva tabla para mas dinamismo
-- (rol: admin, cliente, trabajador, proveedor, vendedor)
CREATE TABLE rol (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  estado ENUM('activo', 'suspendido') DEFAULT 'activo',
  create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  update_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1. USUARIO GENERAL
CREATE TABLE usuario (
  id_us INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellidos VARCHAR(100) NOT NULL,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  dni VARCHAR(8) NOT NULL UNIQUE,
  telefono VARCHAR(15),
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  id_rol INT NOT NULL,
  estado ENUM('pendiente', 'activo') DEFAULT 'pendiente',
  codigo_verificacion VARCHAR(6) DEFAULT NULL,
  intentos_fallidos INT DEFAULT 0,
  bloqueo_until TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. SUCURSAL
CREATE TABLE sucursal (
  id_sucur INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  ciudad VARCHAR(100),
  departamento VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 5. CATEGORÍA
CREATE TABLE categoria (
  id_cat INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. SUBCATEGORÍA
CREATE TABLE subcategoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  id_categoria INT,
  FOREIGN KEY (id_categoria) REFERENCES categoria(id_cat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. MARCA
CREATE TABLE marca (
  id_mar INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. MODELO
CREATE TABLE modelo (
  id_mod INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  id_marca INT,
  FOREIGN KEY (id_marca) REFERENCES marca(id_mar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. PRODUCTO
CREATE TABLE producto (
  id_pro INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10,2) NOT NULL,
  stock INT DEFAULT 0,
  imagen VARCHAR(255),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  id_subcategoria INT,
  id_marca INT,
  id_modelo INT,
  FOREIGN KEY (id_subcategoria) REFERENCES subcategoria(id),
  FOREIGN KEY (id_marca) REFERENCES marca(id_mar),
  FOREIGN KEY (id_modelo) REFERENCES modelo(id_mod)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 10. MÉTODO DE PAGO
CREATE TABLE metodo_pago (
  id_met INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  tipo ENUM('tarjeta', 'transferencia', 'monedero', 'entrega') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 13. DIRECCIÓN ENTREGA
CREATE TABLE direccion_entrega (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  departamento VARCHAR(50),
  provincia VARCHAR(50),
  distrito VARCHAR(50),
  calle VARCHAR(100),
  numero VARCHAR(10),
  piso VARCHAR(10),
  referencia TEXT,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_us)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 11. VENTA
CREATE TABLE venta (
  id_ven INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_direccion INT,
  total DECIMAL(10,2) NOT NULL,
  metodo_pago_id INT,
  tipo_entrega ENUM('recojo en tienda', 'domicilio') NOT NULL,
  id_sucursal INT NULL,
  estado ENUM('pendiente', 'enviado', 'entregado', 'cancelado') DEFAULT 'pendiente',
  fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_entrega TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_us),
  FOREIGN KEY (id_direccion) REFERENCES direccion_entrega(id),
  FOREIGN KEY (metodo_pago_id) REFERENCES metodo_pago(id_met),
  FOREIGN KEY (id_sucursal) REFERENCES sucursal(id_sucur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 12. DETALLE DE VENTA
CREATE TABLE detalle_venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT,
  id_producto INT,
  cantidad INT,
  precio_unitario DECIMAL(10,2),
  FOREIGN KEY (id_venta) REFERENCES venta(id_ven),
  FOREIGN KEY (id_producto) REFERENCES producto(id_pro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- 14. PAGO
CREATE TABLE pago (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT,
  numero_tarjeta VARCHAR(20),
  numero_telefono VARCHAR(15),
  id_transaccion VARCHAR(100),
  FOREIGN KEY (id_venta) REFERENCES venta(id_ven)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 15. PROVEEDOR
CREATE TABLE proveedor (
  id_pro INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  ruc VARCHAR(11) NOT NULL,
  telefono VARCHAR(15),
  direccion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 18. CARRITO DE COMPRAS
CREATE TABLE carrito (
  id_carrito INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  estado ENUM('activo', 'abandonado', 'finalizado') DEFAULT 'activo',
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_us)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 19. DETALLE CARRITO
CREATE TABLE detalle_carrito (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_carrito INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_total DECIMAL(10,2) NOT NULL,
  fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_carrito) REFERENCES carrito(id_carrito) ON DELETE CASCADE,
  FOREIGN KEY (id_producto) REFERENCES producto(id_pro),
  UNIQUE KEY uk_carrito_producto (id_carrito, id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- INSERTAR ROLES
INSERT INTO rol (nombre) VALUES
('admin'),
('cliente'),
('trabajador'),
('proveedor'),
('vendedor');

-- USUARIO------------------------------------------

INSERT INTO usuario (id_us, nombre, apellidos, correo, contrasena, dni, telefono, fecha_registro, id_rol, estado, codigo_verificacion)
VALUES
(1, 'Anthony', 'Mechan Parra', 'admin@uss.edu.pe', '$2y$10$123456789012345678901u1234567890abcd12345678abcde123', '17639560', '987654321', '2025-06-16 05:41:26', 1, 'activo', NULL),
(2, 'Carlos', 'Vega Torres', 'admin1@gmail.com', '$2y$10$1234567890ABCDEfghijk1234567890LMNOPQRSTUVabcde1234', '94653652', '912345678', '2025-06-16 05:50:46', 2, 'activo', NULL),
(3, 'Arni', 'Flores Ramos', 'arni21154@gmail.com', '$2y$10$9876543210QWERTYuiopasd9876543210zxcvb1234567lmnop', '77327894', '987456321', '2025-06-16 06:26:34', 1, 'activo', NULL),
(4, 'Arni', 'Flores Torres', 'arni215s4@gmail.com', '$2y$10$9999999999HHHHHHHHHHHHH9999999999jjjjjjjjjjjjjjjjj', '77327823', '945612378', '2025-06-16 07:06:12', 2, 'activo', NULL),
(5, 'Jose', 'Perez Mendez', 'sd@sad', '$2y$10$555555555555555555555555555555555555555555555555', '12323232', '963852741', '2025-06-16 07:06:59', 2, 'activo', NULL),
(6, 'Anthony', 'Mechan Parra', 'anthony@gmail.com', '$2y$10$Cw23Pn7.4ufjHw0V1sp/pe0RNF.htHW6xUN1F/9mIvuco8vmcZVe.', '77327896', '987654321', '2025-06-16 07:11:37', 1, 'activo', NULL),

-- contraseña = 123
(7, 'junior', 'usuario', 'usuario@gmail.com', '$2y$10$65mv1myKRkI2UwJawu.8sub2DF8P2EbSdc5EXV530pzX1ukJR9sby', '12323999', '963852741', '2025-06-16 07:11:37', 2, 'activo', NULL),
(8, 'jr', 'usuario 2', 'usuario2@gmail.com', '$2y$10$65mv1myKRkI2UwJawu.8sub2DF8P2EbSdc5EXV530pzX1ukJR9sby', '12323635', '963852869', '2025-06-16 07:11:37', 2, 'activo', NULL),
(9, 'admin', 'admin', 'admin@gmail.com', '$2y$10$65mv1myKRkI2UwJawu.8sub2DF8P2EbSdc5EXV530pzX1ukJR9sby', '12323000', '963852741', '2025-06-16 07:11:37', 1, 'activo', NULL);


INSERT INTO direccion_entrega (id_usuario, departamento, provincia, distrito, calle, numero, piso, referencia)
VALUES
(7, 'Lambayeque', 'Chiclayo', 'Chiclayo', 'Av. Chiclayo', '123', '1', 'Referencia 1');

-- CATEGORIAS----------------------------------------------------------------

INSERT INTO categoria (id_cat, nombre) VALUES (1, 'VIDEOVIGILANCIA');
INSERT INTO categoria (id_cat, nombre) VALUES (2, 'GAMER');
INSERT INTO categoria (id_cat, nombre) VALUES (3, 'ALMACENAMIENTO');
INSERT INTO categoria (id_cat, nombre) VALUES (4, 'CABLEADO Y ESTRUCTURADO');
INSERT INTO categoria (id_cat, nombre) VALUES (5, 'CONTROL DE ACCESO');

-- Insertar SUBCATEGORIAS ---------------------------------------------------------------
-- VIDEOVIGILANCIA
INSERT IGNORE INTO subcategoria (id, nombre, id_categoria) VALUES 
(1, 'Cámaras IP', 1),
(2, 'Grabadores NVR', 1),
(3, 'Accesorios de Vigilancia', 1),
(4, 'Monitores de Seguridad', 1),
(5, 'Alarmas', 1);

-- GAMER
INSERT IGNORE INTO subcategoria (id, nombre, id_categoria) VALUES 
(6, 'Teclados Gamer', 2),
(7, 'Mouses Gamer', 2),
(8, 'Parlantes Gamer', 2),
(9, 'Audífonos Gamer', 2),
(10, 'Pad Mouse', 2);

-- ALMACENAMIENTO
INSERT IGNORE INTO subcategoria (id, nombre, id_categoria) VALUES 
(11, 'Discos Duros Externos', 3),
(12, 'Memorias USB', 3),
(13, 'Memorias SD', 3),
(14, 'Discos SSD', 3),
(15, 'Discos HDD', 3);

-- CABLEADO Y ESTRUCTURADO
INSERT IGNORE INTO subcategoria (id, nombre, id_categoria) VALUES 
(16, 'Cables UTP', 4),
(17, 'Conectores RJ45', 4),
(18, 'Patch Panels', 4),
(19, 'Cables Contra Incendios', 4),
(20, 'Canaletas', 4);

-- CONTROL DE ACCESO
INSERT IGNORE INTO subcategoria (id, nombre, id_categoria) VALUES 
(21, 'Tags de Proximidad', 5),
(22, 'Intercomunicadores', 5),
(23, 'Cerraduras Inalámbricas', 5),
(24, 'Lector de Huella Digital', 5),
(25, 'Módulos de Control de Acceso', 5);

-- MARCAS-----------------------------------------------
INSERT INTO marca (id_mar, nombre) VALUES (1, 'Hikvision');
INSERT INTO marca (id_mar, nombre) VALUES (2, 'Dahua');
INSERT INTO marca (id_mar, nombre) VALUES (3, 'Panduit');
INSERT INTO marca (id_mar, nombre) VALUES (4, 'Cybertel');
INSERT INTO marca (id_mar, nombre) VALUES (5, 'Halion');
INSERT INTO marca (id_mar, nombre) VALUES (6, 'Kingston');
INSERT INTO marca (id_mar, nombre) VALUES (7, 'Seagate');
INSERT INTO marca (id_mar, nombre) VALUES (8, 'Western Digital');
INSERT INTO marca (id_mar, nombre) VALUES (9, 'TP-Link');
INSERT INTO marca (id_mar, nombre) VALUES (10, 'Logitech');


-- MODELO (FALTA COMPLETAR) -----------------------------------------------

-- Hikvision
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (1, 'DS-2CD2043G0', 1);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (2, 'DS-7608NI-K2', 1);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (3, 'DS-K1T804', 1);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (4, 'DS-7732NI-I4', 1);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (5, 'DS-2CE16D0T', 1);

-- Dahua
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (6, 'IPC-HFW1230S', 2);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (7, 'XVR5108HS', 2);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (8, 'ASR1102A', 2);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (9, 'DHI-VTH5221', 2);
INSERT INTO modelo (id_mod, nombre, id_marca) VALUES (10, 'HAC-HDW1200EM', 2);

-- PRODUCTO-----------------------------------------------------------
-- Productos de ejemplo para probar el sistema dinámico
-- Estos productos aparecerán automáticamente en la tienda virtual

-- Productos de VIDEOVIGILANCIA
INSERT INTO producto (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
VALUES 
('Cámara IP Hikvision DS-2CD2043G0', 'Cámara de videovigilancia 4MP para exteriores con visión nocturna', 320.00, 25, 'VIDEOVIGILANCIA/CAMARAS/Cámara Anti-vandalismo.png', 1, 1, 1),
('Cámara IP Dahua IPC-HFW1230S', 'Cámara de vigilancia WiFi 1080P con audio integrado', 280.00, 30, 'VIDEOVIGILANCIA/CAMARAS/Cámara c.png', 1, 2, 6),
('NVR Hikvision DS-7608NI-K2', 'Grabador de video en red 8 canales 4K', 450.00, 15, 'VIDEOVIGILANCIA/NVR/NVR 16 Canales.png', 2, 1, 2),
('Monitor de Seguridad 19"', 'Monitor profesional para videovigilancia', 180.00, 20, 'VIDEOVIGILANCIA/MONITORES/Monitor 1.png', 4, 1, 1),
('Cable Ethernet Cat6 100m', 'Cable de red para instalaciones de videovigilancia', 85.00, 50, 'VIDEOVIGILANCIA/ACCESORIOS DE VIGILANCIA/Cable Ethernet Cat6.png', 3, 3, 1);

-- Productos de GAMER
INSERT INTO producto (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
VALUES 
('Teclado Gamer Logitech G Pro', 'Teclado mecánico RGB para gaming profesional', 120.00, 40, 'GAMER/TECLADOS/Teclado1.png', 6, 10, 1),
('Mouse Gamer Logitech G502 Hero', 'Mouse gaming con sensor HERO 25K', 95.00, 35, 'GAMER/mouse/Logitech G502 Hero.png', 7, 10, 2),
('Audífonos Gamer Corsair HS60', 'Audífonos gaming con micrófono desmontable', 75.00, 30, 'GAMER/AUDIFONOS/Audifonos1.png', 9, 10, 1),
('Parlantes Gamer 2.1', 'Sistema de audio gaming con subwoofer', 65.00, 25, 'GAMER/PARLANTES/Parlante1.png', 8, 10, 1),
('Pad Mouse Gamer RGB', 'Alfombrilla gaming con iluminación RGB', 25.00, 60, 'GAMER/PADMOUSE/PadMouse1.png', 10, 10, 1);

-- Productos de ALMACENAMIENTO
INSERT INTO producto (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
VALUES 
('SSD Kingston A2000 500GB', 'Disco sólido interno NVMe de alta velocidad', 85.00, 45, 'ALMACENAMIENTO/Memorias SD/Kingston A2000.png', 1, 6, 1),
('HDD Seagate Barracuda 1TB', 'Disco duro interno 7200 RPM', 45.00, 80, 'ALMACENAMIENTO/Discos HDD/Seagate Barracuda.png', 2, 7, 1),
('USB Kingston DataTraveler 32GB', 'Memoria USB de alta velocidad', 15.00, 100, 'ALMACENAMIENTO/Memorias USB/Kingston DataTraveler G4.png', 3, 6, 1),
('SD Card Kingston Canvas 64GB', 'Tarjeta de memoria para cámaras', 20.00, 75, 'ALMACENAMIENTO/Discos SSD/Kingston Canvas React.png', 4, 6, 1);

-- Productos de CABLEADO Y ESTRUCTURADO
INSERT INTO producto (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
VALUES 
('Cable UTP Cat6 305m', 'Cable de red categoría 6 para instalaciones', 120.00, 30, 'CABLADO/UTP/Cable UTP Cat6.png', 1, 3, 1),
('Patch Panel 24 Puertos', 'Panel de conexión para cableado estructurado', 45.00, 25, 'CABLADO/PATCH/Patch Panel 24 Puertos.png', 2, 3, 1),
('Conector RJ45 Crimpado', 'Conectores RJ45 para terminación de cables', 2.50, 200, 'CABLADO/RJ45/Conector RJ45 Crimpado.png', 3, 3, 1),
('Canaleta PVC 2x2', 'Canaleta para organizar cables', 8.00, 150, 'CABLADO/CANALETAS/Canaleta de PVC.png', 4, 3, 1);

-- Productos de CONTROL DE ACCESO
INSERT INTO producto (nombre, descripcion, precio, stock, imagen, id_subcategoria, id_marca, id_modelo)
VALUES 
('Lector Biométrico Facial', 'Control de acceso con reconocimiento facial', 350.00, 15, 'CONTROL DE ACCESO/LECTOR/BIOMETRICO FACIAL DE ASISTENCIA UFACE800 PLUS.png', 1, 1, 3),
('Cerradura Electrónica WiFi', 'Cerradura inteligente con control remoto', 280.00, 20, 'CONTROL DE ACCESO/CERRADURAS/Cerradura de Seguridad Inalámbrica.png', 2, 1, 1),
('Tag de Proximidad 125kHz', 'Tarjetas de acceso por proximidad', 3.50, 500, 'CONTROL DE ACCESO/TAGS/Tag de Proximidad 125 kHz.png', 3, 1, 1),
('Controlador de Acceso 2 Puertas', 'Módulo de control para 2 puertas', 180.00, 25, 'CONTROL DE ACCESO/MODULO/Controlador de Acceso 2 Puertas.png', 4, 1, 1); 

-- METODOS DE PAGO
INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES (1, 'TARJETA', 'tarjeta');
INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES (2, 'YAPE', 'monedero');
INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES (3, 'PLIN', 'monedero');
INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES (4, 'TRANSFERENCIA', 'transferencia');
INSERT INTO metodo_pago (id_met, nombre, tipo) VALUES (5, 'ENTREGA', 'entrega');


-- SUCURSAL
INSERT INTO sucursal (nombre, direccion, ciudad, departamento) VALUES
('Sede 1', 'Av. Lima 123', 'Lima', 'Lima'),
('Sede 2', 'Jr. Cusco 456', 'Lima', 'Lima'),
('Sede 3', 'Calle Piura 789', 'Lima', 'Lima');