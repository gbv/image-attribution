info:
	@echo "Usage: make install|test|run"

install:
	composer install

test:
	composer test

run:
	php -S localhost:8080 -t example
	
