# SGP-Web - Sistema de Gestión de Procesos de Almacén

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?logo=postgresql)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?logo=tailwind-css)
![License](https://img.shields.io/badge/License-Proprietary-red)

Sistema web interno para digitalizar, controlar y auditar los procesos operativos de las terminales TRP y TRVM. Diseñado para complementar SAP en el control diario de operaciones, eliminando el uso de papel y asegurando trazabilidad completa.

---

## 📋 Tabla de Contenidos

- [Descripción del Sistema](#descripción-del-sistema)
- [Stack Tecnológico](#stack-tecnológico)
- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Infraestructura de Hosting](#infraestructura-de-hosting)
- [Módulos del Sistema](#módulos-del-sistema)
- [Base de Datos](#base-de-datos)
- [Seguridad](#seguridad)
- [Instalación Local](#instalación-local)
- [Despliegue en Producción](#despliegue-en-producción)
- [Plan de Migración a Azure](#plan-de-migración-a-azure)
- [Comandos Útiles](#comandos-útiles)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Descripción del Sistema

SGP-Web **NO** es un sistema de inventario fiscal (eso lo hace SAP). Su objetivo es:

✅ Controlar la operación diaria de almacén (entradas y salidas de material)  
✅ Eliminar el uso de papel en procesos mediante firmas digitales  
✅ Gestionar inventario de consumibles con trazabilidad completa (kardex)  
✅ Generar etiquetas con códigos de barras para productos  
✅ Dashboard ejecutivo con KPIs y alertas en tiempo real  
✅ Gestionar documentos regulatorios (HDS, Certificados)  
✅ Cumplir con la NOM-018-STPS-2015 para materiales peligrosos  

---

## 💻 Stack Tecnológico

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **PHP** | 8.3+ | Lenguaje de programación principal |
| **Laravel** | 12.x | Framework MVC para backend |
| **Eloquent ORM** | - | Abstracción de base de datos |
| **Laravel Breeze** | - | Sistema de autenticación |
| **Maatwebsite Excel** | 3.x | Exportación de reportes a Excel |
| **Barryvdh DomPDF** | - | Generación de PDFs (vales, etiquetas) |
| **Picqer Barcode** | - | Generación de códigos de barras Code 128 |

### Frontend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **Blade** | - | Motor de plantillas de Laravel |
| **Tailwind CSS** | 3.x | Framework CSS para diseño responsive |
| **Alpine.js** | 3.x | JavaScript reactivo para componentes dinámicos |
| **Chart.js** | 4.x | Gráficas interactivas en Dashboard |
| **Vite** | 5.x | Bundler de assets (CSS/JS) |
| **Signature Pad** | 4.x | Captura de firmas digitales en canvas |

### Inteligencia Artificial
| Tecnología | Propósito |
|------------|-----------|
| **Google Gemini 2.5 Flash** | Análisis automático de Hojas de Datos de Seguridad (HDS) |
| *Futuro: Azure AI* | *Migración planificada para sustituir Gemini* |

### Base de Datos
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| **PostgreSQL** | 16+ | Base de datos relacional principal |
| **Neon** | - | Hosting de PostgreSQL serverless (producción actual) |
| *Futuro: Azure Database* | - | *Migración planificada* |

---

## 🏗️ Arquitectura del Sistema

### Patrón de Diseño: MVC (Model-View-Controller)

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENTE (Navegador)                       │
│                   HTML + Tailwind CSS + Alpine.js                │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                         RUTAS (routes/web.php)                   │
│                      Middleware de Autenticación                 │
│                      Middleware de Roles (RBAC)                  │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        CONTROLADORES                             │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │ DashboardController│ │MaterialOutputController│ │ConsumableController│ │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐  │
│  │MaterialReceptionController│ │HazmatProductController│ │LabelController│  │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                          MODELOS                                 │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │    User    │  │  Terminal  │  │ Consumable │  │InventoryLoc│ │
│  └────────────┘  └────────────┘  └────────────┘  └────────────┘ │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │MaterialOutput│ │MaterialReception│ │HazmatProduct│ │InventoryMovement│ │
│  └────────────┘  └────────────┘  └────────────┘  └────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      PostgreSQL (Neon)                           │
│                   Base de datos serverless                       │
└─────────────────────────────────────────────────────────────────┘
```

### Servicios Externos

```
┌─────────────────────────────────────────────────────────────────┐
│                     SERVICIOS EXTERNOS                           │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │  GeminiService.php                                          ││
│  │  - Envía PDF de HDS a Google Gemini API                     ││
│  │  - Recibe JSON estructurado con datos del químico           ││
│  │  - Extrae: nombre, CAS, pictogramas GHS, frases H/P, EPP    ││
│  └─────────────────────────────────────────────────────────────┘│
│                                                                  │
│  PLANIFICADO: Migrar a Azure AI Document Intelligence           │
└─────────────────────────────────────────────────────────────────┘
```

---

## ☁️ Infraestructura de Hosting

### Configuración Actual (Producción)

```
┌─────────────────────────────────────────────────────────────────┐
│                      INFRAESTRUCTURA ACTUAL                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│   ┌──────────────────────────┐    ┌──────────────────────────┐  │
│   │       RENDER.COM         │    │        NEON.TECH         │  │
│   │   (App Server - Free)    │    │ (PostgreSQL - Free Tier) │  │
│   ├──────────────────────────┤    ├──────────────────────────┤  │
│   │ • PHP 8.3                │    │ • PostgreSQL 16          │  │
│   │ • Laravel 12             │    │ • 512 MB Storage         │  │
│   │ • 512 MB RAM             │    │ • Serverless (auto-scale)│  │
│   │ • Auto-deploy desde Git  │    │ • Branching (dev/prod)   │  │
│   │ • SSL gratuito           │    │ • Backups automáticos    │  │
│   │ • Cold starts ~30s       │    │ • Connection pooling     │  │
│   └──────────────────────────┘    └──────────────────────────┘  │
│              │                              ▲                    │
│              │      DATABASE_URL            │                    │
│              └──────────────────────────────┘                    │
│                                                                  │
│   ┌──────────────────────────┐                                  │
│   │     GOOGLE CLOUD         │                                  │
│   │   (Gemini API - Free)    │                                  │
│   ├──────────────────────────┤                                  │
│   │ • Gemini 2.5 Flash       │                                  │
│   │ • Vision API (PDFs)      │                                  │
│   │ • 60 requests/min free   │                                  │
│   └──────────────────────────┘                                  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Variables de Entorno en Producción (Render)

```env
# Base de datos (Neon)
DATABASE_URL=postgresql://user:pass@ep-xxx.us-east-2.aws.neon.tech/dbname?sslmode=require

# Aplicación
APP_NAME="SGP-Web"
APP_ENV=production
APP_KEY=base64:xxx...
APP_DEBUG=false
APP_URL=https://gestion-almacen.onrender.com

# Gemini AI
GEMINI_API_KEY=AIzaSy...

# Almacenamiento
FILESYSTEM_DISK=public
```

### Limitaciones del Tier Gratuito

| Servicio | Limitación | Impacto |
|----------|------------|---------|
| **Render Free** | Spin-down después de 15 min inactividad | Cold start de ~30 segundos |
| **Render Free** | 750 horas/mes | Suficiente para uso normal |
| **Neon Free** | 512 MB storage | Suficiente para ~50K registros |
| **Neon Free** | Auto-suspend después de 5 min | Latencia inicial en queries |
| **Gemini Free** | 60 RPM, 1M tokens/día | Suficiente para análisis de HDS |

---

## 📦 Módulos del Sistema

### 1. Dashboard Ejecutivo
**Archivo principal:** `DashboardController.php`, `dashboard.blade.php`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Panel de Alertas** | Stock bajo, pendientes OT, recepciones pendientes |
| **8 KPIs con Tendencias** | Salidas/Entradas (vs mes anterior), stock bajo, pendientes, consumibles, valor inventario, almacenes, hazmat |
| **Gráfica de Movimientos** | Barras con gradientes - últimos 6 meses |
| **Gráfica Stock por Almacén** | Dona con distribución de stock |
| **Actividad Reciente** | Últimas 5 salidas/recepciones con status |
| **Tabla Stock Bajo** | Productos bajo mínimo con déficit |

### 2. Salidas de Material (Material Outputs)
**Archivos:** `MaterialOutputController.php`, `views/almacen/material-outputs/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Tipos de Material** | SPARE_PART, CONSUMIBLE |
| **SPARE_PART** | OT/SAP opcionales, status PENDIENTE_OT si falta OT |
| **CONSUMIBLE** | Requiere almacén y catálogo, status siempre COMPLETO |
| **Firmas Digitales** | Canvas con Signature Pad para receptor y entregador |
| **Validación de Stock** | No permite salida si stock insuficiente |
| **Descuento Automático** | Llama removeStock() en consumible vinculado |
| **Generación PDF** | Vale de salida con códigos, firmas y datos |
| **Exportación Excel** | Reporte filtrable por fecha/terminal |

### 3. Recepciones de Material (Material Receptions)
**Archivos:** `MaterialReceptionController.php`, `views/almacen/material-receptions/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Tipos de Material** | SPARE_PART, CONSUMIBLE |
| **Filtrado por Almacén** | Consumibles filtrados por almacén destino |
| **Documentos Adjuntos** | Factura, Remisión, Certificado de Calidad (PDFs) |
| **Aumento Automático** | Llama addStock() en consumible vinculado |
| **Generación PDF** | Vale de entrada con información de almacén |
| **Estados** | COMPLETO (consumibles), PENDIENTE_OT (spare parts sin OT) |

### 4. Consumibles (Inventario)
**Archivos:** `ConsumableController.php`, `views/almacen/consumables/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **CRUD Completo** | Crear, editar, ver, desactivar productos |
| **Campos** | SKU, nombre, descripción, categoría, unidad, stock min/max, costo, ubicación, imagen, código de barras |
| **Kardex** | Historial de movimientos con usuario, fecha, cantidad, stock anterior/nuevo |
| **Métodos de Stock** | addStock(), removeStock(), adjustStock() |
| **Alertas** | Indicador visual de stock bajo en listado |
| **Ubicación Específica** | Campo adicional para ubicación exacta dentro del almacén |

### 5. Ubicaciones de Inventario
**Archivos:** `InventoryLocationController.php`, `views/almacen/inventory-locations/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Almacenes** | Representan áreas físicas (Refacciones, Operaciones, etc.) |
| **Campos** | Terminal, Código, Nombre, Descripción |
| **Multi-terminal** | Cada terminal tiene sus propios almacenes |

### 6. Etiquetas con Códigos de Barras
**Archivos:** `LabelController.php`, `views/almacen/labels/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Etiqueta Individual** | 50mm × 25mm optimizada para impresora de etiquetas |
| **Múltiples Etiquetas** | Grid en A4 (4 columnas) para imprimir N etiquetas |
| **Código de Barras** | Code 128 generado con picqer/php-barcode-generator |
| **Contenido** | Nombre producto, código de barras, SKU, ubicación |

### 7. Materiales Peligrosos (Hazmat)
**Archivos:** `HazmatProductController.php`, `GeminiService.php`, `views/almacen/hazmat/`

| Funcionalidad | Descripción |
|---------------|-------------|
| **Análisis con IA** | Gemini 2.5 Flash extrae datos de HDS en PDF |
| **Datos Extraídos** | Nombre comercial, CAS, estado físico, pictogramas GHS, frases H/P, EPP, primeros auxilios |
| **Etiqueta GHS** | Generación según NOM-018-STPS-2015 |
| **Almacenamiento** | HDS original y foto del producto |

### 8. Gestión de Usuarios
**Archivos:** `UserController.php`, `views/admin/users/`

| Rol | Permisos |
|-----|----------|
| **Administrador** | Acceso total a todos los módulos |
| **Gerencia** | Lectura de reportes, exportaciones |
| **Almacenista** | Consumibles, ubicaciones, etiquetas |
| **Mantenimiento** | Solo módulo de salidas |
| **Seguridad y Salud** | Solo módulo Hazmat |

---

## 🗄️ Base de Datos

### Diagrama de Relaciones

```
┌─────────────────┐       ┌─────────────────┐
│     users       │       │    terminals    │
├─────────────────┤       ├─────────────────┤
│ id              │       │ id              │
│ name            │◄──────│ name            │
│ email           │       │ code            │
│ role_id ────────┼──┐    └─────────────────┘
│ terminal_id ────┼──┼────────────▲
└─────────────────┘  │            │
         │           │            │
         │    ┌──────┴────┐       │
         │    │   roles   │       │
         │    ├───────────┤       │
         │    │ id        │       │
         │    │ name      │       │
         │    └───────────┘       │
         │                        │
         ▼                        │
┌─────────────────┐    ┌─────────────────────┐
│ material_outputs│    │ inventory_locations │
├─────────────────┤    ├─────────────────────┤
│ id              │    │ id                  │
│ terminal_id ────┼────│ terminal_id         │
│ user_id ────────┼────│ code                │
│ consumable_id ──┼──┐ │ name                │
│ material_type   │  │ │ description         │
│ status          │  │ └────────┬────────────┘
│ work_order      │  │          │
│ quantity        │  │          │
│ signatures      │  │          ▼
└─────────────────┘  │ ┌─────────────────┐
                     │ │   consumables   │
                     │ ├─────────────────┤
                     └►│ id              │
                       │ terminal_id     │
                       │ location_id ────┼──┐
                       │ sku             │  │
                       │ name            │  │
                       │ current_stock   │  │
                       │ min_stock       │  │
                       │ unit_cost       │  │
                       │ barcode         │  │
                       │ specific_location│ │
                       └────────┬────────┘  │
                                │           │
                                ▼           │
                    ┌───────────────────┐   │
                    │ inventory_movements│  │
                    ├───────────────────┤   │
                    │ id                │   │
                    │ consumable_id ────┼───┘
                    │ movement_type     │
                    │ quantity          │
                    │ previous_stock    │
                    │ new_stock         │
                    │ reference_type    │
                    │ reference_id      │
                    │ user_id           │
                    │ notes             │
                    └───────────────────┘
```

### Tablas Principales

| Tabla | Registros Estimados | Propósito |
|-------|---------------------|-----------|
| `users` | ~50 | Usuarios del sistema |
| `terminals` | ~5 | Terminales físicas |
| `roles` | 5 | Roles de acceso |
| `consumables` | ~500 | Catálogo de productos |
| `inventory_locations` | ~20 | Almacenes/áreas |
| `inventory_movements` | ~10K/año | Kardex de movimientos |
| `material_outputs` | ~5K/año | Salidas de material |
| `material_receptions` | ~2K/año | Recepciones |
| `hazmat_products` | ~100 | Materiales peligrosos |

---

## 🔐 Seguridad

### Implementaciones Actuales

| Área | Implementación |
|------|----------------|
| **Autenticación** | Laravel Breeze, sesiones HTTP-only |
| **Autorización** | Middleware RBAC personalizado |
| **CSRF** | Token en todos los formularios |
| **XSS** | Escape automático en Blade |
| **SQL Injection** | Eloquent ORM parametrizado |
| **Archivos** | Validación MIME, rutas protegidas |
| **Soft Deletes** | Nunca se borra físicamente |
| **HTTPS** | SSL gratuito de Render |

### Recomendaciones para Azure

- [ ] Implementar Azure AD para SSO
- [ ] Configurar Azure Key Vault para secretos
- [ ] Habilitar Azure DDoS Protection
- [ ] Configurar Azure Monitor para logging
- [ ] Implementar Azure Backup para BD

---

## 🚀 Instalación Local

### Requisitos
- PHP 8.3+
- Composer 2.x
- Node.js 18+
- PostgreSQL 16+

### Pasos

```bash
# 1. Clonar repositorio
git clone <repo-url> gestion-almacen
cd gestion-almacen

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_DATABASE=sgp_web
# ...

# 5. Ejecutar migraciones
php artisan migrate --seed

# 6. Crear symlink de storage
php artisan storage:link

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
```

---

## 🌐 Despliegue en Producción (Render)

### Configuración en Render

1. **Crear Web Service** → Conectar repositorio Git
2. **Build Command**: `composer install --no-dev && npm ci && npm run build && php artisan migrate --force`
3. **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
4. **Configurar variables de entorno** (ver sección anterior)

### Actualizar Producción

```bash
# Push a main branch
git push origin main
# Render detecta cambios y despliega automáticamente
```

---

## 🔮 Plan de Migración a Azure

### Arquitectura Objetivo

```
┌─────────────────────────────────────────────────────────────────┐
│                    ARQUITECTURA EN AZURE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│   ┌──────────────────────────┐    ┌──────────────────────────┐  │
│   │    AZURE APP SERVICE     │    │ AZURE DATABASE FOR       │  │
│   │      (Web App)           │    │     PostgreSQL           │  │
│   ├──────────────────────────┤    ├──────────────────────────┤  │
│   │ • PHP 8.3                │    │ • PostgreSQL 16          │  │
│   │ • Laravel 12             │    │ • Flexible Server        │  │
│   │ • Always-on (sin cold)   │    │ • 32 GB storage          │  │
│   │ • Auto-scale             │    │ • Geo-redundant backup   │  │
│   │ • Staging slots          │    │ • Private endpoint       │  │
│   │ • Custom domain + SSL    │    │ • Automatic patching     │  │
│   └──────────────────────────┘    └──────────────────────────┘  │
│                                                                  │
│   ┌──────────────────────────┐    ┌──────────────────────────┐  │
│   │  AZURE BLOB STORAGE      │    │   AZURE AI SERVICES      │  │
│   │   (Archivos)             │    │   (Document Intelligence)│  │
│   ├──────────────────────────┤    ├──────────────────────────┤  │
│   │ • PDFs (HDS, Facturas)   │    │ • Reemplaza Gemini       │  │
│   │ • Imágenes productos     │    │ • Pre-built models       │  │
│   │ • Firmas digitales       │    │ • Custom training        │  │
│   │ • Redundancia GRS        │    │ • Enterprise SLA         │  │
│   └──────────────────────────┘    └──────────────────────────┘  │
│                                                                  │
│   ┌──────────────────────────┐    ┌──────────────────────────┐  │
│   │    AZURE KEY VAULT       │    │    AZURE MONITOR         │  │
│   │    (Secretos)            │    │    (Observabilidad)      │  │
│   ├──────────────────────────┤    ├──────────────────────────┤  │
│   │ • API Keys               │    │ • Application Insights   │  │
│   │ • Connection strings     │    │ • Log Analytics          │  │
│   │ • Certificates           │    │ • Alertas automáticas    │  │
│   └──────────────────────────┘    └──────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Fases de Migración

#### Fase 1: Infraestructura Base (Semana 1-2)
- [ ] Crear Resource Group en Azure
- [ ] Provisionar Azure App Service (PHP 8.3, Linux)
- [ ] Provisionar Azure Database for PostgreSQL Flexible Server
- [ ] Configurar networking privado (VNet)
- [ ] Migrar datos de Neon a Azure PostgreSQL

#### Fase 2: Storage y Archivos (Semana 2-3)
- [ ] Crear Azure Blob Storage Account
- [ ] Migrar archivos de Render storage a Blob
- [ ] Configurar Laravel para usar Azure Blob (flysystem-azure)
- [ ] Actualizar URLs de archivos en base de datos

#### Fase 3: Sustitución de Gemini por Azure AI (Semana 3-4)
- [ ] Crear Azure AI Document Intelligence
- [ ] Desarrollar `AzureDocumentService.php` (reemplaza `GeminiService.php`)
- [ ] Configurar modelo prebuilt para extracción de documentos
- [ ] Entrenar modelo custom para HDS específicas (opcional)
- [ ] Probar análisis de HDS con Azure AI
- [ ] Deprecar `GEMINI_API_KEY`

#### Fase 4: Seguridad y Monitoreo (Semana 4-5)
- [ ] Configurar Azure Key Vault para secretos
- [ ] Integrar Application Insights para telemetría
- [ ] Configurar Log Analytics workspace
- [ ] Establecer alertas de rendimiento y errores
- [ ] Configurar Azure Backup para BD

#### Fase 5: Go-Live y Optimización (Semana 5-6)
- [ ] Configurar dominio personalizado + SSL
- [ ] Pruebas de carga y rendimiento
- [ ] Documentar runbooks de operación
- [ ] Capacitar equipo de TI
- [ ] Cutover a producción

### Estimación de Costos Azure (Aproximado)

| Servicio | SKU | Costo Mensual (USD) |
|----------|-----|--------------------:|
| App Service | B1 (1 core, 1.75 GB) | ~$13 |
| PostgreSQL Flexible | Burstable B1ms | ~$15 |
| Blob Storage | LRS, 50 GB | ~$2 |
| Azure AI Document Intelligence | S0 (1000 pages/mo) | ~$10 |
| Key Vault | Standard | ~$1 |
| Application Insights | 5 GB/mo | Free tier |
| **Total Aproximado** | | **~$41/mes** |

*Nota: Precios aproximados, pueden variar por región y consumo real.*

---

## 🛠️ Comandos Útiles

### Desarrollo
```bash
php artisan serve         # Servidor local
npm run dev              # Compilar assets (watch)
php artisan tinker       # Consola interactiva
php artisan route:list   # Ver rutas
```

### Base de Datos
```bash
php artisan migrate               # Ejecutar migraciones
php artisan migrate:status        # Ver estado
php artisan db:seed              # Ejecutar seeders
php artisan migrate:fresh --seed # Refrescar BD (¡BORRA TODO!)
```

### Producción
```bash
php artisan optimize              # Optimizar caché
php artisan config:cache          # Cachear configuración
php artisan route:cache           # Cachear rutas
php artisan view:cache            # Cachear vistas
npm run build                     # Build de producción
```

### Limpiar Caché
```bash
php artisan optimize:clear        # Limpiar todo
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 🐛 Troubleshooting

### Error: Cold start lento en Render
**Causa:** Free tier tiene spin-down después de 15 min inactividad.
**Solución temporal:** Usar un servicio de health check (UptimeRobot) cada 10 min.
**Solución definitiva:** Migrar a Azure App Service con Always-On.

### Error: Conexión a Neon timeout
**Causa:** Neon auto-suspende después de 5 min inactividad.
**Solución:** Reintentar conexión o usar connection pooler.

### Error: Gemini API rate limit
**Causa:** Exceder 60 RPM en tier gratuito.
**Solución:** Implementar queue con rate limiting.

### Error: Storage link no funciona
```bash
php artisan storage:link
```

### Error: Permisos de archivos
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 👥 Equipo y Soporte

**Desarrollo:** Luis Fernando Enzastiga Romero  
**Infraestructura actual:** Render + Neon + Google AI  
**Infraestructura objetivo:** Microsoft Azure  

---

**Última actualización:** Diciembre 2025  
**Versión:** 2.0.0
