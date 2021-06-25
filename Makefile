install:
	docker-compose run php composer install

test:
	docker-compose run php vendor/bin/phpunit tests