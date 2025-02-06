# Use PHP 7.4 as base image
FROM php:7.4-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    procps  \        
    vim \ 
    && docker-php-ext-install zip pdo pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy application code
COPY . .

# Create database directory and set permissions
RUN mkdir -p database && chmod -R 777 database

# Initialize SQLite database
RUN sqlite3 database/chat.db < init_db.sql

# Verify SQLite database initialization
RUN sqlite3 database/chat.db "SELECT name FROM sqlite_master WHERE type='table';"

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader
RUN composer install
# Expose port 8080
EXPOSE 8080


CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
