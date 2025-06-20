#!/bin/bash

docker-compose exec php bash -c "XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text"
