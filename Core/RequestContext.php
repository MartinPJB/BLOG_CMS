<?php

namespace Core;

/**
 * Request context class
 */
class RequestContext
{
  private $uri;
  private $route;
  private $action;
  private $opt_param;

  private $method;
  private $parameters;

  public function __construct($URI, $method = 'GET', $parameters = [])
  {
    $this->uri = $URI;
    $this->method = $method;

    // Get the route name as well as the action and the optional param if they exist
    $this->route = isset($parameters['GET']['route']) ? $parameters['GET']['route'] : null;
    $this->action = isset($parameters['GET']['action']) ? $parameters['GET']['action'] : null;

    $this->opt_param = isset($parameters['GET']['opt_param']) ? $parameters['GET']['opt_param'] : null;
    $this->opt_param = urldecode($this->opt_param);

    // Remove the route name, the action, and the id from the parameters array
    unset($parameters['GET']['route']);
    unset($parameters['GET']['action']);
    unset($parameters['GET']['opt_param']);

    // Set the parameters
    $this->parameters = $parameters;
  }

  /**
   * Get the value of uri
   *
   * @return string URI of the request context
   */
  public function getUri()
  {
    return $this->uri;
  }

  /**
   * Get the value of route
   *
   * @return null|string Route name of the request context (null if not set)
   */
  public function getRoute()
  {
    return $this->route;
  }

  /**
   * Get the value of action
   *
   * @return null|string Action name of the request context (null if not set)
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * Get the value of opt_param
   *
   * @return null|string Optional param of the request context (null if not set)
   */
  public function getOptParam()
  {
    return $this->opt_param;
  }

  /**
   * Get the value of method
   *
   * @return string Method of the request context
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Get all the parameters
   *
   * @return array Parameters of the request context
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Get a parameter
   *
   * @param string $name Parameter name
   * @return string Parameter value
   */
  public function getParameter($name)
  {
    return $this->parameters[$name];
  }

  /**
   * Check if a parameter exists
   *
   * @param string $name Parameter name
   * @return bool True if the parameter exists, false otherwise
   */
  public function hasParameter($name)
  {
    return isset($this->parameters[$name]);
  }
}
