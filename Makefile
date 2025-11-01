.PHONY: help start stop restart build install migrate seed test artisan npm composer

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

start: ## Start all Docker containers
	docker-compose up -d

stop: ## Stop all Docker containers
	docker-compose down

restart: ## Restart all Docker containers
	docker-compose restart

build: ## Build Docker images
	docker-compose build

install: ## Install dependencies
	docker-compose exec app composer install
	docker-compose exec app npm install

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

fresh: ## Fresh database with seeders
	docker-compose exec app php artisan migrate:fresh --seed

test: ## Run tests
	docker-compose exec app php artisan test

artisan: ## Run artisan command (use: make artisan cmd="route:list")
	docker-compose exec app php artisan $(cmd)

npm: ## Run npm command (use: make npm cmd="run dev")
	docker-compose exec app npm $(cmd)

composer: ## Run composer command (use: make composer cmd="require package")
	docker-compose exec app composer $(cmd)

bash: ## Access app container bash
	docker-compose exec app bash

logs: ## Show logs
	docker-compose logs -f

clean: ## Clean up
	docker-compose down -v
	rm -rf vendor node_modules
