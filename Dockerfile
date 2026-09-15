# ==============================================================================
# Multi-stage Dockerfile for Booking Engine POC (Render Deployment)
# Stage 1: Build Vue 3 Frontend
# Stage 2: PHP 8.2 Alpine (Laravel 12 BFF) + Single Unified Web Service
# ==============================================================================

# --- Stage 1: Frontend Build ---
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ ./
# Build frontend for production (output to dist/)
RUN npm run build

# --- Stage 2: Backend & Runtime ---
FROM php:8.2-cli-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    curl \
    git \
    libzip-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev \
    bash

RUN docker-php-ext-install pdo pdo_sqlite zip bcmath pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy backend code
COPY backend/ ./

# Copy built frontend assets directly into Laravel public directory
COPY --from=frontend-builder /app/dist/ /app/public/

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Environment defaults
ENV PORT=10000
ENV APP_ENV=staging
ENV APP_DEBUG=false
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/app/database/database.sqlite
ENV PHP_CLI_SERVER_WORKERS=4

EXPOSE ${PORT}

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
