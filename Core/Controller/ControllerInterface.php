<?php

namespace Core\Controller;

use \Core\RequestContext;

/**
 * Controller interface | Represents a controller
 *
 * @package CUEJ_CMS\Controller
 * @version 0.0.1
 * @since 0.0.1
 * @property string $name Controller name
 * @property string $description Controller description
 */
interface ControllerInterface
{
  /**
   * Constructs the controller with the given request context
   *
   * @param RequestContext $requestContext
   */
  public function __construct(RequestContext $requestContext);

  /**
   * Index action (mandatory), is called when no action is specified
   *
   * @param array $params Parameters of the request context (GET, POST, ...)
   * @example // GET /articles/ -> ArticleController::index()
   */
  public function index($params);
}
