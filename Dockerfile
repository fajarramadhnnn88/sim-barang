FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    nodejs \
    npm

# 🔥 CONFIGURE GD (INI YANG KEMARIN KURANG)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    zip \
    gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

EXPOSE 9000
CMD ["php-fpm"]
