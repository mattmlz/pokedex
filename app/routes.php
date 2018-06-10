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

// Login page GET
$app->get('/login',function(Request $request, Response $response) {
  // Get flash messages
  $messages = $this->flash->getMessages();
  return $this->view->render($response, 'pages/login.twig', $messages);
})->setName('login');

// User login POST
$app->post('/login',function(Request $request, Response $response) use ($app){
  $value = $request->getParams();
  $logrequest = $this->db->prepare('SELECT * FROM users WHERE email = :email');
  $logrequest->execute([
    'email' => $value['email'],
    ]);
  $user = $logrequest->fetch();
  if(isset($user) && password_verify($value['password'], $user->password)){
    $_SESSION['logged'] = true;
    $_SESSION['auth'] = [
      'first_name' => $user->first_name,
      'id' => $user->id,
    ];
    return $response->withStatus(302)->withHeader('Location', 'dashboard/profile');
  } else {
    $this->flash->addMessage('error','Wrong email or password ⚠️');
    return $response->withStatus(302)->withHeader('Location', 'login');
  }
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
          'password' => password_hash($value['password'], PASSWORD_DEFAULT),
      ]);
      // Redirect
      return $response->withStatus(302)->withHeader('Location', 'login');
    } else {
      $this->flash->addMessage('error','⚠️ Your email or your password are not corresponding ⚠️');
      return $response->withStatus(302)->withHeader('Location', 'sign-in');
    }
  }
})->setName('sign-in');

// Profile
$app->get('/dashboard/profile', function(Request $request, Response $response) use ($app) {
  if($_SESSION['logged'] === true){
    //Fetch list of liked pokemons from user logged
    $req1 = $this->db->prepare('
    SELECT 
      pokemons.id AS pid, 
      pokemons.name AS pname, 
      pokemons.height AS pheight, 
      pokemons.weight AS pweight, 
      pokemons.picture AS ppicture, 
      liked_pokemons.id_user AS likeuser,
      liked_pokemons.id_pokemon_liked AS likedpokemon
    FROM pokemons
    RIGHT JOIN liked_pokemons ON liked_pokemons.id_pokemon_liked = pokemons.id
    WHERE liked_pokemons.id_user = :iduser');
    $req1->execute([
      'iduser' => $_SESSION['auth']['id'],
    ]);
    $res1 = $req1->fetchAll();

    $dataView = [
      'user' => $_SESSION['auth'],
      'pokemons' => $res1
    ];
    return $this->view->render($response, 'pages/dashboard/profile.twig', $dataView);
  } else {
    $this->flash->addMessage('error','Please connect or reconnect to your account ⚠️');
    return $response->withStatus(302)->withHeader('Location', 'login');
  }
})->setName('profile');

// Search pokemons
$app->get('/dashboard/search', function(Request $request, Response $response) use ($app) {
  if(!isset($_GET['search'])){
    $_GET['search'] = "";
  }
  $searchValue = $_GET['search'];
  //Fetch researched list
  $req1 = $this->db->prepare('
  SELECT 
    pokemons.id AS pid, 
    pokemons.name AS pname, 
    pokemons.height AS pheight, 
    pokemons.weight AS pweight, 
    pokemons.picture AS ppicture, 
    pokemons_types.id_type AS pttypes, 
    types.id AS tid, 
    types.name AS tname
  FROM pokemons 
  RIGHT JOIN pokemons_types ON pokemons.id = pokemons_types.id_pokemon
  RIGHT JOIN types ON types.id = pokemons_types.id_type
  WHERE pokemons.name = :pname');
  $req1->execute([
    'pname' => $searchValue,
  ]);
  $res1 = $req1->fetchAll();

  $messages = $this->flash->getMessages();
  $dataView = [
    'pokemons' => $res1,
  ];
  return $this->view->render($response, 'pages/dashboard/search.twig', $dataView);
})->setName('search');

// List of pokemons
$app->get('/dashboard/list', function(Request $request, Response $response) use ($app) {
  // If no condition is selected or if user want to see all pokemons
  if(!isset($_GET['type']) || $_GET['type'] == 0){
    //Fetch pokemon list
    $req1 = $this->db->query('
      SELECT 
        pokemons.id AS pid, 
        pokemons.name AS pname, 
        pokemons.height AS pheight, 
        pokemons.weight AS pweight, 
        pokemons.picture AS ppicture, 
        pokemons_types.id_type AS pttypes, 
        types.id AS tid, 
        types.name AS tname
      FROM pokemons
      RIGHT JOIN pokemons_types ON pokemons.id = pokemons_types.id_pokemon
      RIGHT JOIN types ON types.id = pokemons_types.id_type
    ');
  } else {
    // If user select a category
    $searchValue = $_GET['type'];
    //Fetch pokemon list
    $req1 = $this->db->prepare('
      SELECT 
        pokemons.id AS pid,
        pokemons.slug AS pslug,
        pokemons.name AS pname,
        pokemons.height AS pheight, 
        pokemons.weight AS pweight,
        pokemons.picture AS ppicture,
        pokemons_types.id_type AS pttypes, 
        types.id AS tid, 
        types.name AS tname
      FROM pokemons
      RIGHT JOIN pokemons_types ON pokemons.id = pokemons_types.id_pokemon
      RIGHT JOIN types ON types.id = pokemons_types.id_type
      WHERE types.id = :tid ');
    $req1->execute([
      'tid' => $searchValue,
    ]);
  }
  $res1 = $req1->fetchAll();
  //Fetch types lists
  $req2 = $this->db->query('SELECT * FROM types');
  $res2 = $req2->fetchAll();
  $dataView = [
    'pokemons' => $res1,
    'types' => $res2,
  ];

  return $this->view->render($response, 'pages/dashboard/list.twig', $dataView);
})->setName('list');

// Add a new pokemon in profile list
$app->post('/dashboard/list', function(Request $request, Response $response) use ($app) {
  $likes = $request->getParams();
  $req1 = $this->db->prepare('INSERT INTO liked_pokemons (id_user, id_pokemon_liked) VALUES (:userid, :likedpokemon)');
  $req1->execute([
    'userid' => $_SESSION['auth']['id'],
    'likedpokemon' => $likes['like'],
  ]);
  return $response->withStatus(302)->withHeader('Location', 'list');
});

// Log out
$app->get('/logout', function(Request $request, Response $response) {
  //prevent trying to destroy session without being connected
  if($_SESSION){
    session_destroy();
    $dataView = [];
    return $this->view->render($response, 'pages/logout.twig', $dataView);
  } else {
    $this->flash->addMessage('error','Please connect or reconnect to your account ⚠️');
    return $response->withStatus(302)->withHeader('Location', 'login');
  }
})->setName('logout');