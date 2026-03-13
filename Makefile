.PHONY: build up down restart shell migrate seed fresh test pint larastan analyse queue install

install:
	@test -f .env || (cp .env.example .env && echo ".env utworzony z .env.example")
	docker compose build
	docker compose up -d
	@echo "Oczekiwanie na gotowość kontenerów..."
	@sleep 10
	docker compose exec app php artisan key:generate --force
	docker compose exec app php artisan migrate --force
	docker compose exec app php artisan db:seed --force
	@echo ""
	@echo "====================================="
	@echo "  Aplikacja gotowa!"
	@echo "  URL: http://localhost:8000/api"
	@echo ""
	@echo "  Dane logowania:"
	@echo "  Email:  test@example.com"
	@echo "  Hasło:  password"
	@echo "====================================="

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down && docker compose up -d

shell:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

fresh:
	docker compose exec app php artisan migrate:fresh --seed

test:
	docker compose exec app php artisan test

pint:
	docker compose exec app ./vendor/bin/pint

larastan:
	docker compose exec app ./vendor/bin/phpstan analyse --memory-limit=512M

analyse: pint larastan

queue:
	docker compose exec app php artisan queue:work --tries=3

logs:
	docker compose logs -f

tinker:
	docker compose exec app php artisan tinker
