<?php

namespace Controller;

use Controller\ControllerBase;
use Controller\ControllerInterface;
use Model\User;
use Includes\TypeEscaper;

class UserController extends ControllerBase implements ControllerInterface
{
  const ACTION_LOGIN = 'login';

  const ACTION_LOGOUT = 'logout';

  private User $model;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('user', $twig);
    $this->model = new User($database_credentials);
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Add GET routes
    $this->addSubRoute(self::ACTION_LOGIN, 'login.html.twig', [$this, 'GET_login'], 'GET', 2);
    $this->addSubRoute(self::ACTION_LOGOUT, null, [$this, 'GET_logout'], 'GET', 1);

    // Add POST routes
    $this->addSubRoute(self::ACTION_LOGIN, 'login.html.twig', [$this, 'POST_login'], 'POST', 2);
  }

  /**
   * @return array Retourne un array contenant rien
   */
  public function GET_login(): array
  {
    return [];
  }

  /**
   * @return array Retourne un array contenant rien
   */
  public function GET_register(): array
  {
    return [];
  }

  /**
   * @return array Retourne un array contenant les balises HTML
   */
  public function GET_logout(): array
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user']);
    session_destroy();
    $this->redirectToRoute('articles', '');
    return [];
  }

  /**
   * @return array Retourne un array contenant rien
   */
  public function POST_login(): array
  {
    $erreurs = [];

    if (isset($_POST['user_login'])) {
      // On vérifie que les champs sont remplis
      if (!isset($_POST['username']) || empty($_POST['username'])) {
        $erreurs[] = "Le nom d'utilisateur est obligatoire.";
      }

      if (!isset($_POST['password']) || empty($_POST['password'])) {
        $erreurs[] = "Le mot de passe est obligatoire.";
      }

      // On vérifie que l'utilisateur existe
      $user = $this->model->readUser([], [
        'username' => TypeEscaper::escapeString($_POST['username'])
      ])[0];

      if (!empty($user)) {

        // On vérifie que le mot de passe est correct
        if (password_verify($_POST['password'], $user['password'])) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['user'] = $user;

          $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
        } else {
          $erreurs[] = "Le mot de passe entré est incorrect.";
        }

      } else {
        $erreurs[] = "Aucun utilisateur inscrit ne porte ce nom.";
      }
    }

    return array_merge($this->GET_login(), [
      'erreurs' => $erreurs
    ]);
  }
}
