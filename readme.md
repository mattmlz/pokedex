# Pokedex

*Pokedex is a school project using [Slim Framework](https://www.slimframework.com/), and [Twig](https://twig.symfony.com/).* 

## Using
- Download the .zip
- Run virtual server on the file
- Replace the following database connection settings in `app/settings.php` with your personal informations:
```
$settings['db']  =  [];
$settings['db']['host']  =  'localhost';
$settings['db']['port']  =  'port';
$settings['db']['user']  =  'username';
$settings['db']['pass']  =  'password';
$settings['db']['name']  =  'db_name';
```
- Install composer by running the following code in your terminal:
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
- Run `$ composer require slim/slim slim/twig-view slim/flash` in your terminal (*You may need use sudo*)
- Go to : http://localhost:8888/web

## Features:
- [Materialize CSS](http://materializecss.com/)
- Connection and creating account
- [Twig](https://twig.symfony.com/)
- [Slim Flash message](https://www.slimframework.com/docs/v3/features/flash.html)
