gendiff:
	docker-compose run --rm php-cli chmod +x bin/gendiff && bin/gendiff file1.json file2.json
