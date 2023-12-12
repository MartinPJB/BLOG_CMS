<?php

/**
 * Recreation of all missing functions from recent PHP versions for PHP5.4.45 and MySQL 5.1.37
 */

if (!function_exists("password_hash")) {
  // Defines the password constants
  define('PASSWORD_DEFAULT', 1);
  define('PASSWORD_BCRYPT', 1);

  /**
   * Creates a password hash
   *
   * @param string $password The user's password
   * @param int $algo The algorithm to use (PASSWORD_DEFAULT, PASSWORD_BCRYPT, PASSWORD_ARGON2I, PASSWORD_ARGON2ID)
   * @param array $options The options for the algorithm
   * @return string|false Returns the hashed password, or false on error
   */
  function password_hash($password, $algo, array $options = []) {
    if ($algo === PASSWORD_DEFAULT) {
      $algo = PASSWORD_BCRYPT;
    }

    switch ($algo) {
      case PASSWORD_BCRYPT:
        $cost = isset($options['cost']) ? $options['cost'] : 10;
        // Use openssl_random_pseudo_bytes() instead of mcrypt_create_iv()
        $salt = isset($options['salt']) ? $options['salt'] : substr(base64_encode(openssl_random_pseudo_bytes(22)), 0, 22);
        return crypt($password, '$2y$' . $cost . '$' . $salt);
      case PASSWORD_ARGON2I:
      case PASSWORD_ARGON2ID:
        return false;
      default:
        return false;
    }
  }
}

if (!function_exists("password_verify")) {
  /**
   * Verifies that a password matches a hash
   *
   * @param string $password The user's password
   * @param string $hash The hash to verify the password against
   * @return bool Returns true if the password and hash match, or false otherwise
   */
  function password_verify(string $password, string $hash) {
    return crypt($password, $hash) === $hash;
  }
}