-- =========================================================
-- SISTEMA DE GESTIÓN DE RESTAURANTE
-- Base de datos: db_restaurante
-- =========================================================

DROP DATABASE IF EXISTS db_restaurante;

CREATE DATABASE db_restaurante
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE db_restaurante;


-- =========================================================
-- 1. ROLES
-- =========================================================

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150),
    estado BOOLEAN NOT NULL DEFAULT TRUE
);


-- =========================================================
-- 2. USUARIOS
-- =========================================================

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
);


-- =========================================================
-- 3. CLIENTES
-- =========================================================

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    documento VARCHAR(20),
    telefono VARCHAR(20),
    email VARCHAR(150),
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- 4. CATEGORÍAS DE PRODUCTOS
-- =========================================================

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    estado BOOLEAN NOT NULL DEFAULT TRUE
);


-- =========================================================
-- 5. PRODUCTOS / PLATOS
-- =========================================================

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),
    disponible BOOLEAN NOT NULL DEFAULT TRUE,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
);


-- =========================================================
-- 6. MESAS
-- =========================================================

CREATE TABLE mesas (
    id_mesa INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    capacidad INT NOT NULL,
    estado ENUM(
        'DISPONIBLE',
        'OCUPADA',
        'RESERVADA',
        'INACTIVA'
    ) NOT NULL DEFAULT 'DISPONIBLE'
);


-- =========================================================
-- 7. PEDIDOS
-- =========================================================

CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,

    id_mesa INT,
    id_cliente INT,
    id_usuario INT NOT NULL,

    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    estado ENUM(
        'PENDIENTE',
        'PREPARANDO',
        'LISTO',
        'ENTREGADO',
        'CANCELADO'
    ) NOT NULL DEFAULT 'PENDIENTE',

    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    observacion VARCHAR(255),

    CONSTRAINT fk_pedido_mesa
        FOREIGN KEY (id_mesa)
        REFERENCES mesas(id_mesa),

    CONSTRAINT fk_pedido_cliente
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
);


-- =========================================================
-- 8. DETALLE DEL PEDIDO
-- =========================================================

CREATE TABLE detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,

    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,

    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    observacion VARCHAR(255),

    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido),

    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
);


-- =========================================================
-- 9. PAGOS
-- =========================================================

CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,

    id_pedido INT NOT NULL,

    metodo_pago ENUM(
        'EFECTIVO',
        'TARJETA',
        'TRANSFERENCIA',
        'OTRO'
    ) NOT NULL,

    monto DECIMAL(10,2) NOT NULL,

    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    referencia VARCHAR(100),

    CONSTRAINT fk_pago_pedido
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido)
);


-- =========================================================
-- 10. PROVEEDORES
-- =========================================================

CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,

    razon_social VARCHAR(150) NOT NULL,
    documento VARCHAR(20),
    telefono VARCHAR(20),
    email VARCHAR(150),
    direccion VARCHAR(255),

    estado BOOLEAN NOT NULL DEFAULT TRUE,

    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- 11. INSUMOS
-- =========================================================

CREATE TABLE insumos (
    id_insumo INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(150) NOT NULL,

    unidad_medida ENUM(
        'UNIDAD',
        'KG',
        'GRAMOS',
        'LITROS',
        'ML'
    ) NOT NULL,

    stock_actual DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_minimo DECIMAL(10,2) NOT NULL DEFAULT 0,

    costo_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,

    estado BOOLEAN NOT NULL DEFAULT TRUE
);


-- =========================================================
-- 12. COMPRAS
-- =========================================================

CREATE TABLE compras (
    id_compra INT AUTO_INCREMENT PRIMARY KEY,

    id_proveedor INT NOT NULL,
    id_usuario INT NOT NULL,

    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,

    estado ENUM(
        'REGISTRADA',
        'ANULADA'
    ) NOT NULL DEFAULT 'REGISTRADA',

    CONSTRAINT fk_compra_proveedor
        FOREIGN KEY (id_proveedor)
        REFERENCES proveedores(id_proveedor),

    CONSTRAINT fk_compra_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
);


-- =========================================================
-- 13. DETALLE DE COMPRA
-- =========================================================

CREATE TABLE detalle_compra (
    id_detalle_compra INT AUTO_INCREMENT PRIMARY KEY,

    id_compra INT NOT NULL,
    id_insumo INT NOT NULL,

    cantidad DECIMAL(10,2) NOT NULL,
    costo_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_detalle_compra
        FOREIGN KEY (id_compra)
        REFERENCES compras(id_compra),

    CONSTRAINT fk_detalle_compra_insumo
        FOREIGN KEY (id_insumo)
        REFERENCES insumos(id_insumo)
);


-- =========================================================
-- 14. MOVIMIENTOS DE INVENTARIO
-- =========================================================

CREATE TABLE movimientos_inventario (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,

    id_insumo INT NOT NULL,

    tipo ENUM(
        'ENTRADA',
        'SALIDA',
        'AJUSTE'
    ) NOT NULL,

    cantidad DECIMAL(10,2) NOT NULL,

    motivo VARCHAR(255),

    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_usuario INT NOT NULL,

    CONSTRAINT fk_movimiento_insumo
        FOREIGN KEY (id_insumo)
        REFERENCES insumos(id_insumo),

    CONSTRAINT fk_movimiento_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
);


-- =========================================================
-- DATOS INICIALES
-- =========================================================

INSERT INTO roles (nombre, descripcion)
VALUES
('Administrador', 'Acceso completo al sistema'),
('Cajero', 'Gestión de pedidos y pagos'),
('Mesero', 'Registro y seguimiento de pedidos'),
('Cocina', 'Visualización y preparación de pedidos');


INSERT INTO categorias (nombre, descripcion)
VALUES
('Entradas', 'Entradas y aperitivos'),
('Platos principales', 'Platos principales'),
('Bebidas', 'Bebidas frías y calientes'),
('Postres', 'Postres');


INSERT INTO mesas (numero, capacidad)
VALUES
(1, 2),
(2, 2),
(3, 4),
(4, 4),
(5, 6),
(6, 6);


INSERT INTO productos
(id_categoria, nombre, descripcion, precio)
VALUES
(1, 'Tequeños', 'Porción de tequeños', 15.00),
(2, 'Lomo saltado', 'Lomo saltado tradicional', 35.00),
(2, 'Arroz con pollo', 'Arroz con pollo tradicional', 25.00),
(3, 'Chicha morada', 'Vaso de chicha morada', 6.00),
(3, 'Gaseosa', 'Botella de gaseosa', 5.00),
(4, 'Torta de chocolate', 'Porción de torta', 12.00);


INSERT INTO insumos
(nombre, unidad_medida, stock_actual, stock_minimo, costo_unitario)
VALUES
('Carne de res', 'KG', 20, 5, 28.00),
('Arroz', 'KG', 50, 10, 4.50),
('Papa', 'KG', 30, 8, 3.50),
('Aceite', 'LITROS', 15, 5, 9.00),
('Pollo', 'KG', 25, 5, 12.00);