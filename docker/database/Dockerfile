FROM mariadb:latest

ARG USER_ID
RUN usermod -u ${USER_ID} mysql

CMD ["mysqld"]

EXPOSE 3306