FROM php:7.4-fpm-alpine
RUN docker-php-ext-install mysqli pdo_mysql
COPY . /var/www/html
ARG USER_ID
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
RUN apk --no-cache add shadow && usermod -u ${USER_ID} www-data
CMD ["php-fpm"]

EXPOSE 9000