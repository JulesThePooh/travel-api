DB_NAME="travel_api"

#
# Docker
#

init:
	@docker compose down
	@docker compose build --no-cache --build-arg USER_ID=$$(id -u) --build-arg GROUP_ID=$$(id -g) && docker compose up -d
	@docker compose exec -T php composer install
	@echo "Waiting for MariaDB..."
	@until docker compose exec -T db mariadb -uroot -proot -e "SELECT 1" > /dev/null 2>&1; do sleep 1; done
	@docker compose exec -T php php bin/console d:m:m -n
	@docker compose exec -T php php bin/console doctrine:fixtures:load -n

up:
	@docker compose up -d

down:
	@docker compose down -t 0

fixtures:
	@docker compose exec -T php php bin/console doctrine:fixtures:load -n

#
# Containers
#

php:
	@docker compose exec -it php bash

node:
	@docker compose exec -it node bash

l-node:
	@docker compose logs -f node

r-node:
	@docker compose restart node -t 0

#
# Scripts
#

%:
	@:

npm:
	@docker compose exec -it node npm $(filter-out $@,$(MAKECMDGOALS))

console:
	@docker compose exec -it php symfony console $(filter-out $@,$(MAKECMDGOALS))
