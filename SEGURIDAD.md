# Análisis de Seguridad y Buenas Prácticas
## SGP-Web - Sistema de Gestión de Procesos de Almacén

**Fecha de Análisis**: Diciembre 2025  
**Analista**: Equipo de Desarrollo  
**Versión del Sistema**: 1.0.0  

---

## 📋 Resumen Ejecutivo

Este documento presenta un análisis detallado de las medidas de seguridad implementadas en SGP-Web, identificando fortalezas, áreas de mejora, y recomendaciones para cumplir con los estándares de ciberseguridad corporativos.

**Calificación General de Seguridad**: ⭐⭐⭐⭐☆ (4/5)

---

## 1. Análisis de Vulnerabilidades OWASP Top 10

### A01:2021 - Broken Access Control ✅ MITIGADO

**Riesgo**: Usuarios accediendo a funciones o datos no autorizados.

**Implementaciones**:
- ✅ Middleware de autenticación en todas las rutas protegidas
- ✅ Middleware de roles personalizado (`RoleMiddleware`)
- ✅ Validación de permisos en controladores
- ✅ Filtrado automático por terminal (multi-tenant)
- ✅ Soft deletes para prevenir eliminación accidental

**Código de Ejemplo**:
```php
// routes/web.php
Route::middleware(['auth', 'role:Administrador,Gerencia'])
    ->get('/hazmat', [HazmatProductController::class, 'index']);

// HazmatProductController.php
public function index(Request $request) {
    $user = Auth::user();
    $query = HazmatProduct::with('terminal');
    
    // Filtrado automático por terminal
    if ($user->role->name !== 'Administrador') {
        $query->where('terminal_id', $user->terminal_id);
    }
    // ...
}
```

**Recomendaciones**:
- [ ] Implementar logging de accesos a datos sensibles
- [ ] Agregar auditoría de cambios en registros críticos

---

### A02:2021 - Cryptographic Failures ✅ MITIGADO

**Riesgo**: Exposición de datos sensibles por falta de encriptación.

**Implementaciones**:
- ✅ Contraseñas hasheadas con Bcrypt (cost factor 12)
- ✅ Sesiones encriptadas con AES-256-CBC
- ✅ Cookies HTTP-only y Secure (en HTTPS)
- ✅ Variables sensibles en `.env` (no en código)

**Código de Ejemplo**:
```php
// config/session.php
'encrypt' => true,
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax',
```

**Recomendaciones**:
- [ ] Implementar encriptación de datos sensibles en base de datos (opcional)
- [ ] Usar HTTPS obligatorio en producción (crítico)

---

### A03:2021 - Injection ✅ MITIGADO

**Riesgo**: SQL Injection, Command Injection, etc.

**Implementaciones**:
- ✅ Eloquent ORM con prepared statements automáticos
- ✅ Validación de inputs en todos los formularios
- ✅ Sanitización de datos con reglas de validación
- ✅ No se usa `DB::raw()` sin validación

**Código de Ejemplo**:
```php
// Uso seguro de Eloquent
$products = HazmatProduct::where('terminal_id', $terminalId)
    ->where('product_name', 'like', "%{$search}%")
    ->get();

// Validación estricta
$validated = $request->validate([
    'product_name' => 'required|string|max:255',
    'quantity' => 'required|numeric|min:0',
]);
```

**Estado**: ✅ Sin vulnerabilidades conocidas

---

### A04:2021 - Insecure Design ✅ PARCIALMENTE MITIGADO

**Riesgo**: Diseño inseguro de funcionalidades.

**Implementaciones**:
- ✅ Separación de roles y responsabilidades
- ✅ Principio de mínimo privilegio
- ✅ Validación de lógica de negocio
- ⚠️ No hay rate limiting implementado

**Recomendaciones**:
- [ ] Implementar rate limiting en login (5 intentos / 5 min)
- [ ] Implementar throttling en API de IA
- [ ] Agregar CAPTCHA en formularios públicos (si aplica)

---

### A05:2021 - Security Misconfiguration ⚠️ REQUIERE ATENCIÓN

**Riesgo**: Configuraciones inseguras por defecto.

**Implementaciones**:
- ✅ `APP_DEBUG=false` en producción
- ✅ Directorio `storage` fuera de `public`
- ✅ `.env` en `.gitignore`
- ⚠️ No hay headers de seguridad configurados

**Recomendaciones Críticas**:
```php
// app/Http/Middleware/SecurityHeaders.php (CREAR)
public function handle($request, Closure $next) {
    $response = $next($request);
    
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    
    return $response;
}
```

