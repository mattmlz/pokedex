<?php

// Namespaces
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ROUTES */
// Home
$app->get('/',function(Request $request, Response $response) {
  $dataView = [];
  return $this->view->render($response, 'pages/home.twig', $dataView);
})->setName('home');

// Login
$app->get('/login',function(Request $request, Response $response) {
  $dataView = [];
  return $this->view->render($response, 'pages/login.twig', $dataView);
})->setName('login');