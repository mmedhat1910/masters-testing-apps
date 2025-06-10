FROM php:8.1-apache
# Install the MySQLi extension for PHP to connect to the database
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
# Copy application source code
COPY ./app/ /var/www/html/
# Set correct permissions
RUN chown -R www-data:www-data /var/www/html