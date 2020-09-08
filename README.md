# SlimM4API - API POC

RESTful API proof of concept manage customers and users with JWT.

Based on: `PHP 7, Slim 4, MySQL, PHPUnit, OpenSSL`.

Made with [slim4](https://github.com/slimphp/Slim).

[![Software License][ico-license]](LICENSE.md)

## QUICK INSTALL:

### Pre Requisite:

- PHP 7.2+.
- Composer.
- MySQL/MariaDB.
- OpenSSL


### Composer:

Create a new project:
```bash
$ composer create-project r3md3v/slim4api [my-api-name]
$ cd [my-api-name]
$ composer test
$ composer start
```


### JWT/JSON Web Token keys:

Generate private key and public keys with these commands:
```bash
$gen_cert.sh
$gen_jwt_key.sh
```
or
```
openssl genrsa -out private.pem 2048
openssl rsa -in private.pem -outform PEM -pubout -out public.pem
```


### Create database:

Create a new DB and execute `db.sql` to create 5 tables users customers logins loginlog logtoken (automatic for docker).


#### Configure app (settings.php):

Configure error reporting for development or production (below is production):
```
error_reporting(0);
ini_set('display_errors', '0');
$display_error_details = false;
```

Configure MySQL connection:
```
$settings['db'] = [
	'driver' => 'mysql',
	'host' => 'yourMySqlHost',
	'database' => 'yourMySqlDatabase',
	'username' => 'yourMySqlUsername',
	'password' => 'yourMySqlPassword',
```

Specify redirection if other than 8080:
```
$settings['redirection'] = [
    'port' => 8080,
    'servername' => 'localhost',
];
```
Configure JWT:
- Change issuer, lifetime;
- Copy content of private and public keys generated previously;
- Enable/Disable log of logins and tokens;
- Specify Token log retention.
```
$settings['jwt'] = [
    // The issuer name
    'issuer' => 'www.slim4api.com',
    // Max lifetime in seconds
    'lifetime' => 14400, // 14400 seconds = 4 hours (86400 = 24 hours)
    // Log logins & tokens
    'loglogins' => true, // true or false
    'logtokens' => true, // true or false (to keep stateless)
    // Token log cleanup
    'retention' => 60 * 60 * 24 * 30, // 30 days
    // The private key
    'private_key' => '-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----',
    // The public key
    'public_key' => '-----BEGIN PUBLIC KEY-----
-----END PUBLIC KEY-----',
];
```
 

#### Populate DB:

- For POC purpose, users customers loglogins and logtokens tables have 5 lines
- Run thess command as many times a needed, to populate 500 additionnal lines of data thanks to great faker:
```
$ composer fakercu (customers and users tables)
$ composer fakerlg (loglogins and logtokens tables)
```


## DEPENDENCIES:

### LIST OF REQUIRE DEPENDENCIES:

- For basic app: slim/slim slim/psr7 selective/basepath slim/monolog php-di/php-di
- For Swagger: doctrine/annotations slim/twig-view symfony/yaml
- For JWT: lcobucci/jwt symfony/polyfill-uuid cakephp/chronos


### LIST OF DEVELOPMENT DEPENDENCIES:

- phpunit/phpunit fzaninotto/faker


## ENDPOINTS AND VERBS:

### Default:

- Status: `GET /`
- Status: `GET /status`

### Hello:
- Hello World: `GET /hello`
- Hello Name: `GET /hello/{name}`

### Swagger:
- SwaggerUI: `GET /docs/v1` (with demo file petstore.yaml v3)

### Customers:
- Get customer by id: `GET /customers/id/{id}`
- Create user with data `POST /customers` cusname,address,city,phone,email
- List of customers: `GET /customers`
	- option page = startpage (default 1)
	- option size = page size (default 50)
- Search customer: `GET /customers/search/{keyword}`
	- option page+size
	- option in = field number where keyword is searched (default 1 / all fields if not set)

### Users:
- Get user by id: `GET /users/id/{id}`
- Create user with data `POST /users` username,password,firstname,lastname,email,profile
	- option page = startpage (default 1)
	- option size = page size (default 50)
- Search user: `GET /users/search/{keyword}?page=1&size=50&in=2`
	- option page+size
	- option in = field number where keyword is searched (default 1 / all fields if not set)

### Logins:
- Create token: `POST /tokens` username or email/password
- Delete token: `GET /logout`
- CLeanup logins+token: `GET /cleanup`


## FORMS:

- createUserForm.php = triggers `POST /users` username,password,firstname,lastname,email,profile 
- createCustomerForm.php = triggers `POST /customers` cusname,address,city,phone,email
- login.php = required to trigger `/users` endpoint
- checkJWTForm.php = gives detail about a JSON Web Token
- hashPWDForm.php = returns a BCRYPT hashed version of a string


## JWT IN ACTION:

- Access to endpoint /users is protected by JWT and require issuance of a token via endpoint /tokens or form login.php.
- Each login attempt is recorded in table loginlog


## Docker

#info create containers
- https://vonkrafft.fr/console/simple-site-php-avec-docker-nginx/
- https://dev.to/martinpham/symfony-5-development-with-docker-4hj8

## start
```
docker-compose up -d 
```
##start db + php + app
```
docker-compose -f docker-compose-nginx.yml up -d mysql php-fpm
docker-compose -f docker-compose-nginx.yml up -d my_app
docker-compose -f docker-compose-nginx.yml logs -f
```
## reload conf

### docker
* dkr_reload_nginx.sh:
```
docker-compose -f docker-compose-nginx.yml exec my_app nginx -s reload
```

## faker - populate database
### docker
* dkr_faker.sh:

```
docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_customers.php
docker-compose -f docker-compose-nginx.yml exec php-fpm php faker_users.php
```

## THAT'S IT!
Have fun!
