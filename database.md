# Vergel — Esquema de Base de Datos

Documentación de entidades y atributos del sistema. Basado en `vergel_schema.dbml`.

## Índice

- [Módulo: Roles y Usuarios](#módulo-roles-y-usuarios)
- [Módulo: Permisos (Granular)](#módulo-permisos-granular)
- [Módulo: Clientes y Categorías](#módulo-clientes-y-categorías)
- [Módulo: Productos](#módulo-productos)
- [Módulo: Cajas Registradoras](#módulo-cajas-registradoras)
- [Módulo: Ventas](#módulo-ventas)
- [Módulo: Devoluciones](#módulo-devoluciones)
- [Módulo: Auditoría](#módulo-auditoría)

---

## Módulo: Roles y Usuarios

### `roles`
Roles del sistema, creados libremente por el usuario (ya no es un enum fijo).

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del rol |
| name | varchar(100) | not null, unique | Nombre del rol (personalizable) |
| description | varchar(255) | — | Descripción del rol |
| is_active | boolean | not null, default: true | Estado del rol |
| is_super_admin | boolean | not null, default: false | Si es `true`, el rol tiene acceso total sin necesidad de asignar cada permiso |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

### `users`
Usuarios del sistema.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del usuario |
| full_name | varchar(100) | not null | Nombre completo |
| email | varchar(50) [NOT NULL, UNIQUE]         | Nombre de usuario para login                                        |
| password | varchar(255) [NOT NULL]                | Contraseña encriptada con Bcrypt/Argon2                             |
| address | varchar(255)                           | direccion del usuario                                               |
| DUI   | varchar(10) [unique]                   | Indica el numero Unico De Identidad de la paersona                  |
| Birthday | Date                                   | Ayuda a saber la edad de la persona y claro el dia de su cumpleaños |
| is_active | boolean | not null, default: true | Estado del usuario |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

### `user_has_roles` (pivote)
Permite que un usuario tenga más de un rol asignado.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la asignación |
| user_id | int | FK → `users.id`, not null | Usuario |
| role_id | int | FK → `roles.id`, not null | Rol asignado |
| created_at | timestamp | default: now | Fecha de asignación |

**Índices:** `(user_id, role_id)` único · índice en `role_id`

---

## Módulo: Permisos (Granular)

### `resources`
Módulos o pantallas del sistema (ej. usuarios, inventario, ventas).

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del recurso |
| name | varchar(50) | not null, unique | Nombre técnico (ej. `users`, `products`) |
| display_name | varchar(100) | not null | Nombre visible (ej. "Usuarios") |
| description | varchar(255) | — | Descripción del recurso |
| icon | varchar(50) | — | Ícono para el menú |
| is_active | boolean | not null, default: true | Estado del recurso |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

### `actions`
Acciones u operaciones posibles sobre un recurso (ver, crear, editar, eliminar, exportar, etc).

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la acción |
| name | varchar(50) | not null, unique | Nombre técnico (ej. `view`, `edit`) |
| display_name | varchar(100) | not null | Nombre visible (ej. "Ver", "Editar") |
| description | varchar(255) | — | Descripción de la acción |
| created_at | timestamp | default: now | Fecha de creación |

### `permissions`
Combinación única de recurso + acción.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del permiso |
| resource_id | int | FK → `resources.id`, not null | Recurso al que aplica |
| action_id | int | FK → `actions.id`, not null | Acción permitida |
| name | varchar(100) | not null, unique | Nombre técnico (ej. `products_create`) |
| display_name | varchar(100) | not null | Nombre visible (ej. "Crear Productos") |
| description | varchar(255) | — | Descripción del permiso |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

**Índices:** `name` único · `(resource_id, action_id)` único (evita duplicar el mismo permiso)

### `role_has_permissions` (pivote)
Permisos asignados a cada rol.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la asignación |
| role_id | int | FK → `roles.id`, not null | Rol |
| permission_id | int | FK → `permissions.id`, not null | Permiso otorgado |
| created_at | timestamp | default: now | Fecha de asignación |

**Índices:** `(role_id, permission_id)` único · índice en `permission_id`

### `user_has_permissions` (excepciones)
Permisos otorgados o revocados directamente a un usuario, sin modificar su(s) rol(es).

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la excepción |
| user_id | int | FK → `users.id`, not null | Usuario |
| permission_id | int | FK → `permissions.id`, not null | Permiso afectado |
| type | enum(`grant`, `deny`) | not null, default: `grant` | `grant` otorga el permiso; `deny` lo revoca aunque el rol lo permita |
| created_at | timestamp | default: now | Fecha de creación |

**Índices:** `(user_id, permission_id)` único

> **Regla de negocio:** el permiso efectivo de un usuario = (permisos de todos sus roles) + (`grant` directos) − (`deny` directos).

---

## Módulo: Clientes y Categorías

### `customers`
Clientes del sistema (individuales o empresas).

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del cliente |
| first_name | varchar(100) | not null | Nombre(s) |
| last_name | varchar(100) | — | Apellido(s) |
| tax_id | varchar(20) | unique | NIT / identificación fiscal |
| phone | varchar(20) | — | Teléfono |
| email | varchar(100) | — | Correo electrónico |
| address | text | — | Dirección |
| customer_type | enum(`REGULAR`, `FREQUENT`, `WHOLESALER`) | not null, default: `REGULAR` | Tipo de cliente |
| is_active | boolean | not null, default: true | Estado del cliente |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

### `categories`
Categorías de productos, con soporte para subcategorías.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la categoría |
| parent_category_id | int | FK → `categories.id` | Categoría padre (para subcategorías) |
| name | varchar(100) | not null, unique | Nombre de la categoría |
| description | text | — | Descripción |
| is_active | boolean | not null, default: true | Estado de la categoría |
| created_at | timestamp | default: now | Fecha de creación |

---

## Módulo: Productos

### `products`

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del producto |
| category_id | int | FK → `categories.id`, not null | Categoría del producto |
| barcode | varchar(50) | unique, nullable | Código de barras |
| name | varchar(150) | not null | Nombre del producto |
| description | text | nullable | Descripción |
| purchase_price | decimal(10,2) | not null | Precio de compra |
| sale_price | decimal(10,2) | not null | Precio de venta |
| current_stock | int | not null, default: 0 | Stock actual |
| min_stock | int | not null, default: 5 | Stock mínimo (alertas) |
| has_tax | boolean | not null, default: true | Si el producto aplica impuesto |
| tax_percentage | decimal(5,2) | not null, default: 13.00 | Porcentaje de impuesto |
| is_active | boolean | not null, default: true | Estado del producto |
| created_at | timestamp | default: now | Fecha de creación |
| updated_at | timestamp | default: now | Fecha de última actualización |

---

## Módulo: Cajas Registradoras

### `cash_registers`

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la caja |
| user_id | int | FK → `users.id`, not null | Usuario responsable |
| shift | varchar(20) | — | Turno (ej. mañana/tarde) |
| opening_amount | decimal(10,2) | not null | Monto de apertura |
| theoretical_closing_amount | decimal(10,2) | — | Monto teórico de cierre |
| actual_closing_amount | decimal(10,2) | — | Monto real de cierre |
| difference | decimal(10,2) | — | Diferencia entre teórico y real |
| status | enum(`OPEN`, `CLOSED`) | not null, default: `OPEN` | Estado de la caja |
| opening_date | timestamp | — | Fecha/hora de apertura |
| closing_date | timestamp | — | Fecha/hora de cierre |

---

## Módulo: Ventas

### `sales`

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la venta |
| cash_register_id | int | FK → `cash_registers.id`, not null | Caja donde se realizó |
| user_id | int | FK → `users.id`, not null | Usuario que realizó la venta |
| customer_id | int | FK → `customers.id` | Cliente (opcional) |
| ticket_number | varchar(30) | not null, unique | Número de ticket |
| total | decimal(10,2) | not null | Total de la venta |
| amount_received | decimal(10,2) | not null | Monto recibido |
| change_due | decimal(10,2) | not null | Cambio entregado |
| payment_method | enum(`CASH`, `CARD`, `TRANSFER`, `MIXED`) | not null, default: `CASH` | Método de pago |
| status | enum(`COMPLETED`, `CANCELLED`, `PARTIALLY_RETURNED`) | not null, default: `COMPLETED` | Estado de la venta |
| observations | text | — | Observaciones |
| created_at | timestamp | default: now | Fecha de la venta |

### `sale_details`
Detalle de productos por venta.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del detalle |
| sale_id | int | FK → `sales.id`, not null | Venta asociada |
| product_id | int | FK → `products.id`, not null | Producto vendido |
| quantity | int | not null | Cantidad |
| unit_price | decimal(10,2) | not null | Precio unitario de venta |
| unit_cost | decimal(10,2) | not null | Costo unitario |
| discount | decimal(10,2) | not null, default: 0.00 | Descuento aplicado |
| subtotal | decimal(10,2) | not null | Subtotal de la línea |

---

## Módulo: Devoluciones

### `returns`

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador de la devolución |
| sale_id | int | FK → `sales.id`, not null | Venta original |
| user_id | int | FK → `users.id`, not null | Usuario que procesó la devolución |
| cash_register_id | int | FK → `cash_registers.id` | Caja donde se procesó |
| reason | text | not null | Motivo de la devolución |
| total_returned | decimal(10,2) | not null | Monto total devuelto |
| created_at | timestamp | default: now | Fecha de la devolución |

### `return_details`
Detalle de productos por devolución.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | int | PK, autoincremental | Identificador del detalle |
| return_id | int | FK → `returns.id`, not null | Devolución asociada |
| product_id | int | FK → `products.id`, not null | Producto devuelto |
| quantity | int | not null | Cantidad devuelta |
| subtotal_returned | decimal(10,2) | not null | Subtotal devuelto |

---

## Módulo: Auditoría

### `audit_log`
Registro inmutable de acciones realizadas en el sistema.

| Atributo | Tipo | Restricciones | Descripción |
|---|---|---|---|
| id | bigint | PK, autoincremental | Identificador del registro |
| user_id | int | FK → `users.id` | Usuario que realizó la acción |
| action | varchar(100) | not null | Acción realizada (ej. `create`, `update`, `delete`) |
| affected_table | varchar(50) | not null | Tabla afectada |
| record_id | int | — | ID del registro afectado |
| details | JSON | — | Detalle de los cambios |
| source_ip | varchar(45) | — | IP de origen |
| created_at | timestamp | default: now | Fecha del evento |

---

## Relaciones clave (resumen)

- `users` **N:N** `roles` vía `user_has_roles`
- `roles` **N:N** `permissions` vía `role_has_permissions`
- `users` **N:N** `permissions` (excepciones) vía `user_has_permissions`
- `permissions` = `resources` **×** `actions`
- `categories` es autorreferencial (`parent_category_id`) para subcategorías
- `sales` **1:N** `sale_details` **N:1** `products`
- `returns` **1:N** `return_details` **N:1** `products`, y `returns` referencia a `sales`
