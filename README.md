# Sistema POS Alejo

Sistema de Punto de Venta (POS) construido con Laravel 11 y PHP 8.2.

## Requisitos

- PHP 8.2+
- Composer
- MySQL / MariaDB (o SQLite para desarrollo)
- Node.js y NPM (para compilar assets)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/fixeralejo2/sistema-pos-alejo.git
   cd sistema-pos-alejo
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node**
   ```bash
   npm install
   npm run build
   ```

4. **Configurar el entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos** en `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistema_pos
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Ejecutar migraciones y seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Crear enlace de storage**
   ```bash
   php artisan storage:link
   ```

8. **Iniciar el servidor de desarrollo**
   ```bash
   php artisan serve
   ```

## Credenciales por defecto

| Usuario           | Contraseña | Rol           |
|-------------------|------------|---------------|
| admin@cheveramy.com | password  | Administrador |
| cajero@cheveramy.com | password | Cajero        |

## Módulos disponibles

- **Dashboard** – Resumen de ventas y estadísticas del día
- **Caja** – Apertura y cierre de caja registradora
- **Ventas** – Registro y gestión de ventas (con ticket/recibo PDF)
- **Productos** – Catálogo de productos con variantes
- **Categorías** – Organización de productos
- **Clientes** – Base de datos de clientes
- **Inventario** – Control de stock, movimientos y ajustes
- **Apartados** – Planes de pago en abonos
- **Reportes** – Reportes de ventas e inventario

## Roles y Permisos

- **Administrador**: acceso completo a todos los módulos
- **Cajero**: acceso a caja, ventas, clientes, productos (lectura) y apartados

## Resetear datos de prueba

Para volver a correr los seeders en una base de datos existente:

```bash
php artisan migrate:fresh --seed
```

> ⚠️ Esto elimina **todos** los datos existentes.

## Tecnologías

- Laravel 11
- PHP 8.2
- AdminLTE 3 (interfaz de usuario)
- Spatie Laravel Permission (roles y permisos)
- DomPDF (generación de tickets/recibos)
- Vite + Tailwind CSS
