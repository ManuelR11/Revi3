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
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-png \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif bcmath gd curl \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Instala Node.js 16 y npm# ...dependencias y extensiones PHP...

# Instala Node.js 16 y npm
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && apt-get install -y nodejs

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia los archivos del proyecto
COPY . /var/www/html

WORKDIR /var/www/html

# Instala dependencias y compila assets
RUN composer install --no-dev --optimize-autoloader
RUN npm install

RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan storage:link

# Cambia permisos antes de compilar assets
RUN chown -R www-data:www-data /var/www/html/public

RUN npm run production

# Permisos y habilita mod_rewrite
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Configura Apache para servir desde public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Usa el puerto que espera Railway
ENV PORT 8080
EXPOSE 8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]

