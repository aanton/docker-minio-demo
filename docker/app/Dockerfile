FROM php:7-cli

WORKDIR /var/www

ENV APP_DIR /var/www
WORKDIR $APP_DIR
VOLUME $APP_DIR

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www", "/var/www/index.php"]