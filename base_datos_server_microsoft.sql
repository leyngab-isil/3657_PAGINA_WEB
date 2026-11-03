/* =========================================================
   SISTEMA DE GESTION DE RESTAURANTE
   MOTOR: MICROSOFT SQL SERVER
   BASE DE DATOS: db_restaurante
   ========================================================= */


/* =========================================================
   0. ELIMINAR Y CREAR BASE DE DATOS
   ========================================================= */

USE master;
GO

IF DB_ID('db_restaurante') IS NOT NULL
BEGIN
    ALTER DATABASE db_restaurante
        SET SINGLE_USER
        WITH ROLLBACK IMMEDIATE;

    DROP DATABASE db_restaurante;
END;
GO

CREATE DATABASE db_restaurante;
GO

USE db_restaurante;
GO


/* =========================================================
   1. ROLES
   ========================================================= */

CREATE TABLE roles
(
    id_rol INT IDENTITY(1,1) NOT NULL,

    nombre NVARCHAR(50) NOT NULL,

    descripcion NVARCHAR(150) NULL,

    estado BIT NOT NULL
        CONSTRAINT DF_roles_estado DEFAULT 1,

    CONSTRAINT PK_roles
        PRIMARY KEY (id_rol),

    CONSTRAINT UQ_roles_nombre
        UNIQUE (nombre)
);
GO


/* =========================================================
   2. USUARIOS
   ========================================================= */

CREATE TABLE usuarios
(
    id_usuario INT IDENTITY(1,1) NOT NULL,

    id_rol INT NOT NULL,

    nombres NVARCHAR(100) NOT NULL,

    apellidos NVARCHAR(100) NOT NULL,

    email NVARCHAR(150) NOT NULL,

    password NVARCHAR(255) NOT NULL,

    estado BIT NOT NULL
        CONSTRAINT DF_usuarios_estado DEFAULT 1,

    fecha_creacion DATETIME2 NOT NULL
        CONSTRAINT DF_usuarios_fecha_creacion
        DEFAULT SYSDATETIME(),

    CONSTRAINT PK_usuarios
        PRIMARY KEY (id_usuario),

    CONSTRAINT UQ_usuarios_email
        UNIQUE (email),

    CONSTRAINT FK_usuarios_roles
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol)
);
GO


/* =========================================================
   3. CLIENTES
   ========================================================= */

CREATE TABLE clientes
(
    id_cliente INT IDENTITY(1,1) NOT NULL,

    nombres NVARCHAR(100) NOT NULL,

    apellidos NVARCHAR(100) NULL,

    documento NVARCHAR(20) NULL,

    telefono NVARCHAR(20) NULL,

    email NVARCHAR(150) NULL,

    estado BIT NOT NULL
        CONSTRAINT DF_clientes_estado DEFAULT 1,

    fecha_registro DATETIME2 NOT NULL
        CONSTRAINT DF_clientes_fecha_registro
        DEFAULT SYSDATETIME(),

    CONSTRAINT PK_clientes
        PRIMARY KEY (id_cliente)
);
GO


/* =========================================================
   4. CATEGORIAS
   ========================================================= */

CREATE TABLE categorias
(
    id_categoria INT IDENTITY(1,1) NOT NULL,

    nombre NVARCHAR(100) NOT NULL,

    descripcion NVARCHAR(255) NULL,

    estado BIT NOT NULL
        CONSTRAINT DF_categorias_estado DEFAULT 1,

    CONSTRAINT PK_categorias
        PRIMARY KEY (id_categoria)
);
GO


/* =========================================================
   5. PRODUCTOS / PLATOS
   ========================================================= */

CREATE TABLE productos
(
    id_producto INT IDENTITY(1,1) NOT NULL,

    id_categoria INT NOT NULL,

    nombre NVARCHAR(150) NOT NULL,

    descripcion NVARCHAR(255) NULL,

    precio DECIMAL(10,2) NOT NULL,

    imagen NVARCHAR(255) NULL,

    disponible BIT NOT NULL
        CONSTRAINT DF_productos_disponible DEFAULT 1,

    estado BIT NOT NULL
        CONSTRAINT DF_productos_estado DEFAULT 1,

    fecha_creacion DATETIME2 NOT NULL
        CONSTRAINT DF_productos_fecha_creacion
        DEFAULT SYSDATETIME(),

    CONSTRAINT PK_productos
        PRIMARY KEY (id_producto),

    CONSTRAINT FK_productos_categorias
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria),

    CONSTRAINT CHK_productos_precio
        CHECK (precio >= 0)
);
GO


