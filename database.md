# 📄 database.md

## 🗄️ BASE DE DATOS - MODERATIO

### Sistema de Gestión de Inventario y Punto de Venta

---

## 📋 ENUMS (Tipos de Datos Personalizados)

| Enum | Valores | Uso |
| :--- | :--- | :--- |
| `role_enum` | ADMINISTRATOR, CASHIER, WAREHOUSE | Roles de usuario |
| `cash_register_status` | OPEN, CLOSED | Estado de la caja |
| `sale_status` | COMPLETED, CANCELLED, PARTIALLY_RETURNED | Estado de la venta |
| `payment_method` | CASH, CARD, TRANSFER | Métodos de pago |
| `customer_type` | REGULAR, FREQUENT, WHOLESALER | Tipo de cliente |

---

## 📂 MÓDULO 1: SEGURIDAD Y USUARIOS

### Tabla `roles`

Almacena los roles disponibles en el sistema.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único del rol |
| `name` | role_enum [NOT NULL, UNIQUE] | Nombre del rol (ADMINISTRATOR, CASHIER, WAREHOUSE) |
| `description` | varchar(255) | Descripción del rol |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de creación del registro |
| `updated_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de última actualización |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (name)`

---

### Tabla `users`

Almacena los usuarios que operan el sistema.

| Campo        | Tipo                                   | Descripción                                                         |
|:-------------|:---------------------------------------|:--------------------------------------------------------------------|
| `id`         | int [PK, AI]                           | Identificador único del usuario                                     |
| `role_id`    | int [NOT NULL]                         | ID del rol asignado (FK → roles.id)                                 |
| `full_name`  | varchar(100) [NOT NULL]                | Nombre completo del usuario                                         |
| `email`      | varchar(50) [NOT NULL, UNIQUE]         | Nombre de usuario para login                                        |
| `password`   | varchar(255) [NOT NULL]                | Contraseña encriptada con Bcrypt/Argon2                             |
| `address`    | varchar(255)                           | direccion del usuario                                               |
| `DUI`        | varchar(10) [unique]                   | Indica el numero Unico De Identidad de la paersona                  |
| `Birthday`   | Date                                   | Ayuda a saber la edad de la persona y claro el dia de su cumpleaños |
| `is_active`  | boolean [DEFAULT: true]                | Indica si el usuario está activo                                    |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de creación del registro                                      |
| `updated_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de última actualización                                       |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (email)`
- `INDEX (is_active, role_id)`

**Relaciones:**
- `users.role_id` → `roles.id` (N:1)

---

### Tabla `customers`

Almacena la información de los clientes.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único del cliente |
| `first_name` | varchar(100) [NOT NULL] | Nombre del cliente |
| `last_name` | varchar(100) | Apellido del cliente |
| `tax_id` | varchar(20) [UNIQUE] | Número de NIT (para facturación DTE) |
| `phone` | varchar(20) | Número de teléfono |
| `email` | varchar(100) | Correo electrónico |
| `address` | text | Dirección del cliente |
| `customer_type` | customer_type [DEFAULT: 'REGULAR'] | Tipo de cliente (REGULAR, FREQUENT, WHOLESALER) |
| `is_active` | boolean [DEFAULT: true] | Indica si el cliente está activo |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de creación del registro |
| `updated_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de última actualización |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (tax_id)`
- `INDEX idx_customer_tax_id (tax_id)`
- `INDEX (first_name)`
- `INDEX (phone)`

---

## 📂 MÓDULO 2: INVENTARIO Y CATEGORÍAS

### Tabla `categories`

