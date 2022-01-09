FROM php:8.0.3-cli-alpine

# Install deps
RUN apk update && apk upgrade && apk add --no-cache openssh curl git

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
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
