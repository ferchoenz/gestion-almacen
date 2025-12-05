# SGP-Web - Sistema de Gestión de Procesos de Almacén

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?logo=postgresql)
![License](https://img.shields.io/badge/License-Proprietary-red)

Sistema web interno para digitalizar, controlar y auditar los procesos operativos de las terminales TRP y TRVM. Diseñado para complementar SAP en el control diario de operaciones, eliminando el uso de papel y asegurando trazabilidad completa.

---

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características Principales](#características-principales)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Módulos del Sistema](#módulos-del-sistema)
- [Seguridad](#seguridad)
- [Comandos Útiles](#comandos-útiles)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Descripción

SGP-Web **NO** es un sistema de inventario fiscal (eso lo hace SAP). Su objetivo es:

✅ Controlar la operación diaria de almacén  
✅ Eliminar el uso de papel en procesos  
✅ Asegurar trazabilidad de firmas digitales  
✅ Gestionar documentos regulatorios (HDS, Certificados)  
✅ Cumplir con la NOM-018-STPS-2015 para materiales peligrosos  

---

## ⭐ Características Principales

### 1. **Módulo de Salidas (Material Outputs)**
- Registro de material que sale del almacén
- Firmas digitales de quien entrega y quien recibe
- Generación automática de Vales de Salida en PDF
- Estados: `PENDIENTE_OT` → `PENDIENTE_SAP` → `COMPLETO`
- Exportación a Excel
- Soft deletes con motivo de cancelación

### 2. **Módulo de Recepciones (Material Receptions)**
- Registro de material que llega de proveedores
- Subida de documentos: Factura, Remisión, Certificado de Calidad
- Generación de Vales de Entrada en PDF
- Estados: `PENDIENTE_UBICACION` → `COMPLETO`
- Filtros avanzados (mes, año, terminal, búsqueda)

### 3. **Módulo Hazmat (Materiales Peligrosos)** 🔥
- **Análisis con IA (Google Gemini 2.5 Flash)**: Extrae automáticamente datos de HDS en PDF
- Generación de etiquetas GHS según NOM-018-STPS-2015
- Gestión de pictogramas, frases H/P, EPP recomendado
- Almacenamiento de HDS y fotos del producto
- Listado maestro con filtros y exportación

### 4. **Sistema de Roles y Permisos**
- **Administrador**: Acceso total
- **Gerencia**: Solo lectura de reportes
- **Mantenimiento**: Solo módulo de Salidas
- **Seguridad y Salud**: Solo módulo Hazmat

### 5. **Multi-Tenant Lógico**
- Soporte para múltiples terminales (TRP, TRVM)
- Usuarios asignados a una terminal específica
- SuperAdmin puede ver todas las terminales

---

## 💻 Requisitos del Sistema

### Servidor de Producción
- **PHP**: 8.3 o superior
- **Composer**: 2.x
- **Node.js**: 18.x o superior
- **NPM**: 9.x o superior
- **Base de Datos**: PostgreSQL 16+
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+
- **Memoria RAM**: Mínimo 2GB
- **Espacio en Disco**: Mínimo 5GB (para archivos adjuntos)

### Extensiones PHP Requeridas
```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO (con driver PostgreSQL)
- Tokenizer
- XML
- GD o Imagick (para generación de PDFs)
```

---

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone <repository-url> gestion-almacen
cd gestion-almacen
```

### 2. Instalar Dependencias
```bash
# Dependencias de PHP
composer install

# Dependencias de Node.js
npm install
```

### 3. Configurar Variables de Entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos
Editar `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sgp_web
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 5. Ejecutar Migraciones y Seeders
```bash
# Crear tablas
php artisan migrate

# Poblar datos iniciales (terminales, roles, usuario admin)
php artisan db:seed
```

### 6. Crear Symlink de Storage
```bash
php artisan storage:link
```

### 7. Compilar Assets
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 8. Configurar API de Gemini (Opcional)
Para usar el análisis de HDS con IA:
```env
GEMINI_API_KEY=tu_api_key_de_google
```

---

## ⚙️ Configuración

### Configuración de Filesystems
El sistema usa el disco `public` para almacenar archivos. Asegúrate de que `storage/app/public` tenga permisos de escritura.

```bash
# Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Windows (PowerShell como Administrador)
icacls storage /grant Everyone:F /T
```

### Configuración de Correo (Futuro)
Para notificaciones por email:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@empresa.com
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@empresa.com
MAIL_FROM_NAME="SGP-Web"
```

---

## 📁 Estructura del Proyecto

```
gestion-almacen/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── UserController.php          # Gestión de usuarios
│   │   │   ├── Almacen/
│   │   │   │   ├── MaterialOutputController.php    # Salidas
│   │   │   │   ├── MaterialReceptionController.php # Recepciones
│   │   │   │   └── HazmatProductController.php     # Hazmat
│   │   │   └── DashboardController.php         # Dashboard ejecutivo
│   │   └── Middleware/
│   │       └── RoleMiddleware.php              # Control de acceso por rol
│   ├── Models/
│   │   ├── User.php                            # Usuario con rol y terminal
│   │   ├── Terminal.php                        # Terminales (TRP, TRVM)
│   │   ├── Role.php                            # Roles del sistema
│   │   ├── MaterialOutput.php                  # Salidas de material
│   │   ├── MaterialReception.php               # Recepciones de material
│   │   └── HazmatProduct.php                   # Materiales peligrosos
│   ├── Services/
│   │   └── GeminiService.php                   # Integración con Google Gemini AI
│   └── Exports/
│       ├── MaterialOutputsExport.php           # Exportar salidas a Excel
│       ├── MaterialReceptionsExport.php        # Exportar recepciones a Excel
│       └── HazmatProductsExport.php            # Exportar hazmat a Excel
├── database/
│   ├── migrations/                             # Migraciones de BD
│   └── seeders/                                # Datos iniciales
├── resources/
│   └── views/
│       ├── almacen/
│       │   ├── material-outputs/               # Vistas de salidas
│       │   ├── material-receptions/            # Vistas de recepciones
│       │   └── hazmat/                         # Vistas de hazmat
│       └── dashboard.blade.php                 # Dashboard principal
├── routes/
│   └── web.php                                 # Definición de rutas
├── storage/
│   └── app/
│       └── public/                             # Archivos públicos (PDFs, imágenes)
│           ├── hazmat/
│           │   ├── hds/                        # Hojas de Datos de Seguridad
│           │   └── images/                     # Fotos de productos
│           └── receptions/
│               ├── invoices/                   # Facturas
│               ├── remissions/                 # Remisiones
│               └── certificates/               # Certificados de calidad
└── public/
    ├── images/
    │   └── ghs/                                # Pictogramas GHS (PNG)
    └── storage -> ../storage/app/public        # Symlink
```

---

## 🔐 Seguridad

### Implementaciones de Seguridad

1. **Autenticación**
   - Laravel Breeze (autenticación oficial de Laravel)
   - Sesiones seguras con cookies HTTP-only
   - Protección CSRF en todos los formularios

2. **Autorización**
   - Middleware de roles personalizado
   - Control de acceso basado en roles (RBAC)
   - Validación de permisos en cada ruta

3. **Validación de Datos**
   - Validación server-side en todos los formularios
   - Sanitización de inputs
   - Protección contra SQL Injection (Eloquent ORM)
   - Protección contra XSS (Blade escaping automático)

4. **Archivos**
   - Validación de tipos MIME
   - Límites de tamaño de archivo
   - Almacenamiento fuera del directorio público
   - Rutas protegidas para visualización

5. **Soft Deletes**
   - Los registros nunca se eliminan físicamente
   - Trazabilidad completa con motivo de cancelación
   - Posibilidad de auditoría histórica

### Recomendaciones Adicionales

- [ ] Implementar HTTPS en producción (obligatorio)
- [ ] Configurar firewall para limitar acceso a puertos
- [ ] Implementar rate limiting en rutas públicas
- [ ] Configurar backups automáticos de base de datos
- [ ] Implementar logging de acciones críticas
- [ ] Configurar monitoreo de errores (Sentry, Bugsnag)

---

## 🛠️ Comandos Útiles

### Desarrollo
```bash
# Iniciar servidor de desarrollo
php artisan serve

# Compilar assets en modo watch
npm run dev

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Ver rutas del sistema
php artisan route:list

# Acceder a Tinker (consola interactiva)
php artisan tinker
```

### Base de Datos
```bash
# Ejecutar migraciones
php artisan migrate

# Revertir última migración
php artisan migrate:rollback

# Refrescar base de datos (CUIDADO: borra todo)
php artisan migrate:fresh --seed

# Ver estado de migraciones
php artisan migrate:status
```

### Producción
```bash
# Optimizar aplicación
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compilar assets para producción
npm run build

# Ejecutar migraciones en producción
php artisan migrate --force
```

---

## 🐛 Troubleshooting

### Error: "The stream or file could not be opened"
```bash
# Dar permisos a storage y bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Error: "No application encryption key has been specified"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[08006] Connection refused"
- Verificar que PostgreSQL esté corriendo
- Verificar credenciales en `.env`
- Verificar que la base de datos exista

### Los archivos no se visualizan
```bash
# Recrear symlink de storage
php artisan storage:link
```

### Error 500 en producción
```bash
# Ver logs detallados
tail -f storage/logs/laravel.log

# Limpiar caché
php artisan optimize:clear
```

---

## 📊 Base de Datos

### Tablas Principales

| Tabla | Descripción |
|-------|-------------|
| `users` | Usuarios del sistema con rol y terminal asignada |
| `terminals` | Terminales (TRP, TRVM) |
| `roles` | Roles del sistema (Administrador, Gerencia, etc.) |
| `material_outputs` | Salidas de material con firmas digitales |
| `material_receptions` | Recepciones de material con documentos adjuntos |
| `hazmat_products` | Materiales peligrosos con datos GHS |

### Diagrama de Relaciones
```
users
├── belongsTo: Role
├── belongsTo: Terminal (nullable para SuperAdmin)
├── hasMany: MaterialOutput
└── hasMany: MaterialReception

material_outputs
├── belongsTo: User
└── belongsTo: Terminal

material_receptions
├── belongsTo: User
└── belongsTo: Terminal

hazmat_products
└── belongsTo: Terminal
```

---

## 🔄 Roadmap Futuro

### Fase 2: Sistema de Inventario (Kardex)
- Implementar lógica de suma/resta automática
- Stock en tiempo real de consumibles
- Alertas de stock mínimo
- Reportes de consumo por departamento

### Fase 3: Integración con SAP
- API para sincronización bidireccional
- Importación automática de órdenes de compra
- Exportación de movimientos a SAP

### Fase 4: Notificaciones y Alertas
- Notificaciones por email
- Alertas de vencimiento de certificados
- Recordatorios de actualización de HDS

### Fase 5: App Móvil
- Escaneo de códigos QR/Barras
- Registro de salidas desde dispositivos móviles
- Consulta de inventario en tiempo real

---

## 📝 Licencia

Este proyecto es propiedad de [Nombre de la Empresa]. Todos los derechos reservados.

---

## 👥 Soporte

Para soporte técnico o reportar bugs, contactar a:
- **Coordinador de Materiales**: [Tu Nombre]
- **Email**: [tu.email@empresa.com]

---

**Última actualización**: Diciembre 2025  
**Versión**: 1.0.0
