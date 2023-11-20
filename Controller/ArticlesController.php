<?php

namespace Controller;

use Controller\ControllerBase;
use Controller\ControllerInterface;
use Model\Base;
use Includes\TypeEscaper;

class ArticlesController extends ControllerBase implements ControllerInterface
{
  const ACTION_LIST = 'list';

  private Base $model;
  private bool $admin_only = false;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('articles', $twig, $this->admin_only);
    $this->model = new Base($database_credentials, 'articles');
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Add GET routes
    $this->addSubRoute(self::ACTION_LIST, 'list.html.twig', [$this, 'GET_list'], 'GET', 0);
  }

  /**
   * @return array Retourne un array contenant les éléments
   */
  public function GET_list(): array
  {
    $articles = $this->model->readElement([], ['published' => 1]);

    return [
      'articles' => $articles
    ];
  }
}
