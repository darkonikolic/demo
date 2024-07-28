create: build npm-install npm-build composer-install up migration-run down

up: server-start

build:
	docker compose build

down:
	docker compose down

stop: down

npm-install:
	docker compose run --rm npm install

npm-build:
	docker compose run --rm npm run build

server-build-start:
	docker compose up -d server

server-start:
	docker compose up -d server

php-enter:
	docker compose exec php sh

composer-install:
	docker compose run --rm --build composer install

composer-update:
	docker compose run --rm --build composer install

composer-require-example:
	docker compose run --rm --build composer require api

mysql-enter:
	docker compose exec mysql sh

mysql-test-enter:
	docker compose exec mysql-test sh

clear-cache:
	docker compose run --rm console ca:cl

entity-new:
	docker compose run --rm console make:entity

entity-user:
	docker compose run --rm console make:user

migration-new:
	docker compose run --rm console make:migration

migration-run:
	docker compose run --rm console doctrine:migrations:migrate

fixture-make:
	docker compose run --rm console make:factory

tests:
	docker compose run --rm test

mysql-drop-db:
	docker compose run --rm console doctrine:database:drop --force

mysql-create-db:
	docker compose run --rm console doctrine:database:create

mysql-init: mysql-create-db migration-run

mysql-load-fixture:
	docker compose run --rm console doctrine:fixtures:load

mysql-recreate-db: mysql-drop-db mysql-init

mysql-rebuild-and-load-from-files: mysql-recreate-db import-data-from-files

debug-router:
	docker compose run --rm console debug:router

debug-config-api:
	docker compose run --rm console debug:config api_platform

dump-config-api:
	docker compose run --rm console config:dump api_platform

#example how to execute command
import-data-from-files:
	docker compose run --rm console import:files
