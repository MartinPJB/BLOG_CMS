<?php

namespace Model;

use \Core\Database\Manager;

/**
 * Users model | Handles all actions related to users
 */
class Users {
  private $id;
  private $username;
  private $password;
  private $email;
  private $role;

  /**
   * Constructor for the Users model
   *
   * @param integer $id User ID
   * @param string $username User username
   * @param string|null $password User password
   * @param string $email User email
   * @param string $role User role
   */
  public function __construct(
    $id,
    $username,
    $password,
    $email,
    $role
  ) {
    $this->id = $id;
    $this->username = $username;
    $this->password = $password;
    $this->email = $email;
    $this->role = $role;
  }

  /**
   * Get all users from the database
   *
   * @return array Array of users
   */
  public static function getAllUsers()
  {
    $users = Manager::read('users');
    $result = [];

    foreach ($users as $key => $user) {
      $result[] = new self(
        $user['id'],
        $user['username'],
        $user['password'],
        $user['email'],
        $user['role']
      );
    }

    return $result;
  }

  /**
   * Get a user from the database
   *
   * @param integer $id User ID
   * @return Users|null User object (null if not found)
   */
  public static function getUser($id)
  {
    $user = Manager::read('users', [], ['id' => $id]);

    if (empty($user)) {
      return null;
    }

    return new self(
      $user[0]['id'],
      $user[0]['username'],
      $user[0]['password'],
      $user[0]['email'],
      $user[0]['role']
    );
  }

  /**
   * Get a user from the database by its email
   *
   * @param string $email User email
   * @return Users|null User object (null if not found)
   */
  public static function getUserByEmail($email)
  {
    $user = Manager::read('users', [], ['email' => $email]);

    if (empty($user)) {
      return null;
    }

    return new self(
      $user[0]['id'],
      $user[0]['username'],
      $user[0]['password'],
      $user[0]['email'],
      $user[0]['role']
    );
  }

  /**
   * Create a user in the database
   *
   * @param string $username User username
   * @param string $password User password
   * @param string $email User email
   * @param string $role User role
   */
  public static function create(
    $username,
    $password,
    $email,
    $role
  ) {
    Manager::create('users', [
      'username' => $username,
      'password' => $password,
      'email' => $email,
      'role' => $role,
    ]);
  }

  /**
   * Update a user in the database
   *
   * @param integer $id User ID
   * @param string $username User username
   * @param string $password User password
   * @param string $email User email
   * @param string $role User role
   */
  public static function update(
    $id,
    $username,
    $password,
    $email,
    $role
  ) {
    Manager::update('users', [
      'username' => $username,
      'password' => $password,
      'email' => $email,
      'role' => $role,
    ], [
      'id' => $id,
    ]);
  }

  /**
   * Authenticate a user
   *
   * @param string $email User email
   * @param string $password User password
   * @return Users|null User object (null if not found)
   */
  public static function authentificateUser(
    $email,
    $password
  ) {
    $user = Manager::read('users', [], ['email' => $email]);
    if (empty($user)) {
      return null;
    }

    if (password_verify($password, $user[0]['password'])) {
      $user = new self(
        $user[0]['id'],
        $user[0]['username'],
        null, // Set the password to null so it doesn't leak
        $user[0]['email'],
        $user[0]['role']
      );

      $_SESSION['user'] = $user;
      return $user;
    }
    return null;
  }

  /**
   * Check if a user is authentificated
   *
   * @return boolean True if the user is authentificated, false otherwise
   */
  public static function isAuthentificated()
  {
    return isset($_SESSION['user']);
  }

  /**
   * Get the authentificated user
   *
   * @return Users|null User object (null if not found)
   */
  public static function getAuthentificatedUser()
  {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
  }

  /**
   * Check if a user is an admin
   *
   * @return boolean True if the user is an admin, false otherwise
   */
  public static function isAdmin()
  {
    return isset($_SESSION['user']) && $_SESSION['user']->getRole() === 'admin';
  }

  /**
   * Disconnect a user
   *
   * @return void
   */
  public static function disconnect()
  {
    unset($_SESSION['user']);
  }

  /**
   * Delete a user from the database
   *
   * @param integer $id User ID
   */
  public static function delete(int $id)
  {
    Manager::delete('users', ['id' => $id]);
  }

  /**
   * Get the user ID
   *
   * @return integer User ID
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the user username
   *
   * @return string User username
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Get the user password
   *
   * @return string User password
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * Get the user email
   *
   * @return string User email
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Get the user role
   *
   * @return string User role
   */
  public function getRole()
  {
    return $this->role;
  }
}