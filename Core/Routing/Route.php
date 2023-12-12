<?php

namespace Core\Routing;

/**
 * Route class | Represents a route
 */
class Route
{
  protected $controller;
  protected $method;
  protected $accessLevel = 0;

  /**
   * Constructor of the Route class
   *
   * @param mixed $controller Controller class
   * @param int $accessLevel = 0 Access level required to access the route (0 = public, 1 = user, 2 = admin)
   * @param string $method = 'GET' HTTP method
   */
  public function __construct($controller, $accessLevel = 0, $method = 'GET')
  {
    $this->controller = $controller;
    $this->accessLevel = $accessLevel;
    $this->method = $method;
  }

  /**
   * Get the controller
   *
   * @return mixed Controller class
   */
  public function getController()
  {
    return $this->controller;
  }

  /**
   * Get the access level
   *
   * @return int Access level required to access the route (0 = public, 1 = user, 2 = admin)
   */
  public function getAccessLevel()
  {
    return $this->accessLevel;
  }

  /**
   * Get the method
   *
   * @return string Method of the route
   */
  public function getMethod()
  {
    return $this->method;
  }
}
