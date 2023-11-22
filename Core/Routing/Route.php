<?php

namespace Core\Routing;

use \Core\Routing\RequestContext;

/**
 * Route class
 */
class Route {
  protected mixed $controller;
  protected RequestContext $requestContext;
  protected int $accessLevel = 0;

  /**
   * Constructor
   *
   * @param mixed $controller Controller class
   * @param RequestContext $requestContext Request context
   * @param int $accessLevel = 0 Access level required to access the route (0 = public, 1 = user, 2 = admin)
   */
  public function __construct(mixed $controller, RequestContext $requestContext, int $accessLevel = 0) {
    $this->controller = $controller;
    $this->requestContext = $requestContext;
    $this->accessLevel = $accessLevel;
  }

  /**
   * Get the controller
   *
   * @return mixed
   */
  public function getController(): mixed {
    return $this->controller;
  }

  /**
   * Get the request context
   *
   * @return RequestContext
   */
  public function getRequestContext(): RequestContext {
    return $this->requestContext;
  }

  /**
   * Get the access level
   *
   * @return int
   */
  public function getAccessLevel(): int {
    return $this->accessLevel;
  }
}