<?php

namespace Core\Routing;

use \Core\Routing\Route;
use \Core\Routing\RequestContext;

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
   * @param int $accessLevel = 0 Access level required to access the route (0 = public, 1 = user, 2 = admin)
   * @param string $method = 'GET' HTTP method
   * @param array $params = [] Parameters
   * @example Router::addRoute('articles', 'list', ArticleController::class); // GET /articles/list -> ArticleController::list()
   * @example Router::addRoute('articles', 'delete', ArticleController::class, 'POST'); // POST /articles/delete -> ArticleController::delete()
   * @return void
   */
  public static function addRoute(string $routeName, string $action, mixed $controller, int $accessLevel = 0, string $method = 'GET'): void {
    if (!array_key_exists($routeName, self::$routes)) {
      self::$routes[$routeName] = [];
    }
    self::$routes[$routeName][$action] = new Route($controller, $accessLevel, $method);
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
  public static function dispatch(RequestContext $requestContext): void {
    $route = $requestContext->getRoute();
    $action = $requestContext->getAction();

    if (array_key_exists($route, self::$routes) && array_key_exists($action, self::$routes[$route])) {
      $controller = self::$routes[$route][$action]->getController();

      // Get the id from the request context
      $id = $requestContext->getId();
      $params = $requestContext->getParameters();
      $params['id'] = $id;

      // Call the action method of the controller
      $controller = new $controller($requestContext);
      $controller->$action($params);
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