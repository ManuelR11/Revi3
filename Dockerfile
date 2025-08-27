FROM php:8.3-apache

# Instala dependencias y extensiones
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
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif bcmath \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el proyecto
COPY . /var/www/html

# Configuración de Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Usa el puerto de Railway
ENV PORT 8080
EXPOSE 8080
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Comando final
CMD ["apache2-foreground"]
