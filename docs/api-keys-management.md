# 🔑 Gestión de Claves API

## 📋 Descripción General

Este documento describe cómo gestionar las claves API del sistema DataProvider usando los comandos Artisan y seeders disponibles.

## 🚀 Comandos Disponibles

### 1. **Generar Clave API**

```bash
php artisan api:generate-key [opciones]
```

**Opciones disponibles:**
- `--user=`: ID o email del usuario (opcional)
- `--scope=`: Scope de la clave (`read-only`, `write`, `full-access`) - Por defecto: `read-only`
- `--rate-limit=`: Límite de requests por hora - Por defecto: `1000`
- `--expires=`: Fecha de expiración (`YYYY-MM-DD`) o `never` para sin expiración

**Ejemplos de uso:**

```bash
# Generar clave básica (interactivo)
php artisan api:generate-key

# Generar clave con parámetros específicos
php artisan api:generate-key --user=1 --scope=write --rate-limit=5000 --expires=2026-12-31

# Generar clave sin expiración
php artisan api:generate-key --user=admin@example.com --scope=full-access --expires=never

# Generar clave de solo lectura con límite bajo
php artisan api:generate-key --user=2 --scope=read-only --rate-limit=100
```

### 2. **Revocar Clave API**

```bash
php artisan api:revoke-key {token} [--reason=razón]
```

**Parámetros:**
- `token`: Token de la clave API a revocar
- `--reason=`: Razón de la revocación (opcional)

**Ejemplos de uso:**

```bash
# Revocar clave por token
php artisan api:revoke-key dp_Hm7CHqwsCoiS23NYr...

# Revocar clave con razón específica
php artisan api:revoke-key dp_idBoWDzUOPnvPiiab... --reason="Comprometida"
```

### 3. **Listar Claves API**

```bash
php artisan api:list-keys [opciones]
```

**Opciones disponibles:**
- `--user=`: Filtrar por ID o email del usuario
- `--scope=`: Filtrar por scope (`read-only`, `write`, `full-access`)
- `--status=`: Filtrar por estado (`active`, `revoked`)
- `--expired`: Mostrar solo claves expiradas
- `--expiring-soon`: Mostrar claves que expiran en los próximos 30 días

**Ejemplos de uso:**

```bash
# Listar todas las claves
php artisan api:list-keys

# Filtrar por usuario específico
php artisan api:list-keys --user=1

# Mostrar solo claves activas
php artisan api:list-keys --status=active

# Mostrar claves que expiran pronto
php artisan api:list-keys --expiring-soon

# Filtrar por scope
php artisan api:list-keys --scope=write

# Combinar filtros
php artisan api:list-keys --user=1 --status=active --scope=read-only
```

## 🌱 Seeder de Claves API

### Ejecutar el Seeder

```bash
php artisan db:seed --class=ApiKeySeeder
```

### Características del Seeder

El seeder crea automáticamente:
- **5 claves API de ejemplo** para el primer usuario
- **Claves adicionales** para otros usuarios si existen
- **1 clave revocada** para testing
- **Diferentes scopes** y **rate limits** para demostración

### Claves Generadas por el Seeder

| Scope | Rate Limit | Expiración | Estado | Descripción |
|-------|------------|------------|--------|-------------|
| `full-access` | 10,000/h | 1 año | Activa | Administrador |
| `write` | 5,000/h | 6 meses | Activa | Escritura moderada |
| `read-only` | 1,000/h | 3 meses | Activa | Solo lectura |
| `read-only` | 500/h | 1 mes | Activa | Prueba con límite bajo |
| `write` | 2,000/h | Nunca | Activa | Integración permanente |
| `read-only` | Variable | Variable | Activa | Otros usuarios |
| `read-only` | 1,000/h | Expirada | Revocada | Testing |

## 🔐 Scopes Disponibles

### 1. **read-only** 👁️
- **Permisos**: Solo lectura de datos
- **Uso**: Consultas, reportes, dashboards
- **Rate Limit**: Recomendado 100-1,000/h

### 2. **write** ✏️
- **Permisos**: Lectura y escritura de datos
- **Uso**: Aplicaciones que modifican datos
- **Rate Limit**: Recomendado 1,000-5,000/h

