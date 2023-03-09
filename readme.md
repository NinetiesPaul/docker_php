## This is a PHP's Symfony base application running on Docker

After cloning this rep, cd into the project's folder and run the following commands:

- **```docker-compose build```**
- **```docker-compose up -d```**
- **```docker-compose exec php composer install```**
- **```docker-compose exec php php bin/console doctrine:migrations:migrate```**
- **```docker-compose exec php php bin/console lexik:jwt:generate-keypair```**
