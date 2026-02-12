export HOST_USER_UID = $(shell id -u)
export HOST_USER_GID = $(shell id -g)

DC_DEV = docker compose -f docker-compose.yaml

up:
	$(DC_DEV) up -d

down:
	$(DC_DEV) down

build:
	$(DC_DEV) build

logs:
	$(DC_DEV) logs

cmd:
	$(DC_DEV) exec -it php-fpm bash -c "$(c)"

console:
	$(DC_DEV) exec -it php-fpm bash -c "php bin/console $(c)"

composer_install:
	$(DC_DEV) run --rm --no-deps -u $(HOST_USER_UID):$(HOST_USER_GID) php-fpm sh -c "composer install"

cc:
	$(DC_DEV) exec -it php-fpm bash -c "php bin/console cache:clear"

mcp_start:
	$(DC_DEV) exec -it php-fpm bash -c "php bin/console dev:mcp-server"

toContainer:
	$(DC_DEV) exec -it php-fpm bash
