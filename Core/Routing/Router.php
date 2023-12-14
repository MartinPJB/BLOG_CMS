<?php

namespace Core\Routing;

use Core\Routing\Route;
use Core\RequestContext;
use Core\Controller\ControllerBase;
use Model\Users;


/**
 * Router class | Manages the routes of the application and dispatches them.
 */
class Router
{
  protected static $routes = [];

  private function __construct()
  {
  }

  /**
   * Add a route to the routes array
   *
   * @param string $routeName Route name
   * @param string $action Action name
   * @param mixed $controller Controller class
   * @param int $accessLevel = 0 Access level required to access the route (0 = public, 1 = user, 2 = admin)
   * @param string $method = 'GET' HTTP method
   * @param array $params = [] Parameters
   * @example Router::addRoute('articles', 'list', ArticleController::class); // GET /articles/list -> ArticleController::list()
   * @example Router::addRoute('articles', 'delete', ArticleController::class, 'POST'); // POST /articles/delete -> ArticleController::delete()
   */
  public static function addRoute($routeName, $action, $controller, $accessLevel = 0, $method = 'GET')
  {
    if (!array_key_exists($routeName, self::$routes)) {
      self::$routes[$routeName] = [];
    }
    self::$routes[$routeName][$action] = new Route($controller, $accessLevel, $method);
  }

  /**
   * Get the value of routes
   *
   * @return array Routes array
   */
  public static function getRoutes()
  {
    return self::$routes;
  }

  /**
   * Check access level of the given route
   */
  public static function checkAccessLevel(RequestContext $requestContext)
  {
    $route = $requestContext->getRoute();
    $action = $requestContext->getAction();

    if ($route && (array_key_exists($route, self::$routes) && array_key_exists($action, self::$routes[$route]))) {
      $accessLevel = self::$routes[$route][$action]->getAccessLevel();
      $user = Users::getAuthentificatedUser();

      if ($accessLevel === 0) {
        // Public route
        return true;
      } elseif ($accessLevel === 1 && $user) {
        // User route
        return true;
      } elseif ($accessLevel === 2 && $user && $user->getRole() == 'admin') {
        // Admin route
        return true;
      }
    }

    return false;
  }

  /**
   * Dispatch the route corresponding to the given URI
   *
   * @param string $routeName Route name
   * @param string $action Action name
   */
  public static function dispatch(RequestContext $requestContext)
  {
    $route = $requestContext->getRoute();
    $action = $requestContext->getAction();

    if ($route && (array_key_exists($route, self::$routes) && array_key_exists($action, self::$routes[$route]))) {
      $controller = self::$routes[$route][$action]->getController();

      // Check if the HTTP method is allowed
      if (self::$routes[$route][$action]->getMethod() !== $requestContext->getMethod()) {
        self::dispatchError(405, $requestContext);
        return;
      }

      // Check if the user has the required access level
      if (!self::checkAccessLevel($requestContext)) {
        self::dispatchError(403, $requestContext);
        return;
      }

      $params = $requestContext->getParameters();

      // If no action defined, it means that the action is set as the index action
      if (!$action) {
        $action = 'index';
      }

      // Call the action method of the controller
      $controller = new $controller($requestContext);
      $controller->$action($params);
    } else {
      self::dispatchError(404, $requestContext);
    }
  }

  /**
   * Dispatch an error code to the client (403, 404, 405, 500)
   *
   * @param int $code Error code
   * @param RequestContext $requestContext The request context

   */
  public static function dispatchError($code, $requestContext)
  {
    ControllerBase::renderError($code, $requestContext);
  }
}
