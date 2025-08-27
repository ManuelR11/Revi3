FROM php:8.3-apache

# Instala dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libmagickwand-dev \
    imagemagick \
    unzip \
    git \
    curl \
    curl \
    libcurl4-openssl-dev \
    libgd-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif bcmath gd curl \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Instala Node.js 16 y npm
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && apt-get install -y nodejs

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia los archivos del proyecto
COPY . /var/www/html

# Configura Apache para servir desde public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Usa el puerto que espera Railway
ENV PORT 8080
EXPOSE 8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

# Establece directorio de trabajo
WORKDIR /var/www/html

# Instala dependencias de PHP y JS, y compila assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install
# Ejecuta npm run production y muestra el error si falla
RUN npm run production || (echo "ERROR en npm run production" && cat /var/www/html/npm-debug.log || true)

# Permisos y habilita mod_rewrite
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Comando final
CMD ["apache2-foreground"]
