FROM php:8.0.3-cli-alpine

# Install deps
RUN apk update && apk upgrade && apk add --no-cache openssh curl git

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Create a new user
RUN addgroup -S appgroup && adduser -S appuser -G appgroup
WORKDIR /home/appuser

# Copy our code
ADD . .

# Install dependencies
RUN composer install --dev
