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
$app->get('/sign-in', function(Request $request, Response $response) {
  $dataView = [];
  return $this->view->render($response, 'pages/sign-in.twig', $dataView);
});

// Sign in
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
  foreach($params as $param=>$options){
    $value = $request->getParams();
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    if($options['required']){
      if(!$value){
        $errors[] = $options['name'].' is required!';
      }
    }
  }
  if($errors){
    $app->flash('errors',$errors);
  } else{
  //submit_to_db($email, $subject, $message);
  $app->flash('message','Inscription successed!');
  }

})->setName('sign-in');