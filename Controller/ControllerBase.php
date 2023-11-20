<?php

namespace Controller;

class ControllerBase
{
  protected string $route;
  protected array $subRoutes = [];
  protected string $routeFolder;
  protected $twig;
  private bool $admin_only = false;

  /**
   * @param string $route Le nom de la route
   * @param $twig L'instance de Twig
   * @param bool $admin_only Si l'action est réservée aux administrateurs
   */
  protected function __construct(string $route, $twig, bool $admin_only = false)
  {
    $this->route = $route;
    $this->routeFolder = 'pages/' . ucfirst($route) . '/';
    $this->admin_only = $admin_only;
    $this->twig = $twig;
  }

  /**
   * Permet d'ajouter une sous-route au controller.
   *
   * @param string $name Nom de la route
   * @param string $template Nom du template à utiliser
   * @param array $callback Fonction servant d'action à la route (LE CALLBACK DOIT RETOURNER UN ARRAY SI LE TEMPLATE EST UTILISÉ)
   * @param string $method Méthode HTTP à utiliser
   * @param bool $accessLevel (0 = accessible à tous, 1 = accessible quand l'utilisateur est connecté, 2 = accessible quand l'utilisateur n'est pas connecté, 3 = accessible quand l'utilisateur est administrateur)
   * @return void Retourne rien
   */
  protected function addSubRoute(string $name, ?string $template, array $callback, string $method = 'GET', int $accessLevel = 0): void
  {
    $this->subRoutes[$method][$name] = [
      'template' => $template,
      'callback' => $callback,
      'method' => $method,
      'accessLevel' => $accessLevel,
    ];
  }

  /**
   * Gère la demande HTTP en fonction de l'action.
   *
   * @return void Retourne rien
   */
  public function handleRequest(): void
  {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // Si l'utilisateur n'est pas connecté et que l'action est réservée aux administrateurs
    if ($this->admin_only && !isset($_SESSION['user'])) {
      $this->redirectToRoute('user', UserController::ACTION_LOGIN);
    }

    // Si l'utilisateur n'est pas administrateur et que l'action est réservée aux administrateurs
    if ($this->admin_only && $_SESSION['user']['role'] !== 'admin') {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    if (empty($this->subRoutes[$method])) {
      return;
    }

    switch ($method) {
      case 'GET':
      case 'POST':
        $this->handleRequestByMethod($method);
        break;

      default:
        $this->handleRequestByMethod('GET');
        break;
    }
  }

  /**
   * Gère la demande HTTP en GET ou POST.
   *
   * @param string $method La méthode HTTP (GET ou POST)
   * @return void Retourne rien
   */
  private function handleRequestByMethod(string $method): void
  {
    $keys = array_keys($this->subRoutes[$method]);
    $action = $_GET['action'] ?? '';
    $data = [];

    if (empty($action)) {
      $action = reset($keys);
    }

    if (!isset($this->subRoutes[$method][$action])) {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    if (!$this->canAccess($this->subRoutes[$method][$action]['accessLevel'])) {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    $callback = $this->subRoutes[$method][$action]['callback'] ?? null;

    if (!is_callable($callback)) {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    try {
      $data = $callback();
    } catch (\Exception $e) {
      $data = [
        'erreurs' => [
          'Une erreur s\'est produite lors de l\'envoi du formulaire... 😢',
          $e->getMessage(),
        ],
      ];
    }

    if (is_array($data)) {
      $this->render($this->subRoutes[$method][$action], $data);
    }
  }

  /**
   * Vérifie si l'utilisateur peut accéder à la page.
   *
   * @param integer $accessLevel
   * @return bool Retourne true si l'utilisateur peut accéder à la page, false sinon
   */
  private function canAccess(int $accessLevel): bool
  {
    switch ($accessLevel) {
      case 0:
        return true;

      case 1:
        return isset($_SESSION['user']) && isset($_SESSION['user_id']);

      case 2:
        return !isset($_SESSION['user']) || !isset($_SESSION['user_id']);

      case 3:
        return isset($_SESSION['user']) && isset($_SESSION['user_id']) && $_SESSION['user']['role'] === 'admin';

      default:
        return false;
    }
  }

  /**
   * Affiche le template avec les données.
   *
   * @param array $route La route
   * @param array $data Les données
   * @return void Retourne rien
   */
  protected function render(array $route, array $data): void
  {
    $template = $this->twig->load($this->routeFolder . $route['template']);
    echo $template->render($data);
  }

  /**
   * Redirige vers une sous-route.
   *
   * @param string $subRoute
   * @param array $parameters
   * @return void Retourne rien
   */
  protected function redirectToSubroute(string $subRoute, array $parameters = []): void
  {
    $currentPath = $_SERVER['REQUEST_URI'];
    $basePath = array_slice(explode('/', $currentPath), 0, -1)[1];
    $parameters = http_build_query($parameters);
    header("Location: /$basePath/$this->route/$subRoute&$parameters");
    exit;
  }

  /**
   * Redirige vers une route.
   *
   * @param string $subRoute
   * @param array $parameters
   * @return void Retourne rien
   */
  protected function redirectToRoute(string $route, string $subRoute = '', array $parameters = []): void
  {
    $currentPath = $_SERVER['REQUEST_URI'];
    $basePath = array_slice(explode('/', $currentPath), 0, -1)[1];
    $parameters = http_build_query($parameters);
    header("Location: /$basePath/$route/$subRoute&$parameters");
    exit;
  }
}
