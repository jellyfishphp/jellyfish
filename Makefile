BASE_DIRECTORY ?= $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))

.PHONY: install
install:
	composer install

.PHONY: phpcs
phpcs:
	./vendor/bin/phpcs --standard=./vendor/squizlabs/php_codesniffer/src/Standards/PSR12/ruleset.xml --exclude=Generic.Files.LineLength ./packages/*/src/*

.PHONY: phpcpd
phpcpd:
	./vendor/bin/phpcpd ./packages/*/src/

.PHONY: phpstan
phpstan:
	./vendor/bin/phpstan analyse ./packages/*/src/

.PHONY: codeception
codeception:
	./vendor/bin/codecept run --coverage --coverage-xml --coverage-html

.PHONY: prepare-dandelion-config
prepare-dandelion-config:
	sed -i "s/<GITHUB_TOKEN>/$(GITHUB_TOKEN)/" $(BASE_DIRECTORY)/dandelion.json

.PHONY: split
split:
	docker run -i -v $(BASE_DIRECTORY):/home/dandelion/project -w /home/dandelion/project dandelionphp/dandelion:latest dandelion split:all $(BRANCH)

.PHONY: release
release:
	docker run -i -v $(BASE_DIRECTORY):/home/dandelion/project -w /home/dandelion/project dandelionphp/dandelion:latest dandelion release:all $(BRANCH)

.PHONY: test
test: phpcs phpcpd phpstan codeception
