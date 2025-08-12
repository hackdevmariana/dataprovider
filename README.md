# DataProvider API

## Description

**DataProvider** is a modular API built with Laravel 12. It provides reusable data for other applications and websites (weather, events, quotes, municipalities, and more).

### Features

- Versioned RESTful API (`/api/v1/`)
- Authentication with Laravel Sanctum (API tokens)
- Admin panel with Filament
- Modular and scalable architecture (controllers, services, resources, FormRequests)
- OpenAPI/Swagger documentation (L5-Swagger)
- Feature and unit tests (Pest, PHPUnit)
- Static analysis with PHPStan and Larastan
- CORS and rate limiting configuration
- Data license: CC0 1.0 Universal

### Requirements

- PHP 8.2+
- Composer
- MySQL or MariaDB
- Node.js and NPM (optional, for frontend/panel with Vite)

### Installation

```bash
git clone https://github.com/hackdevmariana/dataprovider.git
cd dataprovider
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

### Authentication

The API uses Laravel Sanctum. Protected endpoints require the header:

```
Authorization: Bearer {TOKEN}
```

### Project structure

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

### Main Endpoints

| Method | Endpoint                                               | Description                                 | Auth Required |
|--------|--------------------------------------------------------|---------------------------------------------|--------------|
| GET    | /api/v1/provinces                                      | List provinces                              | No           |
| GET    | /api/v1/provinces/{id}                                 | Province details                            | No           |
| GET    | /api/v1/countries                                      | List countries                              | No           |
| GET    | /api/v1/countries/{idOrSlug}                           | Country details                             | No           |
| GET    | /api/v1/languages                                      | List languages                              | No           |
| GET    | /api/v1/languages/{idOrSlug}                           | Language details                            | No           |
| GET    | /api/v1/timezones                                      | List timezones                              | No           |
| GET    | /api/v1/timezones/{idOrName}                           | Timezone details                            | No           |
| GET    | /api/v1/municipalities                                 | List municipalities                         | No           |
| GET    | /api/v1/municipalities/province/{slug}                 | Municipalities by province                  | No           |
| GET    | /api/v1/municipalities/country/{slug}                  | Municipalities by country                   | No           |
| GET    | /api/v1/municipalities/{idOrSlug}                      | Municipality details                        | No           |
| GET    | /api/v1/points-of-interest                             | List points of interest                     | No           |
| GET    | /api/v1/points-of-interest/{idOrSlug}                  | Point of interest details                   | No           |
| GET    | /api/v1/points-of-interest/municipality/{slug}         | Points of interest by municipality          | No           |
| GET    | /api/v1/points-of-interest/type/{type}                 | Points of interest by type                  | No           |
| GET    | /api/v1/points-of-interest/tag/{tagSlug}               | Points of interest by tag                   | No           |
| GET    | /api/v1/autonomous-communities                         | List autonomous communities                 | No           |
| GET    | /api/v1/autonomous-communities/{slug}                  | Autonomous community details                | No           |
| GET    | /api/v1/autonomous-communities-with-provinces          | Communities with provinces                  | No           |
| GET    | /api/v1/autonomous-communities-with-provinces-and-municipalities | Communities with provinces and municipalities | No        |
| GET    | /api/v1/persons                                        | List persons                                | No           |
| GET    | /api/v1/persons/{idOrSlug}                             | Person details                              | No           |
| GET    | /api/v1/images                                         | List images                                 | No           |
| GET    | /api/v1/images/{id}                                    | Image details                               | No           |
| GET    | /api/v1/professions                                    | List professions                            | No           |
| GET    | /api/v1/professions/{idOrSlug}                         | Profession details                          | No           |
| GET    | /api/v1/works                                          | List works                                  | No           |
| GET    | /api/v1/works/{idOrSlug}                               | Work details                                | No           |
| GET    | /api/v1/links                                          | List links                                  | No           |
| GET    | /api/v1/links/{id}                                     | Link details                                | No           |
| GET    | /api/v1/awards                                         | List awards                                 | No           |
| GET    | /api/v1/awards/{idOrSlug}                              | Award details                               | No           |
| GET    | /api/v1/award-winners                                  | List award winners                          | No           |
| GET    | /api/v1/award-winners/{id}                             | Award winner details                        | No           |
| GET    | /api/v1/family-members                                 | List family members                         | No           |
| GET    | /api/v1/family-members/{id}                            | Family member details                       | No           |
| GET    | /api/v1/regions                                        | List regions                                | No           |
| GET    | /api/v1/regions/{idOrSlug}                             | Region details                              | No           |
| GET    | /api/v1/provinces/{slug}/regions                       | Regions by province                         | No           |
| GET    | /api/v1/autonomous-communities/{slug}/regions          | Regions by autonomous community             | No           |
| GET    | /api/v1/countries/{slug}/regions                       | Regions by country                          | No           |
| GET    | /api/v1/app-settings                                   | Global app settings                         | Yes          |
| GET    | /api/v1/app-settings/{id}                              | App settings by ID                          | Yes          |
| POST   | /api/v1/points-of-interest                             | Create point of interest                    | Yes          |
| PUT    | /api/v1/points-of-interest/{id}                        | Update point of interest                    | Yes          |
| POST   | /api/v1/images                                         | Upload image                                | Yes          |
| PUT    | /api/v1/images/{id}                                    | Update image                                | Yes          |
| DELETE | /api/v1/images/{id}                                    | Delete image                                | Yes          |
| POST   | /api/v1/professions                                    | Create profession                           | Yes          |
| POST   | /api/v1/works                                          | Create work                                 | Yes          |
| POST   | /api/v1/links                                          | Create link                                 | Yes          |
| POST   | /api/v1/awards                                         | Create award                                | Yes          |
| POST   | /api/v1/award-winners                                  | Create award winner                         | Yes          |

---

### Useful scripts

- Test: `php artisan test`
- Static analysis: `php vendor/bin/phpstan analyse --memory-limit=1G`
- Generate docs: `php artisan l5-swagger:generate`

### Data license

All data served by this API is released under [CC0 1.0 Universal](https://creativecommons.org/publicdomain/zero/1.0/).

#### Venue endpoint notes

- The following fields are optional when creating a Venue via the public API: `address`, `latitude`, `longitude`, `venue_type`. If not provided, they will be stored as null/empty.
- Required fields: `name`, `slug`, `municipality_id`.
