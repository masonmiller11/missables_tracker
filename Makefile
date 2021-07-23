DOCKER_COMPOSE = docker-compose
EXEC = $(DOCKER_COMPOSE) exec
EXEC_PHP = $(DOCKER_COMPOSE) exec php bash
EXEC_DATABASE = $(EXEC) database -u application -p
SYMFONY = php bin/console
SYMFONY_EXEC = $(EXEC_PHP) php bin/console

##
## Project
## -------
##

build:
	@$(DOCKER_COMPOSE) up --build -d

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

start:
	$(DOCKER_COMPOSE) up -d

stop:
	$(DOCKER_COMPOSE) stop

exec-php:
	$(EXEC_PHP)

exec-database:
	$(EXEC_DATABASE)


##
## Symfony Utils
## -------
##

debug-routes:
	$(SYMFONY) debug:router

debug-autowiring:
	$(SYMFONY) debug:autowiring

debug-container:
	$(SYMFONY) debug:container

migration: vendor
	$(SYMFONY) doctrine:migrations:diff

migrate: vendor
	$(SYMFONY) doctrine:migrations:migrate

exec-debug-routes:
	$(EXEC_PHP) $(SYMFONY) debug:router

exec-debug-autowiring:
	$(EXEC_PHP) $(SYMFONY) debug:autowiring

exec-debug-container:
	$(EXEC_PHP) $(SYMFONY) debug:container

exec-migration: vendor
	$(EXEC_PHP) $(SYMFONY) doctrine:migrations:diff

exec-migrate: vendor
	$(EXEC_PHP) $(SYMFONY) doctrine:migrations:migrate