.PHONY: help build up down restart logs shell artisan migrate seed test npm-install npm-dev npm-build fresh

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker containers
	docker-compose build

up: ## Start all services
	docker-compose up -d

down: ## Stop all services
	docker-compose down

restart: ## Restart all services
	docker-compose restart

logs: ## View logs
	docker-compose logs -f

shell: ## Access app container shell
	docker-compose exec app bash

artisan: ## Run artisan command (use: make artisan CMD="migrate")
	docker-compose exec app php artisan $(CMD)

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seed
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeder
	docker-compose exec app php artisan db:seed

test: ## Run tests
	docker-compose exec app php artisan test

npm-install: ## Install npm dependencies
	docker-compose exec node npm install

npm-dev: ## Run npm dev
	docker-compose exec node npm run dev

npm-build: ## Build frontend assets
	docker-compose exec node npm run build

fresh: ## Fresh install (build, up, migrate, seed)
	make build
	make up
	sleep 10
	make migrate-fresh
	make npm-install
	make npm-build

composer-install: ## Install composer dependencies
	docker-compose exec app composer install

composer-update: ## Update composer dependencies
	docker-compose exec app composer update
