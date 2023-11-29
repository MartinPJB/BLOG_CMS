<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\Users;
use \Core\FieldChecker;

/**
 * Users controller | Handles all requests related to the users page
 */
class UsersController extends ControllerBase implements ControllerInterface
{
  public string $name = 'User';
  public string $description = 'Handles all requests related to the users page.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * {@inheritDoc}
   */
  public function index(array $params): void
  {
    // There is no way to show all the users from there
    $this->redirect('articles');
  }

  /**
   * See a specific user
   *
   * @param array $params The parameters passed to the controller
   */
  public function see(array $params): void
  {
    $user_id = intval($this->requestContext->getOptParam());

    if (!isset($user_id) || $user_id === 0) {
      $this->redirect('articles');
    }

    $user = Users::getUser($user_id);
    $this->render('Users/see', [
      'user' => $user,
    ]);
  }

  /**
   * Show the login page
   *
   * @param array $params The parameters passed to the controller
   *
   * [METHOD: GET]
   */
  public function login(array $params): void
  {
    // Check if the user is already logged in
    if (Users::isAuthentificated()) {
      $this->redirect('articles');
    }

    $this->render('Users/login');
  }

  /**
   * Logs the user out
   *
   * @param array $params The parameters passed to the controller
   */
  public function logout(array $params): void
  {
    Users::disconnect();
    $this->redirect('articles');
  }

  /**
   * Process the login of a user
   *
   * @param array $params The parameters passed to the controller
   *
   * [METHOD: POST]
   */
  public function process_login(array $params): void
  {
    // User's authentificated, redirect to the articles page
    if (Users::isAuthentificated()) {
      $this->redirect('articles');
    }

    $POST = $params['POST'];

    // Check if the user is already logged in
    if (Users::isAuthentificated()) {
      $this->redirect('articles');
    }

    // Cleans out the inputs
    $email = FieldChecker::cleanString($POST['email']);
    $password = FieldChecker::cleanString($POST['password']);

    // Validate the login request
    $errors = $this->validateLogin($POST);

    // If there's no errors, try to log the user in
    if (empty($errors)) {
      $user = Users::authentificateUser($email, $password);
      var_dump($user);

      if ($user) {
        $this->redirect('articles');
      } else {
        $errors[] = 'The username or password is incorrect.';
      }
    }

    $this->render('Users/login', [
      'errors' => $errors,
    ]);
  }

  /**
   * Validate a login request
   *
   * @param array $POST The POST parameters passed to the controller
   * @return array The errors
   */
  private function validateLogin(array $POST): array
  {
    $errors = [];

    if (!isset($POST['user_login'])) {
      $errors[] = 'The login form has not been defined.';
    }

    // Verify email and password are set
    if (!isset($POST['email'])) {
      $errors[] = 'The email field is missing.';
    }

    if (!isset($POST['password'])) {
      $errors[] = 'The password field is missing.';
    }

    // Checks if the inputs are valid
    if (!FieldChecker::checkEmail($POST['email'])) {
      $errors[] = 'The email is not valid.';
    }

    if (!FieldChecker::isEmptyField($POST['password'])) {
      $errors[] = 'Please enter a password.';
    }

    return $errors;
  }
}