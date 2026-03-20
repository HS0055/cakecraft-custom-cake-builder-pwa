FROM php:8.4-cli-alpine

# Verify PHP 8.4
RUN php -v && php -r "if(PHP_VERSION_ID < 80400){echo 'WRONG PHP VERSION';exit(1);}"

# Install system dependencies
RUN apk add --no-cache \
    bash \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code
COPY . .

# Create .env with SQLite for build-time artisan commands
RUN cp .env.example .env && \
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env && \
    sed -i 's/DB_DATABASE=laravel//' .env

# Set permissions + create SQLite DB before any artisan commands
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
    storage/framework/views storage/app/public bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    touch database/database.sqlite

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Run composer scripts after full copy
RUN composer run-script post-autoload-dump

# Expose port
EXPOSE 8080

# Start script
COPY docker-start.sh /docker-start.sh
RUN chmod +x /docker-start.sh

CMD ["/docker-start.sh"]
