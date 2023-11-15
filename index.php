<?php

require_once './config.php';
require_once './Include/twig.php';

// Démarre la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) session_start();

// User check
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  $user = new Model\User($config['database']);
  $data = $user->readUser(['role', 'username'], ['id' => $_SESSION['user_id']])[0];
  $_SESSION['user'] = $data;
}

/* Un logger basique histoire de montrer des messages à l'utilisateur */
function basicLogger(string $message)
{
  echo "<p><b>blog$></b> $message</p>";
}

/* Check si la base de données est initialisée, sinon on installe */
$installation_manager = new Include\InstallationManager($config['database']);
$installation_db_name = $config['database']['database'];

if (!$installation_manager->isInstalled($installation_db_name)) {
  basicLogger("Installation du Blog en cours");

  try {
    $installation_manager->install($config);
  } catch (Exception $e) {
    basicLogger("Erreur: " . $e->getMessage());
    $installation_manager->cancel($installation_db_name);
    unset($installation_manager);
    exit;
  }

  basicLogger("Installation terminée, la fenêtre se rechargera dans 5 secondes.");
  header("refresh:5;");
  exit;
}
unset($installation_manager);


// Initialisation d'un routeur primitif
$route = $_GET['route'] ?? NULL;

if (!is_null($route)) {
  $controller = ucfirst($route) . 'Controller';
  $controller = "Controller\\$controller";

  if (class_exists($controller)) {
    $twig = init_twig(__DIR__);
    $twig->addGlobal('session', $_SESSION);
    $twig->addGlobal('GET', $_GET);
    $controller = new $controller($config['database'], $twig);
    $controller->handleRequest();
  } else {
    echo "Erreur: le contrôleur $controller n'existe pas";
  }
}

// $twig = init_twig();
// $elementController = new Controller\ElementController($config['database'], $twig);
// $elementController->handleRequest();

// Unset de la variable $config pour éviter les fuites d'informations
unset($config);
