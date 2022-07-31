NODES := projections projects tasks users

.PHONY: code-style
code-style:
	for node in $(NODES); do\
		echo code-style $$node ; \
		docker exec task_manager-php-$$node ./symfony/vendor/bin/php-cs-fixer fix --config ./symfony/.php-cs-fixer.dist.php --allow-risky=yes --dry-run ; \
	done

.PHONY: static-analysis
static-analysis:
	for node in $(NODES); do\
		echo static-analysis $$node ; \
		docker exec task_manager-php-$$node ./symfony/vendor/bin/psalm --config=symfony/psalm.xml --memory-limit=-1 ; \
		docker exec task_manager-php-$$node ./symfony/vendor/bin/psalm --config=symfony/psalm-test.xml --memory-limit=-1 ; \
	done

.PHONY: test
test:
	for node in $(NODES); do\
		echo test $$node ; \
		docker exec task_manager-php-$$node php symfony/bin/phpunit tests ; \
	done

.PHONY: check-all
check-all: code-style static-analysis test

.PHONY: clean-cache
clean-cache:
	for node in $(NODES); do\
		echo clean-cache $$node ; \
		docker exec task_manager-php-$$node rm -rf symfony/var/cache/shared ; \
		docker exec task_manager-php-$$node rm -rf symfony/var/cache/$$node ; \
		docker exec task_manager-php-$$node php symfony/bin/console cache:warmup ; \
	done

.PHONY: migrate
migrate:
	for node in $(NODES); do\
		echo migrate $$node ; \
		docker exec task_manager-php-$$node php symfony/bin/console --no-interaction doctrine:migrations:migrate ; \
	done

.PHONY: supervisor-reload
supervisor-reload:
	for node in $(NODES); do\
		echo supervisor-reload $$node ; \
		docker exec task_manager-php-$$node supervisorctl reload ; \
	done
