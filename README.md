# Custom CMS based on Symfony

![Symfony](https://img.shields.io/badge/Symfony-6.3-blue.svg?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg?style=flat-square)
![Docker](https://img.shields.io/badge/Docker-✔-blue.svg?style=flat-square)

## 📌 Project Information
A custom CMS built on Symfony, designed for flexibility and ease of development. This project utilizes Docker for containerized development and supports GraphQL.

### 🔖 Version
**Current version:** 1.0.0

---

## 🚀 Installation Guide

Follow these steps to set up the project on your local machine:

```sh
# 1. Clone the repository
git clone <repository_url>
cd <project_directory>

# 2. Start Docker containers
docker-compose up -d --build

# 3. Access the application container
docker exec -ti container_name /bin/bash

# 4. Install dependencies
composer install

# 5. Open the application in a browser
# http://localhost/

# 6. Run database migrations
php bin/console doctrine:migrations:migrate

# 7. (Optional) Create the database manually
php bin/console doctrine:database:create

# 8. (Optional) Add a host entry (Linux/macOS)
echo '127.0.0.1 localhost' | sudo tee -a /etc/hosts
```

---

## 🐛 XDebug Setup

For detailed instructions on setting up XDebug with PHPStorm and Docker, refer to the dedicated guide: [XDEBUG_SETUP.md](XDEBUG_SETUP.md).

---

## 📌 Custom CLI Commands

### 🛠 Create Admin User
```sh
php bin/console app:create-admin <login> <password>
```

### 🏗 Symfony Commands
```sh
# 1. Clear cache
php bin/console cache:clear

# 2. Drop database (force delete!)
php bin/console doctrine:database:drop --force
```

---

## 🔬 GraphQL Testing

To test GraphQL queries, navigate to:
[http://localhost/graphiql](http://localhost/graphiql)

---

📌 _Happy coding! 🚀_