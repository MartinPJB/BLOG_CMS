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
  public static function cleanString($field)
  {
    return htmlspecialchars(trim($field));
  }

  /**
   * Check if a field is empty
   *
   * @param mixed $field The field to check
   * @return bool True if the field is not empty, false otherwise
   */
  public static function isEmptyField($field)
  {
    return !empty($field);
  }

  /**
   * Check if a string field is a valid email
   *
   * @param string $field The field to check
   * @return bool True if the field is a valid email, false otherwise
   */
  public static function checkEmail($field)
  {
    return filter_var($field, FILTER_VALIDATE_EMAIL) !== false;
  }

  /**
   * Check if a string field is a valid URL
   *
   * @param string $field The field to check
   * @return bool True if the field is a valid URL, false otherwise
   */
  public static function checkUrl($field)
  {
    return filter_var($field, FILTER_VALIDATE_URL) !== false;
  }

  /**
   * Check if a field is a valid integer
   *
   * @param mixed $field The field to check
   * @return bool True if the field is a valid integer, false otherwise
   */
  public static function checkInt($field)
  {
    return filter_var($field, FILTER_VALIDATE_INT) !== false;
  }

  /**
   * Clean an integer field to avoid XSS attacks and SQL injections
   *
   * @param mixed $field The field to clean
   * @return int The cleaned field
   */
  public static function cleanInt($field)
  {
    return intval($field);
  }

  /**
   * Check if a field is a valid boolean
   *
   * @param mixed $field The field to check
   * @return bool True if the field is a valid boolean, false otherwise
   */
  public static function checkBool($field)
  {
    return filter_var($field, FILTER_VALIDATE_BOOLEAN) !== false;
  }

  /**
   * Clean a boolean field to avoid XSS attacks and SQL injections
   *
   * @param mixed $field The field to clean
   * @return bool The cleaned field
   */
  public static function cleanBool($field)
  {
    return boolval($field);
  }

  /**
   * Check if a field is a date (DD/MM/YYYY)
   *
   * @param mixed $field The field to check
   * @return bool True if the field is a valid date, false otherwise
   */
  public static function checkDate($field)
  {
    $date = explode('/', $field);
    if (count($date) !== 3) {
      return false;
    }
    return checkdate($date[1], $date[0], $date[2]);
  }

  /**
   * Clean a date field to avoid XSS attacks and SQL injections
   *
   * @param mixed $field The field to clean
   * @return string The cleaned field
   */
  public static function cleanDate($field)
  {
    return date('Y-m-d', strtotime($field));
  }
}
