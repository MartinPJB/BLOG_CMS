<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;

/**
 * Credits controller | Handles all requests related to static pages
 */
class StaticsController extends ControllerBase implements ControllerInterface
{
  public $name = 'Statics';
  public $description = 'Handles all requests related to static pages.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * See a static page
   *
   * @param array $params The parameters passed to the controller
   */
  public function index($params) {
    $this->render('Statics/'. $_REQUEST['route'], []);
  }
}
