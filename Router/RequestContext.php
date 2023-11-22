<?php

namespace Router;

/**
 * Request context class
 */
class RequestContext {
  public string $action;
  public string $method;
  public array $parameters;

  public function __construct(string $action, string $method = 'GET', array $parameters = []) {
    $this->action = $action;
    $this->method = $method;
    $this->parameters = $parameters;
  }

  /**
   * Get the value of action
   *
   * @return string
   */
  public function getAction(): string {
    return $this->action;
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
   * Set a parameter
   *
   * @param string $name Parameter name
   * @param string $value Parameter value
   * @return void
   */
  public function setParameter(string $name, string $value): void {
    $this->parameters[$name] = $value;
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