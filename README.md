# 🏛️ MODERATIO

## *Sistema de Gestión de Inventario y Punto de Venta*

![Estado del Proyecto](https://img.shields.io/badge/Estado-En%20Desarrollo-yellow)
![Versión](https://img.shields.io/badge/Versión-1.0.0--alpha-blue)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.2-4479A1?logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.4-06B6D4?logo=tailwindcss&logoColor=white)
![JWT](https://img.shields.io/badge/JWT-Auth-000000?logo=jsonwebtokens&logoColor=white)
![Arquitectura](https://img.shields.io/badge/Arquitectura-MVC-8A2BE2)
![Licencia](https://img.shields.io/badge/Licencia-MIT-green)

---

## 📋 Descripción General

**MODERATIO** (del latín *"control"* y *"medición"*) es un sistema de gestión integral diseñado para pequeñas y medianas tiendas comerciales. Su objetivo principal es **automatizar el registro de ventas diarias**, **sincronizar el inventario en tiempo real** y **garantizar la transparencia operativa** mediante un estricto control de accesos y auditoría de movimientos.

El sistema resuelve la problemática de la falta de control en negocios pequeños, donde la ausencia de registro provoca:
- Pérdidas económicas sin explicación.
- Desconfianza hacia los empleados.
- Desconocimiento de las ventas reales.
- Stock desactualizado y faltantes imprevistos.

---

## 🎯 Objetivo del Proyecto

Proveer una herramienta tecnológica accesible y confiable que permita a los dueños de negocios:
- **Controlar** el inventario y las ventas en tiempo real.
- **Auditar** cada movimiento realizado por los empleados.
- **Tomar decisiones** basadas en datos concretos (reportes y métricas).
- **Facturar** electrónicamente (DTE) cumpliendo con los requisitos legales.

---

## 🧩 Módulos del Sistema

| # | Módulo | Tipo | Descripción                                                                         |
| :---: | :--- | :--- |:------------------------------------------------------------------------------------|
| **1** | **Autenticación** | Seguridad | Inicio/cierre de sesión seguro mediante tokens JWT.                                 |
| **2** | **Dashboard** | Visual | Panel central con métricas clave: ventas del día, bajo stock, clientes fieles, etc. |
| **3** | **POS (Punto de Venta)** | Operativo | Interfaz ágil para selección de productos, cobro y registro de ventas.              |
| **4** | **Caja / Arqueo** | Operativo | Control de efectivo: apertura, cortes y cierre de turno por cajero.                 |
| **5** | **Inventario** | Operativo | Catálogo de productos, precios, costos, código de barras y stock.                   |
| **6** | **Categorías** | Catálogo | Clasificación organizada para agrupar productos.                                    |
| **7** | **Empleados** | Seguridad | Gestión de usuarios habilitados para operar el sistema.                             |
| **8** | **Roles y Permisos** | Seguridad | Control de accesos: Administrador, Cajero, Bodeguero.                               |
| **9** | **Devoluciones / Notas de Crédito** | Post-Venta | Anulación de transacciones y reingreso automático de productos.                     |
| **10** | **Reportes** | Analítica | Ventas por fecha, productos más vendidos, ganancias reales.                         |
| **11** | **Bitácora (Auditoría)** | Seguridad | Registro inalterable de quién, cuándo y qué acción realizó.                         |

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología | Justificación |
| :--- | :--- | :--- |
| **Backend** | Laravel 11.x + PHP 8.4 | Framework robusto con excelente rendimiento, seguridad integrada y ecosistema maduro. |
| **Base de Datos** | MySQL 8.2 | Servidor relacional robusto con soporte ACID. |
| **ORM / Acceso a Datos** | Eloquent ORM | Sintaxis expresiva y segura, con soporte para consultas nativas cuando se requiere optimización. |
| **Frontend** | Blade + HTML5 + Tailwind CSS | Motor de plantillas ligero con componentes reutilizables y estilos responsivos. |
| **Autenticación** | Laravel Sanctum / JWT | Manejo seguro de tokens para API y sesiones. |
| **Despliegue** | Ubuntu / Debian Linux (VM) | Entorno dedicado con Apache/Nginx, PHP-FPM y MySQL. |

### 🏗️ Arquitectura

El proyecto sigue la **Arquitectura MVC (Modelo-Vista-Controlador)** nativa de Laravel:

- **Models:** Modelos Eloquent que representan las tablas de la base de datos.
- **Views:** Plantillas Blade con Tailwind CSS para el frontend.
- **Controllers:** Controladores que procesan la lógica del negocio y orquestan la interacción entre Models y Views.
- **Middleware:** Capa de filtrado para autenticación, verificación de roles y auditoría.

---

## 📋 Requerimientos Funcionales (Resumen)

| ID | Requerimiento | Descripción |
| :---: | :--- | :--- |
| **RF01** | Autenticación JWT | Autentica usuarios y genera token para autorizar peticiones. |
| **RF02** | Control de Acceso por Roles | Limita el acceso según el rol (Admin, Cajero, Bodeguero). |
| **RF03** | CRUD de Productos | Registra, edita, lista y desactiva productos. |
| **RF04** | Punto de Venta (POS) | Procesa ventas, totales y cambio de efectivo. |
| **RF05** | Descuento Automático de Stock | Resta stock en tiempo real al confirmar una venta. |
| **RF06** | Control de Caja y Arqueo | Apertura con monto inicial y arqueo al cerrar turno. |
| **RF07** | Devoluciones y Anulaciones | Permite devolución total/parcial con reingreso al inventario. |
| **RF08** | Registro de Bitácora | Registra automáticamente cada acción crítica en el sistema. |
| **RF09** | Reportes de Ventas y Ganancias | Calcula total vendido, costo y ganancia neta por fechas. |

---

## ⚙️ Requerimientos No Funcionales

| ID | Requerimiento | Descripción |
| :---: | :--- | :--- |
| **RNF01** | Rendimiento | Tiempo de respuesta < 1.5 segundos. |
| **RNF02** | Seguridad de Contraseñas | Encriptadas con `Hash::make()` (Bcrypt/Argon2). |
| **RNF03** | Protección SQL | Uso de Eloquent ORM y consultas parametrizadas. |
| **RNF04** | Usabilidad del POS | Interfaz limpia, operable con teclado y ratón. |
| **RNF05** | Mantenibilidad | Estructura siguiendo los estándares de Laravel. |

---

## 👥 Historias de Usuario (Priorizadas)

### 🔴 Alta Prioridad

| ID | Historia de Usuario |
| :---: | :--- |
| **HU01** | Como empleado, quiero iniciar sesión con mi usuario y contraseña. |
| **HU02** | Como cajero, quiero ingresar el monto de apertura de caja. |
| **HU03** | Como cajero, quiero buscar productos por nombre o código de barras. |
| **HU04** | Como cajero, quiero procesar una venta viendo el cambio a entregar. |
| **HU06** | Como administrador, quiero crear/editar productos. |

### 🟡 Media Prioridad

| ID | Historia de Usuario |
| :---: | :--- |
| **HU05** | Como cajero, quiero cerrar y arquear la caja al final del turno. |
| **HU07** | Como administrador, quiero ver alertas de bajo stock. |
| **HU08** | Como administrador, quiero gestionar usuarios y roles. |

### 🟢 Baja Prioridad

| ID | Historia de Usuario |
| :---: | :--- |
| **HU09** | Como administrador, quiero registrar devoluciones de ventas. |
| **HU10** | Como administrador, quiero consultar la bitácora de acciones. |
| **HU11** | Como administrador, quiero ver reportes de ganancias. |
| **HU12** | Como administrador, quiero ver el Dashboard con métricas del día. |

---

## 🚀 Instalación y Configuración

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/moderatio.git

# Acceder al directorio
cd moderatio

# Instalar dependencias de PHP (Laravel y paquetes)
composer install

# Instalar dependencias de Node.js (Tailwind CSS)
npm install

# Crear archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar la base de datos en .env y ejecutar migraciones
php artisan migrate --seed

# Compilar assets (Tailwind)
npm run build

# Iniciar el servidor de desarrollo
php artisan serve
