<?php

namespace Core\Routing;

use \Core\Routing\Route;

/**
 * Router class
 */
class Router {
  protected static array $routes = [];

  private function __construct() {}

  /**
   * Add a route to the routes array
   * @param string $routeName Route name
   * @param string $action Action name
   * @param mixed $controller Controller class
   * @param string $method = 'GET' HTTP method
   * @param array $params = [] Parameters
   * @example Router::addRoute('articles', 'list', ArticleController::class); // GET /articles/list -> ArticleController::list()
   * @example Router::addRoute('articles', 'delete', ArticleController::class, 'POST'); // POST /articles/delete -> ArticleController::delete()
   * @example Router::addRoute('articles', 'edit', ArticleController::class, 'POST', ['id' => 1]); // POST /articles/edit?id=1 -> ArticleController::edit()
   * @return void
   */
  public static function addRoute(string $routeName, string $action, mixed $controller, string $method = 'GET', array $params = []): void {
    $rqContext = new RequestContext($action, $method, $params);
    if (!array_key_exists($routeName, self::$routes)) {
      self::$routes[$routeName] = [];
    }

    self::$routes[$routeName][$action] = new Route($controller, $rqContext);
  }

  /**
   * Get the value of routes
   *
   * @return array
   */
  public static function getRoutes(): array {
    return self::$routes;
  }

  /**
   * Dispatch the route corresponding to the given URI
   * @param string $routeName Route name
   * @param string $action Action name
   * @return void
   */
  public static function dispatchRoute(string $routeName, string $action): void {
    if (array_key_exists($routeName, self::$routes) && array_key_exists($action, self::$routes[$routeName])) {
      $route = self::$routes[$routeName][$action];

      $controller = $route->getController();
      $action = $route->getRequestContext()->getAction();

      $controller->$action();
    } else {
      self::dispatchError(404);
    }
  }

  /**
   * HTTP ERROR CODES
   * @param int $code Error code
   * @return void
   */
  public static function dispatchError(int $code): void {
    switch ($code) {
      case 404:
        header('HTTP/1.0 404 Not Found');
        break;
      case 405:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
      default:
        header('HTTP/1.0 500 Internal Server Error');
        break;
    }
  }
}