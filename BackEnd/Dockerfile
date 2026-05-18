FROM php:8.2-fpm

# Instal dependensi sistem
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Hapus cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instal ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Ambil Composer terbaru (DIPERBAIKI DISINI)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tentukan working directory
WORKDIR /var/www

# Copy seluruh isi folder BackEnd ke dalam kontainer
COPY . .

# Berikan izin akses ke folder storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
