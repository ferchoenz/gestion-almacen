# Usamos PHP 8.2 con Apache
FROM php:8.3-apache

# 1. Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# 2. Limpiar caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# 4. Configurar Apache (DocumentRoot y Rewrites)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# --- CORRECCIÓN PARA EL ERROR 404 ---
# Habilitamos el módulo rewrite y configuramos AllowOverride All
RUN a2enmod rewrite

# Creamos un archivo de configuración para permitir .htaccess
RUN echo "<Directory /var/www/html/public>" > /etc/apache2/conf-available/laravel.conf \
    && echo "    Options Indexes FollowSymLinks" >> /etc/apache2/conf-available/laravel.conf \
    && echo "    AllowOverride All" >> /etc/apache2/conf-available/laravel.conf \
    && echo "    Require all granted" >> /etc/apache2/conf-available/laravel.conf \
    && echo "</Directory>" >> /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel
# ------------------------------------

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Configurar directorio
WORKDIR /var/www/html

# 7. Copiar archivos
COPY . .

# 8. Instalar dependencias Laravel
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Frontend
RUN npm install && npm run build

# 10. Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Puerto dinámico de Render
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Comando de inicio
CMD ["apache2-foreground"]