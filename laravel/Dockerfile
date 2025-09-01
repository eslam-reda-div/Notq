# ---------- Base PHP ----------
FROM php:8.3-fpm-alpine AS base

# Install runtime dependencies via Alpine packages
RUN apk update \
    && apk upgrade --no-cache \
    && apk add --no-cache \
    nginx unzip curl python3 py3-pip py3-setuptools \
    icu-dev libzip-dev libpng-dev freetype-dev jpeg-dev libwebp-dev \
    tiff-dev libxpm-dev \
    oniguruma-dev bash autoconf build-base openssh-client zip libxml2-dev

# Create supervisor log directory
RUN mkdir -p /var/log/supervisor

# Install specific version of git (v2.50.1)
RUN apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/main git=2.50.1-r0 || \
    apk add --no-cache git

# Install supervisor via pip to avoid pkg_resources issues and get better Docker compatibility
RUN pip3 install --no-cache-dir --break-system-packages supervisor

# Install PHP extensions using docker-php-ext-install and PECL
RUN docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache \
    intl \
    xml

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG ENV_TYPE=prod
ENV ENV_TYPE=${ENV_TYPE}

WORKDIR /var/www/html

# Copy application code
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Copy app-specific configuration files
COPY production/nginx.conf       /etc/nginx/http.d/default.conf
COPY production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY production/php.ini          /usr/local/etc/php/conf.d/local.ini
COPY production/opcache.ini      /usr/local/etc/php/conf.d/opcache.ini

# Install composer dependencies
RUN composer install --optimize-autoloader --no-interaction

# Set environment file
RUN if [ "$ENV_TYPE" = "prod" ]; then cp .env.prod .env; fi

# Laravel optimizations and setup
RUN php artisan config:clear && \
    php artisan storage:link && \
    php artisan cache:clear && \
    php artisan route:cache && \
    php artisan icon:cache && \
    php artisan l5-swagger:generate

# Install Node.js and npm for front-end assets
RUN apk add --no-cache nodejs npm

# Update npm to latest version to fix brace-expansion vulnerability (CVE-2024-4068)
RUN npm install -g npm@latest

# Install and build front-end assets
RUN npm ci
# Force install the missing rollup binary for Alpine Linux and clean npm cache
RUN npm install --save-dev @rollup/rollup-linux-x64-musl || true
RUN npm run build

EXPOSE 80 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
