#!/bin/bash

set -e

if ! command -v unzip &> /dev/null
then
    echo "unzip could not be found, install it with sudo apt install unzip"
    exit
fi

# Set variables from docker.env
set -a && source docker/docker.env && set +a

docker-compose down -v

docker-compose up --no-start --build

docker-compose run --rm php /bin/bash -c 'composer install'

docker-compose start mysql php dbtest

# unzip -o backup.zip

# docker-compose exec mysql sh -c "mysql -uroot -proot $DB_DATABASE < /var/project/backup.sql"

# rm backup.sql

docker-compose exec php php artisan migrate

docker-compose run --rm -e DB_HOST=dbtest php php artisan migrate

docker-compose run --rm frontend /bin/sh -c "npm install --legacy-peer-deps"

docker-compose start
