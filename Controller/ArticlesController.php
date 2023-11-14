<?php

namespace Controller;

use Controller\ControllerBase;
use Controller\ControllerInterface;
use Model\Article;
use Inc\TypeEscaper;

class ArticlesController extends ControllerBase implements ControllerInterface
{
  const ACTION_LIST = 'list';

  private Article $model;
  private bool $admin_only = true;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('articles', $twig, $this->admin_only);
    $this->model = new Article($database_credentials);
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Add GET routes
    $this->addSubRoute(self::ACTION_LIST, 'list.html.twig', [$this, 'GET_list'], 'GET');
  }

  /**
   * @return array Retourne un array contenant les éléments
   */
  public function GET_list(): array
  {
    $articles = $this->model->readElement();

    return [
      'articles' => $articles
    ];
  }
}
