<?php

// Inclusion des fichiers nécessaires
require_once './default_config.php';
require_once './Includes/twig.php';

// Démarre la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Vérification de l'utilisateur
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
  $user = new Model\Base($config['database'], 'users');
  $userData = $user->readElement(['role', 'username'], ['id' => $_SESSION['user_id']])[0];
  $_SESSION['user'] = $userData;
}

/* Fonction pour afficher des messages */
function basicLogger(string $message)
{
  echo "<p><b>blog$></b> $message</p>";
}

// Vérification de l'installation de la base de données
$installationManager = new Includes\InstallationManager($config['database']);
$installationDbName = $config['database']['database'];

if (!$installationManager->isInstalled($installationDbName)) {
  basicLogger("Installation du Blog en cours");

  try {
    $installationManager->install($config);
  } catch (Exception $e) {
    basicLogger("Erreur: " . $e->getMessage());
    $installationManager->cancel($installationDbName);
    unset($installationManager);
    exit;
  }

  basicLogger("Installation terminée, la fenêtre se rechargera dans 5 secondes.");
  header("refresh:5;");
  exit;
}

unset($installationManager);

// Initialisation d'un routeur primitif
$route = $_GET['route'] ?? NULL;

// Récupération des paramètres du site depuis la base de données
$siteSettingsManager = new Includes\DatabaseManager($config['database']);
$siteSettingsManager->connectToDatabase($config['database']['database']);
$siteSettings = $siteSettingsManager->read('site_settings')[0];

if (!is_null($route)) {
  $controllerName = ucfirst($route) . 'Controller';
  $controllerClass = "Controller\\$controllerName";

  // Initialisation de Twig
  $twig = init_twig(__DIR__, $siteSettings['theme']);

  // Ajout de variables globales à Twig
  $twig->addGlobal('session', $_SESSION);
  $twig->addGlobal('GET', $_GET);

  $siteSettings['project_root'] = $config['root'];
  $twig->addGlobal('SITE_SETTINGS', $siteSettings);

  if (class_exists($controllerClass)) {
    // Initialisation du contrôleur
    $controller = new $controllerClass($config['database'], $twig);
    $controller->handleRequest();
  } else {
    // 404 Not Found
    header("HTTP/1.0 404 Not Found");
    echo $twig->render('Error/404.html.twig');
  }
}

// Si aucune route n'est définie, on redirige vers la route par défaut
if (!isset($_GET["route"]) || empty($_GET["route"])) {
  $defaultRoute = $config['default_route'];
  header("Location: $defaultRoute");
}

// Nettoyage pour éviter les fuites d'informations
unset($siteSettingsManager);
unset($config);
