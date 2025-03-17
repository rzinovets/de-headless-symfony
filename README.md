### Custom CMS based on Symfony

### Installation project
1. `git clone ..`
2. `docker-compose up -d --build`
3. `docker exec -ti container_name /bin/bash`
4. `composer install`
5. Go to [http://localhost/](http://localhost/)
6. `php bin/console doctrine:migrations:migrate`
7. `php bin/console doctrine:database:create` (optional!)
8. `/etc/hosts - 127.0.0.1 localhost` (optional!)

---

### XDebug Setup
For detailed instructions on setting up XDebug with PHPStorm and Docker, refer to [XDEBUG_SETUP.md](XDEBUG_SETUP.md).

---

### Custom CLI
#### Create admin from console
> `php bin/console app:create-admin login password`

---

### Symfony commands
1. `php bin/console cache:clear`
2. `php bin/console doctrine:database:drop --force`

---

### GraphQL tests
> [http://localhost/graphiql](http://localhost/graphiql)