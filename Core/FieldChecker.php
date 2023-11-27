<?php

namespace Core;

/**
 * FieldChecker class | Handles the validation of fields
 */
class FieldChecker {
  /**
   * Prevents direct instantiation of the class
   */
  private function __construct()
  {
  }

  /**
   * Clean a string field to avoid XSS attacks and SQL injections
   *
   * @param string $field The field to clean
   * @return string The cleaned field
   */
  public static function cleanString(string $field): string
  {
    return htmlspecialchars(trim($field));
  }

  /**
   * Check if a field is empty
   *
   * @param mixed $field The field to check
   * @return bool True if the field is not empty, false otherwise
   */
  public static function isEmptyField(mixed $field): bool
  {
    return !empty($field);
  }

  /**
   * Check if a string field is a valid email
   *
   * @param string $field The field to check
   * @return bool True if the field is a valid email, false otherwise
   */
  public static function checkEmail(string $field): bool
  {
    return filter_var($field, FILTER_VALIDATE_EMAIL);
  }

  /**
   * Check if a string field is a valid URL
   *
   * @param string $field The field to check
   * @return bool True if the field is a valid URL, false otherwise
   */
  public static function checkUrl(string $field): bool
  {
    return filter_var($field, FILTER_VALIDATE_URL);
  }

  /**
   * Clean an integer field to avoid XSS attacks and SQL injections
   *
   * @param mixed $field The field to clean
   * @return int The cleaned field
   */
  public static function cleanInt(mixed $field): int
  {
    return intval($field);
  }

  /**
   * Check if a field is a valid integer
   *
   * @param mixed $field The field to check
   * @return bool True if the field is a valid integer, false otherwise
   */
  public static function checkInt(mixed $field): bool
  {
    return filter_var($field, FILTER_VALIDATE_INT);
  }

  /**
   * Check if a field is a valid boolean
   *
   * @param mixed $field The field to check
   * @return bool True if the field is a valid boolean, false otherwise
   */
  public static function checkBool(mixed $field): bool
  {
    return filter_var($field, FILTER_VALIDATE_BOOLEAN);
  }
}