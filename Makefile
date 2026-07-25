DC := docker compose

.DEFAULT_GOAL := help

.PHONY: help up down restart build rebuild ps logs shell root-shell artisan composer test pint migrate fresh queue-logs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers
	$(DC) up -d

down: ## Stop and remove all containers
	$(DC) down

restart: down up ## Restart all containers

build: ## Build images without starting containers
	$(DC) build

rebuild: ## Rebuild images from scratch (no cache) and restart containers
	$(DC) build --no-cache
	$(DC) up -d --force-recreate

ps: ## Show container status
	$(DC) ps

logs: ## Tail logs for all containers (use s=<service> for one)
	$(DC) logs -f $(s)

queue-logs: ## Tail queue worker logs
	$(DC) logs -f queue

shell: ## Open a shell in the app container
	$(DC) exec app sh

root-shell: ## Open a root shell in the app container
	$(DC) exec -u root app sh

artisan: ## Run an artisan command, e.g. make artisan cmd="migrate"
	$(DC) exec app php artisan $(cmd)

composer-install: ## Run a composer command, e.g. make composer install
	$(DC) exec app composer install

test: ## Run the test suite
	$(DC) exec app php artisan test --compact

pint: ## Fix code style with Pint
	$(DC) exec app vendor/bin/pint

migrate: ## Run database migrations
	$(DC) exec app php artisan migrate

fresh: ## Drop all tables and re-run migrations
	$(DC) exec app php artisan migrate:fresh
