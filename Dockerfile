# Usamos PHP 8.2 con Apache (el servidor web)
FROM php:8.2-apache

# 1. Instalar dependencias del sistema (Librerías necesarias)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# 2. Limpiar caché para reducir tamaño
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Instalar extensiones de PHP
# IMPORTANTE: pdo_pgsql es para conectar con Neon (Postgres)
# pdo_mysql es por si acaso
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 4. Activar mod_rewrite de Apache (Para que funcionen las rutas de Laravel)
RUN a2enmod rewrite

# 5. Instalar Composer (El gestor de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Configurar directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar todo tu código al servidor
COPY . .

# 8. Instalar dependencias de Laravel
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Instalar dependencias de Frontend (Tailwind) y compilar
RUN npm install && npm run build

# 10. Dar permisos a las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Configurar Apache para usar el puerto dinámico de Render
# Render nos da un puerto en la variable $PORT, Apache debe escuchar ahí.
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 12. Configurar el DocumentRoot para que apunte a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Comando de arranque
CMD ["apache2-foreground"]