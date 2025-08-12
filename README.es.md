# DataProvider API

## Descripción

**DataProvider** es una API modular construida con Laravel 12. Proporciona datos reutilizables para otras aplicaciones y sitios web (clima, efemérides, citas, municipios, etc.).

### Características

- API RESTful versionada (`/api/v1/`)
- Autenticación con Laravel Sanctum (tokens de API)
- Panel de administración con Filament
- Arquitectura modular y escalable (controladores, servicios, resources, FormRequests)
- Documentación OpenAPI/Swagger (L5-Swagger)
- Tests de feature y unitarios (Pest, PHPUnit)
- Análisis estático con PHPStan y Larastan
- Configuración de CORS y rate limiting
- Licencia de datos: CC0 1.0 Universal

### Requisitos

- PHP 8.2+
- Composer
- MySQL o MariaDB
- Node.js y NPM (opcional, para frontend/panel con Vite)

### Instalación

```bash
git clone https://github.com/hackdevmariana/dataprovider.git
cd dataprovider
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Autenticación

La API usa Laravel Sanctum. Los endpoints protegidos requieren el header:

```
Authorization: Bearer {TOKEN}
```

### Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/Api/V1/
│   ├── Requests/
│   ├── Resources/V1/
│   └── Services/
├── Models/
├── Providers/
routes/
├── api.php
├── web.php
config/
database/
tests/
```

### Endpoints principales

| Método | Endpoint                                               | Descripción                                 | Autenticación |
|--------|--------------------------------------------------------|---------------------------------------------|---------------|
| GET    | /api/v1/provinces                                      | Listar provincias                           | No            |
| GET    | /api/v1/provinces/{id}                                 | Detalle de provincia                        | No            |
| GET    | /api/v1/countries                                      | Listar países                               | No            |
| GET    | /api/v1/countries/{idOrSlug}                           | Detalle de país                             | No            |
| GET    | /api/v1/languages                                      | Listar idiomas                              | No            |
| GET    | /api/v1/languages/{idOrSlug}                           | Detalle de idioma                           | No            |
| GET    | /api/v1/timezones                                      | Listar zonas horarias                       | No            |
| GET    | /api/v1/timezones/{idOrName}                           | Detalle de zona horaria                     | No            |
| GET    | /api/v1/municipalities                                 | Listar municipios                           | No            |
| GET    | /api/v1/municipalities/province/{slug}                 | Municipios por provincia                    | No            |
| GET    | /api/v1/municipalities/country/{slug}                  | Municipios por país                         | No            |
| GET    | /api/v1/municipalities/{idOrSlug}                      | Detalle de municipio                        | No            |
| GET    | /api/v1/points-of-interest                             | Listar puntos de interés                    | No            |
| GET    | /api/v1/points-of-interest/{idOrSlug}                  | Detalle de punto de interés                 | No            |
| GET    | /api/v1/points-of-interest/municipality/{slug}         | Puntos de interés por municipio             | No            |
| GET    | /api/v1/points-of-interest/type/{type}                 | Puntos de interés por tipo                  | No            |
| GET    | /api/v1/points-of-interest/tag/{tagSlug}               | Puntos de interés por tag                   | No            |
| GET    | /api/v1/autonomous-communities                         | Listar comunidades autónomas                | No            |
| GET    | /api/v1/autonomous-communities/{slug}                  | Detalle de comunidad autónoma               | No            |
| GET    | /api/v1/autonomous-communities-with-provinces          | Comunidades con provincias                  | No            |
| GET    | /api/v1/autonomous-communities-with-provinces-and-municipalities | Comunidades con provincias y municipios | No            |
| GET    | /api/v1/persons                                        | Listar personas                             | No            |
| GET    | /api/v1/persons/{idOrSlug}                             | Detalle de persona                          | No            |
| GET    | /api/v1/images                                         | Listar imágenes                             | No            |
| GET    | /api/v1/images/{id}                                    | Detalle de imagen                           | No            |
| GET    | /api/v1/professions                                    | Listar profesiones                          | No            |
| GET    | /api/v1/professions/{idOrSlug}                         | Detalle de profesión                        | No            |
| GET    | /api/v1/works                                          | Listar obras                                | No            |
| GET    | /api/v1/works/{idOrSlug}                               | Detalle de obra                             | No            |
| GET    | /api/v1/links                                          | Listar enlaces                              | No            |
| GET    | /api/v1/links/{id}                                     | Detalle de enlace                           | No            |
| GET    | /api/v1/awards                                         | Listar premios                              | No            |
| GET    | /api/v1/awards/{idOrSlug}                              | Detalle de premio                           | No            |
| GET    | /api/v1/award-winners                                  | Listar ganadores de premios                 | No            |
| GET    | /api/v1/award-winners/{id}                             | Detalle de ganador de premio                | No            |
| GET    | /api/v1/family-members                                 | Listar miembros de familia                  | No            |
| GET    | /api/v1/family-members/{id}                            | Detalle de miembro de familia               | No            |
| GET    | /api/v1/regions                                        | Listar regiones                             | No            |
| GET    | /api/v1/regions/{idOrSlug}                             | Detalle de región                           | No            |
| GET    | /api/v1/provinces/{slug}/regions                       | Regiones por provincia                      | No            |
| GET    | /api/v1/autonomous-communities/{slug}/regions          | Regiones por comunidad autónoma             | No            |
| GET    | /api/v1/countries/{slug}/regions                       | Regiones por país                           | No            |
| GET    | /api/v1/app-settings                                   | Configuración global                        | Sí            |
| GET    | /api/v1/app-settings/{id}                              | Configuración por ID                        | Sí            |
| POST   | /api/v1/points-of-interest                             | Crear punto de interés                      | Sí            |
| PUT    | /api/v1/points-of-interest/{id}                        | Actualizar punto de interés                 | Sí            |
| POST   | /api/v1/images                                         | Subir imagen                                | Sí            |
| PUT    | /api/v1/images/{id}                                    | Actualizar imagen                           | Sí            |
| DELETE | /api/v1/images/{id}                                    | Eliminar imagen                             | Sí            |
| POST   | /api/v1/professions                                    | Crear profesión                             | Sí            |
| POST   | /api/v1/works                                          | Crear obra                                  | Sí            |
| POST   | /api/v1/links                                          | Crear enlace                                | Sí            |
| POST   | /api/v1/awards                                         | Crear premio                                | Sí            |
| POST   | /api/v1/award-winners                                  | Crear ganador de premio                     | Sí            |
| GET    | /api/v1/artists                                         | Listar artistas                              | No            |
| GET    | /api/v1/artists/{idOrSlug}                              | Detalle de artista                           | No            |
| POST   | /api/v1/artists                                         | Crear artista                                | No            |

#### Notas sobre el endpoint de Venue

- Los siguientes campos son opcionales al crear un Venue vía la API pública: `address`, `latitude`, `longitude`, `venue_type`. Si no se indican, se almacenan como null/vacío.
- Campos obligatorios: `name`, `slug`, `municipality_id`.

---

### Scripts útiles

- Test: `