.PHONY: up down build logs restart clean key

# Основные команды
up:        ## Запустить приложение (production)
	docker compose up -d

down:      ## Остановить и удалить контейнеры
	docker compose down

build:     ## Полностью пересобрать образы
	docker compose build --no-cache

logs:      ## Логи (по умолчанию оба сервиса)
	docker compose logs -f

logs-backend:  ## Только бэкенд
	docker compose logs -f backend

logs-frontend: ## Только фронтенд
	docker compose logs -f frontend

restart: down up ## Перезапустить всё

key:
	@echo "Скопируй эту строку в docker-compose.yml -> environment -> APP_KEY:"
	@docker compose run --rm backend php artisan key:generate --show

migrate:   ## Выполнить миграции (на всякий случай)
	docker compose exec backend php artisan migrate --force

fresh:     ## Полная пересборка + запуск с нуля
	docker compose down -v --remove-orphans
	docker compose build --no-cache
	docker compose up -d

shell-backend:  ## Зайти в контейнер бэкенда
	docker compose exec backend sh

shell-frontend: ## Зайти в контейнер фронтенда
	docker compose exec frontend sh

clean:     ## Удалить всё, включая образы и volumes
	docker compose down -v --rmi all --remove-orphans

help:      ## Показать эту справку
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help