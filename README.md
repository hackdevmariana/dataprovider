# DataProvider API

**DataProvider** es una API modular construida con Laravel 12. Está diseñada para proporcionar datos reutilizables a otras aplicaciones y sitios web, como clima, efemérides, citas, municipios y más.

## Características

- API RESTful con Laravel 12
- Sistema de autenticación con Sanctum (tokens de API)
- Panel de administración con Filament
- Arquitectura modular y escalable
- Preparada para integración con múltiples fuentes de datos

## Requisitos

- PHP 8.2+
- Composer
- MySQL o MariaDB
- Node.js y NPM (opcional, si se usa frontend o panel con Vite)

## Instalación

```bash
git clone https://github.com/hackdevmariana/dataprovider.git
cd dataprovider
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Autenticación
La API usa Laravel Sanctum para gestionar tokens de acceso. Los endpoints protegidos requieren el header:

```
Authorization: Bearer {TOKEN}
```

## Estructura (modular en desarrollo)

```
app/
├── Models/
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Providers/
routes/
├── api.php
├── web.php
config/
database/
```

## Tests

Los tests se gestionan con PHPUnit.

```
php artisan test
```

## Licencia de datos

Los datos servidos por esta API están liberados bajo la licencia [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/). Puedes copiarlos, modificarlos y reutilizarlos sin restricciones ni atribución.