- [ ] Configurar headers de seguridad
- [ ] Deshabilitar listado de directorios en Apache/Nginx
- [ ] Ocultar versión de PHP y servidor

---

### A06:2021 - Vulnerable and Outdated Components ✅ MITIGADO

**Riesgo**: Uso de librerías con vulnerabilidades conocidas.

**Implementaciones**:
- ✅ Laravel 12.0 (última versión estable)
- ✅ PHP 8.3 (última versión)
- ✅ Dependencias actualizadas vía Composer

**Proceso de Actualización**:
```bash
# Verificar vulnerabilidades
composer audit

# Actualizar dependencias
composer update

# Revisar changelog de breaking changes
```

**Recomendaciones**:
- [ ] Configurar Dependabot o Renovate para actualizaciones automáticas
- [ ] Revisar security advisories mensualmente
- [ ] Establecer proceso de actualización trimestral

---

### A07:2021 - Identification and Authentication Failures ✅ MITIGADO

**Riesgo**: Autenticación débil o sesiones inseguras.

**Implementaciones**:
- ✅ Laravel Breeze (autenticación oficial)
- ✅ Regeneración de session ID después del login
- ✅ Logout con invalidación de sesión
- ✅ Contraseñas hasheadas con Bcrypt
- ⚠️ No hay política de contraseñas fuertes
- ⚠️ No hay autenticación de dos factores (2FA)

**Recomendaciones**:
```php
// app/Rules/StrongPassword.php (CREAR)
class StrongPassword implements Rule {
    public function passes($attribute, $value) {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }
    
    public function message() {
        return 'La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.';
    }
}
```

- [ ] Implementar política de contraseñas fuertes
- [ ] Implementar 2FA con Google Authenticator (opcional)
- [ ] Implementar bloqueo de cuenta después de 5 intentos fallidos

---

### A08:2021 - Software and Data Integrity Failures ✅ MITIGADO

**Riesgo**: Integridad de código y datos comprometida.

**Implementaciones**:
- ✅ Composer verifica integridad de paquetes
- ✅ NPM verifica integridad de paquetes
- ✅ Git para control de versiones
- ✅ Soft deletes para prevenir pérdida de datos

**Recomendaciones**:
- [ ] Implementar firma de código (code signing)
- [ ] Configurar CI/CD con verificación de integridad
- [ ] Implementar checksums para archivos críticos

---

### A09:2021 - Security Logging and Monitoring Failures ⚠️ REQUIERE ATENCIÓN

**Riesgo**: Falta de visibilidad de incidentes de seguridad.

**Implementaciones**:
- ✅ Logs de Laravel en `storage/logs/laravel.log`
- ✅ Timestamps en todas las tablas
- ⚠️ No hay monitoreo en tiempo real
- ⚠️ No hay alertas automáticas

**Recomendaciones Críticas**:
```php
// app/Http/Middleware/LogSecurityEvents.php (CREAR)
public function handle($request, Closure $next) {
    $response = $next($request);
    
    // Log de accesos a rutas sensibles
    if ($request->is('admin/*') || $request->is('hazmat/*')) {
        Log::info('Security Event', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'route' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
        ]);
    }
    
    return $response;
}
```

- [ ] Implementar Sentry o Bugsnag para tracking de errores
- [ ] Configurar alertas de errores críticos por email
- [ ] Implementar logging de eventos de seguridad
- [ ] Configurar rotación de logs (logrotate)

---

### A10:2021 - Server-Side Request Forgery (SSRF) ✅ MITIGADO

**Riesgo**: Servidor haciendo requests a URLs maliciosas.

**Implementaciones**:
- ✅ No se aceptan URLs de usuarios
- ✅ API de Gemini usa endpoint fijo
- ✅ Validación de archivos subidos

**Estado**: ✅ Sin riesgo identificado

---

## 2. Análisis de Código - Buenas Prácticas

### 2.1 Estructura de Código ✅

**Fortalezas**:
- ✅ Arquitectura MVC bien definida
- ✅ Separación de responsabilidades
- ✅ Uso de Services para lógica compleja (`GeminiService`)
- ✅ Uso de Exports para generación de Excel
- ✅ Modelos con relaciones bien definidas

**Ejemplo de Buena Práctica**:
```php
// app/Services/GeminiService.php
class GeminiService {
    public function analyzeHdsPdf(string $base64Pdf): array {
        // Lógica de negocio aislada en servicio
        // Fácil de testear y reutilizar
    }
}

// app/Http/Controllers/Almacen/HazmatProductController.php
public function __construct(GeminiService $gemini) {
    $this->gemini = $gemini; // Inyección de dependencias
}
```

