FROM php:8.3-fpm-alpine

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
    && docker-php-ext-install pdo pdo_mysql pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev ${PHPIZE_DEPS}

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /app
RUN chown -R www-data:www-data /app/storage
