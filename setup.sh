cd docker

export LD_LIBRARY_PATH=/usr/local/lib

docker-compose build  --build-arg USER_ID=$(id -u) php-fpm

docker-compose build  --build-arg USER_ID=$(id -u) database

docker-compose up -d