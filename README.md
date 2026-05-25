# Memoria Técnica: Desarrollo, Automatización y Despliegue Seguro

Este documento detalla la arquitectura, implementación y automatización de la API desarrollada con Laravel, abarcando desde la lógica de autenticación hasta la puesta en producción bajo estándares DevOps.

---

## FASE 1: Implementación de Autenticación y API

### 1. Configuración de Autenticación JWT
Se ha integrado `tymon/jwt-auth` para la gestión de sesiones sin estado.
- **Seguridad:** Generación de clave única mediante `php artisan jwt:secret`.
- **Modelo:** El modelo `User` implementa la interfaz `JWTSubject` para gestionar los identificadores del token.
- **Configuración:** El `guard api` utiliza el driver `jwt` en `config/auth.php`, con una expiración (TTL) establecida en 60 minutos.

### 2. Endpoints de Autenticación (`/api/auth`)
La API implementa autenticación mediante JSON Web Tokens, protegida por el middleware `auth:api`.

| Método | Ruta | Descripción |
| :--- | :--- | :--- |
| POST | `/login` | Autentica credenciales y devuelve `access_token`. |
| POST | `/logout` | Invalida el token actual (blacklist). |
| POST | `/refresh` | Genera un nuevo token válido. |
| GET | `/me` | Retorna el objeto `User` autenticado (sin password). |

![Descripción de la imagen](Directores/imagenes/cap1/cap2.png)
![Descripción de la imagen](Directores/imagenes/cap3.png)
![Descripción de la imagen](Directores/imagenes/cap4.png)
![Descripción de la imagen](Directores/imagenes/cap5.png)
![Descripción de la imagen](Directores/imagenes/cap6.png)
![Descripción de la imagen](Directores/imagenes/cap7.png)

### 3. Protección de Rutas y Ciclo de Vida del Token
- **Protección:** Aplicada en `routes/api.php` mediante el middleware `auth:api`. Peticiones sin token devuelven `401 Unauthorized` en formato JSON.
- **Privacidad:** El endpoint `/me` serializa al usuario excluyendo el campo `password`.
- **Flujo:** El cliente utiliza la cabecera `Authorization: Bearer {access_token}`. El refresco de token permite mantener la sesión activa sin reautenticación manual.

### 4. Pruebas de Integración (`tests/Feature/`)
Se ha implementado una suite de tests automatizados bajo `PHPUnit` sobre **SQLite en memoria** (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`), garantizando la integridad de:
- **Auth/:** Validación de flujo JWT y control de acceso.
- **Directores/:** CRUD con validaciones y restricciones.
- **Peliculas/:** Gestión con relaciones foráneas.
- **Security/:** Pruebas transversales (expiración, ocultación de errores, protección de datos).

![Descripción de la imagen](Directores/imagenes/cap1.png)

---

## FASE 2: Automatización y Puesta en Producción (DevOps)

Esta fase implementa estándares de industria para asegurar la calidad y el despliegue seguro.

### 1. Estandarización (.devcontainer)
- **Objetivo:** Definir un entorno de desarrollo basado en contenedores Docker.
- **Impacto:** Garantiza que cualquier colaborador trabaje con las mismas versiones de PHP, extensiones y herramientas, eliminando el problema de disparidad de entornos.

### 2. Integración Continua (CI Pipeline - `ci.yml`)
Un "filtro de calidad" automático ejecutado en cada `push`/`pull request`:
- **Linting:** Validación de estilo con `pint`.
- **Seguridad:** Auditoría con `composer audit`.
- **Testing:** Suite de pruebas en SQLite en memoria.
- **Build:** Verificación de construcción de imagen Docker.

### 3. Despliegue Continuo (CD Pipeline - `cd.yml`)
Automatización del ciclo de entrega profesional:
- **Normalización:** Conversión a minúsculas mediante `tr '[:upper:]' '[:lower:]'` para compatibilidad con `ghcr.io`.
- **Registro:** Construcción y publicación en **GitHub Container Registry**.
- **Smoke Testing:** Validación funcional mediante *curl* con reintentos inteligentes post-despliegue.



### 4. Seguridad y Mantenimiento
- **Secrets:** Centralización en GitHub Secrets (no exposición de datos sensibles).
- **Menor Privilegio:** Permisos granulares configurados en archivos YAML.
- **Automatización:** Uso de `dependabot.yml` para auditoría y actualización semanal de dependencias.

#### Resumen de Componentes

| Archivo | Rol | Propósito |
| :--- | :--- | :--- |
| `.devcontainer/` | Entorno | Estandarizar el entorno de desarrollo local. |
| `.github/workflows/ci.yml` | CI | Calidad, seguridad y tests automáticos. |
| `.github/workflows/cd.yml` | CD | Construcción, publicación y validación. |
| `.github/dependabot.yml` | Mantenimiento | Auditoría y actualización automática. |

---
*Documentación generada para el curso de Especialización en Ciberseguridad en Entornos de TI.*