FROM php:7.4-fpm-alpine
RUN docker-php-ext-install mysqli
COPY . /var/www/html


# ENV USER=docker
# ENV GID=23456
# ENV UID=12345

# RUN addgroup --gid $GID "${USER}"

# RUN adduser \
#     --disabled-password \
#     --gecos "" \
#     --ingroup "$USER" \
#     --no-create-home \
#     --uid "$UID" \
#     "$USER"

# USER "${USER}"


# RUN usermod -a -G www-data root
# RUN addgroup root www-data
# ENV user_id=0
# RUN addgroup $user_id www-data
ARG USER_ID
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
RUN apk --no-cache add shadow && usermod -u ${USER_ID} www-data
# RUN chown -R www-data:www-data /var/www/html
CMD ["php-fpm"]

EXPOSE 9000