FROM webdevops/php-nginx:8.3
WORKDIR /var/www/html
COPY . /var/www/html

# Install packages
RUN apt-get update && apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    ssl-cert \
    cron \
    openssl

# Common PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install \
    gd \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \ 
    mysqli \
    pdo_mysql

# Ensure PHP logs are captured by the container
ENV LOG_CHANNEL=stderr

# Copy code and run composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN cd /var/www/html && composer install

# Update the file owner details
RUN chown -R www-data /var/www/html
# Allow the storage directory to be written by the owner
RUN chmod o+w -R /var/www/html/storage/

# RUN crontab -l | echo '* * * * * /usr/bin/bash -l -c "/var/www/html/docker/run_jobs.sh" > /var/www/html/jobs.log 2>&1' | crontab -

# Ensure the entrypoint file can be run
RUN openssl dhparam -out /etc/ssl/dhparam.pem 2048
RUN chmod 755 /var/www/html/docker/entrypoint.sh
CMD ["/bin/bash", "-c", "/var/www/html/docker/entrypoint.sh"]