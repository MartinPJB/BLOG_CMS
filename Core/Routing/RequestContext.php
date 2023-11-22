<?php

namespace Core\Routing;

/**
 * Request context class
 */
class RequestContext {
  public string $uri;
  public ?string $route;
  public ?string $action;
  public ?string $id;

  public string $method;
  public array $parameters;

  public function __construct(string $URI, string $method = 'GET', array $parameters = []) {
    $this->uri = $URI;
    $this->method = $method;

    // Get the route name as well as the action and the id if they exist
    $this->route = $parameters['GET']['route'] ?? null;
    $this->action = $parameters['GET']['action'] ?? null;
    $this->id = $parameters['GET']['id'] ?? null;

    // Remove the route name, the action and the id from the parameters array
    unset($parameters['GET']['route']);
    unset($parameters['GET']['action']);
    unset($parameters['GET']['id']);

    // Set the parameters
    $this->parameters = $parameters;
  }

  /**
   * Get the value of uri
   *
   * @return string
   */
  public function getUri(): string {
    return $this->uri;
  }

  /**
   * Get the value of route
   *
   * @return string
   */
  public function getRoute(): string {
    return $this->route;
  }

  /**
   * Get the value of action
   *
   * @return string
   */
  public function getAction(): ?string {
    return $this->action;
  }

  /**
   * Get the value of id
   *
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * Get the value of method
   *
   * @return string
   */
  public function getMethod(): string {
    return $this->method;
  }

  /**
   * Get all the parameters
   *
   * @return array
   */
  public function getParameters(): array {
    return $this->parameters;
  }

  /**
   * Get a parameter
   *
   * @param string $name Parameter name
   * @return string
   */
  public function getParameter(string $name): string {
    return $this->parameters[$name];
  }

  /**
   * Check if a parameter exists
   *
   * @param string $name Parameter name
   * @return bool
   */
  public function hasParameter(string $name): bool {
    return isset($this->parameters[$name]);
  }
}