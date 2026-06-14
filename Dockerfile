# ─────────────────────────────────────────────────────────────────────────────
# Gestion Scolaire — image de production (Dokploy / Docker)
# Sert UNIQUEMENT public/ via Nginx (le .env reste hors du web root).
# ─────────────────────────────────────────────────────────────────────────────

# ─── Stage 1 : compilation des assets front (Vite + Tailwind) ────────────────
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
# Tout le projet (sauf .dockerignore) pour que Tailwind scanne les vues Blade
COPY . .
RUN npm run build


# ─── Stage 2 : application PHP 8.3 (FPM) + Nginx ─────────────────────────────
FROM php:8.3-fpm-alpine AS app

# Installateur d'extensions PHP : binaires PRÉCOMPILÉS (pas de compilation source).
# Réduit fortement le temps de build (intl/gd ne sont plus compilés).
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add --no-cache nginx supervisor \
    && install-php-extensions pdo_mysql mbstring bcmath gd zip intl exif pcntl opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dépendances PHP (sans scripts : la découverte des packages se fait au runtime)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Code de l'application
COPY . .

# Assets compilés issus du stage 1
COPY --from=assets /app/public/build ./public/build

# Permissions pour php-fpm (www-data)
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configs Nginx / Supervisor / entrypoint
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /run/nginx

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
