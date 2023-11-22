<?php

namespace Controller;

use \Core\Routing\RequestContext;
use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
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
  public function __construct(RequestContext $requestContext) {
    parent::__construct($requestContext);
  }

  /**
   * Index route
   *
   * @param array $params Parameters
   * @return void
   */
  public function index(array $params): void {
    $users = [
      new User('admin', 'admin'),
      new User('user', 'user')
    ];

    $this->render('user/index', [
      'users' => $users
    ]);
  }
}