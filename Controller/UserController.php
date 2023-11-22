<?php

namespace Controller;

use \Controller\ControllerInterface;
use \Controller\ControllerBase;
use \Model\User;

/**
 * User controller
 *
 * @package CUEJ_CMS\Controller
 * @version 0.0.1
 * @since 0.0.1
 * @property string $name Controller name
 * @property string $description Controller description
 */
class UserController extends ControllerBase implements ControllerInterface {
  public function __construct() {
    parent::__construct();
  }

  /**
   * Index route
   *
   * @return void
   */
  public function index(): void {
    $users = [
      new User('admin', 'admin'),
      new User('user', 'user')
    ];

    $this->render('user/index', [
      'users' => $users
    ]);
  }
}