/* =========================================================
   6. MESAS
   ========================================================= */

CREATE TABLE mesas
(
    id_mesa INT IDENTITY(1,1) NOT NULL,

    numero INT NOT NULL,

    capacidad INT NOT NULL,

    estado NVARCHAR(20) NOT NULL
        CONSTRAINT DF_mesas_estado
        DEFAULT N'DISPONIBLE',

    CONSTRAINT PK_mesas
        PRIMARY KEY (id_mesa),

    CONSTRAINT UQ_mesas_numero
        UNIQUE (numero),

    CONSTRAINT CHK_mesas_capacidad
        CHECK (capacidad > 0),

    CONSTRAINT CHK_mesas_estado
        CHECK
        (
            estado IN
            (
                N'DISPONIBLE',
                N'OCUPADA',
                N'RESERVADA',
                N'INACTIVA'
            )
        )
);
GO


/* =========================================================
   7. PEDIDOS
   ========================================================= */

CREATE TABLE pedidos
(
    id_pedido INT IDENTITY(1,1) NOT NULL,

    id_mesa INT NULL,

    id_cliente INT NULL,

    id_usuario INT NOT NULL,

    fecha_pedido DATETIME2 NOT NULL
        CONSTRAINT DF_pedidos_fecha_pedido
        DEFAULT SYSDATETIME(),

    estado NVARCHAR(20) NOT NULL
        CONSTRAINT DF_pedidos_estado
        DEFAULT N'PENDIENTE',

    subtotal DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_pedidos_subtotal
        DEFAULT 0.00,

    impuesto DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_pedidos_impuesto
        DEFAULT 0.00,

    total DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_pedidos_total
        DEFAULT 0.00,

    observacion NVARCHAR(255) NULL,

    CONSTRAINT PK_pedidos
        PRIMARY KEY (id_pedido),

    CONSTRAINT FK_pedidos_mesas
        FOREIGN KEY (id_mesa)
        REFERENCES mesas(id_mesa),

    CONSTRAINT FK_pedidos_clientes
        FOREIGN KEY (id_cliente)
        REFERENCES clientes(id_cliente),

    CONSTRAINT FK_pedidos_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),

    CONSTRAINT CHK_pedidos_estado
        CHECK
        (
            estado IN
            (
                N'PENDIENTE',
                N'PREPARANDO',
                N'LISTO',
                N'ENTREGADO',
                N'CANCELADO'
            )
        ),

    CONSTRAINT CHK_pedidos_subtotal
        CHECK (subtotal >= 0),

    CONSTRAINT CHK_pedidos_impuesto
        CHECK (impuesto >= 0),

    CONSTRAINT CHK_pedidos_total
        CHECK (total >= 0)
);
GO


/* =========================================================
   8. DETALLE DEL PEDIDO
   ========================================================= */

CREATE TABLE detalle_pedido
(
    id_detalle INT IDENTITY(1,1) NOT NULL,

    id_pedido INT NOT NULL,

    id_producto INT NOT NULL,

    cantidad INT NOT NULL,

    precio_unitario DECIMAL(10,2) NOT NULL,

    subtotal DECIMAL(10,2) NOT NULL,

    observacion NVARCHAR(255) NULL,

    CONSTRAINT PK_detalle_pedido
        PRIMARY KEY (id_detalle),

    CONSTRAINT FK_detalle_pedido_pedido
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido),

    CONSTRAINT FK_detalle_pedido_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto),

    CONSTRAINT CHK_detalle_pedido_cantidad
        CHECK (cantidad > 0),

    CONSTRAINT CHK_detalle_pedido_precio
        CHECK (precio_unitario >= 0),

    CONSTRAINT CHK_detalle_pedido_subtotal
        CHECK (subtotal >= 0)
);
GO


/* =========================================================
   9. PAGOS
   ========================================================= */

