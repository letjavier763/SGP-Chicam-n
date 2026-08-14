# SGP CAP Chicamán — Sistema de Gestión de Pacientes

Sistema web para el registro y gestión de pacientes del **Centro de Atención Permanente (CAP) Chicamán**, departamento de El Quiché, Guatemala.

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 10 (PHP 8.1) |
| Base de datos | PostgreSQL 15 |
| Servidor web | Nginx (alpine) |
| Admin BD | pgAdmin 4 |
| Contenedores | Docker + Docker Compose |

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) 
- Git

## Inicio Rápido

### 1. Clonar y preparar
```bash
cd PG2/sgp
```

### 2. Levantar el entorno completo
```bash
docker-compose up -d --build
```
> La primera vez tarda ~5 minutos mientras descarga las imágenes.

### 3. Correr migraciones y datos iniciales
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### 4. Generar clave de aplicación (si es necesario)
```bash
docker-compose exec app php artisan key:generate
```

### 5. Ajustar permisos de storage
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## Accesos

| Servicio | URL | Credenciales |
|---------|-----|-------------|
| Aplicación web | http://localhost:8080 | - |
| pgAdmin | http://localhost:5050 | admin@sgp.gt / pgadmin2026 |

### Usuario administrador por defecto
- **Username:** `admin`
- **Contraseña:** `Admin@2026!`

## Estructura del Proyecto

```
sgp/
├── app/
│   └── Models/          # 12 modelos Eloquent
├── database/
│   ├── migrations/      # 14 migraciones (esquema ER completo)
│   └── seeders/         # Datos iniciales
├── docker/
│   ├── nginx/           # Configuración del servidor web
│   └── php/             # Dockerfile PHP-FPM 8.1
├── docker-compose.yml   # Orquestación de servicios
└── .env                 # Variables de entorno
```

## Comandos Útiles

```bash
# Ver logs de la aplicación
docker-compose logs -f app

# Acceder a la consola del contenedor
docker-compose exec app bash

# Correr tinker (REPL de Laravel)
docker-compose exec app php artisan tinker

# Detener todos los servicios
docker-compose down

# Detener y borrar volúmenes (reinicia la BD)
docker-compose down -v
```

## Conexión a pgAdmin

Al entrar a pgAdmin por primera vez:
1. Add New Server
2. **Host:** `db`
3. **Port:** `5432`
4. **Database:** `sgp_chicaman`
5. **Username:** `sgp_user`
6. **Password:** `sgp_secure_pass_2026`
