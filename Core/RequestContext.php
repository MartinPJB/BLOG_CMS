<?php

namespace Core;

/**
 * Request context class
 */
class RequestContext
{
  private string $uri;
  private ?string $route;
  private ?string $action;
  private ?string $opt_param;

  private string $method;
  private array $parameters;

  public function __construct(string $URI, string $method = 'GET', array $parameters = [])
  {
    $this->uri = $URI;
    $this->method = $method;

    // Get the route name as well as the action and the optional param if they exist
    $this->route = $parameters['GET']['route'] ?? null;
    $this->action = $parameters['GET']['action'] ?? null;

    $this->opt_param = $parameters['GET']['opt_param'] ?? null;
    $this->opt_param = urldecode($this->opt_param);

    // Remove the route name, the action and the id from the parameters array
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
  public function getUri(): string
  {
    return $this->uri;
  }

  /**
   * Get the value of route
   *
   * @return ?string Route name of the request context (null if not set)
   */
  public function getRoute(): ?string
  {
    return $this->route;
  }

  /**
   * Get the value of action
   *
   * @return ?string Action name of the request context (null if not set)
   */
  public function getAction(): ?string
  {
    return $this->action;
  }

  /**
   * Get the value of opt_param
   *
   * @return ?string Optional param of the request context (null if not set)
   */
  public function getOptParam(): ?string
  {
    return $this->opt_param;
  }

  /**
   * Get the value of method
   *
   * @return string Method of the request context
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * Get all the parameters
   *
   * @return array Parameters of the request context
   */
  public function getParameters(): array
  {
    return $this->parameters;
  }

  /**
   * Get a parameter
   *
   * @param string $name Parameter name
   * @return string Parameter value
   */
  public function getParameter(string $name): string
  {
    return $this->parameters[$name];
  }

  /**
   * Check if a parameter exists
   *
   * @param string $name Parameter name
   * @return bool True if the parameter exists, false otherwise
   */
  public function hasParameter(string $name): bool
  {
    return isset($this->parameters[$name]);
  }
}
