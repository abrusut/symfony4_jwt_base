#!/bin/bash

DOCKER_BE = docker_web733_1
DOCKER_DB = docker_db_1
UID = $(shell id -u)

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

run: ## Start the containers
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose down

restart: ## Restart the containers
	U_ID=${UID} docker-compose down && U_ID=${UID} docker-compose up -d

build: ## Rebuilds all the containers
	U_ID=${UID} docker-compose build

ssh-be: ## ssh's into the be container
	U_ID=${UID} docker exec -it -uappuser ${DOCKER_BE} bash

ssh-db: ## ssh's into the db container as root user
	U_ID=${UID} docker exec -it ${DOCKER_DB} mysql -uroot -proot database

be-logs: ## Tails the Symfony dev log
	U_ID=${UID} docker exec -it ${DOCKER_BE} tail -f var/logs/dev.log

composer-install: ## Install composer dependencies
	U_ID=${UID} docker exec -it ${DOCKER_BE} composer install

migrations: ## Runs the migrations
	U_ID=${UID} docker exec -it ${DOCKER_BE} bin/console doctrine:migrations:migrate -n

setup-nfs-macos: ## Install NFS in your machine (use only in MacOS machines)
	chmod +x bin/setup_native_nfs_docker_osx.sh
	./bin/setup_native_nfs_docker_osx.sh

generate-ssh-keys:	##Generate ssh keys in the container
	U_ID=${UID} docker exec -it ${DOCKER_BE} mkdir -p /home/abrusut/public_html/s4apiPlatform/project/config/jwt
	U_ID=${UID} docker exec -it ${DOCKER_BE} openssl genrsa -out /home/abrusut/public_html/s4apiPlatform/project/config/jwt/private.pem -aes256 4096
	U_ID=${UID} docker exec -it ${DOCKER_BE} openssl rsa -pubout -in /home/abrusut/public_html/s4apiPlatform/project/config/jwt/private.pem -out /home/abrusut/public_html/s4apiPlatform/project/config/jwt/public.pem
