<?php

//Twig container
$container = $app->getContainer();

//View
$container['view'] = function($container)
{
  $view = new \Slim\Views\Twig('../views');

  $router = $container->get('router');
  $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
  $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

  return $view;
};

//DB Connection
$container['db'] = function ($container){
	$db = $container['settings']['db'];
    
  $pdo = new PDO('mysql:host='.$db['host'].';dbname='.$db['name'].';port='.$db['port'], $db['user'], $db['pass']);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	return $pdo;
};

//Register provider
$container['flash'] = function () {
  return new \Slim\Flash\Messages();
};