Almacena las categorías de productos con soporte para jerarquías (categorías anidadas).

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único de la categoría |
| `parent_category_id` | int [NULL] | ID de la categoría padre (NULL = categoría raíz) |
| `name` | varchar(100) [NOT NULL, UNIQUE] | Nombre de la categoría |
| `description` | text | Descripción de la categoría |
| `is_active` | boolean [DEFAULT: true] | Indica si la categoría está activa |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de creación del registro |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (name)`
- `INDEX (name)`
- `INDEX (parent_category_id)`

**Relaciones:**
- `categories.parent_category_id` → `categories.id` (Auto-relación, NULL permitido para raíces)

---

### Tabla `products`

Almacena el catálogo de productos con control de inventario.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único del producto |
| `category_id` | int [NOT NULL] | ID de la categoría asignada (FK → categories.id) |
| `barcode` | varchar(50) [UNIQUE] | Código de barras del producto (opcional) |
| `name` | varchar(150) [NOT NULL] | Nombre del producto |
| `description` | text | Descripción del producto |
| `purchase_price` | decimal(10,2) [NOT NULL] | Precio de compra (costo base) |
| `sale_price` | decimal(10,2) [NOT NULL] | Precio de venta al público |
| `current_stock` | int [NOT NULL, DEFAULT: 0] | Cantidad disponible en inventario |
| `min_stock` | int [NOT NULL, DEFAULT: 5] | Cantidad mínima para alertas de bajo stock |
| `has_tax` | boolean [DEFAULT: true] | Indica si el producto tiene IVA |
| `tax_percentage` | decimal(5,2) [DEFAULT: 13.00] | Porcentaje de IVA aplicable |
| `is_active` | boolean [DEFAULT: true] | Indica si el producto está activo |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de creación del registro |
| `updated_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha de última actualización |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (barcode)`
- `INDEX idx_product_barcode (barcode)`
- `INDEX idx_product_name (name)`
- `INDEX idx_stock_alert (current_stock, min_stock)`
- `INDEX (is_active, category_id)`
- `INDEX (is_active, current_stock)`

**Relaciones:**
- `products.category_id` → `categories.id` (N:1)

---

## 📂 MÓDULO 3: OPERACIONES DE CAJA (ARQUEO)

### Tabla `cash_registers`

Controla las sesiones de caja por usuario.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único de la caja |
| `user_id` | int [NOT NULL] | ID del usuario que opera la caja (FK → users.id) |
| `shift` | varchar(20) | Identificador del turno (MORNING, AFTERNOON, NIGHT) |
| `opening_amount` | decimal(10,2) [NOT NULL] | Monto con el que se abre la caja |
| `theoretical_closing_amount` | decimal(10,2) | Monto calculado por el sistema al cerrar |
| `actual_closing_amount` | decimal(10,2) | Monto físico contado por el cajero |
| `difference` | decimal(10,2) | Diferencia entre teórico y real (+ = sobrante, - = faltante) |
| `status` | cash_register_status [NOT NULL, DEFAULT: 'OPEN'] | Estado de la caja (OPEN/CLOSED) |
| `opening_date` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha y hora de apertura |
| `closing_date` | timestamp | Fecha y hora de cierre |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX idx_register_user_status (user_id, status)`
- `INDEX (opening_date)`

**Relaciones:**
- `cash_registers.user_id` → `users.id` (N:1)

---

## 📂 MÓDULO 4: VENTAS Y DETALLES (POS)

### Tabla `sales`

Registro de cada transacción de venta.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único de la venta |
| `cash_register_id` | int [NOT NULL] | ID de la caja donde se realizó (FK → cash_registers.id) |
| `user_id` | int [NOT NULL] | ID del usuario que realizó la venta (FK → users.id) |
| `customer_id` | int [NULL] | ID del cliente (FK → customers.id, NULL para ventas al mostrador) |
| `ticket_number` | varchar(30) [NOT NULL, UNIQUE] | Folio único del ticket/venta |
| `total` | decimal(10,2) [NOT NULL] | Total de la venta |
| `amount_received` | decimal(10,2) [NOT NULL] | Monto recibido por el cliente |
| `change_due` | decimal(10,2) [NOT NULL] | Cambio entregado al cliente |
| `payment_method` | payment_method [NOT NULL, DEFAULT: 'CASH'] | Método de pago usado |
| `status` | sale_status [NOT NULL, DEFAULT: 'COMPLETED'] | Estado de la venta |
| `observations` | text | Observaciones o notas del cajero |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha y hora de la venta |

**Índices:**
- `PRIMARY KEY (id)`
- `UNIQUE KEY (ticket_number)`
- `INDEX (ticket_number)`
- `INDEX idx_sales_date (created_at)`
- `INDEX (cash_register_id)`
- `INDEX (user_id)`
- `INDEX (customer_id)`
- `INDEX (status)`
- `INDEX (status, created_at)`
- `INDEX (payment_method, created_at)`