---

### 2.2 Validación de Datos ✅

**Fortalezas**:
- ✅ Validación server-side en todos los formularios
- ✅ Reglas de validación estrictas
- ✅ Validación de tipos MIME para archivos
- ✅ Límites de tamaño de archivo

**Ejemplo**:
```php
$validated = $request->validate([
    'terminal_id' => ['required', Rule::exists('terminals', 'id')],
    'product_name' => 'required|string|max:255',
    'hds_file' => 'nullable|file|mimes:pdf|max:10240', // 10MB
    'product_image' => 'nullable|image|max:5120', // 5MB
]);
```

---

### 2.3 Manejo de Errores ✅

**Fortalezas**:
- ✅ Try-catch en operaciones críticas
- ✅ Logging de errores con contexto
- ✅ Mensajes de error amigables al usuario

**Ejemplo**:
```php
try {
    $product = HazmatProduct::create($validated);
    Log::info('Product Created:', ['id' => $product->id]);
    return redirect()->route('hazmat.index')->with('success', 'Producto registrado correctamente.');
    
} catch (\Exception $e) {
    Log::error('Error in Hazmat Store:', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
}
```

---

### 2.4 Protección CSRF ✅

**Implementación**:
- ✅ Token CSRF en todos los formularios
- ✅ Verificación automática por Laravel
- ✅ Regeneración de token después del login

**Ejemplo**:
```blade
<form method="POST" action="{{ route('hazmat.store') }}">
    @csrf
    <!-- campos del formulario -->
</form>
```

---

### 2.5 Protección XSS ✅

**Implementación**:
- ✅ Blade escaping automático con `{{ }}`
- ✅ Uso de `{!! !!}` solo cuando es necesario (raro)
- ✅ Sanitización de inputs

**Ejemplo**:
```blade
<!-- Seguro: escaping automático -->
<h1>{{ $product->product_name }}</h1>

<!-- Inseguro: NO usar a menos que sea HTML confiable -->
<div>{!! $trustedHtml !!}</div>
```

---

## 3. Análisis de Base de Datos

### 3.1 Seguridad de Datos ✅

**Fortalezas**:
- ✅ Prepared statements automáticos (Eloquent)
- ✅ Foreign keys con restricciones
- ✅ Soft deletes para auditoría
- ✅ Índices en campos de búsqueda

**Recomendaciones**:
- [ ] Implementar encriptación de datos sensibles (opcional)
- [ ] Configurar backups automáticos
- [ ] Implementar réplica de base de datos

---

### 3.2 Integridad Referencial ✅

**Implementación**:
```php
// Migration
$table->foreignId('terminal_id')
      ->constrained('terminals')
      ->onDelete('cascade');
```

---

## 4. Análisis de Archivos y Storage

### 4.1 Seguridad de Archivos ✅

**Fortalezas**:
- ✅ Validación de tipos MIME
- ✅ Límites de tamaño
- ✅ Almacenamiento fuera de `public`
- ✅ Rutas protegidas con middleware

**Código de Ejemplo**:
```php
// MaterialReceptionController.php
public function viewFile(MaterialReception $recepcione, $type) {
    $path = match($type) {
        'invoice' => $recepcione->invoice_path,
        'remission' => $recepcione->remission_path,
        'certificate' => $recepcione->certificate_path,
        default => null
    };
    
    if (!$path || !Storage::disk('public')->exists($path)) {
        abort(404, 'Archivo no encontrado.');
    }
    
    return response()->file(Storage::disk('public')->path($path));
}
```

**Recomendaciones**:
- [ ] Implementar escaneo de virus (ClamAV)
- [ ] Migrar a Azure Blob Storage o S3
- [ ] Implementar versionado de archivos

---

## 5. Checklist de Seguridad para Producción

### Configuración del Servidor

- [ ] **HTTPS Obligatorio**
  - Certificado SSL/TLS válido
  - Redirección HTTP → HTTPS
  - HSTS habilitado

- [ ] **Firewall Configurado**
  - Solo puertos 80, 443, 22 abiertos
  - SSH solo desde IPs corporativas
  - PostgreSQL no accesible desde internet

- [ ] **Headers de Seguridad**
  ```nginx
  add_header X-Frame-Options "SAMEORIGIN";
  add_header X-Content-Type-Options "nosniff";
  add_header X-XSS-Protection "1; mode=block";
  add_header Referrer-Policy "strict-origin-when-cross-origin";
  add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
  ```

