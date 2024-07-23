# Use the official PHP image
FROM php:8.1-cli

# Set working directory
WORKDIR /app

# Copy composer.json and composer.lockdock
COPY ./composer.json ./

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Install composer dependencies if any
RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

EXPOSE 8080

# Run the PHP application
CMD bash -c "composer install --ignore-platform-reqs --no-scripts && php -S 0.0.0.0:8080"