**Relaciones:**
- `sales.cash_register_id` → `cash_registers.id` (N:1)
- `sales.user_id` → `users.id` (N:1)
- `sales.customer_id` → `customers.id` (N:1, NULL permitido)

---

### Tabla `sale_details`

Detalle de los productos vendidos en cada venta.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único del detalle |
| `sale_id` | int [NOT NULL] | ID de la venta (FK → sales.id) |
| `product_id` | int [NOT NULL] | ID del producto vendido (FK → products.id) |
| `quantity` | int [NOT NULL] | Cantidad vendida del producto |
| `unit_price` | decimal(10,2) [NOT NULL] | Precio de venta unitario en el momento de la venta |
| `unit_cost` | decimal(10,2) [NOT NULL] | Costo de compra unitario en el momento de la venta |
| `discount` | decimal(10,2) [DEFAULT: 0.00] | Descuento aplicado al producto |
| `subtotal` | decimal(10,2) [NOT NULL] | Subtotal del producto (cantidad × unit_price - discount) |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX (sale_id, product_id)`
- `INDEX (product_id)`
- `INDEX idx_detail_product_sale (product_id, sale_id)`

**Relaciones:**
- `sale_details.sale_id` → `sales.id` (N:1)
- `sale_details.product_id` → `products.id` (N:1)

---

## 📂 MÓDULO 5: POST-VENTA (DEVOLUCIONES)

### Tabla `returns`

Registro de devoluciones de ventas.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único de la devolución |
| `sale_id` | int [NOT NULL] | ID de la venta original (FK → sales.id) |
| `user_id` | int [NOT NULL] | ID del usuario que autorizó la devolución (FK → users.id) |
| `cash_register_id` | int [NULL] | ID de la caja donde se realizó la devolución (FK → cash_registers.id) |
| `reason` | text [NOT NULL] | Motivo de la devolución |
| `total_returned` | decimal(10,2) [NOT NULL] | Monto total devuelto |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha y hora de la devolución |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX (sale_id)`
- `INDEX (cash_register_id)`
- `INDEX (created_at)`

**Relaciones:**
- `returns.sale_id` → `sales.id` (N:1)
- `returns.user_id` → `users.id` (N:1)
- `returns.cash_register_id` → `cash_registers.id` (N:1, NULL permitido)

---

### Tabla `return_details`

Detalle de los productos devueltos.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | int [PK, AI] | Identificador único del detalle |
| `return_id` | int [NOT NULL] | ID de la devolución (FK → returns.id) |
| `product_id` | int [NOT NULL] | ID del producto devuelto (FK → products.id) |
| `quantity` | int [NOT NULL] | Cantidad devuelta del producto |
| `subtotal_returned` | decimal(10,2) [NOT NULL] | Monto devuelto por este producto |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX (return_id)`
- `INDEX (product_id)`

**Relaciones:**
- `return_details.return_id` → `returns.id` (N:1)
- `return_details.product_id` → `products.id` (N:1)

---

## 📂 MÓDULO 6: AUDITORÍA (BITÁCORA)

### Tabla `audit_log`

Registro inalterable de todas las acciones críticas en el sistema.

| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id` | bigint [PK, AI] | Identificador único del registro |
| `user_id` | int [NULL] | ID del usuario que realizó la acción (FK → users.id, NULL para sistema/scripts) |
| `action` | varchar(100) [NOT NULL] | Acción realizada (ej: "CREATE_PRODUCT", "CANCEL_SALE", "CHANGE_PRICE") |
| `affected_table` | varchar(50) [NOT NULL] | Nombre de la tabla afectada |
| `record_id` | int | ID del registro modificado |
| `details` | JSON | Estado ANTES y DESPUÉS en formato JSON |
| `source_ip` | varchar(45) | IP desde donde se realizó la acción |
| `created_at` | timestamp [DEFAULT: CURRENT_TIMESTAMP] | Fecha y hora de la acción |

**Índices:**
- `PRIMARY KEY (id)`
- `INDEX idx_audit_date (created_at)`
- `INDEX (user_id)`
- `INDEX (affected_table, record_id)`
- `INDEX (user_id, created_at)`

**Relaciones:**
- `audit_log.user_id` → `users.id` (N:1, NULL permitido)