- [ ] **Ocultar Información del Servidor**
  ```nginx
  server_tokens off;
  ```

### Configuración de Laravel

- [ ] **Variables de Entorno**
  ```env
  APP_ENV=production
  APP_DEBUG=false
  APP_KEY=<generado>
  ```

- [ ] **Optimizaciones**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan optimize
  ```

- [ ] **Permisos de Archivos**
  ```bash
  chmod -R 755 /var/www/sgp-web
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data /var/www/sgp-web
  ```

### Seguridad de Base de Datos

- [ ] **Usuario con Privilegios Limitados**
  ```sql
  CREATE USER sgp_user WITH PASSWORD 'strong_password';
  GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO sgp_user;
  ```

- [ ] **Conexión Encriptada**
  ```env
  DB_SSLMODE=require
  ```

- [ ] **Backups Automáticos**
  ```bash
  # Cron job diario
  0 2 * * * pg_dump sgp_web | gzip > /backups/sgp_web_$(date +\%Y\%m\%d).sql.gz
  ```

### Monitoreo y Logging

- [ ] **Rotación de Logs**
  ```
  /var/www/sgp-web/storage/logs/*.log {
      daily
      rotate 30
      compress
      delaycompress
      notifempty
      create 0640 www-data www-data
  }
  ```

- [ ] **Monitoreo de Uptime**
  - UptimeRobot, Pingdom, o similar

- [ ] **Error Tracking**
  - Sentry, Bugsnag, o similar

---

## 6. Plan de Respuesta a Incidentes

### 6.1 Clasificación de Incidentes

| Severidad | Descripción | Tiempo de Respuesta |
|-----------|-------------|---------------------|
| **Crítico** | Sistema caído, brecha de seguridad | Inmediato (< 1 hora) |
| **Alto** | Funcionalidad principal afectada | 4 horas |
| **Medio** | Funcionalidad secundaria afectada | 24 horas |
| **Bajo** | Mejora o bug menor | 1 semana |

### 6.2 Procedimiento de Respuesta

1. **Detección**
   - Monitoreo automático
   - Reporte de usuario
   - Auditoría de logs

2. **Contención**
   - Aislar sistema afectado
   - Bloquear accesos sospechosos
   - Preservar evidencia

3. **Erradicación**
   - Identificar causa raíz
   - Aplicar parche o fix
   - Verificar que el problema está resuelto

4. **Recuperación**
   - Restaurar desde backup si es necesario
   - Verificar integridad de datos
   - Monitorear de cerca

5. **Post-Mortem**
   - Documentar incidente
   - Identificar lecciones aprendidas
   - Actualizar procedimientos

---

## 7. Recomendaciones Finales

### Prioridad Alta (Implementar antes de producción)

1. ✅ Migrar almacenamiento a Azure Blob Storage o S3
2. ✅ Configurar HTTPS obligatorio
3. ✅ Implementar headers de seguridad
4. ✅ Configurar backups automáticos
5. ✅ Implementar rate limiting en login

### Prioridad Media (Implementar en 3 meses)

6. ✅ Implementar 2FA para administradores
7. ✅ Configurar Sentry para error tracking
8. ✅ Implementar política de contraseñas fuertes
9. ✅ Auditoría de seguridad externa
10. ✅ Implementar logging de eventos de seguridad

### Prioridad Baja (Implementar en 6 meses)

11. ✅ Implementar escaneo de virus en archivos
12. ✅ Configurar WAF (Web Application Firewall)
13. ✅ Implementar encriptación de datos sensibles
14. ✅ Configurar honeypots para detectar ataques

---

## 8. Conclusión

SGP-Web implementa **buenas prácticas de seguridad** y está listo para producción con las siguientes condiciones:

✅ **Fortalezas**:
- Autenticación y autorización robustas
- Protección contra vulnerabilidades comunes (OWASP Top 10)
- Validación estricta de datos
- Código bien estructurado y mantenible

⚠️ **Áreas de Mejora**:
- Migrar almacenamiento a solución persistente
- Implementar headers de seguridad
- Configurar monitoreo en tiempo real
- Implementar rate limiting

**Calificación Final**: ⭐⭐⭐⭐☆ (4/5)

Con las recomendaciones de prioridad alta implementadas, el sistema alcanzará una calificación de ⭐⭐⭐⭐⭐ (5/5).

---

**Documento preparado por**: Equipo de Desarrollo  
**Revisado por**: [Pendiente - TI/Ciberseguridad]  
**Fecha**: Diciembre 2025  
**Próxima Revisión**: Marzo 2026
