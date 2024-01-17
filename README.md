### Installation project
>(optional!)
>> $ eval `ssh-agent` 
>> $ ssh-add id_rsa 
---
1. git clone git@gitlab.com:redepy/de-headless-symfony.git
2. docker-compose up -d --build
3. docker exec -ti redepy_php /bin/bash
4. COMPOSER_MEMORY_LIMIT=-1 composer update
5. Go to http://docker.loc/ 
6. php bin/console doctrine:database:create (optional!)
7. php bin/console doctrine:migrations:migrate
---
### Custom CLI
## Create admin from console 
> php bin/console app:create-admin login password
---
### Symfony command
1. php bin/console cache:clear
2. php bin/console doctrine:database:drop --force
---
### GraphQl tests
> http://docker.loc/graphiql



