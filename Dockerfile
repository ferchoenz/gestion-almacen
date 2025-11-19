# Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 1. Instalar dependencias del sistema
# AGREGAMOS: libzip-dev (necesario para la extensión zip de PHP)
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

# 3. Instalar extensiones de PHP
# AGREGAMOS: zip (requerido para Laravel Excel)
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# 4. Activar mod_rewrite de Apache
RUN a2enmod rewrite

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Configurar directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar código
COPY . .

# 8. Instalar dependencias de Laravel
# (Ahora sí funcionará porque ya tenemos la extensión zip)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Instalar dependencias de Frontend y compilar
RUN npm install && npm run build

# 10. Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Configurar Apache para Render (Puerto Dinámico)
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 12. Configurar DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Comando de arranque
CMD ["apache2-foreground"]