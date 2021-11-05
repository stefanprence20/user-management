# user-management-api
User management API, build with Symfony v4.4

![Travis (.org)](https://img.shields.io/travis/demartis/symfony5-rest-api)
![GitHub last commit](https://img.shields.io/github/last-commit/demartis/symfony5-rest-api.svg)
![GitHub repo size in bytes](https://img.shields.io/github/repo-size/demartis/symfony5-rest-api.svg)
![GitHub language count](https://img.shields.io/github/languages/count/demartis/symfony5-rest-api.svg)
![GitHub top language](https://img.shields.io/github/languages/top/demartis/symfony5-rest-api)
![PHP from Travis config](https://img.shields.io/travis/php-v/demartis/symfony5-rest-api/master)
![GitHub](https://img.shields.io/github/license/demartis/symfony5-rest-api)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fdemartis%2Fsymfony5-rest-api.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fdemartis%2Fsymfony5-rest-api?ref=badge_shield)

### For API testing and information go to [/api](http://localhost:8000/api)
## Installation:

```bash
cp .env.example .env
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start
```


## Admin credentials:

```bash
email: admin@example.com
password: pass_1234
```