### 3. **full-access** 🔓
- **Permisos**: Acceso completo al sistema
- **Uso**: Administración, integraciones críticas
- **Rate Limit**: Recomendado 5,000-10,000/h

## 📊 Gestión de Rate Limits

### Recomendaciones por Tipo de Aplicación

| Tipo de Aplicación | Scope Recomendado | Rate Limit |
|-------------------|-------------------|------------|
| **Dashboard** | `read-only` | 100-500/h |
| **App Móvil** | `read-only` | 500-1,000/h |
| **Integración** | `write` | 1,000-5,000/h |
| **Administración** | `full-access` | 5,000-10,000/h |

### Monitoreo de Uso

```bash
# Ver claves que están cerca del límite
php artisan api:list-keys --status=active

# Ver claves que expiran pronto
php artisan api:list-keys --expiring-soon
```

## 🛡️ Seguridad

### Mejores Prácticas

1. **Rotación Regular**: Cambiar claves cada 3-6 meses
2. **Scope Mínimo**: Usar el scope más restrictivo posible
3. **Rate Limits**: Configurar límites apropiados para el uso
4. **Expiración**: Establecer fechas de expiración cuando sea posible
5. **Revocación**: Revocar claves comprometidas inmediatamente

### Comandos de Seguridad

```bash
# Revocar clave comprometida
php artisan api:revoke-key {token} --reason="Comprometida"

# Ver claves expiradas
php artisan api:list-keys --expired

# Ver claves revocadas
php artisan api:list-keys --status=revoked
```

## 🔄 Integración con el Sistema

### Base de Datos

Las claves API se almacenan en la tabla `api_keys` con:
- Relación con usuarios (`user_id`)
- Tokens únicos con prefijo `dp_`
- Timestamps de creación y expiración
- Estado de revocación

### Modelo Eloquent

```php
use App\Models\ApiKey;

// Obtener clave por token
$apiKey = ApiKey::where('token', $token)->first();

// Verificar si está activa
if ($apiKey && !$apiKey->is_revoked && 
    (!$apiKey->expires_at || $apiKey->expires_at->isFuture())) {
    // Clave válida
}
```

## 📝 Ejemplos de Uso Completo

### Escenario 1: Nueva Integración

```bash
# 1. Generar clave para integración
php artisan api:generate-key \
    --user=integration@company.com \
    --scope=write \
    --rate-limit=2000 \
    --expires=2026-12-31

# 2. Verificar la clave generada
php artisan api:list-keys --user=integration@company.com

# 3. Monitorear uso
php artisan api:list-keys --status=active
```

### Escenario 2: Mantenimiento de Seguridad

```bash
# 1. Ver claves que expiran pronto
php artisan api:list-keys --expiring-soon

# 2. Generar nueva clave para reemplazo
php artisan api:generate-key --user=1 --scope=full-access

# 3. Revocar clave antigua
php artisan api:revoke-key {token_antiguo} --reason="Reemplazada"
```

### Escenario 3: Auditoría

```bash
# 1. Ver todas las claves activas
php artisan api:list-keys --status=active

# 2. Ver claves revocadas
php artisan api:list-keys --status=revoked

# 3. Ver estadísticas generales
php artisan api:list-keys
```

## 🆘 Solución de Problemas

### Error: "Usuario no encontrado"
```bash
# Ver usuarios disponibles
php artisan api:generate-key
# El comando mostrará la lista de usuarios
```

### Error: "Scope inválido"
```bash
# Usar solo estos scopes válidos:
php artisan api:generate-key --scope=read-only
php artisan api:generate-key --scope=write
php artisan api:generate-key --scope=full-access
```

### Error: "Rate limit debe ser mayor a 0"
```bash
# Usar valores positivos:
php artisan api:generate-key --rate-limit=1000
```

## 📚 Comandos Relacionados

- `php artisan migrate` - Ejecutar migraciones de la base de datos
- `php artisan db:seed` - Ejecutar todos los seeders
- `php artisan tinker` - Consola interactiva para testing

---

**Nota**: Todas las claves API generadas comienzan con el prefijo `dp_` para identificar que pertenecen al sistema DataProvider.
