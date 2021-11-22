.PHONY: build, up, down, connect, composer, test
build:
	docker compose build
up:
	docker compose up -d --remove-orphans
down:
	docker compose down --remove-orphans
connect:
	docker compose exec php bash
