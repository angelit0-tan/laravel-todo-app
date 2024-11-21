#
# ------- Build Environment -------
#
FROM php:8.2.0-fpm AS environment

# Arguments defined in docker-compose.yml
ARG user
ARG uid
ARG env

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    zlib1g-dev \
    libjpeg-dev \
    libfreetype6-dev \
    jpegoptim optipng pngquant gifsicle \
    zip \
    unzip \
    build-essential \
    awscli

RUN curl -sL https://deb.nodesource.com/setup_22.x  | bash - && \
apt-get install -y nodejs && \
npm install --global --unsafe-perm cross-env

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user


#
# ------- Build Application -------
#
FROM environment AS raw

# Set working directory
WORKDIR /var/www

# Copy Application files
COPY . .


#
# ------- Install PHP packages and clear cache then publish -------
#
FROM environment AS vendors

# Set working directory
WORKDIR /vendors

COPY . /vendors

# Install Composer (PHP) packages
RUN composer install --ignore-platform-reqs

USER $user

# Run artisan commands
# RUN php artisan migrate && \
#     php artisan key:generate && \
#     php artisan cache:clear