<?php
session_start();
// Namespaces
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/* ROUTES */
// Home
$app->get('/',function(Request $request, Response $response) {
  $dataView = [];
  return $this->view->render($response, 'pages/home.twig', $dataView);
})->setName('home');


// Login page
$app->get('/login',function(Request $request, Response $response) {
  // Get flash messages
  $messages = $this->flash->getMessages();
  return $this->view->render($response, 'pages/login.twig', $messages);
})->setName('login');

// Login page
$app->post('/login',function(Request $request, Response $response) {
  // Get flash messages
  $messages = $this->flash->getMessages();
  return $this->view->render($response, 'pages/login.twig', $messages);
});

// Sign in
$app->get('/sign-in', function(Request $request, Response $response) {
  // Get flash messages
  $messages = $this->flash->getMessages();
  return $this->view->render($response, 'pages/sign-in.twig', $messages);
});

// Create account
$app->post('/sign-in', function(Request $request, Response $response) use ($app) {
  //Set parameters for datas handling
  $errors = [];
  $params = [
    'first_name' => [
      'name' => 'First name',
      'required' => 'true',
    ],
    'last_name' => [
      'name' => 'Last name',
      'required' => 'true',
    ],
    'email' => [
      'name' => 'E-mail',
      'required' => 'true',
    ],
    'password' => [
      'name' => 'Password',
      'required' => 'true',
    ],
  ];
  //Handle datas
  $value = $request->getParams();
  foreach($params as $param => $options){
    if($options['required']){
      if(!$value){
        $errors[] = $options['name'].' is required!';
      }
    }
  }

  if($errors){
    $this->flash->addMessage('errors',$errors);
  } else{
    if($value['email'] === $value['confirm-email'] && $value['password'] === $value['confirm-password']){
      // Create success message
      $this->flash->addMessage('success','Inscription successed! Welcome to Pokedex 💕 Please log-in.');

      // Register new user to DB
      $request = $this->db->prepare('INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)');
      $request->execute([
          'first_name' => $value['first-name'],
          'last_name' => $value['last-name'],
          'email' => $value['email'],
          'password' => $value['password'],
      ]);
      // Redirect
      return $response->withStatus(302)->withHeader('Location', 'login');
    } else {
      $this->flash->addMessage('error','⚠️ Your email or your password are not corresponding ⚠️');
      return $response->withStatus(302)->withHeader('Location', 'sign-in');
    }
  }
})->setName('sign-in');