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

.PHONY: init-split-repos
init-split-repos:
	docker run -i -v $(BASE_DIRECTORY):/home/dandelion/project -w /home/dandelion/project dandelionphp/dandelion:2.0.0 dandelion split-repository:init:all

.PHONY: split
split:
	docker run -i -v $(BASE_DIRECTORY):/home/dandelion/project -w /home/dandelion/project dandelionphp/dandelion:2.0.0 dandelion split:all $(BRANCH)

.PHONY: release
release:
	docker run -i -v $(BASE_DIRECTORY):/home/dandelion/project -w /home/dandelion/project dandelionphp/dandelion:2.0.0 dandelion release:all $(BRANCH)

.PHONY: ci
ci: phpcs phpcpd phpstan codeception

.PHONY: php-cs-fixer
php-cs-fixer:
	./vendor/bin/php-cs-fixer fix --rules=@PSR12,ordered_imports,no_unused_imports packages/