CREATE TABLE pagos
(
    id_pago INT IDENTITY(1,1) NOT NULL,

    id_pedido INT NOT NULL,

    metodo_pago NVARCHAR(30) NOT NULL,

    monto DECIMAL(10,2) NOT NULL,

    fecha_pago DATETIME2 NOT NULL
        CONSTRAINT DF_pagos_fecha_pago
        DEFAULT SYSDATETIME(),

    referencia NVARCHAR(100) NULL,

    CONSTRAINT PK_pagos
        PRIMARY KEY (id_pago),

    CONSTRAINT FK_pagos_pedidos
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido),

    CONSTRAINT CHK_pagos_metodo
        CHECK
        (
            metodo_pago IN
            (
                N'EFECTIVO',
                N'TARJETA',
                N'TRANSFERENCIA',
                N'OTRO'
            )
        ),

    CONSTRAINT CHK_pagos_monto
        CHECK (monto > 0)
);
GO


/* =========================================================
   10. PROVEEDORES
   ========================================================= */

CREATE TABLE proveedores
(
    id_proveedor INT IDENTITY(1,1) NOT NULL,

    razon_social NVARCHAR(150) NOT NULL,

    documento NVARCHAR(20) NULL,

    telefono NVARCHAR(20) NULL,

    email NVARCHAR(150) NULL,

    direccion NVARCHAR(255) NULL,

    estado BIT NOT NULL
        CONSTRAINT DF_proveedores_estado
        DEFAULT 1,

    fecha_registro DATETIME2 NOT NULL
        CONSTRAINT DF_proveedores_fecha_registro
        DEFAULT SYSDATETIME(),

    CONSTRAINT PK_proveedores
        PRIMARY KEY (id_proveedor)
);
GO


/* =========================================================
   11. INSUMOS
   ========================================================= */

CREATE TABLE insumos
(
    id_insumo INT IDENTITY(1,1) NOT NULL,

    nombre NVARCHAR(150) NOT NULL,

    unidad_medida NVARCHAR(20) NOT NULL,

    stock_actual DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_insumos_stock_actual
        DEFAULT 0,

    stock_minimo DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_insumos_stock_minimo
        DEFAULT 0,

    costo_unitario DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_insumos_costo_unitario
        DEFAULT 0,

    estado BIT NOT NULL
        CONSTRAINT DF_insumos_estado
        DEFAULT 1,

    CONSTRAINT PK_insumos
        PRIMARY KEY (id_insumo),

    CONSTRAINT CHK_insumos_unidad_medida
        CHECK
        (
            unidad_medida IN
            (
                N'UNIDAD',
                N'KG',
                N'GRAMOS',
                N'LITROS',
                N'ML'
            )
        ),

    CONSTRAINT CHK_insumos_stock_actual
        CHECK (stock_actual >= 0),

    CONSTRAINT CHK_insumos_stock_minimo
        CHECK (stock_minimo >= 0),

    CONSTRAINT CHK_insumos_costo
        CHECK (costo_unitario >= 0)
);
GO


/* =========================================================
   12. COMPRAS
   ========================================================= */

CREATE TABLE compras
(
    id_compra INT IDENTITY(1,1) NOT NULL,

    id_proveedor INT NOT NULL,

    id_usuario INT NOT NULL,

    fecha_compra DATETIME2 NOT NULL
        CONSTRAINT DF_compras_fecha_compra
        DEFAULT SYSDATETIME(),

    subtotal DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_compras_subtotal
        DEFAULT 0,

    impuesto DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_compras_impuesto
        DEFAULT 0,

    total DECIMAL(10,2) NOT NULL
        CONSTRAINT DF_compras_total
        DEFAULT 0,

    estado NVARCHAR(20) NOT NULL
        CONSTRAINT DF_compras_estado
        DEFAULT N'REGISTRADA',

    CONSTRAINT PK_compras
        PRIMARY KEY (id_compra),

    CONSTRAINT FK_compras_proveedores
        FOREIGN KEY (id_proveedor)
        REFERENCES proveedores(id_proveedor),

    CONSTRAINT FK_compras_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),

    CONSTRAINT CHK_compras_estado
        CHECK
        (
            estado IN
            (
                N'REGISTRADA',
                N'ANULADA'
            )
        ),

    CONSTRAINT CHK_compras_subtotal
        CHECK (subtotal >= 0),

    CONSTRAINT CHK_compras_impuesto
        CHECK (impuesto >= 0),

    CONSTRAINT CHK_compras_total
        CHECK (total >= 0)
);
GO


/* =========================================================
   13. DETALLE DE COMPRA
   ========================================================= */

