# StreamingPlus

## Requirements

- Xampp v3.3.0
- PHP 8.2.12 or above
- Composer 2.8.2 or above

## Intall composer

``` bash
composer install
```

## Configure .env file

Create and modify your .env file using your desired info.

``` bash
cp .env.example .env
php artisan key:generate
```

```
DB_CONNECTION = CONN (mysql)
DB_HOST = HOST (127.0.0.1)
DB_PORT = PORT (3306)
DB_DATABASE = DATABASE (streamingplus)
DB_USERNAME = USERNAME (root)
DB_PASSWORD = PASSWORD (admin)
```
## Run migrations

Once configured your db params, you'll need to run the migrations, use the following command:

``` bash
php artisan migrate
```

Then, you'll need to run the seeder:

``` bash
php artisan db:seed
```

Finally to execute the project you can use this command:

``` bash
php artisan serve
```
