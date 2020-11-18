DOCKER_RUN                             := @docker run --rm
COMPOSER_BASE_CONTAINER                := -v $$(pwd):/app --user $$(id -u):$$(id -g) composer:1
COMPOSER_WC_SMOOTH_GENERATOR_CONTAINER := -v $$(pwd)/wordpress/wp-content/plugins/wc-smooth-generator:/app --user $$(id -u):$$(id -g) composer:1
NODE_IMAGE                             := -w /home/node/app -v $$(pwd):/home/node/app --user node woonode
HAS_LANDO                              := $(shell command -v lando 2> /dev/null)
CURRENTUSER                            := $$(id -u)
CURRENTGROUP                           := $$(id -g)
HIGHLIGHT                              :=\033[0;32m
END_HIGHLIGHT                          :=\033[0m # No Color

.PHONY: build
build: build-docker build-assets

.PHONY: build-assets
build-assets: | build-docker-node install-npm
	@echo "Building plugin assets"
	rm -f plugin/languages/*.pot plugin/scripts/*-min.js
	$(DOCKER_RUN) $(NODE_IMAGE) ./node_modules/gulp-cli/bin/gulp.js

.PHONY: build-docker
build-docker: build-docker-node build-docker-php

.PHONY: build-docker-node
build-docker-node:
	if [ ! "$$(docker images | grep woonode)" ]; then \
		echo "Building the Node image"; \
		docker build \
			-f Docker/Dockerfile-node \
			--build-arg UID=$(CURRENTUSER) \
			--build-arg GID=$(CURRENTUSER) \
			-t woonode .; \
	fi

.PHONY: build-docker-php
build-docker-php:
	if [ ! "$$(docker images | grep woounit)" ]; then \
		echo "Building the PHP image"; \
		docker build -f Docker/Dockerfile-php -t woounit .; \
	fi

.PHONY: clean
clean: clean-assets clean-build

.PHONY: clean-assets
clean-assets:
	@echo "Cleaning up plugin assets"
	rm -rf \
		plugin/languages/*.pot  \
		plugin/scripts/*-min.js

.PHONY: clean-build
clean-build:
	@echo "Cleaning up build-artifacts"
	rm -rf \
		node_modules \
		wordpress \
		build \
		vendor \
		clover.xml \
		.phpunit.result.cache


.PHONY: install
install: | clean-assets clean-build
	$(MAKE) install-composer
	$(MAKE) install-npm

.PHONY: install-composer
install-composer:
	$(DOCKER_RUN) $(COMPOSER_BASE_CONTAINER) install

.PHONY: install-npm
install-npm: | build-docker-node
	$(DOCKER_RUN) $(NODE_IMAGE) npm install

.PHONY: lando-start
lando-start:
ifdef HAS_LANDO
	if [ ! -d ./wordpress/ ]; then \
		$(MAKE) install; \
		$(MAKE) build-assets; \
	fi
	if [ ! "$$(docker ps | grep countthewords_appserver)" ]; then \
		echo "Starting Lando"; \
		lando start; \
	fi
	if [ ! -f ./wordpress/wp-config.php ]; then \
		$(MAKE) setup-wordpress; \
		$(MAKE) setup-wordpress-plugins; \
		$(MAKE) setup-woocommerce; \
		$(MAKE) setup-sample-data; \
		echo "You can open your dev site at: ${HIGHLIGHT}https://count-the-words.lndo.site${END_HIGHLIGHT}"; \
		echo "See the readme for further details."; \
	fi
endif

.PHONY: lando-stop
lando-stop:
ifdef HAS_LANDO
	if [ "$$(docker ps | grep countthewords_appserver)" ]; then \
		echo "Stopping Lando"; \
		lando stop; \
	fi
endif

.PHONY: release
release: | build-assets count-the-words.zip

.PHONY: reset
reset: stop clean

.PHONY: setup-sample-data
setup-sample-data:
	@echo "Setting up sample WooCommerce data"
	if [ ! -d ./wordpress/wp-content/plugins/wc-smooth-generator/ ]; then \
		git clone https://github.com/woocommerce/wc-smooth-generator.git ./wordpress/wp-content/plugins/wc-smooth-generator; \
	fi
	$(DOCKER_RUN) $(COMPOSER_WC_SMOOTH_GENERATOR_CONTAINER) install
	lando wp plugin activate wc-smooth-generator --path=./wordpress
	lando wp --path=./wordpress wc generate products 20
	lando wp --path=./wordpress wc generate customers 10
	lando wp --path=./wordpress wc generate orders 30

.PHONY: setup-woocommerce
setup-woocommerce:
	@echo "Setting up WooCommerce"
	lando wp theme install --path=./wordpress storefront --activate
	lando wp plugin install --path=./wordpress woocommerce --activate
	lando wp option update --path=./wordpress woocommerce_store_address '504 Lavaca St'
	lando wp option update --path=./wordpress woocommerce_store_city 'Austin'
	lando wp option update --path=./wordpress woocommerce_default_country 'US:TX'
	lando wp option update --path=./wordpress woocommerce_store_postcode '78701'
	lando wp option update --path=./wordpress woocommerce_currency 'USD'
	lando wp option update --path=./wordpress woocommerce_weight_unit 'oz'
	lando wp option update --path=./wordpress woocommerce_dimension_unit 'in'
	lando wp option update --path=./wordpress woocommerce_demo_store 'yes'
	lando wp option update --path=./wordpress woocommerce_product_type 'both'
	lando wp option update --path=./wordpress woocommerce_setup_shipping_labels '1'
	lando wp wc tool run install_pages --user=1 --path=./wordpress

.PHONY: setup-wordpress
setup-wordpress:
	@echo "Setting up WordPress"
	lando wp config create --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=database --path=./wordpress
	lando wp core install --path=./wordpress --url=https://count-the-words.lndo.site --title="Count the Words Development" --admin_user=admin --admin_password=password --admin_email=contact@chriswiegman.com

.PHONY: setup-wordpress-plugins
setup-wordpress-plugins:
	lando wp plugin install --path=./wordpress debug-bar --activate
	lando wp plugin install --path=./wordpress query-monitor --activate

.PHONY: start
start: lando-start

.PHONY: stop
stop: lando-stop

.PHONY: test
test: test-lint test-unit

.PHONY: test-lint
test-lint: test-lint-php test-lint-javascript

.PHONY: test-lint-javascript
test-lint-javascript: | build-docker-node
	@echo "Running JavaScript linting"
	$(DOCKER_RUN) $(NODE_IMAGE) ./node_modules/jshint/bin/jshint

.PHONY: test-lint-php
test-lint-php:
	@echo "Running PHP linting"
	./vendor/bin/phpcs --standard=./phpcs.xml

.PHONY: test-unit
test-unit: | build-docker-php
	@echo "Running Unit Tests Without Coverage"
	docker run -v $$(pwd):/app --rm woounit /app/vendor/bin/phpunit

.PHONY: test-unit-coverage
test-unit-coverage: | build-docker-php
	@echo "Running Unit Tests With Coverage"
	docker run -v $$(pwd):/app --rm --user $$(id -u):$$(id -g) woounit /app/vendor/bin/phpunit  --coverage-text --coverage-html build/coverage/

.PHONY: update-composer
update-composer: lando-stop
	$(DOCKER_RUN) $(COMPOSER_BASE_CONTAINER) update
	@echo "Composer updated. If your site had been running please run make start again to access it"

.PHONY: update-npm
update-npm: | build-docker-node
	$(DOCKER_RUN) $(NODE_IMAGE) npm update

count-the-words.zip:
	@echo "Building release file: count-the-words.zip"
	rm -f count-the-words.zip
	cd plugin; zip -r count-the-words.zip *
	mv plugin/count-the-words.zip ./