CREATE TABLE detalle_compra
(
    id_detalle_compra INT IDENTITY(1,1) NOT NULL,

    id_compra INT NOT NULL,

    id_insumo INT NOT NULL,

    cantidad DECIMAL(10,2) NOT NULL,

    costo_unitario DECIMAL(10,2) NOT NULL,

    subtotal DECIMAL(10,2) NOT NULL,

    CONSTRAINT PK_detalle_compra
        PRIMARY KEY (id_detalle_compra),

    CONSTRAINT FK_detalle_compra_compra
        FOREIGN KEY (id_compra)
        REFERENCES compras(id_compra),

    CONSTRAINT FK_detalle_compra_insumo
        FOREIGN KEY (id_insumo)
        REFERENCES insumos(id_insumo),

    CONSTRAINT CHK_detalle_compra_cantidad
        CHECK (cantidad > 0),

    CONSTRAINT CHK_detalle_compra_costo
        CHECK (costo_unitario >= 0),

    CONSTRAINT CHK_detalle_compra_subtotal
        CHECK (subtotal >= 0)
);
GO


/* =========================================================
   14. MOVIMIENTOS DE INVENTARIO
   ========================================================= */

CREATE TABLE movimientos_inventario
(
    id_movimiento INT IDENTITY(1,1) NOT NULL,

    id_insumo INT NOT NULL,

    tipo NVARCHAR(20) NOT NULL,

    cantidad DECIMAL(10,2) NOT NULL,

    motivo NVARCHAR(255) NULL,

    fecha_movimiento DATETIME2 NOT NULL
        CONSTRAINT DF_movimientos_fecha
        DEFAULT SYSDATETIME(),

    id_usuario INT NOT NULL,

    CONSTRAINT PK_movimientos_inventario
        PRIMARY KEY (id_movimiento),

    CONSTRAINT FK_movimientos_insumos
        FOREIGN KEY (id_insumo)
        REFERENCES insumos(id_insumo),

    CONSTRAINT FK_movimientos_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),

    CONSTRAINT CHK_movimientos_tipo
        CHECK
        (
            tipo IN
            (
                N'ENTRADA',
                N'SALIDA',
                N'AJUSTE'
            )
        ),

    CONSTRAINT CHK_movimientos_cantidad
        CHECK (cantidad > 0)
);
GO


/* =========================================================
   DATOS INICIALES
   ========================================================= */


/* =========================================================
   ROLES
   ========================================================= */

INSERT INTO roles
(
    nombre,
    descripcion
)
VALUES
(N'Administrador', N'Acceso completo al sistema'),
(N'Cajero', N'Gestión de pedidos y pagos'),
(N'Mesero', N'Registro y seguimiento de pedidos'),
(N'Cocina', N'Visualización y preparación de pedidos');
GO


/* =========================================================
   CATEGORIAS
   ========================================================= */

INSERT INTO categorias
(
    nombre,
    descripcion
)
VALUES
(N'Entradas', N'Entradas y aperitivos'),
(N'Platos principales', N'Platos principales'),
(N'Bebidas', N'Bebidas frías y calientes'),
(N'Postres', N'Postres');
GO


/* =========================================================
   MESAS
   ========================================================= */

INSERT INTO mesas
(
    numero,
    capacidad
)
VALUES
(1, 2),
(2, 2),
(3, 4),
(4, 4),
(5, 6),
(6, 6);
GO


/* =========================================================
   PRODUCTOS
   ========================================================= */

INSERT INTO productos
(
    id_categoria,
    nombre,
    descripcion,
    precio
)
VALUES
(1, N'Tequeños', N'Porción de tequeños', 15.00),
(2, N'Lomo saltado', N'Lomo saltado tradicional', 35.00),
(2, N'Arroz con pollo', N'Arroz con pollo tradicional', 25.00),
(3, N'Chicha morada', N'Vaso de chicha morada', 6.00),
(3, N'Gaseosa', N'Botella de gaseosa', 5.00),
(4, N'Torta de chocolate', N'Porción de torta', 12.00);
GO


/* =========================================================
   INSUMOS
   ========================================================= */

INSERT INTO insumos
(
    nombre,
    unidad_medida,
    stock_actual,
    stock_minimo,
    costo_unitario
)
VALUES
(N'Carne de res', N'KG', 20.00, 5.00, 28.00),
(N'Arroz', N'KG', 50.00, 10.00, 4.50),
(N'Papa', N'KG', 30.00, 8.00, 3.50),
(N'Aceite', N'LITROS', 15.00, 5.00, 9.00),
(N'Pollo', N'KG', 25.00, 5.00, 12.00);
GO
