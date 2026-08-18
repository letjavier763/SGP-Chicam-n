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

