# Run containers
up:
	docker-compose up -d --build

# Stop containers
down:
	docker-compose down

# Run migrations
migrate:
	docker exec -it task-management-app php artisan migrate --seed
# Generate app key
key:
	docker exec -it task-management-app php artisan key:generate

# Create storage symlink
storage:
	docker exec -it task-management-app php artisan storage:link

# Tail logs
logs:
	docker-compose logs -f app

tinker:
	docker exec -it task-management-app php artisan tinker

test:
	docker exec -it task-management-app php artisan test