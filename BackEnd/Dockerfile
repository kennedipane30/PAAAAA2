FROM php:8.2-fpm

# 1. Instal dependensi sistem (libicu-dev wajib untuk intl)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip

# 2. Hapus cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Konfigurasi dan Instal ekstensi PHP (intl ditambahkan di sini)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd intl

# 4. Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Tentukan working directory
WORKDIR /var/www

# 6. Copy seluruh isi folder BackEnd ke dalam kontainer
COPY . .

# 7. Berikan izin akses ke folder storage dan cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
