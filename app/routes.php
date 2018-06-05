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

// Check login in DB and log or not
/* $app->post('/login/log', function(Request $request, Response $response) {
  $data = $request->getParsedBody();
  $user_data = [];
  $user_data['email'] = filter_var($data['email'], FILTER_SANITIZE_STRING);
  $user_data['password'] = filter_var($data['password'], FILTER_SANITIZE_STRING);
  echo '<pre>';
  var_dump($user_data);
  echo '</pre>';
}); */

// Sign in
$app->get('/sign-in',function(Request $request, Response $response) {
  $dataView = [];
  return $this->view->render($response, 'pages/sign-in.twig', $dataView);
})->setName('sign-in');