# ---- Base Image: PHP 8.2 FPM ----
FROM php:8.2-fpm

# System dependencies install karo
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions jo Laravel 12 + maatwebsite/excel ke liye chahiye
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        mbstring \
        exif \
        xml \
        bcmath \
        opcache

# Composer install karo (official image se copy)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Working directory set karo
WORKDIR /var/www/html

# Poora application code copy karo
# (composer.json me "classmap": ["database"] hai, isliye composer install se PEHLE
#  poora code copy karna zaroori hai — warna 'database' folder na milne ki wajah se
#  autoloader build fail ho jaata hai)
COPY . .

# Dependencies install karo
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Purani cached config files agar galti se project me aa gayi hon, unhe hatao
# (warna .env ki values ignore ho jaati hain)
RUN rm -f bootstrap/cache/config.php bootstrap/cache/services.php \
    bootstrap/cache/services.json bootstrap/cache/packages.php \
    bootstrap/cache/events.php bootstrap/cache/routes-v7.php 2>/dev/null || true

# Storage, cache aur uploaded images folder ke permissions sahi karo
RUN mkdir -p /var/www/html/public/uploads /var/www/html/public/uploads/thumbnail \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

EXPOSE 9000

CMD ["php-fpm"]