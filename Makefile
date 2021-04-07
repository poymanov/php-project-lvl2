gendiff:
	docker-compose run --rm php-cli chmod +x bin/gendiff && bin/gendiff file1.json file2.json

lint:
	docker-compose run --rm php-cli composer run-script phpcs -- --standard=PSR12 src bin
