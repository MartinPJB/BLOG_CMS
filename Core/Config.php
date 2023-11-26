<?php

namespace Core;

/**
 * Configuration class | Handles all configuration variables
 */
class Config
{
  private static array $configs = [];


  /**
   * Prevents direct instantiation of the class
   */
  private function __construct()
  {
  }

  /**
   * Set the configuration variables automatically (recursively in case of nested arrays)
   */
  public static function setConfig(array $config, string $prefix = ''): void
  {
    foreach ($config as $key => $value) {
      if (is_array($value)) {
        self::setConfig($value, $prefix . $key . '_');
      } else {
        self::$configs[$prefix . $key] = $value;
      }
    }
  }

  /**
   * Get a value from the configuration
   *
   * @param string $name The name of the configuration variable
   * @return ?string The value of the configuration variable (null if not found)
   */
  public static function get(string $name): ?string
  {
    return self::$configs[$name] ?? null;
  }

  /**
   * Set a value in the configuration
   *
   * @param string $name The name of the configuration variable
   * @param string $value The value of the configuration variable
   */
  public static function set(string $name, string $value): void
  {
    self::$configs[$name] = $value;
  }
}