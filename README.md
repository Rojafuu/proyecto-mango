# Proyecto Mango — Sistema Web de Agenda para Estudios de BodyArt (Laravel 10 + Livewire 3)

Sistema web académico para gestionar **reservas de tatuajes y bodypiercing** con perfiles por rol (cliente / profesional / administrador) y **control de disponibilidad** para evitar sobrecupos (overbooking).
Incluye flujo de solicitud, revisión del profesional y gestión de agenda/bloqueos.

---

## Funcionalidades principales

* **Autenticación y roles** (Spatie Permission): cliente, profesional y administrador.
* **Cliente**

  * Crear solicitud de reserva (brief + selección de horario).
  * Ver listado de reservas y su estado.
  * Editar/cancelar reservas según reglas del sistema.
* **Profesional**

  * Ver agenda por fecha/estado.
  * Crear bloqueos de agenda.
  * Revisar solicitudes y registrar una propuesta/observaciones.
* **Control de disponibilidad**

  * Evita reservas en horarios ya ocupados.
  * Respeta bloqueos de agenda del profesional.

---

## Tecnologías

* Laravel 10
* Livewire 3
* MySQL / MariaDB
* Spatie Laravel Permission (roles)
* Vite (assets)

> Nota: en local puedes usar **MySQL o MariaDB** (XAMPP/WAMP). En hosting normalmente tendrás MySQL o MariaDB y se configura igual en `.env`.

---

## Requisitos (local)

* PHP 8.2+
* Composer
* Node.js + npm
* MySQL o MariaDB (XAMPP/WAMP sirve)

---

## Instalación y ejecución (local)

### 1) Clonar / abrir el proyecto

Entra a la carpeta del proyecto (debe existir `artisan` en la raíz).

### 2) Instalar dependencias PHP

```bash
composer install
```

### 3) Crear `.env`

* Copia `.env.example` a `.env`
* Configura la conexión a BD:

  * `DB_DATABASE`
  * `DB_USERNAME`
  * `DB_PASSWORD`

### 4) Generar APP_KEY

```bash
php artisan key:generate
```

### 5) Instalar dependencias front

```bash
npm install
```

### 6) Migraciones + seeders (datos de prueba)

```bash
php artisan migrate:fresh --seed
```

### 7) Limpiar cachés (recomendado)

```bash
php artisan optimize:clear
```

---

## Ejecutar el sistema (modo desarrollo) — recomendado (2 terminales)

### Terminal 1 (Vite)

```bash
npm run dev
```

### Terminal 2 (Laravel)

```bash
php artisan serve
```

Abrir:
[http://localhost:8000](http://localhost:8000)

---

## Ejecutar con XAMPP (alternativa)

1. Iniciar Apache y MySQL en XAMPP.
2. En una terminal:

```bash
npm run dev
```

3. Abrir:
   [http://localhost/mango/public](http://localhost/mango/public)

---

## Build para entrega (sin hot reload)

```bash
npm run build
```

---

## Base de datos (script SQL)

El script de base de datos se encuentra en:

* `database/sql/mango.sql`

Este archivo permite recrear la estructura y (si corresponde) datos de prueba en un entorno compatible con **MySQL/MariaDB**.

---

## Roles y credenciales de prueba (Seeders)

> Password para todos los usuarios: `12345678`

* **Administrador**

  * Email: `administrador@mango.cl`

* **Profesional (Tatuaje)**

  * Email: `tattoo@mango.cl`

* **Profesional (Bodypiercing)**

  * Email: `piercing@mango.cl`

* **Cliente**

  * Email: `cliente@mango.cl`

---

## Rutas principales

### Redirección por rol

* `/home` (redirige automáticamente según rol)

### Cliente

* `/cliente`
* `/cliente/reservas`
* `/cliente/reservas/crear`
* `/cliente/reservas/{reserva}/editar`

### Profesional

* `/profesional`
* `/profesional/agenda`
* `/profesional/bloqueos`
* `/profesional/bloqueos/crear`

### Administrador

* `/administrador`

---

## Configuración relevante

* Zona horaria del sistema:

  * `APP_TIMEZONE=America/Santiago`

* Archivo `.env`

  * **No se sube al repositorio** (contiene credenciales y configuración sensible).

---

## Notas de operación

* Una reserva puede pasar por estados como:

  * `pendiente`, `propuesta`, `confirmada`, `cancelada`, `completada`
* El sistema valida:

  * no agendar en el pasado
  * fin > inicio
  * no chocar con bloqueos del profesional
  * no chocar con otras reservas activas en el mismo horario

---

## Licencia

Proyecto académico (IPLACEX). Uso educativo.
