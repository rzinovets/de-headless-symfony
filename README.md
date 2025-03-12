### Custom CMS system based on symfony
### Installation project
1. git clone ..
2. docker-compose up -d --build
3. docker exec -ti redepy_php /bin/bash
4. composer install
5. Go to http://docker.loc/ 
6. php bin/console doctrine:migrations:migrate
7. php bin/console doctrine:database:create (optional!)
8. /etc/hosts - 127.0.0.1 docker.loc (optional!)
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



